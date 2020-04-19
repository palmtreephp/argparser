<?php

namespace Palmtree\ArgParser;

use Palmtree\NameConverter\NameConverterInterface;
use Palmtree\NameConverter\SnakeCaseToCamelCaseNameConverter;

class ArgParser
{
    /** @var NameConverterInterface */
    protected $nameConverter;
    protected $args;

    /**
     * @param array|string           $args
     * @param string                 $primary
     * @param NameConverterInterface $nameConverter
     */
    public function __construct($args = [], $primary = '', NameConverterInterface $nameConverter = null)
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
     * @param      $object
     * @param bool $removeCalled
     */
    public function parseSetters($object, $removeCalled = true)
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
     *
     * @return array
     */
    public function resolveOptions(array $defaults)
    {
        return array_replace_recursive($defaults, $this->args);
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return ArgParser
     */
    public function setNameConverter(NameConverterInterface $nameConverter)
    {
        $this->nameConverter = $nameConverter;

        return $this;
    }

    /**
     * @return NameConverterInterface
     */
    public function getNameConverter()
    {
        return $this->nameConverter;
    }

    /**
     * @param array|string $args
     *
     * @return ArgParser
     */
    public function setArgs($args)
    {
        $this->args = $args;

        return $this;
    }
}
