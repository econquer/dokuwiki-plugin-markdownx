<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once (DOKU_PLUGIN.'syntax.php');

class syntax_plugin_markdownx_references extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'substition'; }
    function getPType() { return 'normal'; }
    function getSort()  { return 100; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\n[ ]{0,3}\[[^\n]+?\]:[ \t]*\n?[ \t]*<?\S+?>?[ \t]*\n?[ \t]*(?:(?<=\s)["(].+?[")][\t]*)?(?=\n)', $mode, 'plugin_markdownx_references');
    }

    function handle($match, $state, $pos, Doku_Handler $handler) {
        return array($state, $match);
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        if ($mode != 'metadata')
            return false;

        preg_match('/\[(.+)\]:[ \t]*\n?[ \t]*<?(\S+)>?[ \t]*\n?[ \t]*(?:(?<=\s)["(](.+?)[")][\t]*)?/', $data[1], $matches);
        $key = 'markdownx_references_'.preg_replace("/ /", ".", $matches[1]);
        $renderer->meta[$key] = $matches[2];
        return true;
    }
}