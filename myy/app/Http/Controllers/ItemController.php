<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    protected $supabaseService;
    protected $table = 'items'; 

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

  
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

  
    public function create()
    {
        return view('items.create');
    }

  
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $request->only(['name', 'email']);
            
            $data['created_at'] = now()->toISOString();
            $data['updated_at'] = now()->toISOString();
            
            Log::info('Creating item with data:', $data);
            
            $result = $this->supabaseService->create($this->table, $data);
            
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

  
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $request->only(['name', 'email']);
            
            $data['updated_at'] = now()->toISOString();
            
            $result = $this->supabaseService->update($this->table, $id, $data);
            
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