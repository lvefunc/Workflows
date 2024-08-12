( function () {
    mw.workflows.model.State = function MwWorkflowsModelState( id, createdAt, startedAt, endedAt, executionState ) {
        this.id = id;
        this.createdAt = createdAt;
        this.startedAt = startedAt;
        this.endedAt = endedAt;
        this.executionState = executionState;
    };

    OO.initClass( mw.workflows.model.State );

    mw.workflows.model.State.static.NotStarted  = 0;
    mw.workflows.model.State.static.InProgress  = 1;
    mw.workflows.model.State.static.Completed   = 2;
    mw.workflows.model.State.static.Skipped     = 3;
    mw.workflows.model.State.static.Obsolete    = 4;

    mw.workflows.model.State.static.construct = function ( data ) {
        return new mw.workflows.model.State(
            data.id,
            data.createdAt,
            data.startedAt,
            data.endedAt,
            data.executionState
        );
    };

    mw.workflows.model.State.static.unixTStoString = function ( unixTS ) {
        return unixTS === null
            ? mw.msg( "workflows-model-state-empty-timestamp" )
            : new Date( unixTS * 1000 ).toLocaleString();
    };

    mw.workflows.model.State.static.execStateToString = function ( executionState ) {
        switch ( executionState ) {
            case 0:
                return mw.msg( "workflows-model-state-not-started" );
            case 1:
                return mw.msg( "workflows-model-state-in-progress" );
            case 2:
                return mw.msg( "workflows-model-state-completed" );
            case 3:
                return mw.msg( "workflows-model-state-skipped" );
            case 4:
                return mw.msg( "workflows-model-state-obsolete" );
        }
    };

    mw.workflows.model.State.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.State.prototype.getCreatedAt = function () {
        return this.createdAt;
    };

    mw.workflows.model.State.prototype.getStartedAt = function () {
        return this.startedAt;
    };

    mw.workflows.model.State.prototype.getEndedAt = function () {
        return this.endedAt;
    };

    mw.workflows.model.State.prototype.getExecutionState = function () {
        return this.executionState;
    };
}() );