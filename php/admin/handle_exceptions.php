<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

require_once '../db_connect.php';
$success = $error = "";

// 处理重新上架操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ISBN'])) {
    $isbn = $_POST['ISBN'];
    $new_status = '在架';
    $total_copies = intval($_POST['total_copies']);
    $available_copies = intval($_POST['available_copies']);

    if ($available_copies > $total_copies) {
        $error = "可借数量不能大于总库存。";
    } else {
        $stmt = $conn->prepare("UPDATE book SET status = ?, total_copies = ?, available_copies = ? WHERE ISBN = ?");
        $stmt->bind_param("siis", $new_status, $total_copies, $available_copies, $isbn);
        if ($stmt->execute()) {
            $success = "图书 $isbn 已重新上架。";
        } else {
            $error = "更新失败：" . $stmt->error;
        }
    }
}

// 查询所有“下架”或“遗失”的图书
$sql = "SELECT * FROM book WHERE status IN ('下架', '遗失') ORDER BY title ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>异常图书处理</title>
    <link rel="stylesheet" href="../../css/manage_books.css">
</head>
<body>
    <h2>异常图书处理（下架/遗失）</h2>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <table border="1" cellpadding="6">
        <tr>
            <th>ISBN</th><th>书名</th><th>作者</th><th>出版社</th><th>分类</th>
            <th>原状态</th><th>总库存</th><th>可借数量</th><th>操作</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="ISBN" value="<?= htmlspecialchars($row['ISBN']) ?>">
                <td><?= htmlspecialchars($row['ISBN']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td><?= htmlspecialchars($row['publisher']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= $row['status'] ?></td>
                <td><input type="number" name="total_copies" value="<?= $row['total_copies'] ?>" min="0" required></td>
                <td><input type="number" name="available_copies" value="<?= $row['available_copies'] ?>" min="0" required></td>
                <td><button type="submit">重新上架</button></td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
