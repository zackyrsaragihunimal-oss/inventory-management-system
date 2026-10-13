
<?php

session_start();

require_once __DIR__ . '/../config/database.php';

// Mengamankan output HTML agar karakter khusus tidak dieksekusi.
function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

// Membuat token CSRF untuk form.
function csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

// Memeriksa token CSRF ketika form dikirim.
function check_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Permintaan tidak valid.');
    }
}

// Memastikan pengguna sudah login.
function require_login(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: /4inventory-app/public/login.php');
        exit;
    }
}

// Membatasi akses hanya untuk admin.
function require_admin(): void
{
    require_login();

    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Akses admin diperlukan.');
    }
}

// Mengalihkan pengguna ke halaman lain.
function redirect(string $url): never
{
    header("Location: $url");
    exit;
}

// Menyimpan atau mengambil pesan notifikasi.
function flash(?string $msg = null): ?string
{
    if ($msg !== null) {
        $_SESSION['flash'] = $msg;
        return null;
    }

    $x = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $x;
}

// Menentukan alamat dasar aplikasi dari lokasi folder public.
function app_urls(): array
{
    $scriptDir = str_replace(
        '\\',
        '/',
        dirname($_SERVER['SCRIPT_NAME'] ?? '/4inventory-app/public/index.php')
    );

    $publicPos = strpos($scriptDir, '/public');

    if ($publicPos !== false) {
        $appRoot = substr($scriptDir, 0, $publicPos);
    } else {
        $appRoot = '/4inventory-app';
    }

    $appRoot = rtrim($appRoot, '/');

    return [
        'public' => $appRoot . '/public',
        'assets' => $appRoot . '/assets',
    ];
}

// Layout utama yang digunakan semua halaman.
function layout_start(string $title): void
{
    require_login();

    $u = $_SESSION['user'];
    $m = flash();
    $urls = app_urls();
    $public = $urls['public'];
    $assets = $urls['assets'];

    echo '<!doctype html>';
    echo '<html lang="id">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($title) . ' · Stockroom</title>';
    echo '<link rel="stylesheet" href="' . e($assets) . '/css/app.css">';
    echo '</head>';
    echo '<body>';

    echo '<header>';
    echo '<strong>Stockroom</strong>';
    echo '<nav>';

    echo '<a href="' . e($public) . '/dashboard.php">Dashboard</a>';
    echo '<a href="' . e($public) . '/products/index.php">Produk</a>';
    echo '<a href="' . e($public) . '/stock/create.php">Stok</a>';
    echo '<a href="' . e($public) . '/reports/index.php">Laporan</a>';

    if (($u['role'] ?? '') === 'admin') {
        echo '<a href="' . e($public) . '/categories/index.php">Kategori</a>';
        echo '<a href="' . e($public) . '/suppliers/index.php">Supplier</a>';
        echo '<a href="' . e($public) . '/users/index.php">Users</a>';
    }

    echo '<a href="' . e($public) . '/logout.php">Keluar</a>';
    echo '</nav>';
    echo '</header>';

    echo '<main>';
    echo '<p class="muted">Masuk sebagai ' . e($u['name'])
        . ' · ' . e($u['role']) . '</p>';

    if ($m) {
        echo '<div class="flash">' . e($m) . '</div>';
    }
}

// Penutup layout utama.
function layout_end(): void
{
    echo '<footer>Inventory Management System · Web 4</footer>';
    echo '</main>';
    echo '</body>';
    echo '</html>';
}

