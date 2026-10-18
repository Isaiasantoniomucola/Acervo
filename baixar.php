<?php
// Entrega o PDF (download ou leitura inline) e regista o download.
require_once __DIR__ . '/includes/funcoes.php';
exigir_login();
$id = (int)($_GET['id'] ?? 0); $inline = isset($_GET['inline']);
$st = db()->prepare('SELECT * FROM livros WHERE id=?'); $st->execute([$id]); $l = $st->fetch();
$arq = $l ? UPLOAD_PDF_DIR . basename($l['arquivo_pdf']) : '';
if (!$l || !is_file($arq)) { http_response_code(404); exit('Arquivo PDF não encontrado.'); }
if (!$inline) {
    db()->prepare('UPDATE livros SET downloads=downloads+1 WHERE id=?')->execute([$id]);
    db()->prepare('INSERT INTO downloads_log (livro_id,usuario_id) VALUES (?,?)')->execute([$id, usuario()['id']]);
}
$nome = preg_replace('/[^A-Za-z0-9_\- ]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $l['titulo'])) . '.pdf';
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($arq));
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $nome . '"');
header('X-Content-Type-Options: nosniff');
readfile($arq); exit;
