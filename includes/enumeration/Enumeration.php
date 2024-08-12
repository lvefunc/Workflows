<?php

namespace Workflows\Enumeration;

use MWException;
use ReflectionClass;

abstract class Enumeration {
    /**
     * @throws MWException In case value does not correspond to any of the constants
     */
    public static final function verify( $value ) {
        $reflectionClass = new ReflectionClass( static::class );
        $constants = array_values( $reflectionClass->getConstants() );

        if ( !in_array( $value, $constants ) ) {
            throw new MWException(
                "\"" . $value . "\" is not a value contained in one of the " .
                "\"" . $reflectionClass->getName() . "\" class constants!"
            );
        }
    }

    /**
     * @throws MWException
     */
    public static final function valueOf( $constant ) {
        $reflectionClass = new ReflectionClass( static::class );
        $constants = $reflectionClass->getConstants();

        if ( !isset( $constants[$constant] ) ) {
            throw new MWException(
                "Can't retrieve value of \"" . $constant . "\" " .
                "since it is not a valid constant that is contained in " .
                "\"" . $reflectionClass->getName() . "\" class!"
            );
        }

        return $constants[$constant];
    }

    /**
     * @throws MWException
     */
    public static final function toString( $value ) {
        $reflectionClass = new ReflectionClass( static::class );
        $constants = array_flip( $reflectionClass->getConstants() );

        if ( !isset( $constants[$value] ) ) {
            throw new MWException(
                "Can't retrieve constant name of \"" . $value . "\" " .
                "since there are no constants contained in " .
                "\"" . $reflectionClass->getName() . "\" that have such value"
            );
        }

        return $constants[$value];
    }
}