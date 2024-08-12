<?php

namespace Workflows\Definition\Element\Gateway;

use MWException;
use Workflows\Definition\Element\Element;
use Workflows\Enumeration\GatewayDirection;

/**
 * @Table(name: "wfs_def_element_gateway")
 * @BaseEntity(name: "Workflows\Definition\Element\Element")
 */
abstract class Gateway extends Element {
    /**
     * @Column(name: "direction", type: "varbinary", length: 255, nullable: false)
     */
    protected string $direction;

    public function __construct( string $name, string $direction ) {
        parent::__construct( $name );
        $this->setDirection( $direction );
    }

    public function getDirection() : string {
        return $this->direction;
    }

    /**
     * @throws MWException
     */
    public function setDirection( string $direction ) : void {
        GatewayDirection::verify( $direction );
        $this->direction = $direction;
        $this->markAsDirty();
    }
}