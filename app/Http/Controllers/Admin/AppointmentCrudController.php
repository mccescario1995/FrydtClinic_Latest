<?php
namespace App\Http\Controllers\Admin;

// use Backpack\CRUD\app\Http\Controllers\CrudController;
// use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class AppointmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AppointmentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Appointment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/appointment');
        CRUD::setEntityNameStrings('appointment', 'appointments');

        // Check if the authenticated user has the 'patient' role
        $isPatient = backpack_user()->hasRole('Patient');

        // Limit access for patients
        if ($isPatient) {
            $this->crud->denyAccess(['update', 'delete', 'show']);
            $this->crud->allowAccess(['create', 'list']);
            $this->crud->addClause('where', 'patient_id', backpack_user()->id);
        } else {
            // For staff and admin, allow full access to manage appointments
            $this->crud->allowAccess(['create', 'update', 'delete', 'show']);
        }
    }

    public function showBookingForm()
    {
        $services = Service::all();
        $user     = auth()->user();

        return view('appointments.index', compact('services', 'user'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operations-list
     * @return void
     */
    protected function setupListOperation()
    {
        // Enable responsive table and persistent settings
        CRUD::setOperationSetting('responsiveTable', true);
        CRUD::setOperationSetting('persistentTable', true);
        CRUD::setOperationSetting('showEntryCount', true);

        // Export buttons disabled - requires Backpack Pro
        // CRUD::enableExportButtons();

        // Patient column with search
        CRUD::addColumn([
            'name'      => 'patient_name',
            'label'     => 'Patient',
            'type'      => 'closure',
            'function'  => function($entry) {
                return $entry->patient ? $entry->patient->name : 'N/A';
            },
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('patient', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%'.$searchTerm.'%')
                      ->orWhere('email', 'like', '%'.$searchTerm.'%');
                });
            }
        ]);

        // Employee column (hidden for patients)
        CRUD::addColumn([
            'name'           => 'employee_name',
            'label'          => 'Employee',
            'type'           => 'closure',
            'function'       => function($entry) {
                return $entry->employee ? $entry->employee->name : 'N/A';
            },
            'visibleInTable' => !backpack_user()->hasRole('Patient'),
            'searchLogic'    => function ($query, $column, $searchTerm) {
                if (!backpack_user()->hasRole('Patient')) {
                    $query->orWhereHas('employee', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                }
            }
        ]);

        // Service column with search
        CRUD::addColumn([
            'name'      => 'service_name',
            'label'     => 'Service',
            'type'      => 'closure',
            'function'  => function($entry) {
                return $entry->service ? $entry->service->name : 'N/A';
            },
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('service', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%'.$searchTerm.'%');
                });
            }
        ]);

        // Date & Time column
        CRUD::addColumn([
            'name'  => 'appointment_datetime',
            'label' => 'Date & Time',
            'type'  => 'datetime',
            'format' => 'M j, Y g:i A',
        ]);

        // Duration column
        CRUD::addColumn([
            'name'  => 'duration_in_minutes',
            'label' => 'Duration',
            'type'  => 'closure',
            'function' => function($entry) {
                return $entry->duration_in_minutes . ' min';
            },
        ]);

        // Status column with badges
        CRUD::addColumn([
            'name'  => 'status',
            'label' => 'Status',
            'type'  => 'badge',
            'colors' => [
                'success' => 'completed',
                'warning' => 'scheduled',
                'danger' => 'cancelled',
                'info' => 'confirmed',
            ],
        ]);

        // Patient Notes (truncated)
        CRUD::addColumn([
            'name'  => 'patient_notes',
            'label' => 'Notes',
            'type'  => 'text',
            'limit' => 50,
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('patient_notes', 'like', '%'.$searchTerm.'%');
            }
        ]);

        // Created date
        CRUD::addColumn([
            'name'  => 'created_at',
            'label' => 'Booked On',
            'type'  => 'datetime',
            'format' => 'M d, Y',
        ]);

        // Filters disabled - requires Backpack Pro
        // Advanced Filters removed to maintain compatibility with Backpack Free

        // Date range filters disabled - requires Backpack Pro
        // CRUD::addFilter([
        //     'name'  => 'appointment_date',
        //     'type'  => 'date_range',
        //     'label' => 'Appointment Date'
        // ], [], function($value) {
        //     if ($value && isset($value['from']) && isset($value['to'])) {
        //         CRUD::addClause('whereDate', 'appointment_datetime', '>=', $value['from']);
        //         CRUD::addClause('whereDate', 'appointment_datetime', '<=', $value['to']);
        //     }
        // });

        // CRUD::addFilter([
        //     'name'  => 'booking_date',
        //     'type'  => 'date_range',
        //     'label' => 'Booking Date'
        // ], [], function($value) {
        //     if ($value && isset($value['from']) && isset($value['to'])) {
        //         CRUD::addClause('whereDate', 'created_at', '>=', $value['from']);
        //         CRUD::addClause('whereDate', 'created_at', '<=', $value['to']);
        //     }
        // });

        // Enable bulk operations for staff
        // Bulk actions disabled - requires Backpack Pro
        // if (!backpack_user()->hasRole('Patient')) {
        //     CRUD::enableBulkActions();
        //     CRUD::addBulkDeleteButton();
        // }

        // Set default ordering
        CRUD::orderBy('appointment_datetime', 'desc');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operations-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $isPatient = backpack_user()->hasRole('Patient');

        if ($isPatient) {
            CRUD::setValidation([
                'service_id'           => 'required|exists:services,id',
                'employee_id'          => 'required|exists:users,id',
                'appointment_datetime' => 'required|date|after:now',
            ]);

            CRUD::addField([
                'name'  => 'patient_id',
                'type'  => 'hidden',
                'value' => backpack_user()->id,
            ]);

            CRUD::addField([
                'name'      => 'service_id',
                'label'     => 'Service',
                'type'      => 'select',
                'entity'    => 'service',
                'model'     => 'App\Models\Service',
                'attribute' => 'name',
            ]);

            CRUD::addField([
                'name'      => 'employee_id',
                'label'     => 'Employee',
                'type'      => 'select',
                'entity'    => 'employee',
                'model'     => 'App\Models\User',
                'attribute' => 'name',
                'options'   => (function ($query) {
                    return $query->role('employee', 'web')->get();
                }),
            ]);

            CRUD::addField([
                'name'  => 'date',
                'label' => 'Appointment Date',
                'type'  => 'date',
            ]);

            CRUD::addField([
                'name'    => 'time',
                'label'   => 'Appointment Time',
                'type'    => 'select_from_array',
                'options' => $this->generateTimeSlots(),
            ]);

            CRUD::addField([
                'name'  => 'patient_notes',
                'label' => 'Notes',
                'type'  => 'textarea',
            ]);
        } else {
            // Staff/admin view
            CRUD::setValidation([
                'patient_id'           => 'required',
                'employee_id'          => 'required',
                'service_id'           => 'required',
                'appointment_datetime' => 'required|date',
            ]);

            CRUD::addField([
                'name'      => 'patient_id',
                'label'     => 'Patient',
                'type'      => 'select',
                'entity'    => 'patient',
                'model'     => 'App\Models\User',
                'attribute' => 'name',
                'options'   => (function ($query) {
                    return $query->role('Patient', 'web')->get();
                }),
            ]);

            CRUD::addField([
                'name'      => 'employee_id',
                'label'     => 'Employee',
                'type'      => 'select',
                'entity'    => 'employee',
                'model'     => 'App\Models\User',
                'attribute' => 'name',
                'options'   => (function ($query) {
                    return $query->role('employee', 'web')->get();
                }),
            ]);

            CRUD::addField([
                'name'      => 'service_id',
                'label'     => 'Service',
                'type'      => 'select',
                'entity'    => 'service',
                'model'     => 'App\Models\Service',
                'attribute' => 'name',
            ]);

            CRUD::addField([
                'name'  => 'date',
                'label' => 'Appointment Date',
                'type'  => 'date',
            ]);

            // CRUD::addField([
            //     'name' => 'time',
            //     'label' => 'Appointment Time',
            //     'type' => 'time',
            // ]);
            CRUD::addField([
                'name'    => 'time',
                'label'   => 'Appointment Time',
                'type'    => 'select_from_array',
                'options' => $this->generateTimeSlots(),
            ]);

            CRUD::addField([
                'name'    => 'status',
                'label'   => 'Status',
                'type'    => 'select_from_array',
                'options' => ['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'],
                'default' => 'scheduled',
            ]);

            CRUD::addField([
                'name'  => 'patient_notes',
                'label' => 'Patient Notes',
                'type'  => 'textarea',
            ]);

            CRUD::addField([
                'name'  => 'employee_notes',
                'label' => 'Employee Notes',
                'type'  => 'textarea',
            ]);

            CRUD::addField([
                'name'    => 'duration_in_minutes',
                'label'   => 'Duration (minutes)',
                'type'    => 'number',
                'default' => 30,
            ]);
        }
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operations-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Handle the update operation
     */
    public function update()
    {
        $response = $this->traitUpdate();

        // Check if status was changed to cancelled
        $appointment = $this->crud->getCurrentEntry();
        $request = $this->crud->getRequest();

        if ($request->has('status') && $request->status === 'cancelled') {
            // Get the original status before update
            $originalStatus = $appointment->getOriginal('status');

            // Only fire event if status actually changed to cancelled
            if ($originalStatus !== 'cancelled') {
                event(new \App\Events\AppointmentCancelled($appointment));
            }
        }

        return $response;
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $isPatient = backpack_user()->hasRole('Patient');

        if ($isPatient) {
            // Patient-specific validation and store
            $request = $this->crud->getRequest();
            $request->validate([
                'service_id'           => 'required',
                'employee_id'          => 'required',
                'appointment_datetime' => 'required|date|after:now',
                'patient_notes'        => 'nullable|string',
            ]);

            $service             = Service::findOrFail($request->service_id);
            $appointmentDateTime = Carbon::parse($request->appointment_datetime);

            // Check if the chosen time slot is available
            $employeeSchedule = User::findOrFail($request->employee_id)
                ->employee_schedules()
                ->where('day_of_week', $appointmentDateTime->dayOfWeekIso)
                ->first();

            if (! $employeeSchedule) {
                return redirect()->back()->withErrors(['appointment_datetime' => 'Employee is not available on this day.'])->withInput();
            }

            $existingAppointments = Appointment::where('employee_id', $request->employee_id)
                ->whereDate('appointment_datetime', $appointmentDateTime->toDateString())
                ->get();

            $isAvailable = true;
            foreach ($existingAppointments as $appointment) {
                $existingStart     = Carbon::parse($appointment->appointment_datetime);
                $existingEnd       = $existingStart->copy()->addMinutes($appointment->duration_in_minutes);
                $newAppointmentEnd = $appointmentDateTime->copy()->addMinutes($service->duration_minutes);

                if ($appointmentDateTime->lt($existingEnd) && $newAppointmentEnd->gt($existingStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if (! $isAvailable) {
                return redirect()->back()->withErrors(['appointment_datetime' => 'The selected time slot is already booked.'])->withInput();
            }

            // Create the appointment
            $appointment = Appointment::create([
                'patient_id'           => backpack_user()->id,
                'employee_id'          => $request->employee_id,
                'service_id'           => $service->id,
                'appointment_datetime' => $appointmentDateTime,
                'duration_in_minutes'  => $service->duration_minutes,
                'patient_notes'        => $request->patient_notes,
                'status'               => 'scheduled',
            ]);

            return redirect()->route('appointment.index')->with('success', 'Appointment successfully booked!');
        } else {
            // Staff/admin store operation
            $request = $this->crud->validateRequest();

            // Set duration based on service
            $service = Service::findOrFail($request->service_id);
            $request->request->set('duration_in_minutes', $service->duration_minutes);

            $this->crud->create($request->all());

            return redirect()->route('appointment.index')->with('success', 'Appointment successfully created!');
        }
    }

    /**
     * Generate time slots in 30-minute increments.
     *
     * @return array
     */
    protected function generateTimeSlots(): array
    {
        $slots = [];
        $start = Carbon::createFromTime(0, 0, 0);
        $end   = Carbon::createFromTime(23, 59, 59);

        while ($start->lte($end)) {
            $slots[$start->format('H:i')] = $start->format('H:i A');
            $start->addMinutes(30);
        }

        return $slots;
    }
}
