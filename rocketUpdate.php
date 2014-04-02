<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
date_default_timezone_set('America/Montreal');
if(isset($_REQUEST['sid'])) {
  $timeoutSeconds = 30;
  $filename = dirname(__FILE__) . '/counter.db';
  $db = new PDO('sqlite:' . $filename);
  $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  // 1. create a sqlite database table with the following columns
  //    if it does not already exist in the current directory:
  //    id, timestamp, session_id
  $stmt = $db->prepare('CREATE TABLE IF NOT EXISTS counter (id INTEGER PRIMARY KEY AUTOINCREMENT, timestamp TIMESTAMP, session_id varchar(32))');
  $stmt->execute();
  // 2. get PHP session_id with session_id call
  $sid = $_REQUEST['sid'];
  // 3. remove all rows with timestamps less than current_time - timeout.
  $mindate = time() - $timeoutSeconds;
  $mindate = date('Y-m-d H:i:s', $mindate);
  $stmt = $db->prepare("DELETE FROM counter WHERE timestamp < :timestamp");
  $stmt->execute(array(':timestamp' => $mindate));
  // 4. add a row for session_id if it does not exist, or update
  //    the timestamp of the row with the provided session_id if it does exist
  $stmt = $db->prepare('select id from counter where session_id = :sid');
  $stmt->execute(array(':sid' => $sid));
  $rows = $stmt->fetch(PDO::FETCH_NUM);
        
  $now = date('Y-m-d H:i:s');
  if($rows === FALSE) {
    $stmt = $db->prepare('INSERT INTO counter (timestamp, session_id) VALUES(:timestamp, :sid )');
    $ret = $stmt->execute(array(':sid' => $sid, ':timestamp' => $now));
  } else {
    $id = $rows[0];
    $stmt = $db->prepare('UPDATE counter SET timestamp=:timestamp WHERE id= :id');
    $ret = $stmt->execute(array(':id' => $id, ':timestamp' => $now));
  }
        
  // 5. return json of the following form to report on the number
  //    of users: {"count": n}    for instance:   {"count": 4}
  $stmt = $db->prepare('select COUNT(id) from counter');
  $stmt->execute();
  $rows = $stmt->fetch(PDO::FETCH_NUM);
  $array = array();
  $array['count'] = $rows[0];
  header('Access-Control-Allow-Origin: *');
  header('Content-type: application/json');
  die(json_encode($array));
}
?>