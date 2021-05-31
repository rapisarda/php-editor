<?php declare(strict_types=1);

namespace PhpEditor;

abstract class AbstractNode
{
    /**
     * @param Visitor $visitor
     */
    public function accept(Visitor $visitor)
    {
        $visitor->{'visit'.(new \ReflectionClass($this))->getShortName()}($this);
    }
}
