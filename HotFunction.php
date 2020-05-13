<?php
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
/*
javascript通过Ajax直接调用任意PHP函数多参数例程
菜农在网友(QQ：20345037)指点下完成此例程，非常感谢！！！
雁塔菜农HotPower@163.com 2018.6.20于西安雁塔菜地
测试网址：http://www.hotpage.com.cn/phptest/HotFunction.html
*/
function f0()
{
    return;
}

function f1()
{
    return 1;
}

function f2()
{
    return 2;
}

function a1($a)
{
    return $a;
}

function a2($a, $b)
{
    return $a . $b;
}

function a3($a, $b, $c)
{
    return $a . $b . $c;
}

function a4($a, $b, $c, $d)
{
    return $a . $b . $c . $d;
}

$arr = array("f0", "f1", "f2", "a1", "a2", "a3", "a4");
$func = $_GET['function'];
//$func = $_REQUEST["function"];
//if(function_exists($func)){//可以调用任意PHP函数
if (in_array($func, $arr)) {//只能调用例程函数
    $fs = isset($_GET["age"]) ? explode(",", $_GET["age"]) : array();//参数以","分割
    echo call_user_func_array($func, $fs);
} else {
    echo "函数$func()不存在!!!";
}
?>