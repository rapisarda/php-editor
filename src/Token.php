<?php declare(strict_types=1);

namespace PhpEditor;

class Token
{
    /**
     * @var int|string
     */
    private $type = - 1;

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var int
     */
    private $line;

    public function __construct($token)
    {
        if (is_array($token)) {
            $this->setType($token[0]);
            $this->setContent($token[1]);
            $this->line = $token[2];
        } else {
            $this->setType($token);
            $this->setContent($token);
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function is($id): bool
    {
        return $this->type === $id;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function isAny(array $ids): bool
    {
        return in_array($this->type, $ids, true);
    }

    public function isDocToken(): bool
    {
        return $this->type > 999;
    }

    /**
     * @return int|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Token
     */
    public function setType($type): Token
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Token
     */
    public function setContent(string $content): Token
    {
        $this->content = $content;
        return $this;
    }

    public function getName()
    {
        if (is_int($this->type)) {
            if (!$this->isDocToken()) {
                return token_name($this->type);
            }
            $reflClass = new \ReflectionClass(Lexer::class);
            $constants = $reflClass->getConstants();

            foreach ($constants as $name => $value) {
                if ($value === $this->type) {
                    return 'Lexer::' . $name;
                }
            }

        }

        return $this->type;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }
}
