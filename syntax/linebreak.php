<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once (DOKU_PLUGIN . 'syntax.php');

class syntax_plugin_markdownx_linebreak extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'substition'; }
    function getPType() { return 'block'; }
    function getSort()  { return 139; }
    function getAllowedTypes() {
        return array('formatting', 'substition', 'disabled', 'protected');
    }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('(?<!^|\n)\n(?!\n|>)', 'base', 'plugin_markdownx_linebreak');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        $handler->_addCall('linebreak', array(), $pos);
        return false;
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        return false;
    }
}