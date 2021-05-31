<?php

namespace Tests;

use PhpEditor\DocLexer;
use PHPUnit\Framework\TestCase;

class DocLexerTest
{

    public function testScan()
    {
        $lexer = new DocLexer();


        $tokens = $lexer->scan('@salut\Les\Gas("ici un arg")');

    }

}