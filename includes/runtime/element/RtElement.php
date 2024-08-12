<?php

namespace Workflows\Runtime\Element;

use MiniORM\Entity;
use Workflows\Runtime\RtState;
use Workflows\Runtime\RtWorkflow;

/**
 * @Table(name: "wfs_rt_element")
 */
abstract class RtElement extends Entity {
    /**
     * @Column(name: "name", type: "varbinary", length: 255, nullable: false)
     */
    protected string $name;

    /**
     * @Column(name: "state_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\RtState")
     */
    protected RtState $state;

    /**
     * @Column(name: "workflow_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Runtime\RtWorkflow")
     */
    protected ?RtWorkflow $workflow = null;

    public function __construct( string $name ) {
        parent::__construct();
        $this->name = $name;
        $this->state = new RtState();
    }

    /**
     * Queue this element for execution. Implementation of this function must verify that elements preceding to this one
     * were completed, prepare element before execution and mark it as being in progress after. There's no need to call
     * this function, it will get called automatically when workflow execution gets to this element.
     */
    public abstract function queue() : void;

    /**
     * Execute this element. Implementation of this function must execute all actions that this element is trying to
     * accomplish and mark it as being completed after. There's no need to call this function, it will get called
     * automatically when workflow execution gets to this element.
     */
    public abstract function end() : void;

    public function getName() : string {
        return $this->name;
    }

    public function getState() : RtState {
        return $this->state;
    }

    public function getWorkflow() : ?RtWorkflow {
        return $this->workflow;
    }

    public function setWorkflow( ?RtWorkflow $workflow ) : void {
        $this->workflow = $workflow;
        $this->markAsDirty();
    }
}