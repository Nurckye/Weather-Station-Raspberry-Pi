<?php

  function get_data_months($db, $month) {
    $sql = sprintf("SELECT * FROM weather_data WHERE month='%s'",
          $month);
    $res = array();
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
       array_push($res, $row);
    }
    return $res;
  }

  function get_data_today($db, $current_month, $current_day) {
    $sql = sprintf("SELECT * FROM weather_data WHERE month='%s' AND month_day='%s'",
          $current_month, $current_day);
    $res = array();
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
       array_push($res, $row);
    }
    return $res;
  }

  function get_last_entry($db, $current_month, $current_day){
    $sql = sprintf("SELECT * FROM weather_data WHERE month='%s' AND month_day='%s' ORDER BY seconds DESC",
          $current_month, $current_day);
    $ret = $db->query($sql);
    if ( ! empty($ret))
      $res = $ret->fetchArray(SQLITE3_ASSOC);
    return $res;
  }

  function get_last_10_minutes($db, $current_month, $current_day, $seconds_since_midnight) {
      $res = array();
      for( $i = 0; $i <= 4; $i++){
        $sql = sprintf("SELECT * FROM weather_data WHERE month='%s' AND month_day='%s' AND seconds < %s AND seconds > %s",
          $current_month, $current_day, (int)$seconds_since_midnight - 120 * $i , (int)$seconds_since_midnight - 120 * ($i + 1));
        $ret = $db->query($sql);
         if ( ! empty($ret))
          array_push($res, $ret->fetchArray(SQLITE3_ASSOC));

      }
      return $res;
  }

  class MyDB extends SQLite3 {
      function __construct() {
         $this->open('weather_database.db');
      }
  }
   $db = new MyDB();
   if(!$db) {
      echo $db->lastErrorMsg();
   }

  $current_month = date('F');
  $current_day = date('j');
  $all_months = json_encode(array(
   "January" => get_data_months($db, "January"),
   "February" => get_data_months($db, "February"),
   "March" => get_data_months($db, "March"),
   "April" => get_data_months($db, "April"),
   "May" => get_data_months($db, "May"),
   "June" => get_data_months($db, "June"),
   "July" => get_data_months($db, "July"),
   "August" => get_data_months($db, "August"),
   "September" => get_data_months($db, "September"),
   "October" => get_data_months($db, "October"),
   "November" => get_data_months($db, "November"),
   "December" => get_data_months($db, "December"),
  ));

  $today = json_encode(get_data_today($db, date('F'), date('j')));
  //
  $seconds_since_midnight = (int)date('H') * 3600 + date('i') * 60;
  $last_5_hours = json_encode(get_last_10_minutes($db, date('F'), date('j'), $seconds_since_midnight));
  //
  $last_entry = json_encode(get_last_entry($db, date('F'), date('j')));

  $current_month = json_encode(date('F'));
  $current_day = json_encode(date('j'));
  $db->close();
?>
