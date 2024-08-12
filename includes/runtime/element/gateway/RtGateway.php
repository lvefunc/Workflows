<?php

namespace Workflows\Runtime\Element\Gateway;

use Workflows\Enumeration\GatewayDirection;
use Workflows\Runtime\Element\RtElement;

/**
 * @Table(name: "wfs_rt_element_gateway")
 * @BaseEntity(name: "Workflows\Runtime\Element\RtElement")
 */
abstract class RtGateway extends RtElement {
    /**
     * @Column(name: "direction", type: "int", nullable: false)
     */
    private int $direction;

    public function __construct( string $name, int $direction ) {
        parent::__construct( $name );
        GatewayDirection::verify( $direction );
        $this->direction = $direction;
    }

    public function getDirection() : int {
        return $this->direction;
    }
}