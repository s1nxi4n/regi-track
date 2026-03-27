<?php

require_once __DIR__ . '/../../includes/session.php';

startSession();
regenerateSession(); // Prevent session fixation after logout
destroySession();

header('Location: /views/login.php');
exit;
