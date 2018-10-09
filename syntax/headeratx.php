<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'syntax.php');

class syntax_plugin_markdownx_headeratx extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'baseonly'; }
    function getPType() { return 'block'; }
    function getSort()  { return 4; }
    function getAllowedTypes() {
        return array('formatting', 'substition', 'disabled', 'protected');
    }
  
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('^[ \t]{0,3}\#{1,6}[ \t]+.+?[ \t]*\#*(?=\n+)', $mode, 'plugin_markdownx_headeratx');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        $title = trim($match);
        $level = max(1, min(6, strspn($title, '#')));
        $title = trim($title, '#');
        $title = trim($title);

        if ($handler->status['section'])
            $handler->_addCall('section_close', array(), $pos);
        if ($level <= $conf['maxseclevel']) {
            $handler->status['section_edit_start'] = $pos;
            $handler->status['section_edit_level'] = $level;
            $handler->status['section_edit_title'] = $title;
        }
        $handler->_addCall('header', array($title, $level, $pos), $pos);
        $handler->_addCall('section_open', array($level), $pos);
        $handler->status['section'] = true;

        return true;
    }
  
    function render($mode, Doku_Renderer $renderer, $data) {
        return true;
    }
}