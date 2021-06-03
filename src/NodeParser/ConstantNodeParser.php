<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Node\ConstantNode;

class ConstantNodeParser extends NodeParser
{
    public function getNode()
    {
        $node = new ConstantNode();
        $node->setName($this->next()->token()->getContent());
        $node->setValue($this->next()->expect('=')->next()->getContentUntil(';'));
        $this->next();

        return $node;
    }
}