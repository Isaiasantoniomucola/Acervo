// Leitor de PDF com PDF.js
(function () {
  const cfg = window.LEITOR;
  const lib = window['pdfjs-dist/build/pdf'];
  lib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
  const canvas = document.getElementById('pdfCanvas');
  const ctx = canvas.getContext('2d');
  const infoPag = document.getElementById('infoPag');
  const area = document.getElementById('leitorArea');
  let pdf = null, pag = 1, zoom = 1.2, ocupado = false, pendente = null;

  async function render(n) {
    ocupado = true;
    const page = await pdf.getPage(n);
    const larguraBase = page.getViewport({ scale: 1 }).width;
    const escala = Math.min(zoom, zoom * (area.clientWidth - 32) / larguraBase) || zoom;
    const vp = page.getViewport({ scale: escala });
    const dpr = window.devicePixelRatio || 1;
    canvas.width = vp.width * dpr; canvas.height = vp.height * dpr;
    canvas.style.width = vp.width + 'px'; canvas.style.height = vp.height + 'px';
    await page.render({ canvasContext: ctx, viewport: vp, transform: [dpr, 0, 0, dpr, 0, 0] }).promise;
    ocupado = false;
    infoPag.textContent = 'Página ' + n + ' de ' + pdf.numPages;
    localStorage.setItem('pag_' + cfg.id, n);
    if (pendente !== null) { const p = pendente; pendente = null; render(p); }
  }
  function ir(n) {
    if (!pdf || n < 1 || n > pdf.numPages) return;
    pag = n; ocupado ? (pendente = n) : render(n);
  }
  lib.getDocument(cfg.url).promise.then(d => {
    pdf = d;
    const salva = parseInt(localStorage.getItem('pag_' + cfg.id) || '1', 10);
    pag = salva >= 1 && salva <= d.numPages ? salva : 1;
    render(pag);
  }).catch(() => { infoPag.textContent = 'Não foi possível abrir este PDF.'; });

  document.getElementById('prev').onclick = () => ir(pag - 1);
  document.getElementById('next').onclick = () => ir(pag + 1);
  document.getElementById('zoomMais').onclick = () => { zoom = Math.min(3, zoom + .2); ir(pag); };
  document.getElementById('zoomMenos').onclick = () => { zoom = Math.max(.5, zoom - .2); ir(pag); };
  document.getElementById('tela').onclick = () => area.requestFullscreen && area.requestFullscreen();
  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') ir(pag + 1);
    if (e.key === 'ArrowLeft') ir(pag - 1);
  });
})();
