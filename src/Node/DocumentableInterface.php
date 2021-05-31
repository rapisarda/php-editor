<?php


namespace PhpEditor\Node;


interface DocumentableInterface
{
    public function getDocComment(): ?string;
    public function setDocComment(?string $doc);
}