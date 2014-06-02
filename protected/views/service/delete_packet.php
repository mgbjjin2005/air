<div class="content">
    <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2">套餐:<?php echo $packet["p_desc"]; ?></th>
            
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>价格</td>
            <td><?php echo sprintf("%.2f", $packet["price"]);?> 元</td>
        </tr>
        <tr>
            <td>流量</td>
            <td><?php echo sprintf("%.2f", $packet["traffic"]);?>MB</td>
        </tr>
        <tr>
            <td>电影豆</td>
            <td><?php echo sprintf("%.2f", $packet["movie_tickets"]);?>豆</td>
        </tr>
        <tr>
            <td>有效月</td>
            <td><?php echo $packet["period_month"];?>个月</td>
        </tr>

        <tr>
            <td>

            </td>
            <td>
                 <input type="hidden" name="packet_id" value="<?php echo $packet['packet_id']?>">
                <a  href="javascript:deletePacket(<?php echo $packet['packet_id']?>)" >确定删除</a> &nbsp;&nbsp;
                <a  href="<?php echo $return_url;?>">返回</a>
            </td>
        </tr>

    </tbody>
</table>


</div>
<script>
     function deletePacket(packet_id){
        var url="index.php?r=service/deletePacket";
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
