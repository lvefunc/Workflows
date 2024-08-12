<?php

namespace Workflows\Runtime\Task;

use EchoEvent;
use MiniORM\Entity;
use User;
use Workflows\Enumeration\ExecutionState;
use Workflows\Runtime\Element\Activity\RtUserActivity;
use Workflows\Runtime\RtState;

/**
 * @Table(name: "wfs_rt_task")
 */
abstract class RtTask extends Entity {
    /**
     * @Column(name: "user_activity_id", type: "int", nullable: false)
     * @ManyToOne(target: "Workflows\Runtime\Element\Activity\RtUserActivity")
     */
    protected RtUserActivity $userActivity;

    /**
     * @Column(name: "assignee", type: "int", nullable: false)
     */
    protected User $assignee;

    /**
     * @Column(name: "state", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\RtState")
     */
    protected RtState $state;

    public function __construct( RtUserActivity $userActivity, User $assignee ) {
        parent::__construct();
        $this->userActivity = $userActivity;
        $this->assignee = $assignee;
        $this->state = new RtState();
        $this->state->setExecutionState( ExecutionState::InProgress );

        EchoEvent::create( [
            "type" => "workflows-new-task",
            "extra" => [
                "assignee" => $this->assignee->getId()
            ]
        ] );
    }

    public function getUserActivity() : RtUserActivity {
        return $this->userActivity;
    }

    public function getAssignee() : User {
        return $this->assignee;
    }

    public function getState() : RtState {
        return $this->state;
    }
}