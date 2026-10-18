<?php
require_once __DIR__ . '/includes/funcoes.php';
if (usuario()) redirecionar('index.php');
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $id = trim($_POST['email'] ?? '');
    $st = db()->prepare('SELECT * FROM usuarios WHERE email = ? OR nome = ? LIMIT 1');
    $st->execute([$id, $id]); $u = $st->fetch();
    if ($u && password_verify($_POST['senha'] ?? '', $u['senha'])) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = ['id' => (int)$u['id'], 'nome' => $u['nome'], 'email' => $u['email'], 'perfil' => $u['perfil']];
        $volta = $_GET['volta'] ?? '';
        if ($volta && str_starts_with($volta, BASE_URL . '/') && !str_contains($volta, '//')) { header('Location: ' . $volta); exit; }
        redirecionar($u['perfil'] === 'professor' ? 'professor/index.php' : 'index.php');
    }
    $erro = 'E-mail ou senha incorretos. Verifique os dados e tente de novo.';
}
$titulo = 'Entrar'; require __DIR__ . '/includes/header.php';
?>
<div class="painel auth-box">
  <h1 class="h4 text-center">Bem-vindo ao EDUCA PDF</h1>
  <p class="text-center text-muted-2">Acesse sua conta para continuar</p>
  <?php if ($erro): ?><div class="alert alert-danger"><?= e($erro) ?></div><?php endif; ?>
  <form method="post" novalidate>
    <?= csrf_campo() ?>
    <div class="mb-3"><label class="form-label" for="email">E-mail ou usuário</label><input class="form-control" id="email" name="email" required autofocus value="<?= e($_POST['email'] ?? '') ?>"></div>
    <div class="mb-3"><label class="form-label" for="senha">Senha</label>
      <div class="input-group"><input class="form-control" type="password" id="senha" name="senha" required>
      <button class="btn btn-contorno" type="button" data-toggle-senha="#senha" aria-label="Mostrar senha"><i class="bi bi-eye"></i></button></div></div>
    <button class="btn btn-educa w-100">Entrar</button>
  </form>
  <p class="text-center mt-3 mb-0 small">Ainda não tem uma conta? <a href="<?= url('registar.php') ?>">Registe-se</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
