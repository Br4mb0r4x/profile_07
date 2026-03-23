<?php
require 'db.php';

// vytvoření tabulky
$db->exec("CREATE TABLE IF NOT EXISTS interests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT UNIQUE
)");

/* ===== CREATE ===== */
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);

    // kontrola existence
    $stmt = $db->prepare("SELECT COUNT(*) FROM interests WHERE name = ?");
    $stmt->execute([$name]);

    if ($stmt->fetchColumn() > 0) {
        header("Location: ?page=interests&msg=exists");
        exit;
    }

    $stmt = $db->prepare("INSERT INTO interests (name) VALUES (?)");
    $stmt->execute([$name]);

    header("Location: ?page=interests&msg=added");
    exit;
}

/* ===== DELETE ===== */
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM interests WHERE id = ?");
    $stmt->execute([$_GET['delete']]);

    header("Location: ?page=interests&msg=deleted");
    exit;
}

/* ===== UPDATE ===== */
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $id = $_POST['id'];

    // zjisti původní hodnotu
    $stmt = $db->prepare("SELECT name FROM interests WHERE id = ?");
    $stmt->execute([$id]);
    $original = $stmt->fetchColumn();

    if ($original === $name) {
        header("Location: ?page=interests&msg=nochange");
        exit;
    }

    // kontrola duplicity
    $stmt = $db->prepare("SELECT COUNT(*) FROM interests WHERE name = ? AND id != ?");
    $stmt->execute([$name, $id]);

    if ($stmt->fetchColumn() > 0) {
        header("Location: ?page=interests&msg=exists");
        exit;
    }

    $stmt = $db->prepare("UPDATE interests SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);

    header("Location: ?page=interests&msg=updated");
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

<!-- 🔔 HLÁŠKY -->
<?php if (isset($_GET['msg'])): ?>
    <p class="msg">
        <?php
        switch ($_GET['msg']) {
            case 'added':
                echo "Zájem byl úspěšně přidán.";
                break;
            case 'deleted':
                echo "Zájem byl smazán.";
                break;
            case 'updated':
                echo "Zájem byl upraven.";
                break;
            case 'exists':
                echo "Tento zájem již existuje.";
                break;
            case 'nochange':
                echo "Nic nebylo změněno.";
                break;
        }
        ?>
    </p>
<?php endif; ?>

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
            <span>
                <a href="?page=interests&edit=<?= $i['id'] ?>">✏️</a>
                <a href="?page=interests&delete=<?= $i['id'] ?>">❌</a>
            </span>
        </li>
    <?php endforeach; ?>
</ul>