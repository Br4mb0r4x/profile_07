<?php
require 'db.php';

// vytvoření tabulky
$db->exec("CREATE TABLE IF NOT EXISTS interests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT
)");

/* ===== CREATE ===== */
if (isset($_POST['add'])) {
    $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
    $stmt->execute([$_POST['name']]);

    header("Location: ?page=interests");
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: ?page=interests");
    exit;
}

/* ===== UPDATE ===== */
if (isset($_POST['update'])) {
    $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
    $stmt->execute([$_POST['name'], $_POST['id']]);

    header("Location: ?page=interests");
    exit;
}

/* ===== EDIT MODE ===== */
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM interests WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ===== LOAD DATA ===== */
$interests = $db->query("SELECT * FROM interests")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Zájmy</h1>

<nav>
    <a href="?page=home">Home</a> |
    <a href="?page=interests">Interests</a> |
    <a href="?page=skills">Skills</a>
</nav>

<!-- FORM -->
<form method="POST">
    <input type="text" name="name" 
        value="<?= $edit['name'] ?? '' ?>" 
        placeholder="Zájem" required>

    <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
        <button type="submit" name="update">Upravit</button>
    <?php else: ?>
        <button type="submit" name="add">Přidat</button>
    <?php endif; ?>
</form>

<hr>

<!-- LIST -->
<ul>
    <?php foreach ($interests as $i): ?>
        <li>
            <?= htmlspecialchars($i['name']) ?>
            <a href="?page=interests&edit=<?= $i['id'] ?>">✏️</a>
            <a href="?page=interests&delete=<?= $i['id'] ?>">❌</a>
        </li>
    <?php endforeach; ?>
</ul>