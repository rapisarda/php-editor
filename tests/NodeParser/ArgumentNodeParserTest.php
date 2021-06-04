<?php declare(strict_types=1);

namespace Tests\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\NodeParser\ArgumentNodeParser;
use PhpEditor\ParserCollection;
use PHPUnit\Framework\TestCase;

class ArgumentNodeParserTest extends TestCase
{
    /**
     * @throws \Exception
     * @dataProvider argProvider
     */
    public function testGetNode($arg, $type, $var, $default)
    {
//        $arg = '$toto = ["salut" => "monde", \'hello\'=> \'world\'])';
//        $yola = '$toto = "";';
//        $yola = '$toto = "$youpi yo",';
//        $yole = '$toto = \'yolo\';';
//        $yoli = '$toto = self::class;';
//        $arg = '<?php ' . $arg;

//        $val = ['$toto = ["salut" => "monde", \'hello\'=> \'world\'])', '["salut" => "monde", \'hello\'=> \'world\']'];

        $lexer = new Lexer('<?php ' . $arg);


        $lexer->next();

        $parser = new ArgumentNodeParser($lexer, new ParserCollection());
        $node = $parser->getNode();
//        dump($node);
        $this->assertEquals($type, $node->getType());
        $this->assertEquals($var, $node->getName());
        $this->assertEquals($default, $node->getDefault());

//        dd($parser->getNode());

//        $nodeParser = new ArgumentNodeParser();
//        $this->assertTrue(true);
    }

    public function argProvider()
    {
        yield [
            '$toto = ["salut" => "monde", \'hello\'=> \'world\'])',
            null,
            '$toto',
            '["salut" => "monde", \'hello\'=> \'world\']',
        ];
        yield ['$toto = "")', null, '$toto', '""'];
        yield ['$toto = "",', null, '$toto', '""'];
        yield ['array $toto = ["hi !"],', 'array', '$toto', '["hi !"]'];
        yield ['Salut $toto = "slurp",', 'Salut', '$toto', '"slurp"'];
//        yield ['Salut $toto = array("slurp"),', 'Salut', '$toto', '["slurp"]']; //@todo
    }
}
