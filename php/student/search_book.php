<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php';

// 当前学生基本信息
$student_id = $_SESSION['user_id'];
$student_status = '未知';  // 避免未定义
$max_borrow = 0;
$current_borrow = 0;

// 查询学生状态和借阅信息（供页面展示使用）
$student_info_stmt = $conn->prepare("SELECT status, max_borrow, current_borrow FROM student WHERE student_id = ?");
$student_info_stmt->bind_param("s", $student_id);
$student_info_stmt->execute();
$student_info = $student_info_stmt->get_result()->fetch_assoc();
if ($student_info) {
    $student_status = $student_info['status'] ?? '未知';
    $max_borrow = $student_info['max_borrow'] ?? 0;
    $current_borrow = $student_info['current_borrow'] ?? 0;
}

// 借阅逻辑处理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_isbn'])) {
    $isbn_to_borrow = $_POST['borrow_isbn'];

    // 动态重新获取最新学生状态
    $status_stmt = $conn->prepare("SELECT status FROM student WHERE student_id = ?");
    $status_stmt->bind_param("s", $student_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result()->fetch_assoc();
    $current_status = $status_result['status'] ?? '未知';

    // 查询图书状态
    $check = $conn->prepare("SELECT status, available_copies FROM book WHERE ISBN = ?");
    $check->bind_param("s", $isbn_to_borrow);
    $check->execute();
    $check_result = $check->get_result()->fetch_assoc();

    if ($current_status !== '正常') {
        $error = "你的账户状态为“$current_status”，无法借阅图书。";
    } elseif (!$check_result || $check_result['status'] !== '在架' || $check_result['available_copies'] <= 0) {
        $error = "图书不可借阅，可能已借空或状态异常。";
    } elseif ($current_borrow >= $max_borrow) {
        $error = "你已达到最大借阅数量（{$max_borrow} 本），请先归还部分图书后再借阅。";
    } else {
        // 插入借阅记录
$borrow_date = date('Y-m-d');

// 默认归还天数为30（防止没有设置）
$borrow_days = 30;

// 查询系统配置中归还天数
$days_sql = "SELECT config_value FROM systemconfig WHERE config_key = 'default_borrow_days'";

$days_result = $conn->query($days_sql);
if ($days_result && $row = $days_result->fetch_assoc()) {
    $borrow_days = intval($row['config_value']); // 转换为整数
}

// 计算归还日期
$due_date = date('Y-m-d', strtotime("+{$borrow_days} days"));

        $status = '借出';

        $insert = $conn->prepare("INSERT INTO borrowrecord (ISBN, student_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $isbn_to_borrow, $student_id, $borrow_date, $due_date, $status);

        if ($insert->execute()) {
            $success = "借阅成功！请于 $due_date 前归还。";
            // 可选：更新 current_borrow
            $current_borrow++; // 页面刷新前即时更新
        } else {
            $error = "借阅失败，请稍后再试。";
        }
    }
}

// 预约逻辑处理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve_isbn'])) {
    $isbn_to_reserve = $_POST['reserve_isbn'];

    // 重新获取学生状态
    $status_stmt = $conn->prepare("SELECT status FROM student WHERE student_id = ?");
    $status_stmt->bind_param("s", $student_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result()->fetch_assoc();
    $current_status = $status_result['status'] ?? '未知';

    if ($current_status !== '正常') {
        $error = "你的账户状态为“$current_status”，无法预约图书。";
    } else {
        // 检查是否已有“等待”状态的预约记录（唯一阻止重复预约的状态）
        $check_reserve = $conn->prepare("SELECT status FROM reservation WHERE ISBN = ? AND student_id = ? ORDER BY reserve_time DESC LIMIT 1");
        $check_reserve->bind_param("ss", $isbn_to_reserve, $student_id);
        $check_reserve->execute();
        $reserve_result = $check_reserve->get_result();

        if ($reserve_result->num_rows > 0) {
            $row = $reserve_result->fetch_assoc();
            if ($row['status'] === '等待') {
                $error = "你已经预约过此书，请勿重复预约。";
            } else {
                // 状态为“已通知”或“取消”，允许重新预约
                $insert_reserve = $conn->prepare("INSERT INTO reservation (ISBN, student_id, reserve_time, status) VALUES (?, ?, NOW(), '等待')");
                $insert_reserve->bind_param("ss", $isbn_to_reserve, $student_id);

                if ($insert_reserve->execute()) {
                    $success = "预约成功，届时会通知您。";
                } else {
                    $error = "预约失败，请稍后再试。";
                }
            }
        } else {
            // 从未预约过该书，允许预约
            $insert_reserve = $conn->prepare("INSERT INTO reservation (ISBN, student_id, reserve_time, status) VALUES (?, ?, NOW(), '等待')");
            $insert_reserve->bind_param("ss", $isbn_to_reserve, $student_id);

            if ($insert_reserve->execute()) {
                $success = "预约成功，届时会通知您。";
            } else {
                $error = "预约失败，请稍后再试。";
            }
        }
    }
}



// 处理查询条件
$title = $_GET['title'] ?? '';
$author = $_GET['author'] ?? '';
$category = $_GET['category'] ?? '';
$isbn = $_GET['isbn'] ?? '';

$sql = "SELECT * FROM book WHERE 1=1";
$params = [];

if (!empty($title)) {
    $sql .= " AND title LIKE ?";
    $params[] = "%$title%";
}
if (!empty($author)) {
    $sql .= " AND author LIKE ?";
    $params[] = "%$author%";
}
if (!empty($category)) {
    $sql .= " AND category LIKE ?";
    $params[] = "%$category%";
}
if (!empty($isbn)) {
    $sql .= " AND ISBN = ?";
    $params[] = $isbn;
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>图书查询与借阅</title>

    <link rel="stylesheet" href="../../css/search_book.css">
</head>
<body>

<h2 style="color: black;">欢迎你，<?php echo htmlspecialchars($_SESSION['name']); ?> 同学</h2>

<!-- 提示信息 -->
<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

<!-- 搜索栏 -->
<form method="get" class="search-box">
    <input type="text" name="title" placeholder="书名" value="<?php echo htmlspecialchars($title); ?>">
    <input type="text" name="author" placeholder="作者" value="<?php echo htmlspecialchars($author); ?>">
    <input type="text" name="category" placeholder="分类" value="<?php echo htmlspecialchars($category); ?>">
    <input type="text" name="isbn" placeholder="ISBN" value="<?php echo htmlspecialchars($isbn); ?>">
    <button type="submit">搜索</button>
</form>

<!-- 图书列表 -->
<div class="book-container">
<?php while ($row = $result->fetch_assoc()): ?>
    <div class="book-card">
        <img src="<?php echo htmlspecialchars($row['cover_image'] ?? '../images/default.jpg'); ?>" alt="封面">
        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><strong>作者：</strong><?php echo htmlspecialchars($row['author']); ?></p>
        <p><strong>分类：</strong><?php echo htmlspecialchars($row['category']); ?></p>
        <p><strong>ISBN：</strong><?php echo htmlspecialchars($row['ISBN']); ?></p>
        <p><strong>状态：</strong><?php echo htmlspecialchars($row['status']); ?></p>
        <p><strong>可借/总藏：</strong><?php echo $row['available_copies'] . "/" . $row['total_copies']; ?></p>
        <p><strong>简介：</strong><?php echo mb_substr($row['description'], 0, 50) . "..."; ?></p>

        <!-- 详情链接 -->
        <a href="book_detail.php?ISBN=<?php echo urlencode($row['ISBN']); ?>">查看详情</a>
<!-- 借阅或预约按钮 -->
<?php if ($student_status !== '正常'): ?>

    <p style="color: gray;">账户状态异常，禁止借阅或预约</p>
<?php elseif ($current_borrow >= $max_borrow): ?>
    <p style="color: gray;">你已达到最大借阅数量（<?php echo $max_borrow; ?> 本）</p>
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
