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
            <?php foreach($kind as $k): ?>
                <?php if ($k["selected"] == 0): ?>
				<li ><a onclick="selectKind('<?php echo $k['id']?>')"><?php echo $k['name']?></a></li>
                <?php elseif ($k["selected"] == 1): ?>
                <li class="current"><span><?php echo $k['name']?></span></li>
                <?php endif; ?>
			 <?php endforeach; ?>
            </ul>
            
		</div>
        <div class="item item-moreshow">
			<label>地区</label>
			<ul>
            <?php foreach($area as $k): ?>
                <?php if ($k["selected"] == 0): ?>
				<li ><a onclick="selectKind('<?php echo $k['id']?>')"><?php echo $k['name']?></a></li>
                <?php elseif ($k["selected"] == 1): ?>
                <li class="current"><span><?php echo $k['name']?></span></li>
                <?php endif; ?>
			 <?php endforeach; ?>
            </ul>
            
		</div>
        <div class="item item-moreshow">
			<label>类型</label>
			<ul>
            <?php foreach($type as $k): ?>
                <?php if ($k["selected"] == 0): ?>
				<li ><a onclick="selectKind('<?php echo $k['id']?>')"><?php echo $k['name']?></a></li>
                <?php elseif ($k["selected"] == 1): ?>
                <li class="current"><span><?php echo $k['name']?></span></li>
                <?php endif; ?>
			 <?php endforeach; ?>
            </ul>
            
		</div>

        <!--
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
		</div>-->
        <!--
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
        -->
   
    </div>   
    <!--
	<div class="yk-filter-handle">
		<div id="filter_handle" class="btn-handle" style="display: block;">
		</div>
	</div>-->
</div>
<!--end 筛选器-->
<!--begin the main-->
<div class=" yk-row yk-v-80">
	<!--begin the 1th 第一个电影div-->
     <?php if ($total_records == 0): ?>
     无内容
     <?php endif; ?>
    <?php foreach($recoreds as $row): ?>
	<div class="yk-col3">
            <div class="p p-small">
				<!--大图片以及图片上面的内容-->
                <div class="p-thumb">
                    <img src="<?php echo $row['poster_url'] ?>" alt="<?php echo $row['name']?>">
                    <div class="p-thumb-tagrt">
                        <i class="ico-SD" title="超清"></i>
                    </div>
					<!--
					<div class="p-thumb-tagrt">
                        <i class="ico-1080P" title="1080P"></i>
                    </div>
					-->
                    
                    <div class="p-thumb-taglb"><span class="p-status">正片</span></div>
                    <div class="p-thumb-tagrb">
                        <span class="p-rating">
                            <?php 
                                $arr=explode(".",$row['douban_num']);
                                if(count($arr)>0){
                                    if(count($arr)==1){
                                        echo "<em/>$arr[0].<//em>0";
                                    }else if(count($arr)==2){
                                        echo "<em>$arr[0].</em>$arr[1]";
                                    }
                                }else{
                                    echo "<em>0.</em>0";
                                }
                            ?>
                        </span></div>
                    <div class="p-thumb-overlay"></div>
                </div>
				<!--电影的播放链接-->
                <div class="p-link">
                    <a href="/index.php?r=tv/detail&id=<?php echo $row['id']?>" target="_blank" title="<?php echo $row['name']?>"></a>
                </div>
                <div class="p-isdrama"></div>
				<!--电影的标题，主演及播放次数信息-->
                <div class="p-meta pa">
                    <div class="p-meta-title">
						<a href="/index.php?r=tv/detail&id=<?php echo $row['id']?>" target="_blank" title="<?php echo $row['name']?>"><?php echo $row['name']?></a>
					</div>
                    <div class="p-meta-entry">
						<span class="p-actor"><label>主演:<?php echo $row['actors']?></label>
                        <a href="http://www.youku.com/star_page/uid_UNDU0NjQ=.html" target="_blank"><?php echo $row['actors']?></a>
                    </span>
                    </div>
					<div class="p-meta-entry">
						<label>播放:<?php echo $row['pv']?></label><span class="p-num"><?php echo $row['pv']?></span>
					</div>
                </div>
            </div>
        </div>
	<!--end of the 1th tv-->
	 <?php endforeach; ?>
	
</div>
<!--end the main-->
