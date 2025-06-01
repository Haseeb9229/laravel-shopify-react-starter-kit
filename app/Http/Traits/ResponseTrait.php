<?php

namespace App\Http\Traits;

use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

trait ResponseTrait
{
    protected function render(string $component, array $props = [])
    {
        $prefix = env('SHOPIFY_APPBRIDGE_ENABLED') ? 'Embedded/' : 'NonEmbedded';
        return Inertia::render($prefix . $component, $props);
    }

    protected function sendResponse($data = [], $message = 'Success', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function logData($data){
        Log::info(json_encode($data, JSON_PRETTY_PRINT));
    }
}
