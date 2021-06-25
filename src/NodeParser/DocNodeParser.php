<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\Node\DocNode;
use PhpParser\Node\Expr\Throw_;

class DocNodeParser extends NodeParser
{
    public function getNode()
    {
        $doc = new DocNode();
        $ignoreToken = [Lexer::T_NONE, Lexer::T_WHITE_SPACE];
        $this
            ->expect(Lexer::T_AT)
            ->next()
            ->expect(Lexer::T_IDENTIFIER)
        ;
        $content = $this->content();
        $doc->setName($content);
        $this->next($ignoreToken);

        if ('var' === $content && !$this->is(Lexer::T_VAR)) {
            $doc->setType($this->getContentUntil(self::NONE_TOKENS));
            $this->next($ignoreToken);
        }

        if ('return' === $content) {
            $doc->setType($this->getContentUntil(self::NONE_TOKENS));
            $this->next($ignoreToken);
        }

        if ($this->lookIs([Lexer::T_IDENTIFIER, Lexer::T_VAR])) {
            $doc->setType($this->content());
            $this->next($ignoreToken);
        }
        if ($this->is(Lexer::T_VAR)) {
            $doc->setVar($this->content());
            $this->next($ignoreToken);
        }

        $doc->setComment($this->getContentUntil([Lexer::T_NL, Lexer::T_CLOSE_DOC]));

        return $doc;
    }
}
