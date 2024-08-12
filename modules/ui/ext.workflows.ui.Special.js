( function () {
    mw.workflows.ui.Special = function MwWorkflowsUiSpecial( config ) {
        config = config || {};
        mw.workflows.ui.Special.super.call( this, config );

        this.initialize();
    };

    OO.inheritClass( mw.workflows.ui.Special, OO.ui.Widget );

    mw.workflows.ui.Special.prototype.initialize = function () {
        this.index = new OO.ui.IndexLayout( {} );

        // ... my workflows
        this.myWorkflows = new OO.ui.TabPanelLayout( "My workflows", {
            id: "my-workflows",
            label: mw.msg( "workflows-special-my-workflows" ),
            scrollable: false
        } );

        this.myWorkflowsRenderer = new mw.workflows.ui.WorkflowRenderer( this );
        this.myWorkflowsTable = new mw.workflows.ui.PaginatedTableWidget( {
            renderer: this.myWorkflowsRenderer
        } );

        this.myWorkflows.$element.html( this.myWorkflowsTable.$element );

        // ... my tasks
        this.myTasks = new OO.ui.TabPanelLayout( "My tasks", {
            id: "my-tasks",
            label: mw.msg( "workflows-special-my-tasks" ),
            scrollable: false
        } );

        this.myTasksRenderer = new mw.workflows.ui.TaskRenderer( this );
        this.myTasksTable = new mw.workflows.ui.PaginatedTableWidget( {
            renderer: this.myTasksRenderer
        } );

        this.myTasks.$element.html( this.myTasksTable.$element );

        // ... create workflow
        this.createWorkflow = new OO.ui.TabPanelLayout( "Create workflow", {
            id: "create-workflow",
            label: mw.msg( "workflows-special-create-workflow" ),
            scrollable: false
        } );

        this.createWorkflowWidget = new mw.workflows.ui.CreateWorkflowWidget( {
            special: this
        } );

        this.createWorkflow.$element.html( this.createWorkflowWidget.$element );

        this.index.addTabPanels( [ this.myWorkflows, this.myTasks, this.createWorkflow ] );
        this.$element.html( this.index.$element );
    };

    mw.workflows.ui.Special.prototype.update = function () {
        this.myWorkflowsTable.clearPages();
        this.myWorkflowsTable.changePageTo( 1 );
        this.myTasksTable.clearPages();
        this.myTasksTable.changePageTo( 1 );
    };
}() );