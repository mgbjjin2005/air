<?php
Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = "支付电影";
?>
<div class="content">
    <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2"> 支付电影信息:</th>
            
        </tr>
    </thead>
    <tbody>
		<tr>
            <td> 影片</td>
            <td><?php echo $media_info['m_chs_desc'];?> </td>
        </tr>
		<tr>
            <td>电影价格</td>
            <td><?php echo $media_info['m_price'];?>豆</td>
        </tr>
        <tr>
            <td>支付账户</td>
            <td><?php echo $user_info['user_name'];?> </td>
        </tr>
        <tr>
            <td>账户详情</td>
            <td><?php echo "账户邮箱:".$user_info['email'];?></td>
        </tr>
        
        <tr>
            <td>支付金额</td>
            <td><?php echo $media_info['m_price'];?>元</td>
        </tr>

        <tr>
            <td>

            </td>
            <td>
                <a  href="javascript:doCharge()">确定支付</a> &nbsp;&nbsp;
                <a  href="<?php echo $return_url;?>">返回</a>
            </td>
        </tr>

    </tbody>
</table>
</form>


</div>
<script>
     function doCharge(){
        var url="index.php?r=tv/charge";
		/*
        var charge_name='';
        var charge_name_type='';
        var charge_wifibi='';
        var charge_price='';
        $.ajax({type: "post", url: url,async:false,data:{charge_name:charge_name,charge_name_type:charge_name_type,charge_wifibi:charge_wifibi,charge_price:charge_price}
        ,success: function(resData)
        {
                var resData = eval("(" + resData + ")"); 
                var message=resData.message;
                var return_url=resData.return_url;
                document.location.href="index.php?r=site/warning&message="+ message+"&return_url="+return_url;
                   
        },error:function(){
                alert("连接失败");
                }
        });
		*/
     }

</script>