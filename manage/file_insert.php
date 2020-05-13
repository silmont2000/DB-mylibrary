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
        <li class="breadcrumb-item"><a href="book_manage.php">Book Manage</a></li>
        <li class="breadcrumb-item active" aria-current="page">Results</li>
        <div style="position: relative;left: 80%">Welcome, manager <?php echo $_SESSION['USER'] ?></div>

    </ol>
</nav>
<div id="result_frame" style="width: 80%; left: 10%; right: 10%;overflow: auto;padding: 10%">


    <?php
    require_once "../try.php";
    header("Content-Type: text/html;charset=UTF-8");
    //error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    if ($_FILES["book"]["error"] > 0) {
        echo "Error: " . $_FILES["book"]["error"] . "<br />";
    } else {
        echo "Upload: " . $_FILES["book"]["name"] . "<br />";
        echo "Type: " . $_FILES["book"]["type"] . "<br />";
        echo "Size: " . ($_FILES["book"]["size"] / 1024) . " Kb<br />";
        echo "Stored in: " . $_FILES["book"]["tmp_name"];
    }
    define('DB_HOST', 'localhost');
    define('DB_USER', 'manager');
    define('DB_PASS', 'manage');
    define('DB_NAME', 'my_lib');
    define('DB_MANAGE', '1001');
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("数据库未连接" . mysqli_connect_error());
    }

    $book_file = fopen("{$_FILES["book"]["tmp_name"]}", "r+") OR die("文件无法打开或损坏");
    while (!feof($book_file)) {
        $this_line = fgets($book_file);
        $str = trim($this_line);
        if (empty(strlen($str))) {
            echo "空的";
            continue;
        } else {
            $pattern = "/[(),]/";
            $contents = preg_split($pattern, $this_line, -1, PREG_SPLIT_NO_EMPTY);

//        echo "insert into book values ('$contents[0]','$contents[1]','$contents[2]','$contents[3]',
//                    $contents[4],'$contents[5]',$contents[6],$contents[7],$contents[7])<br>";
            if ($contents[0] != "") {
                $check = "select * from book where bno = '$contents[0]' and title = '$contents[2]'";
                $err_check = mysqli_query($conn, $check);
                $result_check = mysqli_fetch_array($err_check);
                if ($result_check != null) {
                    $new_sql = "update book set total = total + $contents[7], stock = stock+$contents[7] where bno = '$contents[0]'";
                } else {
                    $new_sql = "insert into book values ('$contents[0]','$contents[1]','$contents[2]','$contents[3]',
                    $contents[4],'$contents[5]',$contents[6],$contents[7],$contents[7])";
                }
                echo $new_sql . "<br>";

                $new_err = mysqli_query($conn, $new_sql);
                if (!$new_err) {
                    echo "<div><div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                <span aria-hidden=\"true\">&times;</span></button><strong>本条信息出错了！</strong><br>
                $new_sql</div></div>";
                } else {
                    echo "<br>成功</br>";
                }
            }
        }
    }
    fclose($book_file);
    ?>
</div>
</body>
