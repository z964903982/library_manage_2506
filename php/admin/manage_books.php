<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

require_once '../db_connect.php';
$success = $error = "";

// 处理添加/编辑图书
if (isset($_POST['action']) && ($_POST['action'] === 'add' || $_POST['action'] === 'edit')) {
    $isbn = $_POST['isbn'];
    $title = $_POST['title'];
    $author = $_POST['author'] ?? null;
    $publisher = $_POST['publisher'] ?? null;
    $publish_date = $_POST['publish_date'] ?? null;
    $price = $_POST['price'] ?? null;
    $category = $_POST['category'] ?? null;
    $location = $_POST['location'] ?? null;
    $total = intval($_POST['total_copies'] ?? 0);
    $available = intval($_POST['available_copies'] ?? 0);
    $status = $_POST['status'] ?? '在架';
    $description = $_POST['description'] ?? null;

    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO book (ISBN, title, author, publisher, publish_date, price, category, location, total_copies, available_copies, status, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssdssiiss", $isbn, $title, $author, $publisher, $publish_date, $price, $category, $location, $total, $available, $status, $description);
    } else {
        $stmt = $conn->prepare("UPDATE book SET title=?, author=?, publisher=?, publish_date=?, price=?, category=?, location=?, total_copies=?, available_copies=?, status=?, description=? WHERE ISBN=?");
        $stmt->bind_param("ssssdssiisss", $title, $author, $publisher, $publish_date, $price, $category, $location, $total, $available, $status, $description, $isbn);
    }

    if ($stmt->execute()) {
        $success = $_POST['action'] === 'add' ? "添加成功！" : "更新成功！";
    } else {
        $error = "操作失败：" . $stmt->error;
    }
}

// 删除图书
if (isset($_GET['delete'])) {
    $isbn = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM book WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    if ($stmt->execute()) {
        $success = "删除成功！";
    } else {
        $error = "删除失败：" . $stmt->error;
    }
}

// 批量导入图书（CSV文件）
if (isset($_POST['import']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($file, "r")) !== FALSE) {
        fgetcsv($handle); // 跳过标题行
        $imported = 0;
        while (($data = fgetcsv($handle)) !== FALSE) {
            // 确保数据长度匹配
            if (count($data) >= 12) {
                list($isbn, $title, $author, $publisher, $publish_date, $price, $category, $location, $total, $available, $status, $description) = $data;
                $stmt = $conn->prepare("INSERT INTO book (ISBN, title, author, publisher, publish_date, price, category, location, total_copies, available_copies, status, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title=VALUES(title), author=VALUES(author)");
                $stmt->bind_param("sssssdssiiss", $isbn, $title, $author, $publisher, $publish_date, $price, $category, $location, $total, $available, $status, $description);
                $stmt->execute();
                $imported++;
            }
        }
        fclose($handle);
        $success = "成功导入 $imported 本图书。";
    } else {
        $error = "文件读取失败。";
    }
}

// 查询逻辑
$title = $_GET['title'] ?? '';
$author = $_GET['author'] ?? '';
$category = $_GET['category'] ?? '';
$isbn = $_GET['isbn'] ?? '';

$sql = "SELECT * FROM book WHERE 1=1";
$params = [];

if (!empty($title)) {
    $sql .= " AND title LIKE ?";
    $params[] = "%$title%";
}
if (!empty($author)) {
    $sql .= " AND author LIKE ?";
    $params[] = "%$author%";
}
if (!empty($category)) {
    $sql .= " AND category LIKE ?";
    $params[] = "%$category%";
}
if (!empty($isbn)) {
    $sql .= " AND ISBN = ?";
    $params[] = $isbn;
}

$sql .= " ORDER BY title ASC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>图书管理</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<h2>图书管理</h2>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<h3>查询图书</h3>
<form method="get">
    标题: <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>">
    作者: <input type="text" name="author" value="<?php echo htmlspecialchars($author); ?>">
    分类: <input type="text" name="category" value="<?php echo htmlspecialchars($category); ?>">
    ISBN: <input type="text" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>">
    <button type="submit">查询</button>
    <a href="admin_book_manage.php">重置</a>
</form>

<h3>添加图书</h3>
<form method="post">
    <input type="hidden" name="action" value="add">
    ISBN: <input name="isbn" required>
    标题: <input name="title" required>
    作者: <input name="author">
    出版社: <input name="publisher">
    出版年: <input name="publish_date" type="number" min="1000" max="9999">
    价格: <input name="price" type="number" step="0.01">
    分类: <input name="category">
    位置: <input name="location">
    总量: <input name="total_copies" type="number">
    可借: <input name="available_copies" type="number">
    状态:
    <select name="status">
        <option value="在架">在架</option>
        <option value="借出">借出</option>
        <option value="遗失">遗失</option>
        <option value="下架">下架</option>
    </select>
    简介: <textarea name="description"></textarea>
    <button type="submit">添加图书</button>
</form>

<h3>批量导入图书（CSV）</h3>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="csv_file" accept=".csv" required>
    <button type="submit" name="import" value="1">导入</button>
</form>

<h3>图书列表</h3>
<table border="1" cellpadding="6">
    <tr>
        <th>ISBN</th><th>标题</th><th>作者</th><th>出版社</th><th>出版年</th><th>价格</th><th>分类</th><th>位置</th><th>状态</th><th>操作</th>
    </tr>
    <?php while($row = $books->fetch_assoc()): ?>
    <tr>
        <form method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="isbn" value="<?php echo htmlspecialchars($row['ISBN']); ?>">
            <td><?php echo htmlspecialchars($row['ISBN']); ?></td>
            <td><input name="title" value="<?php echo htmlspecialchars($row['title']); ?>"></td>
            <td><input name="author" value="<?php echo htmlspecialchars($row['author']); ?>"></td>
            <td><input name="publisher" value="<?php echo htmlspecialchars($row['publisher']); ?>"></td>
            <td><input name="publish_date" type="number" value="<?php echo htmlspecialchars($row['publish_date']); ?>"></td>
            <td><input name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($row['price']); ?>"></td>
            <td><input name="category" value="<?php echo htmlspecialchars($row['category']); ?>"></td>
            <td><input name="location" value="<?php echo htmlspecialchars($row['location']); ?>"></td>
            <td>
                <select name="status">
                    <option value="在架" <?php if($row['status']==='在架') echo 'selected'; ?>>在架</option>
                    <option value="借出" <?php if($row['status']==='借出') echo 'selected'; ?>>借出</option>
                    <option value="遗失" <?php if($row['status']==='遗失') echo 'selected'; ?>>遗失</option>
                    <option value="下架" <?php if($row['status']==='下架') echo 'selected'; ?>>下架</option>
                </select>
            </td>
            <td>
                <input type="hidden" name="total_copies" value="<?php echo (int)$row['total_copies']; ?>">
                <input type="hidden" name="available_copies" value="<?php echo (int)$row['available_copies']; ?>">
                <input type="hidden" name="description" value="<?php echo htmlspecialchars($row['description']); ?>">
                <button type="submit">保存</button>
                <a href="?delete=<?php echo $row['ISBN']; ?>" onclick="return confirm('确定删除该图书？')">删除</a>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
