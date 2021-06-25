<?php declare(strict_types=1);

namespace PhpEditor\Node;

use PhpEditor\AbstractNode;

class DocNode extends AbstractNode
{
    /** @var string */
    private $name;
    /** @var null|string */
    private $type;
    /** @var null|string */
    private $var;
    /** @var null|string */
    private $comment;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return DocNode
     */
    public function setName(string $name): DocNode
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return DocNode
     */
    public function setType(?string $type): DocNode
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVar(): ?string
    {
        return $this->var;
    }

    /**
     * @param string|null $var
     * @return DocNode
     */
    public function setVar(?string $var): DocNode
    {
        $this->var = $var;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return DocNode
     */
    public function setComment(?string $comment): DocNode
    {
        $this->comment = $comment;
        return $this;
    }
}
