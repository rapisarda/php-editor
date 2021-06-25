<?php declare(strict_types=1);

namespace Tests\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\NodeParser\DocNodeParser;
use PhpEditor\ParserCollection;
use PHPUnit\Framework\TestCase;

class DocNodeParserTest extends TestCase
{
    /**
     * @throws \Exception
     * @dataProvider argProvider
     */
    public function testGetNode($arg, $name, $type, $var, $comment)
    {
        $lexer = new Lexer('<?php ' . $arg);

        $lexer->next()->next()->next();

        $parser = new DocNodeParser($lexer, new ParserCollection());
        $node = $parser->getNode();

        self::assertEquals($name, $node->getName());
        self::assertEquals($type, $node->getType());
        self::assertEquals($var, $node->getVar());
        self::assertEquals($comment, $node->getComment());
    }

    public function argProvider()
    {
        yield ['/** @var Foo $bar */', 'var', 'Foo', '$bar', ''];
        yield ['/** @return Foo|Bar */', 'return', 'Foo|Bar', null, ''];
        yield ['/** @var Foo */', 'var', 'Foo', null, ''];
        yield ['/** @var null|Foo[] */', 'var', 'null|Foo[]', null, ''];
        yield ["/**\n * @var \$foo with an' awesome comment\n and this is ignored \n * also this*/", 'var', null, '$foo', 'with an\' awesome comment'];
        yield ['/** @param Foo $bar what awesome comment */', 'param', 'Foo', '$bar', 'what awesome comment '];
        yield ["/**\n * @param Foo \$bar what awesome comment\n*/", 'param', 'Foo', '$bar', 'what awesome comment'];
        yield ["/**\n * @todo do that\n*/", 'todo', null, null, 'do that'];
    }
}
