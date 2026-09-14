<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'walimurid@smkn1boyolangu.sch.id')->first();

// Test passwords
$passwords = ['password', 'admin123', '12345678', 'Password123', 'password123'];
foreach($passwords as $pass) {
    if (Hash::check($pass, $user->password)) {
        echo "Password MATCH: $pass\n";
        break;
    }
}

// Reset password to 'password'
$user->update(['password' => Hash::make('password')]);
echo "Password has been reset to: password\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";
