( function () {
    mw.workflows.model.Input = function MwWorkflowsModelInput( id, name, value ) {
        this.id = id;
        this.name = name;
        this.value = value;
    };

    OO.initClass( mw.workflows.model.Input );

    mw.workflows.model.Input.static.construct = function ( data ) {
        let id = data.id;
        let name = data.name;
        let value = mw.workflows.model.Value.static.construct( data.value );

        return new mw.workflows.model.Input( id, name, value );
    };

    mw.workflows.model.Input.static.findByID = function ( id ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "runtime";
            params.readruntime = "input";
            params.id = id;

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( new mw.workflows.model.Input.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Input.static.create = function ( name, value ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "create";
            params.create = "runtime";
            params.createruntime = "input";
            params.name = name;
            params.value_id = value.getID();

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( mw.workflows.model.Input.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Input.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.Input.prototype.getName = function () {
        return this.name;
    };

    mw.workflows.model.Input.prototype.getValue = function () {
        return this.value;
    };
}() );