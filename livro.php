<?php
require_once __DIR__ . '/includes/funcoes.php';
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT l.*, c.nome AS categoria FROM livros l LEFT JOIN categorias c ON c.id=l.categoria_id WHERE l.id=?');
$st->execute([$id]); $l = $st->fetch();
if (!$l) { http_response_code(404); $titulo = 'Livro não encontrado'; require __DIR__ . '/includes/header.php'; echo '<div class="painel text-center">Livro não encontrado. <a href="' . url('livros.php') . '">Voltar aos livros</a></div>'; require __DIR__ . '/includes/footer.php'; exit; }

$u = usuario();
// Avaliar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nota'])) {
    exigir_login(); csrf_validar();
    $nota = max(1, min(5, (int)$_POST['nota']));
    db()->prepare('INSERT INTO avaliacoes (livro_id,usuario_id,nota,comentario) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE nota=VALUES(nota), comentario=VALUES(comentario)')
        ->execute([$id, $u['id'], $nota, mb_substr(trim($_POST['comentario'] ?? ''), 0, 500)]);
    flash('Avaliação guardada.', 'success'); redirecionar('livro.php?id=' . $id . '#avaliacoes');
}
$av = db()->prepare('SELECT AVG(nota) m, COUNT(*) t FROM avaliacoes WHERE livro_id=?'); $av->execute([$id]); $av = $av->fetch();
$coms = db()->prepare('SELECT a.*, u.nome FROM avaliacoes a JOIN usuarios u ON u.id=a.usuario_id WHERE livro_id=? ORDER BY a.criado_em DESC LIMIT 20'); $coms->execute([$id]); $coms = $coms->fetchAll();
$bib = ['favorito' => 0, 'salvo' => 0];
if ($u) { $b = db()->prepare('SELECT favorito FROM biblioteca WHERE usuario_id=? AND livro_id=?'); $b->execute([$u['id'], $id]); $r = $b->fetch(); if ($r) $bib = ['favorito' => (int)$r['favorito'], 'salvo' => 1]; }

$titulo = $l['titulo']; $pagina = 'livros';
require __DIR__ . '/includes/header.php';
$media = round((float)$av['m'], 1);
?>
<a href="<?= url('livros.php') ?>" class="d-inline-block mb-3"><i class="bi bi-arrow-left"></i> Voltar</a>
<div class="row g-4 livro-detalhe">
  <div class="col-md-4 col-lg-3"><?= capa_html($l) ?></div>
  <div class="col-md-8 col-lg-9">
    <h1 class="h3 mb-0"><?= e($l['titulo']) ?></h1>
    <p class="text-muted-2"><?= e($l['subtitulo']) ?></p>
    <p class="mb-1">Autor: <span class="text-info"><?= e($l['autor']) ?></span></p>
    <span class="badge-cat"><?= e($l['categoria'] ?? 'Sem categoria') ?></span>
    <div class="d-flex flex-wrap gap-3 my-3 text-muted-2">
      <span><i class="bi bi-file-earmark"></i> <?= (int)$l['paginas'] ?> páginas</span>
      <span><i class="bi bi-filetype-pdf"></i> PDF · <?= e($l['tamanho_mb']) ?> MB</span>
      <span><i class="bi bi-download"></i> <?= fmt_num($l['downloads']) ?> downloads</span>
    </div>
    <div class="estrelas mb-3">
      <?php for ($i = 1; $i <= 5; $i++) echo '<i class="bi ' . ($i <= round($media) ? 'bi-star-fill' : 'bi-star') . '"></i> '; ?>
      <span class="text-light"><?= $av['t'] ? $media : '—' ?></span> <small class="text-muted-2">(<?= (int)$av['t'] ?> avaliações)</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-educa" href="<?= url('baixar.php?id=' . $id) ?>"><i class="bi bi-download"></i> Baixar PDF</a>
      <a class="btn btn-contorno" href="<?= url('ler.php?id=' . $id) ?>"><i class="bi bi-book"></i> Ler online</a>
      <button class="btn btn-contorno <?= $bib['salvo'] ? 'active' : '' ?>" data-acao-biblioteca="salvar" data-livro="<?= $id ?>" data-url="<?= url('biblioteca.php') ?>" data-csrf="<?= csrf() ?>" aria-label="Guardar em Meus Livros"><i class="bi <?= $bib['salvo'] ? 'bi-bookmark-check-fill' : 'bi-bookmark-plus' ?>"></i></button>
      <button class="btn btn-contorno <?= $bib['favorito'] ? 'active' : '' ?>" data-acao-biblioteca="favorito" data-livro="<?= $id ?>" data-url="<?= url('biblioteca.php') ?>" data-csrf="<?= csrf() ?>" aria-label="Favoritar"><i class="bi <?= $bib['favorito'] ? 'bi-heart-fill' : 'bi-heart' ?>"></i></button>
    </div>
  </div>
</div>

<ul class="nav nav-tabs mt-4" role="tablist">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#t-desc">Descrição</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-cont">Conteúdo</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t-av" id="avaliacoes">Avaliações</button></li>
</ul>
<div class="tab-content painel border-top-0 rounded-top-0">
  <div class="tab-pane fade show active" id="t-desc"><?= nl2br(e($l['descricao'] ?: 'Sem descrição.')) ?></div>
  <div class="tab-pane fade" id="t-cont">
    <?php $itens = array_filter(array_map('trim', preg_split('/\R|\\\\n/', (string)$l['conteudo']))); ?>
    <?php if ($itens): ?><ul class="mb-0"><?php foreach ($itens as $i) echo '<li>' . e($i) . '</li>'; ?></ul><?php else: ?>Sem índice disponível.<?php endif; ?>
  </div>
  <div class="tab-pane fade" id="t-av">
    <?php if ($u): ?>
    <form method="post" class="mb-4">
      <?= csrf_campo() ?>
      <div class="estrelas-input">
        <?php for ($i = 5; $i >= 1; $i--): ?><input type="radio" name="nota" id="n<?= $i ?>" value="<?= $i ?>" required><label for="n<?= $i ?>" title="<?= $i ?> estrelas"><i class="bi bi-star-fill"></i></label><?php endfor; ?>
      </div>
      <textarea class="form-control my-2" name="comentario" rows="2" maxlength="500" placeholder="Comentário (opcional)"></textarea>
      <button class="btn btn-educa btn-sm">Enviar avaliação</button>
    </form>
    <?php else: ?><p><a href="<?= url('login.php') ?>">Entre</a> para avaliar este livro.</p><?php endif; ?>
    <?php foreach ($coms as $c): ?>
      <div class="border-top border-secondary-subtle py-2"><strong><?= e($c['nome']) ?></strong> <span class="estrelas"><?= str_repeat('★', (int)$c['nota']) ?></span><br><span class="text-muted-2"><?= e($c['comentario']) ?></span></div>
    <?php endforeach; if (!$coms) echo '<p class="text-muted-2 mb-0">Ainda sem avaliações. Seja o primeiro.</p>'; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
