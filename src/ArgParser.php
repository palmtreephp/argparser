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
     * ArgParser constructor.
     *
     * @param array                  $args
     * @param string                 $primary
     * @param NameConverterInterface $nameConverter
     */
    public function __construct($args = [], $primary = '', $nameConverter = null)
    {
        if (is_string($args) && func_num_args() === 2) {
            $args = [$primary => $args];
        }
/**
 * Class Template
 */
        // By default we convert args like error_message to errorMessage
        if (!$nameConverter instanceof NameConverterInterface) {
            $nameConverter = new SnakeCaseToCamelCaseNameConverter();
        }

        $this->setNameConverter($nameConverter);

        $this->setArgs($args);
    }

    /**
     *
     * @param      $object
     * @param bool $removeCalled
     */
    public function parseSetters($object, $removeCalled = true)
    {
        $callable = [$object];
        foreach ($this->getArgs() as $key => $value) {
            if ($this->nameConverter instanceof NameConverterInterface) {
                $key = $this->nameConverter->normalize($key);
            } else {
                $key = ucfirst($key);
            }

            // If the key is 'name', see if 'setName' is a callable method on $object.
            $method      = 'set' . $key;
            $callable[1] = $method;

            if (is_callable($callable)) {
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
     * @param array $defaults
     *
     * @return array
     */
    public function resolveOptions($defaults)
    {
        $resolved = array_replace_recursive($defaults, $this->getArgs());

        return $resolved;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param NameConverterInterface $nameConverter
     *
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
