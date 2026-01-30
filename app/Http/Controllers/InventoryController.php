<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\InventoryTransaction;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\DeductInventoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InventoryController extends Controller
{
    /**
     * Display listing of items with search (Requirement 5)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::query()
            ->search($search)
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Items/Index', [
            'items' => $items,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Show form for creating items
     */
    public function create()
    {
        return Inertia::render('Items/Create');
    }

    /**
     * Store new items - single or bulk (Requirement 1)
     */
    public function store(StoreInventoryRequest $request)
    {
        try {
            $items = DB::transaction(function () use ($request) {
                $batchId = count($request->items) > 1 ? 'batch-' . Str::uuid() : null;
                $createdItems = [];

                foreach ($request->items as $itemData) {
                    // Create item
                    $item = Item::create([
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'unit_type' => $itemData['unit_type'],
                        'current_quantity' => $itemData['quantity'],
                        'minimum_quantity' => $itemData['minimum_quantity'] ?? null,
                    ]);

                    // Record transaction (Requirement 4)
                    InventoryTransaction::create([
                        'item_id' => $item->id,
                        'transaction_type' => 'addition',
                        'quantity' => $itemData['quantity'],
                        'previous_quantity' => 0,
                        'new_quantity' => $itemData['quantity'],
                        'notes' => 'Initial stock',
                        'batch_id' => $batchId,
                        'user_id' => auth()->id(),
                    ]);

                    $createdItems[] = $item;
                }

                return $createdItems;
            });

            $message = count($items) === 1 
                ? 'Item added successfully!' 
                : count($items) . ' items added successfully!';

            return redirect()->route('items.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add items: ' . $e->getMessage());
        }
    }

    /**
     * Show form for deducting items
     */
    public function createDeduct()
    {
        $items = Item::where('current_quantity', '>', 0)
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Items/Deduct', [
            'items' => $items,
        ]);
    }

    /**
     * Deduct quantities from items (Requirement 3)
     */
    public function deduct(DeductInventoryRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $batchId = count($request->deductions) > 1 ? 'batch-' . Str::uuid() : null;

                foreach ($request->deductions as $deduction) {
                    $item = Item::findOrFail($deduction['id']);
                    $previousQuantity = $item->current_quantity;
                    $newQuantity = $previousQuantity - $deduction['quantity'];

                    // Update item quantity
                    $item->update(['current_quantity' => $newQuantity]);

                    // Record transaction (Requirement 4)
                    InventoryTransaction::create([
                        'item_id' => $item->id,
                        'transaction_type' => 'deduction',
                        'quantity' => $deduction['quantity'],
                        'previous_quantity' => $previousQuantity,
                        'new_quantity' => $newQuantity,
                        'notes' => $deduction['notes'] ?? null,
                        'batch_id' => $batchId,
                        'user_id' => auth()->id(),
                    ]);
                }
            });

            $message = count($request->deductions) === 1 
                ? 'Item deducted successfully!' 
                : count($request->deductions) . ' items deducted successfully!';

            return redirect()->route('items.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deduct items: ' . $e->getMessage());
        }
    }

    /**
     * Display transaction history for an item (Requirement 4)
     */
    public function history(Item $item)
    {
        $transactions = $item->transactions()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Items/History', [
            'item' => $item,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Remove item (soft delete)
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully!');
    }
}