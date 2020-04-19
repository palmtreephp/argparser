<?php

namespace Palmtree\ArgParser;

use Palmtree\NameConverter\NameConverterInterface;
use Palmtree\NameConverter\SnakeCaseToCamelCaseNameConverter;

class ArgParser
{
    /** @var NameConverterInterface */
    protected $nameConverter;
    /** @var array */
    protected $args;

    /**
     * @param array|string $args
     */
    public function __construct($args = [], ?string $primary = '', ?NameConverterInterface $nameConverter = null)
    {
        if (\is_string($args) && \func_num_args() > 1) {
            $args = [$primary => $args];
        }

        // By default we convert args like error_message to errorMessage
        if (!$nameConverter instanceof NameConverterInterface) {
            $nameConverter = new SnakeCaseToCamelCaseNameConverter();
        }

        $this->setNameConverter($nameConverter);

        $this->setArgs($args);
    }

    /**
     * @param object $object
     */
    public function parseSetters($object, bool $removeCalled = true): void
    {
        $callable = [$object];
        foreach ($this->args as $key => $value) {
            if ($this->nameConverter instanceof NameConverterInterface) {
                $key = $this->nameConverter->normalize($key);
            } else {
                $key = ucfirst($key);
            }

            // If the key is 'name', see if 'setName' is a callable method on $object.
            $method      = 'set' . $key;
            $callable[1] = $method;

            if (\is_callable($callable)) {
                $object->$method($value);

                if ($removeCalled) {
                    unset($this->args[$key]);
                }
            }
        }
    }

    /**
     * Returns an array of resolved options from the current arguments
     * and given defaults.
     */
    public function resolveOptions(array $defaults): array
    {
        return array_replace_recursive($defaults, $this->args);
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function setNameConverter(NameConverterInterface $nameConverter): self
    {
        $this->nameConverter = $nameConverter;

        return $this;
    }

    public function getNameConverter(): NameConverterInterface
    {
        return $this->nameConverter;
    }

    public function setArgs(array $args): self
    {
        $this->args = $args;

        return $this;
    }
}
