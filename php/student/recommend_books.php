<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
require_once '../db_connect.php';

$student_id = $_SESSION['user_id'];

// 获取学生状态、最大借阅数、当前借阅数
$student_status = '未知';
$max_borrow = 0;
$current_borrow = 0;
$info_stmt = $conn->prepare("SELECT status, max_borrow, current_borrow FROM student WHERE student_id = ?");
$info_stmt->bind_param("s", $student_id);
$info_stmt->execute();
$info_result = $info_stmt->get_result()->fetch_assoc();
if ($info_result) {
    $student_status = $info_result['status'];
    $max_borrow = $info_result['max_borrow'];
    $current_borrow = $info_result['current_borrow'];
}

// 处理借阅请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_isbn'])) {
    $isbn = $_POST['borrow_isbn'];
    $status_stmt = $conn->prepare("SELECT status FROM student WHERE student_id = ?");
    $status_stmt->bind_param("s", $student_id);
    $status_stmt->execute();
    $student_status = $status_stmt->get_result()->fetch_assoc()['status'] ?? '未知';

    $book_stmt = $conn->prepare("SELECT status, available_copies FROM book WHERE ISBN = ?");
    $book_stmt->bind_param("s", $isbn);
    $book_stmt->execute();
    $book = $book_stmt->get_result()->fetch_assoc();

    if ($student_status !== '正常') {
        $error = "账户状态为“$student_status”，无法借阅。";
    } elseif (!$book || $book['status'] !== '在架' || $book['available_copies'] <= 0) {
        $error = "图书状态异常或无可借副本。";
    } elseif ($current_borrow >= $max_borrow) {
        $error = "你已达最大借阅数量（$max_borrow 本）。";
    } else {
        $borrow_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+30 days'));
        $insert = $conn->prepare("INSERT INTO borrowrecord (ISBN, student_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, '借出')");
        $insert->bind_param("ssss", $isbn, $student_id, $borrow_date, $due_date);
        if ($insert->execute()) {
            $success = "借阅成功！请于 $due_date 前归还。";
            $current_borrow++;
        } else {
            $error = "借阅失败，请稍后再试。";
        }
    }
}

// 处理预约请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve_isbn'])) {
    $isbn = $_POST['reserve_isbn'];

    $status_stmt = $conn->prepare("SELECT status FROM student WHERE student_id = ?");
    $status_stmt->bind_param("s", $student_id);
    $status_stmt->execute();
    $student_status = $status_stmt->get_result()->fetch_assoc()['status'] ?? '未知';

    if ($student_status !== '正常') {
        $error = "账户状态为“$student_status”，无法预约。";
    } else {
        $check_stmt = $conn->prepare("SELECT status FROM reservation WHERE ISBN = ? AND student_id = ? ORDER BY reserve_time DESC LIMIT 1");
        $check_stmt->bind_param("ss", $isbn, $student_id);
        $check_stmt->execute();
        $res = $check_stmt->get_result();

        $allow_insert = true;
        if ($res->num_rows > 0) {
            $last_status = $res->fetch_assoc()['status'];
            if ($last_status === '等待') {
                $error = "你已预约该书，请勿重复。";
                $allow_insert = false;
            }
        }

        if ($allow_insert) {
            $reserve_stmt = $conn->prepare("INSERT INTO reservation (ISBN, student_id, reserve_time, status) VALUES (?, ?, NOW(), '等待')");
            $reserve_stmt->bind_param("ss", $isbn, $student_id);
            if ($reserve_stmt->execute()) {
                $success = "预约成功，请耐心等待通知。";
            } else {
                $error = "预约失败，请稍后重试。";
            }
        }
    }
}

// 查询热门推荐图书（使用 popularbooks 视图）
$sql = "SELECT b.*, pb.borrow_count FROM popularbooks pb JOIN book b ON pb.ISBN = b.ISBN ORDER BY pb.borrow_count DESC LIMIT 10";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>图书推荐</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/search_book.css">
</head>
<body>

<h2>热门图书推荐</h2>

<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

<div class="book-container">
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="book-card">
        <img src="<?php echo htmlspecialchars($row['cover_image'] ?? '../images/default.jpg'); ?>" alt="封面">
        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><strong>作者：</strong><?php echo htmlspecialchars($row['author']); ?></p>
        <p><strong>分类：</strong><?php echo htmlspecialchars($row['category']); ?></p>
        <p><strong>ISBN：</strong><?php echo htmlspecialchars($row['ISBN']); ?></p>
        <p><strong>状态：</strong><?php echo htmlspecialchars($row['status']); ?></p>
        <p><strong>借阅次数：</strong><?php echo $row['borrow_count']; ?></p>
        <p><strong>可借/总藏：</strong><?php echo $row['available_copies'] . "/" . $row['total_copies']; ?></p>
        <p><strong>简介：</strong><?php echo mb_substr($row['description'], 0, 50) . "..."; ?></p>

        <a href="book_detail.php?ISBN=<?php echo urlencode($row['ISBN']); ?>">查看详情</a>

        <?php if ($student_status !== '正常'): ?>
            <p style="color: gray;">账户异常，无法操作</p>
        <?php elseif ($current_borrow >= $max_borrow): ?>
            <p style="color: gray;">已达最大借阅数（<?php echo $max_borrow; ?>）</p>
        <?php elseif ($row['status'] === '在架' && $row['available_copies'] > 0): ?>
            <form method="post" style="margin-top: 10px;">
                <input type="hidden" name="borrow_isbn" value="<?php echo htmlspecialchars($row['ISBN']); ?>">
                <button type="submit">借阅图书</button>
            </form>
        <?php elseif ($row['status'] === '借出'): ?>
            <form method="post" style="margin-top: 10px;">
                <input type="hidden" name="reserve_isbn" value="<?php echo htmlspecialchars($row['ISBN']); ?>">
                <button type="submit">预约图书</button>
            </form>
        <?php else: ?>
            <p style="color: gray;">暂不可借</p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
