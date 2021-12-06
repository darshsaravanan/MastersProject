<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php

$db = getDB();

//get users points and show on profile page
     $stmt = $db->prepare("SELECT points from tfp_userstats WHERE user_id = :id LIMIT 1");
    $params = array(":id" => get_user_id());
    $r = $stmt->execute($params);
    $e = $stmt->errorInfo();
    //echo var_export($e, true);
    /*if ($e[0] == "23000") 
    {
        $response["message"] = "query executed";
    }
    else
    {
        $response["message"] = "did not execute";
    } */

    if($r){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $profilePoints = (int)($result?$result["points"]:0);
        flash("Your account has " .$profilePoints . " points.");
    }




$pub = "public";
$priv = "private";
$uid = get_user_id();
//update status to public
if (isset($_POST["makePub"])) {
    $stmt = $db->prepare("UPDATE Users set setting = :status where id = :uid");
        $r = $stmt->execute([":status" => $pub, ":uid" => $uid]);
        //flash("line 73 " . count($r));
        if ($r) {
            flash("Your profile is public");
        }
        else {
            flash("Error updating profile");
        }
}
//update status to private
if (isset($_POST["makePriv"])) {
    $stmt = $db->prepare("UPDATE Users set setting = :status where id = :uid");
        $r = $stmt->execute([":status" => $priv, ":uid" => $uid]);
        //flash("line 73 " . count($r));
        if ($r) {
            flash("Your profile is private");
        }
        else {
            flash("Error updating profile");
        }
}

if (isset($_POST["saved"])) {
    $isValid = true;
    $newEmail = get_email();
    if (get_email() != $_POST["email"]) {
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Email already in use");
            $isValid = false;
        }
        else {
            $newEmail = $email;
        }
    }
    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Username already in use");
            $isValid = false;
        }
        else {
            $newUsername = $username;
        }
    }
    if ($isValid) {
        $stmt = $db->prepare("UPDATE Users set email = :email, username= :username where id = :id");
        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_user_id()]);
        if ($r) {
            flash("Updated profile");
        }
        else {
            flash("Error updating profile");
        }
        if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
            if ($_POST["password"] == $_POST["confirm"]) {
                $password = $_POST["password"];
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                $r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
                if ($r) {
                    flash("Reset Password");
                }
                else {
                    flash("Error resetting password");
                }
            }
        }
        
        $stmt = $db->prepare("SELECT email, username from Users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $email = $result["email"];
            $username = $result["username"];
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
        }
    }
    else {
    }
}


?>
<html>    
    <body>
        <form method="POST">
            <table style="width:100%">
            <div id="currStatus"></div>
            <tr>
        <td>  <input class="btn btn-primary" type="submit" name="makePub" value="Set your profile to Public"/>  </td>
        <td>  <input class="btn btn-primary" type="submit" name="makePriv" value="Set your profile to Private"/>  </td>
            </tr>
            </table>
        </form>
        
        
    <form method="POST">
        <table style="width:100%">

    <form method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" value="<?php safer_echo(get_email()); ?>"/>
        <label for="username">Username</label>
        <input type="text" maxlength="60" name="username" value="<?php safer_echo(get_username()); ?>"/>

        <!-- DO NOT PRELOAD PASSWORD-->

	<label for="crpw">Current Password</label>
	<input type="cpassword" name="cpassword"/>

        <label for="pw">Password</label>
        <input type="password" name="password"/>
        <label for="cpw">Confirm Password</label>
        <input type="password" name="confirm"/>
        <input type="submit" name="saved" value="Save Profile"/>
    </form>
    <a href = "scorehistory.php"><button type="button"> Previous scores </button></a>

    <form action="top.php" method ="get">
    <label for="score">Choose the top score: </label>
    <input list ="scores" name = "score" id ="score">
    <datalist id = "scores">
        <option value= "Top weekly">
        <option value="Top monthly">
        <option value ="Top Lifetime">
    </datalist>
    <input type = "submit">
    </form> 
<?php require(__DIR__ . "/partials/flash.php");
