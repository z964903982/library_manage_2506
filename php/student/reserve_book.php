<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php';

$student_id = $_SESSION['user_id'];

// 取消预约逻辑
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $cancel_id = $_POST['cancel_id'];

    $cancel_stmt = $conn->prepare("UPDATE reservation SET status = '取消' WHERE reserve_id = ? AND student_id = ?");
    $cancel_stmt->bind_param("is", $cancel_id, $student_id);
    if ($cancel_stmt->execute()) {
        $success = "已取消预约。";
    } else {
        $error = "取消失败，请稍后再试。";
    }
}

// 查询当前学生的预约记录，联合图书信息
$reserve_sql = "
    SELECT r.*, b.title, b.author, b.cover_image, b.available_copies 
    FROM reservation r 
    JOIN book b ON r.ISBN = b.ISBN 
    WHERE r.student_id = ?
    ORDER BY r.reserve_time DESC
";
$stmt = $conn->prepare($reserve_sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$reserve_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>我的预约记录</title>
    <link rel="stylesheet" href="../../css/reserve_book.css">
    <style>
        .book-card {
            border: 1px solid #ccc;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            gap: 20px;
            background: #f9f9f9;
        }
        .book-card img {
            width: 100px;
            height: 140px;
            object-fit: cover;
        }
        .book-info {
            flex-grow: 1;
        }
        .book-info h3 {
            margin: 0 0 5px;
        }
        .book-info p {
            margin: 4px 0;
        }
        .status-hint {
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>

<h2>你好，<?php echo htmlspecialchars($_SESSION['name']); ?> 同学</h2>
<h3>我的图书预约记录</h3>

<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

<?php if ($reserve_result->num_rows === 0): ?>
    <p>你当前没有任何预约记录。</p>
<?php else: ?>
    <?php while ($row = $reserve_result->fetch_assoc()): ?>
        <div class="book-card">
            <img src="<?php echo htmlspecialchars($row['cover_image'] ?? '../images/default.jpg'); ?>" alt="封面">
            <div class="book-info">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><strong>作者：</strong><?php echo htmlspecialchars($row['author']); ?></p>
                <p><strong>预约时间：</strong><?php echo htmlspecialchars($row['reserve_time']); ?></p>
                <p><strong>状态：</strong><?php echo htmlspecialchars($row['status']); ?></p>

                <?php if ($row['status'] === '已通知'): ?>
                    <p class="status-hint" style="color: red;">图书已到架！请尽快前往借阅页面借阅。</p>
                <?php endif; ?>

                <?php if ($row['status'] === '等待'): ?>
                    <form method="post" onsubmit="return confirm('确定取消该预约吗？');">
                        <input type="hidden" name="cancel_id" value="<?php echo $row['reserve_id']; ?>">
                        <button type="submit">取消预约</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

</body>
</html>
