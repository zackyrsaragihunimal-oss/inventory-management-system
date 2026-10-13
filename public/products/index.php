<WritingBlock id="61582" variant="document">```php'p.name',
    'price' => 'p.price',
    'stock' => 'p.stock',
];
$sort = $allowed[$_GET['sort'] ?? 'name'] ?? 'p.name';
$dir = ($_GET['dir'] ?? 'asc') === 'desc'
    ? 'DESC'
    : 'ASC';
// Menyiapkan filter pencarian.
$where = [];
$args = [];
$search = trim($_GET['q'] ?? '');
if ($search !== '')