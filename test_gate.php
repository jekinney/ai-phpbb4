<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Gate;

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = User::whereHas('roles', function($q) { 
    $q->where('name', 'Super Administrator'); 
})->first();

echo "Admin ID: " . $admin->id . PHP_EOL;
echo "Has manage_home_page: " . ($admin->hasPermission('manage_home_page') ? 'YES' : 'NO') . PHP_EOL;

// Check if Gate is properly configured
try {
    $can = Gate::forUser($admin)->allows('manage_home_page');
    echo "Can manage_home_page: " . ($can ? 'YES' : 'NO') . PHP_EOL;
} catch (Exception $e) {
    echo "Gate error: " . $e->getMessage() . PHP_EOL;
}
