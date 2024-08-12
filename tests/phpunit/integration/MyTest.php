<?php

use MiniORM\UnitOfWork;
use Workflows\Definition\Workflow;
use Workflows\Runtime\RtWorkflow;

final class MyTest extends MediaWikiIntegrationTestCase {
    /**
     * @throws ReflectionException
     * @throws MWException
     * @throws Exception
     */
    public function test() {
        $uow = UnitOfWork::getInstance();
        $uow->getDBR()->insert( "wfs_rt_context", );
    }
}