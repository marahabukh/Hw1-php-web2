<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $headers;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');
        $this->headers = [
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation'
        ];
    }

    public function getAll($table)
    {
        $response = Http::withHeaders($this->headers)
            ->withoutVerifying()
            ->get("{$this->supabaseUrl}/rest/v1/{$table}?select=*");
        
        // Log the response for debugging
        Log::info("Supabase getAll response for {$table}:", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        return $response->json() ?: [];
    }

    public function getById($table, $id)
    {
        $response = Http::withHeaders($this->headers)
            ->withoutVerifying()
            ->get("{$this->supabaseUrl}/rest/v1/{$table}?id=eq.{$id}&select=*");
        
        // Log the response for debugging
        Log::info("Supabase getById response for {$table} id {$id}:", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        $data = $response->json();
        return !empty($data) ? $data[0] : null;
    }

    public function create($table, $data)
    {
        // Add timestamps if they don't exist
        if (!isset($data['created_at'])) {
            $data['created_at'] = now()->toISOString();
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = now()->toISOString();
        }
        
        // Log the request data for debugging
        Log::info("Supabase create request for {$table}:", [
            'data' => $data
        ]);
        
        $response = Http::withHeaders($this->headers)
            ->withoutVerifying()
            ->post("{$this->supabaseUrl}/rest/v1/{$table}", $data);
        
        // Log the response for debugging
        Log::info("Supabase create response for {$table}:", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        return $response->json() ?: [];
    }

    public function update($table, $id, $data)
    {
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = now()->toISOString();
        }
        
        // Log the request data for debugging
        Log::info("Supabase update request for {$table} id {$id}:", [
            'data' => $data
        ]);
        
        $response = Http::withHeaders($this->headers)
            ->withoutVerifying()
            ->patch("{$this->supabaseUrl}/rest/v1/{$table}?id=eq.{$id}", $data);
        
        Log::info("Supabase update response for {$table} id {$id}:", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        return $response->json() ?: [];
    }

    public function delete($table, $id)
    {
        $response = Http::withHeaders($this->headers)
            ->withoutVerifying()
            ->delete("{$this->supabaseUrl}/rest/v1/{$table}?id=eq.{$id}");
        
        Log::info("Supabase delete response for {$table} id {$id}:", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
        
        return $response->successful();
    }
}