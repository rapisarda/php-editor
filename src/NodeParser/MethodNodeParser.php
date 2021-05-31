<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

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
        $indent = 1;
        $body = '';

        $current = $this->lexer->next()->current();

        if ($current->is(T_WHITESPACE)) {
            $spaces = explode("\n", $current->getContent());
            $indent = strlen(end($spaces));
        }

        while ($level !== 0) {
            $current = $this->lexer->next()->current();
            $nextContent = $current->getContent();
            if ($current->is('}')) {
                --$level;
                $level || $nextContent = '';
            } elseif ($current->isAny(['{', T_CURLY_OPEN])) {
                ++$level;
            } elseif ($current->is(T_WHITESPACE)) {
                $spaces = explode("\n", $current->getContent());
                $count = count($spaces);
                if ($count > 1) {
                    $spacesNb = (strlen($spaces[$count-1]) - $indent);
                    $spaces[$count-1] = str_repeat(' ', $spacesNb < 0 ? 0 : $spacesNb);
                    $space = implode("\n", $spaces);
                    $nextContent = $space;
                }
            }
            $body .= $nextContent;
        }
        $method->setBody(trim($body));
        $this->expect('}')->next();

        return $method;
    }
}