<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
require_once '../db_connect.php';
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>统计报表</title>
    <link rel="stylesheet" href="../../css/manage_books.css">
</head>
<body>

<h1>图书馆统计报表</h1>

<!-- 热门图书 -->
<h2>热门图书TOP 20</h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ISBN</th><th>书名</th><th>借阅次数</th></tr>
    <?php
    $res = $conn->query("SELECT * FROM popularbooks");
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['ISBN']}</td><td>{$row['title']}</td><td>{$row['borrow_count']}</td></tr>";
    }
    ?>
</table>

<!-- 各学院借阅统计 -->
<h2>各学院借阅统计</h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>学院</th><th>总借阅次数</th><th>逾期次数</th></tr>
    <?php
    $res = $conn->query("SELECT * FROM departmentborrowstats");
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['department']}</td><td>{$row['total_borrow']}</td><td>{$row['overdue_count']}</td></tr>";
    }
    ?>
</table>

<!-- 每月借阅统计 -->
<h2>每月借阅量统计</h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>月份</th><th>借阅数量</th></tr>
    <?php
    $res = $conn->query("SELECT * FROM borrow_by_month");
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['month']}</td><td>{$row['borrow_count']}</td></tr>";
    }
    ?>
</table>

<!-- 图书类型统计 -->
<h2>图书类型借阅统计</h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>图书类型</th><th>借阅数量</th></tr>
    <?php
    $res = $conn->query("SELECT * FROM borrow_by_category");
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['category']}</td><td>{$row['borrow_count']}</td></tr>";
    }
    ?>
</table>

<!-- 逾期分析（按学院） -->
<h2>逾期行为分析（按学院）</h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>学院</th><th>逾期次数</th><th>累计罚款金额（元）</th></tr>
    <?php
    $res = $conn->query("SELECT * FROM overdue_analysis");
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['department']}</td><td>{$row['overdue_count']}</td><td>{$row['total_overdue_amount']}</td></tr>";
    }
    ?>
</table>

<!-- 逾期分析（按学生） -->
<h2>逾期行为分析（按学生）</h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>学号</th><th>姓名</th><th>学院</th><th>逾期次数</th><th>累计罚款金额（元）</th></tr>
    <?php
    $res = $conn->query("SELECT * FROM overdue_by_student");
    while ($row = $res->fetch_assoc()) {
        echo "<tr>
                <td>{$row['student_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['department']}</td>
                <td>{$row['overdue_count']}</td>
                <td>{$row['total_overdue_amount']}</td>
              </tr>";
    }
    ?>
</table>

</body>
</html>
