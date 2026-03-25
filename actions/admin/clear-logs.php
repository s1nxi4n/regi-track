<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

firebaseRequest(ADMIN_LOGS_PATH, 'DELETE');

header('Location: ../../views/admin/history.php');
exit;
