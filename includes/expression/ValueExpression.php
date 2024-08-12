<?php

namespace Workflows\Expression;

use Workflows\Runtime\Context\Context;
use Workflows\Value\Value;

/**
 * @Table(name: "wfs_expr_value")
 * @BaseEntity(name: "Workflows\Expression\Expression")
 */
final class ValueExpression extends Expression {
    /**
     * @Column(name: "value_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Value\Value")
     */
    private Value $value;

    public function __construct( Value $value ) {
        parent::__construct();
        $this->setValue( $value );
    }

    public function evaluate( Context $context ) {
        return $this->getValue()->dereference();
    }

    public function getValue() : Value {
        return $this->value;
    }

    public function setValue( Value $value ) : void {
        $this->value = $value;
        $this->markAsDirty();
    }
}