<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php';

$student_id = $_SESSION['user_id'];
$success = $error = "";

// 处理信息更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_contact = $_POST['contact'] ?? '';
    $new_email = $_POST['email'] ?? '';
    $new_password = $_POST['password'] ?? '';

    if (empty($new_contact) || empty($new_email)) {
        $error = "手机号和邮箱不能为空";
    } else {
        // 不加密密码，直接保存（ 实验用途）
        if (!empty($new_password)) {
            $stmt = $conn->prepare("UPDATE student SET contact = ?, email = ?, password = ? WHERE student_id = ?");
            $stmt->bind_param("ssss", $new_contact, $new_email, $new_password, $student_id);
        } else {
            $stmt = $conn->prepare("UPDATE student SET contact = ?, email = ? WHERE student_id = ?");
            $stmt->bind_param("sss", $new_contact, $new_email, $student_id);
        }

        if ($stmt->execute()) {
            $success = "信息更新成功！";
        } else {
            $error = "更新失败，请稍后重试。";
        }
    }
}


// 查询当前学生信息
$stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>账户信息</title>
    <link rel="stylesheet" href="../../css/student_info.css">
</head>
<body>
<div class="container">

<h2>账户信息</h2>

<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<?php if ($student['status'] === '冻结'): ?>
    <p style="color:red;">⚠️ 你的账户已冻结，请尽快前往缴纳罚款以恢复使用。</p>
<?php endif; ?>

<table border="1" cellpadding="8" style="margin-bottom: 20px;">
    <tr><th>学号</th><td><?php echo htmlspecialchars($student['student_id']); ?></td></tr>
    <tr><th>姓名</th><td><?php echo htmlspecialchars($student['name']); ?></td></tr>
    <tr><th>性别</th><td><?php echo htmlspecialchars($student['gender']); ?></td></tr>
    <tr><th>学院</th><td><?php echo htmlspecialchars($student['department']); ?></td></tr>
    <tr><th>专业</th><td><?php echo htmlspecialchars($student['major']); ?></td></tr>
    <tr><th>年级</th><td><?php echo htmlspecialchars($student['grade']); ?></td></tr>
    <tr><th>班级</th><td><?php echo htmlspecialchars($student['class']); ?></td></tr>
    <tr><th>状态</th><td><?php echo htmlspecialchars($student['status']); ?></td></tr>
    <tr><th>当前借阅</th><td><?php echo $student['current_borrow'] . " / " . $student['max_borrow']; ?></td></tr>
</table>

<h3>修改联系方式与密码</h3>
<form method="post">
    <label>手机号：<input type="text" name="contact" value="<?php echo htmlspecialchars($student['contact']); ?>"></label><br><br>
    <label>邮箱：<input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>"></label><br><br>
    <label>新密码：<input type="password" name="password" placeholder="不修改可留空"></label><br><br>
    <button type="submit">保存修改</button>
</form>
</div>
</body>
</html>
