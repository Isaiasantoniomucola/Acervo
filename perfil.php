<?php
require_once __DIR__ . '/includes/funcoes.php'; exigir_login();
$u = usuario();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $nome = trim($_POST['nome'] ?? '');
    if (mb_strlen($nome) < 3) { flash('Informe um nome válido.', 'danger'); redirecionar('perfil.php'); }
    db()->prepare('UPDATE usuarios SET nome=? WHERE id=?')->execute([$nome, $u['id']]);
    $_SESSION['usuario']['nome'] = $nome;
    if (!empty($_POST['nova_senha'])) {
        $r = db()->prepare('SELECT senha FROM usuarios WHERE id=?'); $r->execute([$u['id']]);
        if (!password_verify($_POST['senha_atual'] ?? '', $r->fetchColumn())) { flash('Senha atual incorreta. O nome foi guardado, a senha não.', 'danger'); redirecionar('perfil.php'); }
        if (strlen($_POST['nova_senha']) < 6) { flash('A nova senha precisa de pelo menos 6 caracteres.', 'danger'); redirecionar('perfil.php'); }
        db()->prepare('UPDATE usuarios SET senha=? WHERE id=?')->execute([password_hash($_POST['nova_senha'], PASSWORD_DEFAULT), $u['id']]);
    }
    flash('Perfil atualizado.', 'success'); redirecionar('perfil.php');
}
$titulo = 'Meu perfil'; require __DIR__ . '/includes/header.php'; $u = usuario();
?>
<div class="painel auth-box">
  <h1 class="h4 mb-3">Meu perfil</h1>
  <form method="post"><?= csrf_campo() ?>
    <div class="mb-3"><label class="form-label">E-mail</label><input class="form-control" value="<?= e($u['email']) ?>" disabled></div>
    <div class="mb-3"><label class="form-label" for="nome">Nome</label><input class="form-control" id="nome" name="nome" value="<?= e($u['nome']) ?>" required></div>
    <hr class="border-secondary"><p class="small text-muted-2">Para trocar a senha, preencha os dois campos abaixo.</p>
    <div class="mb-3"><label class="form-label" for="sa">Senha atual</label><input class="form-control" type="password" id="sa" name="senha_atual"></div>
    <div class="mb-3"><label class="form-label" for="ns">Nova senha</label><input class="form-control" type="password" id="ns" name="nova_senha" minlength="6"></div>
    <button class="btn btn-educa w-100">Salvar alterações</button>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
