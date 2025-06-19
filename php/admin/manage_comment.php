<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
require_once '../db_connect.php';

// 处理删除请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM review WHERE review_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('评论已删除'); location.href='manage_comment.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>评论管理</title>
</head>
<body>

<h1>评论信息管理</h1>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>评论ID</th>
        <th>图书ISBN</th>
        <th>用户ID</th>
        <th>评分</th>
        <th>内容</th>
        <th>评论时间</th>
        <th>操作</th>
    </tr>

    <?php
    $res = $conn->query("SELECT * FROM review ORDER BY review_time DESC");
    while ($row = $res->fetch_assoc()) {
        echo "<tr>
                <td>{$row['review_id']}</td>
                <td>{$row['ISBN']}</td>
                <td>{$row['student_id']}</td>
                <td>{$row['rating']}</td>
                <td>" . htmlspecialchars($row['content']) . "</td>
                <td>{$row['review_time']}</td>
                <td>
                    <form method='POST' style='display:inline;' onsubmit=\"return confirm('确定要删除这条评论吗？');\">
                        <input type='hidden' name='delete_id' value='{$row['review_id']}'>
                        <input type='submit' value='删除'>
                    </form>
                </td>
              </tr>";
    }
    ?>
</table>

</body>
</html>
