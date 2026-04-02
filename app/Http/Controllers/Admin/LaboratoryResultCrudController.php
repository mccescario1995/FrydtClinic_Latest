<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LaboratoryResultCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LaboratoryResultCrudController extends CrudController
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
        CRUD::setModel(\App\Models\LaboratoryResult::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/laboratory-result');
        CRUD::setEntityNameStrings('laboratory result', 'laboratory results');
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
            'name'  => 'test_name',
            'label' => 'Test Name',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'test_category',
            'label' => 'Category',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'test_ordered_date_time',
            'label' => 'Ordered',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'test_status',
            'label' => 'Status',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'result_value',
            'label' => 'Result',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'result_status',
            'label' => 'Result Status',
            'type'  => 'text',
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
            'name'  => 'test_name',
            'label' => 'Test Name',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'test_code',
            'label' => 'Test Code',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'    => 'test_category',
            'label'   => 'Test Category',
            'type'    => 'select_from_array',
            'options' => [
                'hematology'    => 'Hematology',
                'chemistry'     => 'Chemistry',
                'microbiology'  => 'Microbiology',
                'immunology'    => 'Immunology',
                'endocrinology' => 'Endocrinology',
                'urinalysis'    => 'Urinalysis',
                'other'         => 'Other',
            ],
        ]);

        CRUD::addField([
            'name'  => 'sample_type',
            'label' => 'Sample Type',
            'type'  => 'text',
            'hint'  => 'e.g., Blood, Urine, Stool, Swab',
        ]);

        CRUD::addField([
            'name'    => 'urgent',
            'label'   => 'Urgent',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
        ]);

        CRUD::addField([
            'name'    => 'stat',
            'label'   => 'STAT',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
        ]);

        CRUD::addField([
            'name'  => 'test_ordered_date_time',
            'label' => 'Test Ordered Date & Time',
            'type'  => 'datetime',
            'default' => now(),
        ]);

        CRUD::addField([
            'name'  => 'sample_collection_date_time',
            'label' => 'Sample Collection Date & Time',
            'type'  => 'datetime',
        ]);

        CRUD::addField([
            'name'  => 'test_performed_date_time',
            'label' => 'Test Performed Date & Time',
            'type'  => 'datetime',
        ]);

        CRUD::addField([
            'name'  => 'result_available_date_time',
            'label' => 'Result Available Date & Time',
            'type'  => 'datetime',
        ]);

        CRUD::addField([
            'name'  => 'result_reviewed_date_time',
            'label' => 'Result Reviewed Date & Time',
            'type'  => 'datetime',
        ]);

        CRUD::addField([
            'name'    => 'test_status',
            'label'   => 'Test Status',
            'type'    => 'select_from_array',
            'options' => [
                'ordered'         => 'Ordered',
                'sample_collected' => 'Sample Collected',
                'in_progress'     => 'In Progress',
                'completed'       => 'Completed',
                'cancelled'       => 'Cancelled',
            ],
        ]);

        CRUD::addField([
            'name'  => 'result_value',
            'label' => 'Result Value',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'result_unit',
            'label' => 'Result Unit',
            'type'  => 'text',
            'hint'  => 'e.g., mg/dL, mmol/L, cells/μL',
        ]);

        CRUD::addField([
            'name'  => 'reference_range',
            'label' => 'Reference Range',
            'type'  => 'text',
            'hint'  => 'e.g., 70-100 mg/dL',
        ]);

        CRUD::addField([
            'name'    => 'result_status',
            'label'   => 'Result Status',
            'type'    => 'select_from_array',
            'options' => [
                'normal'   => 'Normal',
                'abnormal' => 'Abnormal',
                'critical' => 'Critical',
                'pending'  => 'Pending',
            ],
        ]);

        CRUD::addField([
            'name'  => 'interpretation',
            'label' => 'Interpretation',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'  => 'comments',
            'label' => 'Comments',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'    => 'qc_passed',
            'label'   => 'QC Passed',
            'type'    => 'select_from_array',
            'options' => [
                null  => 'Not Applicable',
                true  => 'Yes',
                false => 'No',
            ],
        ]);

        CRUD::addField([
            'name'  => 'qc_notes',
            'label' => 'QC Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'  => 'test_cost',
            'label' => 'Test Cost (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        CRUD::addField([
            'name'    => 'covered_by_philhealth',
            'label'   => 'Covered by PhilHealth',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
        ]);

        CRUD::addField([
            'name'  => 'philhealth_coverage_amount',
            'label' => 'PhilHealth Coverage Amount (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        CRUD::addField([
            'name'      => 'ordering_provider_id',
            'label'     => 'Ordering Provider',
            'type'      => 'select',
            'entity'    => 'orderingProvider',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->role(['Doctor', 'Employee'])->get()->pluck('name', 'id');
            }),
        ]);

        CRUD::addField([
            'name'      => 'performing_technician_id',
            'label'     => 'Performing Technician',
            'type'      => 'select',
            'entity'    => 'performingTechnician',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->role(['Doctor', 'Employee'])->get()->pluck('name', 'id');
            }),
        ]);

        CRUD::addField([
            'name'      => 'reviewing_provider_id',
            'label'     => 'Reviewing Provider',
            'type'      => 'select',
            'entity'    => 'reviewingProvider',
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
            'name'  => 'test_code',
            'label' => 'Test Code',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'sample_type',
            'label' => 'Sample Type',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'urgent',
            'label' => 'Urgent',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'stat',
            'label' => 'STAT',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'sample_collection_date_time',
            'label' => 'Sample Collection',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'test_performed_date_time',
            'label' => 'Test Performed',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'result_available_date_time',
            'label' => 'Result Available',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'result_reviewed_date_time',
            'label' => 'Result Reviewed',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'result_unit',
            'label' => 'Unit',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'reference_range',
            'label' => 'Reference Range',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'interpretation',
            'label' => 'Interpretation',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'comments',
            'label' => 'Comments',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'qc_passed',
            'label' => 'QC Passed',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'qc_notes',
            'label' => 'QC Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'test_cost',
            'label' => 'Test Cost',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'covered_by_philhealth',
            'label' => 'PhilHealth Covered',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'philhealth_coverage_amount',
            'label' => 'PhilHealth Coverage',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'      => 'ordering_provider_name',
            'label'     => 'Ordering Provider',
            'entity'    => 'orderingProvider',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'      => 'performing_technician_name',
            'label'     => 'Performing Technician',
            'entity'    => 'performingTechnician',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'      => 'reviewing_provider_name',
            'label'     => 'Reviewing Provider',
            'entity'    => 'reviewingProvider',
            'attribute' => 'name',
        ]);
    }
}
