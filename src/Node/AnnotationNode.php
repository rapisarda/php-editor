<?php declare(strict_types=1);

namespace PhpEditor\Node;

use PhpEditor\AbstractNode;

class AnnotationNode extends AbstractNode
{
    /** @var string */
    private $name;
    /** @var array */
    private $values;

    /**
     * AnnotationNode constructor.
     * @param string|null $name
     * @param array $values
     */
    public function __construct(string $name = null, array $values = [])
    {
        $this->name = $name;
        $this->values = $values;
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
     * @return AnnotationNode
     */
    public function setName(string $name): AnnotationNode
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getValues(): ?array
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @return AnnotationNode
     */
    public function setValues(array $values): AnnotationNode
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param $value
     * @param string|null $key
     */
    public function addValue($value, ?string $key = null)
    {
        $key ? $this->values[$key] = $value : $this->values[] = $value;
        return $this;
    }
}
