<?php

require_once __DIR__ . '/includes/firebase-helper.php';
require_once __DIR__ . '/config/constants.php';

echo "Creating comprehensive busy day simulation...\n\n";

$studentNames = [
    'Juan dela Cruz', 'Maria Garcia', 'Jose Martinez', 'Ana Rodriguez', 'Luis Hernandez',
    'Carmen Lopez', 'Pedro Gonzalez', 'Rosa Fernandez', 'Miguel Sanchez', 'Elena Ramirez',
    'Carlos Torres', 'Sofia Flores', 'Diego Rivera', 'Isabella Cruz', 'Gabriel Morales',
    'Valentina Perez', 'Alejandro Diaz', 'Natalia Vargas', 'Ricardo Castillo', 'Lucia Gomez',
    'Fernando Ruiz', 'Maria Torres', 'Antonio Vargas', 'Patricia Medina', 'Eduardo Herrera',
    'Andrea Jimenez', 'Oscar Romero', 'Claudia Vega', 'Raul Campos', 'Teresa Aguilar',
    'Mark Santos', 'Jessica Lee', 'Kevin Wang', 'Hannah Kim', 'Brian Co'
];

$studentData = [];
for ($i = 0; $i < 35; $i++) {
    $studentId = sprintf('24-%04d-%06d', rand(1000, 9999), rand(100000, 999999));
    $studentData[] = [
        'student_id' => $studentId,
        'full_name' => $studentNames[$i],
        'email' => strtolower(str_replace(' ', '.', $studentNames[$i])) . '.ui@phinmaed.com'
    ];
}

foreach ($studentData as $data) {
    createUser($data['student_id'], $data['student_id'], ROLE_STUDENT, $data['full_name'], $data['email']);
}

echo "Created " . count($studentData) . " students\n\n";

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$in5days = date('Y-m-d', strtotime('+5 days'));
$yesterday = date('Y-m-d', strtotime('-1 day'));
$days2 = date('Y-m-d', strtotime('+2 days'));
$days3 = date('Y-m-d', strtotime('+3 days'));
$days4 = date('Y-m-d', strtotime('+4 days'));
$days5 = date('Y-m-d', strtotime('+5 days'));
$days7 = date('Y-m-d', strtotime('+7 days'));
$daysNeg2 = date('Y-m-d', strtotime('-2 days'));

