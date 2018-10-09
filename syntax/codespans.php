<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once (DOKU_PLUGIN.'syntax.php');
 
class syntax_plugin_markdownx_codespans extends DokuWiki_Syntax_Plugin {
    function getType()         { return 'formatting'; }
    function getPType()        { return 'normal'; }
    function getSort()         { return 99; }
    function getAllowedTypes() { return array(); }
 
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('(?<!`)`(?!`).+?(?<!`)`(?!`)', $mode, 'plugin_markdownx_codespans');
        $this->Lexer->addSpecialPattern('(?<!`)``(?!`).+?(?<!`)``(?!`)', $mode, 'plugin_markdownx_codespans');
        $this->Lexer->addSpecialPattern('(?<!`)```(?!`).+?(?<!`)```(?!`)', $mode, 'plugin_markdownx_codespans');
        $this->Lexer->addSpecialPattern('(?<!`)````(?!`).+?(?<!`)````(?!`)', $mode, 'plugin_markdownx_codespans');
        $this->Lexer->addSpecialPattern('(?<!`)`````(?!`).+?(?<!`)`````(?!`)', $mode, 'plugin_markdownx_codespans');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
                $match = preg_replace('/^`+/', '', $match);
                $match = preg_replace('/`+$/', '', $match);
        return array($match);
    }
 
    function render($mode, Doku_Renderer $renderer, $data) {
        $renderer->monospace_open();
        $renderer->cdata($data[0]);
        $renderer->monospace_close();
        return true;
    }
}