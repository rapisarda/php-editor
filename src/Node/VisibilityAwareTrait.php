<?php


namespace PhpEditor\Node;


trait VisibilityAwareTrait
{
    /**
     * @var string|null
     */
    private $visibility = VisibilityAwareInterface::VISIBILITY_PUBLIC;

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(?string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }
}