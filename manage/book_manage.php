<!--非法访问检查-->
<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
session_start();
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
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css"/>
</head>

<body>
<nav aria-label="breadcrumb" style="position: relative; z-index:9999">
    <ol class="breadcrumb" style="background-color: rgba(255,255,255,0.9)">
        <li class="breadcrumb-item"><a href="../welcome/login.php">Home</a></li>
        <li class="breadcrumb-item"><a href="manage.php">Manage</a></li>
        <li class="breadcrumb-item active" aria-current="page">Book Manage</li>
        <div style="position: relative;left: 73%">Welcome, manager <?php echo $_SESSION['USER'] ?></div>

    </ol>
</nav>

<div id="result_frame" style="width: 80%; left: 10%; right: 10%;overflow: auto;padding: 10%">
    <form method="post" action="book_manage.php">
        <div class="form-row">
            <label for="single" class="col-sm-2 col-form-label"
                   style="background-color: #416082; color:white; border-radius: 5px">单本录入</label>
            <div class="col-md-4">
                <input type="text" class="form-control" name="bno" placeholder="书号">
            </div>
            <button type="submit" class="btn btn-primary" style="float:right">确定</button>
        </div>

        <div style="height: 10%"></div>
        <div class="form-row">
            <div class="form-group col-md-2">
                <input type="text" class="form-control" name="category" placeholder="类别">
            </div>
            <div class="form-group col-md-6">
                <input type="text" class="form-control" name="title" placeholder="书名">
            </div>
            <div class="form-group col-md-4">
                <input type="text" class="form-control" name="press" placeholder="出版社">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <input type="text" class="form-control" name="year" placeholder="年份">
            </div>
            <div class="form-group col-md-3">
                <input type="text" class="form-control" name="author" placeholder="作者">
            </div>
            <div class="form-group col-md-3">
                <input type="text" class="form-control" name="price" placeholder="价格">
            </div>
            <div class="form-group col-md-3">
                <input type="text" class="form-control" name="total" placeholder="数量">
            </div>
        </div>
    </form>

    <form action="file_insert.php" method="post" enctype="multipart/form-data">
        <!--    <form>-->
        <div class="form-row">
            <label for="pile" class="col-sm-2 col-form-label"
                   style="background-color: #416082; color:white; border-radius: 5px; height: 40px">批量录入</label>
            <div class="form-group">
                <input type="file" class="form-control-file" style="margin-left: 5%; margin-top: 2%"
                       id="book_file" name="book">
            </div>

            <button type="submit" class="btn btn-primary" style="margin-left: 1%; float:right; height: 40px ">确定
            </button>
        </div>
    </form>
    <div id="window_position"></div>
    <?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'manager');
    define('DB_PASS', 'manage');
    define('DB_NAME', 'my_lib');
    define('DB_MANAGE', '1001');

    $bno = $_POST['bno'];
    $category = $_POST['category'];
    $title = $_POST['title'];
    $press = $_POST['press'];
    $year = $_POST['year'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $total = $_POST['total'];
    //    insert into book values('00013', '计算机', 'CG从入门到放弃', '黄河HUANGHE大学出版社', 2016, '郭扬州',9.80, 5, 3);
    // Create connection
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // Check connection
    if (!$conn) {
        die("数据库未连接" . mysqli_connect_error());
    }

    $check = "select * from book where bno = '$bno' and title = '$title'";
    $err_check = mysqli_query($conn, $check);
    $OUTPUT = mysqli_query($conn, $check);
    $result_check = mysqli_fetch_array($err_check);
    if ($result_check != null) {
        $new_sql = "update book set total = total + $total, stock = stock+$total where bno = '$bno'";
    } else {
        $new_sql = "insert into book values ('$bno','$category','$title','$press',$year,'$author',$price,$total,$total)";
    }
    echo $new_sql . "<br>";
    //    echo $result_check[0] . "<br>";
    $new_err = mysqli_query($conn, $new_sql);
    if ($bno != '') {
        if (!$new_err) {
            echo "<div><div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                <span aria-hidden=\"true\">&times;</span></button><strong>更新失败! 请重新分配主键。</strong></div></div>";
        } else {
            echo "<div><div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                <span aria-hidden=\"true\">&times;</span></button><strong>更新成功! 刷新显示本次更新图书信息：</strong></div></div>";
            output_book($OUTPUT);
        }
    }

    mysqli_close($conn);

    function output_book($query)
    {
        echo "<table class=\"table table-hover\">
        <thead><tr>
            <th scope=\"col\">编号</th>
            <th scope=\"col\">类别</th>
            <th scope=\"col\">名称</th>
            <th scope=\"col\">出版社</th>
            <th scope=\"col\">年份</th>
            <th scope=\"col\">作者</th>
            <th scope=\"col\">价格</th>
            <th scope=\"col\">总量</th>
            <th scope=\"col\">库存</th></tr>
        </thead>";

        while ($row = mysqli_fetch_array($query)) {
            echo "<tbody>";
            echo "<tr > ";
            echo "<td>" . $row[0] . "</td>";
            echo "<td>" . $row[1] . "</td>";
            echo "<td>" . $row[2] . "</td>";
            echo "<td>" . $row[3] . "</td>";
            echo "<td>" . $row[4] . "</td>";
            echo "<td>" . $row[5] . "</td>";
            echo "<td>" . $row[6] . "</td>";
            echo "<td>" . $row[7] . "</td>";
            echo "<td>" . $row[8] . "</td>";
            echo "</tr > ";
        }
        echo "  </tbody></table>";
    }

    ?>
</div>
</body>
</html>
