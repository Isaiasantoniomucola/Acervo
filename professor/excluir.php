<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_professor();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirecionar('professor/livros.php');
csrf_validar();
$id = (int)($_POST['id'] ?? 0);
$st = db()->prepare('SELECT arquivo_pdf, capa FROM livros WHERE id=?'); $st->execute([$id]); $l = $st->fetch();
if ($l) {
    db()->prepare('DELETE FROM livros WHERE id=?')->execute([$id]);
    if ($l['arquivo_pdf'] !== 'exemplo.pdf') @unlink(UPLOAD_PDF_DIR . basename($l['arquivo_pdf']));
    if ($l['capa']) @unlink(UPLOAD_CAPA_DIR . basename($l['capa']));
    flash('Livro excluído.', 'success');
}
redirecionar('professor/livros.php');
