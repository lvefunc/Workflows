<?php

namespace Workflows\Value;

use Exception;
use MiniORM\Entity;

/**
 * @Table(name: "wfs_value")
 */
abstract class Value extends Entity {
    /**
     * @param mixed $value
     *
     * @throws Exception
     */
    public function __construct( $value ) {
        parent::__construct();
        $this->reference( $value );
    }

    /**
     * @return mixed
     */
    public abstract function dereference();

    /**
     * @param mixed $value
     */
    public abstract function reference( $value ) : void;
}