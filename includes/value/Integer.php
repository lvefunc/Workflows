<?php

namespace Workflows\Value;

use Exception;

/**
 * @Table(name: "wfs_value_int")
 * @BaseEntity(name: "Workflows\Value\Value")
 */
final class Integer extends Value {
    /**
     * @Column(name: "value", type: "int", nullable: false)
     */
    private int $value;

    /**
     * @param int $value
     *
     * @throws Exception
     */
    public function __construct( int $value ) {
        parent::__construct( $value );
    }

    /**
     * @return int
     */
    public function dereference() : int {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function reference( $value ) : void {
        $this->value = $value;
        $this->markAsDirty();
    }
}