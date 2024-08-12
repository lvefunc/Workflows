<?php

namespace Workflows;

use MediaWiki\MediaWikiServices;
use SpecialPage;

final class Special extends SpecialPage {
    public function __construct() {
        parent::__construct( "Workflows" );
    }

    public function execute( $subPage ) {
        $this->setHeaders();
        $this->getOutput()->setPageTitle( wfMessage( "workflows" )->text() );
        $this->getOutput()->enableOOUI();
        $this->getOutput()->addModules( "ext.workflows.special" );
        MediaWikiServices::getInstance()->getHookContainer()->run( "RegisterWorkflowsResourceModules", [ &$this ] );
    }
}