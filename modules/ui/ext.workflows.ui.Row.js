( function () {
    mw.workflows.ui.Row = function MwWorkflowsUiRow() {
        this.row = $( "<tr>" );
    };

    OO.initClass( mw.workflows.ui.Row );

    mw.workflows.ui.Row.prototype.addCell = function ( value ) {
        this.row.append( $( "<td>" ).html( value ) );
    };

    mw.workflows.ui.Row.prototype.get = function () {
        return this.row;
    };
}() );