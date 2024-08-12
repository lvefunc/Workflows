<?php

namespace Workflows\Api;

use ApiBase;
use MediaWiki\MediaWikiServices;
use MWException;

final class ModuleRegistry {
    private static ?ModuleRegistry $instance = null;
    private array $modules = [];
    private array $names = [];
    private array $specs = [];
    private array $instances = [];

    private function __construct() {
        MediaWikiServices::getInstance()->getHookContainer()->run( "RegisterWorkflowsModules", [ &$this ] );
    }

    public static function getInstance() : ModuleRegistry {
        if ( is_null( self::$instance ) ) {
            self::$instance = new ModuleRegistry();
        }

        return self::$instance;
    }

    public function register( $parentClass, $childName, $childClass ) {
        if ( !isset( $this->modules[$parentClass] ) ) {
            $this->modules[$parentClass] = [];
        }

        if ( !in_array( $childClass, $this->modules[$parentClass] ) ) {
            $this->modules[$parentClass][] = $childClass;
            $this->names[$childClass] = $childName;
            $this->specs[$childClass] = [ "class" => $childClass ];
        }
    }

    /**
     * @throws MWException
     */
    public function getChildren( $moduleClass ) {
        if ( !isset( $this->modules[$moduleClass] ) ) {
            throw new MWException( "\"" . $moduleClass . "\" is not a registered module" );
        }

        return $this->modules[$moduleClass];
    }

    /**
     * @throws MWException
     */
    public function getParent( $moduleClass ) {
        foreach ( $this->modules as $parentClass => $childClasses ) {
            if ( in_array( $moduleClass, $childClasses ) ) {
                return $parentClass;
            }
        }

        throw new MWException( "\"" . $moduleClass . "\" is not a registered module" );
    }

    /**
     * @throws MWException
     */
    public function getName( $moduleClass ) {
        if ( !isset( $this->names[$moduleClass] ) ) {
            throw new MWException( "\"" . $moduleClass . "\" is not a registered module" );
        }

        return $this->names[$moduleClass];
    }

    /**
     * @throws MWException
     */
    public function getSpec( $moduleClass ) {
        if ( !isset( $this->specs[$moduleClass] ) ) {
            throw new MWException( "\"" . $moduleClass . "\" is not a registered module" );
        }

        return $this->specs[$moduleClass];
    }

    public function addModule( ApiBase $parentModule, $childName, ApiBase $childModule ) {
        $parentClass = get_class( $parentModule );

        if ( !isset( $this->instances[$parentClass] ) ) {
            $this->instances[$parentClass] = [];
        }

        if ( !isset( $this->instances[$parentClass][$childName] ) ) {
            $this->instances[$parentClass][$childName] = $childModule;
        }
    }

    /**
     * @throws MWException
     */
    public function getModule( ApiBase $parentModule, $childName ) {
        $parentClass = get_class( $parentModule );

        if ( !isset( $this->instances[$parentClass] ) ) {
            throw new MWException(
                "Parent module \"" . $parentClass . "\" has no child module instances"
            );
        }

        if ( !isset( $this->instances[$parentClass][$childName] ) ) {
            throw new MWException(
                "Parent module \"" . $parentClass . "\" has no child module instance with name \"" . $childName . "\""
            );
        }

        return $this->instances[$parentClass][$childName];
    }

    public function getAllModules() : array {
        $instances = [];

        foreach ( array_values( $this->instances ) as $array ) {
            $instances = array_merge( $instances, $array );
        }

        return $instances;
    }

    /**
     * @throws MWException
     */
    public function instantiateModules( ApiBase $module, $group ) {
        $childClasses = $this->getChildren( get_class( $module ) );

        foreach ( $childClasses as $childClass ) {
            $name = $this->getName( $childClass );
            $spec = $this->getSpec( $childClass );
            $module->getModuleManager()->addModule( $name, $group, $spec );

            $instance = $module->getModuleManager()->getModule( $name );
            $this->addModule( $module, $name, $instance );
        }
    }
}