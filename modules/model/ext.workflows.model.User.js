( function () {
    mw.workflows.model.User = function MwWorkflowsModelUser(
        id, name
    ) {
        this.id = id;
        this.name = name;
    };

    OO.initClass( mw.workflows.model.User );

    mw.workflows.model.User.static.identityMap = [];

    mw.workflows.model.User.static.findByID = function ( id ) {
        return new Promise( ( resolve, reject ) => {
            let user = mw.workflows.model.User.static.identityMap[ id ];

            if ( user !== undefined ) {
                resolve( user );
                return;
            }

            let params = {};

            params.action = "query";
            params.list = "users";
            params.ususerids = id;

            return ( new mw.Api() ).get( params ).done( result => {
                let data = result.query.users.find( user => user.userid === id );
                let user = new mw.workflows.model.User( data.userid, data.name );

                mw.workflows.model.User.static.identityMap[ user.getID() ] = user;
                resolve( user );
            } ).fail( error => reject( error ) );
        } );
    };

    mw.workflows.model.User.static.findCurrent = function () {
        return new Promise( ( resolve, reject ) => {
            let params = {};

            params.action = "query";
            params.meta = "userinfo";

            return ( new mw.Api() ).get( params ).done( result => {
                let data = result.query.userinfo;
                let user = mw.workflows.model.User.static.identityMap[ data.id ];

                if ( user === undefined ) {
                    user = new mw.workflows.model.User( data.id, data.name );
                    mw.workflows.model.User.static.identityMap[ user.getID() ] = user;
                }

                resolve( user );
            } ).fail( error => reject( error ) );
        } );
    };

    mw.workflows.model.User.prototype.getID = function () {
        return this.id;
    };

    mw.workflows.model.User.prototype.getName = function () {
        return this.name;
    };
}() );