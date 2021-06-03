<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\Node\AnnotationNode;

class AnnotationNodeParser extends NodeParser
{
    public function getNode()
    {
        $annotation = new AnnotationNode();
        $this
            ->expect(Lexer::T_AT)
            ->next()
            ->expect(Lexer::T_IDENTIFIER)
        ;
        $annotation->setName($this->content());
        $this->next()->expect(Lexer::T_OPEN_PARENTHESIS)->next();

        while (!$this->is(Lexer::T_CLOSE_PARENTHESIS)) {
            $key = null;
            if ($this->is(Lexer::T_IDENTIFIER)) {
                $key = $this->content();
                $this->next()->expect(Lexer::T_EQUALS)->next();
            }
            $annotation->addValue($this->valueAnnotation(), $key);
            $this->next();

            if ($this->is(Lexer::T_COMMA)) {
                $this->next();
            }
        }
        return $annotation;
    }

    protected function valueAnnotation()
    {
        switch($this->type()) {
            case Lexer::T_STRING:
                return $this->content();
            case Lexer::T_INTEGER:
                return (int)$this->content();
            case Lexer::T_FLOAT:
                return (float)$this->content();
            case Lexer::T_TRUE:
                return true;
            case Lexer::T_FALSE:
                return false;
            case Lexer::T_AT:
                return $this->getNode();
            case Lexer::T_OPEN_CURLY_BRACES:
                return $this->arrayAnnotation();
        }
        $this->dump(10);
        $this->parseError();
    }

    protected function arrayAnnotation()
    {
        $array = [];
        $this->expect(Lexer::T_OPEN_CURLY_BRACES)->next();
        while (!$this->is(Lexer::T_CLOSE_CURLY_BRACES)) {

            if (
                ($data = $this->extract([Lexer::T_STRING, Lexer::T_COLON]))
                || ($data = $this->extract([Lexer::T_STRING, Lexer::T_EQUALS]))
            ) {
                $key = $data[0];
            }
            $value = $this->valueAnnotation();
            $this->next();
            if (isset($key)) {
                $array[$key] = $value;
            } else {
                $array[] = $value;
            }
            if ($this->is(Lexer::T_COMMA)) {
                $this->next();
            }
        }
        return $array;
    }
}