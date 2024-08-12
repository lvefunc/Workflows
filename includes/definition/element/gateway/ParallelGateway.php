<?php

namespace Workflows\Definition\Element\Gateway;

use Exception;
use Workflows\Runtime\Element\Gateway\RtParallelGateway;
use Workflows\Runtime\Element\RtElement;

/**
 * @Table(name: "wfs_def_element_parallel_gateway")
 * @BaseEntity(name: "Workflows\Definition\Element\Gateway\Gateway")
 */
final class ParallelGateway extends Gateway {
    /**
     * @throws Exception
     */
    public function createRuntimeInstance() : RtElement {
        return new RtParallelGateway( $this->getName(), $this->getDirection() );
    }
}