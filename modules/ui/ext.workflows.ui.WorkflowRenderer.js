( function () {
    mw.workflows.ui.WorkflowRenderer = function MwWorkflowsUiWorkflowRenderer( special ) {
        this.special = special;
    };

    OO.inheritClass( mw.workflows.ui.WorkflowRenderer, mw.workflows.ui.Renderer );

    mw.workflows.ui.WorkflowRenderer.prototype.load = function ( ordering, from, limit ) {
        return new Promise( ( resolve, reject ) => {
            Promise.resolve( mw.workflows.model.User.static.findCurrent() ).then( r => {
                Promise.resolve( mw.workflows.model.Workflow.static.findByOptions( r.getID(), ordering, from, limit ) )
                    .then( r => {
                        let table = new mw.workflows.ui.TableWidget( {} );
                        let header = new mw.workflows.ui.Header();

                        header.addColumn( mw.msg( "workflows-workflow-table-header-identifier", "" ) );
                        header.addColumn( mw.msg( "workflows-workflow-table-header-name", "" ) );
                        header.addColumn( mw.msg( "workflows-workflow-table-header-owner", "" ) );
                        header.addColumn( mw.msg( "workflows-workflow-table-header-execution-state", "" ) );
                        header.addColumn( mw.msg( "workflows-workflow-table-header-created-at", "" ) );
                        header.addColumn( mw.msg( "workflows-workflow-table-header-started-at", "" ) );
                        header.addColumn( mw.msg( "workflows-workflow-table-header-ended-at", "" ) );
                        header.addColumn( mw.msg( "workflows-workflow-table-header-start-workflow", "" ) );

                        table.setHeader( header );

                        r.result.forEach( r => {
                            let id = r.getID();
                            let name = r.getName();
                            let owner = r.getOwner().getName();
                            let executionState = r.getState().getExecutionState();
                            let createdAt = r.getState().getCreatedAt();
                            let startedAt = r.getState().getStartedAt();
                            let endedAt = r.getState().getEndedAt();

                            let startWorkflowButton = new OO.ui.ButtonWidget( {
                                label: mw.msg( "workflows-workflow-table-start-workflow-button-label" )
                            } );

                            startWorkflowButton.on( "click", () => {
                                startWorkflowButton.setDisabled( true );

                                Promise.resolve( r.start() ).then( () => {
                                    this.special.update();
                                }, e => reject( e ) );
                            } );

                            let row = new mw.workflows.ui.Row();

                            row.addCell( id );
                            row.addCell( name );
                            row.addCell( owner );
                            row.addCell( mw.workflows.model.State.static.execStateToString( executionState ) );
                            row.addCell( mw.workflows.model.State.static.unixTStoString( createdAt ) );
                            row.addCell( mw.workflows.model.State.static.unixTStoString( startedAt ) );
                            row.addCell( mw.workflows.model.State.static.unixTStoString( endedAt ) );
                            row.addCell( executionState === mw.workflows.model.State.static.NotStarted ? startWorkflowButton.$element : "" );

                            table.addRow( row );
                        } );

                        resolve( { table: table, from: r.from } );
                    }, e => reject( e ) );
            }, e => reject( e ) );
        } );
    };
}() );