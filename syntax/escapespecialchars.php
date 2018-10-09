<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once (DOKU_PLUGIN.'syntax.php');

class syntax_plugin_markdownx_escapespecialchars extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'substition'; }
    function getPType() { return 'normal'; }
    function getSort()  { return 61; }
    
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\`',  $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\*', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\_',  $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\{', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\}', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\[', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\]', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\(', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\)', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\>',  $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\#', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\+', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\-', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\-', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\\.', $mode,'plugin_markdownx_escapespecialchars');
        $this->Lexer->addSpecialPattern('(?<!\\\\)\\\\!',  $mode,'plugin_markdownx_escapespecialchars');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        return array($state, $match);
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        $renderer->doc .= substr($data[1], -1);
        return true;
    }
}