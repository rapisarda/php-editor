<?php declare(strict_types=1);

namespace PhpEditor;

class Lexer implements \SeekableIterator
{
    public const T_NONE    = 1000;
    public const T_INTEGER = 2000;
    public const T_STRING  = 3000;
    public const T_FLOAT   = 4000;
    public const T_NL   = 5000;

    // All tokens that are also identifiers should be >= 100
    public const T_IDENTIFIER          = 100000;
    public const T_AT                  = 101000;
    public const T_CLOSE_CURLY_BRACES  = 102000;
    public const T_CLOSE_PARENTHESIS   = 103000;
    public const T_COMMA               = 104000;
    public const T_EQUALS              = 105000;
    public const T_FALSE               = 106000;
    public const T_NAMESPACE_SEPARATOR = 107000;
    public const T_OPEN_CURLY_BRACES   = 108000;
    public const T_OPEN_PARENTHESIS    = 109000;
    public const T_TRUE                = 110000;
    public const T_NULL                = 111000;
    public const T_COLON               = 112000;
    public const T_MINUS               = 113000;
    public const T_OPEN_BLOC           = 114000;
    public const T_CLOSE_BLOC          = 115000;
    public const T_WHITE_SPACE         = 116000;
    public const T_VAR                 = 117000;
    public const T_DOT                 = 118000;

    /** @var array<string, int> */
    protected $noCase = [
        '@'  => self::T_AT,
        ','  => self::T_COMMA,
        '('  => self::T_OPEN_PARENTHESIS,
        ')'  => self::T_CLOSE_PARENTHESIS,
        '{'  => self::T_OPEN_CURLY_BRACES,
        '}'  => self::T_CLOSE_CURLY_BRACES,
        '='  => self::T_EQUALS,
        ':'  => self::T_COLON,
        '-'  => self::T_MINUS,
        '\\' => self::T_NAMESPACE_SEPARATOR,
        '/**'=> self::T_OPEN_BLOC,
        '*/' => self::T_CLOSE_BLOC,
        ' '  => self::T_WHITE_SPACE,
        '.'  => self::T_DOT,
    ];

    /** @var array<string, int> */
    protected $withCase = [
        'true'  => self::T_TRUE,
        'false' => self::T_FALSE,
        'null'  => self::T_NULL,
    ];

    private const DOC_CATCHABLE_PATTERN = [
        '\/\*\*',
        '\*\/',
        '\n *(?:\*\ ?)?(?!\/)',
        '\$\w+',
        '[a-z_\\\][a-z0-9_\:\\\]*[a-z_][a-z0-9_]*',
        '(?:[+-]?[0-9]+(?:[\.][0-9]+)*)(?:[eE][+-]?[0-9]+)?',
        '"(?:""|[^"])*+"',
    ];

    /**
     * @var Token[]
     */
    private $tokens = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var int
     */
    private $sense = 1;

    /**
     * Lexer constructor.
     * @param $source
     */
    public function __construct($source)
    {
        $this->scan($source);
    }

    /**
     * @param string $source
     */
    private function scan(string $source): void
    {
        foreach (token_get_all($source) as $phpToken) {
            if (is_array($phpToken) && T_DOC_COMMENT === $phpToken[0]) {
                $line = $phpToken[2];
                foreach ($this->scanDoc($phpToken[1]) as $docToken) {
                    if ($nbLine = substr_count($docToken[1], "\n")) {
                        $line += $nbLine;
                    }
                    $this->tokens[] = new Token([$docToken[0], $docToken[1], $line]);
                }
                continue;
            }
            $this->tokens[] = new Token($phpToken);
        }
    }

    /**
     * @param string $input
     * @return array
     */
    private function scanDoc(string $input)
    {
        if (! isset($this->regex)) {
            $this->regex = sprintf(
                '/(%s)|/%s',
                implode(')|(', self::DOC_CATCHABLE_PATTERN),
                'iu'
            );
        }

        $flags   = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($this->regex, $input, -1, $flags);

        if ($matches === false) {
            // Work around https://bugs.php.net/78122
            $matches = [[$input, 0]];
        }

        $tokens = [];

        foreach ($matches as $match) {
            // Must remain before 'value' assignment since it can change content
            $type = $this->getType($match[0]);

            $tokens[] = [$type, $match[0], $match[1],];
        }

        return $tokens;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function getType(&$value)
    {

        $type = self::T_NONE;

        if ($value[0] === '"') {
            $value = str_replace('""', '"', substr($value, 1, strlen($value) - 2));

            return self::T_STRING;
        }

        if ($value[0] === '$') {
            return self::T_VAR;
        }

        if ($value[0] === "\n") {
            return self::T_NL;
        }

        if (isset($this->noCase[$value])) {
            return $this->noCase[$value];
        }

        $lowerValue = strtolower($value);

        if (isset($this->withCase[$lowerValue])) {
            return $this->withCase[$lowerValue];
        }

        // Checking numeric value
        if (is_numeric($value)) {
            return strpos($value, '.') !== false || stripos($value, 'e') !== false
                ? self::T_FLOAT : self::T_INTEGER;
        }

        if ($value[0] === '_' || $value[0] === '\\' || ctype_print($value)) {
            return self::T_IDENTIFIER;
        }

        return $type;
    }

    /**
     * @param int $position
     */
    public function seek($position): void
    {
        if (!isset($this->tokens[$position])) {
            throw new \OutOfBoundsException("invalid seek position ($position)");
        }

        $this->position = $position;
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @return Token
     */
    public function current(): Token
    {
        return $this->tokens[$this->position];
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * @return $this
     */
    public function next(): Lexer
    {
//        echo $this->tokens[$this->position]->getContent();
        $this->position += $this->sense;

        return $this;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->tokens[$this->position]);
    }
}
