<?php declare(strict_types=1);

namespace PhpEditor\Node;

class DocBlockNode
{
    private $statements = [];

    /**
     * @return array
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
        if (!($statement instanceof AnnotationNode || is_string($statement))) {
            throw new \InvalidArgumentException();
        }

        $this->statements[] = $statement;
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
}
