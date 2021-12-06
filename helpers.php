<?php

session_start();
require_once(__DIR__ . "/db.php");
function is_logged_in() {
    return isset($_SESSION["user"]);
} 


function has_role($role) {
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] == $role) {
                return true;
            }
        }
    }
    return false;
}

function get_username() {
    if (is_logged_in() && isset($_SESSION["user"]["username"])) {
        return $_SESSION["user"]["username"];
    }
    return "";
}

function get_email() {
    if (is_logged_in() && isset($_SESSION["user"]["email"])) {
        return $_SESSION["user"]["email"];
    }
    return "";
}

function get_user_id() {
    if (is_logged_in() && isset($_SESSION["user"]["id"])) {
        return $_SESSION["user"]["id"];
    }
    return -1;
}

function safer_echo($var) {
    if (!isset($var)) {
        echo "";
        return;
    }
    echo htmlspecialchars($var, ENT_QUOTES, "UTF-8");
}

//for flash feature
function flash($msg) {
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $msg);
    }
    else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $msg);
    }

}

function getMessages() {
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}

function getURL($path)
{
    if (substr($path, 0, 1) == "/") {
        return $path;
    }
    return $_SERVER["CONTEXT_PREFIX"] . "/gitClone1/project/$path";
}
function safe_get($arr, $key, $default = "")
{
    if (is_array($arr) && isset($arr[$key])) {
        return $arr[$key];
    }
    return $default;
}
function changePoints($user_id, $points, $reason)
{
    $db = getDB();
    $query = "INSERT INTO PointsHistory (user_id, points_change, reason) VALUES(:uid, :change, :reason)";

    $stmt = $db->prepare($query);
    $r = $stmt->execute(
        [":uid" => $user_id, 
        ":change" => $points,
        ":reason" =>$reason]);
   // if ($r) {
    	/*if(checkUserStats() = 0){
    		$query = "INSERT INTO tfp_userstats (user_id, points) VALUES (:uid, :points)";
    	}*/ 
        // if i include the code above, the site breaks down

	        $querya = "UPDATE tfp_userstats set points = IFNULL((SELECT sum(points_change) FROM PointsHistory where user_id = :uid),0) WHERE user_id = :uid";
	        $stmt = $db->prepare($querya);
	        $r = $stmt->execute([":uid" => $user_id]);

	        //refresh session data
	       /* if(safe_get($_SESSION, "user", false) && safe_get($_SESSION["user"], "points", false)){
	            $_SESSION["user"]["points"] = get_points_balance();
	        } */
	        $_SESSION["user"]["points"] = get_points_balance();
	        return $r;
    	//}//end of else
  //  }//end of 1st if
    return false;
}
function get_seconds_since_dates($date1, $date2 = NULL)
{
    if (!isset($date2)) {
        $date2 = new DateTime();
    }
    if (!$date1 instanceof DateTime) {
        $date1 = new DateTime($date1);
    }
    return $date2->getTimestamp() - $date1->getTimestamp();
}
function get_seconds_since_start()
{
    $started = safe_get($_SESSION, "started", false);
    if ($started) {
        try {
            if (is_string($started)) {
                $started = new DateTime($started);
            }
            $now = new DateTime();
            if ($started < $now) {
                return $now->getTimestamp() - $started->getTimestamp();
            }
        } catch (Exception $e) {
            //invalid date
            error_log($e->getMessage());
        }
    }
    return -1;
}
function is_valid_game($isWin)
{
    $seconds = get_seconds_since_start();
    error_log("Seconds $seconds");
    $min = 10; 
    if (!$isWin) {
        $min = 5;
    }
    $max = 3600;
    error_log("min $min max $max");
    return ($seconds >= $min && $seconds <= $max);
}
function update_experience($user_id){
    $db = getDB();
    $query = "UPDATE tfp_userstats set experience = (select (SUM(IFNULL(score, 0)) * 10) FROM Scores WHERE user_id = :uid) WHERE user_id = :uid";
     $stmt = $db->prepare($query);
        $r = $stmt->execute([":uid" => $user_id]);
        return $r;
}

function get_points_balance(){
    $uid = get_user_id();
    $db = getDB();
    //only change this
    $query = "SELECT IFNULL(points,0) as `points` from tfp_userstats where user_id = :id";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":id"=>$uid]);
    if($r){
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($stats["points"])){
            return (int)$stats["points"];
        }
    }
    return 0;
}
function paginate($query, $params = [], $per_page = 10) {
    global $page;
    if (isset($_GET["page"])) {
        try {
            $page = (int)$_GET["page"];
        }
        catch (Exception $e) {
            $page = 1;
        }
    }
    else {
        $page = 1;
    }
    $db = getDB();
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = 0;
    if ($result) {
        $total = (int)$result["total"];
        //flash("total is $total");
    }
    global $total_pages;
    $total_pages = ceil($total / $per_page);
    global $offset;
    $offset = ($page - 1) * $per_page;
    //return ("Page " .  . " of " . total_pages);
}
?>
