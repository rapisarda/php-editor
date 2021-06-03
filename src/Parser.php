<?php declare(strict_types=1);

namespace PhpEditor;

use PhpEditor\NodeParser\NodeParser;
use PhpEditor\NodeParser\RootNodeParser;

class Parser
{
    /** @var NodeParser[] */
    private $nodeParsers = [];
    private $lexer;
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->lexer = new Lexer(file_get_contents($filename));
    }

    public function getAst()
    {
        return $this->parse(RootNodeParser::class);
    }

    public function parse(string $nodeParser)
    {
        if (!isset($this->nodeParsers[$nodeParser])) {
            $this->nodeParsers[$nodeParser] = new $nodeParser($this->lexer, $this);
            assert($this->nodeParsers[$nodeParser] instanceof NodeParser);
        }

        return $this->nodeParsers[$nodeParser]->getNode();
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
