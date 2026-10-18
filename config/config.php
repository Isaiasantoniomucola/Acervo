<?php
// ===== Configuração do EDUCA PDF =====
define('DB_HOST', 'localhost');
define('DB_NAME', 'educa_pdf');
define('DB_USER', 'root');
define('DB_PASS', '');

// Pasta do projeto no servidor (ex.: '/educa-pdf'). Use '' se estiver na raiz.
define('BASE_URL', '/educa-pdf');

define('MAX_PDF_MB', 50);
define('UPLOAD_PDF_DIR', __DIR__ . '/../uploads/pdfs/');
define('UPLOAD_CAPA_DIR', __DIR__ . '/../uploads/capas/');
