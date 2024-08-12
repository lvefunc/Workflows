( function () {
    mw.workflows.model.Definition = function MwWorkflowsModelDefinition( id, name, requiredInputs ) {
        this.id = id;
        this.name = name;
        this.requiredInputs = requiredInputs;
    };

    OO.initClass( mw.workflows.model.Definition );

    mw.workflows.model.Definition.static.construct = function ( data ) {
        let id = data.id;
        let name = data.name;
        let requiredInputs = data.requiredInputs.map( r => mw.workflows.model.RequiredInput.static.construct( r ) );

        return new mw.workflows.model.Definition( id, name, requiredInputs );
    };

    mw.workflows.model.Definition.static.findByID = function ( id ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "definition";
            params.readdefinition = "workflow";
            params.id = id;

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( new mw.workflows.model.Definition.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Definition.static.findByName = function ( name ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "definition";
            params.readdefinition = "workflow";
            params.name = name;

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( new mw.workflows.model.Definition.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Definition.static.findAll = function ( result = [], from = 0 ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "definition";
            params.readdefinition = "workflow";
            params.ordering = "Ascending";
            params.from = from;
            params.limit = 10;

            return ( new mw.Api() ).get( params ).done( r => {
                if ( r.result === undefined ) {
                    resolve( result );
                    return;
                }

                r.result.forEach( r => result.push( mw.workflows.model.Definition.static.construct( r ) ) );

                let cont = r.continue !== undefined;
                from = cont ? r.continue.from : from;

                resolve( cont ? mw.workflows.model.Definition.static.findAll( result, from ) : result );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Definition.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.Definition.prototype.getName = function () {
        return this.name;
    };

    mw.workflows.model.Definition.prototype.getRequiredInputs = function () {
        return this.requiredInputs;
    };
}() );