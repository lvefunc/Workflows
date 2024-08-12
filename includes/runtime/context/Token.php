<?php

namespace Workflows\Runtime\Context;

use MiniORM\Entity;
use Workflows\Runtime\Element\RtElement;

/**
 * @Table(name: "wfs_rt_token")
 */
final class Token extends Entity {
    /**
     * @Column(name: "initiator_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\Element\RtElement")
     */
    private RtElement $initiator;

    /**
     * @Column(name: "position_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\Element\RtElement")
     */
    private RtElement $position;

    /**
     * @Column(name: "parent_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Runtime\Context\Token")
     */
    private ?Token $parent = null;

    /**
     * @var Token[]
     * @OneToMany(target: "Workflows\Runtime\Context\Token", mappedBy: "parent")
     */
    private array $children = [];

    /**
     * @Column(name: "context_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Runtime\Context\Context")
     */
    private ?Context $context = null;

    public function __construct( RtElement $initiator, ?Token $parent = null, ?Context $context = null ) {
        parent::__construct();
        $this->initiator = $initiator;
        $this->position = $initiator;

        if ( !is_null( $parent ) ) {
            $parent->addChild( $this );
        }

        if ( !is_null( $context ) ) {
            $context->addToken( $this );
        }
    }

    /**
     * Move this token to a new position
     *
     * @param RtElement $position New position for this token
     */
    public function moveTo( RtElement $position ) {
        $this->position = $position;
        $this->markAsDirty();
    }

    public function getInitiator() : RtElement {
        return $this->initiator;
    }

    public function getPosition() : RtElement {
        return $this->position;
    }

    public function getParent() : ?Token {
        return $this->parent;
    }

    protected function setParent( ?Token $parent ) {
        $this->parent = $parent;
        $this->markAsDirty();
    }

    /**
     * Get all child tokens
     *
     * @return Token[] Child tokens
     */
    public function getChildren() : array {
        return $this->children;
    }

    protected function addChild( Token $token ) {
        foreach ( $this->children as $child ) {
            if ( $child->equals( $token ) ) {
                return;
            }
        }

        $token->setParent( $this );
        $this->children[] = $token;
    }

    public function getContext() : ?Context {
        return $this->context;
    }

    public function setContext( ?Context $context ) : void {
        $this->context = $context;
        $this->markAsDirty();
    }
}