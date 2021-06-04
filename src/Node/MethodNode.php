<?php


namespace PhpEditor\Node;


use PhpEditor\Visitor;

class MethodNode extends AbstractDocumentableNode implements VisibilityAwareInterface
{
    use VisibilityAwareTrait;

    /**
     * @var bool|null
     */
    private $abstract = false;

    /**
     * @var bool
     */
    private $isFinal = false;

    /**
     * @var bool
     */
    private $isStatic = false;

    /**
     * @var string
     */
    private $name;

    /**
     * @todo replace by ParameterNode[]
     * @var string
     */
    private $parameters;

    /** @var ArgumentNode[] */
    private $arguments = [];

    /**
     * @var string|null
     */
    private $returnType;

    /**
     * @var bool
     */
    private $nullable = false;

    /**
     * @var string
     */
    private $body = '';

    /**
     * @return bool
     */
    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    /**
     * @param bool $abstract
     * @return MethodNode
     */
    public function setAbstract(bool $abstract): MethodNode
    {
        $this->abstract = $abstract;
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
     * @return MethodNode
     */
    public function setIsFinal(bool $isFinal): MethodNode
    {
        $this->isFinal = $isFinal;
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
     * @return MethodNode
     */
    public function setIsStatic(bool $isStatic): MethodNode
    {
        $this->isStatic = $isStatic;
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
     * @return MethodNode
     */
    public function setName(string $name): MethodNode
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $parameters
     * @return MethodNode
     */
    public function setParameters(string $parameters): MethodNode
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param ArgumentNode $arg
     * @return $this\
     */
    public function addArgument(ArgumentNode $arg): MethodNode
    {
        $this->arguments[$arg->getName()] = $arg;
        return $this;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return string|null
     */
    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    /**
     * @param string $returnType
     * @return MethodNode
     */
    public function setReturnType(?string $returnType): MethodNode
    {
        $this->returnType = $returnType;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     * @return MethodNode
     */
    public function setNullable(bool $nullable): MethodNode
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return MethodNode
     */
    public function setBody(string $body): MethodNode
    {
        $this->body = $body;
        return $this;
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitMethod($this);
    }

}