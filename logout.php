<?php
require_once __DIR__ . '/includes/funcoes.php';
$_SESSION = []; session_destroy();
header('Location: ' . url('index.php')); exit;
