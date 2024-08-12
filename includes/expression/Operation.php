<?php

namespace Workflows\Expression;

/**
 * @Table(name: "wfs_expr_operation")
 * @BaseEntity(name: "Workflows\Expression\Expression")
 */
abstract class Operation extends Expression {
    /**
     * @Column(name: "left_expr_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Expression\Expression")
     */
    protected Expression $left;

    /**
     * @Column(name: "right_expr_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Expression\Expression")
     */
    protected Expression $right;

    public function __construct( Expression $left, Expression $right ) {
        parent::__construct();
        $this->setLeft( $left );
        $this->setRight( $right );
    }

    public function getLeft() : Expression {
        return $this->left;
    }

    public function setLeft( Expression $left ) : void {
        $this->left = $left;
        $this->markAsDirty();
    }

    public function getRight() : Expression {
        return $this->right;
    }

    public function setRight( Expression $right ) : void {
        $this->right = $right;
        $this->markAsDirty();
    }
}