<?php declare(strict_types=1);

namespace PhpEditor;

use Generator;
use PhpEditor\Node\RootNode;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RuntimeException;
use SplFileInfo;

class PsrFile
{
    /** @var RootNode */
    private $node;
    /** @var PsrPrinter */
    private $printer;
    /** @var string */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->printer = new PsrPrinter();
    }

    public static function from(string $filename): self
    {
        return new self($filename);
    }

    public function node(): RootNode
    {
        if (null === $this->node) {
            $this->node = is_file($this->filename) ? (new Parser($this->filename))->getAst() : new RootNode();
        }
        return $this->node;
    }

    /** @return Generator|self[] */
    public static function all(string $directory, string $patters = '.*\.php$'): Generator
    {
        $rii = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory)
            ),
            "'^{$directory}{$patters}'"
        );

        /** @var SplFileInfo $file */
        foreach ($rii as $file) {
            if ($file->isFile()){
                yield self::from($file->__toString());
            }
        }
    }

    public function save(string $filename = null): void
    {
        if ($filename) {
            $dir = dirname($filename);
            if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }
        file_put_contents($filename ?? $this->filename, $this->printer->dump($this->node));
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
