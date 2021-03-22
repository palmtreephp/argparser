<?php declare(strict_types=1);

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
        foreach ($this->args as $key => $value) {
            if ($this->nameConverter instanceof NameConverterInterface) {
                $key = $this->nameConverter->normalize($key);
            }

            if ($method = $this->buildSetterMethod($object, $key)) {
                $this->callCallable($object, $method, $value);

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

    private function buildSetterMethod($object, string $key): ?string
    {
        // Setter. e.g setFoo
        $method = 'set' . ucfirst($key);

        if (\is_callable([$object, $method])) {
            return $method;
        }

        // Adder. e.g addFoo
        if (substr($key, -1) === 's') {
            $method = 'add' . ucfirst(substr($key, 0, -1));

            if (\is_callable([$object, $method])) {
                return $method;
            }
        }

        return null;
    }

    private function callCallable($object, $method, $value): void
    {
        if (\is_array($value)) {
            try {
                $parameter = new \ReflectionParameter([$object, $method], 0);
                if ($parameter->isVariadic()) {
                    $object->$method(...$value);

                    return;
                }
            } catch (\ReflectionException $e) {
                // do nothing
            }
        }

        $object->$method($value);
    }
}
