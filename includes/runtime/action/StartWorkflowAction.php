<?php

namespace Workflows\Runtime\Action;

use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use RequestContext;
use StatusValue;
use Workflows\Runtime\RtWorkflow;

final class StartWorkflowAction implements IAction {
    private RtWorkflow $workflow;

    public function __construct( RtWorkflow $workflow ) {
        $this->workflow = $workflow;
    }

    /**
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() : StatusValue {
        $user = RequestContext::getMain()->getUser();

        if (
            !$this->getWorkflow()->getOwner()->equals( $user ) &&
            !$user->isAllowed( "workflows-admin-powers" )
        ) {
            throw new MWException( "You are not allowed to start this workflow" );
        }

        $this->getWorkflow()->startExecution();
        UnitOfWork::getInstance()->commit();

        return StatusValue::newGood();
    }

    public function getWorkflow() : RtWorkflow {
        return $this->workflow;
    }

    public function setWorkflow( RtWorkflow $workflow ) : void {
        $this->workflow = $workflow;
    }
}