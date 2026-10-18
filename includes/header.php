<?php require_once __DIR__ . '/funcoes.php'; $u = usuario(); $pagina = $pagina ?? ''; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo ?? 'EDUCA PDF') ?> · EDUCA PDF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-educa sticky-top">
  <div class="container-xl">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
      <i class="bi bi-book-half logo-ico"></i>
      <span><strong>EDUCA PDF</strong><small class="d-block">Repositório Digital de Livros para o Ensino</small></span>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Abrir menu"><i class="bi bi-list"></i></button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link <?= $pagina==='inicio'?'active':'' ?>" href="<?= url('index.php') ?>"><i class="bi bi-house"></i> Início</a></li>
        <li class="nav-item"><a class="nav-link <?= $pagina==='livros'?'active':'' ?>" href="<?= url('livros.php') ?>"><i class="bi bi-book"></i> Explorar Livros</a></li>
        <li class="nav-item"><a class="nav-link <?= $pagina==='meus'?'active':'' ?>" href="<?= url('meus_livros.php') ?>"><i class="bi bi-bookmark-heart"></i> Meus Livros</a></li>
        <?php if ($u && e_professor()): ?>
        <li class="nav-item"><a class="nav-link" href="<?= url('professor/index.php') ?>"><i class="bi bi-people"></i> Área do Professor</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link <?= $pagina==='sobre'?'active':'' ?>" href="<?= url('sobre.php') ?>"><i class="bi bi-info-circle"></i> Sobre</a></li>
      </ul>
      <form class="busca-nav me-lg-3 mb-2 mb-lg-0" action="<?= url('livros.php') ?>" method="get">
        <input class="form-control" type="search" name="q" placeholder="Pesquisar livros, autores, categorias..." aria-label="Pesquisar">
        <button class="btn btn-educa" aria-label="Buscar"><i class="bi bi-search"></i></button>
      </form>
      <?php if ($u): ?>
        <div class="dropdown">
          <a class="btn btn-link text-light dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> <?= e(explode(' ', $u['nome'])[0]) ?></a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
            <li><a class="dropdown-item" href="<?= url('perfil.php') ?>">Meu perfil</a></li>
            <li><a class="dropdown-item" href="<?= url('logout.php') ?>">Sair</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="<?= url('login.php') ?>" class="btn btn-link text-light">Entrar</a>
        <a href="<?= url('registar.php') ?>" class="btn btn-educa ms-lg-2">Registar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<main class="container-xl py-4">
<?php foreach (flash() as [$m, $t]): ?>
  <div class="alert alert-<?= e($t) ?> alert-dismissible fade show" role="alert"><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endforeach; ?>
