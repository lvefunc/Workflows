<?php

namespace Workflows\Enumeration;

use MWException;
use Workflows\Value\Boolean;
use Workflows\Value\Integer;
use Workflows\Value\Text;
use Workflows\Value\Value;

final class ValueType extends Enumeration {
    public const Boolean    = "Boolean";
    public const Integer    = "Integer";
    public const Text       = "Text";

    /**
     * @throws MWException
     */
    public static final function typeOf( Value $value ) : string {
        switch ( get_class( $value ) ) {
            case Boolean::class:    return self::Boolean;
            case Integer::class:    return self::Integer;
            case Text::class:       return self::Text;
            default:                throw new MWException( "Unsupported value type" );
        }
    }
}