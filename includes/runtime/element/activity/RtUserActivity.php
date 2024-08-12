<?php

namespace Workflows\Runtime\Element\Activity;

use MWException;
use Workflows\Enumeration\ExecutionState;
use Workflows\Runtime\Task\RtTask;

/**
 * @Table(name: "wfs_rt_element_user_activity")
 * @BaseEntity(name: "Workflows\Runtime\Element\RtElement")
 */
abstract class RtUserActivity extends RtActivity {
    /**
     * @Column(name: "quorum", type: "int", nullable: false)
     */
    protected int $quorum = 0;

    /**
     * @OneToMany(target: "Workflows\Runtime\Task\RtTask", mappedBy: "userActivity")
     */
    protected array $tasks = [];

    public function __construct( string $name ) {
        parent::__construct( $name );
    }

    protected abstract function initialize();
    protected abstract function postExecution();

    /**
     * @throws MWException
     */
    public function queue() : void {
        $preceding = $this->getWorkflow()->findPrecedingElementOf( $this );

        if ( $preceding->getState()->isCompleted() ) {
            foreach ( $this->getTasks() as $task ) {
                $task->getState()->setExecutionState( ExecutionState::Obsolete );
            }

            $this->initialize();
            $this->getState()->setExecutionState( ExecutionState::InProgress );
            $this->getWorkflow()->continueExecution();
        }
    }

    /**
     * @throws MWException
     */
    public function notify() {
        $completed = 0;

        foreach ( $this->getTasks() as $task ) {
            if ( $task->getState()->isCompleted() ) {
                $completed++;
            }
        }

        if ( $completed >= $this->getQuorum() ) {
            $this->end();
        }
    }

    /**
     * @throws MWException
     */
    public function end() : void {
        foreach ( $this->getTasks() as $task ) {
            if ( !$task->getState()->isCompleted() && !$task->getState()->isObsolete() ) {
                $task->getState()->setExecutionState( ExecutionState::Skipped );
            }
        }

        $this->postExecution();

        $this->getState()->setExecutionState( ExecutionState::Completed );
        $succeeding = $this->getWorkflow()->findElementSucceedingTo( $this );
        $token = $this->getWorkflow()->getContext()->findTokenByPosition( $this );
        $token->moveTo( $succeeding );
        $succeeding->queue();
    }

    public function getQuorum() : int {
        return $this->quorum;
    }

    public function setQuorum( int $quorum ) : void {
        $this->quorum = $quorum;
        $this->markAsDirty();
    }

    /**
     * @return RtTask[]
     */
    public function getTasks() : array {
        return $this->tasks;
    }

    public function addTask( RtTask $task ) {
        foreach ( $this->tasks as $existing ) {
            if ( $existing->equals( $task ) ) {
                return;
            }
        }

        $this->tasks[] = $task;
    }
}