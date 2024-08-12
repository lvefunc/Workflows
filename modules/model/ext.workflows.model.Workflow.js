( function () {
    mw.workflows.model.Workflow = function MwWorkflowsModelWorkflow( id, name, owner, state ) {
        this.id = id;
        this.name = name;
        this.owner = owner;
        this.state = state;
    };

    OO.initClass( mw.workflows.model.Workflow );

    mw.workflows.model.Workflow.static.construct = function ( data ) {
        return new Promise( ( resolve, reject ) => {
            Promise.resolve( mw.workflows.model.User.static.findByID( data.owner ) ).then( r => {
                let id = data.id;
                let name = data.name;
                let owner = r;
                let state = mw.workflows.model.State.static.construct( data.state );

                resolve( new mw.workflows.model.Workflow( id, name, owner, state ) );
            }, e => reject( e ) );
        } );
    };

    mw.workflows.model.Workflow.static.findByID = function ( id ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "runtime";
            params.readruntime = "runtimeworkflow";
            params.id = id;

            return ( new mw.Api() ).get( params ).done( r => {
                Promise.resolve( mw.workflows.model.Workflow.static.construct( r.result ) )
                    .then( r => resolve( r ), e => reject( e ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Workflow.static.findByOptions = function ( owner = undefined, ordering = "Ascending", from = 0, limit = 10 ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "runtime";
            params.readruntime = "runtimeworkflow";
            params.ordering = ordering;
            params.from = from;
            params.limit = limit;

            if ( owner !== undefined ) {
                params.owner = owner;
            }

            return ( new mw.Api() ).get( params ).done( r => {
                if ( r.result === undefined ) {
                    return Promise.resolve( { result: [] } );
                }

                let promises = r.result.map( r => mw.workflows.model.Workflow.static.construct( r ) );
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

    mw.workflows.model.Workflow.static.create = function ( prototype, inputs ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "create";
            params.create = "runtime";
            params.createruntime = "runtimeworkflow";
            params.prototype_id = prototype.getID();
            params.input_ids = inputs.map( r => r.getID() ).join( "|" );

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( mw.workflows.model.Workflow.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Workflow.prototype.start = function () {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "execute";
            params.execute = "startworkflow";
            params.id = this.id;

            return ( new mw.Api() ).get( params ).done( () => {
                resolve();
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Workflow.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.Workflow.prototype.getName = function () {
        return this.name;
    };

    mw.workflows.model.Workflow.prototype.getOwner = function () {
        return this.owner;
    };

    mw.workflows.model.Workflow.prototype.getState = function () {
        return this.state;
    };
}() );