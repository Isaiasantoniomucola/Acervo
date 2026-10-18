<?php
$titulo = 'Sobre'; $pagina = 'sobre'; require __DIR__ . '/includes/header.php';
$n = db()->query('SELECT (SELECT COUNT(*) FROM livros) l, (SELECT COUNT(*) FROM usuarios WHERE perfil="professor") p')->fetch();
?>
<div class="painel">
  <h1 class="h3">Sobre o EDUCA PDF</h1>
  <p>O EDUCA PDF é um repositório digital de livros em PDF para o ensino. Professores publicam e organizam o acervo; alunos pesquisam, leem no navegador e baixam o que precisam.</p>
  <p class="mb-0"><strong><?= fmt_num($n['l']) ?></strong> livros · <strong><?= fmt_num($n['p']) ?></strong> professores.</p>
  <h2 class="h5 mt-4">Como acessar</h2>
  <ol><li>Clique em um livro da lista ou categoria.</li><li>Faça o download do PDF.</li><li>Leia diretamente no navegador ou no seu dispositivo.</li></ol>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
