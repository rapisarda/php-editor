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
        while (!$this->is(')')) {
            $method->addArgument($this->parse(ArgumentNodeParser::class));
            if ($this->is(',')) {
                $this->next();
            }
        }

        if ($this->next()->token()->is(':')) {
            if ($this->next()->token()->is('?')) {
                $method->setNullable(true);
                $this->next();
            }
            $method->setReturnType($this->getContentWhile([T_NS_SEPARATOR, T_STRING, T_ARRAY], self::NONE_TOKENS));
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