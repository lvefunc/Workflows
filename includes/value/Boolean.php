<?php

namespace Workflows\Value;

use Exception;

/**
 * @Table(name: "wfs_value_bool")
 * @BaseEntity(name: "Workflows\Value\Value")
 */
final class Boolean extends Value {
    /**
     * @Column(name: "value", type: "tinyint", length: 1, nullable: false)
     */
    private bool $value;

    /**
     * @param bool $value
     *
     * @throws Exception
     */
    public function __construct( bool $value ) {
        parent::__construct( $value );
    }

    /**
     * @return bool
     */
    public function dereference() : bool {
        return $this->value;
    }

    /**
     * @param bool $value
     */
    public function reference( $value ) : void {
        $this->value = $value;
        $this->markAsDirty();
    }
}