<?php

class TvController extends Controller
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
		$req =  Yii::app()->request ;
        $type_id=$req->getParam("type_id",0);
		$area_id=$req->getParam("area_id",0);
		$kind_id=$req->getParam("kind_id",0);
		$keys=$req->getParam("keys","");
		$page_cur=$req->getParam("page_cur",1);
		$keys=urldecode($keys);
        $ret=air_get_media_list($kind_id,$area_id,$type_id,$page_cur,$keys);
		//var_dump($ret);
        //$ret['base_url']="www.wifi.com/";
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('tv',$ret);
	}
	public function actionDetail()
	{
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }
		$req =  Yii::app()->request ;
        $id=$req->getParam("id","-1");
		if ($id =='-1') {
            Yii::app()->session['msg'] = "该影片为空.";
            $this->render('//site/error_msg');
            return;
        }
		$ret=array();
		$dbDeal=new DbDeal();
		//
		$ret['info'] = $dbDeal->getMediaInfo($id);
		if ($ret['info'] =='' || count($ret['info'])==0) {
            Yii::app()->session['msg'] = "该影片内容为空.";
            $this->render('//site/error_msg');
            return;
        }
		$ret['info']=$ret['info'][0];
		$m_id=$ret['info']['auto_id'];
		//找到剧集详情
        $ret['detail'] =$dbDeal->getMediaDetail($m_id);
		$this->render('tv_detail',$ret);  
	}
	public function actionToWatch()
	{
        if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }
		$user_name=Yii::app()->session["username"];
		$req =  Yii::app()->request ;
        $id=$req->getParam("id","-1");//影片的id media_detail
		$media_path=$req->getParam("media_path","");
		if ($id =='-1') {
            Yii::app()->session['msg'] = "该影片详情为空.";
            $this->render('//site/error_msg');
            return;
        }
		$dbDeal=new DbDeal();
		//先通过函数判断 用户是否 可以看这个电影
		$tmp_ret=air_check_user_buy("haodan",$id);
		$flag=$tmp_ret['flag'];
		if($flag=='error'){
			$this->render('//site/error_msg');
            return;
		}else if($flag=='already_buy'){
			//获取media_detail表里面的数据
			$media_info = $dbDeal->getMediaDetail("",$id);
			if ($media_info ==null || count($media_info)<1) {
				Yii::app()->session['msg'] = "该影片详情为null.";
				$this->render('//site/error_msg');
				return;
			}
			//获取media表里面的数据
			$media = $dbDeal->getMediaInfo($media_info[0]['m_id']);
			if ($media ==null || count($media)<1) {
				Yii::app()->session['msg'] = "该影片为null.";
				$this->render('//site/error_msg');
				return;
			}
			$ret['media_info']=$media_info[0];
            $ret['media']=$media[0];
			$this->render('tv_watch',$ret);
		}else if($flag=='need_buy'){
			$ret['media_info']=$tmp_ret;
			$user_info=$dbDeal->getUserInfo($user_name);
			$ret['user_info']=$user_info;
			$this->render('tv_deal',$ret);
		}
		
		/*
		//获取media_detail表里面的数据
        $media_info = $dbDeal->getMediaDetail("",$id);
		if ($media_info ==null || count($media_info)<1) {
            Yii::app      ()->session['msg'] = "该影片详情为null.";
            $this->render('//site/error_msg');
            return;
        }
        //获取media表里面的数据
        $media = $dbDeal->getMediaInfo($media_info[0]['m_id']);
        if ($media ==null || count($media)<1) {
            Yii::app      ()->session['msg'] = "该影片为null.";
            $this->render('//site/error_msg');
            return;
        }

		$user_info=$dbDeal->getUserInfo($user_name);
		//判断是否可以观看，是否付钱了，如果付钱了就跳转到watch.html
		//如果没有付钱就跳转到购买电影页面
		$isDeal=true;
		$ret=array();
		//TODO:$isDeal=$dbDeal->isDealMedia($id);
		if($isDeal){
			
			$ret['media_info']=$media_info[0];
            $ret['media']=$media[0];
			$this->render('tv_watch',$ret);
		}else{
			$ret['id']=$id;
			//
			$ret['media_info']=$media_info[0];
			$ret['user_info']=$user_info[0];
			$ret['return_url']="index.php?r=tv/detail&id=".$ret['media_info']['m_id'];
			$this->render('tv_deal',$ret);
		}*/
	}
	public function actionCharge(){
		if (airAutoLogin() == false) {
            $this->render('error_msg');
            return;
        }
		$user_name=Yii::app()->session["username"];
		$req =  Yii::app()->request ;
        $id=$req->getParam("mv_id","-1");//影片的id media_detail
		if ($id =='-1') {
            Yii::app()->session['msg'] = "该影片的详情为空.";
            $this->render('//site/error_msg');
            return;
        }
		$ret=air_video_buy($user_name,$id);
		if($ret==false){
            $this->render('//site/error_msg');
            return;
		}else{
			$dbDeal=new DbDeal();
			//获取media_detail表里面的数据
			$media_info = $dbDeal->getMediaDetail("",$id);
			if ($media_info ==null || count($media_info)<1) {
				Yii::app()->session['msg'] = "该影片详情为null.";
				$this->render('//site/error_msg');
				return;
			}
			//获取media表里面的数据
			$media = $dbDeal->getMediaInfo($media_info[0]['m_id']);
			if ($media ==null || count($media)<1) {
				Yii::app()->session['msg'] = "该影片为null.";
				$this->render('//site/error_msg');
				return;
			}
			$ret['media_info']=$media_info[0];
            $ret['media']=$media[0];
			$this->render('tv_watch',$ret);
		}
	}
}
