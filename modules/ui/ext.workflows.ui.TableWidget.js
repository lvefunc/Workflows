( function () {
    mw.workflows.ui.TableWidget = function MwWorkflowsUiTableWidget( config ) {
        config = config || {};
        mw.workflows.ui.TableWidget.super.call( this, config );

        this.header = $( "<thead>" );
        this.body = $( "<tbody>" );
        this.table = $( "<table>" );
        this.table.append( this.header ).append( this.body );

        this.$element.append( this.table );
        this.$element.addClass( "ext-workflows-table-widget" );
    };

    OO.inheritClass( mw.workflows.ui.TableWidget, OO.ui.Widget );

    mw.workflows.ui.TableWidget.prototype.setHeader = function ( header ) {
        this.header.html( header.get() );
    };

    mw.workflows.ui.TableWidget.prototype.addRow = function ( row ) {
        this.body.append( row.get() );
    };

    mw.workflows.ui.TableWidget.prototype.clearRows = function () {
        this.body.html( "" );
    };
}() );