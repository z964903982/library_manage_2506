<?php
// 文件：php/login.php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id']);
    $password = trim($_POST['password']);

    // 先查 student 表
$stmt = $conn->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$student_result = $stmt->get_result();

if ($student_result->num_rows === 1) {
    $user = $student_result->fetch_assoc();

    if ($password === $user['password']) {

        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = 'student';
        $_SESSION['name'] = $user['name'];
        $_SESSION['status'] = $user['status']; 

        echo "<script>alert('登录成功！'); window.location.href='../php/student_dashboard.php';</script>";
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => '密码错误']);
        exit();
    }
}

    }

    // 再查 admin 表
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $admin_result = $stmt->get_result();

    if ($admin_result->num_rows === 1) {
        $user = $admin_result->fetch_assoc();
        if ($password === $user['password']) {

            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = $user['name'];
            $_SESSION['permission'] = $user['permission'];

            echo "<script>alert('登录成功！'); window.location.href='../php/admin_dashboard.php';</script>";

            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => '密码错误']);
            exit();
        }
    }

    // 都没找到
    echo json_encode(['status' => 'error', 'message' => '用户不存在']);
    exit();

?>
