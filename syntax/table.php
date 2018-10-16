<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once (DOKU_PLUGIN.'syntax.php');
 
function trimpipe($line) {
    if ($line[0] == '|') $line = substr($line, 1);
    if ($line[strlen($line)-1] == '|') $line = substr($line, 0, strlen($line)-1);
    return $line;
}

function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function get_instructions($text){
    $types = array('formatting', 'substition', 'disabled');
    $modes = p_get_parsermodes();

    // Create the parser
    $Parser = new Doku_Parser();

    // Add the Handler
    $Parser->Handler = new Doku_Handler();

    //add modes to parser
    foreach($modes as $mode){
        $accepts = false;
        if (startsWith($mode['mode'], 'plugin_')) {
            if (in_array($mode['obj']->getType(), $types)) {
                $accepts = true;
            }
        } else {
            if ($mode['obj']->accepts($types)) {
                $accepts = true;
            } else if (preg_match('/link$|media$/',$mode['mode']) > 0){
                $accepts = true;
            }
        }
        if ($accepts) {
            $Parser->addMode($mode['mode'],$mode['obj']);
        }
    }

    // Do the parsing
    trigger_event('PARSER_WIKITEXT_PREPROCESS', $text);
    $p = $Parser->parse($text);
    //  dbg($p);

    while ($p[0][0] == 'document_start') array_shift($p);
    while ($p[count($p)-1][0] == 'document_end') array_pop($p);

    return $p;
}


class syntax_plugin_markdownx_table extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'container'; }
    function getPType() { return 'block'; }
    function getSort()  { return 55; }
    function getAllowedTypes() { return array('formatting', 'substition', 'disabled', 'protected'); }
  
    function connectTo($mode) {
        $cellPattern = '(?:[ \t]*(?:[^\n]*\|)+[^\n]*\n)';
        $headerPattern = '(?:[ \t]*\|?(?::?-+:?\|)+(?::?-+:?)?|(?::?-+:?)?(?:\|:?-+:?)+)\n';

        $tablePattern = $cellPattern.$headerPattern.$cellPattern.'*';
        $this->Lexer->addSpecialPattern($tablePattern, $mode, 'plugin_markdownx_table');
        
    }
    
    function parseTable($match, Doku_Handler $handler) {
        $lines = explode("\n", $match);
        $celltype = 'tableheader';

        $alignCells = explode("|", trimpipe($lines[1]));
        $aligns = array();
        foreach ($alignCells as &$alignCell) {
            $isLeft = $alignCell[0] == ':';
            $isRight = $alignCell[strlen($alignCell)-1] == ':';
            if ($isLeft && $isRight) {
                array_push($aligns, array(true, true));
                // error_log('>>> CENTER');
            } else if ($isLeft) {
                array_push($aligns, array(true, false));
                // error_log('>>> LEFT');
            } else if ($isRight) {
                array_push($aligns, array(false, true));
                // error_log('>>> RIGHT');
            } else {
                array_push($aligns, null);
            }
        }
        
        $types = array('formatting', 'substition', 'disabled');
        $lineCount = count($lines);
        for($i=0;$i<$lineCount;$i++) {
            $cells = explode("|", trimpipe($lines[$i]));
            if($i == 1) {
                continue;
            }
            $handler->_addCall($celltype, array(), $pos);
            $cellCount = count($cells);
            for($j=0;$j<$cellCount;$j++) {
                $cell = $cells[$j];
                if ($aligns[$j][1]) {
                    $handler->_addCall('table_align', array(), $pos);
                }

                $calls = get_instructions($cell, $types);
                if (count($calls) > 0) {
                    $handler->CallWriter->writeCalls($calls);
                }

                if ($aligns[$j][0]) {
                    $handler->_addCall('table_align', array(), $pos);
                }
                $handler->_addCall($celltype, array(), $pos);
            }
            $handler->_addCall('table_row', array(), $pos);
            $celltype = 'tablecell';
        }
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        $ReWriter = new Doku_Handler_Table($handler->CallWriter);
        $handler->CallWriter = & $ReWriter;
        $handler->_addCall('table_start', array(), $pos);
        $this->parseTable($match, $handler);
        $handler->_addCall('table_end', array(), $pos);

        $handler->CallWriter->process();
        $ReWriter = & $handler->CallWriter;
        $handler->CallWriter = & $ReWriter->CallWriter;
        return true;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}
