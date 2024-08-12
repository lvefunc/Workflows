<?php

namespace Workflows\Expression;

use MWException;
use Workflows\Enumeration\ComparisonType;
use Workflows\Runtime\Context\Context;

/**
 * @Table(name: "wfs_expr_comparison")
 * @BaseEntity(name: "Workflows\Expression\Operation")
 */
final class Comparison extends LogicalExpression {
    /**
     * @Column(name: "type", type: "varbinary", length: 255, nullable: false)
     */
    private string $type;

    public function __construct( Expression $left, Expression $right, string $type ) {
        parent::__construct( $left, $right );
        $this->setType( $type );
    }

    /**
     * @throws MWException
     */
    public function evaluate( Context $context ) : bool {
        $left = $this->getLeft()->evaluate( $context );
        $right = $this->getRight()->evaluate( $context );

        switch ( $this->getType() ) {
            case ComparisonType::EqualTo:
                return $left === $right;
            case ComparisonType::LessThan:
                return $left < $right;
            case ComparisonType::MoreThan:
                return $left > $right;
            case ComparisonType::LessThanOrEqualTo:
                return $left <= $right;
            case ComparisonType::MoreThanOrEqualTo:
                return $left >= $right;
            default:
                throw new MWException( "Unsupported comparison type" );
        }
    }

    public function getType() : string {
        return $this->type;
    }

    /**
     * @throws MWException
     */
    public function setType( string $type ) : void {
        ComparisonType::verify( $type );
        $this->type = $type;
        $this->markAsDirty();
    }
}