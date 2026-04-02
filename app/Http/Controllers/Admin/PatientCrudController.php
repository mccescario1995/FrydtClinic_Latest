<?php
namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PatientCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PatientCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {create as traitCreate;}
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
        CRUD::setModel(\App\Models\PatientProfile::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/patient');
        CRUD::setEntityNameStrings('patient', 'patients');

        CRUD::addClause('whereHas', 'user.roles', function ($query) {
            $query->where('name', 'Patient');
        });
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setOperationSetting('showEntryCount', true);

        // Enable responsive table
        CRUD::setOperationSetting('responsiveTable', true);

        // Enable persistent table (remembers user preferences)
        CRUD::setOperationSetting('persistentTable', true);

        // Export buttons disabled - requires Backpack Pro
        // CRUD::enableExportButtons();

        // Name column from the related User model
        CRUD::addColumn([
            'name'      => 'user_name',
            'label'     => 'Name',
            'entity'    => 'user',
            'attribute' => 'name',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('user', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%'.$searchTerm.'%');
                });
            }
        ]);

        // Email column from the related User model
        CRUD::addColumn([
            'name'      => 'user_email',
            'label'     => 'Email',
            'entity'    => 'user',
            'attribute' => 'email',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('user', function ($q) use ($searchTerm) {
                    $q->where('email', 'like', '%'.$searchTerm.'%');
                });
            }
        ]);

        // Gender column with badge
        CRUD::addColumn([
            'name'  => 'gender',
            'label' => 'Gender',
            'type'  => 'badge',
            'colors' => [
                'primary' => 'Male',
                'success' => 'Female',
                'secondary' => 'Other',
            ],
        ]);

        // Phone column
        CRUD::addColumn([
            'name'  => 'phone',
            'label' => 'Phone',
            'type'  => 'text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('phone', 'like', '%'.$searchTerm.'%');
            }
        ]);

        // Address column
        CRUD::addColumn([
            'name'  => 'address',
            'label' => 'Address',
            'type'  => 'text',
            'limit' => 50,
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('address', 'like', '%'.$searchTerm.'%');
            }
        ]);

        // Civil Status column with badge
        CRUD::addColumn([
            'name'  => 'civil_status',
            'label' => 'Civil Status',
            'type'  => 'badge',
            'colors' => [
                'info' => 'single',
                'success' => 'married',
                'warning' => 'widowed',
                'danger' => 'divorced',
                'secondary' => 'separated',
            ],
        ]);

        // Blood Type column
        CRUD::addColumn([
            'name'  => 'blood_type',
            'label' => 'Blood Type',
            'type'  => 'badge',
            'colors' => [
                'danger' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            ],
        ]);

        // PhilHealth Membership column with badge
        CRUD::addColumn([
            'name'  => 'philhealth_membership',
            'label' => 'PhilHealth',
            'type'  => 'badge',
            'colors' => [
                'success' => 'Member',
                'warning' => 'Dependent',
                'secondary' => 'None',
            ],
        ]);

        // Age column (calculated from birth_date)
        CRUD::addColumn([
            'name'  => 'age',
            'label' => 'Age',
            'type'  => 'closure',
            'function' => function($entry) {
                return $entry->birth_date ? $entry->birth_date->age . ' years' : 'N/A';
            },
            'searchLogic' => false,
        ]);

        // Registration Date
        CRUD::addColumn([
            'name'  => 'created_at',
            'label' => 'Registered',
            'type'  => 'datetime',
            'format' => 'M j, Y',
        ]);

        // Filters disabled - requires Backpack Pro
        // Advanced Filters and Bulk Actions removed to maintain compatibility with Backpack Free

        // Set default ordering
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
        // CRUD::setValidation(PatientRequest::class);
        // CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
        CRUD::field([
            'label'     => 'Registered User',
            'type'      => 'select',
            'name'      => 'user_id',
            'entity'    => 'user',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => function () {
                return \App\Models\User::role('Patient')
                    ->whereDoesntHave('patientProfile')
                    ->pluck('name', 'id')
                    ->toArray();
            },
            'hint'      => 'Select a registered user that does not have a profile yet',
        ]);

        // CRUD::field([
        //     'name'         => 'image_path',
        //     'label'        => 'Profile Image',
        //     'type'         => 'image',
        //     'crop'         => true,       // set to true to allow cropping, false to disable
        //     'aspect_ratio' => 1,          // omit or set to 0 to allow any aspect ratio
        //     'disk'         => 'public',   // in case you need to show images from a different disk
        //     'prefix'       => 'storage/', // in case your stored files have a common prefix
        // ]);

