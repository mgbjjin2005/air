<?php
/* @var $this SiteController */
$name = Yii::app()->name;
$title = "加油包介绍";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "service/transaction";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、加油包办理后立即生效，有效期从办理时间开始算。</br>";
Yii::app()->session['board_msg'] .= "2、加油包为单独的流量购买行为，不具有连续性，如果还需要加油包，需要重新购买，系统不会自动为你购买。</br>";

?>

<div class="content">
<?php foreach($set_t as $row): ?>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th colspan="2">加油包:<?php echo $row['p_desc']; ?></th>
            
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
                开通
            <?php elseif ($row["user_status"] == false): ?>
                可开通
            <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
            <?php if ($row["user_status"] == false): ?>
                <a onclick="openAddition(<?php echo $row['packet_id']?>)">
                开通此加油包
                </a>
            <?php endif; ?>

            </td>
        </tr>

    </tbody>
</table>
 <?php endforeach; ?>
</div>
<script>
     function openAddition(packet_id){
        var return_url="index.php/r=service/addition";
        var confirm_url="index.php?r=service/confirmOpenAddition";
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

    function deleteAddition(packet_id){
        var return_url="index.php/r=service/addition";
        var confirm_url="index.php?r=service/confirmDeleteAddition";
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
