<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
require_once '../db_connect.php';

$student_id = $_SESSION['user_id'];
$feedback = "";

// 处理删除操作
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_stmt = $conn->prepare("DELETE FROM review WHERE review_id = ? AND student_id = ?");
    $delete_stmt->bind_param("is", $delete_id, $student_id);
    if ($delete_stmt->execute()) {
        $feedback = "评价删除成功。";
    } else {
        $feedback = "删除失败，请稍后再试。";
    }
}

// 处理编辑操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['rating'], $_POST['content'])) {
    $review_id = intval($_POST['review_id']);
    $rating = intval($_POST['rating']);
    $content = trim($_POST['content']);

    if ($rating < 1 || $rating > 5 || $content === '') {
        $feedback = "请输入有效的评分和内容。";
    } else {
        $update_stmt = $conn->prepare("UPDATE review SET rating = ?, content = ? WHERE review_id = ? AND student_id = ?");
        $update_stmt->bind_param("isis", $rating, $content, $review_id, $student_id);
        if ($update_stmt->execute()) {
            $feedback = "评价修改成功。";
        } else {
            $feedback = "修改失败，请稍后再试。";
        }
    }
}

// 查询当前学生的所有评价
$review_stmt = $conn->prepare("
    SELECT r.*, b.title 
    FROM review r 
    JOIN book b ON r.ISBN = b.ISBN 
    WHERE r.student_id = ? 
    ORDER BY r.review_time DESC
");
$review_stmt->bind_param("s", $student_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>我的评价</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .container { max-width: 900px; margin: auto; padding: 20px; }
        .review-block { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 8px; }
        textarea { width: 100%; }
        .review-actions { margin-top: 10px; }
        .rating-select { width: 60px; }
        .success { color: green; }
        .error { color: red; }
        .delete-button { color: red; background: none; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2><?php echo htmlspecialchars($_SESSION['name']); ?> 的图书评价</h2>

    <?php if (!empty($feedback)): ?>
        <p class="<?php echo strpos($feedback, '成功') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($feedback); ?>
        </p>
    <?php endif; ?>

    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="review-block">
                <h3><?php echo htmlspecialchars($review['title']); ?></h3>
                <form method="post" action="">
                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                    <label>评分：</label>
                    <select name="rating" class="rating-select" required>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($i == $review['rating']) ? 'selected' : ''; ?>>
                                <?php echo $i; ?>分
                            </option>
                        <?php endfor; ?>
                    </select><br><br>

                    <label>内容：</label><br>
                    <textarea name="content" rows="4" required><?php echo htmlspecialchars($review['content']); ?></textarea><br>

                    <div class="review-actions">
                        <button type="submit">保存修改</button>
                        <a href="?delete_id=<?php echo $review['review_id']; ?>" onclick="return confirm('确定要删除这条评价吗？');" class="delete-button">🗑 删除</a>
                    </div>
                </form>
                <p style="color:gray;">提交时间：<?php echo $review['review_time']; ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>你还没有发表过评价。</p>
    <?php endif; ?>

    <a href="search_book.php">⬅ 返回图书查询</a>
</div>

</body>
</html>
