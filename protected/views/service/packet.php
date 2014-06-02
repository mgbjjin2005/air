<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$title = "套餐介绍";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、套餐为包月形式，新的套餐办理后，老的套餐会在下月自动失效。</br>";
Yii::app()->session['board_msg'] .= "2、套餐的生效周期为当月办理，下月开始生效。</br>";
Yii::app()->session['board_msg'] .= "3、套餐流量的有效期为一个月，月底如果没有使用完会被清零。所以亲请根据需要选择适合自己的套餐。</br>";
Yii::app()->session['board_msg'] .= "4、每个月最后一天为出账期，不能办理套餐业务。</br>";
Yii::app()->session['board_msg'] .= "4、套餐具有连续性。办理后每个月最后一天会自动扣下个月的套餐费，扣费不成功(余额不足)套餐不生效。</br>";
Yii::app()->session['board_msg'] .= "5、如果下个月不想再使用任何套餐，请一定记得在本月最后一天之前办理";
Yii::app()->session['board_msg'] .= "&lt取消我的套餐&gt业务，防止系统误扣套餐费，给你带来不必要的损失。</br>";
Yii::app()->session['board_msg'] .= "6、如果本月流量不够需要增加本月的流量，请选择对应的加油包</br>";

?>

<div class="content">
<?php foreach($set_t as $row): ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2" 
                <?php if($row["user_status"] == true): ?> 
                    style="color:#A1B60A"
                <?php endif; ?> 
            >
                    套餐:<?php echo $row['p_desc']; ?></th>
            
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>价格</td>
            <td><?php echo sprintf("%.2f", $row["price"]);?> 元</td>
        </tr>
        <tr>
            <td>流量</td>
            <td><?php echo sprintf("%.2f", $row["traffic"]);?>MB</td>
        </tr>
        <tr>
            <td>电影豆</td>
            <td><?php echo sprintf("%.2f", $row["movie_tickets"]);?>豆</td>
        </tr>
        <tr>
            <td>有效月</td>
            <td><?php echo $row["period_month"];?>个月</td>
        </tr>

        <tr>
            <td>状态</td>
            <td>
            <?php if ($row["user_status"] == true): ?>
               <color style="color:#A1B60A" > 当前已开通</color>
            <?php elseif ($row["user_status"] == false): ?>
                当前可开通
            <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
            <?php if ($row["user_status"] == true): ?> 
                <a onclick="deletePacket(<?php echo $row['packet_id']?>)">
                取消此套餐
                </a>                  
            <?php elseif ($row["user_status"] == false): ?>
                <a onclick="openPacket(<?php echo $row['packet_id']?>)">
                开通此套餐
                </a>
            <?php endif; ?>

            </td>
        </tr>

    </tbody>
</table>
 <?php endforeach; ?>
</div>
<script>
     function openPacket(packet_id){
        var return_url="index.php/r=service/packet";
        var confirm_url="index.php?r=service/confirmOpenPacket";
        var myForm = document.createElement("form");   
        myForm.method="post" ; 
        myForm.action = confirm_url;     
        //packet_id
        var myInput = document.createElement("input") ;   
        myInput.setAttribute("name", "packet_id") ;   
        myInput.setAttribute("value", packet_id); 
        myForm.appendChild(myInput) ;

        //return_url
        var myInput2 = document.createElement("input") ;   
        myInput2.setAttribute("name", "return_url") ;   
        myInput2.setAttribute("value", return_url); 
        myForm.appendChild(myInput2) ;

        myForm.submit() ;   
        document.body.removeChild(myForm) ; 


     }

    function deletePacket(packet_id){
        var return_url="index.php/r=service/packet";
        var confirm_url="index.php?r=service/confirmDeletePacket";
        var myForm = document.createElement("form");   
        myForm.method="post" ; 
        myForm.action = confirm_url;     
        //packet_id
        var myInput = document.createElement("input") ;   
        myInput.setAttribute("name", "packet_id") ;   
        myInput.setAttribute("value", packet_id); 
        myForm.appendChild(myInput) ;

        //return_url
        var myInput2 = document.createElement("input") ;   
        myInput2.setAttribute("name", "return_url") ;   
        myInput2.setAttribute("value", return_url); 
        myForm.appendChild(myInput2) ;

        myForm.submit() ;   
        document.body.removeChild(myForm) ; 


     }


</script>
