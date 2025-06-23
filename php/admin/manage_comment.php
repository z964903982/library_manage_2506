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
    <style>
    body {
        font-family: "Helvetica Neue", "微软雅黑", sans-serif;
        background-color: #f0f8ff;
        padding: 20px;
        color: #333;
    }

    h1 {
        color:rgb(0, 0, 0);
        border-bottom: 2px solid #cce4f7;
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-size: 24px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 14px;
        background-color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    table th,
    table td {
        border: 1px solid #cce4f7;
        padding: 10px 12px;
        text-align: center;
    }

    table th {
        background-color: #32729c;
        color: white;
    }

    table tr:nth-child(even) {
        background-color: #f5faff;
    }

    .btn-delete {
        background-color: #fff;
        color: #d9534f;
        border: 1px solid #d9534f;
        padding: 4px 10px;
        font-size: 13px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-delete:hover {
        background-color: #d9534f;
        color: #fff;
    }
</style>

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
                        <input type='submit' value='删除' class='btn-delete'>

                    </form>
                </td>
              </tr>";
    }
    ?>
</table>

</body>
</html>
