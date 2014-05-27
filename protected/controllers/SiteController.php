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

    public function actionTransanction()
    {
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }

        $this->render('transanction');
    }

    /*加油包*/
    public function actionPacket()
    {
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }

        $sql = "select t_id, t_desc, traffic, price from traffic_packet where category = 'packet' order by price";
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_t);
        if ($count < 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量套餐, 系统维护中...count=$count";
            $this->render('error_msg');
            return;
        }

        $this->render('packet', array('set_t' => $set_t));
    }

    /*加油包*/
    public function actionAddition()
    {
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }

        $sql = "select t_id, t_desc, traffic, price from traffic_packet where category = 'addition' order by price";
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_t);
        if ($count < 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量套餐, 系统维护中...count=$count";
            $this->render('error_msg');
            return;
        }

        $this->render('addition', array('set_t' => $set_t));
    }

    public function actionUserinfo()
    {
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }

        $user_name = Yii::app()->session["username"];
        $cur_mon = Date("Ym");
        $sql  = "select traffic_packet,traffic_addition, traffic_recharge, traffic_last, traffic_idle, ";
        $sql .= "traffic_busy, traffic_internal, traffic_bill,traffic_remain from ";
        $sql .= "traffic_mon where user_name = '$user_name' and date_mon = '$cur_mon'";
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_t);
        if($count != 1){
            Yii::app()->session['msg'] = "还没有本月的相关信息。count=$count";
            $this->render('error_msg');
            return;
        }

        $ret = array();
        $ret['user_name'] = $user_name;
        foreach ($set_t as $tuple) {
            $ret['traffic_packet']   = $tuple['traffic_packet'];
            $ret['traffic_addition'] = $tuple['traffic_addition'];
            $ret['traffic_recharge'] = $tuple['traffic_recharge'];
            $ret['traffic_last']     = $tuple['traffic_last'];
            $ret['traffic_idle']     = $tuple['traffic_idle'];
            $ret['traffic_busy']     = $tuple['traffic_busy'];
            $ret['traffic_internal'] = $tuple['traffic_internal'];
            $ret['traffic_bill']     = $tuple['traffic_bill'];
            $ret['traffic_remain']   = $tuple['traffic_remain'];

            $ret['total'] = $ret['traffic_addition'] + $ret['traffic_recharge'] + 
                            $ret['traffic_packet'] + $ret['traffic_last'];
        }

        $sql = " select balance from user_info where user_name = '$user_name'";
        $set_u = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_u);
        if($count != 1){
            Yii::app()->session['msg'] = "位查到此用户信息。count=$count";
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
