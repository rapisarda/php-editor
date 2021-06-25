<?php declare(strict_types=1);

namespace PhpEditor\Node;

class DocBlockNode
{
    private $statements = [];

    /**
     * @return array<string|DocNode|AnnotationNode>
     */
    public function getStatements(): array
    {
        return $this->statements;
    }

    /**
     * @param array $statements
     */
    public function setStatements(array $statements): void
    {
        $this->statements = $statements;
    }

    /**
     * @param AnnotationNode|string $statement
     */
    public function addStatement($statement)
    {
        if (!($statement instanceof AnnotationNode || $statement instanceof DocNode|| is_string($statement))) {
            throw new \InvalidArgumentException();
        }

        $this->statements[] = $statement;
        return $this;
    }

    public function getAnnotationNode(string $name): ?AnnotationNode 
    {
        foreach ($this->statements as $stmt) {
            if ($stmt instanceof AnnotationNode && $stmt->getName() === $name) {
                return $stmt;
            }
        }
        return null;
    }

    public function getDocNode(string $name): ?DocNode
    {
        foreach ($this->statements as $stmt) {
            if ($stmt instanceof DocNode && $stmt->getName() === $name) {
                return $stmt;
            }
        }
        return null;
    }
}
