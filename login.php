<?php
session_start(); // 启动会话

// 检查表单是否提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 从表单中获取用户名和密码
	// 测试账号：test1
	// 测试密码：test1
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 验证输入是否为空
    if (empty($username) || empty($password)) {
        $error_message = "用户名和密码不能为空。";
    } else {
        // 连接到数据库
        $conn = new mysqli('localhost', 'db_user', 'db_password', 'db_name');

        // 检查连接
        if ($conn->connect_error) {
            die("数据库连接失败: " . $conn->connect_error);
        }

        // 查询用户
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // 验证密码（假设密码是明文保存的，建议使用哈希处理）
            if (password_verify($password, $user['password'])) {
                // 设置会话
                $_SESSION['username'] = $username;
                header("Location: welcome.php"); // 重定向到欢迎页面
                exit();
            } else {
                $error_message = "用户名或密码错误。";
            }
        } else {
            $error_message = "用户名或密码错误。";
        }

        // 关闭连接
        $stmt->close();
        $conn->close();
		//flag{DO_NOT_SAVE_PASSWORD_IN_CODE_FILES}
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录页面</title>
	
</head>
<body>
    <h2>登录</h2>
    <?php
    if (!empty($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="post" action="login.php">
        <label for="username">用户名:</label>
        <input type="text" id="username" name="username" required>
        <br><br>
        <label for="password">密码:</label>
        <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit">登录</button>
    </form>
</body>
</html>
