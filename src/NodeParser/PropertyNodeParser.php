<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Node\PropertyNode;

class PropertyNodeParser extends NodeParser
{
    public function getNode()
    {
        $propertyNode = new PropertyNode();
        $propertyNode->setName($this->token()->getContent());
        if ($this->next()->token()->is('=')) {
            $propertyNode->setValue($this->next()->extractWhile(';'));
        }
        $this->expect(';')->next();

        return $propertyNode;
    }
}