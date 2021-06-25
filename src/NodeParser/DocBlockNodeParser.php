<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\Node\DocBlockNode;

class DocBlockNodeParser extends NodeParser
{
    public function getNode()
    {
        $doc = new DocBlockNode();
        while (!$this->is(Lexer::T_CLOSE_DOC)) {
            $current = $this->token();
            switch ($current->getType()) {
                case Lexer::T_NONE:
                case Lexer::T_STRING:
                case Lexer::T_NL:
                case Lexer::T_WHITE_SPACE:
                case Lexer::T_IDENTIFIER:
                case Lexer::T_OPEN_CURLY_BRACES:
                    $doc->addStatement($this->getContentUntil(Lexer::T_NL));
                    $this->next([]);
                    break;
                case Lexer::T_AT:
                    if ($this->lookIs([Lexer::T_AT, Lexer::T_IDENTIFIER, Lexer::T_OPEN_PARENTHESIS])) {
                        $doc->addStatement($this->parse(AnnotationNodeParser::class));
                        $this->expect(Lexer::T_CLOSE_PARENTHESIS)->next([])->expect(Lexer::T_NL)->next([]);
                    } else {
                        $doc->addStatement($this->parse(DocNodeParser::class));
                        if (!$this->is(Lexer::T_CLOSE_DOC)) {
                            $this->next([]);
                        }
                    }
                    break;
                default:
                    $this->parseError();
            }
        }
        return $doc;
    }
}
