<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugController extends Controller
{
    public function testAnalyze(Request $request)
    {
        try {
            Log::info('=== DEBUG ANALYZE START ===');
            Log::info('Request method: ' . $request->method());
            Log::info('Request URL: ' . $request->url());
            Log::info('All input: ', $request->all());
            Log::info('Has image: ' . ($request->hasFile('image') ? 'YES' : 'NO'));
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                Log::info('Image info: ', [
                    'name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType()
                ]);
            }
            
            // Test simple de validation
            Log::info('Testing validation...');
            
            $request->validate([
                'patient_id' => 'required',
                'image' => 'required|file'
            ]);
            
            Log::info('Validation passed');
            
            // Test de réponse JSON simple
            return response()->json([
                'success' => true,
                'message' => 'Test réussi !',
                'patient_id' => $request->input('patient_id'),
                'has_image' => $request->hasFile('image'),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('DEBUG ERROR: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}