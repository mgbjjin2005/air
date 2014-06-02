<div class="content">
<form  action="index.php?r=service/openAddition" name="form" method="post">
    <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2">加油包:<?php echo $addition["p_desc"]; ?></th>
            
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>价格</td>
            <td><?php echo sprintf("%.2f", $addition["price"]);?> 元</td>
        </tr>
        <tr>
            <td>流量</td>
            <td><?php echo sprintf("%.2f", $addition["traffic"]);?>MB</td>
        </tr>
        <tr>
            <td>电影豆</td>
            <td><?php echo sprintf("%.2f", $addition["movie_tickets"]);?>豆</td>
        </tr>
        <tr>
            <td>有效月</td>
            <td><?php echo $addition["period_month"];?>个月</td>
        </tr>

        <tr>
            <td>

            </td>
            <td>
                <a  href="javascript:openAddition(<?php echo $addition['packet_id']?>)" >确定开通</a> &nbsp;&nbsp;
                <a  href="<?php echo $return_url;?>">返回</a>
            </td>
        </tr>

    </tbody>
</table>
</form>


</div>
<script>
     function openAddition(packet_id){
        var url="index.php?r=service/openAddition";
        $.ajax({type: "post", url: url,async:false,data:{packet_id:packet_id}
        ,success: function(resData)
        {
                var resData = eval("(" + resData + ")"); 
                var message=resData.message;
                var return_url=resData.return_url;
                //if(resData.status=="Success"){
                document.location.href="index.php?r=site/warning&message="+ message+"&return_url="+return_url;
                /*}
                else{
                    alert("保存失败，\n\n 详情： " + resData.message);
                }*/   
        },error:function(){
                alert("连接失败");
                }
        });
     }

</script>