$appointments = [
    // Pending (new requests)
    ['type' => 'tor', 'date' => $tomorrow, 'status' => STATUS_PENDING, 'details' => ['contact_no' => '09123456789', 'purpose' => 'Job application', 'copy_quantity' => 2, 'message' => '']],
    ['type' => 'diploma', 'date' => $days2, 'status' => STATUS_PENDING, 'details' => ['year_graduated' => '2024', 'message' => '']],
    ['type' => 'certificate', 'date' => $days3, 'status' => STATUS_PENDING, 'details' => ['contact_no' => '09123456790', 'course' => 'BSIT', 'certification_type' => 'Enrollment', 'purpose' => 'Enrollment', 'copy_quantity' => 1]],
    ['type' => 'tor', 'date' => $days4, 'status' => STATUS_PENDING, 'details' => ['contact_no' => '09123456791', 'purpose' => 'Graduate school', 'copy_quantity' => 3, 'message' => '']],
    ['type' => 'request_rf', 'date' => $days5, 'status' => STATUS_PENDING, 'details' => ['contact_no' => '09123456792', 'semester' => '1st Semester', 'school_year' => '2025-2026', 'message' => '']],
    ['type' => 'tor', 'date' => $tomorrow, 'status' => STATUS_PENDING, 'details' => ['contact_no' => '09123456793', 'purpose' => 'Board exam', 'copy_quantity' => 5, 'message' => '']],
    ['type' => 'diploma', 'date' => $days2, 'status' => STATUS_PENDING, 'details' => ['year_graduated' => '2023', 'message' => '']],
    ['type' => 'certificate', 'date' => $days3, 'status' => STATUS_PENDING, 'details' => ['contact_no' => '09123456794', 'course' => 'BSBA', 'certification_type' => 'Graduation', 'purpose' => 'Graduation', 'copy_quantity' => 2]],
    ['type' => 'tor', 'date' => $days4, 'status' => STATUS_PENDING, 'details' => ['contact_no' => '09123456795', 'purpose' => 'Overseas application', 'copy_quantity' => 4, 'message' => '']],
    ['type' => 'diploma', 'date' => $days5, 'status' => STATUS_PENDING, 'details' => ['year_graduated' => '2022', 'message' => '']],
    
    // Scheduled for TODAY
    ['type' => 'tor', 'date' => $today, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456701', 'purpose' => 'Employment', 'copy_quantity' => 1, 'message' => '']],
    ['type' => 'diploma', 'date' => $today, 'status' => STATUS_SCHEDULED, 'details' => ['year_graduated' => '2024', 'message' => '']],
    ['type' => 'certificate', 'date' => $today, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456702', 'course' => 'BSHRM', 'certification_type' => 'Course Completion', 'purpose' => 'Thesis', 'copy_quantity' => 1]],
    ['type' => 'tor', 'date' => $today, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456703', 'purpose' => 'Internship', 'copy_quantity' => 2, 'message' => '']],
    
    // Scheduled for TOMORROW
    ['type' => 'tor', 'date' => $tomorrow, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456704', 'purpose' => 'Job interview', 'copy_quantity' => 1, 'message' => '']],
    ['type' => 'diploma', 'date' => $tomorrow, 'status' => STATUS_SCHEDULED, 'details' => ['year_graduated' => '2023', 'message' => '']],
    ['type' => 'request_rf', 'date' => $tomorrow, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456705', 'semester' => '2nd Semester', 'school_year' => '2024-2025', 'message' => '']],
    ['type' => 'tor', 'date' => $tomorrow, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456706', 'purpose' => 'Scholarship', 'copy_quantity' => 2, 'message' => '']],
    
    // Scheduled for 2 days from now
    ['type' => 'certificate', 'date' => $days2, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456707', 'course' => 'BSIT', 'certification_type' => 'Enrollment', 'purpose' => 'Enrollment verification', 'copy_quantity' => 1]],
    ['type' => 'tor', 'date' => $days2, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456708', 'purpose' => 'Visa application', 'copy_quantity' => 2, 'message' => '']],
    ['type' => 'diploma', 'date' => $days2, 'status' => STATUS_SCHEDULED, 'details' => ['year_graduated' => '2021', 'message' => '']],
    
    // Scheduled for 5 days from now
    ['type' => 'tor', 'date' => $days5, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456709', 'purpose' => 'Board exam requirements', 'copy_quantity' => 5, 'message' => '']],
    ['type' => 'diploma', 'date' => $days5, 'status' => STATUS_SCHEDULED, 'details' => ['year_graduated' => '2024', 'message' => '']],
    ['type' => 'certificate', 'date' => $days5, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456710', 'course' => 'BSBA', 'certification_type' => 'Graduation', 'purpose' => 'Commencement', 'copy_quantity' => 2]],
    ['type' => 'tor', 'date' => $days5, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456711', 'purpose' => 'Higher education', 'copy_quantity' => 3, 'message' => '']],
    
    // Scheduled for 7 days from now
    ['type' => 'diploma', 'date' => $days7, 'status' => STATUS_SCHEDULED, 'details' => ['year_graduated' => '2020', 'message' => '']],
    ['type' => 'tor', 'date' => $days7, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456712', 'purpose' => 'Abroad work', 'copy_quantity' => 1, 'message' => '']],
    
    // Pending with RESCHEDULE request (student requested new date)
    ['type' => 'tor', 'date' => $today, 'status' => STATUS_PENDING, 'rescheduled_date' => $days5, 'reschedule_reason' => 'Conflict with class schedule', 'details' => ['contact_no' => '09123456713', 'purpose' => 'Job application', 'copy_quantity' => 1, 'message' => '']],
    ['type' => 'diploma', 'date' => $tomorrow, 'status' => STATUS_PENDING, 'rescheduled_date' => $days7, 'reschedule_reason' => 'Family emergency', 'details' => ['year_graduated' => '2023', 'message' => '']],
    ['type' => 'certificate', 'date' => $days2, 'status' => STATUS_PENDING, 'rescheduled_date' => $days5, 'reschedule_reason' => 'Have a major exam', 'details' => ['contact_no' => '09123456714', 'course' => 'BSIT', 'certification_type' => 'Enrollment', 'purpose' => 'Enrollment', 'copy_quantity' => 1]],
    
    // Scheduled past dates (should show in today's scheduled)
    ['type' => 'tor', 'date' => $yesterday, 'status' => STATUS_SCHEDULED, 'details' => ['contact_no' => '09123456715', 'purpose' => 'Previous appointment', 'copy_quantity' => 1, 'message' => '']],
    ['type' => 'diploma', 'date' => $daysNeg2, 'status' => STATUS_SCHEDULED, 'details' => ['year_graduated' => '2022', 'message' => '']],
];

$count = 0;
foreach ($studentData as $index => $student) {
    if ($index >= count($appointments)) break;
    
    $apt = $appointments[$index];
    $data = [
        'student_id' => $student['student_id'],
        'type' => $apt['type'],
        'date' => $apt['date'],
        'status' => $apt['status'],
        'details' => $apt['details'],
        'created_at' => date('Y-m-d H:i:s', strtotime('-'.rand(1,5).' days'))
    ];
    
    if (!empty($apt['rescheduled_date'])) {
        $data['rescheduled_date'] = $apt['rescheduled_date'];
        $data['reschedule_reason'] = $apt['reschedule_reason'];
    }
    
    createAppointment($data);
    
    $statusLabel = $apt['status'];
    if (!empty($apt['rescheduled_date'])) $statusLabel .= ' (Reschedule Request)';
    
    echo "Created {$apt['type']} - {$apt['date']} - $statusLabel\n";
    $count++;
}

echo "\n=== Simulation Complete ===\n";
echo "Students: " . count($studentData) . "\n";
echo "Appointments: $count\n\n";
echo "Summary:\n";
echo "  - Pending Requests: ~10\n";
echo "  - Reschedule Requests: 3\n";
echo "  - Scheduled Today: 4\n";
echo "  - Scheduled Tomorrow: 4\n";
echo "  - Scheduled 2-5 days: 10\n";
echo "  - Scheduled 7+ days: 2\n\n";
echo "Login: admin / 1\n";