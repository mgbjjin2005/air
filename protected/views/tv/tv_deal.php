<?php
$name = Yii::app()->name;
$title = "电影购买";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "tv";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、请在购买后三天内尽快观看，否则电影将会过期。</br>";
Yii::app()->session['board_msg'] .= "2、电影是跟移动设备绑定的，即你在哪台设备上买的这部电影，就在哪部设备上观看。</br>";
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
            <td> 影片名称</td>
            <td><?php echo $media_info['mv_name'];?> </td>
        </tr>
		<tr>
            <td>电影价格</td>
            <td><?php echo $media_info['price'];?>豆</td>
        </tr>
        <tr>
            <td>支付账户</td>
            <td><?php echo $user_info['user_name'];?> </td>
        </tr>
        <tr>
            <td>支付方式</td>
            <td> <?php echo $media_info['buy_msg'];?></th>
        </tr>
        <tr>
            <td>

            </td>
            <td>
                <a  onclick="javascript:doCharge()">确定支付</a> &nbsp;&nbsp;
                <a  href="<?php echo $return_url;?>">返回</a>
            </td>
        </tr>

    </tbody>
</table>
</form>


</div>
<script>
    $(document).ready(function(){ 
    });
        function doCharge(){
            var url="index.php?r=tv/charge";
            var m_alias='<?php echo $media_info['m_alias']?>';
            $.ajax({type: "post", url: url,async:false,data:{m_alias:m_alias}
            ,success: function(resData)
            {
                    var resData = eval("(" + resData + ")"); 
                    var message=resData.message;
                    var return_url=resData.return_url;
                    if(resData.status=='Success'){
                        document.location.href=return_url;
                    }else{
                        document.location.href="index.php?r=site/warning&message="+ message+"&return_url="+encodeURI(return_url);
                    }
                       
            },error:function(){
                    alert("连接失败");
                    }
            });
         }

</script>
