<?php

namespace Tests;

use PhpEditor\Extractor;
use PhpEditor\Parser;
use PhpEditor\PsrPrinter;
use PHPUnit\Framework\TestCase;

class ExtractorTest Extends TestCase
{
    public function testParse()
    {
        foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . 'Mocks/*Exact.php') as $filename) {
            $fileContent = file_get_contents($filename);
            $parser = new Parser($filename);
            $printer = new PsrPrinter();
            $node = $parser->getAst();

            self::assertSame($fileContent, $printer->dump($node));
        }
    }
}
