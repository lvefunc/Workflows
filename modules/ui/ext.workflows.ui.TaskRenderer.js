( function () {
    mw.workflows.ui.TaskRenderer = function MwWorkflowsUiTaskRenderer( special ) {
        this.special = special;
    };

    OO.inheritClass( mw.workflows.ui.TaskRenderer, mw.workflows.ui.Renderer );

    mw.workflows.ui.TaskRenderer.prototype.load = function ( ordering, from, limit ) {
        return new Promise( ( resolve, reject ) => {
            Promise.resolve( mw.workflows.model.User.static.findCurrent() ).then( r => {
                Promise.resolve( mw.workflows.model.Task.static.findByOptions( r.getID(), ordering, from, limit ) )
                    .then( r => {
                        let table = new mw.workflows.ui.TableWidget( {} );
                        let header = new mw.workflows.ui.Header();

                        header.addColumn( mw.msg( "workflows-task-table-header-identifier" ), "" );
                        header.addColumn( mw.msg( "workflows-task-table-header-type" ), "" );
                        header.addColumn( mw.msg( "workflows-task-table-header-assignee" ), "" );
                        header.addColumn( mw.msg( "workflows-task-table-header-execution-state" ), "" );
                        header.addColumn( mw.msg( "workflows-task-table-header-created-at" ), "" );
                        header.addColumn( mw.msg( "workflows-task-table-header-started-at" ), "" );
                        header.addColumn( mw.msg( "workflows-task-table-header-ended-at" ), "" );
                        header.addColumn( mw.msg( "workflows-task-table-header-execute-task" ), "" );

                        table.setHeader( header );

                        let taskExecutor = new mw.workflows.ui.TaskExecutor();

                        r.result.forEach( r => {
                            let id = r.getID();
                            let type = taskExecutor.getType( r.getClass() );
                            let assignee = r.getAssignee().getName();
                            let executionState = r.getState().getExecutionState();
                            let createdAt = r.getState().getCreatedAt();
                            let startedAt = r.getState().getStartedAt();
                            let endedAt = r.getState().getEndedAt();

                            let executeTaskButton = new OO.ui.ButtonWidget( {
                                label: mw.msg( "workflows-task-table-execute-task-button-label" )
                            } );

                            executeTaskButton.on( "click", () => {
                                taskExecutor.execute( r.getClass(), r.getID() ).then( () => {
                                    this.special.update();
                                } );
                            } );

                            let row = new mw.workflows.ui.Row();

                            row.addCell( id );
                            row.addCell( type );
                            row.addCell( assignee );
                            row.addCell( mw.workflows.model.State.static.execStateToString( executionState ) );
                            row.addCell( mw.workflows.model.State.static.unixTStoString( createdAt ) );
                            row.addCell( mw.workflows.model.State.static.unixTStoString( startedAt ) );
                            row.addCell( mw.workflows.model.State.static.unixTStoString( endedAt ) );
                            row.addCell( executionState === mw.workflows.model.State.static.InProgress ? executeTaskButton.$element : "" );

                            table.addRow( row );
                        } );

                        resolve( { table: table, from: r.from } );
                    }, e => reject( e ) );
            }, e => reject( e ) );
        } );
    };
}() );