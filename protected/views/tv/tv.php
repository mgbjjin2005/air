<?php
Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = "影视专区";
?>
<!--筛选器-->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/filter.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/tv.css" />
<div class="yk-filter yk-filter-open" id="filter">
    <div class="yk-filter-panel">
        <div class="item item-moreshow">
			<label>分类</label>
			<ul>
				<li><a href="/v_showlist/c0.html">全部</a></li>
				<li><a href="/v_olist/c_97.html">电视剧</a></li>
                <li class="current"><span>电影</span></li>
				<li><a href="/v_olist/c_85.html">综艺</a></li>
                <li><a href="/v_olist/c_100.html">动漫</a></li>
                <li><a href="/v_olist/c_95.html">音乐MV</a></li>
				<li><a href="/v_showlist/c98.html">体育</a></li>
				<li><a href="/v_showlist/c102.html">其它</a></li>
			</ul>
            
			<!--<div class="btn-handle">更多<b class="caret"></b></div>-->
		</div>
    
		<div class="item item-moreshow">
			<label>地区</label>
			<ul>
				<li class="current"><span>全部</span></li>
                <li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_大陆.html">欧美</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_香港.html">日韩</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_台湾.html">港台</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_韩国.html">大陆</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_美国.html">其它</a></li>
			</ul>    
		</div>
   
		<div class="item item-moreshow">
			<label>类型</label>
			<ul>
				<li class="current"><span>全部</span></li>
                <li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_大陆.html">喜剧</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_香港.html">恐怖</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_台湾.html">爱情</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_韩国.html">动作</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_美国.html">科幻</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_美国.html">战争</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_美国.html">犯罪</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_美国.html">剧情</a></li>
				<li><a href="http://www.youku.com/v_olist/c_96_s_1_d_1_a_美国.html">其它</a></li>
			</ul>    
		</div>
   
    </div>   
    <!--
	<div class="yk-filter-handle">
		<div id="filter_handle" class="btn-handle" style="display: block;">
		</div>
	</div>-->
</div>
<!--end 筛选器-->
<!--begin the main-->
<div class="yk-row yk-v-80">
	<!--begin the 1th 第一个电影div-->
	<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
	<!--end of the 1th tv-->
	
	
	<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
		<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
		<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
		<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
		<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
		<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
		<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="http://r4.ykimg.com/05160000529FD8B26758396F360D66B0" alt="风暴">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb"><span class="p-rating"><em>8.</em>7</span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="http://www.youku.com/show_page/id_zb583d13e243011e38b3f.html" target="_blank" title="风暴">风暴</a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:</label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank">刘德华</a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:</label><span class="p-num">303.3万</span>
					</div>
                </div>
            </div>
        </div>
	
</div>
<!--end the main-->
