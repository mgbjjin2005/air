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

        $sql = "select packet_id, p_desc, traffic, price,movie_tickets from packet_info 
                where category = 'packet' and enable_state='enable' order by price";
        $set_t = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
        $count = count($set_t);
        if ($count < 1) {
            Yii::app()->session['msg'] = "没有查询到任何可用的流量套餐, 系统维护中...count=$count";
            $this->render('error_msg');
            return;
        }
        //get user packet
        $sql = "select pi.packet_id, pi.p_desc, pi.traffic, pi.price,pi.movie_tickets from packet_info as pi,packet_auto as po
                where pi.packet_id=pi.packet_id and pi.category = 'packet' and po.user_name='".Yii::app()->session['username']."'
                and po.enable_state='enable' order by price";
        $user_packet_list = Yii::app()->getDbByName("db_air")->createCommand($sql)->queryAll();
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
    public function actionConfirm(){
        echo "**";
    }





}
