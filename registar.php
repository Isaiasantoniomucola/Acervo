<?php
require_once __DIR__ . '/includes/funcoes.php';
if (usuario()) redirecionar('index.php');
$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $nome = trim($_POST['nome'] ?? ''); $email = strtolower(trim($_POST['email'] ?? '')); $senha = $_POST['senha'] ?? '';
    if (mb_strlen($nome) < 3) $erros[] = 'Informe o nome completo.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    if (strlen($senha) < 6) $erros[] = 'A senha precisa de pelo menos 6 caracteres.';
    if ($senha !== ($_POST['senha2'] ?? '')) $erros[] = 'As senhas não coincidem.';
    if (!$erros) {
        $ex = db()->prepare('SELECT 1 FROM usuarios WHERE email=?'); $ex->execute([$email]);
        if ($ex->fetch()) $erros[] = 'Este e-mail já está registado.';
    }
    if (!$erros) {
        // Contas novas são sempre "aluno"; um professor existente promove-as em Usuários.
        db()->prepare('INSERT INTO usuarios (nome,email,senha,perfil) VALUES (?,?,?,"aluno")')->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
        flash('Conta criada. Já pode entrar.', 'success'); redirecionar('login.php');
    }
}
$titulo = 'Registar'; require __DIR__ . '/includes/header.php';
?>
<div class="painel auth-box">
  <h1 class="h4 text-center mb-3">Criar conta</h1>
  <?php foreach ($erros as $er): ?><div class="alert alert-danger py-2"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post" novalidate>
    <?= csrf_campo() ?>
    <div class="mb-3"><label class="form-label" for="nome">Nome completo</label><input class="form-control" id="nome" name="nome" required value="<?= e($_POST['nome'] ?? '') ?>"></div>
    <div class="mb-3"><label class="form-label" for="email">E-mail</label><input class="form-control" type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></div>
    <div class="mb-3"><label class="form-label" for="senha">Senha</label><input class="form-control" type="password" id="senha" name="senha" required minlength="6"></div>
    <div class="mb-3"><label class="form-label" for="senha2">Repita a senha</label><input class="form-control" type="password" id="senha2" name="senha2" required></div>
    <button class="btn btn-educa w-100">Criar conta</button>
  </form>
  <p class="text-center mt-3 mb-0 small">Já tem conta? <a href="<?= url('login.php') ?>">Entrar</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
