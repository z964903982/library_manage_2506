<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

require_once '../db_connect.php';
$success = $error = "";

// 修改预约状态
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve_id'])) {
    $reserve_id = $_POST['reserve_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE reservation SET status = ? WHERE reserve_id = ?");
    $stmt->bind_param("si", $status, $reserve_id);
    if ($stmt->execute()) {
        $success = "预约状态已更新。";
    } else {
        $error = "更新失败：" . $stmt->error;
    }
}

// 查询条件
$student_id = $_GET['student_id'] ?? '';
$isbn = $_GET['isbn'] ?? '';
$status = $_GET['status'] ?? '';

// 构建 SQL 查询
$sql = "SELECT r.*, s.name AS student_name, b.title AS book_title
        FROM reservation r
        JOIN student s ON r.student_id = s.student_id
        JOIN book b ON r.ISBN = b.ISBN
        WHERE 1=1";
$params = [];
$types = "";

if (!empty($student_id)) {
    $sql .= " AND r.student_id LIKE ?";
    $params[] = "%$student_id%";
    $types .= "s";
}
if (!empty($isbn)) {
    $sql .= " AND r.ISBN LIKE ?";
    $params[] = "%$isbn%";
    $types .= "s";
}
if (!empty($status)) {
    $sql .= " AND r.status = ?";
    $params[] = $status;
    $types .= "s";
}
$sql .= " ORDER BY reserve_time ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>预约队列管理</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <h2>预约队列管理</h2>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="get">
        学号: <input type="text" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
        ISBN: <input type="text" name="isbn" value="<?= htmlspecialchars($isbn) ?>">
        状态: 
        <select name="status">
            <option value="">全部</option>
            <option value="等待" <?= $status === '等待' ? 'selected' : '' ?>>等待</option>
            <option value="已通知" <?= $status === '已通知' ? 'selected' : '' ?>>已通知</option>
            <option value="取消" <?= $status === '取消' ? 'selected' : '' ?>>取消</option>
        </select>
        <button type="submit">查询</button>
        <a href="manage_reservation.php">重置</a>
    </form>

    <table border="1" cellpadding="6">
        <tr>
            <th>预约号</th><th>学号</th><th>姓名</th><th>ISBN</th><th>书名</th>
            <th>预约时间</th><th>状态</th><th>操作</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="reserve_id" value="<?= $row['reserve_id'] ?>">
                <td><?= $row['reserve_id'] ?></td>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['ISBN']) ?></td>
                <td><?= htmlspecialchars($row['book_title']) ?></td>
                <td><?= $row['reserve_time'] ?></td>
                <td>
                    <select name="status">
                        <option value="等待" <?= $row['status'] === '等待' ? 'selected' : '' ?>>等待</option>
                        <option value="已通知" <?= $row['status'] === '已通知' ? 'selected' : '' ?>>已通知</option>
                        <option value="取消" <?= $row['status'] === '取消' ? 'selected' : '' ?>>取消</option>
                    </select>
                </td>
                <td><button type="submit">更新</button></td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
