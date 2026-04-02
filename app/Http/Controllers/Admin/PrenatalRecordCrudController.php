<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrenatalRecordCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PrenatalRecordCrudController extends CrudController
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
        CRUD::setModel(\App\Models\PrenatalRecord::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/prenatal-record');
        CRUD::setEntityNameStrings('prenatal record', 'prenatal records');
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
            'name'  => 'visit_date',
            'label' => 'Visit Date',
            'type'  => 'date',
        ]);

        CRUD::addColumn([
            'name'  => 'gestational_age_weeks',
            'label' => 'Gestational Age',
            'type'  => 'text',
            'suffix' => ' weeks',
        ]);

        CRUD::addColumn([
            'name'  => 'blood_pressure',
            'label' => 'Blood Pressure',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'weight',
            'label' => 'Weight (kg)',
            'type'  => 'number',
            'decimals' => 1,
        ]);

        CRUD::addColumn([
            'name'  => 'next_visit_date',
            'label' => 'Next Visit',
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
            'name'  => 'visit_date',
            'label' => 'Visit Date',
            'type'  => 'date',
            'default' => date('Y-m-d'),
        ]);

        CRUD::addField([
            'name'  => 'gestational_age_weeks',
            'label' => 'Gestational Age (Weeks)',
            'type'  => 'number',
            'attributes' => ['min' => 1, 'max' => 42],
        ]);

        CRUD::addField([
            'name'  => 'gestational_age_days',
            'label' => 'Gestational Age (Days)',
            'type'  => 'number',
            'attributes' => ['min' => 0, 'max' => 6],
        ]);

        CRUD::addField([
            'name'  => 'blood_pressure',
            'label' => 'Blood Pressure',
            'type'  => 'text',
            'hint'  => 'Format: 120/80 mmHg',
        ]);

        CRUD::addField([
            'name'  => 'weight',
            'label' => 'Weight (kg)',
            'type'  => 'number',
            'attributes' => ['step' => '0.1'],
        ]);

        CRUD::addField([
            'name'  => 'fundal_height',
            'label' => 'Fundal Height (cm)',
            'type'  => 'number',
        ]);

        CRUD::addField([
            'name'  => 'fetal_heart_rate',
            'label' => 'Fetal Heart Rate (bpm)',
            'type'  => 'number',
        ]);

        CRUD::addField([
            'name'  => 'edema',
            'label' => 'Edema',
            'type'  => 'select_from_array',
            'options' => [
                'none' => 'None',
                'mild' => 'Mild',
                'moderate' => 'Moderate',
                'severe' => 'Severe',
            ],
        ]);

        CRUD::addField([
            'name'  => 'urinalysis_protein',
            'label' => 'Urinalysis - Protein',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'urinalysis_glucose',
            'label' => 'Urinalysis - Glucose',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'next_visit_date',
            'label' => 'Next Visit Date',
            'type'  => 'date',
        ]);

        CRUD::addField([
            'name'  => 'notes',
            'label' => 'Clinical Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'      => 'provider_id',
            'label'     => 'Healthcare Provider',
            'type'      => 'select',
            'entity'    => 'provider',
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
            'name'  => 'gestational_age_days',
            'label' => 'Gestational Days',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'fundal_height',
            'label' => 'Fundal Height (cm)',
            'type'  => 'number',
        ]);

        CRUD::addColumn([
            'name'  => 'fetal_heart_rate',
            'label' => 'Fetal Heart Rate',
            'type'  => 'number',
        ]);

        CRUD::addColumn([
            'name'  => 'edema',
            'label' => 'Edema',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'urinalysis_protein',
            'label' => 'Urinalysis Protein',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'urinalysis_glucose',
            'label' => 'Urinalysis Glucose',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'notes',
            'label' => 'Clinical Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'      => 'provider_name',
            'label'     => 'Healthcare Provider',
            'entity'    => 'provider',
            'attribute' => 'name',
        ]);
    }
}
