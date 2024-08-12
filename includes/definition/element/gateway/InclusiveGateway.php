<?php

namespace Workflows\Definition\Element\Gateway;

use Exception;
use Workflows\Runtime\Element\Gateway\RtInclusiveGateway;
use Workflows\Runtime\Element\RtElement;

/**
 * @Table(name: "wfs_def_element_inclusive_gateway")
 * @BaseEntity(name: "Workflows\Definition\Element\Gateway\Gateway")
 */
final class InclusiveGateway extends Gateway {
    /**
     * @throws Exception
     */
    public function createRuntimeInstance() : RtElement {
        return new RtInclusiveGateway( $this->getName(), $this->getDirection() );
    }
}