( function () {
    mw.workflows.ui.PaginatedTableWidget = function MwWorkflowsUiPaginatedTableWidget( config ) {
        config = config || {};
        mw.workflows.ui.PaginatedTableWidget.super.call( this, config );

        this.renderer = config.renderer;
        this.initialize();
    };

    OO.inheritClass( mw.workflows.ui.PaginatedTableWidget, OO.ui.Widget );

    mw.workflows.ui.PaginatedTableWidget.prototype.initialize = function () {
        this.position = 1;
        this.pages = [];

        this.progressBar = new OO.ui.ProgressBarWidget( {} );

        this.content = $( "<div>" );
        this.content.addClass( "ext-workflows-paginated-table-widget-content" );
        this.content.html( this.progressBar.$element );

        // Navigation
        this.positionLabel = new OO.ui.LabelWidget( {} );
        this.previousButton = new OO.ui.ButtonWidget( { icon: "arrowPrevious", invisibleLabel: true } );
        this.nextButton = new OO.ui.ButtonWidget( { icon: "arrowNext", invisibleLabel: true } );

        this.previousButton.on( "click", () => this.changePageTo( --this.position ) );
        this.nextButton.on( "click", () => this.changePageTo( ++this.position ) );

        this.navigationBar = new OO.ui.HorizontalLayout( {
            items: [
                this.previousButton,
                this.positionLabel,
                this.nextButton
            ]
        } );

        this.orderingInput = new OO.ui.DropdownInputWidget( {
            options: [
                { data: "Descending", label: mw.msg( "workflows-paginated-table-widget-ordering-input-descending" ) },
                { data: "Ascending", label: mw.msg( "workflows-paginated-table-widget-ordering-input-ascending" ) }
            ]
        } );

        this.orderingButton = new OO.ui.ButtonWidget( {
            label: mw.msg( "workflows-paginated-table-widget-ordering-button-label" )
        } );

        this.orderingButton.on( "click", () => {
            this.clearPages();
            this.changePageTo( 1 );
        } );

        this.orderingBar = new OO.ui.ActionFieldLayout( this.orderingInput, this.orderingButton, {
            align: "left",
            label: mw.msg( "workflows-paginated-table-widget-ordering-bar-label" )
        } );

        this.limitInput = new OO.ui.DropdownInputWidget( {
            options: [
                { data: 10, label: mw.msg( "workflows-paginated-table-widget-limit-input-ten" ) },
                { data: 25, label: mw.msg( "workflows-paginated-table-widget-limit-input-twenty-five" ) },
                { data: 50, label: mw.msg( "workflows-paginated-table-widget-limit-input-fifty" ) }
            ]
        } );

        this.limitButton = new OO.ui.ButtonWidget( {
            label: mw.msg( "workflows-paginated-table-widget-limit-button-label" )
        } );

        this.limitButton.on( "click", () => {
            this.clearPages();
            this.changePageTo( 1 );
        } );

        this.limitBar = new OO.ui.ActionFieldLayout( this.limitInput, this.limitButton, {
            align: "left",
            label: mw.msg( "workflows-paginated-table-widget-limit-bar-label" )
        } );

        this.controlBars = $( "<div>" );
        this.controlBars.append( this.navigationBar.$element );
        this.controlBars.append( this.orderingBar.$element );
        this.controlBars.append( this.limitBar.$element );
        this.controlBars.addClass( "ext-workflows-paginated-table-widget-control-bars" );

        this.$element.html( "" );
        this.$element.append( this.content );
        this.$element.append( this.controlBars );
        this.$element.addClass( "ext-workflows-paginated-table-widget" );

        this.changePageTo( 1 );
    };

    mw.workflows.ui.PaginatedTableWidget.prototype.showProgressBar = function () {
        this.buttonStates = {
            "prev": this.previousButton.isDisabled(),
            "next": this.nextButton.isDisabled()
        };

        this.previousButton.setDisabled( true );
        this.nextButton.setDisabled( true );
        this.orderingInput.setDisabled( true );
        this.orderingButton.setDisabled( true );
        this.limitInput.setDisabled( true );
        this.limitButton.setDisabled( true );

        this.progressBar.$element.show();
    };

    mw.workflows.ui.PaginatedTableWidget.prototype.hideProgressBar = function () {
        this.previousButton.setDisabled( this.buttonStates.prev );
        this.nextButton.setDisabled( this.buttonStates.next );
        this.orderingInput.setDisabled( false );
        this.orderingButton.setDisabled( false );
        this.limitInput.setDisabled( false );
        this.limitButton.setDisabled( false );

        this.progressBar.$element.hide();
    };

    mw.workflows.ui.PaginatedTableWidget.prototype.changePageTo = function ( position ) {
        this.pages.forEach( page => page.table.$element.hide() );

        if ( this.pages[ position ] === undefined ) {
            this.showProgressBar();

            let ordering = this.orderingInput.getValue();
            let from = position === 1 ? 0 : this.pages[ position - 1 ].from;
            let limit = this.limitInput.getValue();

            Promise.resolve( this.renderer.load( ordering, from, limit ) ).then( r => {
                this.hideProgressBar();
                this.pages[ position ] = r;
                this.pages[ position ].table.$element.hide();
                this.content.append( this.pages[ position ].table.$element );
                this.setPage( position );
            }, e => console.log( e ) );
        } else {
            this.setPage( position );
        }
    };

    mw.workflows.ui.PaginatedTableWidget.prototype.setPage = function ( position ) {
        this.page = position;
        this.pages[ position ].table.$element.show();
        this.positionLabel.setLabel( position.toString() );
        this.previousButton.setDisabled( position === 1 );
        this.nextButton.setDisabled( this.pages[ position ].from === undefined );
    };

    mw.workflows.ui.PaginatedTableWidget.prototype.clearPages = function () {
        this.pages.forEach( page => page.table.$element.hide() );
        this.pages = [];
    };
}() );