<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_professor();
$id = (int)($_GET['id'] ?? 0); $l = null;
if ($id) { $st = db()->prepare('SELECT * FROM livros WHERE id=?'); $st->execute([$id]); $l = $st->fetch(); if (!$l) { http_response_code(404); exit('Livro não encontrado.'); } }
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $d = [
        'titulo' => trim($_POST['titulo'] ?? ''), 'subtitulo' => trim($_POST['subtitulo'] ?? ''), 'autor' => trim($_POST['autor'] ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''), 'conteudo' => trim($_POST['conteudo'] ?? ''),
        'categoria_id' => (int)($_POST['categoria_id'] ?? 0) ?: null, 'paginas' => max(0, (int)($_POST['paginas'] ?? 0)),
        'destaque' => isset($_POST['destaque']) ? 1 : 0,
    ];
    if ($d['titulo'] === '') $erros[] = 'O título é obrigatório.';
    if ($d['autor'] === '') $erros[] = 'O autor é obrigatório.';

    $pdfNome = $l['arquivo_pdf'] ?? null; $tam = $l['tamanho_mb'] ?? 0; $capaNome = $l['capa'] ?? null;
    $f = $_FILES['pdf'] ?? null;
    if ($f && $f['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($f['error'] !== UPLOAD_ERR_OK) $erros[] = 'Falha no envio do PDF (código ' . $f['error'] . '). Verifique upload_max_filesize no php.ini.';
        elseif ($f['size'] > MAX_PDF_MB * 1048576) $erros[] = 'O PDF excede ' . MAX_PDF_MB . ' MB.';
        elseif (!pdf_valido($f['tmp_name'])) $erros[] = 'O ficheiro enviado não é um PDF válido.';
        else { $novoPdf = bin2hex(random_bytes(12)) . '.pdf'; $tamNovo = round($f['size'] / 1048576, 1); }
    } elseif (!$l) $erros[] = 'Selecione o ficheiro PDF do livro.';

    $c = $_FILES['capa'] ?? null; $novaCapa = null;
    if ($c && $c['error'] === UPLOAD_ERR_OK) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($c['tmp_name']);
        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
        if (!$ext || $c['size'] > 3 * 1048576) $erros[] = 'A capa deve ser JPG, PNG ou WebP até 3 MB.';
        else $novaCapa = bin2hex(random_bytes(12)) . '.' . $ext;
    }

    if (!$erros) {
        if (isset($novoPdf)) {
            move_uploaded_file($f['tmp_name'], UPLOAD_PDF_DIR . $novoPdf);
            if ($l && $l['arquivo_pdf'] !== 'exemplo.pdf') @unlink(UPLOAD_PDF_DIR . basename($l['arquivo_pdf']));
            $pdfNome = $novoPdf; $tam = $tamNovo;
            if (!$d['paginas']) $d['paginas'] = contar_paginas_pdf(UPLOAD_PDF_DIR . $novoPdf);
        }
        if ($novaCapa) {
            move_uploaded_file($c['tmp_name'], UPLOAD_CAPA_DIR . $novaCapa);
            if ($l && $l['capa']) @unlink(UPLOAD_CAPA_DIR . basename($l['capa']));
            $capaNome = $novaCapa;
        }
        if ($l) {
            db()->prepare('UPDATE livros SET titulo=?,subtitulo=?,autor=?,descricao=?,conteudo=?,categoria_id=?,paginas=?,destaque=?,arquivo_pdf=?,tamanho_mb=?,capa=? WHERE id=?')
              ->execute([$d['titulo'], $d['subtitulo'], $d['autor'], $d['descricao'], $d['conteudo'], $d['categoria_id'], $d['paginas'], $d['destaque'], $pdfNome, $tam, $capaNome, $id]);
            flash('Livro atualizado.', 'success');
        } else {
            db()->prepare('INSERT INTO livros (titulo,subtitulo,autor,descricao,conteudo,categoria_id,paginas,destaque,arquivo_pdf,tamanho_mb,capa,professor_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
              ->execute([$d['titulo'], $d['subtitulo'], $d['autor'], $d['descricao'], $d['conteudo'], $d['categoria_id'], $d['paginas'], $d['destaque'], $pdfNome, $tam, $capaNome, usuario()['id']]);
            flash('Livro adicionado ao acervo.', 'success');
        }
        redirecionar('professor/livros.php');
    }
    $l = array_merge($l ?? [], $d);
}
$titulo = $id ? 'Editar livro' : 'Adicionar livro';
require __DIR__ . '/_topo.php';
$v = fn($k) => e($l[$k] ?? '');
?>
<h1 class="h3 mb-3"><?= $titulo ?></h1>
<?php foreach ($erros as $er): ?><div class="alert alert-danger py-2"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" enctype="multipart/form-data" class="painel">
  <?= csrf_campo() ?>
  <div class="row g-3">
    <div class="col-md-8"><label class="form-label" for="titulo">Título *</label><input class="form-control" id="titulo" name="titulo" value="<?= $v('titulo') ?>" required></div>
    <div class="col-md-4"><label class="form-label" for="categoria_id">Categoria</label>
      <select class="form-select" id="categoria_id" name="categoria_id"><option value="">Sem categoria</option>
        <?php foreach (categorias() as $c): ?><option value="<?= $c['id'] ?>" <?= (int)($l['categoria_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-6"><label class="form-label" for="subtitulo">Subtítulo</label><input class="form-control" id="subtitulo" name="subtitulo" value="<?= $v('subtitulo') ?>"></div>
    <div class="col-md-6"><label class="form-label" for="autor">Autor *</label><input class="form-control" id="autor" name="autor" value="<?= $v('autor') ?>" required></div>
    <div class="col-12"><label class="form-label" for="descricao">Descrição</label><textarea class="form-control" id="descricao" name="descricao" rows="3"><?= $v('descricao') ?></textarea></div>
    <div class="col-12"><label class="form-label" for="conteudo">Conteúdo (um tópico por linha)</label><textarea class="form-control" id="conteudo" name="conteudo" rows="4"><?= str_replace('\n', "\n", $v('conteudo')) ?></textarea></div>
    <div class="col-md-6"><label class="form-label" for="pdf">Ficheiro PDF <?= $id ? '(deixe vazio para manter o atual)' : '*' ?></label><input class="form-control" type="file" id="pdf" name="pdf" accept="application/pdf"></div>
    <div class="col-md-6"><label class="form-label" for="inputCapa">Capa (JPG, PNG ou WebP)</label><input class="form-control" type="file" id="inputCapa" name="capa" accept="image/jpeg,image/png,image/webp">
      <img id="previewCapa" class="d-none mt-2 rounded" style="max-height:120px" alt="Pré-visualização da capa"></div>
    <div class="col-md-4"><label class="form-label" for="paginas">Páginas (vazio = detetar)</label><input class="form-control" type="number" min="0" id="paginas" name="paginas" value="<?= (int)($l['paginas'] ?? 0) ?: '' ?>"></div>
    <div class="col-md-4 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" id="destaque" name="destaque" <?= !empty($l['destaque']) ? 'checked' : '' ?>><label class="form-check-label" for="destaque">Livro em destaque</label></div></div>
  </div>
  <div class="mt-4 d-flex gap-2"><button class="btn btn-educa"><i class="bi bi-check-lg"></i> Salvar livro</button><a class="btn btn-contorno" href="<?= url('professor/livros.php') ?>">Cancelar</a></div>
</form>
<?php require __DIR__ . '/_base.php'; ?>
