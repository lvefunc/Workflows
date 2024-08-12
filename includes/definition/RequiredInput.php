<?php

namespace Workflows\Definition;

use MiniORM\Entity;
use MWException;
use Workflows\Enumeration\ValueType;

/**
 * @Table(name: "wfs_def_required_input")
 */
final class RequiredInput extends Entity {
    /**
     * @Column(name: "name", type: "varbinary", length: 255, nullable: false)
     */
    private string $name;

    /**
     * @Column(name: "type", type: "varbinary", length: 255, nullable: false)
     */
    private string $type;

    /**
     * @Column(name: "workflow_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Definition\Workflow")
     */
    private ?Workflow $workflow;

    public function __construct( string $name, string $type, ?Workflow $workflow = null ) {
        parent::__construct();
        $this->setName( $name );
        $this->setType( $type );
        $this->setWorkflow( $workflow );
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName( string $name ) : void {
        $this->name = $name;
        $this->markAsDirty();
    }

    public function getType() : string {
        return $this->type;
    }

    /**
     * @throws MWException
     */
    public function setType( string $type ) : void {
        ValueType::verify( $type );
        $this->type = $type;
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