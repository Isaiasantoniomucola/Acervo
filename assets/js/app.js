// EDUCA PDF · scripts gerais
document.addEventListener('DOMContentLoaded', () => {
  // Mostrar/ocultar senha
  document.querySelectorAll('[data-toggle-senha]').forEach(btn => {
    btn.addEventListener('click', () => {
      const campo = document.querySelector(btn.dataset.toggleSenha);
      const visivel = campo.type === 'text';
      campo.type = visivel ? 'password' : 'text';
      btn.innerHTML = visivel ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });
  });

  // Favoritar / salvar (AJAX)
  document.querySelectorAll('[data-acao-biblioteca]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const dados = new FormData();
      dados.append('livro_id', btn.dataset.livro);
      dados.append('acao', btn.dataset.acaoBiblioteca);
      dados.append('csrf', btn.dataset.csrf);
      try {
        const r = await fetch(btn.dataset.url, { method: 'POST', body: dados });
        const j = await r.json();
        if (j.login) { window.location.href = j.login; return; }
        if (!j.ok) throw new Error(j.erro || 'Erro');
        btn.classList.toggle('active', j.ativo);
        btn.querySelector('i').className = 'bi ' + (btn.dataset.acaoBiblioteca === 'favorito'
          ? (j.ativo ? 'bi-heart-fill' : 'bi-heart') : (j.ativo ? 'bi-bookmark-check-fill' : 'bi-bookmark-plus'));
      } catch (e) { alert('Não foi possível atualizar. Tente novamente.'); }
    });
  });

  // Confirmar ações destrutivas
  document.querySelectorAll('form[data-confirmar]').forEach(f => {
    f.addEventListener('submit', ev => { if (!confirm(f.dataset.confirmar)) ev.preventDefault(); });
  });

  // Pré-visualizar capa
  const inCapa = document.getElementById('inputCapa');
  if (inCapa) inCapa.addEventListener('change', () => {
    const img = document.getElementById('previewCapa');
    if (inCapa.files[0]) { img.src = URL.createObjectURL(inCapa.files[0]); img.classList.remove('d-none'); }
  });
});
