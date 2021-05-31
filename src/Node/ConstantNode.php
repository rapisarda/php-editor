<?php


namespace PhpEditor\Node;


use PhpEditor\Visitor;

class ConstantNode extends AbstractDocumentableNode implements VisibilityAwareInterface
{
    use VisibilityAwareTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ConstantNode
     */
    public function setName(string $name): ConstantNode
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return ConstantNode
     */
    public function setValue(string $value): ConstantNode
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param Visitor $visitor
     */
    public function accept(Visitor $visitor)
    {
        $visitor->visitConstant($this);
    }
}