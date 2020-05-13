<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>jQuery+php实现点击按钮加载更多</title>
    <style>
        *{margin: 0;padding:0;list-style: none;}
        a{color: #333;text-decoration: none;}
        .hidden{display:none;}
        .content{width: 300px;height:auto;margin:0 auto;overflow: hidden;text-align: left;background:#fff;padding:5px;}
        .content ul.list{overflow: hidden;}
        .content ul.list li{width: 300px;height:auto;margin:5px;float:left;overflow:hidden;text-align:center;}
        .content .more{overflow: hidden;padding:10px;text-align: center;}
        .content .more a{display: block;width: 120px;padding:8px 0;color:#fff;margin:0 auto;background:#333;text-align:center;border-radius:100px;font-size: 15px;}
        .content .more a:hover{text-decoration: none;background: red;color: #fff;}
    </style>
</head>
<body>
<!--代码部分begin-->
<div class="content">
    <div class="hidden">
        <?php
        //获取数据
        require_once("connect_sql.php");
        ?>
    </div>
    <ul class="list">数据加载中，请稍后...</ul>
    <div class="more"><a href="javascript:;" onClick="loadding.loadMore();">点击加载更多</a></div><br/>
</div>
<script src="jquery.min.js"></script>
<script>
    var _content = []; //临时存储li循环内容
    var loadding = {
        _default:3, //默认个数
        _loading:3, //每次点击按钮后加载的个数
        init:function(){
            var lis = $(".content .hidden li");
            $(".content ul.list").html("");
            for(var n=0;n<loadding._default;n++){
                lis.eq(n).appendTo(".content ul.list");
            }
            for(var i=loadding._default;i<lis.length;i++){
                _content.push(lis.eq(i));
            }
            $(".content .hidden").html("");
        },
        loadMore:function(){
            var mLis = $(".content ul.list li").length;
            for(var i =0;i<loadding._loading;i++){
                var target = _content.shift();
                if(!target){
                    $('.content .more').html("<p style='color:#f00;'>已加载全部...</p>");
                    break;
                }
                $(".content ul.list").append(target);
            }
        }
    }
    loadding.init();
</script>
<!--代码部分end-->
</body>
</html>