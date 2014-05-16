<?php
/*所有html打印输出的函数都写在这个文件*/

function html_print_head($title)
{
    global $g_name;
    $output  = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" ";
    $output .= "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $output .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
    $output .= "<head>";
    $output .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
    $output .= "<title>$title</title>";
    $output .= "<meta name=\"viewport\" content=\"width=device-width; initial-scale=1.0; maximum-scale=1.0;\">";
    $output .= "<meta http-equiv=\"keywords\" content=\"$g_name\" />";
    $output .= "<meta http-equiv=\"description\" content=\"$g_name\" />";
    $output .= "<link href=\"css/style.css\" rel=\"stylesheet\" type=\"text/css\" />";
    $output .= "</head>";
    print($output); 
}

function html_print_top_menu()
{
    $output  = "<body>";
    $output .= "<div class=\"top_menu\">";
    $output .= "欢迎你,<a href=\"user_info.php\">".$_SESSION["username"]."</a>&nbsp;|&nbsp;";
    $output .= "<a href=\"user_message.php\">8条消息</a></div>";
    print($output);
}

function html_print_msg_board($title, $msg)
{
    $output  = "<h2 class=\"board\">$title</h2>";
    $output .= "<div class=\"content\">";
    $output .= "<p>$msg</p></div>";
    print($output);
}


function html_print_footer()
{
    global $g_name;
    $output  = "<div class=\"footer\">";
    $output .= "<p>Design By <a href=\"http://www.air-wifi.cn\">$g_name</a></p></div>";
    print($output);
}


function html_print_err_result($msg)
{
    print("$msg<br>");
    exit();
}

function html_print_nav($title, $url)
{
    global $g_name;
    $output  = "<div class=\"logo\">";
    $output .= "<h1><a href=\"index.php\">$g_name</a><span class=\"nav\">&gt</span>";
    $output .= "<a href=\"$url\">$title</a></h1></div>";
    print($output);
}


?>
