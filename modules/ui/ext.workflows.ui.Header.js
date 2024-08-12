( function () {
    mw.workflows.ui.Header = function MwWorkflowsUiHeader() {
        this.header = $( "<tr>" );
    };

    OO.initClass( mw.workflows.ui.Header );

    mw.workflows.ui.Header.prototype.addColumn = function ( name ) {
        this.header.append( $( "<th>" ).html( name ) );
    };

    mw.workflows.ui.Header.prototype.get = function () {
        return this.header;
    };
}() );