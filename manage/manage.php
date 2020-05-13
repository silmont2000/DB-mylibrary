<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
session_start();
//如果不是从login进入，提示非法访问
if (!isset($_SESSION['USER'])) {
    die("非法访问" . $_SESSION['USER']);
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>后台-折工大学图书馆</title>
    <script src="../jquery/jquery-1.10.2.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../search/search.css"/>
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="manage.css"/>

</head>
<body>
<!--面包屑导航-->
<nav aria-label="breadcrumb" style="position: relative; z-index:9999">
    <ol class="breadcrumb" style="background-color: rgba(255,255,255,0.9)">
        <li class="breadcrumb-item"><a href="../welcome/login.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manage</li>
        <div style="position: relative;left: 80%">Welcome, manager <?php echo $_SESSION['USER'] ?></div>
    </ol>
</nav>
<!--左侧的栏目框-->
<div id="column_frame">
    <!--    空元素控制布局-->
    <div class="blank_block"></div>

    <!--    查询条件表单-->
    <form method="post" action="manage.php">
        <p><label class="column_name">借书管理</label>
            <label><input placeholder="借书证" class="column_input" style="width: 95px" type="text" name="cno1"/> </label>
            <label><input placeholder="书号" class="column_input" type="text" style="width: 95px" name="bno1"/> </label>
        </p>
        <p><label class="column_name">还书管理</label>
            <label><input placeholder="借书证" class="column_input" style="width: 95px" type="text" name="cno2"/> </label>
            <label><input placeholder="书号" class="column_input" style="width: 95px" type="text" name="bno2"/> </label>
        </p>
        <p><label class="column_name">借书查询</label> <label>
                <input placeholder="借书证号" class="column_input" style="width: 200px" type="text" name="cno3"/> </label>
        </p>
        <button class="button" type="submit" name="submit">操作</button>
    </form>

    <!--    管理跳转接口-->
    <a href="card_manage.php" class="button">借书证管理</a>
    <a href="book_manage.php" class="button">图书入库</a>
    <a href="../welcome/login.php" class="button">退出系统</a>
</div>

<!--右侧查询结果框-->
<div id="result_frame" style="overflow: auto; position: fixed; right: 3%; top: 0; bottom: 0;">
    <div class="blank_block"></div>
    <?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'manager');
    define('DB_PASS', 'manage');
    define('DB_NAME', 'my_lib');
    define('DB_MANAGE', $_SESSION['USER']);

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
//        echo "succeed";//self check
    }

    $cno1 = $_POST['cno1'];//借书变量
    $bno1 = $_POST['bno1'];
    $cno2 = $_POST['cno2'];//还书变量
    $bno2 = $_POST['bno2'];
    $cno3 = $_POST['cno3'];//查询借书情况的卡号

    if (!empty($cno1)) {//如果是借书
//        echo "self check:借书<br>";
//        echo "$cno1<br>";
//        查询历史借书情况
        $borrow_history_sql = "select distinct  * from borrow natural join book where cno= '$cno1' ORDER BY borrow_date DESC ";
//        事务声明
        $transaction_sql = "START TRANSACTION";
//        插入一条借书记录,如果失败说明违反外键约束，要么没这本书要么卡号错了要么管理员错了（这不太可能）
        $first_sql = "INSERT into borrow VALUES('$cno1','$bno1',now(),null," . DB_MANAGE . ")";
//        更改库存情况。失败说明库存为零，没有这本书的报错不会发生在这里，因为上面已经错了，所以不会有影响行数为零对应的情况。
        $second_need_check_sql = "update book set stock = stock-1 where bno = '$bno1'";
//        echo "affect_rows是" . $affect_rows . "<br>";//self check
    } elseif (!empty($cno2)) {//如果是还书
//        echo "self check:还书<br>";
//        echo "$cno2<br>";
        $borrow_history_sql = "select distinct * from borrow natural join book where cno= '$cno2' ORDER BY borrow_date DESC";
        $transaction_sql = "START TRANSACTION";
//        先更新书目情况
        $first_sql = "update book set stock = stock+1 where bno = '$bno2'";
//        更新借书情况，如果影响行数为0，说明没借这本书
        $second_need_check_sql = "update borrow set return_date = now() where return_date is null and bno = '$bno2' and cno = '$cno2' ";
        $affect_rows = mysqli_affected_rows($conn);
    } elseif (!empty($cno3)) {//查询
//        echo "self check:查询<br>";
//        echo "$cno3<br>";
        $borrow_history_sql = "select distinct * from borrow natural join book where cno= '$cno3' ORDER BY borrow_date DESC";
        $borrow_total = "select count(bno) from borrow where cno='$cno3'";
        $un_returned_total = "select count(bno) from borrow where cno='$cno3' and return_date is null";

        $err_total = mysqli_query($conn, $borrow_total);
        $err_un_returned = mysqli_query($conn, $un_returned_total);
        $_total = mysqli_fetch_array($err_total)[0];
        $_unreturned = mysqli_fetch_array($err_un_returned)[0];
//        self check
//        echo $_total . "<br>";
//        echo $_unreturned . "<br>";
        echo "
            <div class=\"card text-center\">
                <div class=\"card-header\">
                借书统计
                </div>
                <div class=\"card-body\">
                <h5 class=\"card-title\">$cno3 的借阅统计</h5>
                <p class=\"card-text\">您总共借阅了 $_total 本书</p>
                <p class=\"card-text\">您还有 $_unreturned 本书待归还</p>
                </div>
                <div class=\"card-footer text-muted\">
                    求  是  创  新
                </div>
            </div>";
    }

    //    echo "self check<br>";
    //    echo "<strong>borrow_history_sql是</strong>" . $borrow_history_sql . "<br>";
    //    echo "<strong>first_sql是</strong>" . $first_sql . "<br>";
    //    echo "<strong>second_need_check_sql是</strong>" . $second_need_check_sql . "<br>";

    //    启动事务
    if (isset($transaction_sql))
        mysqli_query($conn, $transaction_sql);

    //    mysqli_query失败时返回 FALSE，成功执行SELECT, SHOW, DESCRIBE或 EXPLAIN查询会返回一个mysqli_result 对象
    //      其他查询则返回TRUE
    $err_first = mysqli_query($conn, $first_sql);
    $err_second = mysqli_query($conn, $second_need_check_sql);
    //    注意！这里不要放在前面。query执行后才是查询了。
    $affect_rows = mysqli_affected_rows($conn);
    $err_history = mysqli_query($conn, $borrow_history_sql);

    //    分析可能的失败：first对应先行处理有误，second对应库存为零，affect rows对应没借过这本书、没有这本书。
    //    其实affect rows的错和second是连着的，而且要错一定是还书，所以在下面这个if else也可以不判断affect rows的情况。
    if (!$err_second || !$err_first || !$affect_rows) {
//      借书时：先行处理有误、没库存、没这本书
        if (!empty($cno1)) {
            if (!$err_first) {
                echo "<div><div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                            <span aria-hidden=\"true\">&times;</span></button>
                            <strong>插入借书记录操作失败! </strong> <br>请检查：卡号是否存在；本校区图书馆是否藏有本书.</div></div>";
            } elseif ((!$err_second)) {
//                查询最后的还书记录
                $latest_date_sql = "SELECT return_date FROM borrow WHERE bno ='$bno1' ORDER by return_date DESC LIMIT 1;";
                $err_date = mysqli_query($conn, $latest_date_sql);
                $date_result = mysqli_fetch_array($err_date)[0];
                echo "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                            <span aria-hidden=\"true\">&times;</span></button>
                            <strong>操作失败! 书库库存量为零.</strong> <br>最近归还时间： $date_result. 很遗憾！</div>";
            }
        } //        还书：先行处理有误、没借过这本书、书的库存满了（有误）
        elseif (!empty($cno2)) {
            echo "
            <div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                    <span aria-hidden=\"true\">&times;</span></button>
                <strong>操作失败! </strong> <br>请检查：借书卡是否正确；持卡人是否持有本书；书库库存量是否登记有误.
            </div>";
        }
//        有失误，全部回滚
        mysqli_query($conn, "ROLLBACK");
    } else {
//        没有失误
        echo "
            <div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                    <span aria-hidden=\"true\">&times;</span></button>
                <strong>操作成功! </strong>
        </div>";
        mysqli_query($conn, "commit");
    }
    //打印新的表格
    output_borrow_join_book($err_history);
    mysqli_close($conn);
    function output_borrow_join_book($query)
    {
        echo "<table class=\"table table-hover\">
  <thead>
    <tr>
      <th scope=\"col\">编号</th>
      <th scope=\"col\">类别</th>
      <th scope=\"col\">名称</th>
      <th scope=\"col\">出版社</th>
      <th scope=\"col\">年份</th>
      <th scope=\"col\">作者</th>
      <th scope=\"col\">价格</th>
      <th scope=\"col\">库存</th>
      <th scope=\"col\">还期</th>
    </tr>
  </thead>";
        while ($result = mysqli_fetch_array($query)) {
            echo "<tbody>";
            echo "<tr > ";
            echo "<td > " . $result[0] . "</td > ";
            echo "<td > " . $result[5] . "</td > ";
            echo "<td > " . $result[6] . "</td > ";
            echo "<td > " . $result[7] . "</td > ";
            echo "<td > " . $result[8] . "</td > ";
            echo "<td > " . $result[9] . "</td > ";
            echo "<td > " . $result[10] . "</td > ";
            echo "<td > " . $result[12] . "</td > ";
            echo "<td > " . $result[3] . "</td > ";
            echo "</tr > ";
        }
        echo "  </tbody></table>";
    }

    ?>
</div>
<div style="height: 15 %"></div>
</body>
</html>
