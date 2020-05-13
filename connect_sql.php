<?php
//页面字符编码
header("Content-type:text/html;charset=utf-8");
//隐藏报错信息
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

//数据库地址
$host = "localhost";
//数据库账号
$username = "maneger";
//数据库密码
$password = "manage";
//数据库名
$db = "my_lib";
//数据库表名
$tb = "book";

//连接数据库
$con = mysqli_connect($host, $username, $password, $db);
if (!$con) {
    die('连接数据库失败，失败原因：' . mysqli_error());
}
//设置数据库字符集
mysqli_query("SET NAMES UTF8");
//查询数据库
mysqli_select_db($db, $con);
//获取数据
$result = mysqli_query("SELECT * FROM $tb ORDER BY id ASC");
while ($row = mysqli_fetch_array($result)) {
    echo "<li>" . $row[title] . "</li>";
    echo "<br/>";
}
?>