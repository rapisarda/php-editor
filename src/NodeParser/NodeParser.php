<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\Parser;
use PhpEditor\ParserCollection;
use PhpEditor\Token;

abstract class NodeParser
{
    protected const NONE_TOKENS = [T_WHITESPACE, Lexer::T_NONE, Lexer::T_WHITE_SPACE, Lexer::T_NL];
    /** @var Lexer */
    protected $lexer;
    /** @var Parser */
    protected $parser;

    /**
     * NodeParser constructor.
     * @param Lexer $lexer
     * @param ParserCollection $parser
     */
    public function __construct(Lexer $lexer, ParserCollection $parser)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
    }

    abstract public function getNode();

    public function parse(string $parser)
    {
        return $this->parser->get($parser)->getNode();
    }

    /**
     * @param int|string|array<int|string> $endToken
     * @param array<int|string> $ignore
     * @return string
     */
    protected function getContentUntil($endToken, array $ignore = []): string
    {
        $endToken = is_array($endToken) ? $endToken : [$endToken];
        $val = '';
        while ($this->lexer->valid() && !$this->isAny($endToken)) {
            $val .= $this->content();
            $this->next($ignore);
        }

        return $val;
    }

    /**
     * @param int|string|array<int|string> $token
     * @param array<int|string> $ignore
     * @return string
     */
    protected function getContentWhile($token, array $ignore = []): string
    {
        $token = is_array($token) ? $token : [$token];
        $val = '';
        while ($this->lexer->valid() && $this->isAny($token)) {
            $val .= $this->content();
            $this->next($ignore);
        }

        return $val;
    }

    /**
     * @return Token
     */
    protected function token(): Token
    {
        return $this->lexer->current();
    }

    /**
     * @return $this
     */
    protected function next($ignore = self::NONE_TOKENS)
    {
        $this->lexer->next();
        if ($this->lexer->valid() && $this->isAny($ignore)) {
            $this->next();
        }
        return $this;
    }

    /**
     * @param int|string $id
     * @return $this
     * @throws \Exception
     */
    protected function expect($id)
    {
        if (!$this->is($id)) {
            throw new \Exception(sprintf(
                'Expected %s got %s line %d near %s',
                is_int($id) ? token_name($id) : $id,
                $this->token()->getName(),
                $this->token()->getLine(),
                $this->content()
            ));
        }

        return $this;
    }

    /**
     * @param int|string $id
     * @return bool
     */
    protected function is($id)
    {
        return $this->token()->is($id);
    }

    /**
     * @param array<int|string> $ids
     * @return bool
     */
    protected function isAny(array $ids)
    {
        return $this->token()->isAny($ids);
    }

    protected function debug()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];

        dump([$this->name(), $this->content(), $trace['function'].':'.$trace['line']]);
    }

    protected function name()
    {
        return $this->token()->getName();
    }

    protected function content()
    {
        return $this->token()->getContent();
    }

    protected function extract(array $ids)
    {
        $extract = [];
        $pos = $this->lexer->key();
        foreach ($ids as $id) {
            if (!$this->is($id)) {
                $this->lexer->seek($pos);
                return null;
            }
            $extract[] = $this->content();
            $this->next();
        }
        return $extract;
    }

    public function lookIs($ids)
    {
        $pos = $this->lexer->key();
        foreach ($ids as $id) {
            if (!$this->is($id)) {
                $this->lexer->seek($pos);
                return false;
            }
            $this->next();
        }
        $this->lexer->seek($pos);
        return true;
    }

    protected function save()
    {
        $this->saved = $this->lexer->key();
    }

    protected function restor()
    {
        $this->lexer->seek($this->saved);
    }

    protected function dump(int $tokensNb = 10, $nbBefore = 5)
    {
        $this->save();
        $this->lexer->seek($this->lexer->key() - $nbBefore);
        for ($i = 0; $i < $tokensNb; $i++) {
            echo '"'.$this->name().'"';
            echo " => '{$this->content()}'";
            echo "\n";
            $this->next([]);
        }
        $this->restor();
    }

    protected function type()
    {
        return $this->token()->getType();
    }

    protected function parseError($expect = [])
    {
        $got = $this->name();
        $content = $this->content();
        $content .= $this->next([])->content();
        $content .= $this->next([])->content();
        $content .= $this->next([])->content();
        $content .= $this->next([])->content();
        throw new \ParseError(sprintf(
            'Error near "%s" at line %d, expect "%s" got %s',
            $content,
            $this->token()->getLine(),
            implode('" ,"', $expect),
            $got
        ));
    }
}
