<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    protected $supabaseService;
    protected $table = 'items'; // Change this to your table name

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $items = $this->supabaseService->getAll($this->table);
            
            // Log for debugging
            Log::info('Items retrieved:', ['count' => count($items)]);
            
            return view('items.index', compact('items'));
        } catch (\Exception $e) {
            Log::error('Error retrieving items: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error retrieving items: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                // Add more validation rules as needed
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Prepare data for Supabase
            $data = $request->only(['name', 'description']);
            
            // Add timestamps
            $data['created_at'] = now()->toISOString();
            $data['updated_at'] = now()->toISOString();
            
            // Log data being sent
            Log::info('Creating item with data:', $data);
            
            $result = $this->supabaseService->create($this->table, $data);
            
            // Check if we got a valid response
            if (empty($result)) {
                Log::warning('Empty result from Supabase create operation');
                return redirect()->route('items.index')
                    ->with('warning', 'Item may not have been created. Please check the database.');
            }
            
            Log::info('Item created successfully:', $result);
            
            return redirect()->route('items.index')
                ->with('success', 'Item created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating item: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating item: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $item = $this->supabaseService->getById($this->table, $id);
            
            if (!$item) {
                return redirect()->route('items.index')
                    ->with('error', 'Item not found.');
            }
            
            return view('items.show', compact('item'));
        } catch (\Exception $e) {
            Log::error('Error retrieving item: ' . $e->getMessage());
            return redirect()->route('items.index')
                ->with('error', 'Error retrieving item: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $item = $this->supabaseService->getById($this->table, $id);
            
            if (!$item) {
                return redirect()->route('items.index')
                    ->with('error', 'Item not found.');
            }
            
            return view('items.edit', compact('item'));
        } catch (\Exception $e) {
            Log::error('Error retrieving item for edit: ' . $e->getMessage());
            return redirect()->route('items.index')
                ->with('error', 'Error retrieving item: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                // Add more validation rules as needed
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Prepare data for Supabase
            $data = $request->only(['name', 'description']);
            
            // Add updated_at timestamp
            $data['updated_at'] = now()->toISOString();
            
            $result = $this->supabaseService->update($this->table, $id, $data);
            
            // Check if we got a valid response
            if (empty($result)) {
                Log::warning('Empty result from Supabase update operation');
                return redirect()->route('items.index')
                    ->with('warning', 'Item may not have been updated. Please check the database.');
            }
            
            return redirect()->route('items.index')
                ->with('success', 'Item updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating item: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating item: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $result = $this->supabaseService->delete($this->table, $id);
            
            if (!$result) {
                return redirect()->route('items.index')
                    ->with('error', 'Failed to delete item.');
            }
            
            return redirect()->route('items.index')
                ->with('success', 'Item deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting item: ' . $e->getMessage());
            return redirect()->route('items.index')
                ->with('error', 'Error deleting item: ' . $e->getMessage());
        }
    }
}