<?php
session_start();
$_SESSION['order'] = 'title';
?>
<!DOCTYPE html>
<html lang="en" xmlns:overflow-y="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <title>图书查询-折工大学图书馆</title>

    <script type="text/javascript" src="search.php?action=test"></script>

    <script src="../jquery/jquery-1.10.2.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="search.css"/>
    <script type="text/javascript">
        function GetXmlHttpObject() {
            var xmlhttp = null;
            return new XMLHttpRequest();
        }

        var my_option;

        set_order = function (opt) {
            // alert(opt);
            switch (opt) {
                case 1:
                    if (my_option !== 'bno desc')
                        my_option = 'bno desc';
                    else my_option = 'bno asc';
                    break;
                case 2:
                    if (my_option !== 'category desc')
                        my_option = 'category desc';
                    else my_option = 'category asc';
                    break;
                case 0:
                case 3:
                    // my_option = 'title';
                    if (my_option !== 'title desc')
                        my_option = 'title desc';
                    else my_option = 'title asc';
                    break;
                case 4:
                    // my_option = 'press';
                    if (my_option !== 'press desc')
                        my_option = 'press desc';
                    else my_option = 'press asc';
                    break;
                case 5:
                    // my_option = 'year';
                    if (my_option !== 'year desc')
                        my_option = 'year desc';
                    else my_option = 'year asc';
                    break;
                case 6:
                    // my_option = 'author';
                    if (my_option !== 'author desc')
                        my_option = 'author desc';
                    else my_option = 'author asc';
                    break;
                case 7:
                    // my_option = 'price';
                    if (my_option !== 'price desc')
                        my_option = 'price desc';
                    else my_option = 'price asc';
                    break;
                case 8:
                    // my_option = 'total';
                    if (my_option !== 'total desc')
                        my_option = 'total desc';
                    else my_option = 'total asc';
                    break;
                case 9:
                    // my_option = 'stock';
                    if (my_option !== 'stock desc')
                        my_option = 'stock desc';
                    else my_option = 'stock asc';
                    break;
            }
            var my_data = $.param({'order': my_option}) + '&' + $('#condition').serialize();
            $.ajax({
                //几个参数需要注意一下
                type: "POST",//方法类型
                dataType: "text",//预期服务器返回的数据类型
                url: "search_book.php",//url
                data: my_data,
                success: function (result) {
                    // alert(my_data);
                    // alert(result);
                    document.getElementById("search_result").innerHTML = result;
                }
                ,
                error: function () {
                    alert("异常！");
                }
            })
            ;
        }

    </script>
</head>

<body>
<nav aria-label="breadcrumb" style="position: relative; z-index:9999">
    <ol class="breadcrumb" style="background-color: rgba(255,255,255,0.9)">
        <li class="breadcrumb-item"><a href="../welcome/login.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Search</li>
    </ol>
</nav>


<div id="column_frame">
    <div class="blank_block"></div>
    <form id="condition">
        <!--    <form method="post" action="search.php">-->

        <p><label class="column_name">书名</label> <label>
                <input placeholder="包含" class="column_input" type="text" name="title"/></label></p>
        <p><label class="column_name">作者</label> <label>
                <input placeholder="包含" class="column_input" type="text" name="author"/> </label></p>
        <p><label class="column_name">出版社</label> <label>
                <input placeholder="包含" class="column_input" type="text" name="press"/> </label></p>
        <p><label class="column_name">类别</label> <label>
                <input placeholder="类别" class="column_input" type="text" name="category"/> </label>
        </p>
        <p><label class="column_name">年份区间</label><label>
                <input placeholder="最低" class="column_input" style="width: 95px" type="text" name="low_year"/> </label>
            <label>
                <input placeholder="最高" class="column_input" style="width: 95px" type="text" name="high_year"/> </label>
        </p>
        <p><label class="column_name">价格区间</label><label>
                <input placeholder="最低" class="column_input" style="width: 95px" type="text" name="low"/> </label>
            <label>
                <input placeholder="最高" class="column_input" style="width: 95px" type="text" name="high"/> </label></p>

        <input class="button" type="button" value="查询" id="submit" onclick="set_order(0)">
        <!--        <button class="button" type="submit" id="submit">查询-->
        </input>
    </form>

    <div id="result_frame" style="overflow: auto">
        <div class="blank_block"></div>
        <span>查询结果</span>
        <div id="search_result"></div>
    </div>

</div>
</body>
</html>






