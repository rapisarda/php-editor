<?php


namespace PhpEditor\Node;


interface VisibilityAwareInterface
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';
    public const VISIBILITY_PRIVATE = 'private';

    public function getVisibility();
    public function setVisibility(string $visibility);
}