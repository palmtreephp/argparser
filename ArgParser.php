<?php

namespace Palmtree\ArgParser;

class ArgParser
{
    protected $args;

    /**
     * ArgParser constructor.
     *
     * @param array|mixed $args       Array of arguments to parse or the primary argument's value.
     * @param string      $primaryArg Primary argument key.
     */
    public function __construct($args = [], $primaryArg = '')
    {
        // If $args is not an array and $primaryArg is set we
        // assume $args is the primary argument's value.
        if (! is_array($args) && func_num_args() > 1) {
            $args = [$primaryArg => $args];
        }

        $this->args = $args;
    }

    /**
     * Iterates through the args array searching for method setters on $object.
     * e.g: If the key 'name' is set, call $object->setName() if it is callable.
     *
     * @param object $object
     * @param bool   $removeCalled Whether to remove matching method keys from the args array.
     */
    public function parseSetters($object, $removeCalled = true)
    {
        $callable = [$object];
        foreach ($this->args as $key => $value) {
            // e.g: If the key is 'name', see if 'setName' is a callable method on $object.
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
