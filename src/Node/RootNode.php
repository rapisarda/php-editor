<?php declare(strict_types=1);

namespace PhpEditor\Node;

use PhpEditor\Visitor;

class RootNode extends AbstractDocumentableNode
{
    /** @var string|null */
    private $declare;
    /** @var string|null */
    private $namespace;
    /** @var string[] */
    private $uses = [];
    /** @var string[] */
    private $useFunctions = [];
    /** @var string[] */
    private $useConstants = [];
    /** @var ClassLikeNode */
    private $classLike;

    /**
     * @return string
     */
    public function getDeclare(): ?string
    {
        return $this->declare;
    }

    /**
     * @param string $declare
     * @return RootNode
     */
    public function setDeclare(string $declare): RootNode
    {
        $this->declare = $declare;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     * @return RootNode
     */
    public function setNamespace(string $namespace): RootNode
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @param string[] $uses
     * @return RootNode
     */
    public function setUses(array $uses): RootNode
    {
        $this->uses = $uses;
        return $this;
    }

    /**
     * @param string $use
     * @return RootNode
     */
    public function addUse(string $use): RootNode
    {
        $this->uses[$use] = $use;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getUseFunctions(): array
    {
        return $this->useFunctions;
    }

    /**
     * @param string[] $useFunctions
     * @return RootNode
     */
    public function setUseFunctions(array $useFunctions): RootNode
    {
        $this->useFunctions = $useFunctions;
        return $this;
    }

    /**
     * @param string $useFunction
     * @return RootNode
     */
    public function addUseFunction(string $useFunction): RootNode
    {
        $this->useFunctions[] = $useFunction;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getUseConstants(): array
    {
        return $this->useConstants;
    }

    /**
     * @param string[] $useConstants
     * @return RootNode
     */
    public function setUseConstants(array $useConstants): RootNode
    {
        $this->useConstants = $useConstants;
        return $this;
    }

    /**
     * @param string $useConst
     * @return RootNode
     */
    public function addUseConstant(string $useConst): RootNode
    {
        $this->useConstants[] = $useConst;
        return $this;
    }

    /**
     * @return ClassLikeNode
     */
    public function getClassLike(): ClassLikeNode
    {
        return $this->classLike;
    }

    /**
     * @param ClassLikeNode $classLike
     * @return RootNode
     */
    public function setClassLike(ClassLikeNode $classLike): RootNode
    {
        $this->classLike = $classLike;
        return $this;
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitRoot($this);
    }
}
