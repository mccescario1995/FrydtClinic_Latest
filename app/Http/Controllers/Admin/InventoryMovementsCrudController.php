<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\InventoryMovementsRequest;
use App\Models\Inventory;
use App\Models\InventoryMovements;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class InventoryMovementsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InventoryMovementsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\InventoryMovements::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/inventory-movements');
        CRUD::setEntityNameStrings('stock history', 'stock history');

        // Disable create and update operations as inventory movements should be logged via stock management
        CRUD::denyAccess(['create', 'update', 'delete', 'show']);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->data['breadcrumbs'] = [
            trans('backpack::base.dashboard') => backpack_url('dashboard'),
            'Inventory'                       => backpack_url('inventory'),
            'Stock History'                   => false,
        ];
        CRUD::column('inventory')->label('Item Name')->entity('inventory')->model("App\Models\InventoryMovements")->attribute('name');
        CRUD::column('quantity_moved')->label('Quantity Moved');
        CRUD::column('movement_type')->label('Movement Type');
        CRUD::column('notes');
        CRUD::column('user')->label('User')->entity('user')->model("App\Models\InventoryMovements")->attribute('name');
        CRUD::column('created_at')->label('Date');
        CRUD::column('updated_at')->label('Last Updated');
        //remove action column
        $this->crud->removeAllButtonsFromStack('line');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(InventoryMovementsRequest::class);
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

    /**
     * Show the stock management page for a specific inventory item.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function manageStock($id)
    {
        $inventory               = Inventory::findOrFail($id);
        $this->data['inventory'] = $inventory;
        $this->data['title']     = 'Manage Stock for ' . $inventory->name;
        return view('admin.inventory.stock_manage_page', $this->data);
    }

    /**
     * Handle the stock management form submission.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity_moved' => 'required|integer|not_in:0',
            'movement_type'  => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        $inventory      = Inventory::findOrFail($id);
        $quantity_moved = (int) $request->input('quantity_moved');

        // Update the inventory quantity
        $inventory->current_quantity += $quantity_moved;
        $inventory->save();

        // Log the movement
        InventoryMovements::create([
            'inventory_id'   => $inventory->id,
            'quantity_moved' => $quantity_moved,
            'movement_type'  => $request->input('movement_type'),
            'notes'          => $request->input('notes'),
            'user_id'        => backpack_auth()->id(),
        ]);

        \Alert::success('Stock updated successfully!')->flash();

        return redirect()->route('inventory.index');
    }
}
