<?php
require_once __DIR__ . '/includes/funcoes.php';
exigir_login();
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT * FROM livros WHERE id=?'); $st->execute([$id]); $l = $st->fetch();
if (!$l) { http_response_code(404); exit('Livro não encontrado.'); }
$titulo = 'Lendo: ' . $l['titulo'];
$scripts_extra = '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>'
  . '<script>window.LEITOR=' . json_encode(['id' => $id, 'url' => url('baixar.php?id=' . $id . '&inline=1')]) . ';</script>'
  . '<script src="' . url('assets/js/leitor.js') . '"></script>';
require __DIR__ . '/includes/header.php';
?>
<div class="leitor-barra">
  <div><a href="<?= url('livro.php?id=' . $id) ?>" class="me-2" aria-label="Voltar"><i class="bi bi-chevron-left"></i></a><strong><?= e($l['titulo']) ?></strong></div>
  <div class="d-flex align-items-center gap-2">
    <button id="prev" class="btn btn-contorno btn-sm" aria-label="Página anterior"><i class="bi bi-chevron-left"></i></button>
    <span id="infoPag" class="small">A carregar...</span>
    <button id="next" class="btn btn-contorno btn-sm" aria-label="Próxima página"><i class="bi bi-chevron-right"></i></button>
    <button id="zoomMenos" class="btn btn-contorno btn-sm" aria-label="Diminuir"><i class="bi bi-zoom-out"></i></button>
    <button id="zoomMais" class="btn btn-contorno btn-sm" aria-label="Aumentar"><i class="bi bi-zoom-in"></i></button>
    <button id="tela" class="btn btn-contorno btn-sm" aria-label="Ecrã inteiro"><i class="bi bi-arrows-fullscreen"></i></button>
    <a class="btn btn-educa btn-sm" href="<?= url('baixar.php?id=' . $id) ?>"><i class="bi bi-download"></i></a>
  </div>
</div>
<div class="leitor-area" id="leitorArea"><canvas id="pdfCanvas"></canvas></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
