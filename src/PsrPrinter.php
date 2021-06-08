<?php declare(strict_types=1);

namespace PhpEditor;

use PhpEditor\Node\AbstractDocumentableNode;
use PhpEditor\Node\AnnotationNode;
use PhpEditor\Node\ArgumentNode;
use PhpEditor\Node\ClassLikeNode;
use PhpEditor\Node\ConstantNode;
use PhpEditor\Node\MethodNode;
use PhpEditor\Node\PropertyNode;
use PhpEditor\Node\RootNode;

class PsrPrinter extends Visitor
{
    private const MAX_LENGTH = 120;

    /**
     * @var string|null
     */
    private $build;

    /**
     * @var string
     */
    private $indentation = '    ';

    /**
     * @var int
     */
    private $level = 0;

    private $col = 0;
    private $line = 0;

    /**
     * @param RootNode $node
     * @return string|null
     */
    public function dump(RootNode $node)
    {
        $this->level = 0;
        $node->accept($this);
        $this->build = implode("\n", array_map(static function ($toto) {
            return rtrim($toto);
        }, explode("\n", $this->build)));
        return $this->build;
    }

    /**
     * @param RootNode $node
     */
    public function visitRoot(RootNode $node): void
    {
        $this->build = "<?php\n\n";

        if ($node->getDocComment()) {
            $this->build .= "{$node->getDocComment()}\n\n";
        }
        if ($node->getDeclare()) {
            $this->build .= "declare({$node->getDeclare()});\n\n";
        }
        if ($node->getNamespace()) {
            $this->build .= "namespace {$node->getNamespace()};\n\n";
        }
        if ($node->getUses()) {
            $uses = array_unique($node->getUses());
            sort($uses);
            foreach ($uses as $use) {
                $this->build .= "use $use;\n";
            }
            $this->build .= "\n";
        }
        if ($node->getUseFunctions()) {
            $uses = array_unique($node->getUseFunctions());
            sort($uses);
            foreach ($uses as $use) {
                $this->build .= "use function $use;\n";
            }
            $this->build .= "\n";
        }
        if ($node->getUseConstants()) {
            $uses = array_unique($node->getUseConstants());
            sort($uses);
            foreach ($uses as $use) {
                $this->build .= "use const $use;\n";
            }
            $this->build .= "\n";
        }

        if ($node->getClassLike()) {
            $node->getClassLike()->accept($this);
        }
    }

    /**
     * @param ClassLikeNode $node
     */
    public function visitClassLike(ClassLikeNode $node): void
    {
        $this->visitDocumentable($node);
        if ($node->isAbstract()) {
            $this->build .= "abstract ";
        }

        $this->build .= $node->getType().' ';
        $this->build .= $node->getName();
        if ($node->getExtends()) {
            $this->build .= " extends {$node->getExtends()}";
        }
        if ($node->getImplements()) {
            $this->build .= ' implements '.implode(', ', $node->getImplements());
        }
        $this->build .= "\n{\n";
        $this->level++;
        $hasSomething = false;

        foreach ($node->getTraits() as $trait) {
            $this->build .= "{$this->indent()}use {$trait};\n";
            $hasSomething = true;
        }

        if ($hasSomething && $node->hasConstants()) {
            $this->build .= "\n";
        }

        $first = true;
        foreach ($node->getConstants() as $const) {
            $first || ($const->hasDocComment() && $this->build .= "\n");
            $const->accept($this);
            $first = false;
            $hasSomething = true;
        }

        if ($hasSomething && $node->hasProperties()) {
            $this->build .= "\n";
        }

        $first = true;
        foreach ($node->getProperties() as $prop) {
            $first || ($prop->hasDocComment() && $this->build .= "\n");
            $prop->accept($this);
            $first = false;
            $hasSomething = true;
        }

        if ($hasSomething && $node->hasMethods()) {
            $this->build .= "\n";
        }

        $first = true;
        foreach ($node->getMethods() as $method) {
            $first || $this->build .= "\n";
            $method->accept($this);
            $first = false;
        }

        $this->build .= "}\n";

    }

