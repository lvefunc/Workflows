( function () {
    mw.workflows.ui.TaskExecutor = function MwWorkflowsUiTaskExecutor() {
        mw.hook( "mw.workflows.ui.TaskExecutor" ).fire( this );
    };

    OO.initClass( mw.workflows.ui.TaskExecutor );

    mw.workflows.ui.TaskExecutor.prototype.register = function ( cls, type, executorDialog ) {
        if ( this.types === undefined ) {
            this.types = {};
        }

        if ( this.types[ cls ] === undefined ) {
            this.types[ cls ] = type;
        }

        if ( this.executorDialogs === undefined ) {
            this.executorDialogs = {};
        }

        if ( this.executorDialogs[ cls ] === undefined ) {
            this.executorDialogs[ cls ] = executorDialog;
        }
    };

    mw.workflows.ui.TaskExecutor.prototype.execute = function ( cls, taskID ) {
        if ( this.executorDialogs === undefined ) {
            throw new Error( "Executor dialog was requested but no tasks were registered at all" );
        }

        if ( this.executorDialogs[ cls ] === undefined ) {
            throw new Error( "Executor dialog was requested but there was no task that've been registered by given class" );
        }

        let windowManager = new OO.ui.WindowManager();
        $( document.body ).append( windowManager.$element );

        let executorDialogInstance = new this.executorDialogs[ cls ]( { taskID: taskID } );
        windowManager.addWindows( [ executorDialogInstance ] );

        return windowManager.openWindow( executorDialogInstance ).closed;
    };

    mw.workflows.ui.TaskExecutor.prototype.getType = function ( cls ) {
        if ( this.types === undefined ) {
            throw new Error( "Task type was requested but no tasks were registered at all" );
        }

        if ( this.types[ cls ] === undefined ) {
            throw new Error( "Task type was requested but there was no task that've been registered by given class" );
        }

        return this.types[ cls ];
    };
}() );