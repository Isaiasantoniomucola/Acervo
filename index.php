<?php
$titulo = 'Início'; $pagina = 'inicio';
require __DIR__ . '/includes/header.php';
$cats = categorias();
$destaques = db()->query('SELECT l.*, c.nome AS categoria FROM livros l LEFT JOIN categorias c ON c.id=l.categoria_id ORDER BY l.destaque DESC, l.downloads DESC LIMIT 6')->fetchAll();
$total = db()->query('SELECT COUNT(*) FROM livros')->fetchColumn();
?>
<section class="hero mb-4">
  <h1 class="h2">Encontre os melhores livros para o seu aprendizado.</h1>
  <p class="text-muted-2 mb-3"><?= fmt_num($total) ?> livros em PDF, organizados por disciplina e partilhados por professores.</p>
  <form action="<?= url('livros.php') ?>" class="busca-nav" style="max-width:560px">
    <input class="form-control form-control-lg" name="q" placeholder="Pesquise por título, autor ou categoria">
    <button class="btn btn-educa px-4"><i class="bi bi-search"></i></button>
  </form>
</section>

<div class="titulo-secao"><h2>Categorias</h2><a href="<?= url('livros.php') ?>">Ver todas</a></div>
<div class="row g-2">
<?php foreach ($cats as $c): ?>
  <div class="col-6 col-md-4 col-lg-3"><a class="cat-chip" href="<?= url('livros.php?categoria=' . $c['id']) ?>"><i class="bi <?= e($c['icone']) ?>"></i><?= e($c['nome']) ?></a></div>
<?php endforeach; ?>
</div>

<div class="titulo-secao"><h2>Livros em Destaque</h2><a href="<?= url('livros.php') ?>">Ver todos</a></div>
<div class="row g-3"><?php foreach ($destaques as $l) echo card_livro($l); ?></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
