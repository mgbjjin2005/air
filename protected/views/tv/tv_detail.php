<?php
Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = "影视专区";
?>
<!--筛选器-->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/filter.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/tv.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/tv_detail.css" />
<style type="text/css">
	.footer{
		clear: both;
	}
</style>
<script type="text/javascript">
	var tabList=['showDesc','showSeries'];
	function getTabId(func){
		return '#subnav_' + func;
	}
	function getBodyId(func){
		return '#reload_' + func;
	}
	function changeTab(o,func){
		/*
		if($(tabId).parentNode.parentNode.className === 'popmore'){
			this.more($(tabId).childNodes[0], func);
		}
		this.isChange = true;
		this.createBodyDIV(func);*/
		
		var arr = tabList, i = 0, iLen = arr.length;
		for(; i < iLen; i++){
			if(func == arr[i]){
				
				$(getTabId(func)).addClass('current');
				$(getBodyId(func)).show();
			}else{
				$(getTabId(arr[i])).removeClass('current');
				$(getBodyId(arr[i])).hide();
			}
		}
	}
</script>
<div class="s_main col2_21" style="margin-top: -40px;">
<!--start the main-->
	<div id="showInfo_wrap">
		<div id="showInfo">
		<!--上面的部分-->
		<div class="showInfo poster_w">
			<!--poster_w:海报尺寸200x300; poster_s:海报尺寸120x170;-->
			<ul class="baseinfo">
				<!--电影链接-->
				<li class="link"><a charset="420-2-11" href="#" target="_blank" title="<?php echo $info['m_chs_name']?>"></a></li>
				<!--图片src链接-->
				<li class="thumb">
					<img src="<?php echo $info['m_pic_path']?>" alt="<?php echo $info['m_chs_name']?>">
				</li>
				<li class="status"><span class="bg"></span></li>
				<li class="ishd"><span class="ico__SD"></span></li>
				
			</ul>
			<!--播放按钮-->
			
			<ul class="baseaction">
				<li class="action">
                    <!--<a charset="420-2-2" class="btnShow btnplaytrailer" href="http://v.youku.com/v_show/id_XNzAzMzA1OTAw.html" target="_blank"><em>播放预告片</em></a>
                    <a class="btnShow btnplayposi" charset="420-2-3" href="http://v.youku.com/v_show/id_XNzA5MzU1OTM2.html" target="_blank"><em>播放正片</em></a>
                    -->
					<!--
					<a href="http://www.youku.com/show_page/id_z428adf4cda7911e38b3f.html" data-from="1-3" target="video" class="btn btn-small">播&nbsp;&nbsp;放</a>
					-->
				</li>
			</ul>
			<div class="clear"></div>
		</div>

	</div>
	</div>
	<!--end of showInfo_wrap-->


	<a name="top"></a>
	<div id="package_wrap">
		<div id="package"></div>
	</div>
	<div class="box nBox">
			<div class="overview">
				<!--导航-->
				<div id="subnav_wrap">
					<!---->
					 <div class="yk-vcontent">
                        <div class="interact-box">
                            <div class="interact-skin" id="player_tabsbox" style="width: auto;">
                                <ul class="tabs">
                                    <li  id="subnav_showDesc" _to="videoinfo">
                                        <a onclick="changeTab(this,'showDesc')" charset="hz-4008949-1000752">
                                            详情介绍
                                        </a>
                                    </li>
                                    <li class="current" id="subnav_showSeries" _to="showlist" _type="side" >
                                        <a onclick="changeTab(this,'showSeries')" charset="hz-4008949-1000752">
                                            剧集列表
                                        </a>
                                    </li>
                                    
                                </ul>

                            </div>
                            <!--end interact-skin-->
                        </div>
                        <!--end interact-box-->
                        <!--content剧集系列-->
                        <div class="yk-vbox" id="reload_showSeries" >
                            <div class="yk-body">
                                
                                <div class="showlists">
                                   
                                    <div class="items">
                                        <div class="item item-open" vid="180620718">
                                            <div class="inner">
                                                <div class="panel">
                                                    <ul class="rel-aspect" vid="180620718">
														<?php foreach($detail as $key=>$row): ?>
															<?php if ($key == 0): ?>
                                                        <li class="current">
															<?php else: ?>
														<li class="">
															<?php endif; ?>
                                                            <a _hzcharset="hz-4009222-1000752" class="A" href="index.php?r=tv/toWatch&id=<?php echo $row['auto_id']?>">
                                                                <div class="headline">
                                                                    <?php echo $row['m_chs_desc']?>
                                                                </div>
                                                            </a>
                                                        </li>
														<?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
						<!---->
						
                    </div>
                    <!--end yk-vcontent-->
					
				</div><!--end of 导航-->
				<div class="bd" id="bd">
					<!--影片基本信息-->
					<div  class="opus" id="reload_showDesc" style="display: none;">
						<div id="overview_wrap">
							<div id="overview">
								<div class="overview_wrap">
									<ul class="baseinfo">
				
									<!--评分-->
									<li class="row1 rate">
										<!--
										<span class="ratingstar">
											<span class='labelClass'>评分:</span>
											<span class="rating" title="有2,795人顶过
											有610人踩过">
												<em class="ico__ratefull"></em>
												<em class="ico__ratefull"></em>
												<em class="ico__ratefull"></em>
												<em class="ico__ratefull"></em>
												<em class="ico__ratenull"></em>
												<em class="num">7.7</em>
											</span>
										</span>
										-->
										<!--豆瓣 评分-->
										<span class="rating_dp">
											<span class='labelClass'>豆瓣评分:</span>
											<?php echo $info['m_douban_num']?>
										</span>
									</li>
									
									
									<li class="row1 alias"> 
										<span class='labelClass'>电影名:</span>
										<?php echo $info['m_chs_name']?> 	
									</li>
									<!--别名-->
									<li class="row1 alias"> 
										<span class='labelClass'>别名:</span>
										<?php echo $info['m_alias']?> 	
									</li>
									<!--上映时间-->
									<li class="row2">
										<span class="pub">
											<span class='labelClass'>上映:</span>
											<?php echo $info['m_show_date']?> 
										</span>
										<!--<span class="pub"><label>优酷上映:</label>2014-05-09</span>-->
									</li>
								
									<!--地区+-->	
									<li class="row2">
										<span class="area">
											<span class='labelClass'>地区:</span>
											<!--<a charset="420-2-1" href="#" target="_blank"></a>-->
											<?php echo $info['m_area_desc']?> 
											
										</span>	
										<!--类型-->
									
										<span class="type">
											<span class='labelClass'>类型:</span>
											<!--<a target="_blank" charset="420-2-8" href="#">
											科幻
											</a> /--> 					
											<?php echo $info['m_type_desc']?>		
										</span>	
									
									</li>
									
									<!--导演-->	
									<li class="row2">
										<span class="director">
											<span class='labelClass'>导演:</span>
											<!--<a href="http://www.youku.com/star_page/uid_UMzQxMjY3Ng==.html" charset="420-2-7" target="_blank">李合</a>			
											-->
											<?php echo $info['m_director']?>	
										</span>	
									</li>
									<!--主演-->	
									<li class="row2">
										<span class="actor">
											<span class='labelClass'>主演:</span>
											<!--<a href="http://www.youku.com/star_page/uid_UMzQxMjY3Mg==.html" charset="420-2-10" target="_blank">赵婧伊</a> / 				
											<a href="http://www.youku.com/star_page/uid_UMzM4NTE0OA==.html" charset="420-2-10" target="_blank">潘春春</a>			
											-->
											<?php echo $info['m_main_actors']?>	
										</span>	
									</li>
								</ul>


								<ul class="basedata">
									<!--总播放数-->
									<li class="row2">
										<span class="play">
											<span class='labelClass'>总播放:</span>
											<?php echo $info['m_total_pv']?>	
										</span>
										<!--
										<span class="comment">
											<span class='labelClass'>评论:</span><em class="num">486</em> / <label>收藏:</label><em class="num">564</em>
										</span>
										-->
									</li>
									<!--评论-->
									<!--
									<li class="row2">
										<span class="comment">
											<span class='labelClass'>评论:</span><em class="num">486</em> / <span class='labelClass'>收藏:</span><em class="num">564</em>
										</span>
									</li>-->
									<!--今日新增播放数-->
									<!--
									<li class="row2">
										<span class="increm">
											<span class='labelClass'>今日新增播放:</span>1,581,607			</span>
										</span>
									</li>
									-->
									<!--时长-->
									<li class="row2">
										<span class="duration">
											<span class='labelClass'>时长:</span>
											<?php echo $info['m_time_length']?>	
										</span>
									</li>
									
									<li class="clear"></li>
								</ul>
									
									<!---->
								</div>	
								
								<div class="overview_wrap">
									<div class="detail" id="Detail">
										<!--剧情介绍，短的-->
										<span class="short" style="display:block;">
											<?php echo $info['m_des']?>	
										</span>
										<!--剧情介绍，长的，通过查看更多-->
										<span class="long" style="display:none;">苦逼青年小白（徐欢 饰）投资失败，工作失意，负债累累，觉得自己整个人生都黑暗了。女友阿V（赵婧伊 饰）苦心规劝，小白根本听不进去，靠着酒精麻痹自己。一天小白在酒吧街外英雄大战流氓之后，捡到一个奇怪眼罩。回家之后小白戴着眼罩就要睡觉，却不想眼前一黑。清醒过来时已经置身在一个变装晚会上，变成了风流倜傥的高富帅。一个萨拉丁打扮的美女正朝着自己明送秋波。小白抵挡不住诱惑，正要更进一步的时候，小白眼前又一黑，眼罩没电了。 
										</span>
									</div>
									<!--查看更多剧情-->
									<!--
									<div class="maspect" id="Point">
										<div class="handle">
											<a charset="420-3-1" class="more" onclick="yshow.showInfoToggle(this, '查看更多', '隐藏')">查看更多
											</a>
										</div>				
									</div>
									--><!-- .maspect-->
									<!---->
								</div>
								<!--end of剧情介绍-->

							</div><!--end of overview-->
					</div><!--end of overview_wrap-->
					
						
				</div> <!--.desc-->
			</div><!--.bd-->
				
		</div> <!--.overview-->
	</div><!--.box nBox-->
<!--end the main-->
</div>