// Fields for the PatientProfile table

// Dropdown for Gender using select_from_array
        CRUD::field([
            'name'    => 'gender',
            'label'   => 'Gender',
            'type'    => 'select_from_array',
            'options' => [
                'Male'   => 'Male',
                'Female' => 'Female',
                'Other'  => 'Other',
            ],
        ]);

        CRUD::field([
            'name'  => 'phone',
            'label' => 'Phone Number',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'address',
            'label' => 'Address',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'birth_date',
            'label' => 'Birth Date',
            'type'  => 'date',
        ]);

        CRUD::field([
            'name'  => 'emergency_contact_name',
            'label' => 'Emergency Contact Name',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'emergency_contact_phone',
            'label' => 'Emergency Contact Phone',
            'type'  => 'text',
        ]);

        CRUD::field([
            'name'  => 'emergency_contact_relationship',
            'label' => 'Emergency Contact Relationship',
            'type'  => 'text',
        ]);

// Dropdown for PhilHealth Membership using select_from_array
        CRUD::field([
            'name'    => 'philhealth_membership',
            'label'   => 'PhilHealth Membership',
            'type'    => 'select_from_array',
            'options' => [
                'None'      => 'None',
                'Member'    => 'Member',
                'Dependent' => 'Dependent',
            ],
        ]);

        CRUD::field([
            'name'  => 'philhealth_number',
            'label' => 'PhilHealth Number',
            'type'  => 'text',
        ]);

        // Civil Status field
        CRUD::field([
            'name'    => 'civil_status',
            'label'   => 'Civil Status',
            'type'    => 'select_from_array',
            'options' => [
                'single'     => 'Single',
                'married'    => 'Married',
                'widowed'    => 'Widowed',
                'separated'  => 'Separated',
                'divorced'   => 'Divorced',
            ],
        ]);

        // Occupation field
        CRUD::field([
            'name'  => 'occupation',
            'label' => 'Occupation',
            'type'  => 'text',
            'hint'  => 'e.g., Teacher, Business Owner, Student',
        ]);

        // Religion field
        CRUD::field([
            'name'  => 'religion',
            'label' => 'Religion',
            'type'  => 'text',
            'hint'  => 'e.g., Catholic, Protestant, Muslim',
        ]);

        // Blood Type field
        CRUD::field([
            'name'    => 'blood_type',
            'label'   => 'Blood Type',
            'type'    => 'select_from_array',
            'options' => [
                'A+'  => 'A+',
                'A-'  => 'A-',
                'B+'  => 'B+',
                'B-'  => 'B-',
                'AB+' => 'AB+',
                'AB-' => 'AB-',
                'O+'  => 'O+',
                'O-'  => 'O-',
            ],
        ]);

        // Barangay Captain field
        CRUD::field([
            'name'  => 'barangay_captain',
            'label' => 'Barangay Captain',
            'type'  => 'text',
            'hint'  => 'Name of the barangay captain',
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

    public function store()
    {
        $request = $this->crud->getRequest();

        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gender' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);

        // Create the patient profile
        $patientProfile = \App\Models\PatientProfile::create($request->all());

        // Assign Patient role to the user if they don't have it
        $user = \App\Models\User::find($request->user_id);
        if ($user && !$user->hasRole('Patient')) {
            $user->assignRole('Patient');
        }

        // Redirect back to the list with success message
        return redirect()->route('patient.index')->with('success', 'Patient profile created successfully!');
    }
}
