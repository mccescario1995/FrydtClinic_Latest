<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ServiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServiceCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Service::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/service');
        CRUD::setEntityNameStrings('service', 'services');
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
            'name'  => 'name',
            'label' => 'Service Name',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'service_type',
            'label' => 'Type',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'category',
            'label' => 'Category',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'price',
            'label' => 'Price',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'duration_minutes',
            'label' => 'Duration',
            'type'  => 'text',
            'suffix' => ' minutes',
        ]);

        CRUD::addColumn([
            'name'  => 'is_active',
            'label' => 'Active',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'requires_appointment',
            'label' => 'Requires Appointment',
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
            'name'  => 'name',
            'label' => 'Service Name',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'description',
            'label' => 'Description',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'    => 'service_type',
            'label'   => 'Service Type',
            'type'    => 'select_from_array',
            'options' => [
                'consultation' => 'Consultation',
                'procedure'    => 'Procedure',
                'test'         => 'Test',
                'therapy'      => 'Therapy',
                'vaccination'  => 'Vaccination',
                'other'        => 'Other',
            ],
        ]);

        CRUD::addField([
            'name'    => 'category',
            'label'   => 'Category',
            'type'    => 'select_from_array',
            'options' => [
                'general_practice'    => 'General Practice',
                'obstetrics_gynecology' => 'Obstetrics & Gynecology',
                'pediatrics'          => 'Pediatrics',
                'internal_medicine'   => 'Internal Medicine',
                'surgery'             => 'Surgery',
                'laboratory'          => 'Laboratory',
                'radiology'           => 'Radiology',
                'pharmacy'            => 'Pharmacy',
                'other'               => 'Other',
            ],
        ]);

        CRUD::addField([
            'name'  => 'price',
            'label' => 'Price (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        CRUD::addField([
            'name'  => 'duration_minutes',
            'label' => 'Duration (minutes)',
            'type'  => 'number',
            'attributes' => ['min' => 1],
        ]);

        CRUD::addField([
            'name'    => 'requires_appointment',
            'label'   => 'Requires Appointment',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
            'default' => true,
        ]);

        CRUD::addField([
            'name'    => 'is_active',
            'label'   => 'Active',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
            'default' => true,
        ]);

        CRUD::addField([
            'name'  => 'preparation_instructions',
            'label' => 'Preparation Instructions',
            'type'  => 'textarea',
            'hint'  => 'Instructions for patient preparation',
        ]);

        CRUD::addField([
            'name'  => 'post_service_instructions',
            'label' => 'Post-Service Instructions',
            'type'  => 'textarea',
            'hint'  => 'Instructions after service completion',
        ]);

        CRUD::addField([
            'name'  => 'equipment_required',
            'label' => 'Equipment Required',
            'type'  => 'textarea',
            'hint'  => 'List of equipment needed for this service',
        ]);

        CRUD::addField([
            'name'  => 'special_requirements',
            'label' => 'Special Requirements',
            'type'  => 'textarea',
            'hint'  => 'Any special requirements or considerations',
        ]);

        CRUD::addField([
            'name'  => 'philhealth_code',
            'label' => 'PhilHealth Code',
            'type'  => 'text',
            'hint'  => 'PhilHealth procedure code if applicable',
        ]);

        CRUD::addField([
            'name'  => 'icd_10_codes',
            'label' => 'ICD-10 Codes',
            'type'  => 'textarea',
            'hint'  => 'Related ICD-10 diagnosis codes',
        ]);

        CRUD::addField([
            'name'  => 'cpt_codes',
            'label' => 'CPT Codes',
            'type'  => 'textarea',
            'hint'  => 'Related CPT procedure codes',
        ]);

        CRUD::addField([
            'name'  => 'keywords',
            'label' => 'Keywords',
            'type'  => 'textarea',
            'hint'  => 'Search keywords for this service',
        ]);

        CRUD::addField([
            'name'  => 'sort_order',
            'label' => 'Sort Order',
            'type'  => 'number',
            'hint'  => 'Order for displaying in lists',
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
            'name'  => 'description',
            'label' => 'Description',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'preparation_instructions',
            'label' => 'Preparation Instructions',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'post_service_instructions',
            'label' => 'Post-Service Instructions',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'equipment_required',
            'label' => 'Equipment Required',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'special_requirements',
            'label' => 'Special Requirements',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'philhealth_code',
            'label' => 'PhilHealth Code',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'icd_10_codes',
            'label' => 'ICD-10 Codes',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'cpt_codes',
            'label' => 'CPT Codes',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'keywords',
            'label' => 'Keywords',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'sort_order',
            'label' => 'Sort Order',
            'type'  => 'number',
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
