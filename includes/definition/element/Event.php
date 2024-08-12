<?php

namespace Workflows\Definition\Element;

use Exception;
use MWException;
use Workflows\Enumeration\EventType;
use Workflows\Runtime\Element\RtElement;
use Workflows\Runtime\Element\RtEvent;

/**
 * @Table(name: "wfs_def_element_event")
 * @BaseEntity(name: "Workflows\Definition\Element\Element")
 */
final class Event extends Element {
    /**
     * @Column(name: "type", type: "varbinary", length: 255, nullable: false)
     */
    private string $type;

    public function __construct( string $name, string $type ) {
        parent::__construct( $name );
        $this->setType( $type );
    }

    /**
     * @throws Exception
     */
    public function createRuntimeInstance() : RtElement {
        return new RtEvent( $this->getName(), $this->getType() );
    }

    public function getType() : string {
        return $this->type;
    }

    /**
     * @throws MWException
     */
    public function setType( string $type ) : void {
        EventType::verify( $type );
        $this->type = $type;
        $this->markAsDirty();
    }
}