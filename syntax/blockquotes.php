<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once (DOKU_PLUGIN.'syntax.php');
 
class syntax_plugin_markdownx_blockquotes extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'container'; }
    function getPType() { return 'block'; }
    function getSort()  { return 219; }
    function getAllowedTypes() { return array('formatting', 'substition', 'disabled', 'protected', 'container'); }
  
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('(?:(?:[ \t]*>)+[^\n]*\n)+', $mode, 'plugin_markdownx_blockquotes');
    }
  
    function flush(Doku_Handler $handler) {
        if ($this->buffer == '') {
            return;
        }
        if ($this->level != $this->prevLevel) {
            $handler->_addCall('quote_newline', array($this->level), $pos);
        }
        $calls = p_get_instructions($this->buffer);
        while ($calls[0][0] == 'document_start') array_shift($calls);
        while ($calls[count($calls)-1][0] == 'document_end') array_pop($calls);

        $handler->CallWriter->writeCalls($calls);
        $this->buffer = '';
        $this->prevLevel = $this->level;
    }
    
    function parseQuotes($match, Doku_Handler $handler) {
        $lines = explode("\n", $match);
        $this->prevLevel = 1;
        $this->level = 1;
        $this->buffer = '';
        $isCodeBlock = false;
        $codeBlockSyntax = '';
        foreach ($lines as &$line) {
            $isSplitted = false;
            if ($isCodeBlock) {
                $depth = 0;
                for ($i=0;$i<$this->level;$i++) {
                    $src = $line;
                    $line = preg_replace('/^[ \t]*>/', '', $line, $this->level, $depth);
                }

                if (preg_match('/^[ \t]*(```|\~\~\~)[ \t]*$/', $line, $matches) > 0) {
                    if ($matches[1] == $codeBlockSyntax) {
                        $isCodeBlock = false;
                        $codeBlockSyntax = '';
                    }
                }
            } else {
                if (preg_match('/^([ \t]*>)+[ \t]*(```|\~\~\~)/', $line, $matches) > 0) {
                    $isCodeBlock = true;
                    $codeBlockSyntax = $matches[2];
                    $this->flush($handler);
                }
                
                if (preg_match('/^([ \t]*>)+/', $line, $matches) > 0) {
                    $depth = substr_count($matches[0], '>');
                    if ($isCodeBlock) {
                        $this->level = $depth;
                    }
                    $line = preg_replace('/^([ \t]*>)+/', '', $line);
                }

                if ($this->level != $depth) {
                    $this->flush($handler);
                    $this->level = $depth;
                }
            }
            
            $this->buffer .= $line."\n";
        }
        $this->flush($handler);
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        $ReWriter = new Doku_Handler_MarkdownX_Quote($handler->CallWriter);
        $handler->CallWriter = & $ReWriter;
        
        $handler->_addCall('quote_start', array(1), $pos);
        $this->parseQuotes($match, $handler);
        $handler->_addCall('quote_end', array(), $pos);

        $handler->CallWriter->process();
        $ReWriter = & $handler->CallWriter;
        $handler->CallWriter = & $ReWriter->CallWriter;
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}

class Doku_Handler_MarkdownX_Quote extends Doku_Handler_Quote {
    function __construct(Doku_Handler_CallWriter_Interface $CallWriter) {
        parent::__construct($CallWriter);
    }

    function getDepth($marker) {
        return $marker;
    }
}
