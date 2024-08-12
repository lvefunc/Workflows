<?php

namespace Workflows\Runtime\Context;

use MiniORM\Entity;
use Workflows\Value\Value;

/**
 * @Table(name: "wfs_rt_variable")
 */
final class Variable extends Entity {
    /**
     * @Column(name: "name", type: "varbinary", length: 255, nullable: false)
     */
    private string $name;

    /**
     * @Column(name: "value_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Value\Value")
     */
    private Value $value;

    /**
     * @Column(name: "context_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Runtime\Context\Context")
     */
    private ?Context $context;

    public function __construct( string $name, Value $value, ?Context $context = null ) {
        parent::__construct();
        $this->name = $name;
        $this->value = $value;
        $this->context = $context;
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

    public function getContext() : ?Context {
        return $this->context;
    }

    public function setContext( Context $context ) : void {
        $this->context = $context;
        $this->markAsDirty();
    }
}