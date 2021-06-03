<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\Node\ClassLikeNode;

class ClassNodeParser extends NodeParser
{
    public function getNode()
    {
        $node = new ClassLikeNode();
        $node->setName($this->token()->getContent());
        $this->next();

        if ($this->token()->is(T_EXTENDS)) {
            if (!$this->next()->isAny($expect = [T_NS_SEPARATOR, T_STRING])) {
                $this->parseError($expect);
            }
            $node->setExtends($this->getContentWhile([T_NS_SEPARATOR, T_STRING], self::NONE_TOKENS));
        }

        if ($this->token()->is(T_IMPLEMENTS)) {
            $this->next();
        dump($this->token(), $this->name());
            do {
                $node->addImplement($this->getContentWhile([T_NS_SEPARATOR, T_STRING], self::NONE_TOKENS));
            } while ($this->token()->is(',') && $this->next());
        }

        $this->expect('{')->next();

        while(!$this->token()->is('}')){
            $doc = null;
            $isFinal = false;
            $isAbstract = false;
            $visibility = null;
            $isStatic = false;
            if ($this->token()->is(Lexer::T_OPEN_BLOC)) {
                $doc = $this->next()->parse(DocBlockNodeParser::class);
                $this->next();
            }
            if ($this->token()->is(T_FINAL)) {
                $isFinal = true;
                $this->next();
            }
            if ($this->token()->is(T_ABSTRACT)) {
                $isAbstract = true;
                $this->next();
            }
            if ($this->token()->isAny([T_PUBLIC, T_PROTECTED, T_PRIVATE])) {
                $visibility = [
                    T_PUBLIC => 'public',
                    T_PROTECTED => 'protected',
                    T_PRIVATE => 'private']
                [$this->token()->getType()];
                $this->next();
            }
            if ($this->token()->is(T_STATIC)) {
                $isStatic = true;
                $this->next();
            }

            switch ($this->token()->getType()) {
                case T_FUNCTION:
                    $this->next();
                    $method = $this->parse(MethodNodeParser::class)
                        ->setAbstract($isAbstract)
                        ->setIsFinal($isFinal)
                        ->setDocComment($doc)
                        ->setVisibility($visibility)
                        ->setIsStatic($isStatic)
                    ;
                    $isAbstract = false;
                    $isFinal = false;
                    $doc = null;
                    $visibility = null;
                    $isStatic = false;
                    $node->addMethod($method);
                    break;
                case T_VARIABLE:
                    $property = $this->parse(PropertyNodeParser::class);
                    $property->setDocComment($doc)->setVisibility($visibility)->setIsStatic($isStatic);
                    $node->addPropertyNode($property);
                    $doc = null;
                    $visibility = null;
                    $isStatic = false;
                    break;
                case T_CONST:
                    $constant = $this->parse(ConstantNodeParser::class);
                    $constant->setDocComment($doc)->setVisibility($visibility);
                    $node->addConstant($constant);
                    break;
                case T_USE:
                    $this->next();
                    $node->addTrait($this->getContentUntil(';'));
                    $this->next();
                    break;
                default:
                    throw new \Exception(sprintf('Invalid Token %s at line %d', $this->token()->getName(), $this->token()->getLine()));
            }
        }

        return $node;
    }
}