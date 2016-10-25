<?php

namespace Palmtree\ArgParser;

class ArgParser {
	protected $args;

	/**
	 * ArgParser constructor.
	 *
	 * @param array  $args
	 * @param string $primary
	 */
	public function __construct( $args = [], $primary = '' ) {
		if ( is_string( $args ) && func_num_args() === 2 ) {
			$args = [ $primary => $args ];
		}

		$this->args = $args;
	}

	/**
	 *
	 * @param      $object
	 * @param bool $removeCalled
	 */
	public function parseSetters( $object, $removeCalled = true ) {
		$callable = [ $object ];
		foreach ( $this->args as $key => $value ) {
			$method      = 'set' . ucfirst( $key );
			$callable[1] = $method;
			if ( is_callable( $callable ) ) {
				$object->$method( $value );

				if ( $removeCalled ) {
					unset( $this->args[ $key ] );
				}
			}
		}
	}

	/**
	 * @param $defaults
	 *
	 * @return array
	 */
	public function resolveOptions( $defaults ) {
		$resolved = array_replace_recursive( $defaults, $this->args );

		return $resolved;
	}

	/**
	 * @return array|string
	 */
	public function getArgs() {
		return $this->args;
	}
}
