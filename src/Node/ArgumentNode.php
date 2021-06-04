<?php declare(strict_types=1);

namespace PhpEditor\Node;

use PhpEditor\AbstractNode;

class ArgumentNode extends AbstractNode
{
    /** @var null|string */
    private $type;
    /** @var string */
    private $name;
    /** @var null|string */
    private $default;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return ArgumentNode
     */
    public function setType(?string $type): ArgumentNode
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ArgumentNode
     */
    public function setName(string $name): ArgumentNode
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @param string|null $default
     * @return ArgumentNode
     */
    public function setDefault(?string $default): ArgumentNode
    {
        $this->default = $default;
        return $this;
    }
}
