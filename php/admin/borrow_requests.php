<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

require_once '../db_connect.php';
$success = $error = "";

// 修改状态（手动还书或调整状态）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_id'])) {
    $borrow_id = $_POST['borrow_id'];
    $status = $_POST['status'];
    $return_date = $status === '已还' ? date('Y-m-d') : null;
    $renew_count = isset($_POST['renew_count']) ? intval($_POST['renew_count']) : 0;

    $stmt = $conn->prepare("UPDATE borrowrecord SET status = ?, return_date = ?, renew_count = ? WHERE borrow_id = ?");
    $stmt->bind_param("ssii", $status, $return_date, $renew_count, $borrow_id);
    if ($stmt->execute()) {
        $success = "借阅状态与续借次数已更新。";
    } else {
        $error = "更新失败：" . $stmt->error;
    }
}

// 查询记录
$student_id = $_GET['student_id'] ?? '';
$isbn = $_GET['isbn'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT b.*, s.name AS student_name, bk.title AS book_title 
        FROM borrowrecord b 
        JOIN student s ON b.student_id = s.student_id 
        JOIN book bk ON b.ISBN = bk.ISBN 
        WHERE 1=1";
$params = [];
$types = "";

if (!empty($student_id)) {
    $sql .= " AND b.student_id LIKE ?";
    $params[] = "%$student_id%";
    $types .= "s";
}
if (!empty($isbn)) {
    $sql .= " AND b.ISBN LIKE ?";
    $params[] = "%$isbn%";
    $types .= "s";
}
if (!empty($status_filter)) {
    $sql .= " AND b.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}
$sql .= " ORDER BY borrow_date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// 分类借阅记录
$not_returned = [];
$returned = [];
while ($row = $result->fetch_assoc()) {
    if ($row['status'] === '已还') {
        $returned[] = $row;
    } else {
        $not_returned[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>借阅记录管理</title>
    <link rel="stylesheet" href="../../css/manage_books.css">
</head>
<body>
    <h2>借阅记录管理</h2>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="get">
        学号: <input type="text" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
        ISBN: <input type="text" name="isbn" value="<?= htmlspecialchars($isbn) ?>">
        状态: 
        <select name="status">
            <option value="">全部</option>
            <option value="借出" <?= $status_filter === '借出' ? 'selected' : '' ?>>借出</option>
            <option value="已还" <?= $status_filter === '已还' ? 'selected' : '' ?>>已还</option>
            <option value="逾期" <?= $status_filter === '逾期' ? 'selected' : '' ?>>逾期</option>
        </select>
        <button type="submit">查询</button>
        <a href="borrow_requests.php">重置</a>
    </form>

    <h3>当前未归还记录（借出 + 逾期）</h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>借阅号</th><th>学号</th><th>姓名</th><th>ISBN</th><th>书名</th>
            <th>借出日期</th><th>应还日期</th><th>归还日期</th><th>状态</th><th>续借次数</th><th>操作</th>
        </tr>
        <?php foreach ($not_returned as $row): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
                <td><?= $row['borrow_id'] ?></td>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['ISBN']) ?></td>
                <td><?= htmlspecialchars($row['book_title']) ?></td>
                <td><?= $row['borrow_date'] ?></td>
                <td><?= $row['due_date'] ?></td>
                <td><?= $row['return_date'] ?: '——' ?></td>
                <td>
                    <select name="status">
                        <option value="借出" <?= $row['status'] === '借出' ? 'selected' : '' ?>>借出</option>
                        <option value="已还" <?= $row['status'] === '已还' ? 'selected' : '' ?>>已还</option>
                        <option value="逾期" <?= $row['status'] === '逾期' ? 'selected' : '' ?>>逾期</option>
                    </select>
                </td>
                <td><input type="number" name="renew_count" value="<?= $row['renew_count'] ?>" min="0" style="width: 50px;"></td>
                <td><button type="submit">更新</button></td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3 style="margin-top: 40px;">已归还记录</h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>借阅号</th><th>学号</th><th>姓名</th><th>ISBN</th><th>书名</th>
            <th>借出日期</th><th>应还日期</th><th>归还日期</th><th>状态</th><th>续借次数</th><th>操作</th>
        </tr>
        <?php foreach ($returned as $row): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
                <td><?= $row['borrow_id'] ?></td>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['ISBN']) ?></td>
                <td><?= htmlspecialchars($row['book_title']) ?></td>
                <td><?= $row['borrow_date'] ?></td>
                <td><?= $row['due_date'] ?></td>
                <td><?= $row['return_date'] ?: '——' ?></td>
                <td>
                    <select name="status">
                        <option value="借出" <?= $row['status'] === '借出' ? 'selected' : '' ?>>借出</option>
                        <option value="已还" <?= $row['status'] === '已还' ? 'selected' : '' ?>>已还</option>
                        <option value="逾期" <?= $row['status'] === '逾期' ? 'selected' : '' ?>>逾期</option>
                    </select>
                </td>
                <td><input type="number" name="renew_count" value="<?= $row['renew_count'] ?>" min="0" style="width: 50px;"></td>
                <td><button type="submit">更新</button></td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
