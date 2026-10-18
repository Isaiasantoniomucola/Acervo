<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $p = ''): string { return BASE_URL . '/' . ltrim($p, '/'); }
function redirecionar(string $p): void { header('Location: ' . url($p)); exit; }

function usuario(): ?array { return $_SESSION['usuario'] ?? null; }
function e_professor(): bool { return (usuario()['perfil'] ?? '') === 'professor'; }
function exigir_login(): void {
    if (!usuario()) { flash('Entre na sua conta para continuar.', 'warning'); redirecionar('login.php?volta=' . urlencode($_SERVER['REQUEST_URI'])); }
}
function exigir_professor(): void {
    exigir_login();
    if (!e_professor()) { http_response_code(403); exit('Acesso restrito a professores.'); }
}

function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_campo(): string { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_validar(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Pedido inválido. Volte e tente novamente.'); }
}

function flash(?string $msg = null, string $tipo = 'info') {
    if ($msg !== null) { $_SESSION['flash'][] = [$msg, $tipo]; return null; }
    $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f;
}

function categorias(): array { return db()->query('SELECT * FROM categorias ORDER BY id')->fetchAll(); }

function capa_html(array $l, string $classe = ''): string {
    $cores = [['#1b3fd8','#7b2ff7'],['#b45309','#451a03'],['#047857','#0e7490'],['#7c2d12','#92400e'],['#1d4ed8','#0f766e'],['#6d28d9','#be185d']];
    if (!empty($l['capa']) && is_file(UPLOAD_CAPA_DIR . $l['capa'])) {
        return '<img class="capa ' . $classe . '" src="' . url('uploads/capas/' . rawurlencode($l['capa'])) . '" alt="Capa de ' . e($l['titulo']) . '">';
    }
    $c = $cores[$l['id'] % count($cores)];
    return '<div class="capa capa-gerada ' . $classe . '" style="background:linear-gradient(145deg,' . $c[0] . ',' . $c[1] . ')"><span>' . e($l['titulo']) . '</span></div>';
}

function card_livro(array $l): string {
    ob_start(); ?>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
      <div class="card-livro h-100">
        <a href="<?= url('livro.php?id=' . $l['id']) ?>" class="capa-wrap"><?= capa_html($l) ?></a>
        <div class="p-2 d-flex flex-column flex-grow-1">
          <a class="titulo" href="<?= url('livro.php?id=' . $l['id']) ?>"><?= e($l['titulo']) ?></a>
          <small class="text-muted-2"><?= e($l['subtitulo'] ?: $l['autor']) ?></small>
          <span class="badge-cat mt-1"><?= e($l['categoria'] ?? 'Sem categoria') ?></span>
          <a href="<?= url('livro.php?id=' . $l['id']) ?>" class="btn btn-educa btn-sm mt-auto">Ver PDF</a>
        </div>
      </div>
    </div>
    <?php return ob_get_clean();
}

function fmt_num($n): string { return number_format((int)$n, 0, ',', '.'); }

function pdf_valido(string $tmp): bool {
    $f = new finfo(FILEINFO_MIME_TYPE);
    return $f->file($tmp) === 'application/pdf';
}
function contar_paginas_pdf(string $arq): int {
    $c = @file_get_contents($arq);
    if ($c === false) return 0;
    return preg_match_all('/\/Type\s*\/Page[^s]/', $c, $m) ?: 0;
}
