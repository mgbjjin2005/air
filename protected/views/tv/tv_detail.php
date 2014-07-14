<?php
$name = Yii::app()->name;
$title = "电影详情";
$this->pageTitle = $name." ".$title;

Yii::app()->session['nav'] = "tv";
Yii::app()->session['nav_msg'] = $title;
Yii::app()->session['board_name'] = $title;
Yii::app()->session['board_msg']  = "1、浏览本网站所产生的流量算内部流量，不会计入计费流量。</br>";
Yii::app()->session['board_msg'] .= "2、所有的外语片均有中文字幕，可以放心的选择观看。</br>";
Yii::app()->session['board_msg'] .= "3、不同的浏览器对视频的播放能力差别很大，建议优先尝试使用猎豹/UC/百度浏览器进行观看.</br>";
?>
<!--筛选器-->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/filter.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/tv.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/tv/tv_detail.css" />



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
       }``````````````````````````````````````````````````````````````````````````````````
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

<div>
    <h2><span class="title">
        <?php
            print($info['m_kind_desc']." : ". $info['m_chs_name']);
            $time=explode("-",$info['m_show_date']);
            print(" (".$time[0].")");
        ?>
        </span>
    </h2>
</div>

<div class="s_main">
    <div style="text-align:center">
        <img style="width:200px;heigth:300px;" src="<?php echo $info['m_pic_path']?>">
    </div>

    <div class="interact-box">
        <div class="interact-skin" id="player_tabsbox" style="width: auto;">
            <ul class="tabs">
                <li id="subnav_showDesc" _to="videoinfo">
                    <a onclick="changeTab(this,&#39;showDesc&#39;)">详情介绍</a>
                </li>
                <li class="current" id="subnav_showSeries" _to="showlist" _type="side">
                    <a onclick="changeTab(this,&#39;showSeries&#39;)" >剧集列表</a>
                </li>
            </ul>
        </div>
    </div>


    <div class="showlists" id="reload_showSeries">
        <div class="items panel">
            <ul class="rel-aspect">
                <?php foreach($detail as $key=>$row): ?>
                    <?php if ($key == 0): ?>
                        <li class="current">

                    <?php else: ?>
                        <li>
                    <?php endif; ?>

                        <a class="A" href="index.php?r=tv/toWatch&id=<?php echo $row['auto_id']?>">
                            <div class="headline"><?php print(air_format_str($row['m_chs_desc'], 10) . " / ".$row['m_price']."豆")?></div></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!--影片基本信息-->
    <div id="reload_showDesc" class="detail" style="display:none;">
        <ul class="baseinfo">

            <li><span class="labelClass">豆瓣评分:</span><?php echo $info['m_douban_num']?></li>
            <li><span class="labelClass">电影名  :</span><?php echo $info['m_original_name']?></li>
            <li><span class="labelClass">中文名  :</span><?php echo $info['m_chs_name']; ?></li>
            <li><span class="labelClass">上映时间:</span><?php echo $info['m_show_date']; ?></li>
            <li><span class="labelClass">地区    :</span><?php echo $info['m_area_desc']; ?></li>
            <li><span class="labelClass">类型    :</span><?php echo $info['m_type_desc']; ?></li>
            <li><span class="labelClass">导演    :</span><?php echo $info['m_director']; ?></li>
            <li><span class="labelClass">主演    :</span><?php echo $info['m_main_actors']; ?></li>
            <li><span class="labelClass">播放次数:</span><?php echo $info['m_total_pv']; ?>次</li>
            <li><span class="labelClass">时长    :</span><?php echo $info['m_time_length']; ?>分钟</li>
            <li class="clear"></li>

        </ul>
        <div class="overview_wrap">
            <span class="short"><?php print("<br/>&nbsp&nbsp&nbsp&nbsp".air_output_des($info['m_des'])); ?></span>
        </div>

    </div>
</div>
