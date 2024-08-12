( function () {
    mw.workflows.ui.CreateWorkflowWidget = function MwWorkflowsUiCreateWorkflowWidget( config ) {
        config = config || {};
        mw.workflows.ui.CreateWorkflowWidget.super.call( this, config );

        this.special = config.special;
        this.initialize();
    };

    OO.inheritClass( mw.workflows.ui.CreateWorkflowWidget, OO.ui.Widget );

    mw.workflows.ui.CreateWorkflowWidget.prototype.initialize = function () {
        this.workflows = [];

        this.selectWorkflowArea = $( "<div>" );

        this.progressBar = new OO.ui.ProgressBarWidget( {} );

        this.selectWorkflowCombo = new OO.ui.ComboBoxInputWidget( {
            options: [],
            menu: { filterFromInput: true }
        } );

        this.selectWorkflowCombo.on( "enter", () => {
            this.loadSelected( this.selectWorkflowCombo.getValue() );
        } );

        this.selectWorkflowButton = new OO.ui.ButtonWidget( {
            label: mw.msg( "workflows-create-workflow-widget-select-workflow-button-label" )
        } );

        this.selectWorkflowButton.on( "click", () => {
            this.loadSelected( this.selectWorkflowCombo.getValue() );
        } );

        this.selectWorkflowBar = new OO.ui.ActionFieldLayout( this.selectWorkflowCombo, this.selectWorkflowButton, {
            align: "left",
            label: mw.msg( "workflows-create-workflow-widget-select-workflow-bar-label" )
        } );

        this.createWorkflowArea = $( "<div>" );
        this.createWorkflowArea.addClass( "ext-workflows-create-workflow-widget-create-workflow-area" );

        this.$element.html( "" );
        this.$element.append( this.selectWorkflowArea );
        this.$element.append( this.createWorkflowArea );
        this.$element.addClass( "ext-workflows-create-workflow-widget" );

        this.preload();
    };

    mw.workflows.ui.CreateWorkflowWidget.prototype.preload = function () {
        this.selectWorkflowArea.html( this.progressBar.$element );

        Promise.resolve( mw.workflows.model.Definition.static.findAll() ).then( r => {
            this.selectWorkflowArea.html( "" );

            let options = [];

            r.forEach( r => {
                this.workflows[ r.getName() ] = r;
                options.push( { data: r.getName(), label: r.getName() } );
            } );

            this.selectWorkflowCombo.setOptions( options );
            this.selectWorkflowArea.append( this.selectWorkflowBar.$element );
        }, e => console.log( e ) );
    };

    mw.workflows.ui.CreateWorkflowWidget.prototype.loadSelected = function ( input ) {
        if ( this.workflows[ input ] === undefined ) {
            return;
        }

        let table = new mw.workflows.ui.TableWidget( {} );
        let header = new mw.workflows.ui.Header();

        header.addColumn( mw.msg( "workflows-create-workflow-widget-table-header-variable-name" ) );
        header.addColumn( mw.msg( "workflows-create-workflow-widget-table-header-variable-type" ) );
        header.addColumn( mw.msg( "workflows-create-workflow-widget-table-header-variable-value" ) );

        table.setHeader( header );

        let workflow = this.workflows[ input ];
        let requiredInputs = workflow.getRequiredInputs();
        let inputs = [];

        if ( requiredInputs.length !== 0 ) {
            requiredInputs.forEach( r => {
                switch ( r.getType() ) {
                    case mw.workflows.model.RequiredInput.static.Boolean:
                        inputs[ r.getName() ] = new OO.ui.ToggleSwitchWidget();
                        break;
                    case mw.workflows.model.RequiredInput.static.Integer:
                        inputs[ r.getName() ] = new OO.ui.NumberInputWidget( {
                            required: true,
                            validate: "non-empty",
                            step: 1
                        } );

                        break;
                    case mw.workflows.model.RequiredInput.static.Text:
                        inputs[ r.getName() ] = new OO.ui.TextInputWidget( {
                            required: true,
                            validate: "non-empty"
                        } );

                        break;
                }

                let row = new mw.workflows.ui.Row();

                let name = r.getName();
                let type = mw.workflows.model.RequiredInput.static.typeToString( r.getType() );
                let value = inputs[ r.getName() ].$element;

                row.addCell( name );
                row.addCell( type )
                row.addCell( value );

                table.addRow( row );
            } );
        } else {
            let row = new mw.workflows.ui.Row();

            row.addCell( mw.msg( "workflows-create-workflow-widget-table-empty-variable-name" ) );
            row.addCell( mw.msg( "workflows-create-workflow-widget-table-empty-variable-type" ) );
            row.addCell( mw.msg( "workflows-create-workflow-widget-table-empty-variable-value" ) );

            table.addRow( row );
        }

        let createWorkflowButton = new OO.ui.ButtonWidget( {
            label: mw.msg( "workflows-create-workflow-widget-create-workflow-button-label" )
        } );

        createWorkflowButton.on( "click", () => {
            this.create( workflow, inputs, createWorkflowButton );
        } );

        let row = new mw.workflows.ui.Row();

        row.addCell( "" );
        row.addCell( "" );
        row.addCell( createWorkflowButton.$element );

        table.addRow( row );

        this.createWorkflowArea.html( table.$element );
    };

    mw.workflows.ui.CreateWorkflowWidget.prototype.create = function ( workflow, inputs, button ) {
        button.setDisabled( true );

        let promises = Object.entries( inputs ).map( ( [ name, input ] ) => {
            return new Promise( ( resolve, reject ) => {
                if ( input instanceof OO.ui.ToggleSwitchWidget ) {
                    resolve( name );
                }

                input.getValidity().then( () => resolve( name ), () => reject( name ) );
            } );
        } );

        Promise.all( promises ).then( r => {
            let promises = r.map( r => {
                return new Promise( ( resolve, reject ) => {
                    let name = r;
                    let input = inputs[ name ];
                    let value = () => {
                        switch ( true ) {
                            case input instanceof OO.ui.ToggleSwitchWidget:
                                return mw.workflows.model.Value.static.create(
                                    mw.workflows.model.Value.static.Boolean,
                                    input.getValue()
                                );
                            case input instanceof OO.ui.NumberInputWidget:
                                return mw.workflows.model.Value.static.create(
                                    mw.workflows.model.Value.static.Integer,
                                    input.getNumericValue()
                                );
                            case input instanceof OO.ui.TextInputWidget:
                                return mw.workflows.model.Value.static.create(
                                    mw.workflows.model.Value.static.Text,
                                    input.getValue()
                                );
                        }
                    };

                    Promise.resolve( value() ).then( r => {
                        resolve( mw.workflows.model.Input.static.create( name, r ) );
                    }, e => reject( e ) );
                } );
            } );

            Promise.all( promises ).then( r => {
                Promise.resolve( mw.workflows.model.Workflow.static.create( workflow, r ) ).then( r => {
                    OO.ui.alert( mw.msg( "workflows-create-workflow-widget-workflow-created-alert", r.getName() ) )
                        .done( () => {
                            button.setDisabled( false );
                            this.special.update();
                        } );
                }, e => console.log( e ) );
            }, e => console.log( e ) );
        }, e => OO.ui.alert( mw.msg( "workflows-create-workflow-widget-input-is-not-valid-alert", e ) ) );
    };
}() );