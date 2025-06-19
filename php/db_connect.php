<?php
$host = 'localhost';
$user = 'root';
$pass = '88888888'; // 替换为你的数据库密码
$dbname = 'library_manage_2506';
$charset = 'utf8mb4';

// 创建 MySQLi 连接
$conn = new mysqli($host, $user, $pass, $dbname);

// 设置字符集
$conn->set_charset($charset);

// 检查连接
if ($conn->connect_error) {
    die("数据库连接失败：" . $conn->connect_error);
}


?>
