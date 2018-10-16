# dokuwiki-plugin:MarkdownX
> MarkdownX is a simple & integrated markdown syntax plugin of DokuWiki 

## Installation
...

#### Quote
comment here: `inc/parser/parser.php`
```php
class Doku_Parser_Mode_quote extends Doku_Parser_Mode {
    ...
    function connectTo($mode) {
#        $this->Lexer->addEntryPattern('\n>{1,}',$mode,'quote');
    }

    function postConnect() {
#        $this->Lexer->addPattern('\n>{1,}','quote');
#        $this->Lexer->addExitPattern('\n','quote');
    }
    ...
}

```

#### Table
comment here: `inc/parser/parser.php`
```php
class Doku_Parser_Mode_table extends Doku_Parser_Mode {
    ...
    function connectTo($mode) {
#        $this->Lexer->addEntryPattern('[\t ]*\n\^',$mode,'table');
#        $this->Lexer->addEntryPattern('[\t ]*\n\|',$mode,'table');
    }

    function postConnect() {
#        $this->Lexer->addPattern('\n\^','table');
#        $this->Lexer->addPattern('\n\|','table');
#        $this->Lexer->addPattern('[\t ]*:::[\t ]*(?=[\|\^])','table');
#        $this->Lexer->addPattern('[\t ]+','table');
#        $this->Lexer->addPattern('\^','table');
#        $this->Lexer->addPattern('\|','table');
#        $this->Lexer->addExitPattern('\n','table');
    }
    ...
}

```



## License

[MIT License](https://opensource.org/licenses/MIT) © 2018 [econquer](https://github.com/econquer/)