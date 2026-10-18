<?php
require_once __DIR__ . '/../includes/funcoes.php';
exigir_professor();
$pagina = 'prof';
require __DIR__ . '/../includes/header.php';
$atual = basename($_SERVER['SCRIPT_NAME']);
$itens = [
  'index.php' => ['bi-grid-1x2', 'Dashboard'], 'livros.php' => ['bi-book', 'Meus Livros'],
  'livro_form.php' => ['bi-file-earmark-plus', 'Adicionar Livro'], 'categorias.php' => ['bi-folder', 'Categorias'],
  'estatisticas.php' => ['bi-bar-chart', 'Estatísticas'], 'usuarios.php' => ['bi-people', 'Usuários'],
  '../perfil.php' => ['bi-gear', 'Configurações'],
];
?>
<div class="prof-layout">
  <aside class="menu-prof" aria-label="Menu do professor">
    <div class="mb-3 px-2"><strong>EDUCA PDF</strong><br><small class="text-muted-2">Painel do Professor</small></div>
    <?php foreach ($itens as $arq => [$ico, $nome]): ?>
      <a href="<?= url('professor/' . $arq) ?>" class="<?= $atual === $arq && !isset($_GET['id']) ? 'ativo' : '' ?>"><i class="bi <?= $ico ?>"></i><?= $nome ?></a>
    <?php endforeach; ?>
    <a href="<?= url('logout.php') ?>"><i class="bi bi-box-arrow-left"></i>Sair</a>
  </aside>
  <section>
