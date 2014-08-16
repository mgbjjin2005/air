<?php

class DbDeal
{
     function __construct()   
     {
     }
     public function getUserEnablePacket(){
        //get user packet
        $sql = "select po.packet_id from packet_auto as po
                where po.user_name='".Yii::app()->session['username']."'
                and po.enable_state='enable'";
        Yii::log($sql, 'info', 'haodan');
        $user_packet_list = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        return $user_packet_list;
     }
    public function getPacketAutoById($packet_id){
        //get user packet
        $sql = "select po.packet_id from packet_auto as po
                where po.user_name='".Yii::app()->session['username']."'
                and po.enable_state='enable' and packet_id=$packet_id";
        Yii::log($sql, 'info', 'haodan');
        $user_packet_list = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        return $user_packet_list;
     }

     public function getPacketInfo($type){
        $sql = "select packet_id, p_desc, traffic, price,movie_tickets,period_month from packet_info 
                where category = '$type' and enable_state='enable' order by price";
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        return $set_t;
     }

     public function getPacketInfoById($packet_id){
        $sql = "select packet_id, p_desc, traffic, price,movie_tickets,period_month from packet_info 
                where packet_id=".$packet_id;
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        return $set_t;
     }
     //结束时间之前的电影豆或者是套餐 流量包的详情
     public function getUserServiceDetail($type){
        $sql="select quota, remain, state,state_desc, packet_desc, start_date, stop_date ,packet_category
                from user_quota where user_name = '".Yii::app()->session['username']."' and category='$type'
                 and stop_date> now() 
                order by stop_date;";
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        return $set_t;

     
     }
     //可用的电影豆或者流量的总数
     public function getUserServiceCount($type){
        $sql="select sum(remain) as count
                from user_quota where user_name = '".Yii::app()->session['username']."' and category='$type'
                 and state='enable' and stop_date > now()";
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        if($set_t==null || count($set_t)<1 || $set_t[0]['count'] ==null)
        { 
            return 0;
        }else{
            return $set_t[0]['count'];
        }

     
     }
     //获取用户信息
     public function getUserInfo($user_name){
        $sql="select email,user_name
                from user_info where user_name = '$user_name'";
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        return $set_t;

     }
     //更新用户的账户的金额数                    
     public function updateChargePrice($charge_name,$charge_wifibi,$charge_price){
        $sql="update user_info set balance=balance +$charge_price  where user_name='$charge_name'";
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->execute();

        $sql  = "insert into transaction_info (user_name,msg,category,change_quota,create_date) ";
        $sql .= "values ('$charge_name', '账户充值', 'money', $charge_price, now())";
        Yii::log($sql,"info","sql");
        Yii::app()->getDbByName("db_air")->createCommand($sql)->execute();

        return $set_t;

     }
	 public function getMediaInfo($id){
		$sql = "select auto_id,m_alias,m_director,m_main_actors,m_time_length
				,m_show_date,m_revenue,m_douban_num
				,m_original_name,m_chs_name,m_area_desc,m_kind_desc,m_type_desc
				,m_total_pv,m_pic_path,m_des from media as m
                where m.enable_state='enable' and m.auto_id=$id";
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
		return $set_t;
	 }

	 public function getMediaDetail($media_id="",$m_alias=""){
		$con="1";
		if($m_alias != ""){
			$con=$con." and m_alias='$m_alias'";
		}

		if($media_id != ""){
			$con=$con." and m_id=$media_id";
		}
		$sql = "select auto_id,m_id,m_alias,m_chs_desc,m_price
				,m_pv,m_video_path,m_real_path from media_detail as m
                where  $con order by m_episode desc";
        Yii::log($sql, 'info', 'haodan');
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        return $set_t;
	 }



}
