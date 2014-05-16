<?php

function html_print_index_content()
{
    $output  = "<div class=\"body_split\"></div>";
    $output .= "<div class=\"menu\">";
    $output .= "<a href=\"user_info.php\">我的账户</a>";
    $output .= "<a href=\"action.php\">业务办理</a>";
    $output .= "<a href=\"tv.php\">影视专区</a>";
    $output .= "<a href=\"manual.php\">如何使用</a>";
    $output .= "<a href=\"manual.php\">常见问题</a></div>";
    print($output);
}


?>
