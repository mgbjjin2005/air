<?php

require_once('init.php');
require_once('include/html_index.inc');

html_print_head($g_name."欢迎你");
html_print_top_menu();
html_print_nav("欢迎你", "index.php");
html_print_index_content();
$title = "公告栏";
$desc = $g_name."致力于为大家提供高速的WIFI服务；使用".$g_name."不仅可以畅游互联网, 更有超过1000部超高清国内外大片可以不用等待立即观看。赶紧过来体验吧^_^";
html_print_msg_board($title, $desc);
html_print_footer();

?>
