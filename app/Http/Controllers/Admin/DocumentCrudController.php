<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DocumentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DocumentCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Document::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/document');
        CRUD::setEntityNameStrings('document', 'documents');
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
            'name'  => 'document_name',
            'label' => 'Document Name',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'document_type',
            'label' => 'Type',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'file_path',
            'label' => 'File',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'file_size',
            'label' => 'Size',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'access_level',
            'label' => 'Access Level',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'is_confidential',
            'label' => 'Confidential',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'expires_at',
            'label' => 'Expires',
            'type'  => 'date',
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
            'name'  => 'document_name',
            'label' => 'Document Name',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'document_description',
            'label' => 'Description',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'    => 'document_type',
            'label'   => 'Document Type',
            'type'    => 'select_from_array',
            'options' => [
                'medical_record'     => 'Medical Record',
                'lab_result'         => 'Lab Result',
                'prescription'       => 'Prescription',
                'consent_form'       => 'Consent Form',
                'insurance_form'     => 'Insurance Form',
                'discharge_summary'  => 'Discharge Summary',
                'referral_letter'    => 'Referral Letter',
                'other'              => 'Other',
            ],
        ]);

        CRUD::addField([
            'name'  => 'file_path',
            'label' => 'File Upload',
            'type'  => 'upload',
            'upload' => true,
            'disk'  => 'public',
        ]);

        CRUD::addField([
            'name'  => 'file_size',
            'label' => 'File Size (bytes)',
            'type'  => 'number',
            'attributes' => ['readonly' => 'readonly'],
        ]);

        CRUD::addField([
            'name'  => 'mime_type',
            'label' => 'MIME Type',
            'type'  => 'text',
            'attributes' => ['readonly' => 'readonly'],
        ]);

        CRUD::addField([
            'name'    => 'access_level',
            'label'   => 'Access Level',
            'type'    => 'select_from_array',
            'options' => [
                'patient_only'    => 'Patient Only',
                'staff_only'      => 'Staff Only',
                'confidential'    => 'Confidential',
                'public'          => 'Public',
            ],
            'default' => 'patient_only',
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
            'name'  => 'expires_at',
            'label' => 'Expiration Date',
            'type'  => 'date',
            'hint'  => 'Leave blank if document never expires',
        ]);

        CRUD::addField([
            'name'  => 'tags',
            'label' => 'Tags',
            'type'  => 'text',
            'hint'  => 'Comma-separated tags for organization',
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
            'name'      => 'uploaded_by_id',
            'label'     => 'Uploaded By',
            'type'      => 'select',
            'entity'    => 'uploadedBy',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->role(['Doctor', 'Employee'])->get()->pluck('name', 'id');
            }),
        ]);

        CRUD::addField([
            'name'      => 'approved_by_id',
            'label'     => 'Approved By',
            'type'      => 'select',
            'entity'    => 'approvedBy',
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
            'name'  => 'document_description',
            'label' => 'Description',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'mime_type',
            'label' => 'MIME Type',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'tags',
            'label' => 'Tags',
            'type'  => 'text',
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
            'name'      => 'uploaded_by_name',
            'label'     => 'Uploaded By',
            'entity'    => 'uploadedBy',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'      => 'approved_by_name',
            'label'     => 'Approved By',
            'entity'    => 'approvedBy',
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
