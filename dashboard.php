<?php
session_start();
include 'config/db.php';

$message = "";
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// --- CREATE / UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $category = htmlspecialchars(trim($_POST['category']));

    if ($name === "") {
        $message = "<div class='alert'>⚠️ Nama habit tidak boleh kosong!</div>";
    } else {
        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE habits SET name=?, description=?, category=? WHERE id=?");
            $stmt->execute([$name, $description, $category, $_POST['id']]);
            $message = "<div class='alert success'>✅ Habit berhasil diperbarui!</div>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO habits (name, description, category, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $description, $category]);
            $message = "<div class='alert success'>✅ Habit berhasil ditambahkan!</div>";
        }
    }
}

// --- DELETE ---
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM habits WHERE id=?");
    $stmt->execute([$id]);
    $message = "<div class='alert warning'>Habit berhasil dihapus!</div>";
}

// --- SEARCH & PAGINATION ---
$keyword = $_GET['keyword'] ?? "";
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

$whereClause = "";
$params = [];

if ($keyword) {
    $whereClause = "WHERE name LIKE ? OR category LIKE ?";
    $params = ["%$keyword%", "%$keyword%"];
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM habits $whereClause");
$countStmt->execute($params);
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$query = "SELECT * FROM habits $whereClause ORDER BY created_at DESC LIMIT $start, $limit";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$habits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- EDIT (prefill) ---
$editHabit = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM habits WHERE id=?");
    $stmt->execute([$id]);
    $editHabit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard - Habit Tracker</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
    body {
        transition: background 0.4s, color 0.4s;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
    }

    #darkModeToggle {
        background-color: #f6d8e0;
        color: #5c4b43;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-family: 'Poppins', sans-serif;
        cursor: pointer;
        transition: 0.3s;
    }
    #darkModeToggle:hover {
        transform: scale(1.05);
    }

    .dark-mode {
        background-color: #1e1e1e;
        color: #eaeaea;
    }
    .dark-mode table {
        background-color: #2b2b2b;
        border-color: #444;
        color: #f0f0f0;
    }
    .dark-mode th {
        background-color: #3a3a3a;
        color: #fff;
    }
    .dark-mode tr:hover {
        background-color: #333;
    }
    .dark-mode input,
    .dark-mode textarea {
        background-color: #2c2c2c;
        color: #fff;
        border-color: #555;
    }
    .dark-mode .container {
        background-color: #1b1a39ff;
        border-color: #444;
    }

    /* Komponen lain tetap sama */
    .alert {
        background: #ffecef;
        border: 2px solid #f6d8e0;
        color: #5c4b43;
        padding: 0.8rem;
        margin: 1rem auto;
        max-width: 700px;
        border-radius: 8px;
        animation: fadeIn 0.5s ease;
    }
    .alert.success { background: #eaf7e1; border-color: #b4e1a2; }
    .alert.warning { background: #fff0e1; border-color: #f6c28b; }

    table {
        margin: 1rem auto;
        border-collapse: collapse;
        width: 90%;
        background: #fff;
        border: 2px solid #f6d8e0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
    th, td {
        padding: 0.8rem;
        border-bottom: 1px solid #f6d8e0;
    }
    th {
        background: #f6d8e0;
        color: #5c4b43;
    }
    tr:hover { background: #fff7ec; }

    form {
        margin: 1rem auto;
        max-width: 700px;
        text-align: left;
    }
    input, textarea {
        width: 100%;
        padding: 0.6rem;
        margin-bottom: 1rem;
        border: 2px solid #f6d8e0;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
    }
    label { font-weight: bold; }

    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        margin-top: 1rem;
        padding: 0;
    }
    .pagination li {
        margin: 0 5px;
    }
    .pagination a {
        display: block;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        background: #f6d8e0;
        color: #5c4b43;
        transition: 0.3s;
    }
    .pagination .active a {
        background: #e5ded7;
        font-weight: bold;
    }
    .pagination a:hover {
        transform: scale(1.05);
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        background: #fff7ec;
        border: 2px solid #f6d8e0;
        border-radius: 12px;
        padding: 2rem;
    }
</style>
</head>

<body>
<header>
  <h1 style="font-family:'Sofia', cursive;">ReHabit Dashboard</h1>
  <button id="darkModeToggle">🌙 Dark Mode</button>
</header>

<nav>
  <ul>
    <li><a href="index.php">Beranda</a></li>
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="?action=create">Tambah Habit</a></li>
    <li><a href="?">Daftar Habit</a></li>
    <li style="float:right;"><a href="logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a></li>
  </ul>
</nav>

<main>
<div class="container">
    <?= $message ?>

    <!-- FORM TAMBAH / EDIT -->
    <section>
        <h2><?= $editHabit ? "Edit Habit" : "Tambah Habit Baru" ?></h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $editHabit['id'] ?? '' ?>">
            <label>Nama Habit</label>
            <input type="text" name="name" placeholder="Misal: Bangun Pagi" value="<?= htmlspecialchars($editHabit['name'] ?? '') ?>">
            
            <label>Deskripsi</label>
            <textarea name="description" rows="3" placeholder="Deskripsi singkat"><?= htmlspecialchars($editHabit['description'] ?? '') ?></textarea>
            
            <label>Kategori</label>
            <input type="text" name="category" placeholder="Kesehatan / Produktivitas" value="<?= htmlspecialchars($editHabit['category'] ?? '') ?>">

            <button class="btn btn-primary" type="submit"><?= $editHabit ? "💾 Update Habit" : "Tambah Habit" ?></button>
            <?php if ($editHabit): ?>
                <a href="dashboard.php" class="btn btn-primary" style="background:#ffecef;">Batal</a>
            <?php endif; ?>
        </form>
    </section>

    <!-- SEARCH -->
    <section>
        <form method="GET" style="max-width:500px;margin:auto;">
            <input type="text" name="keyword" placeholder="Cari habit..." value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
    </section>

    <!-- TABLE DATA -->
    <section>
        <h2>Daftar Habit</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($habits)): ?>
                <tr><td colspan="5">Tidak ada data ditemukan.</td></tr>
            <?php else: ?>
                <?php foreach ($habits as $habit): ?>
                <tr>
                    <td><?= $habit['id'] ?></td>
                    <td><?= htmlspecialchars($habit['name']) ?></td>
                    <td><?= htmlspecialchars($habit['category']) ?></td>
                    <td><?= $habit['created_at'] ?></td>
                    <td>
                        <a class="btn btn-primary" href="?action=detail&id=<?= $habit['id'] ?>">Detail</a>
                        <a class="btn btn-primary" href="?action=edit&id=<?= $habit['id'] ?>">Edit</a>
                        <a class="btn btn-primary" style="background:#ffecef;" onclick="return confirm('Yakin ingin hapus habit ini?')" href="?action=delete&id=<?= $habit['id'] ?>">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- PAGINATION -->
        <ul class="pagination">
            <?php for ($i=1; $i<=$totalPages; $i++): ?>
                <li class="<?= ($i==$page)?'active':'' ?>">
                    <a href="?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </section>

    <!-- DETAIL -->
    <?php if ($action === 'detail' && $id):
        $stmt = $pdo->prepare("SELECT * FROM habits WHERE id=?");
        $stmt->execute([$id]);
        $habit = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($habit): ?>
        <section>
            <h2>Detail Habit</h2>
            <div class="card">
                <p><b>Nama:</b> <?= htmlspecialchars($habit['name']) ?></p>
                <p><b>Deskripsi:</b><br><?= nl2br(htmlspecialchars($habit['description'])) ?></p>
                <p><b>Kategori:</b> <?= htmlspecialchars($habit['category']) ?></p>
                <p><b>Dibuat:</b> <?= $habit['created_at'] ?></p>
                <a href="dashboard.php" class="btn btn-primary">Tutup</a>
            </div>
        </section>
    <?php endif; endif; ?>
</div>
</main>

<footer>
  <p>© 2025 ReHabit | <a href="#">Kembali ke atas</a></p>
</footer>

<script src="assets/script.js"></script>
</body>
</html>
