<?php declare(strict_types=1);

namespace PhpEditor;

use PhpEditor\Node\ClassLikeNode;
use PhpEditor\Node\ConstantNode;
use PhpEditor\Node\MethodNode;
use PhpEditor\Node\PropertyNode;
use PhpEditor\Node\RootNode;

abstract class Visitor
{
    public function visitRoot(RootNode $node): void {}
    public function visitClassLike(ClassLikeNode $node): void {}
    public function visitProperty(PropertyNode $node): void {}
    public function visitMethod(MethodNode $node): void {}
    public function visitConstant(ConstantNode $node): void {}
}
