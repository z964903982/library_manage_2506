<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
require_once '../db_connect.php';

$success = $error = "";

// 查询学生
$search_id = $_GET['search_id'] ?? '';
$students = [];

if (!empty($search_id)) {
    $stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
    $stmt->bind_param("s", $search_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
} else {
    $result = $conn->query("SELECT * FROM student ORDER BY student_id ASC");
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// 添加或修改
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $department = $_POST['department'];
    $major = $_POST['major'];
    $grade = $_POST['grade'];
    $class = $_POST['class'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $max_borrow = $_POST['max_borrow'];
    $current_borrow = $_POST['current_borrow'];
    $password = $_POST['password'];

    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO student (student_id, name, gender, department, major, grade, class, contact, email, status, max_borrow, current_borrow, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssiis", $student_id, $name, $gender, $department, $major, $grade, $class, $contact, $email, $status, $max_borrow, $current_borrow, $password);
        if ($stmt->execute()) {
            $success = "添加成功！";
        } else {
            $error = "添加失败：" . $stmt->error;
        }
    } elseif ($action === 'edit') {
        $stmt = $conn->prepare("UPDATE student SET name=?, gender=?, department=?, major=?, grade=?, class=?, contact=?, email=?, status=?, max_borrow=?, current_borrow=?, password=? WHERE student_id=?");
        $stmt->bind_param("sssssssssiiss", $name, $gender, $department, $major, $grade, $class, $contact, $email, $status, $max_borrow, $current_borrow, $password, $student_id);
        if ($stmt->execute()) {
            $success = "修改成功！";
        } else {
            $error = "修改失败：" . $stmt->error;
        }
    }
}

// 删除
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
    $stmt->bind_param("s", $delete_id);
    if ($stmt->execute()) {
        $success = "删除成功！";
    } else {
        $error = "删除失败：" . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>学生账户管理</title>
</head>
<body>
    <h2>学生账户管理</h2>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <h3>查询学生（按学号）</h3>
    <form method="get">
        学号：<input type="text" name="search_id" value="<?php echo htmlspecialchars($search_id); ?>">
        <button type="submit">查询</button>
        <a href="manage_students.php">重置</a>
    </form>

    <h3>添加学生</h3>
    <form method="post">
        <input type="hidden" name="action" value="add">
        学号：<input name="student_id" required>
        姓名：<input name="name" required>
        性别：<select name="gender">
        <option value="女">女</option>
            <option value="男">男</option>
            
        </select>
        院系：<input name="department">
        专业：<input name="major">
        年级：<input name="grade">
        班级：<input name="class">
        联系方式：<input name="contact">
        邮箱：<input name="email">
        状态：<select name="status">
            <option value="正常">正常</option>
            <option value="冻结">冻结</option>
            <option value="挂失">挂失</option>
        </select>
        最大可借数：<input name="max_borrow" type="number" value="5">
        当前借阅数：<input name="current_borrow" type="number" value="0">
        密码：<input name="password" required>
        <button type="submit">添加</button>
    </form>

    <h3>学生列表</h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>学号</th><th>姓名</th><th>性别</th><th>院系</th><th>专业</th><th>年级</th><th>班级</th><th>联系方式</th><th>邮箱</th><th>状态</th><th>最大可借</th><th>当前借阅</th><th>密码</th><th>操作</th>
        </tr>
        <?php foreach ($students as $s): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($s['student_id']); ?>">
                <td><?php echo htmlspecialchars($s['student_id']); ?></td>
                <td><input name="name" value="<?php echo htmlspecialchars($s['name']); ?>"></td>
                <td>
                    <select name="gender">
                        <option value="男" <?php if ($s['gender'] === '男') echo 'selected'; ?>>男</option>
                        <option value="女" <?php if ($s['gender'] === '女') echo 'selected'; ?>>女</option>
                    </select>
                </td>
                <td><input name="department" value="<?php echo htmlspecialchars($s['department']); ?>"></td>
                <td><input name="major" value="<?php echo htmlspecialchars($s['major']); ?>"></td>
                <td><input name="grade" value="<?php echo htmlspecialchars($s['grade']); ?>"></td>
                <td><input name="class" value="<?php echo htmlspecialchars($s['class']); ?>"></td>
                <td><input name="contact" value="<?php echo htmlspecialchars($s['contact']); ?>"></td>
                <td><input name="email" value="<?php echo htmlspecialchars($s['email']); ?>"></td>
                <td>
                    <select name="status">
                        <option value="正常" <?php if ($s['status'] === '正常') echo 'selected'; ?>>正常</option>
                        <option value="冻结" <?php if ($s['status'] === '冻结') echo 'selected'; ?>>冻结</option>
                        <option value="挂失" <?php if ($s['status'] === '挂失') echo 'selected'; ?>>挂失</option>
                    </select>
                </td>
                <td><input name="max_borrow" type="number" value="<?php echo $s['max_borrow']; ?>"></td>
                <td><input name="current_borrow" type="number" value="<?php echo $s['current_borrow']; ?>"></td>
                <td><input name="password" value="<?php echo htmlspecialchars($s['password']); ?>"></td>
                <td>
                    <button type="submit">保存</button>
                    <a href="?delete=<?php echo $s['student_id']; ?>" onclick="return confirm('确定删除该学生？')">删除</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
