
<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$this->pageTitle = $name." 账户充值";

Yii::app()->session['nav'] = "charge";
Yii::app()->session['nav_msg'] = "账户充值";
Yii::app()->session['board_name'] = "信息栏";
Yii::app()->session['board_msg']  = "1、此页面仅供自己充值使用，如果需要帮助其它人充值，可以先在此页面为自己充值，然后通过‘业务办理’->‘转账给好友’功能完成。";

?>

<div class="content">
<form id="myform" action="index.php?r=service/confirmCharge" method="post">

<table class="table table-bordered table-striped">
   <input type="hidden" name="ListID" value="27917287443">
    <thead>
        <tr>
            <th colspan="2">充值详情</th>

        </tr>
    </thead>
    <tbody>
        <tr>
            <td>账号</td>
            <td>
                <input readonly="readonly"   type="hidden" data-required="true" name="charge_name" id="charge_name" value="<?php echo Yii::app()->session['username']?>">
                <?php  echo Yii::app()->session['username']?>
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
                </select> 元
            </td>
        </tr>
        
        <tr>
            <td>应付金额</td>
            <td>
                <input  type="hidden" name="charge_price" id="charge_price" value="" />
			    <div id="charge_price_msg" name="charge_price_msg;">20元</div>
            </td>
        </tr>
        <tr>
            <td>充值方式</td>
            <td>
			    <input type="radio" name="chargeMethod" id="charge_mjethod0" value="0" checked=""> 支付宝
            </td>
        </tr>

        <tr>
            <td colspan="2" class="menu"><a  onclick="javascript:charge();">充值</a></td>
        </tr>


    </tbody>
</table>

</form>
</div><!--end of the content-->

<script>
$(document).ready(function(){ 
    $("#charge_price").val('20');
    $('#charge_wifibi').change(function(){ 
        var charge_wifibi=$('#charge_wifibi').val();
        var charge_price=charge_wifibi;
        $('#charge_price_msg').html(charge_price+"元");
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
         $('#myform').submit();

}
function getChargeNameType(type){
    if(type=='0'){
        $('#charge_name').val("<?php echo Yii::app()->session['username']?>");

        $('#charge_name').attr("readonly","readonly");
    }else{
        $('#charge_name').val("");
        $('#charge_name').removeAttr("readonly"); 
    }
}

</script>
