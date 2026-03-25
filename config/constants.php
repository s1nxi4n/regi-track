<?php

define('ROLE_ADMIN', 'admin');
define('ROLE_STUDENT', 'student');

define('STATUS_SCHEDULED', 'Scheduled');
define('STATUS_IN_PROCESS', 'In Process');
define('STATUS_PENDING', 'Pending');
define('STATUS_REJECTED', 'Rejected');
define('STATUS_SETTLED', 'Settled');
define('STATUS_NO_SHOW', 'No-Show');
define('STATUS_CANCELLED', 'Cancelled');

define('STATUS_ORDER', [
    STATUS_SCHEDULED => 1,
    STATUS_IN_PROCESS => 2,
    STATUS_PENDING => 3
]);

$APPOINTMENT_TYPES = [
    'tor' => [
        'label' => 'Transcript of Records (TOR)',
        'fields' => ['contact_no', 'purpose', 'copy_quantity', 'message']
    ],
    'diploma' => [
        'label' => 'Diploma',
        'fields' => ['year_graduated', 'message']
    ],
    'request_rf' => [
        'label' => 'Request RF',
        'fields' => ['contact_no', 'semester', 'school_year', 'purpose']
    ],
    'certificate' => [
        'label' => 'Certificate',
        'fields' => ['contact_no', 'course', 'certification_type', 'purpose', 'copy_quantity']
    ]
];

define('STUDENT_ID_FORMAT', '/^\d{2}-\d{4}-\d{6}$/');
define('EMAIL_FORMAT', '/^.+\.ui@phinmaed\.com$/');
