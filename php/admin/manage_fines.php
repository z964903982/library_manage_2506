<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

require_once '../db_connect.php';

$success = $error = "";

// 提交表单：修改罚款规则
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rate = floatval($_POST['overdue_rate']);
    if ($rate > 0) {
        $stmt = $conn->prepare("INSERT INTO FineRule (overdue_rate) VALUES (?)");
        $stmt->bind_param("d", $rate);
        if ($stmt->execute()) {
            $success = "罚款规则更新成功，当前每天罚款 $rate 元。";
        } else {
            $error = "更新失败：" . $stmt->error;
        }
    } else {
        $error = "请输入有效的罚款金额。";
    }
}

// 获取最新罚款规则
$result = $conn->query("SELECT overdue_rate FROM FineRule ORDER BY id DESC LIMIT 1");
$current_rate = $result->fetch_assoc()['overdue_rate'];
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>罚款规则设置</title>
</head>
<body>
    <h2>设置逾期罚款规则</h2>

    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        当前规则：每逾期一天罚款 <strong><?= htmlspecialchars($current_rate) ?></strong> 元<br><br>
        修改为：每天罚款 <input type="number" name="overdue_rate" step="0.01" min="0.01" required> 元
        <button type="submit">更新规则</button>
    </form>
</body>
</html>
