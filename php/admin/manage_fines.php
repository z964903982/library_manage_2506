<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
require_once '../db_connect.php';

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'fine') {
        $rate = floatval($_POST['overdue_rate']);
        if ($rate > 0) {
            $stmt = $conn->prepare("INSERT INTO FineRule (overdue_rate) VALUES (?)");
            $stmt->bind_param("d", $rate);
            if ($stmt->execute()) {
                $success = "罚款规则已更新为：每天 $rate 元";
            } else {
                $error = "罚款规则更新失败：" . $stmt->error;
            }
        } else {
            $error = "请输入有效的罚款金额";
        }

    } elseif ($action === 'max_borrow') {
        $max_borrow = intval($_POST['max_borrow']);
        if ($max_borrow > 0) {
            $stmt = $conn->prepare("UPDATE systemconfig SET config_value = ? WHERE config_key = 'max_borrow_limit'");
            $stmt->bind_param("s", $max_borrow);
            if ($stmt->execute()) {
                $success = "最大借阅数已设置为 $max_borrow 本";
            } else {
                $error = "最大借阅数设置失败：" . $stmt->error;
            }
        } else {
            $error = "请输入有效的最大借阅数";
        }

    } elseif ($action === 'borrow_days') {
        $borrow_days = intval($_POST['borrow_days']);
        if ($borrow_days > 0) {
            $stmt = $conn->prepare("UPDATE systemconfig SET config_value = ? WHERE config_key = 'default_borrow_days'");
            $stmt->bind_param("s", $borrow_days);
            if ($stmt->execute()) {
                $success = "借阅期限已设置为 $borrow_days 天";
            } else {
                $error = "借阅期限设置失败：" . $stmt->error;
            }
        } else {
            $error = "请输入有效的借阅期限";
        }
    }
}


// 获取当前值
$current_rate = 0;
$res = $conn->query("SELECT overdue_rate FROM FineRule ORDER BY id DESC LIMIT 1");
if ($row = $res->fetch_assoc()) $current_rate = $row['overdue_rate'];

function get_config_value($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT config_value FROM systemconfig WHERE config_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) return $row['config_value'];
    return $default;
}

$current_max_borrow = get_config_value($conn, 'max_borrow', '5');
$current_borrow_days = get_config_value($conn, 'borrow_days', '30');
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>系统参数配置</title>
    <link rel="stylesheet" href="../../css/manage_books.css">
</head>
<body>
<h2>系统参数配置</h2>

<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<!-- 罚款规则 -->
<form method="post">
    <input type="hidden" name="action" value="fine">
    <p>
        当前每天罚款：<strong><?= htmlspecialchars($current_rate) ?> 元</strong><br>
        修改为：<input type="number" name="overdue_rate" step="0.01" min="0.01" required> 元/天
        <button type="submit">更新罚款规则</button>
    </p>
</form>
<hr>

<!-- 最大借阅数 -->
<form method="post">
    <input type="hidden" name="action" value="max_borrow">
    <p>
        当前最大借阅数：<strong><?= htmlspecialchars($current_max_borrow) ?> 本</strong><br>
        修改为：<input type="number" name="max_borrow" min="1" required> 本
        <button type="submit">更新借阅上限</button>
    </p>
</form>
<hr>

<!-- 借阅期限 -->
<form method="post">
    <input type="hidden" name="action" value="borrow_days">
    <p>
        当前归还期限：<strong><?= htmlspecialchars($current_borrow_days) ?> 天</strong><br>
        修改为：<input type="number" name="borrow_days" min="1" required> 天
        <button type="submit">更新归还期限</button>
    </p>
</form>

</body>
</html>
