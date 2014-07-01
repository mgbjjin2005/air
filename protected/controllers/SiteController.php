<?php

class SiteController extends Controller
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
            $this->render('error_msg');
            return;
        }
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

    public function actionVideoList()
    {
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }

        $user_name = Yii::app()->session["username"];
        $mac = Yii::app()->session["mac"];
        
        $sql  = "select auto_id,m_id,mv_id,price,m_chs_desc, ";
        $sql .= "(timestamp(expire_date) - timestamp(now())) as expire, ";
        $sql .= "expire_date ,create_date from  media_deal_info where ";
        $sql .= "user_name = '$user_name' and mac ='$mac' and ";
        $sql .= "(to_days(now()) - to_days(expire_date)) <= 30 ";
        $sql .= "order by expire_date desc";

        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();

        $ret_expire =  array();
        $ret_history = array();
        $ret['user_name'] = $user_name;
        foreach ($set_t as $tuple) {
            $expire = $tuple['expire'];
            $obj = array();

            $obj["mv_id"] = $tuple["mv_id"];
            $obj["price"] = $tuple["price"];
            $obj["name"] = $tuple["m_chs_desc"];
            $obj["expire_date"] = $tuple["expire_date"];
            $obj["buy_date"] = $tuple["create_date"];

            if ($expire > 0) {
                $ret_expire[] = $obj;

            } else {
                $ret_history[] = $obj;
            }

        }
       
        $this->render('//site/video_list',array('ret_expire'=>$ret_expire, 'ret_history'=>$ret_history));
        
    }

    public function actionUserinfo()
    {
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }

        $user_name = Yii::app()->session["username"];
        $cur_mon = Date("Ym");
        air_update_user_mon($user_name,false);
        
        //流量相关的
        $sql  = "select traffic_idle, ";
        $sql .= "traffic_busy, traffic_internal, traffic_bill,traffic_remain from ";
        $sql .= "user_mon where user_name = '$user_name' and date_mon = '$cur_mon'";
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_t);
        if($count != 1){
            Yii::app()->session['msg'] =  "没有查询到相关信息;如果你是首次登陆，请稍等10分钟再来查看.";
            $this->render('error_msg');
            return;
        }

        $ret = array();
        $ret['user_name'] = $user_name;
        foreach ($set_t as $tuple) {
            $ret['traffic_idle']     = $tuple['traffic_idle'];
             $ret['traffic_busy']     = $tuple['traffic_busy'];
            $ret['traffic_internal'] = $tuple['traffic_internal'];
            $ret['traffic_bill']     = $tuple['traffic_bill'];
            $ret['traffic_remain']   = $tuple['traffic_remain'];

        }
        //账户余额
        $sql = " select balance from user_info where user_name = '$user_name'";
        $set_u = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_u);
        if($count != 1){
            Yii::app()->session['msg'] = "未查到此用户信息。count=$count";
            $this->render('error_msg');
        }

        foreach ($set_u as $tuple) {
            $ret['balance']   = $tuple['balance'];
        }
        //电影豆获取
        $dbDeal=new DbDeal();
        $ret['movie_tickets']=$dbDeal->getUserServiceCount("beans");
        //get detail
        
        $this->render('userinfo', $ret);
        
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{

		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}
    public function actionWarning()
	{
        $req =  Yii::app()->request ;

        $retData=array();
        $retData["return_url"]=$req->getParam("return_url","index.php?r=site/index");
        if($retData['return_url']=="index.php?r=tv/detail"){
            $id=$req->getParam("id","-1");
            $retData['return_url']=$retData['return_url']."&id=".$id;
        }
        $retData["message"]=$req->getParam("message","");
	    $this->render('warning', $retData);
	}
    

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}
