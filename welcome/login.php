<?php
session_start();
?>
<html lang="en">
<head>
    <!--    标题-->
    <meta charset="UTF-8">
    <title>登录-折工大学图书馆</title>

    <!--    css和js链接-->
    <script src="../jquery/jquery-1.10.2.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="welcome.css"/>
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
</head>

<body>
<div id="login_frame">

    <!--    slogan-->
    <div id="slogan">
        <p style="font-size: 300%; letter-spacing: 0; color: #403d40; font-family: 超世纪粗毛楷,sans-serif" ;>
            折工大學圖書館</p>
    </div>

    <!--    管理员入口-->
    <div id="manager_login">
        <p style="font-size: 25px; letter-spacing: 10px; color: #403d40; font-family: 超世纪粗毛楷, sans-serif">管理員入口</p>
    </div>
    <!--    用户和密码输入，表单-->
    <form method="post" action="login.php">
        <div class="form-group" style="width: 50%; margin: auto">
            <label for="exampleInputEmail1" style="float: left">管理員ID</label>
            <input type="text" class="form-control" name="username" id="exampleInputEmail1" placeholder="ID" required>
        </div>
        <div class="form-group" style="width: 50%; margin: auto">
            <label for="exampleInputPassword1" style="float: left">密碼</label>
            <input type="password" class="form-control" name="password" id="exampleInputPassword1"
                   placeholder="Password" required>
        </div>
        <!--        表单采集结束-->
        <!--        管理员登录 提交表单-->
        <div id="login_control">
            <button type="submit" class="btn btn-primary" name="submit" style="width: 15%">登錄</button>
            <a id="forget_pwd" href="../forget_pwd.html">忘記密碼？</a>
        </div>
    </form>

    <!--    登陆检查-->
    <?php
    //    不输出notice和warning
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

    //    连接数据库变量以及post接收变量定义
    define('DB_HOST', 'localhost');
    define('DB_USER', 'manager');
    define('DB_PASS', 'manage');
    define('DB_NAME', 'my_lib');
    $username = $_POST['username'];
    $password = $_POST['password'];

    //      连接数据库
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        echo("connect succeed");//self check
    }

    //检测用户名及密码是否正确
    $uprequest_sql = "select id from manager where id=$username and pw = $password";//申请检查username和password
    $uprequest_query = mysqli_query($conn, $uprequest_sql);//发送数据库请求
    if ($result = mysqli_fetch_array($uprequest_query, MYSQLI_NUM)) {
        //登录成功，全局保存用户信息，跳转
        $_SESSION['USER'] = $username;
        header("Location: ../manage/manage.php");
    } else {
        //登录失败
        if ($_POST['username'] != null)//防止打开界面就弹框，必须检查是否已经填写表单
            echo "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
  <button type = \"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
  <strong>登錄失敗!</strong> 請檢查ID和密碼.</div>";
    }
    ?>

    <!--    查询图书入口-->
    <div id="reader_login">
        <p style="font-size: 25px; letter-spacing: 10px; color: #403d40; font-family: 超世纪粗毛楷, sans-serif">讀者入口</p>
    </div>
    <div class="login_control">
        <!--        <a  href="#" role="button">Link</a>-->
        <a class="btn btn-primary" href="../search/search.php" style="width: 20%">圖書查詢</a>
    </div>

</div>

</body>
</html>


