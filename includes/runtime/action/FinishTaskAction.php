<?php

namespace Workflows\Runtime\Action;

use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use StatusValue;
use Workflows\Enumeration\ExecutionState;

final class FinishTaskAction extends TaskAction {
    /**
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() : StatusValue {
        $this->verifyUser();
        $this->getTask()->getState()->setExecutionState( ExecutionState::Completed );
        $this->getTask()->getUserActivity()->notify();
        UnitOfWork::getInstance()->commit();

        return StatusValue::newGood();
    }
}