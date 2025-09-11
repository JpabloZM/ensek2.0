<?php

// Debug script to test service request
require_once __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo '<h1>Debugging ServiceRequest</h1>';

// Check if ServiceRequest model exists
echo '<h2>Model Check</h2>';
if (class_exists('App\Models\ServiceRequest')) {
    echo 'ServiceRequest model exists<br>';
} else {
    echo 'ServiceRequest model does not exist<br>';
}

// Check table structure
echo '<h2>Table Structure</h2>';
try {
    $columns = DB::getSchemaBuilder()->getColumnListing('service_requests');
    echo 'Columns in service_requests table:<br>';
    echo '<pre>';
    print_r($columns);
    echo '</pre>';
} catch (Exception $e) {
    echo 'Error getting table structure: ' . $e->getMessage();
}

// Check authentication
echo '<h2>Authentication</h2>';
if (Auth::check()) {
    echo 'User is logged in:<br>';
    echo '<pre>';
    print_r(Auth::user());
    echo '</pre>';
} else {
    echo 'No user is logged in<br>';
}

// Test creating a service request
echo '<h2>Create Service Request Test</h2>';
try {
    $request = new App\Models\ServiceRequest([
        'service_id' => 1,
        'user_id' => 1,
        'client_name' => 'Test Client',
        'client_phone' => '123456789',
        'client_email' => 'test@example.com',
        'description' => 'Test description',
        'address' => 'Test address',
        'status' => 'pendiente'
    ]);
    
    echo '<pre>';
    print_r($request->toArray());
    echo '</pre>';
    
} catch (Exception $e) {
    echo 'Error creating ServiceRequest: ' . $e->getMessage();
}

$kernel->terminate($request, $response);
