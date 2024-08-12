<?php

namespace Workflows\Expression;

use Workflows\Runtime\Context\Context;
use Workflows\Value\Value;

/**
 * @Table(name: "wfs_expr_variable")
 * @BaseEntity(name: "Workflows\Expression\Expression")
 */
final class VariableExpression extends Expression {
    /**
     * @Column(name: "variable_name", type: "varbinary", length: 255, nullable: false)
     */
    private string $variableName;

    public function __construct( string $variableName ) {
        parent::__construct();
        $this->setVariableName( $variableName );
    }

    public function evaluate( Context $context ) {
        return $context->getVariableValue( $this->getVariableName() )->dereference();
    }

    public function getVariableName() : string {
        return $this->variableName;
    }

    public function setVariableName( string $variableName ) : void {
        $this->variableName = $variableName;
        $this->markAsDirty();
    }
}