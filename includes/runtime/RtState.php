<?php

namespace Workflows\Runtime;

use MiniORM\Entity;
use MWException;
use MWTimestamp;
use Workflows\Enumeration\ExecutionState;

/**
 * @Table(name: "wfs_rt_state")
 */
final class RtState extends Entity {
    /**
     * @Column(name: "exec_state", type: "tinyint", nullable: false)
     */
    private int $executionState;

    /**
     * @Column(name: "created_at", type: "int", nullable: false)
     */
    private MWTimestamp $createdAt;

    /**
     * @Column(name: "started_at", type: "int", nullable: true)
     */
    private ?MWTimestamp $startedAt = null;

    /**
     * @Column(name: "ended_at", type: "int", nullable: true)
     */
    private ?MWTimestamp $endedAt = null;

    public function __construct() {
        parent::__construct();
        $this->executionState = ExecutionState::NotStarted;
        $this->createdAt = MWTimestamp::getInstance();
    }

    public function hasNotStarted() : bool {
        return $this->executionState === ExecutionState::NotStarted;
    }

    public function isInProgress() : bool {
        return $this->executionState === ExecutionState::InProgress;
    }

    public function isCompleted() : bool {
        return $this->executionState === ExecutionState::Completed;
    }

    public function isSkipped() : bool {
        return $this->executionState === ExecutionState::Skipped;
    }

    public function isObsolete() : bool {
        return $this->executionState === ExecutionState::Obsolete;
    }

    public function getExecutionState() : int {
        return $this->executionState;
    }

    /**
     * @throws MWException In case $executionState value is not a valid execution state
     */
    public function setExecutionState( int $executionState ) : void {
        ExecutionState::verify( $executionState );
        $this->executionState = $executionState;

        switch ( $executionState ) {
            case ExecutionState::NotStarted:
                $this->createdAt = MWTimestamp::getInstance();
                break;
            case ExecutionState::InProgress:
                $this->startedAt = MWTimestamp::getInstance();
                break;
            case ExecutionState::Completed:
            case ExecutionState::Skipped:
                $this->endedAt = MWTimestamp::getInstance();
                break;
            case ExecutionState::Obsolete:
                break;
        }

        $this->markAsDirty();
    }

    public function getCreatedAt() : MWTimestamp {
        return $this->createdAt;
    }

    public function getStartedAt() : ?MWTimestamp {
        return $this->startedAt;
    }

    public function getEndedAt() : ?MWTimestamp {
        return $this->endedAt;
    }
}