    /**
     * @param ConstantNode $node
     */
    public function visitConstant(ConstantNode $node): void
    {
        $this->visitDocumentable($node);
        $visibility = $node->getVisibility();
        $visibility = $visibility ? $visibility .' ' : '';
        $this->build .=
            $this->indentation
            .$visibility
            .'const '
            .$node->getName()
            . ' = '.$node->getValue()
            .";\n"
        ;
    }

    /**
     * @param PropertyNode $node
     */
    public function visitProperty(PropertyNode $node): void
    {
        $this->visitDocumentable($node);
        $this->build .= $this->indentation.$node->getVisibility().' ';
        if ($node->isStatic()) {
            $this->build .= 'static ';
        }
        $this->build .= $node->getName();

        if (null !== $node->getValue()) {
            $this->build .= ' = '.$node->getValue();
        }
        $this->build .= ";\n";
    }

    /**
     * @param MethodNode $node
     */
    public function visitMethod(MethodNode $node): void
    {
        $this->visitDocumentable($node);
        $argsArray = array_map([$this, 'visitParameters'], $node->getArguments());
        $argsPart = implode(', ', $argsArray);
        $argsPart = "({$argsPart})";
        $build =
            $this->indentation
            .($node->isFinal() ? 'final ' : '')
            .$node->getVisibility().' '
            .($node->isStatic() ? 'static ' : '')
            .($node->isAbstract() ? 'abstract ' : '')
            .'function '
            .$node->getName()
        ;

        if ($type = $node->getReturnType()) {
            $type   = ': '.($node->isNullable() ? '?' : '').$type;
        }
        $type = $type ?? '';
        $rowLength = mb_strlen($build) + mb_strlen($argsPart) + mb_strlen($type);
        $indented = false;
        if ($rowLength > self::MAX_LENGTH) {
            $this->level++;
            $argsPart = "(\n{$this->indent()}".implode(",\n{$this->indent()}", $argsArray);
            $this->level--;
            $argsPart .= "\n{$this->indent()})";
            $indented = true;
        }
        $build .= $argsPart.$type;

        if ($node->isAbstract()) {
            $build .= ';';
        } else {
            $build .= $indented ? " {" : "\n$this->indentation{";
            $build .= "{$node->getBody()}}\n";
        }

        $this->build .= $build;
    }

    public function visitParameters(ArgumentNode $node): string
    {
        $build = $node->isNullable() ? '?' : '';
        if ($type =  $node->getType()) {
            $build .= $type.' ';
        }
        $build .= $node->getName();
        if ($default = $node->getDefault()) {
            $build .= " = $default";
        }
        return $build;
    }

    /**
     * @param AbstractDocumentableNode $node
     * @return bool
     */
    private function visitDocumentable(AbstractDocumentableNode $node)
    {
        if ($node->hasDocComment() && $stmts = $node->getDocComment()->getStatements()) {
            foreach ($stmts as $k => $stmt) {
                if ($stmt instanceof AnnotationNode) {
                    $stmts[$k] = $this->dumpAnnotation($stmt);
                }
            }
            $indent = $this->indent();
            $this->build .= "{$indent}/**\n{$indent} * ".implode("\n{$indent} * ", $stmts)."\n{$indent} */\n";
            return true;
        }
        return false;
    }

    private function dumpAnnotation($node)
    {
        if ($node instanceof AnnotationNode) {
            $values = [];
            foreach ($node->getValues() as $k => $v) {
                $values[] = (is_int($k) ? '' : "$k=") . $this->dumpAnnotation($v);
            }
            $values = implode(', ', $values);
            return "@{$node->getName()}($values)";
        }
        switch (gettype($node)) {
            case 'array':
                $preOut = [];
                foreach ($node as $k => $v) {
                    $preOut[] = (is_int($k) ? '' : "\"$k\": ") . $this->dumpAnnotation($v);
                }
                return '{'.implode(', ', $preOut).'}';
            case 'boolean':
                return $node ? 'true' : 'false';
            case 'double':
            case 'integer':
                return $node;
            case 'string':

                return "\"$node\"";
        }

        throw new \ParseError();
    }

    /**
     * @return string
     */
    private function indent()
    {
        return str_repeat($this->indentation, $this->level);
    }

    public function add(string $build = ''): void
    {
        $lines = explode(PHP_EOL, $build);
        $this->line += count($lines);
        $line = end($lines);
        $this->col = mb_strlen($line);
    }
}
