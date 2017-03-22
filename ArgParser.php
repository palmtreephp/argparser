<?php

namespace Palmtree\ArgParser;

use Palmtree\NameConverter\NameConverterInterface;

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
            // If the key is 'name', see if 'setName' is a callable method on $object.
            $method      = 'set' . ucfirst($key);
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
        if ($this->nameConverter instanceof NameConverterInterface) {
            foreach ($args as $key => $value) {
                $normalizedKey = $this->nameConverter->normalize($key);
                if ($normalizedKey !== $key) {
                    $args[$normalizedKey] = $value;
                    unset($args[$key]);
                }
            }
        }

        $this->args = $args;

        return $this;
    }
}
