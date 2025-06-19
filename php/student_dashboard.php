<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <title>学生主页</title>
    <link rel="stylesheet" href="../css/student_dashboard.css">

</head>

<body>

    <div class="dashboard-container">
        <div class="header">
            <div>欢迎你，
                <?php echo $_SESSION['name']; ?> 同学
            </div>
            <a class="logout" href="../html/login.html">[退出登录]</a>
        </div>

        <table>
            <tr>
                <td><a href="../php/student/search_book.php">查询图书</a></td>
                <td><a href="../php/student/my_borrow.php">借阅记录</a></td>
                <td><a href="../php/student/reserve_book.php">我的预约</a></td>
                <td><a href="../php/student/comment.php">我的评价</a></td>
            </tr>
            <tr>
                <td><a href="../php/student/fines.php">罚款缴费</a></td>
                <td><a href="../php/student/recommend_books.php">图书推荐</a></td>
                <td><a href="../php/student/student_info.php">账户信息</a></td>
               
                
            </tr>
        </table>

        
    </div>

</body>

</html>