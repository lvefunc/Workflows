( function () {
    mw.workflows.model.Task = function MwWorkflowsModelTask( id, cls, assignee, state ) {
        this.id = id;
        this.cls = cls;
        this.assignee = assignee;
        this.state = state;
    };

    OO.initClass( mw.workflows.model.Task );

    mw.workflows.model.Task.static.construct = function ( data ) {
        return new Promise( ( resolve, reject ) => {
            Promise.resolve( mw.workflows.model.User.static.findByID( data.assignee ) ).then( r => {
                let id = data.id;
                let cls = data.class;
                let assignee = r;
                let state = mw.workflows.model.State.static.construct( data.state );

                resolve( new mw.workflows.model.Task( id, cls, assignee, state ) );
            }, e => reject( e ) );
        } );
    };

    mw.workflows.model.Task.static.findByID = function ( id ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "runtime";
            params.readruntime = "task";
            params.id = id;

            return ( new mw.Api() ).get( params ).done( r => {
                Promise.resolve( mw.workflows.model.Task.construct( r.result ) )
                    .then( r => resolve( r ), e => reject( e ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Task.static.findAll = function ( assignee = undefined, result = [], from = 0 ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "runtime";
            params.readruntime = "task";
            params.ordering = "Ascending";
            params.from = from;
            params.limit = 10;

            if ( assignee !== undefined ) {
                params.assignee = assignee;
            }

            return ( new mw.Api() ).get( params ).done( r => {
                if ( r.result === undefined ) {
                    resolve( result );
                }

                let promises = r.result.map( r => mw.workflows.model.Task.static.construct( r ) );
                let cont = r.continue !== undefined;
                from = cont ? r.continue.from : from;

                return Promise.all( promises ).then( r => {
                    r.forEach( r => result.push( r ) );
                    resolve( cont ? mw.workflows.model.Task.static.findAll( assignee, result, from ) : result );
                }, e => reject( e ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Task.static.findByOptions = function ( assignee = undefined, ordering = "Ascending", from = 0, limit = 10 ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "runtime";
            params.readruntime = "task";
            params.ordering = ordering;
            params.from = from;
            params.limit = limit;

            if ( assignee !== undefined ) {
                params.assignee = assignee;
            }

            return ( new mw.Api() ).get( params ).done( r => {
                if ( r.result === undefined ) {
                    return Promise.resolve( { result: [] } );
                }

                let promises = r.result.map( r => mw.workflows.model.Task.static.construct( r ) );
                let cont = r.continue !== undefined;
                let from = cont ? r.continue.from : null;

                Promise.all( promises ).then( r => {
                    let result = {};

                    if ( cont ) {
                        result.from = from;
                    }

                    result.result = r;

                    resolve( result );
                }, e => reject( e ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Task.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.Task.prototype.getClass = function () {
        return this.cls;
    };

    mw.workflows.model.Task.prototype.getAssignee = function () {
        return this.assignee;
    };

    mw.workflows.model.Task.prototype.getState = function () {
        return this.state;
    };
}() );