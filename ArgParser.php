<?php

namespace Palmtree\ArgParser;

class ArgParser
{
    protected $args;

    /**
     * ArgParser constructor.
     *
     * @param array  $args
     * @param string $primary
     */
    public function __construct($args = [], $primary = '')
    {
        if (is_string($args) && func_num_args() === 2) {
            $args = [$primary => $args];
        }

        $this->args = $args;
    }

    /**
     *
     * @param      $object
     * @param bool $removeCalled
     */
    public function parseSetters($object, $removeCalled = true)
    {
        $callable = [$object];
        foreach ($this->args as $key => $value) {
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
}
