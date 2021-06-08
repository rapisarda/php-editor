<?php declare(strict_types=1);

namespace PhpEditor;

use PhpEditor\Node\RootNode;
use PhpEditor\NodeParser\AnnotationNodeParser;
use PhpEditor\NodeParser\ArgumentNodeParser;
use PhpEditor\NodeParser\ClassNodeParser;
use PhpEditor\NodeParser\ConstantNodeParser;
use PhpEditor\NodeParser\DocBlockNodeParser;
use PhpEditor\NodeParser\MethodNodeParser;
use PhpEditor\NodeParser\PropertyNodeParser;
use PhpEditor\NodeParser\RootNodeParser;

class Parser
{
    /** @var ParserCollection */
    private $nodeParsers;
    private $filename;

    public function __construct($filename)
    {
        $this->nodeParsers = new ParserCollection();
        $lexer = new Lexer(file_get_contents($filename));
        $this->filename = $filename;

        $parsers = [
            AnnotationNodeParser::class,
            ArgumentNodeParser::class,
            ClassNodeParser::class,
            ConstantNodeParser::class,
            MethodNodeParser::class,
            DocBlockNodeParser::class,
            PropertyNodeParser::class,
            RootNodeParser::class,
        ];

        foreach ($parsers as $parser) {
            $this->nodeParsers->add($parser, new $parser($lexer, $this->nodeParsers));
        }
    }

    /**
     * @throws \Exception
     */
    public function getAst(): RootNode
    {
        try {
            return $this->nodeParsers->get(RootNodeParser::class)->getNode();
        } catch (\Throwable $t) {
            throw new \Exception(sprintf('Error while parsing file %s: %s', $this->filename, $t->getMessage()), $t->getCode(), $t);
        }
    }
}
