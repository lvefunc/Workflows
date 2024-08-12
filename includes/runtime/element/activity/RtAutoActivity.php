<?php

namespace Workflows\Runtime\Element\Activity;

use MWException;
use Workflows\Enumeration\ExecutionState;

abstract class RtAutoActivity extends RtActivity {
    public abstract function execute() : void;

    /**
     * @throws MWException
     */
    public function queue() : void {
        $preceding = $this->getWorkflow()->findPrecedingElementOf( $this );

        if ( $preceding->getState()->isCompleted() ) {
            $this->getState()->setExecutionState( ExecutionState::InProgress );
            $this->getWorkflow()->continueExecution();
        }
    }

    /**
     * @throws MWException
     */
    public function end() : void {
        $this->execute();
        $this->getState()->setExecutionState( ExecutionState::Completed );

        $succeeding = $this->getWorkflow()->findElementSucceedingTo( $this );
        $succeeding->queue();
    }
}