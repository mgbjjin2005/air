<div class="content">
<form id="myform" action="index.php?r=service/confirmCharge" method="post">




<table class="table table-bordered table-striped">
   <input type="hidden" name="ListID" value="27917287443">
    <thead>
        <tr>
            <th colspan="2">
                    <?php if ($row['packet_category']=="packet") : ?>
                    套餐类型:
                    <?php else:?>
                    加油包类型:
                    <?php endif;?><?php echo $row['packet_desc']; ?></th>

        </tr>
    </thead>
    <tbody>
        <tr>
            <td>账号</td>
            <td>
                <input readonly="readonly"  type="text" data-required="true" name="charge_name" maxlength="16" id="charge_name" value="<?php echo Yii::app()->session['username']?>">
            </td>
        </tr>
        <tr>
            <td>充值金额</td>
            <td>
                <select name="charge_wifibi" id="charge_wifibi">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20" selected="selected">20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </td>
        </tr>
        
        <tr>
            <td>状态</td>
            <td><?php echo $row["state_desc"] ;?></td>
        </tr>
        <tr>
            <td>有效期</td>
            <td>
                <?php echo $row["start_date"];?></br>至</br>
                <?php echo $row["stop_date"];?>
            </td>
        </tr>


    </tbody>
</table>



        <input type="hidden" name="ListID" value="27917287443">

        <label>充值账号: (<span class="warning">默认为登录账户充值</span>)
            <div class="controls_form">
                    <label class="radio">
                        <input type="radio" name="charge_name_type"  value="0" checked="" onclick="getChargeNameType(0)"  >
                        充值登录账户

                    </label>
                    <label class="radio">
                        <input type="radio" name="charge_name_type"  value="1" onclick="getChargeNameType(1)"> 
                        充值好友账户
                     </label>
            </div>
            <input readonly="readonly"  type="text" data-required="true" name="charge_name" maxlength="16" id="charge_name" value="<?php echo Yii::app()->session['username']?>">
             <div  class="error_tips   " id="charge_name_error">错误提示:</div>
         </label>
        <label>充值数量: (<span class="warning">wifi币</span>)
            <!--<input type="text" data-required="true" name="charge_num" maxlength="16" id="charge_num">-->
                  <div class="controls_form">
                    <!--<label class="radio">
                        <input type="radio" name="charge_wifibi"  value="50" checked="">
                        50
                    </label>
                    <label class="radio">
                        <input type="radio" name="charge_wifibi"  value="100">
                         100
                     </label>-->
                     <select name="charge_wifibi" id="charge_wifibi"  style="
                         width: 50px;
                         ">
                         <option value="5">5</option>
                         <option value="10">10</option>
                         <option value="20" selected="selected">20</option>
                         <option value="50">50</option>
                         <option value="100">100</option>
                   </select>
                  </div>

        </label>
		<label>应付金额: </br> 
            <input  type="hidden" name="charge_price" id="charge_price" value="" />
			<div id="charge_price_msg" name="charge_price_msg;" style="color:red;float:left;margin-left:5px">5</div>
            <div style="color:red;margin-left:4px">元</div>
		</label>
		<label>充值方式:
			</br>
			<input type="radio" name="chargeMethod" id="charge_mjethod0" value="0" checked="">
		支付宝
		</label>	
        <!--
		<div id="zhifubao">
			<label>支付宝账号: 
            <input type="text" data-required="true" name="zhifubao_name"  id="zhifubao_name">
			</label>
			
		</div>
        -->

        
		<div class="button"><a  onclick="javascript:charge();">提交</a></div>
</form>
</div><!--end of the content-->
<script>
$(document).ready(function(){ 
    $("#charge_price").val('5');
    $('#charge_wifibi').change(function(){ 
        var charge_wifibi=$('#charge_wifibi').val();
        var charge_price=charge_wifibi;
        $('#charge_price_msg').html(charge_price);
        $('#charge_price').val(charge_price);
    });
    
});
function charge(){
        var charge_name=$('#charge_name').val();
        
        if(charge_name==""){
                $("#charge_name_error").html("错误提示:充值账号不能为空.");
                $("#charge_name_error").style.diaplay="block";
                return;
        }
        //$('#charge_name').attr("disabled",true);
        //console.log("name:"+charge_name+" "+charge_wifibi+"price"+charge_price+" "+charge_name_type);
         $('#myform').submit();
        /*
        var url="index.php?r=service/confirmCharge";
        $.ajax({type: "post", url: url,async:false,data:{charge_name:charge_name,charge_wifibi:charge_wifibi,charge_price:charge_price,charge_name_type:charge_name_type}
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
        //*},error:function(){
        //        alert("连接失败");
         //       }
       // });

}
function getChargeNameType(type){
    //alert(type);
    if(type=='0'){
        $('#charge_name').val("<?php echo Yii::app()->session['username']?>");

        $('#charge_name').attr("readonly","readonly");
    }else{
        $('#charge_name').val("");
        $('#charge_name').removeAttr("readonly"); 
    }
}

</script>
