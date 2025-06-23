<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connect.php';
$student_id = $_SESSION['user_id'];

// 处理归还
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $borrow_id = $_POST['return_id'];
    $today = date('Y-m-d');

    $update_return = $conn->prepare("UPDATE borrowrecord SET return_date = ?, status = '已还' WHERE borrow_id = ? AND student_id = ?");
    $update_return->bind_param("sis", $today, $borrow_id, $student_id);
    if ($update_return->execute()) {
        $success = "归还成功！";
    } else {
        $error = "归还失败，请稍后再试。";
    }
}

// 处理续借
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew_id'])) {
    $borrow_id = $_POST['renew_id'];

    // 查询当前续借次数
    $check = $conn->prepare("SELECT renew_count, due_date FROM borrowrecord WHERE borrow_id = ? AND student_id = ?");
    $check->bind_param("is", $borrow_id, $student_id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if (!$res) {
        $error = "未找到借阅记录。";
    } elseif ($res['renew_count'] >= 3) {
        $error = "续借失败，最多可续借3次。";
    } else {
        $new_due = date('Y-m-d', strtotime($res['due_date'] . ' +7 days'));
        $new_count = $res['renew_count'] + 1;

        $renew = $conn->prepare("UPDATE borrowrecord SET due_date = ?, renew_count = ?, status = '逾期' WHERE borrow_id = ? AND student_id = ?");
        $renew->bind_param("sisi", $new_due, $new_count, $borrow_id, $student_id);
        if ($renew->execute()) {
            $success = "续借成功！新截止日期为 $new_due";
        } else {
            $error = "续借失败，请稍后再试。";
        }
    }
}

// 查询所有借阅记录
$records = $conn->prepare("SELECT br.*, b.title, b.author FROM borrowrecord br JOIN book b ON br.ISBN = b.ISBN WHERE br.student_id = ? ORDER BY br.borrow_date DESC");
$records->bind_param("s", $student_id);
$records->execute();
$result = $records->get_result();

$borrowed = [];
$returned = [];

while ($row = $result->fetch_assoc()) {
    if ($row['status'] === '已还') {
        $returned[] = $row;
    } else {
        $borrowed[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>我的借阅记录</title>
    <link rel="stylesheet" href="../../css/my_borrow.css">
    <style>
        .record-section { margin-bottom: 30px; }
        .record-card {
            border: 1px solid #ccc; padding: 15px; margin: 10px 0;
            border-radius: 10px; background: #f9f9f9;
        }
        .record-card p { margin: 5px 0; }
        .btns form { display: inline; margin-left: 10px; }
    </style>
</head>
<body>

<h2>你好，<?php echo htmlspecialchars($_SESSION['name']); ?> 同学</h2>

<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

<!-- 未归还 -->
<div class="record-section">
    <h3>未归还图书</h3>
    <?php if (empty($borrowed)): ?>
        <p>暂无未归还图书</p>
    <?php else: ?>
        <?php foreach ($borrowed as $r): ?>
            <div class="record-card">
                <p><strong>书名：</strong><?php echo htmlspecialchars($r['title']); ?></p>
                <p><strong>作者：</strong><?php echo htmlspecialchars($r['author']); ?></p>
                <p><strong>借阅日期：</strong><?php echo $r['borrow_date']; ?></p>
                <p><strong>应还日期：</strong><?php echo $r['due_date']; ?></p>
                <p><strong>续借次数：</strong><?php echo $r['renew_count']; ?>/3</p>
                <p><strong>状态：</strong><?php echo $r['status']; ?></p>
                <div class="btns">
                    <form method="post">
                        <input type="hidden" name="return_id" value="<?php echo $r['borrow_id']; ?>">
                        <button type="submit">归还</button>
                    </form>
                    <form method="post">
                        <input type="hidden" name="renew_id" value="<?php echo $r['borrow_id']; ?>">
                        <button type="submit" <?php echo $r['renew_count'] >= 3 ? 'disabled' : ''; ?>>续借7天</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- 已归还 -->
<div class="record-section">
    <h3>已归还图书</h3>
    <?php if (empty($returned)): ?>
        <p>暂无归还记录</p>
    <?php else: ?>
        <?php foreach ($returned as $r): ?>
            <div class="record-card">
                <p><strong>书名：</strong><?php echo htmlspecialchars($r['title']); ?></p>
                <p><strong>作者：</strong><?php echo htmlspecialchars($r['author']); ?></p>
                <p><strong>借阅日期：</strong><?php echo $r['borrow_date']; ?></p>
                <p><strong>应还日期：</strong><?php echo $r['due_date']; ?></p>
                <p><strong>归还日期：</strong><?php echo $r['return_date']; ?></p>
                <p><strong>状态：</strong><?php echo $r['status']; ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
