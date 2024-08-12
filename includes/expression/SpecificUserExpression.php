<?php

namespace Workflows\Expression;

use User;
use Workflows\Runtime\Context\Context;

/**
 * @Table(name: "wfs_expr_specific_user")
 * @BaseEntity(name: "Workflows\Expression\Expression")
 */
final class SpecificUserExpression extends UserExpression {
    /**
     * @Column(name: "user", type: "int", nullable: false)
     */
    private User $user;

    public function __construct( User $user ) {
        parent::__construct();
        $this->setUser( $user );
    }

    public function evaluate( Context $context ) : User {
        return $this->getUser();
    }

    public function getUser() : User {
        return $this->user;
    }

    public function setUser( User $user ) : void {
        $this->user = $user;
        $this->markAsDirty();
    }
}