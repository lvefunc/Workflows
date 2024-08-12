<?php

namespace Workflows\Value;

use Exception;

/**
 * @Table(name: "wfs_value_text")
 * @BaseEntity(name: "Workflows\Value\Value")
 */
final class Text extends Value {
    /**
     * @Column(name: "value", type: "mediumblob", nullable: false)
     */
    private string $value;

    /**
     * @param string $value
     *
     * @throws Exception
     */
    public function __construct( string $value ) {
        parent::__construct( $value );
    }

    /**
     * @return string
     */
    public function dereference() : string {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function reference( $value ) : void {
        $this->value = $value;
        $this->markAsDirty();
    }
}