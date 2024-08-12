<?php

namespace Workflows\Expression;

use Workflows\Runtime\Context\Context;

/**
 * @Table(name: "wfs_expr_conjunction")
 * @BaseEntity(name: "Workflows\Expression\Operation")
 */
final class Conjunction extends LogicalExpression {
    public function evaluate( Context $context ) : bool {
        $left = $this->getLeft()->evaluate( $context );
        $right = $this->getRight()->evaluate( $context );

        return $left && $right;
    }
}