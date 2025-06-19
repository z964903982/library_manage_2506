<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $gender = $_POST['gender'];
    $department = trim($_POST['department']);
    $major = trim($_POST['major']);
    $grade = trim($_POST['grade']);
    $class = trim($_POST['class']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "<script>alert('两次密码不一致'); history.back();</script>";
        exit();
    }

    // 检查学号是否已注册
    $stmt = $conn->prepare("SELECT student_id FROM student WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('该学号已注册'); history.back();</script>";
        exit();
    }

    $hashed_password = $password;  // 明文密码

// 进行插入数据操作
    $stmt = $conn->prepare("INSERT INTO student 
        (student_id, name, gender, department, major, grade, class, contact, email, status, max_borrow, current_borrow, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '正常', 5, 0, ?)");

    $stmt->bind_param("ssssssssss", $student_id, $name, $gender, $department, $major, $grade, $class, $contact, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('注册成功！请登录'); window.location.href='../html/login.html';</script>";
    } else {
        echo "<script>alert('注册失败，请稍后再试'); history.back();</script>";
    }
}
?>
