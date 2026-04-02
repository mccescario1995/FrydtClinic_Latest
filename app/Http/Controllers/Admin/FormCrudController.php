<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FormCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FormCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Form::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/form');
        CRUD::setEntityNameStrings('form', 'forms');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'      => 'patient_name',
            'label'     => 'Patient',
            'entity'    => 'patient.user',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'  => 'form_name',
            'label' => 'Form Name',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'form_type',
            'label' => 'Type',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'status',
            'label' => 'Status',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'priority',
            'label' => 'Priority',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'assigned_date',
            'label' => 'Assigned',
            'type'  => 'date',
        ]);

        CRUD::addColumn([
            'name'  => 'due_date',
            'label' => 'Due Date',
            'type'  => 'date',
        ]);

        CRUD::addColumn([
            'name'  => 'is_confidential',
            'label' => 'Confidential',
            'type'  => 'boolean',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::addField([
            'name'      => 'patient_id',
            'label'     => 'Patient',
            'type'      => 'select',
            'entity'    => 'patient.user',
            'model'     => "App\Models\PatientProfile",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->with('user')->get()->pluck('user.name', 'id');
            }),
        ]);

        CRUD::addField([
            'name'  => 'form_name',
            'label' => 'Form Name',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'form_description',
            'label' => 'Form Description',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'    => 'form_type',
            'label'   => 'Form Type',
            'type'    => 'select_from_array',
            'options' => [
                'consent_form'      => 'Consent Form',
                'medical_history'   => 'Medical History',
                'insurance_form'    => 'Insurance Form',
                'discharge_form'    => 'Discharge Form',
                'admission_form'    => 'Admission Form',
                'treatment_plan'    => 'Treatment Plan',
                'referral_form'     => 'Referral Form',
                'other'             => 'Other',
            ],
        ]);

        CRUD::addField([
            'name'  => 'form_template',
            'label' => 'Form Template/Content',
            'type'  => 'textarea',
            'hint'  => 'HTML content or template for the form',
        ]);

        CRUD::addField([
            'name'    => 'status',
            'label'   => 'Status',
            'type'    => 'select_from_array',
            'options' => [
                'draft'           => 'Draft',
                'pending_review'  => 'Pending Review',
                'completed'       => 'Completed',
                'signed'          => 'Signed',
                'rejected'        => 'Rejected',
            ],
            'default' => 'draft',
        ]);

        CRUD::addField([
            'name'    => 'priority',
            'label'   => 'Priority',
            'type'    => 'select_from_array',
            'options' => [
                'low'     => 'Low',
                'medium'  => 'Medium',
                'high'    => 'High',
                'urgent'  => 'Urgent',
            ],
            'default' => 'medium',
        ]);

        CRUD::addField([
            'name'  => 'assigned_date',
            'label' => 'Assigned Date',
            'type'  => 'date',
            'default' => date('Y-m-d'),
        ]);

        CRUD::addField([
            'name'  => 'due_date',
            'label' => 'Due Date',
            'type'  => 'date',
        ]);

        CRUD::addField([
            'name'  => 'completed_date',
            'label' => 'Completed Date',
            'type'  => 'date',
        ]);

        CRUD::addField([
            'name'  => 'form_data',
            'label' => 'Form Data (JSON)',
            'type'  => 'textarea',
            'hint'  => 'JSON data containing form responses',
        ]);

        CRUD::addField([
            'name'  => 'digital_signature',
            'label' => 'Digital Signature',
            'type'  => 'text',
            'hint'  => 'Base64 encoded signature or signature hash',
        ]);

        CRUD::addField([
            'name'  => 'signature_date',
            'label' => 'Signature Date',
            'type'  => 'datetime',
        ]);

        CRUD::addField([
            'name'    => 'is_confidential',
            'label'   => 'Confidential',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
            'default' => false,
        ]);

        CRUD::addField([
            'name'  => 'access_restrictions',
            'label' => 'Access Restrictions',
            'type'  => 'textarea',
            'hint'  => 'Who can access this form (roles, departments, etc.)',
        ]);

        CRUD::addField([
            'name'  => 'version',
            'label' => 'Version',
            'type'  => 'text',
            'default' => '1.0',
        ]);

        CRUD::addField([
            'name'  => 'notes',
            'label' => 'Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'      => 'assigned_by_id',
            'label'     => 'Assigned By',
            'type'      => 'select',
            'entity'    => 'assignedBy',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->role(['Doctor', 'Employee'])->get()->pluck('name', 'id');
            }),
        ]);

        CRUD::addField([
            'name'      => 'reviewed_by_id',
            'label'     => 'Reviewed By',
            'type'      => 'select',
            'entity'    => 'reviewedBy',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->role(['Doctor', 'Employee'])->get()->pluck('name', 'id');
            }),
        ]);
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

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->setupListOperation();

        CRUD::addColumn([
            'name'  => 'form_description',
            'label' => 'Description',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'form_template',
            'label' => 'Form Template',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'completed_date',
            'label' => 'Completed Date',
            'type'  => 'date',
        ]);

        CRUD::addColumn([
            'name'  => 'form_data',
            'label' => 'Form Data',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'digital_signature',
            'label' => 'Digital Signature',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'signature_date',
            'label' => 'Signature Date',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'access_restrictions',
            'label' => 'Access Restrictions',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'version',
            'label' => 'Version',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'notes',
            'label' => 'Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'      => 'assigned_by_name',
            'label'     => 'Assigned By',
            'entity'    => 'assignedBy',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'      => 'reviewed_by_name',
            'label'     => 'Reviewed By',
            'entity'    => 'reviewedBy',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'  => 'created_at',
            'label' => 'Created',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'updated_at',
            'label' => 'Updated',
            'type'  => 'datetime',
        ]);
    }
}
