<?php

class ServiceController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;
        }
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

    public function actionTransaction()
    {
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;
        }

        $this->render('transaction');
    }

    /*套餐列表*/
    public function actionPacket()
    {
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;
        }
        $dbDeal=new DbDeal();
        $set_t=$dbDeal->getPacketInfo("packet");
        $count = count($set_t);
        if ($count < 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量套餐, 系统维护中...count=$count";
            $this->render('//site/error_msg');
            return;
        }
        $user_packet_list=$dbDeal->getUserEnablePacket();
        //handle
        foreach($set_t as $key=> $packet){
            $user_status=false;
            foreach($user_packet_list as $user_packet){
                if($user_packet['packet_id']==$packet['packet_id']){
                    $user_status=true;
                    break;
                }
            }
            $set_t[$key]['user_status']=$user_status;
        }

        $this->render('packet', array('set_t' => $set_t));
    }
    /*加油包列表*/
    public function actionAddition()
    {
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;
        }
        $dbDeal=new DbDeal();
        $set_t=$dbDeal->getPacketInfo("addition");
        $count = count($set_t);
        if ($count < 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量加油包, 系统维护中...count=$count";
            $this->render('//site/error_msg');
            return;
        }
        $user_packet_list=$dbDeal->getUserEnablePacket();
        //handle
        foreach($set_t as $key=> $packet){
            $user_status=false;
            foreach($user_packet_list as $user_packet){
                if($user_packet['packet_id']==$packet['packet_id']){
                    $user_status=true;
                    break;
                }
            }
            $set_t[$key]['user_status']=$user_status;
        }

        $this->render('addition', array('set_t' => $set_t));
    }

    public function actionConfirmOpenPacket()
	{
        //get param
        $req =  Yii::app()->request ;
        $packet_id=$req->getParam("packet_id","");
        $user_name=Yii::app()->session["username"];
        $return_url="index.php?r=service/packet";
        $dbDeal=new DbDeal();
        //get packet info

        $packets =$dbDeal->getPacketInfoById($packet_id);
        $count = count($packets);
        if ($count != 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量套餐, 系统维护中...count=$count";
            $this->render('//site/error_msg');
            return;
        }
        //render
        $params=array();
        $params['packet']=$packets[0];
        $params['return_url']=$return_url;
        /*
        $retData["return_url"]=$req->getParam("return_url","index.php") ;
        if( empty($params["packet_id"])){
            $retData["message"]="套餐为空";
            $this->render('warning', $retData);
        }else{
	        $this->render("open_packet", $params);
        }
        */
        $this->render("open_packet", $params);
	}
    public function actionConfirmOpenAddition()
	{
        //get param
        $req =  Yii::app()->request ;
        $packet_id=$req->getParam("packet_id","");
        $user_name=Yii::app()->session["username"];
        $return_url="index.php?r=service/addition";
        $dbDeal=new DbDeal();
        //get packet info
        //check
        if ($packet_id=="") {
            Yii::app()->session['msg'] = "没有输入套餐，请返回重新提交";
            $this->render('//site/error_msg');
            return;
        }

        $addition =$dbDeal->getPacketInfoById($packet_id);
        $count = count($addition);
        if ($count != 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量套餐, 系统维护中...count=$count";
            $this->render('//site/error_msg');
            return;
        }
        //render
        $params=array();
        $params['addition']=$addition[0];
        $params['return_url']=$return_url;
        /*
        $retData["return_url"]=$req->getParam("return_url","index.php") ;
        if( empty($params["packet_id"])){
            $retData["message"]="套餐为空";
            $this->render('warning', $retData);
        }else{
	        $this->render("open_packet", $params);
        }
        */
        $this->render("open_addition", $params);
	}

    public function actionConfirmDeletePacket()
	{
        //get param
        $req =  Yii::app()->request ;
        $packet_id=$req->getParam("packet_id","");
        $user_name=Yii::app()->session["username"];
        $return_url="index.php?r=service/packet";
        //get packet info
        $dbDeal=new DbDeal();
        $packets=$dbDeal->getPacketInfoById($packet_id);
        $count = count($packets);
        if ($count != 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量套餐, 系统维护中...count=$count";
            $this->render('//site/error_msg');
            return;
        }
        //render
        $params=array();
        $params['packet']=$packets[0];
        $params['return_url']=$return_url;
        /*
        $retData["return_url"]=$req->getParam("return_url","index.php") ;
        if( empty($params["packet_id"])){
            $retData["message"]="套餐为空";
            $this->render('warning', $retData);
        }else{
	        $this->render("open_packet", $params);
        }
        */
        $this->render("delete_packet", $params);
	}
    /*
    public function actionConfirmDeleteAddition()
	{
        //get param
        $req =  Yii::app()->request ;
        $packet_id=$req->getParam("packet_id","");
        $user_name=Yii::app()->session["username"];
        $return_url="index.php?r=service/addition";
        //get packet info
        $dbDeal=new DbDeal();
        $additions=$dbDeal->getPacketInfoById($packet_id);
        $count = count($additions);
        if ($count != 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量加油包, 系统维护中...count=$count";
            $this->render('//site/error_msg');
            return;
        }
        //render
        $params=array();
        $params['addition']=$additions[0];
        $params['return_url']=$return_url;
        /*
        $retData["return_url"]=$req->getParam("return_url","index.php") ;
        if( empty($params["packet_id"])){
            $retData["message"]="套餐为空";
            $this->render('warning', $retData);
        }else{
	        $this->render("open_packet", $params);
        }
        */
        //$this->render("delete_addition", $params);
	//}
   // */
    /*开通套餐*/
    public function actionOpenPacket(){
        $req =  Yii::app()->request ;
        $packet_id=$req->getParam("packet_id","");
        $cur_month_ok=$req->getParam("cur_month_ok","");
        $user_name=Yii::app()->session["username"];
        $check_date="";
        //warning
        $retData=array();
        $retData["return_url"]="index.php?r=service/packet";

        /*防止重复提交*/
        $sql  = "select count(*) as value from packet_auto where user_name = '$user_name' ";
        $sql .= "and packet_id = $packet_id and enable_state = 'enable'";

        $count = air_get_value_by_sql($sql);
        if ($count >=1) {
            $retData["message"]="你已经开通过此套餐。为了防止误操作导致不必要的损失，系统不允许同时开通两个完全一样的套餐，如果确实需要，请选择开通其他套餐.";
            echo CJSON::encode($retData);
            return;
        }

        if(air_add_packet_deal($user_name,$packet_id)) {
            $retData["message"]="恭喜,套餐开通成功";
            air_update_user_radgroup($user_name);

        } else {
            $retData["message"] = Yii::app()->session['msg'];
        }

        echo CJSON::encode($retData);

    }
    public function actionDeletePacket(){
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;
        }
        $req =  Yii::app()->request ;
        $packet_id=$req->getParam("packet_id","");
        $user_name=Yii::app()->session["username"];
        $sql="update packet_auto set enable_state='disable' where user_name='$user_name' and packet_id=$packet_id";
        Yii::log($sql, 'info', 'haodan');
        $rowCount= Yii::app()->getDbByName("db_air")->createCommand($sql)->execute();
        $retData=array();
        $retData["return_url"]="index.php?r=service/packet";
        if( $rowCount>=1){
            $retData["message"]="套餐取消成功。";
            //$this->render('//site/warning', $retData);
        }else{
            $retData["message"]="取消失败";
	        //$this->render("//site/warning", $retData );
        }
        echo CJSON::encode($retData);

    }

    /*加油包*/
    public function actionOpenAddition()
    {
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;
        }
        $req =  Yii::app()->request ;
        $packet_id=$req->getParam("packet_id","");
        $user_name=Yii::app()->session["username"];
        $retData=array();
        $retData["return_url"]="index.php?r=service/addition";
        $retStatus=air_add_packet_deal($user_name,$packet_id);
        air_update_user_radgroup($user_name);
        if( $retStatus==true){
            $retData["status"]="Success";
            $retData["message"]="恭喜你,开通成功!<br>提示:如果账号之前处于'访问受限'状态,请重新登录(右上角,先退出,再登录即可).";
            //$this->render('//site/warning', $retData);
        }else{
            $retData["message"]="开通失败.message:".Yii::app()->session["msg"];
            
            $retData["status"]="Failed";
	        //$this->render("//site/warning", $retData );
        }
        //var_dump($retData);
        echo CJSON::encode($retData);

    }

    /*用户的电影豆详情*/
    public function actionMovieTicketsDetail(){
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;

        } 
        $dbDeal=new DbDeal();
        $retData=array();
        $retData["detail"]=$dbDeal->getUserServiceDetail("beans");
         $this->render('//service/movie_tickets_detail', $retData);

    }
    /*用户的流量详情*/
    public function actionUserPacketDetail(){
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;

        } 
        $dbDeal=new DbDeal();
        $retData=array();
        $retData["detail"]=$dbDeal->getUserServiceDetail("traffic");
         $this->render('//service/user_packet_detail', $retData);

    }
    /* 充值*/
    public function actionDisCharge(){
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;

        } 
        $retData=array();
        $this->render('//service/charge', $retData);
    }
    public function actionConfirmCharge(){
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;

        } 
        $req =  Yii::app()->request ;
        $user_name=Yii::app()->session["username"];
        $charge_name=$req->getParam("charge_name","");
        $charge_name_type=$req->getParam("charge_name_type","0");
        $charge_wifibi=$req->getParam("charge_wifibi","");
        $charge_price=$req->getParam("charge_price","");
        $retData=array();
        $retData["return_url"]="index.php?r=service/disCharge";
        $params=array();
        $params['charge_name']=$charge_name;
        $params['charge_name_type']=$charge_name_type;
        $params['charge_wifibi']=$charge_wifibi;
        $params['charge_price']=$charge_price;
        $dbDeal=new DbDeal();
        $ret=$dbDeal->getUserInfo($charge_name);
        if( $ret==null || count($ret)==0){
            $retData["message"]="账户:".$charge_name."不存在";
            $this->render('//site/warning', $retData);
            return;
        }
        $params['email']=$ret[0]['email'];
        $params['return_url']=$retData["return_url"];
	    $this->render("//service/do_charge", $params );
        //var_dump($retData);
        //echo CJSON::encode($retData);

    }
    public function actionCharge(){
        if (airAutoLogin() == false) {
            $this->render('//site/error_msg');
            return;

        } 
        $req =  Yii::app()->request ;
        $user_name=Yii::app()->session["username"];
        $charge_name=$req->getParam("charge_name","");
        $charge_wifibi=$req->getParam("charge_wifibi","");
        $charge_price=$req->getParam("charge_price","");
        $retData=array();
        $retData["return_url"]="index.php?r=//site/userinfo";
        $dbDeal=new DbDeal();
        $retStatus=$dbDeal->updateChargePrice($charge_name,$charge_wifibi,$charge_price);
        if( $retStatus==1){
            $retData["status"]="Success";
            $retData["message"]="充值成功.";
            //$this->render('//site/warning', $retData);
        }else{
            $retData["message"]="充值失败.";
            
            $retData["status"]="Failed";
	        //$this->render("//site/warning", $retData );
        }
        //var_dump($retData);
        echo CJSON::encode($retData);

    }


}
