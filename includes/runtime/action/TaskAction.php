<?php

namespace Workflows\Runtime\Action;

use MWException;
use RequestContext;
use Workflows\Runtime\Task\RtTask;

abstract class TaskAction implements IAction {
    protected RtTask $task;

    public function __construct( RtTask $task ) {
        $this->setTask( $task );
    }

    /**
     * @throws MWException
     */
    public final function verifyUser() {
        $user = RequestContext::getMain()->getUser();

        if (
            !$user->equals( $this->getTask()->getAssignee() ) &&
            !$user->isAllowed( "workflows-admin-powers" )
        ) {
            throw new MWException(
                "You are not allowed to execute actions on this task"
            );
        }
    }

    public function getTask() : RtTask {
        return $this->task;
    }

    public function setTask( RtTask $task ) : void {
        $this->task = $task;
    }
}