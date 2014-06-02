<div class="content">
    <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2"> 充值信息:</th>
            
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>充值账户</td>
            <td><?php echo $charge_name;?> </td>
        </tr>
        <tr>
            <td>账户详情</td>
            <td><?php echo "账户邮箱:$email";?></td>
        </tr>
        <tr>
            <td>充值WIFI币</td>
            <td><?php echo $charge_wifibi;?>WIFI币</td>
        </tr>
        <tr>
            <td>应付金额</td>
            <td><?php echo $charge_price;?>元</td>
        </tr>

        <tr>
            <td>

            </td>
            <td>
                <a  href="javascript:doCharge()">确定充值</a> &nbsp;&nbsp;
                <a  href="<?php echo $return_url;?>">返回</a>
            </td>
        </tr>

    </tbody>
</table>
</form>


</div>
<script>
     function doCharge(){
        var url="index.php?r=service/charge";
        var charge_name='<?php echo $charge_name?>';
        var charge_name_type='<?php echo $charge_name_type?>';
        var charge_wifibi='<?php echo $charge_wifibi?>';
        var charge_price='<?php echo $charge_price?>';
        $.ajax({type: "post", url: url,async:false,data:{charge_name:charge_name,charge_name_type:charge_name_type,charge_wifibi:charge_wifibi,charge_price:charge_price}
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
