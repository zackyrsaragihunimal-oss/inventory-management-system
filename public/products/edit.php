
<?php
require_once __DIR__ . '/../../helpers/bootstrap.php';

require_admin();

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = max(0, (float) ($_POST['price'] ?? 0));
    $minimumStock = max(0, (int) ($_POST['minimum_stock'] ?? 5));

    if ($id > 0) {
        $stmt = db()->prepare(
            'UPDATE products
             SET category_id = ?, sku = ?, name = ?,
                 price = ?, minimum_stock = ?
             WHERE id = ?'
        );

        $stmt->execute([
            $categoryId,
            $sku,
            $name,
            $price,
            $minimumStock,
            $id
        ]);
    } else {
        $stmt = db()->prepare(
            'INSERT INTO products
             (category_id, sku, name, price, minimum_stock)
             VALUES (?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $categoryId,
            $sku,
            $name,
            $price,
            $minimumStock
        ]);
    }

    flash('Produk berhasil disimpan.');
    redirect('index.php');
}

$product = [];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch() ?: [];

    if (empty($product)) {
        flash('Produk tidak ditemukan.');
        redirect('index.php');
    }
}

$categories = db()->query(
    'SELECT * FROM categories ORDER BY name'
)->fetchAll();

layout_start('Produk');
?>

<h1><?= $id > 0 ? 'Edit' : 'Tambah' ?> Produk</h1>

<form class="panel narrow" method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

    <label>
        SKU
        <input
            type="text"
            name="sku"
            required
            value="<?= e($product['sku'] ?? '') ?>"
        >
    </label>

    <label>
        Nama
        <input
            type="text"
            name="name"
            required
            value="<?= e($product['name'] ?? '') ?>"
        >
    </label>

    <label>
        Kategori
        <select name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= (int) $category['id'] ?>"
                    <?= (int) $category['id'] === (int) ($product['category_id'] ?? 0) ? 'selected' : '' ?>
                >
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Harga
        <input
            type="number"
            name="price"
            min="0"
            step="0.01"
            required
            value="<?= e($product['price'] ?? 0) ?>"
        >
    </label>

    <label>
        Minimum stok
        <input
            type="number"
            name="minimum_stock"
            min="0"
            required
            value="<?= e($product['minimum_stock'] ?? 5) ?>"
        >
    </label>

    <button type="submit">Simpan</button>
</form>

<?php layout_end(); ?>
