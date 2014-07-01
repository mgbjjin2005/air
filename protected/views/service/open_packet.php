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
            <td>有效期</td>
            <td><?php echo $packet["period_month"];?>个月</td>
        </tr>

        <tr>
            <td>生效时间</td>
            <td>
                <!--
                <div class="checkbox_div">  
                <input type="radio" name=" cur_month_ok" value="0" /> <label>当月生效</label>
                </br>
                <input type="radio" name=" cur_month_ok" value="1" checked/><label>下个月生效</label>
                </div>-->

                <!--
                <div class="controls_form">
                    <label class="radio">
                        <input type="radio" name="cur_month_ok"  value="1" checked="">
                        下个月生效
                    </label>
                    <label class="radio">
                        <input type="radio" name="cur_month_ok"  value="0">
                         当月生效
                     </label>
                  </div>
                -->

                开通后立即生效
            </td>
        </tr>
        <tr>
            <td>

            </td>
            <td>
                <a  href="javascript:openPacket(<?php echo $packet['packet_id'];?>)">确定开通</a> &nbsp;&nbsp;
                <a  href="<?php echo $return_url;?>">返回</a>
            </td>
        </tr>

    </tbody>
</table>
</form>


</div>
<!--
<script>
     function openPacket(packet_id){
        var url="index.php?r=service/openPacket";
        $.ajax({type: "post", url: url,async:false,data:{packet_id:packet_id}
        ,success: function(data)
        {
                alert("123");
                alert(data);
                alert(data.message);
                var resData = eval("(" + data + ")");
                //var message=resData.message;
                //var return_url=resData.return_url;
                //if(resData.status=="Success"){
                //document.location.href="index.php?r=site/warning&message="+ message+"&return_url="+return_url;

        },error:function(){
                alert("连接失败");
                }
        });
     }

</script>-->
<script>
     function openPacket(packet_id){
        var url="index.php?r=service/openPacket";
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
