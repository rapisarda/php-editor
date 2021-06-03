<?php declare(strict_types=1);

namespace PhpEditor\NodeParser;

use PhpEditor\Lexer;
use PhpEditor\Parser;
use PhpEditor\Token;

abstract class NodeParser
{
    /** @var Lexer */
    protected $lexer;
    /** @var Parser */
    protected $parser;

    /**
     * NodeParser constructor.
     * @param Lexer $lexer
     * @param Parser $parser
     */
    public function __construct(Lexer $lexer, Parser $parser)
    {
        $this->lexer = $lexer;
        $this->parser = $parser;
    }

    abstract public function getNode();

    public function parse(string $parser)
    {
        return $this->parser->parse($parser);
    }

    /**
     * @param $endToken
     * @return string
     */
    protected function extractWhile($endToken)
    {
        $endToken = is_array($endToken) ? $endToken : [$endToken];
        $val = '';
        while ($this->lexer->valid() && !$this->lexer->current()->isAny($endToken)) {
            $val .= $this->lexer->current()->getContent();
            $this->lexer->next();
        }

        return $val;
    }

    /**
     * @param $endToken
     * @return array
     */
    protected function extractUntil($endToken)
    {
        $extract = [];
        while ($this->isAny($endToken)) {
            $extract[] = $this->content();
            $this->next([]);
        }

        return $extract;
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
    protected function next($ignore = [T_WHITESPACE, Lexer::T_NONE, Lexer::T_WHITE_SPACE, Lexer::T_NL])
    {
        $this->lexer->next();
        if ($this->lexer->valid() && $this->isAny($ignore)) {
            $this->next();
        }
        return $this;
    }

    protected function expect($id)
    {
        if (!$this->token()->is($id)) {
            throw new \Exception(sprintf(
                'Error in file %s Expected %s got %s line %d near %s',
                $this->parser->getFilename(),
                is_int($id) ? token_name($id) : $id,
                $this->token()->getName(),
                $this->token()->getLine(),
                $this->content()
            ));
        }

        return $this;
    }


    protected function is($id)
    {
        return $this->token()->is($id);
    }


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

    /**
     * @return string
     */
    protected function getFullName()
    {
        $identifier = '';
        while ($this->token()->isAny([T_NS_SEPARATOR, T_STRING, T_ARRAY])) {
            $identifier .= $this->token()->getContent();
            $this->next();
        }

        return $identifier;
    }

    protected function parseError($expect = [])
    {
        debug_print_backtrace(0, 5);
        $got = $this->name();
        $content = $this->content();
        $content .= $this->next([])->content();
        $content .= $this->next([])->content();
        $content .= $this->next([])->content();
        $content .= $this->next([])->content();
        throw new \ParseError(sprintf(
            'Error in file %s near "%s" at line %d, expect "%s" got %s',
            $this->parser->getFilename(),
            $content,
            $this->token()->getLine(),
            implode('" ,"', $expect),
            $got
        ));
    }
}
