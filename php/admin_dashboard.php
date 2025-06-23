<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

$permission = $_SESSION['permission'];
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <title>管理员主页</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <div class="dashboard-container">
        <div class="header">
            <div>欢迎你，管理员 <?php echo $_SESSION['name']; ?> （权限等级 <?php echo $permission; ?>）</div>
            <a class="logout" href="../html/login.html">[退出登录]</a>
        </div>

        <table>
            <tr>
                <td><a href="../php/admin/manage_books.php">图书信息管理</a></td>
                <?php if ($permission == 2): ?>
                <td><a href="../php/admin/manage_students.php">学生账户管理</a></td>
                <?php else: ?>
                <td style="color:gray;">学生账户管理（无权限）</td>
                <?php endif; ?>
                <td><a href="../php/admin/borrow_requests.php">借阅记录管理</a></td>
                <td><a href="../php/admin/manage_reservations.php">管理预约队列</a></td>
            </tr>
            <tr>
                <td><a href="../php/admin/handle_exceptions.php">异常图书处理</a></td>
                <td><a href="../php/admin/manage_comment.php">评论信息管理</a></td>
                <?php if ($permission == 2): ?>
                <td><a href="../php/admin/manage_fines.php">系统参数配置</a></td>
                <?php else: ?>
                <td style="color:gray;">系统参数配置（无权限）</td>
                <?php endif; ?>
                
                <td><a href="../php/admin/statistics.php">统计报表</a></td>
                
                
                
            </tr>
        </table>

    </div>

</body>

</html>
