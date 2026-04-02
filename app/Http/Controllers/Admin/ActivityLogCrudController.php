<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActivityLogRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ActivityLogCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ActivityLogCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ActivityLog::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/activity-log');
        CRUD::setEntityNameStrings('activity log', 'activity logs');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Remove default operations for activity logs (read-only)
        CRUD::denyAccess(['create', 'update', 'delete']);

        // Custom columns for better readability
        CRUD::column('created_at')
            ->label('Date & Time')
            ->type('datetime')
            ->format('M j, Y g:i A');

        CRUD::column('user.name')
            ->label('User')
            ->type('relationship')
            ->attribute('name')
            ->fallback('System');

        CRUD::column('user_type')
            ->label('User Type')
            ->type('badge')
            ->colors([
                'primary' => 'staff',
                'success' => 'patient',
                'secondary' => 'system',
            ]);

        CRUD::column('action')
            ->label('Action')
            ->type('badge')
            ->colors([
                'success' => 'create',
                'warning' => 'update',
                'danger' => 'delete',
                'info' => 'view',
                'primary' => 'login',
                'secondary' => 'logout',
            ]);

        CRUD::column('description')
            ->label('Description')
            ->limit(100);

        CRUD::column('model_type')
            ->label('Resource')
            ->type('closure')
            ->function(function($entry) {
                if ($entry->model_type) {
                    return class_basename($entry->model_type);
                }
                return '-';
            });

        CRUD::column('ip_address')
            ->label('IP Address');

        // Add filters
        CRUD::filter('user_type')
            ->type('dropdown')
            ->values([
                'staff' => 'Staff',
                'patient' => 'Patient',
                'system' => 'System',
            ]);

        CRUD::filter('action')
            ->type('dropdown')
            ->values([
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete',
                'view' => 'View',
                'login' => 'Login',
                'logout' => 'Logout',
                'export' => 'Export',
                'import' => 'Import',
            ]);

        // Date range filter disabled - requires Backpack Pro
        // CRUD::filter('created_at')
        //     ->type('date_range')
        //     ->label('Date Range');

        // Export buttons disabled - requires Backpack Pro
        // CRUD::enableExportButtons();

        // Order by latest first
        CRUD::orderBy('created_at', 'desc');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ActivityLogRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
