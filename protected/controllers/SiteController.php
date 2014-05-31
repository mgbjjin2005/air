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

    public function actionUserinfo()
    {
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }

        $user_name = Yii::app()->session["username"];
        $cur_mon = Date("Ym");

        /*每月的0点 到 0点10分为出账期，不允许查账*/
        $day = Date("d");
        if ($day == 1) {
            Yii::app()->session['msg'] =  "正在出账，还没有本月的相关信息。请在10分钟以后再来查询...";
            $this->render('error_msg');
            return;
        }

        $sql  = "select traffic_idle, ";
        $sql .= "movie_tickets, traffic_busy, traffic_internal, traffic_bill,traffic_remain from ";
        $sql .= "user_mon where user_name = '$user_name' and date_mon = '$cur_mon'";
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_t);
        if($count != 1){
            Yii::app()->session['msg'] =  "没有查询到您的相关信息，请联系管理员";
            $this->render('error_msg');
            return;
        }

        $ret = array();
        $ret['user_name'] = $user_name;
        foreach ($set_t as $tuple) {
            $ret['movie_tickets']   = $tuple['movie_tickets'];
            //$ret['traffic_packet']   = $tuple['traffic_packet'];
            //$ret['traffic_addition'] = $tuple['traffic_addition'];
            //$ret['traffic_recharge'] = $tuple['traffic_recharge'];
            $ret['traffic_idle']     = $tuple['traffic_idle'];
            $ret['traffic_busy']     = $tuple['traffic_busy'];
            $ret['traffic_internal'] = $tuple['traffic_internal'];
            $ret['traffic_bill']     = $tuple['traffic_bill'];
            $ret['traffic_remain']   = $tuple['traffic_remain'];

        }

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
        $retData=array();
        $retData["return_url"]="index.php?r=site/index";
        $retData["message"]="";

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
