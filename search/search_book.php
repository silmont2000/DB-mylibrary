<?php
//session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('DB_HOST', 'localhost');
define('DB_USER', 'borrower');
define('DB_PASS', 'borrow');
define('DB_NAME', 'my_lib');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {

}
$order = $_REQUEST['order'];
//$order = "title";

$title = $_REQUEST['title'];
$author = $_REQUEST['author'];
$press = $_REQUEST['press'];
$category = $_REQUEST['category'];
$low_year = $_REQUEST['low_year'];
$high_year = $_REQUEST['high_year'];
$low = $_REQUEST['low'];
$high = $_REQUEST['high'];

if (!empty($title)) {
    $where .= " and title like \"%{$title}%\"";
}
if (!empty($author)) {
    $where .= " and author like \"%{$author}%\"";
}
if (!empty($press)) {
    $where .= " and press like \"%{$press}%\"";
}
if (!empty($category)) {
    $where .= " and category like \"%{$category}%\"";
}
if (!empty($low_year)) {
    $where .= " and year >= '{$low_year}'";
}
if (!empty($high_year)) {
    $where .= " and year <= '{$high_year}'";
}
if (!empty($low)) {
    $where .= " and price >= '{$low}'";
}
if (!empty($high)) {
    $where .= " and price <= '{$high}'";
}

$sql = "select * from book where true $where order by $order";
echo $sql . "<br>";
$query = mysqli_query($conn, $sql);
output_book($query, $order);
mysqli_close($conn);
exit();
function output_book($query, $order)
{
    echo " <table class=\"table table-hover\">
            <thead>
            <tr>";
    if ($order == 'bno desc')
        echo "<th scope=\"col\" onclick='set_order(1)'>编号▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(1)'>编号▴</th>";
    if ($order == 'category desc')
        echo "<th scope=\"col\" onclick='set_order(2)'>类别▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(2)'>类别▴</th>";
    if ($order == 'title desc')
        echo "<th scope=\"col\" onclick='set_order(3)'>名称▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(3)'>名称▴</th>";
    if ($order == 'press desc')
        echo "<th scope=\"col\" onclick='set_order(4)'>出版社▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(4)'>出版社▴</th>";
    if ($order == 'year desc')
        echo "<th scope=\"col\" onclick='set_order(5)'>年份▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(5)'>年份▴</th>";
    if ($order == 'author desc')
        echo "<th scope=\"col\" onclick='set_order(6)'>作者▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(6)'>作者▴</th>";
    if ($order == 'price desc')
        echo "<th scope=\"col\" onclick='set_order(7)'>价格▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(7)'>价格▴</th>";
    if ($order == 'total desc')
        echo "<th scope=\"col\" onclick='set_order(8)'>总量▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(8)'>总量▴</th>";
    if ($order == 'stock desc')
        echo "<th scope=\"col\" onclick='set_order(9)'>库存▾</th>";
    else
        echo "<th scope=\"col\" onclick='set_order(9)'>库存▴</th>";
    echo "</tr ></thead >";
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
    echo "</tbody></table>";
}

?>