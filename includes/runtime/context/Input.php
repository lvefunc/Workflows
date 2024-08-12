<?php

namespace Workflows\Runtime\Context;

use MiniORM\Entity;
use Workflows\Value\Value;

/**
 * @Table(name: "wfs_rt_input")
 */
final class Input extends Entity {
    /**
     * @Column(name: "name", type: "varbinary", length: 255, nullable: false)
     */
    private string $name;

    /**
     * @Column(name: "value_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Value\Value")
     */
    private Value $value;

    public function __construct( string $name, Value $value ) {
        parent::__construct();
        $this->setName( $name );
        $this->setValue( $value );
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName( string $name ) : void {
        $this->name = $name;
        $this->markAsDirty();
    }

    public function getValue() : Value {
        return $this->value;
    }

    public function setValue( Value $value ) : void {
        $this->value = $value;
        $this->markAsDirty();
    }
}