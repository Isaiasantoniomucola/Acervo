<?php
// Endpoint AJAX: guardar em "Meus Livros" ou favoritar (alterna).
require_once __DIR__ . '/includes/funcoes.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo '{"ok":false}'; exit; }
if (!usuario()) { echo json_encode(['ok' => false, 'login' => url('login.php')]); exit; }
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); echo '{"ok":false,"erro":"csrf"}'; exit; }
$uid = usuario()['id']; $lid = (int)($_POST['livro_id'] ?? 0); $acao = $_POST['acao'] ?? '';
$ex = db()->prepare('SELECT favorito FROM livros l LEFT JOIN biblioteca b ON b.livro_id=l.id AND b.usuario_id=? WHERE l.id=?');
$ex->execute([$uid, $lid]); $r = $ex->fetch();
if (!$r) { echo '{"ok":false,"erro":"livro"}'; exit; }
$salvo = db()->prepare('SELECT favorito FROM biblioteca WHERE usuario_id=? AND livro_id=?'); $salvo->execute([$uid, $lid]); $b = $salvo->fetch();
if ($acao === 'salvar') {
    if ($b) { db()->prepare('DELETE FROM biblioteca WHERE usuario_id=? AND livro_id=?')->execute([$uid, $lid]); $ativo = false; }
    else { db()->prepare('INSERT INTO biblioteca (usuario_id,livro_id) VALUES (?,?)')->execute([$uid, $lid]); $ativo = true; }
} else {
    $novo = $b ? (int)!$b['favorito'] : 1;
    db()->prepare('INSERT INTO biblioteca (usuario_id,livro_id,favorito) VALUES (?,?,?) ON DUPLICATE KEY UPDATE favorito=VALUES(favorito)')->execute([$uid, $lid, $novo]);
    $ativo = (bool)$novo;
}
echo json_encode(['ok' => true, 'ativo' => $ativo]);
