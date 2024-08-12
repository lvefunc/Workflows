( function () {
    mw.workflows.model.RequiredInput = function MwWorkflowsModelRequiredInput( id, name, type ) {
        this.id = id;
        this.name = name;
        this.type = type;
    };

    OO.initClass( mw.workflows.model.RequiredInput );

    mw.workflows.model.RequiredInput.static.Boolean = "Boolean";
    mw.workflows.model.RequiredInput.static.Integer = "Integer";
    mw.workflows.model.RequiredInput.static.Text = "Text";

    mw.workflows.model.RequiredInput.static.construct = function ( data ) {
        return new mw.workflows.model.RequiredInput( data.id, data.name, data.type );
    };

    mw.workflows.model.RequiredInput.static.findByID = function ( id ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "definition";
            params.readdefinition = "requiredinput";
            params.id = id;

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( mw.workflows.model.RequiredInput.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.RequiredInput.static.typeToString = function ( type ) {
        switch ( type ) {
            case mw.workflows.model.RequiredInput.static.Boolean:
                return mw.msg( "workflows-model-required-input-boolean" );
            case mw.workflows.model.RequiredInput.static.Integer:
                return mw.msg( "workflows-model-required-input-integer" );
            case mw.workflows.model.RequiredInput.static.Text:
                return mw.msg( "workflows-model-required-input-text" );
        }
    };

    mw.workflows.model.RequiredInput.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.RequiredInput.prototype.getName = function () {
        return this.name;
    };

    mw.workflows.model.RequiredInput.prototype.getType = function () {
        return this.type;
    };
}() );