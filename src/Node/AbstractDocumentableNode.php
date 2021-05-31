<?php


namespace PhpEditor\Node;


use PhpEditor\AbstractNode;

abstract class AbstractDocumentableNode extends AbstractNode
{
    /**
     * @var string
     */
    private $comment = null;

    /**
     * @return bool
     */
    public function hasDocComment(): bool
    {
        return !empty($this->comment);
    }

    /**
     * @return string
     */
    public function getDocComment(): ?DocBlockNode
    {
        return $this->comment;
    }

    /**
     * @param string $doc
     * @return $this
     */
    public function setDocComment(?DocBlockNode $doc)
    {
        $this->comment = $doc;

        return $this;
    }

    /**
     * @param array $docLines
     */
    public function insertAnnotations(array $docLines): void
    {
        $currentDockBlock = $this->comment;
        if (!empty($currentDockBlock)) {
            $docBlockLines = explode("\n", $currentDockBlock);
            $docLines = array_diff($docLines, $docBlockLines);
            if (empty($docLines)) {
                return ;
            }

            $comments = $docBlockLines;
            $currentAnnotations = [];

            foreach ($docBlockLines as $k => $line) {

                if (!empty($line) && $line[0] === '@') {
                    $currentAnnotations = array_slice($docBlockLines, $k);
                    $comments = array_slice($docBlockLines, 0, $k);
                    break;
                }
            }
            !empty($currentAnnotations) && $docLines[] = '';
            $docBlockLines = array_merge(
                $comments,
                $docLines,
                $currentAnnotations
            );
        } else {
            $docBlockLines = $docLines;
        }

        $this->comment = implode("\n", $docBlockLines);
    }
}