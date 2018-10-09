<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once (DOKU_PLUGIN.'syntax.php');

class syntax_plugin_markdownx_codeblocks2 extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort()  { return 91; }
    
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('^[ \t]{0,3}\~\~\~[a-z0-9_]*[ \t]*\n', $mode, 'plugin_markdownx_codeblocks2');
        $this->Lexer->addExitPattern('^[ \t]{0,3}\~\~\~[ \t]*\n', 'plugin_markdownx_codeblocks2');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        switch ($state) {
        case DOKU_LEXER_ENTER:
            if (preg_match('/^[ \t]{0,3}\~\~\~([a-z0-9_]*)[ \t]*\n/', $match, $matches) > 0) {
                $this->lang = $matches[1];
                if (!plugin_isdisabled('syntaxhighlighter4')) {
                    if ($this->lang)
                        $handler->plugin('<sxh', $state, $pos, 'syntaxhighlighter4');
                }
            }
            break;
        case DOKU_LEXER_EXIT:
            break;
        case DOKU_LEXER_UNMATCHED:
            if (!plugin_isdisabled('syntaxhighlighter4')) {
                if ($this->lang)
                    $handler->plugin(' '.$this->lang.">\n".$match, $state, $pos, 'syntaxhighlighter4');
                else
                    $handler->_addCall('code', array($match, $this->lang), $pos);
            } else {
                if ($this->lang)
                    $handler->_addCall('file', array($match, $this->lang, 'snippet.'.$this->lang), $pos);
                else
                    $handler->_addCall('code', array($match, $this->lang), $pos);
            }
            break;
        }
        return false;
    }
    
    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}