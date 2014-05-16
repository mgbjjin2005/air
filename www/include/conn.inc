<?php

function air_check_table_exist($table_name, $db_link) {

    $result = mysql_query("show tables", $db_link);
    while($row = mysql_fetch_assoc($result)) {
        $arr = array_values($row);
        if ($table_name == $arr[0]) {
            return true;
        }
    }
    return false;
}

function air_check_record_exists($table_name, $where, $db_link)
{
    $sql = "SELECT COUNT(1) AS cnt FROM $table_name WHERE $where";
    $arr = get_query_result($sql, $db_link);
    return $arr[0]['cnt'] > 0;
}

function air_exec_sql($sql, $db_link)
{
    $result = mysql_query($sql, $db_link) or
              exit_err("Could not execute $sql: " . mysql_error($db_link)."\n".get_stack_trace());

    if (is_resource($result)) {
        while ($row = mysql_fetch_assoc($result)) {
            var_dump($row);
        };
        mysql_free_result($result);
    }
}

function air_get_query_result($sql, $db_link)
{
    $result = mysql_query($sql, $db_link) or
              print(mysql_error($db_link));
    $arr = array();

    while ($row = mysql_fetch_assoc($result)) {
        array_push($arr, $row);
    }
    mysql_free_result($result);
    return $arr;
}

function air_purify_string($s)
{
    $s = str_replace('%', '\\%', $s);
    $s = str_replace('_', '\\_', $s);
    $s = str_replace('*', '%', $s);
    return mysql_escape_string($s);
}

function init_db($db_name)
{
    $db_host = "localhost";
    $db_port = "3306";
    $db_user = "air";
    $db_pass = "***King1985***";

    $ret = mysql_connect("$db_host:$db_port", $db_user,"$db_pass" , TRUE) or
           exit_err("Could not connect to $db_host using $db_user", FALSE);

    mysql_select_db($db_name, $ret) or
           exit_err("Could not use db $db_name: " . mysql_error($ret), FALSE);

    $sql = "SET NAMES UTF8";
    mysql_query($sql, $ret) or
           exit_err("Could not execute $sql: " . mysql_errno($ret) ." ".mysql_error($ret), FALSE);

    return $ret;
}

function close_db($db_link)
{
    if ($db_link && is_resource($db_link)) {
        @mysql_close($db_link);
    }
}

?>
