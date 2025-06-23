<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
require_once '../db_connect.php';

// 获取 ISBN 参数
if (!isset($_GET['ISBN'])) {
    echo "无效的请求";
    exit();
}
$isbn = $_GET['ISBN'];

// 查询图书信息
$book_stmt = $conn->prepare("SELECT * FROM book WHERE ISBN = ?");
$book_stmt->bind_param("s", $isbn);
$book_stmt->execute();
$book_result = $book_stmt->get_result();
$book = $book_result->fetch_assoc();

if (!$book) {
    echo "未找到图书信息。";
    exit();
}

// 评价提交处理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['content'])) {
    $rating = (int)$_POST['rating'];
    $content = trim($_POST['content']);
    $student_id = $_SESSION['user_id'];

    // 限制评分范围
    if ($rating < 1 || $rating > 5) {
        $review_error = "评分必须在 1 到 5 分之间。";
    } elseif ($content === '') {
        $review_error = "评价内容不能为空。";
    } else {
        $insert_review = $conn->prepare("INSERT INTO review (ISBN, student_id, rating, content) VALUES (?, ?, ?, ?)");
        $insert_review->bind_param("ssis", $isbn, $student_id, $rating, $content);
        if ($insert_review->execute()) {
            // 插入成功后刷新页面，防止重复提交
            header("Location: book_detail.php?ISBN=" . urlencode($isbn));
            exit();
        } else {
            $review_error = "提交失败，请稍后再试。";
        }
    }
}


// 查询评论信息（按时间倒序）
$review_stmt = $conn->prepare("SELECT r.*, s.name 
    FROM review r 
    JOIN student s ON r.student_id = s.student_id 
    WHERE r.ISBN = ? 
    ORDER BY r.review_time DESC");
$review_stmt->bind_param("s", $isbn);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>图书详情 - <?php echo htmlspecialchars($book['title']); ?></title>
    <link rel="stylesheet" href="../../css/book_detail.css">
    <style>
        .detail-container { max-width: 800px; margin: auto; padding: 20px; }
        .book-info img { float: left; width: 150px; margin-right: 20px; }
        .book-info { overflow: hidden; }
        .review { border-top: 1px solid #ccc; padding-top: 10px; margin-top: 10px; }
        .review h4 { margin: 0; }
        .back-link { margin-top: 20px; display: block; }
    </style>
</head>
<body>

<div class="detail-container">
    <h2><?php echo htmlspecialchars($book['title']); ?></h2>

    <div class="book-info">
        <img src="<?php echo htmlspecialchars($book['cover_image'] ?? '../images/default.jpg'); ?>" alt="封面">
        <p><strong>作者：</strong><?php echo htmlspecialchars($book['author']); ?></p>
        <p><strong>出版社：</strong><?php echo htmlspecialchars($book['publisher']); ?></p>
        <p><strong>出版年份：</strong><?php echo htmlspecialchars($book['publish_date']); ?></p>
        <p><strong>分类：</strong><?php echo htmlspecialchars($book['category']); ?></p>
        <p><strong>ISBN：</strong><?php echo htmlspecialchars($book['ISBN']); ?></p>
        <p><strong>价格：</strong>￥<?php echo htmlspecialchars($book['price']); ?></p>
        <p><strong>馆藏情况：</strong><?php echo $book['available_copies'] . "/" . $book['total_copies']; ?></p>
        <p><strong>状态：</strong><?php echo htmlspecialchars($book['status']); ?></p>
        <p><strong>简介：</strong><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
    </div>

    <h3>我要评价</h3>
<form method="post" action="">
    <label for="rating">评分（1~5分）：</label>
    <select name="rating" id="rating" required>
        <option value="">请选择</option>
        <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?> 分</option>
        <?php endfor; ?>
    </select><br><br>

    <label for="content">评价内容：</label><br>
    <textarea name="content" id="content" rows="4" cols="70" required></textarea><br><br>

    <button type="submit">提交评价</button>
</form>

<?php if (isset($review_error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($review_error); ?></p>
<?php endif; ?>
<hr>


    <h3>学生评价</h3>
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="review">
                <h4><?php echo htmlspecialchars($review['name']); ?> 同学</h4>
                <p>评分：<?php echo str_repeat("⭐", $review['rating']); ?>（<?php echo $review['rating']; ?>/5）</p>
                <p><?php echo nl2br(htmlspecialchars($review['content'])); ?></p>
                <p style="color: gray;">时间：<?php echo $review['review_time']; ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>暂无评论。</p>
    <?php endif; ?>

    <a href="search_book.php" class="back-link">⬅ 返回图书查询</a>
</div>

</body>
</html>
