<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\Node\MethodNode;

class MethodNodeParser extends NodeParser
{
    public function getNode()
    {
        $method = new MethodNode();
        $method->setName($this->token()->getContent());
        $this->next()->expect('(')->next();
        $method->setParameters($this->extractWhile(')'));

        if ($this->next()->token()->is(':')) {
            if ($this->next()->token()->is('?')) {
                $method->setNullable(true);
                $this->next();
            }
            $method->setReturnType($this->getFullName());
        }

        $level = 1;
        $body = '';


        while ($level !== 0) {
            $current = $this->lexer->next()->current();
            $nextContent = $current->getContent();
            if ($current->is('}')) {
                --$level;
                $level || $nextContent = '';
            } elseif ($current->isAny(['{', T_CURLY_OPEN])) {
                ++$level;
            }
            $body .= $nextContent;
        }
        $method->setBody($body);
        $this->expect('}')->next();

        return $method;
    }
}