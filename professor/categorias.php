<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_professor();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $acao = $_POST['acao'] ?? ''; $nome = trim($_POST['nome'] ?? ''); $icone = preg_match('/^bi-[a-z0-9-]+$/', $_POST['icone'] ?? '') ? $_POST['icone'] : 'bi-folder';
    try {
        if ($acao === 'criar' && $nome !== '') { db()->prepare('INSERT INTO categorias (nome,icone) VALUES (?,?)')->execute([$nome, $icone]); flash('Categoria criada.', 'success'); }
        elseif ($acao === 'editar' && $nome !== '') { db()->prepare('UPDATE categorias SET nome=?, icone=? WHERE id=?')->execute([$nome, $icone, (int)$_POST['id']]); flash('Categoria atualizada.', 'success'); }
        elseif ($acao === 'excluir') { db()->prepare('DELETE FROM categorias WHERE id=?')->execute([(int)$_POST['id']]); flash('Categoria excluída. Os livros dela ficam sem categoria.', 'success'); }
    } catch (PDOException $ex) { flash('Já existe uma categoria com esse nome.', 'danger'); }
    redirecionar('professor/categorias.php');
}
$titulo = 'Categorias'; require __DIR__ . '/_topo.php';
$cats = db()->query('SELECT c.*, COUNT(l.id) total FROM categorias c LEFT JOIN livros l ON l.categoria_id=c.id GROUP BY c.id ORDER BY c.id')->fetchAll();
?>
<h1 class="h3 mb-3">Categorias</h1>
<form method="post" class="painel row g-2 mb-3"><?= csrf_campo() ?><input type="hidden" name="acao" value="criar">
  <div class="col-md-6"><input class="form-control" name="nome" placeholder="Nome da nova categoria" required aria-label="Nome"></div>
  <div class="col-md-3"><input class="form-control" name="icone" value="bi-folder" aria-label="Ícone Bootstrap Icons"></div>
  <div class="col-md-3"><button class="btn btn-educa w-100"><i class="bi bi-plus-lg"></i> Criar</button></div>
</form>
<div class="painel">
<?php foreach ($cats as $c): ?>
  <div class="row g-2 align-items-center mb-2">
    <form method="post" class="col-lg-9 row g-2"><?= csrf_campo() ?><input type="hidden" name="acao" value="editar"><input type="hidden" name="id" value="<?= $c['id'] ?>">
      <div class="col-md-6"><input class="form-control" name="nome" value="<?= e($c['nome']) ?>" required aria-label="Nome"></div>
      <div class="col-md-4"><input class="form-control" name="icone" value="<?= e($c['icone']) ?>" aria-label="Ícone"></div>
      <div class="col-md-2"><button class="btn btn-contorno w-100" aria-label="Salvar"><i class="bi bi-check-lg"></i></button></div>
    </form>
    <div class="col-lg-3 d-flex align-items-center gap-2"><span class="badge-cat"><?= (int)$c['total'] ?> livros</span>
      <form method="post" data-confirmar="Excluir a categoria «<?= e($c['nome']) ?>»?"><?= csrf_campo() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button class="btn btn-contorno btn-sm" aria-label="Excluir"><i class="bi bi-trash"></i></button></form></div>
  </div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/_base.php'; ?>
