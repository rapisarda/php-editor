<?php


namespace PhpEditor\NodeParser;


use PhpEditor\Lexer;
use PhpEditor\Node\RootNode;

class RootNodeParser extends NodeParser
{
    
    public function getNode()
    {
        $node = new RootNode();
        $abstract = false;
        $doc = null;
        $type = null;
        $isFinal = false;
        while ($this->lexer->valid()) {
            switch ($this->token()->getType()) {
                case T_OPEN_TAG:
                    $this->next();
                    if ($this->token()->is(T_DOC_COMMENT)) {
                        // @todo parse comment
                        $this->next();
                    }
                    break;
                case T_DECLARE:
                    $node->setDeclare($this->next()->expect('(')->next()->getContentUntil(')'));
                    $this->next()->expect(';')->next();
                    break;
                case T_NAMESPACE:
                    $node->setNamespace($this->next()->getContentUntil(';'));
                    $this->next();
                    break;
                case T_USE:
                    switch($this->next()->token()->getType()) {
                        case T_FUNCTION:
                            $node->addUseFunction($this->next()->getContentUntil(';'));
                            break;
                        case T_CONST:
                            $node->addUseConstant($this->next()->getContentUntil(';'));
                            break;
                        case T_STRING:
                            $node->addUse($this->getContentUntil(';'));
                            break;
                    }
                    $this->next();
                    break;
                case T_COMMENT:
                case Lexer::T_OPEN_BLOC:
                    $this->next();
                    $doc = $this->parse(DocBlockNodeParser::class);
                    $this->next();
                    break;
                case T_ABSTRACT:
                    $abstract = true;
                    $this->next();
                    break;
                case T_FINAL:
                    $isFinal = true;
                    $this->next();
                    break;
                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    $type = $this->token()->getContent();
                    $this->next();
                    $classNode = $this->parse(ClassNodeParser::class);

                    $classNode
                        ->setIsAbstract($abstract)
                        ->setIsFinal($isFinal)
                        ->setDocComment($doc)
                        ->setType($type);

                    $node->setClassLike($classNode);

                    $abstract = false;
                    $isFinal = false;
                    $doc = null;
                    $type = null;

                    return $node;

                default:
                    $token = $this->token();
//                    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                    throw new \Exception($token->getLine().' '.$token->getName(). ' '. $token->getContent());
            }
        }
        return $node;
    }

}