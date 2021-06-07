<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Node\ArgumentNode;

class ArgumentNodeParser extends NodeParser
{
    /**
     * @return ArgumentNode
     * @throws \Exception
     */
    public function getNode()
    {
        $node = new ArgumentNode();
        if ($this->is('?')) {
            $node->setIsNullable(true);
            $this->next();
        }
        if ($this->isAny([T_STRING, T_ARRAY, T_NS_SEPARATOR])) {
            $node->setType($this->getContentUntil([T_VARIABLE], self::NONE_TOKENS));
        }

        $this->expect(T_VARIABLE);
        $node->setName($this->content());
        $this->next();

        if ($this->is('=')) {
            $this->next();
            $node->setDefault($this->rawValue());
        }

        return $node;
    }

    protected function rawValue()
    {
        $level = 0;
        $value = '';
        while (true) {
            switch ($this->type()) {
                case '[':
                    $level++;
                    break;
                case ']':
                    $level--;
                    break;
                case ',':
                    if ($level === 0) {
                        return $value;
                    }
                    break;
                case ')':
                    return $value;
            }
            $value .= $this->content();
            $this->next($level > 0 ? [] : self::NONE_TOKENS);
        }
    }
}
