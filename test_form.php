<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Check tables exist
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
echo "Tables: " . implode(', ', array_column($tables, 'name')) . "\n";

// Simulate a valid form POST
$request = Illuminate\Http\Request::create('/contact', 'POST', [
    'name'             => 'Test Visitor',
    'company'          => 'Test Company',
    'email'            => 'visitor@testcompany.com',
    'service'          => 'software-development',
    'project_details'  => 'We need help building a custom web application for our business operations team.',
    'website'          => '',
    '_form_load_time'  => (string)(time() - 10),
    'recaptcha_token'  => '',
]);
$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

$response = $kernel->handle($request);
echo "HTTP Status: " . $response->getStatusCode() . "\n";
echo "Response body: " . $response->getContent() . "\n";

// Check DB
$count = DB::table('inquiries')->count();
echo "Inquiries in DB: $count\n";
if ($count > 0) {
    $last = DB::table('inquiries')->latest()->first();
    echo "Last record name: " . $last->name . "\n";
    echo "Last record email: " . $last->email . "\n";
    echo "Last record service: " . $last->service . "\n";
}