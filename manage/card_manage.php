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
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">

    <!--    为了在js里面引入这段html代码，生成一个模板（个人觉得没必要单独写一个文件出来）-->
    <!--    还有书未归还的提示-->
    <script type="text/template" id="fail_temp">
        <div class="alert alert-warning alert-dismissible" role="alert" style="margin: 5% ">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            <strong>删除失败! </strong> 有书未归还，请返回上一级查看。暂时不能销卡。
        </div>
    </script>
    <!--删除成功的提示-->
    <script type="text/template" id="succeed_temp">
        <div class="alert alert-warning alert-dismissible" role="alert" style="margin: 5% ">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                    onClick="document.location.reload()">
                <span aria-hidden="true">&times;</span></button>
            <strong>删除成功！</strong></div>
    </script>

    <!--    局部更新的js，主要是为了判断的时候不跳页面（以为没必要，而且用户操作变麻烦了）-->
    <script>
        //        分配请求  因为不想支持旧浏览器所以其实这个分配也没必要写函数
        //        但考虑到以后的维护还是单独写了
        function GetXmlHttpObject() {
            var xmlhttp = null;
            return new XMLHttpRequest();
        }

        my_info = function (obj) {
            //首先获得用户选择这一行的内容 一级是td，还不够要获取一行的，所以取两次祖先
            const tr = $(obj).parent().parent();
            const cno = tr.children("td#cno").text();
            const name = tr.children("td#name").text();

            var xmlhttp = GetXmlHttpObject();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    //获取delete.php的输出（返回值）
                    var str = this.responseText;
                    if (str == 1) {
                        document.getElementById('window_position').innerHTML = document.getElementById('fail_temp').innerHTML;
                    } else {
                        document.getElementById('window_position').innerHTML = document.getElementById('succeed_temp').innerHTML;
                    }
                }
            };
            xmlhttp.open("GET", "delete.php?function=my_delete&cno=" + cno, true);
            xmlhttp.send();
        }
    </script>
</head>

<body>
<nav aria-label="breadcrumb" style="position: relative; z-index:9999">
    <ol class="breadcrumb" style="background-color: rgba(255,255,255,0.9)">
        <li class="breadcrumb-item"><a href="../welcome/login.php">Home</a></li>
        <li class="breadcrumb-item"><a href="manage.php">Manage</a></li>
        <li class="breadcrumb-item active" aria-current="page">Card Manage</li>
        <div style="position: relative;left: 72%">Welcome, manager <?php echo $_SESSION['USER'] ?></div>

    </ol>
</nav>

<!--中间这个结果框。引用的是search.css-->
<div id="result_frame" style="width: 80%; left: 10%; right: 10%;overflow: auto;padding: 10%">

    <!--    增加借书证的表单-->
    <form method="post" action="card_manage.php">
        <div class="form-row">
            <label for="inputPassword" class="col-sm-2 col-form-label"
                   style="background-color: #416082; color:white; border-radius: 5px">增加借书证</label>
            <div class="col"><input type="text" class="form-control" name="cno" placeholder="借书证号"></div>
            <div class="col"><input type="text" class="form-control" name="name" placeholder="姓名"></div>
            <div class="col"><input type="text" class="form-control" name="department" placeholder="学院"></div>
            <div class="col"><input type="text" class="form-control" name="type" placeholder="类别"></div>
            <button type="submit" class="btn btn-primary">确定</button>
        </div>
    </form>

    <!--    删除借书证的表单-->
    <!--    默认必须查找。二次确认防止错误-->
    <form method="post" action="card_manage.php">
        <div class="form-row">
            <label for="inputPassword" class="col-sm-2 col-form-label"
                   style="background-color: #416082; color:white; border-radius: 5px">删除借书证</label>
            <div class="col"><input type="text" class="form-control" name="cno1" placeholder="借书证号"></div>
            <div class="col"><input type="text" class="form-control" name="name1" placeholder="姓名"></div>
            <div class="col"><input type="text" class="form-control" name="department1" placeholder="学院"></div>
            <div class="col"><input type="text" class="form-control" name="type1" placeholder="类别"></div>
            <button type="submit" class="btn btn-primary">查找</button>
        </div>
    </form>

    <!--    接收ajax信息的位置-->
    <div id="window_position"></div>

    <?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'manager');
    define('DB_PASS', 'manage');
    define('DB_NAME', 'my_lib');
    define('DB_MANAGE', '1001');

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


    $cno = $_POST['cno'];//增加借书证
    $name = $_POST['name'];
    $department = $_POST['department'];
    $type = $_POST['type'];

    $cno1 = $_POST['cno1'];//删除借书证
    $name1 = $_POST['name1'];
    $department1 = $_POST['department1'];
    $type1 = $_POST['type1'];

    if (!empty($cno)) {//如果是增加借书证
        $sql = "insert into card values ('$cno','$name','$department','$type')";
        $err = mysqli_query($conn, $sql);
        if (!$err) {
            echo "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                        <button type = \"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                        <strong>新增失败! </strong> 卡号重复，请重新分配卡号，并检查信息！</div>";
        } else {
            echo "<div class=\"alert alert-warning alert-dismissible\" role=\"alert\" style=\"margin: 5% \">
                        <button type = \"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                        <strong>新增成功! </strong></div>";
        }
    } //    删除借书证，先查询
    else {
        if (!empty($cno1)) {
            $where = " and cno = '$cno1'";
        }
        if (!empty($department1)) {
            $where .= " and department like \"%{$department1}%\"";
        }
        if (!empty($name1)) {
            $where .= " and name like \"%{$name1}%\"";
        }
        if (!empty($type1)) {
            $where .= " and type = '{$type1}'";
        }
        $sql = "select * from card where true $where";
        $query = mysqli_query($conn, $sql);

        echo "self check";
        echo $sql . "<br>";

        echo "<div id='card_search'><table class=\"table table-hover\"><thead><tr><th scope=\"col\">卡号</th><th scope=\"col\">姓名</th>
                <th scope=\"col\">学院</th><th scope=\"col\">类别</th></tr></thead>";

//        这里要注意，mysqli_fetch_array这个函数是自动推进的。之前不能输出。
        while ($row = mysqli_fetch_array($query)) {
            echo "<tbody>";
            echo "<tr > ";
            echo "<td id='cno'>$row[0]</td><td id='name'>$row[1]</td><td id='department'>$row[2]</td><td id='type'>$row[3]</td>";
//            删除按钮
            echo "<td><input type=\"button\" class=\"btn btn-primary\" value=\"删除\" onclick='my_info(this)'/></td>";
            echo "</tr> ";
        }
        echo "</tbody></table></div>";
    }
    ?>
</div>
</body>
</html>
