<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once (DOKU_PLUGIN.'syntax.php');
 
class syntax_plugin_markdownx_hr extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'container'; }
    function getPType() { return 'block'; }
    function getSort()  { return 7; }
 
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('^[ \t]{0,3}(?:\*[ \t]*){3,}(?=\n)', $mode, 'plugin_markdownx_hr');
        $this->Lexer->addSpecialPattern('^[ \t]{0,3}(?:-[ \t]*){3,}(?=\n)', $mode, 'plugin_markdownx_hr');
        $this->Lexer->addSpecialPattern('^[ \t]{0,3}(?:_[ \t]*){3,}(?=\n)', $mode, 'plugin_markdownx_hr');
    }
 
    function handle($match, $state, $pos, Doku_Handler $handler) {
        $handler->_addCall('hr', array(), $pos);
        return true;
    }
 
    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}