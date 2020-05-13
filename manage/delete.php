<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'manager');
define('DB_PASS', 'manage');
define('DB_NAME', 'my_lib');
define('DB_MANAGE', '1001');


$m_cno = $_GET['cno'];
$func = $_GET['function'];

if ($func = "my_delete") {
    echo call_user_func('my_del', $m_cno);
}

function my_del($my_cno)
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
//        echo "connect" . $my_cno;
    }
    $delete_sql = "delete from card where cno = '$my_cno' and not exists (select *from borrow where return_date is null and borrow.cno = '$my_cno')";
    $err2 = mysqli_query($conn, $delete_sql);
    if (!$err2 || !mysqli_affected_rows($conn)) {// 失败了
//        echo $delete_sql;
        return 1;
    } else {// 成功了
//        echo $delete_sql;
        return 0;
    }
}

?>