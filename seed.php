<?php

require_once __DIR__ . '/includes/firebase-helper.php';
require_once __DIR__ . '/config/constants.php';

echo "Seeding RegiTrack Database...\n\n";

$adminData = [
    'student_id' => 'admin',
    'password' => password_hash('1', PASSWORD_DEFAULT),
    'is_first_login' => false,
    'role' => ROLE_ADMIN
];

$result = firebaseRequest(USERS_PATH . '/admin', 'PUT', $adminData);

if ($result !== null) {
    echo "✓ Admin account created (username: admin, password: 1)\n";
} else {
    echo "✗ Failed to create admin account\n";
}

echo "\nSeed complete!\n";
echo "Login credentials:\n";
echo "  Admin: admin / 1\n";
