<?php


namespace PhpEditor\Node;


use PhpEditor\Visitor;

class ClassLikeNode extends AbstractDocumentableNode
{
    const TYPE_CLASS = 'class';
    const TYPE_INTERFACE = 'interface';
    const TYPE_TRAIT = 'trait';

    /**
     * @var bool
     */
    private $isAbstract = false;

    /**
     * @var bool
     */
    private $isFinal = false;

    /**
     * @var string|self::TYPE_CLASS|self::TYPE_TRAIT|self::TYPE_INTERFACE
     */
    private $type = self::TYPE_CLASS;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $implements = [];

    /**
     * @var string|null
     */
    private $extends;

    /**
     * @var PropertyNode[]
     */
    private $properties = [];

    /**
     * @var MethodNode[]
     */
    private $methods = [];

    /**
     * @var string[]
     */
    private $traits = [];

    /**
     * @var ConstantNode[]
     */
    private $constants = [];

    public function isAbstract()
    {
        return $this->isAbstract;
    }

    public function setIsAbstract(bool $isAbstract)
    {
        $this->isAbstract = $isAbstract;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    /**
     * @param bool $isFinal
     * @return ClassLikeNode
     */
    public function setIsFinal(bool $isFinal): ClassLikeNode
    {
        $this->isFinal = $isFinal;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string
     * @return ClassLikeNode
     */
    public function setType(string $type): ClassLikeNode
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
     * @return ClassLikeNode
     */
    public function setName(string $name): ClassLikeNode
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getImplements(): array
    {
        return $this->implements;
    }

    /**
     * @param string[] $implements
     * @return ClassLikeNode
     */
    public function setImplements(array $implements): ClassLikeNode
    {
        $this->implements = $implements;
        return $this;
    }

    public function addImplement(string $implement)
    {
        $this->implements[] = $implement;
    }

    /**
     * @return string|null
     */
    public function getExtends(): ?string
    {
        return $this->extends;
    }

    /**
     * @param string $extends
     * @return ClassLikeNode
     */
    public function setExtends(string $extends): ClassLikeNode
    {
        $this->extends = $extends;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasProperties(): bool
    {
        return !empty($this->properties);
    }

    /**
     * @return PropertyNode[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param PropertyNode[] $properties
     * @return ClassLikeNode
     */
    public function setProperties(array $properties): ClassLikeNode
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param string $name
     * @return PropertyNode
     */
    public function getProperty(string $name): PropertyNode
    {
        return $this->properties[$name];
    }

    /**
     * @param PropertyNode $property
     * @return ClassLikeNode
     */
    public function addPropertyNode(PropertyNode $property): ClassLikeNode
    {
        $this->properties[$property->getName()] = $property;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMethods(): bool
    {
        return !empty($this->methods);
    }

    /**
     * @return MethodNode[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param MethodNode[] $methods
     * @return ClassLikeNode
     */
    public function setMethods(array $methods): ClassLikeNode
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * @param string $name
     * @return null|MethodNode
     */
    public function getMethod(string $name): ?MethodNode
    {
        return $this->methods[$name] ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasMethod(string $name): bool
    {
        return isset($this->methods[$name]);
    }

    /**
     * @param MethodNode $method
     * @return ClassLikeNode
     */
    public function addMethod(MethodNode $method): ClassLikeNode
    {
        $this->methods[$method->getName()] = $method;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasTraits(): bool
    {
        return !empty($this->traits);
    }

    /**
     * @return string[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param string[] $traits
     * @return ClassLikeNode
     */
    public function setTraits(array $traits): ClassLikeNode
    {
        $this->traits = $traits;
        return $this;
    }

    /**
     * @param string $traits
     * @return ClassLikeNode
     */
    public function addTrait(string $traits): ClassLikeNode
    {
        $this->traits[] = $traits;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasConstants(): bool
    {
        return !empty($this->constants);
    }

    /**
     * @return ConstantNode[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }

    /**
     * @param ConstantNode[] $constants
     * @return ClassLikeNode
     */
    public function setConstants(array $constants): ClassLikeNode
    {
        $this->constants = $constants;
        return $this;
    }

    /**
     * @param ConstantNode $constant
     * @return ClassLikeNode
     */
    public function addConstant(ConstantNode $constant): ClassLikeNode
    {
        $this->constants[] = $constant;
        return $this;
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitClassLike($this);
    }
}