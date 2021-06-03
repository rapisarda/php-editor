<?php declare(strict_types=1);

namespace PhpEditor;

use PhpEditor\NodeParser\NodeParser;

class ParserCollection
{
    /** @var NodeParser[] */
    private $nodeParsers = [];

    public function __construct(array $parsers = [])
    {
        foreach ($parsers as $name => $parser) {
            $this->add($name, $parser);
        }
    }

    public function get(string $id): NodeParser
    {
        return $this->nodeParsers[$id];
    }

    public function add(string $name, NodeParser $parser): void
    {
        $this->nodeParsers[$name] = $parser;
    }
}
