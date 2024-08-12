<?php

namespace Workflows\Definition\Element\Gateway;

use Exception;
use Workflows\Runtime\Element\Gateway\RtExclusiveGateway;
use Workflows\Runtime\Element\RtElement;

/**
 * @Table(name: "wfs_def_element_exclusive_gateway")
 * @BaseEntity(name: "Workflows\Definition\Element\Gateway\Gateway")
 */
final class ExclusiveGateway extends Gateway {
    /**
     * @throws Exception
     */
    public function createRuntimeInstance() : RtElement {
        return new RtExclusiveGateway( $this->getName(), $this->getDirection() );
    }
}