<?php

declare(strict_types=1);

namespace Vendor\Package;

use Vendor\Package\SomeNamespace\ClassD as D;
use Vendor\Package\{ClassA as A, ClassB, ClassC as C};

use function Vendor\Package\{functionA, functionB, functionC};

use const Vendor\Package\{ConstantA, ConstantB, ConstantC};

/**
 * Class Foo
 *
 * @package Vendor\Package
 *
 * @Route("hello", "salut", {"hello"}, {"hello": "World"}, @salut({"hello"}))
 *
 */
class MockClassExact extends Bar implements FooInterface
{
    use salutTrait;

    const FOOD = 'FOO';

    /**
     * const FOO|Bar[]
     */
    const FOO = 'FOO';
    const FOO_FOO = 'FOO';
    const FOO_BAR = 'FOO';
    const FOO_BAZ = 'FOO';

    public $foo = 'foo';
    protected $bar = 0;

    /**
     * @var int
     */
    protected $baz = 0;
    protected $fooBar = 0;

    /**
     * @var string|string[]
     */
    private $bazFoo = self::FOO;

    /**
     * @param int $a
     * @param int|null $b
     * @return array
     */
    public function sampleFunction(int $a, ?int $b = null): array
    {
        if ($a === $b) {
            bar();
        } elseif ($a > $b) {
            $foo->bar($arg1);
        } else {
            BazClass::bar($arg2, $arg3);
        }
    }

    private function awesomeLongMethod(
        WithVery $long = 'parameters',
        AndOther $var = 'someVars',
        AndOther $iable = 'll'
    ): yo {
        //some body
    }

    /**
     * Return version avec contrat sans numero externe
     * @return Version
     */
    final public static function bar()
    {
        // method body
    }

    abstract function hiThere();
}
