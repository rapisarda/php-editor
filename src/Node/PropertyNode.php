<?php


namespace PhpEditor\Node;


use PhpEditor\Visitor;

class PropertyNode extends AbstractDocumentableNode implements VisibilityAwareInterface
{
    use VisibilityAwareTrait;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isStatic = false;

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return PropertyNode
     */
    public function setValue(string $value): PropertyNode
    {
        $this->value = $value;
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
     * @return PropertyNode
     */
    public function setName(string $name): PropertyNode
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    /**
     * @param bool $isStatic
     * @return PropertyNode
     */
    public function setIsStatic(bool $isStatic): PropertyNode
    {
        $this->isStatic = $isStatic;
        return $this;
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitProperty($this);
    }
}