<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_professor();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar(); $uid = (int)($_POST['id'] ?? 0);
    if ($uid === usuario()['id']) { flash('Não pode alterar a sua própria conta aqui.', 'warning'); }
    elseif (($_POST['acao'] ?? '') === 'perfil') { db()->prepare('UPDATE usuarios SET perfil=? WHERE id=?')->execute([($_POST['perfil'] ?? '') === 'professor' ? 'professor' : 'aluno', $uid]); flash('Perfil atualizado.', 'success'); }
    elseif (($_POST['acao'] ?? '') === 'excluir') { db()->prepare('DELETE FROM usuarios WHERE id=?')->execute([$uid]); flash('Utilizador removido.', 'success'); }
    redirecionar('professor/usuarios.php');
}
$titulo = 'Usuários'; require __DIR__ . '/_topo.php';
$us = db()->query('SELECT * FROM usuarios ORDER BY perfil DESC, nome')->fetchAll();
?>
<h1 class="h3 mb-3">Usuários</h1>
<div class="painel table-responsive"><table class="table table-educa align-middle mb-0">
  <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Registo</th><th></th></tr></thead><tbody>
  <?php foreach ($us as $x): $eu = (int)$x['id'] === usuario()['id']; ?>
  <tr><td><?= e($x['nome']) ?></td><td><?= e($x['email']) ?></td>
    <td><form method="post" class="d-flex gap-1"><?= csrf_campo() ?><input type="hidden" name="acao" value="perfil"><input type="hidden" name="id" value="<?= $x['id'] ?>">
      <select class="form-select form-select-sm" name="perfil" <?= $eu ? 'disabled' : '' ?> aria-label="Perfil"><option value="aluno" <?= $x['perfil'] === 'aluno' ? 'selected' : '' ?>>Aluno</option><option value="professor" <?= $x['perfil'] === 'professor' ? 'selected' : '' ?>>Professor</option></select>
      <?php if (!$eu): ?><button class="btn btn-contorno btn-sm" aria-label="Salvar"><i class="bi bi-check-lg"></i></button><?php endif; ?></form></td>
    <td><?= date('d/m/Y', strtotime($x['criado_em'])) ?></td>
    <td><?php if (!$eu): ?><form method="post" data-confirmar="Remover <?= e($x['nome']) ?>?"><?= csrf_campo() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= $x['id'] ?>"><button class="btn btn-contorno btn-sm" aria-label="Remover"><i class="bi bi-trash"></i></button></form><?php endif; ?></td></tr>
  <?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/_base.php'; ?>
