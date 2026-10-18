<?php
$titulo = 'Explorar Livros'; $pagina = 'livros';
require __DIR__ . '/includes/header.php';
$q = trim($_GET['q'] ?? ''); $cat = (int)($_GET['categoria'] ?? 0);
$ordem = ['recentes' => 'l.criado_em DESC', 'baixados' => 'l.downloads DESC', 'titulo' => 'l.titulo ASC'];
$ord = $ordem[$_GET['ordem'] ?? 'recentes'] ?? $ordem['recentes'];
$porPag = 12; $pg = max(1, (int)($_GET['p'] ?? 1));

$where = ['1=1']; $par = [];
if ($q !== '') { $where[] = '(l.titulo LIKE :q OR l.autor LIKE :q OR l.descricao LIKE :q OR c.nome LIKE :q)'; $par[':q'] = "%$q%"; }
if ($cat) { $where[] = 'l.categoria_id = :c'; $par[':c'] = $cat; }
$w = implode(' AND ', $where);

$st = db()->prepare("SELECT COUNT(*) FROM livros l LEFT JOIN categorias c ON c.id=l.categoria_id WHERE $w");
$st->execute($par); $total = (int)$st->fetchColumn();
$paginas = max(1, (int)ceil($total / $porPag)); $pg = min($pg, $paginas);

$st = db()->prepare("SELECT l.*, c.nome AS categoria FROM livros l LEFT JOIN categorias c ON c.id=l.categoria_id WHERE $w ORDER BY $ord LIMIT $porPag OFFSET " . (($pg - 1) * $porPag));
$st->execute($par); $livros = $st->fetchAll();
$qs = fn($extra = []) => http_build_query(array_merge(['q' => $q, 'categoria' => $cat ?: '', 'ordem' => $_GET['ordem'] ?? ''], $extra));
?>
<h1 class="h3">Explorar Livros</h1>
<p class="text-muted-2">Encontre os melhores livros para o seu aprendizado.</p>

<div class="row g-2 mb-3">
  <?php foreach (categorias() as $c): ?>
    <div class="col-6 col-md-3 col-lg-2"><a class="cat-chip <?= $cat === (int)$c['id'] ? 'ativa' : '' ?>" href="?categoria=<?= $cat === (int)$c['id'] ? '' : $c['id'] ?>"><i class="bi <?= e($c['icone']) ?>"></i><?= e($c['nome']) ?></a></div>
  <?php endforeach; ?>
</div>

<form class="row g-2 mb-4" method="get">
  <input type="hidden" name="categoria" value="<?= $cat ?: '' ?>">
  <div class="col-md-8"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Pesquisar por título, autor, categoria..."></div>
  <div class="col-6 col-md-2">
    <select class="form-select" name="ordem" onchange="this.form.submit()" aria-label="Ordenar">
      <?php foreach (['recentes' => 'Mais recentes', 'baixados' => 'Mais baixados', 'titulo' => 'Título A-Z'] as $k => $v): ?>
        <option value="<?= $k ?>" <?= ($_GET['ordem'] ?? 'recentes') === $k ? 'selected' : '' ?>><?= $v ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-6 col-md-2"><button class="btn btn-educa w-100"><i class="bi bi-search"></i> Buscar</button></div>
</form>

<?php if (!$livros): ?>
  <div class="painel text-center py-5"><i class="bi bi-search fs-1 text-muted-2"></i><p class="mt-2 mb-0">Nenhum livro encontrado. Tente outro termo ou limpe o filtro de categoria.</p></div>
<?php else: ?>
  <p class="text-muted-2"><?= fmt_num($total) ?> resultado(s)</p>
  <div class="row g-3"><?php foreach ($livros as $l) echo card_livro($l); ?></div>
  <?php if ($paginas > 1): ?>
  <nav class="mt-4"><ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $paginas; $i++): ?>
      <li class="page-item <?= $i === $pg ? 'active' : '' ?>"><a class="page-link bg-transparent border-secondary-subtle" href="?<?= $qs(['p' => $i]) ?>"><?= $i ?></a></li>
    <?php endfor; ?>
  </ul></nav>
  <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
