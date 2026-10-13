```php
<?php
require_once __DIR__ . '/../../helpers/bootstrap.php';

require_admin();

// Proses tambah supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '') {
        flash('Nama supplier wajib diisi.');
        redirect('index.php');
    }

    if ($id > 0) {
        $stmt = db()->prepare(
            'UPDATE suppliers
             SET name = ?, phone = ?, email = ?
             WHERE id = ?'
        );

        $stmt->execute([
            $name,
            $phone !== '' ? $phone : null,
            $email !== '' ? $email : null,
            $id
        ]);

        flash('Supplier berhasil diperbarui.');
    } else {
        $stmt = db()->prepare(
            'INSERT INTO suppliers (name, phone, email)
             VALUES (?, ?, ?)'
        );

        $stmt->execute([
            $name,
            $phone !== '' ? $phone : null,
            $email !== '' ? $email : null
        ]);

        flash('Supplier berhasil ditambahkan.');
    }

    redirect('index.php');
}

// Proses hapus supplier
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id > 0) {
        try {
            $stmt = db()->prepare(
                'DELETE FROM suppliers WHERE id = ?'
            );
            $stmt->execute([$id]);

            flash('Supplier berhasil dihapus.');
        } catch (Throwable $e) {
            flash(
                'Supplier tidak dapat dihapus karena masih digunakan.'
            );
        }
    }

    redirect('index.php');
}

// Ambil daftar supplier
$rows = db()->query(
    'SELECT * FROM suppliers ORDER BY id DESC'
)->fetchAll();

layout_start('Supplier');
?>

<div class="toolbar">
    <div>
        <h1>Supplier</h1>
        <p class="muted">
            Kelola data pemasok barang dalam inventaris.
        </p>
    </div>
</div>

<div class="panel">
    <h2>Tambah Supplier</h2>
    <p class="muted">
        Masukkan informasi pemasok yang ingin didaftarkan.
    </p>

    <form class="inline-form" method="post">
        <input
            type="hidden"
            name="csrf"
            value="<?= e(csrf()) ?>"
        >

        <input type="hidden" name="id" value="0">

        <input
            type="text"
            name="name"
            placeholder="Nama supplier"
            aria-label="Nama supplier"
            required
        >

        <input
            type="text"
            name="phone"
            placeholder="Nomor telepon"
            aria-label="Nomor telepon"
        >

        <input
            type="email"
            name="email"
            placeholder="Email supplier"
            aria-label="Email supplier"
        >

        <button type="submit">Tambah Supplier</button>
    </form>
</div>

<div class="panel" style="margin-top: 20px;">
    <div class="toolbar">
        <div>
            <h2>Daftar Supplier</h2>
            <p class="muted">
                Total <?= count($rows) ?> supplier terdaftar.
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Supplier</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <strong><?= e($row['name']) ?></strong>
                    </td>

                    <td><?= e($row['phone'] ?? '') ?: '-' ?></td>

                    <td><?= e($row['email'] ?? '') ?: '-' ?></td>

                    <td>
                        <a
                            class="danger"
                            href="?delete=<?= (int) $row['id'] ?>"
                            onclick="return confirm('Yakin ingin menghapus supplier ini?')"
                        >
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="4">
                        Belum ada supplier. Tambahkan supplier melalui
                        formulir di atas.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php layout_end(); ?>
```