<?php
$name = Yii::app()->name;
$title = "影视专区";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "index";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、浏览本网站所产生的所有流量均被视为内部流量，不计入计费带宽。</br>";
Yii::app()->session['board_msg'] .= "2、经过测试，安卓环境下，猎豹浏览器看电影效果最好；";
Yii::app()->session['board_msg'] .= "iphone下都比较不错。建议大家选择合适的浏览器观看</br>";
Yii::app()->session['board_msg'] .= "3、为了提高小站的电影质量，我们在选片的时候精挑细选,";
Yii::app()->session['board_msg'] .= "大部分电影都是全球票房过50亿的超级经典大片</br>";
Yii::app()->session['board_msg'] .= "4、本网站内的所有国外大片都配有中文字幕，大家可以放心购买观看。</br>";



?>
<!--筛选器-->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/filter.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/tv.css" />
<div class="yk-filter yk-filter-open" id="filter">
    <div class="yk-filter-panel">
        <div class="item item-moreshow">
			<label>分类</label>
			<ul id="filter_kind">
            <?php foreach($kind as $k): ?>
                <?php if ($k["selected"] == 0): ?>
				<li id='filter_kind_<?php echo $k['id']?>' name='<?php echo $k['name']?>'><a onclick="selectKind('kind','<?php echo $k['id']?>')"><?php echo $k['name']?></a></li>
                <?php elseif ($k["selected"] == 1): ?>
                <li class="current" id='filter_kind_<?php echo $k['id']?>' name='<?php echo $k['name']?>'><span><?php Yii::app()->session['kind_cur'] = $k['id'];echo $k['name'];?></span></li>
                <?php endif; ?>
			 <?php endforeach; ?>
            </ul>
            
		</div>
        <div class="item item-moreshow">
			<label>地区</label>
			<ul id="filter_area">
            <?php foreach($area as $k): ?>
                <?php if ($k["selected"] == 0): ?>
				<li id='filter_area_<?php echo $k['id']?>'><a onclick="selectKind('area','<?php echo $k['id']?>')"><?php echo $k['name']?></a></li>
                <?php elseif ($k["selected"] == 1): ?>
                <li class="current" id='filter_area_<?php echo $k['id']?>'><span><?php Yii::app()->session['area_cur'] = $k['id'];echo $k['name']?></span></li>
                <?php endif; ?>
			 <?php endforeach; ?>
            </ul>
            
		</div>
        <div class="item item-moreshow">
			<label>类型</label>
			<ul id="filter_type">
            <?php foreach($type as $k): ?>
                <?php if ($k["selected"] == 0): ?>
				<li id='filter_type_<?php echo $k['id']?>'><a onclick="selectKind('type','<?php echo $k['id']?>')"><?php echo $k['name']?></a></li>
                <?php elseif ($k["selected"] == 1): ?>
                <li class="current" id='filter_type_<?php echo $k['id']?>'><span><?php Yii::app()->session['type_cur'] = $k['id'];echo $k['name']?></span></li>
                <?php endif; ?>
			 <?php endforeach; ?>
            </ul>
            
		</div>
		
		<div class="item item-moreshow">
			<label>搜索</label>
			<input class="i_stext c_a5" value=""  placeholder="影片名/演员名/导演名"  id='keys' name='keys' style="width:60%"/>
            <input onclick="search()" id="air_tv_search" type="button" class="i_slbut">
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
<div class='yk-filterresult'>
<div class="yk-row yk-v-80">
	<!--begin the 1th 第一个电影div-->
 <?php if ($total_records == 0): ?>
 无内容
 <?php else: ?>

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
						<a href="/index.php?r=tv/detail&id=<?php echo $row['id']?>" target="_blank">
                            <?php print(air_format_str($row['name'],8)); ?>
                        </a>
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
<?php endif; ?>	
</div><!--end of row-->
<?php if ($total_records > 0): ?>
<div id="pager" class="yk-pager"> 
		<?php 
		$this->widget('CLinkPager',array( 
			'header'=>'', 
			'firstPageLabel' => '首页', 
			'lastPageLabel' => '末页', 
			'prevPageLabel' => '上一页', 
			'nextPageLabel' => '下一页', 
			'pages' => $pages, 
			'maxButtonCount'=>6 
			) 
		); 
		?> 
</div><!--end of page-->
<?php endif; ?>
</div><!--end of result-->
<!--end the main-->
<script type="text/javascript">
	var kind_id='<?php  echo Yii::app()->session['kind_cur']?>';
	var area_id='<?php  echo Yii::app()->session['area_cur']?>';
	var type_id='<?php  echo Yii::app()->session['type_cur']?>';
	//alert(kind_id);
	//alert(area_id);
	//alert(type_id);
	$(document).ready(function(){
		/*begin to for placeholder*/
		var funPlaceholder=function(element){
			var placeholder='';   
			if(element&&!("placeholder"in document.createElement("input"))&&(placeholder=element.getAttribute("placeholder"))){
				element.onfocus=function(){
					if(this.value===placeholder){
						this.value="";            
					}
					this.style.color='';       
				 };        
				element.onblur=function(){
					if(this.value===""){
						this.value=placeholder;                
						this.style.color='graytext';                
					}
				};                
				//样式初始化
				if(element.value===""){
					element.value=placeholder;            
					element.style.color='graytext';            
				}
		}};
		funPlaceholder(document.getElementById("keys"));
		/*end of for holder*/
		
	});
	function selectKind(name,id){
		var li_id="#filter_"+name+"_"+id;
		var li_name=$(li_id).attr("name");
		//
		if(name=='kind'){
			kind_id=id;
		}else if(name=='area'){
			area_id=id;
		}else if(name=='type'){
			type_id=id;
		}
		//添加类 替换，加上
		/*
		$(li_id).addClass('current');
		var html="";
		html="<span>"+li_name+"</span>";
		$(li_id).html(html);
		*/
		//把选中的li id的class置为current，把其他选中的置为空
		/*
		if(name=='kind'){
			$("#kind_filter li").each(function(){
				$(this).removeClass('current');
			});
			
		}else if(name=='area'){
			$("#area_filter li").each(function(){
				$(this).removeClass('current');
			});
		}else if(name=='type'){
			$("#type_filter li").each(function(){
				$(this).removeClass('current');
			});
		}*/
		//删除click种类的当前class
		/*
		$("ul").find("li[class='current']").each(function(){
			var tmp_id=$(this).attr('id');
			var tmp_name=$(this).attr('name');
			var html="";
			if(tmp_id.indexOf(name)>0){
				$(this).removeClass('current');
				
				//<a onclick="selectKind('kind','1')">电影</a>
				html="<a onclick='selectKind('"+name+"','"+id+"')'>"+tmp_name+"</a>";
				//加上新的html
				alert(html);
				$(this).html(html);
			}
		});
		*/
		
		//找到为current的元素id
		/*
		kind_id=<?php echo Yii::app()->session['kind_cur']?>;
		area_id=<?php echo Yii::app()->session['area_cur']?>;
		type_id=<?php echo Yii::app()->session['type_cur']?>;
		*/
		//ajax操作
		document.location.href="index.php?r=tv&kind_id="+kind_id+"&area_id="+area_id+"&type_id="+type_id;
                
	}
	function search(){
		var search_value=encodeURI($("#keys").val());
		document.location.href="index.php?r=tv&keys="+search_value;
         
	}
	
</script>
