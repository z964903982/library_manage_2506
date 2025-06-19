<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo "未登录或无权限访问。";
    exit;
}

$student_id = $_SESSION['user_id'];
$message = "";

// 处理缴费操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fine_id'])) {
    $fine_id = intval($_POST['fine_id']);

    $stmt = $conn->prepare("UPDATE Fine SET status = '已缴' WHERE fine_id = ? AND student_id = ?");
    $stmt->bind_param("is", $fine_id, $student_id);
    if ($stmt->execute()) {
        $message = "缴费成功！";
    } else {
        $message = "缴费失败，请稍后再试。";
    }
    $stmt->close();
}

// 查询该学生的罚款记录
$sql = "SELECT fine_id, borrow_id, amount, reason, status FROM Fine WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>罚款缴费</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background:rgb(255, 255, 255); }
        table { border-collapse: collapse; width: 100%; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background:rgb(221, 221, 221); color: black; }
        button { padding: 6px 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .message { margin: 10px 0; color: green; }
    </style>
</head>
<body>
    <h2>罚款缴费记录</h2>
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>罚款编号</th>
                <th>借阅编号</th>
                <th>金额 (元)</th>
                <th>原因</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['fine_id'] ?></td>
                    <td><?= $row['borrow_id'] ?></td>
                    <td><?= number_format($row['amount'], 2) ?></td>
                    <td><?= $row['reason'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <?php if ($row['status'] === '未缴'): ?>
                            <form method="POST" style="margin:0;">
                                <input type="hidden" name="fine_id" value="<?= $row['fine_id'] ?>">
                                <button type="submit">缴费</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
