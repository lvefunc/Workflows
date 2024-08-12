<?php

namespace Workflows\Definition\Element;

use MiniORM\Entity;
use Workflows\Definition\Workflow;
use Workflows\Runtime\Element\RtElement;

/**
 * @Table(name: "wfs_def_element")
 */
abstract class Element extends Entity {
    /**
     * @Column(name: "name", type: "varbinary", length: 255, nullable: false)
     */
    protected string $name;

    /**
     * @Column(name: "workflow_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Definition\Workflow")
     */
    protected ?Workflow $workflow = null;

    public function __construct( string $name ) {
        parent::__construct();
        $this->setName( $name );
    }

    public abstract function createRuntimeInstance() : RtElement;

    public function getName() : string {
        return $this->name;
    }

    public function setName( string $name ) : void {
        $this->name = $name;
        $this->markAsDirty();
    }

    public function getWorkflow() : ?Workflow {
        return $this->workflow;
    }

    public function setWorkflow( ?Workflow $workflow ) : void {
        $this->workflow = $workflow;
        $this->markAsDirty();
    }
}