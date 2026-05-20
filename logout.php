<?php
require_once __DIR__ . '/auth.php';

roomtemperature_clear_session();

header('Location: login.php');
exit;
