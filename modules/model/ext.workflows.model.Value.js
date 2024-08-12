( function () {
    mw.workflows.model.Value = function MwWorkflowsModelValue( id, cls, value ) {
        this.id = id;
        this.cls = cls;
        this.value = value;
    };

    OO.initClass( mw.workflows.model.Value );

    mw.workflows.model.Value.static.Boolean = "Boolean";
    mw.workflows.model.Value.static.Integer = "Integer";
    mw.workflows.model.Value.static.Text    = "Text";

    mw.workflows.model.Value.static.construct = function ( data ) {
        let id = data.id;
        let cls = data.class;
        let type = mw.workflows.model.Value.static.classToType( data.cls );
        let bool = type === mw.workflows.model.Value.static.Boolean;
        let value = bool ? data.value === "true" : data.value;

        return new mw.workflows.model.Value( id, cls, value );
    };

    mw.workflows.model.Value.static.findByID = function ( id ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "read";
            params.read = "value";
            params.id = id;

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( mw.workflows.model.Value.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Value.static.create = function ( type, value ) {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "workflows";
            params.operation = "create";
            params.create = "value";

            switch ( type ) {
                case mw.workflows.model.Value.static.Boolean:
                    params.createvalue = "boolean";
                    break;
                case mw.workflows.model.Value.static.Integer:
                    params.createvalue = "integer";
                    break;
                case mw.workflows.model.Value.static.Text:
                    params.createvalue = "text";
                    break;
            }

            if ( type === mw.workflows.model.Value.static.Boolean ) {
                if ( value ) {
                    params.value = 1;
                }
            } else {
                params.value = value;
            }

            return ( new mw.Api() ).get( params ).done( r => {
                resolve( new mw.workflows.model.Value.static.construct( r.result ) );
            } ).fail( e => reject( e ) );
        } );
    };

    mw.workflows.model.Value.static.classToType = function ( cls ) {
        switch ( cls ) {
            case "Workflows\\Value\\Boolean":
                return mw.workflows.model.Value.static.Boolean;
            case "Workflows\\Value\\Integer":
                return mw.workflows.model.Value.static.Integer;
            case "Workflows\\Value\\Text":
                return mw.workflows.model.Value.static.Text;
        }
    };

    mw.workflows.model.Value.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.Value.prototype.getClass = function () {
        return this.cls;
    };

    mw.workflows.model.Value.prototype.getType = function () {
        return mw.workflows.model.Value.static.classToType( this.cls );
    };

    mw.workflows.model.Value.prototype.getValue = function () {
        return this.value;
    };
}() );