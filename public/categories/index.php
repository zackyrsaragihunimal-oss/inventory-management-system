
<?php
require_once __DIR__ . '/../../helpers/bootstrap.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        flash('Nama kategori wajib diisi.');
        redirect('index.php');
    }

    if ($id > 0) {
        $stmt = db()->prepare(
            'UPDATE categories SET name = ? WHERE id = ?'
        );
        $stmt->execute([$name, $id]);

        flash('Kategori berhasil diperbarui.');
    } else {
        $stmt = db()->prepare(
            'INSERT INTO categories (name) VALUES (?)'
        );
        $stmt->execute([$name]);

        flash('Kategori berhasil ditambahkan.');
    }

    redirect('index.php');
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id > 0) {
        try {
            $stmt = db()->prepare(
                'DELETE FROM categories WHERE id = ?'
            );
            $stmt->execute([$id]);

            flash('Kategori berhasil dihapus.');
        } catch (Throwable $e) {
            flash('Kategori tidak dapat dihapus karena masih digunakan.');
        }
    }

    redirect('index.php');
}

$rows = db()->query(
    'SELECT * FROM categories ORDER BY id DESC'
)->fetchAll();

layout_start('Kategori');
?>

<h1>Kategori</h1>

<form class="inline-form" method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
    <input type="hidden" name="id" value="0">

    <input
        type="text"
        name="name"
        placeholder="Nama kategori"
        required
    >

    <button type="submit">Tambah Kategori</button>
</form>

<table>
    <thead>
        <tr>
            <th>Nama Kategori</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= e($row['name']) ?></td>
                <td>
                    <a
                        class="danger"
                        href="?delete=<?= (int) $row['id'] ?>"
                        onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                    >
                        Hapus
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($rows)): ?>
            <tr>
                <td colspan="2">Belum ada kategori.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php layout_end(); ?>
