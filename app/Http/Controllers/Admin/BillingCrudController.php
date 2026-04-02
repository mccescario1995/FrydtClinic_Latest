<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class BillingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BillingCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Billing::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/billing');
        CRUD::setEntityNameStrings('billing record', 'billing records');
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
            'entity'    => 'patient',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'name'  => 'invoice_number',
            'label' => 'Invoice #',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'invoice_date',
            'label' => 'Invoice Date',
            'type'  => 'date',
        ]);

        CRUD::addColumn([
            'name'  => 'total_amount',
            'label' => 'Total Amount',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'balance_due',
            'label' => 'Balance Due',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'payment_status',
            'label' => 'Status',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'due_date',
            'label' => 'Due Date',
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
            'entity'    => 'patient',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => (function ($query) {
                return $query->role('Patient')->get();
            }),
        ]);

        CRUD::addField([
            'name'  => 'invoice_number',
            'label' => 'Invoice Number',
            'type'  => 'text',
            'hint'  => 'Auto-generated if left blank',
        ]);

        CRUD::addField([
            'name'  => 'invoice_date',
            'label' => 'Invoice Date',
            'type'  => 'date',
            'default' => date('Y-m-d'),
        ]);

        CRUD::addField([
            'name'  => 'due_date',
            'label' => 'Due Date',
            'type'  => 'date',
        ]);

        CRUD::addField([
            'name'  => 'service_start_date',
            'label' => 'Service Start Date',
            'type'  => 'date',
        ]);

        CRUD::addField([
            'name'  => 'service_end_date',
            'label' => 'Service End Date',
            'type'  => 'date',
        ]);

        CRUD::addField([
            'name'  => 'subtotal_amount',
            'label' => 'Subtotal Amount (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        CRUD::addField([
            'name'  => 'discount_amount',
            'label' => 'Discount Amount (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
            'default' => 0,
        ]);

        CRUD::addField([
            'name'  => 'philhealth_coverage',
            'label' => 'PhilHealth Coverage (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
            'default' => 0,
        ]);

        CRUD::addField([
            'name'  => 'tax_amount',
            'label' => 'Tax Amount (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
            'default' => 0,
        ]);

        CRUD::addField([
            'name'  => 'total_amount',
            'label' => 'Total Amount (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        CRUD::addField([
            'name'  => 'amount_paid',
            'label' => 'Amount Paid (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
            'default' => 0,
        ]);

        CRUD::addField([
            'name'  => 'balance_due',
            'label' => 'Balance Due (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        CRUD::addField([
            'name'    => 'payment_status',
            'label'   => 'Payment Status',
            'type'    => 'select_from_array',
            'options' => [
                'unpaid'   => 'Unpaid',
                'partial'  => 'Partial',
                'paid'     => 'Paid',
                'overdue'  => 'Overdue',
                'cancelled' => 'Cancelled',
            ],
            'default' => 'unpaid',
        ]);

        CRUD::addField([
            'name'  => 'payment_method',
            'label' => 'Payment Method',
            'type'  => 'text',
            'hint'  => 'e.g., Cash, Credit Card, Bank Transfer',
        ]);

        CRUD::addField([
            'name'  => 'payment_reference',
            'label' => 'Payment Reference',
            'type'  => 'text',
            'hint'  => 'Receipt number, transaction ID, etc.',
        ]);

        // Insurance Information
        CRUD::addField([
            'name'    => 'has_insurance',
            'label'   => 'Has Insurance',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
        ]);

        CRUD::addField([
            'name'  => 'insurance_provider',
            'label' => 'Insurance Provider',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'insurance_policy_number',
            'label' => 'Insurance Policy Number',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'insurance_coverage_amount',
            'label' => 'Insurance Coverage Amount (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        // PhilHealth Information
        CRUD::addField([
            'name'    => 'philhealth_member',
            'label'   => 'PhilHealth Member',
            'type'    => 'select_from_array',
            'options' => [
                false => 'No',
                true  => 'Yes',
            ],
        ]);

        CRUD::addField([
            'name'  => 'philhealth_number',
            'label' => 'PhilHealth Number',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'philhealth_benefit_amount',
            'label' => 'PhilHealth Benefit Amount (₱)',
            'type'  => 'number',
            'attributes' => ['step' => '0.01'],
        ]);

        // Responsible Party
        CRUD::addField([
            'name'  => 'responsible_party_name',
            'label' => 'Responsible Party Name',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'responsible_party_relationship',
            'label' => 'Responsible Party Relationship',
            'type'  => 'text',
        ]);

        CRUD::addField([
            'name'  => 'responsible_party_contact',
            'label' => 'Responsible Party Contact',
            'type'  => 'text',
        ]);

        // Notes
        CRUD::addField([
            'name'  => 'billing_notes',
            'label' => 'Billing Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addField([
            'name'  => 'collection_notes',
            'label' => 'Collection Notes',
            'type'  => 'textarea',
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
            'name'  => 'service_start_date',
            'label' => 'Service Start',
            'type'  => 'date',
        ]);

        CRUD::addColumn([
            'name'  => 'service_end_date',
            'label' => 'Service End',
            'type'  => 'date',
        ]);

        CRUD::addColumn([
            'name'  => 'subtotal_amount',
            'label' => 'Subtotal',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'discount_amount',
            'label' => 'Discount',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'philhealth_coverage',
            'label' => 'PhilHealth Coverage',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'tax_amount',
            'label' => 'Tax',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'amount_paid',
            'label' => 'Amount Paid',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'payment_method',
            'label' => 'Payment Method',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'payment_reference',
            'label' => 'Payment Reference',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'has_insurance',
            'label' => 'Has Insurance',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'insurance_provider',
            'label' => 'Insurance Provider',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'insurance_policy_number',
            'label' => 'Insurance Policy',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'insurance_coverage_amount',
            'label' => 'Insurance Coverage',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'philhealth_member',
            'label' => 'PhilHealth Member',
            'type'  => 'boolean',
        ]);

        CRUD::addColumn([
            'name'  => 'philhealth_number',
            'label' => 'PhilHealth Number',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'philhealth_benefit_amount',
            'label' => 'PhilHealth Benefit',
            'type'  => 'number',
            'prefix' => '₱',
            'decimals' => 2,
        ]);

        CRUD::addColumn([
            'name'  => 'responsible_party_name',
            'label' => 'Responsible Party',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'responsible_party_relationship',
            'label' => 'Relationship',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'responsible_party_contact',
            'label' => 'Contact',
            'type'  => 'text',
        ]);

        CRUD::addColumn([
            'name'  => 'billing_notes',
            'label' => 'Billing Notes',
            'type'  => 'textarea',
        ]);

        CRUD::addColumn([
            'name'  => 'collection_notes',
            'label' => 'Collection Notes',
            'type'  => 'textarea',
        ]);
    }
}
