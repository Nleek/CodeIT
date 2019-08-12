<?php

require('../includes/includes.php');
require('notifications_backend.php');

# This file deals with challenges

function get_challenge_progress($uuid, $ucid){
    # Gets a user's progress in a particular challenge.
    # Returns Array[ progress, completed ]

    GLOBAL $connection;

    $challenge = $connection -> prepare("SELECT * FROM `user_challenge_progress` WHERE  `uuid` = ? AND `ucgid` = ?");
    $challenge -> bind_param("ss", $uuid, $ucid);
    $challenge -> execute();
    $challenge -> store_result();
    $challenge -> bind_result($uuid, $ucgid, $attempts, $completed, $id);
    $challenge -> fetch();
    $challenge -> close();

    return Array($attempts, $completed);

}

function get_battle_state($uuid, $ucgid){
    # Checks if a challenge is a battle for a particular user.
    # Returns false or an array[ 'challenged' / 'challenger', challenger_name / challenged_name ]

    GLOBAL $connection;

    // Check if the users was the initiator of a challenge
    $battle = check_exists('user_battles', Array('challenger','challenge'), Array($uuid, $ucgid));
    if($battle){

        $battle_db = $connection -> prepare("SELECT * FROM `user_battles` WHERE `challenger` = ? AND `challenge` = ?");
        $battle_db -> bind_param("ss", $uuid, $ucgid);
        $battle_db -> execute();
        $battle_db -> store_result();
        $battle_db -> bind_result($challenger_uuid, $challenged_uuid, $challenge_id, $id);
        $battle_db -> fetch();
        $battle_db -> close();
        $name = json_decode(get_user($challenged_uuid))[2];

        return Array("You're the Challenger", $name);

    }
    else{

        $battle = check_exists('user_battles', Array('challenged','challenge'), Array($uuid, $ucgid));
        if($battle){

            $battle_db = $connection -> prepare("SELECT * FROM `user_battles` WHERE `challenged` = ? AND `challenge` = ?");
            $battle_db -> bind_param("ss", $uuid, $ucgid);
            $battle_db -> execute();
            $battle_db -> store_result();
            $battle_db -> bind_result($challenger_uuid, $challenged_uuid, $challenge_id, $id);
            $battle_db -> fetch();
            $battle_db -> close();
            $name = json_decode(get_user($challenger_uuid))[2];

            return Array("You've been Challenged", $name);

        }

    }

    return false;

}

function list_challenges($uuid){
    # Gets all of the challenges.
    # Returns JSON Array[ [ details, battle, progress ] ]

    if(check_perms(10)){
        $challenges_db = select_all('challenges');
    }
    else{
        $challenges_db = select_uid('challenges', 'published', '1');
    }

    $challenges = Array();
    $challenges_db -> bind_result($lang,$name,$desc,$cbid,$auth,$pub,$diff,$ucgid,$id);
    while($challenges_db -> fetch()){

        $author = json_decode(get_user($auth));
        $details = Array("language" => $lang, "name" => $name, "published" => $pub, "description" => str_replace('\"','"', str_replace('"\n"',"\n", $desc)), "author" => $author[2], "difficulty" => $diff, "ucbid" => $cbid, "ucgid" => $ucgid);
        $battle = get_battle_state($uuid, $ucgid);
        $progress = get_challenge_progress($uuid, $ucgid);
        array_push($challenges, Array("details" => $details, "battle" => $battle, "progress" => $progress));

    }

    return json_encode($challenges);

}

function get_challenge($ucgid){
    # Looks up a challenge by ucgid.
    # Return Array

    $challenge = select_uid('challenges', 'ucgid', $ucgid);
    $challenge -> bind_result($lang,$name,$desc,$cbid,$auth,$pub,$diff,$ucgid,$id);
    $challenge -> fetch();
    $challenge -> close();

    return Array("language" => $lang, "name" => $name, "description" => $desc, "author" => json_decode(get_user($auth))[2], "difficulty" => $diff, "ucbid" => $cbid, "ucgid" => $ucgid);

}

function new_battle($uuid){
    # Adds a battle entry in the DB. Sends an alert to the recipient and sender.
    # Returns string.

    GLOBAL $connection;

    $challenge = is_posted('challenge');
    $challenged = is_posted('challenged');
    if($challenged){

        if(!get_challenge_progress($uuid, $challenge)[1]){
            if(!get_challenge_progress($challenged, $challenge)[1]){

                $battle = $connection -> prepare("INSERT INTO `user_battles`(`challenger`, `challenged`, `challenge`) VALUES (?, ?, ?)");
                $battle -> bind_param("sss", $uuid, $challenged, $challenge);
                if($battle -> execute()){

                    new_notification("Challenge Sent!","You challenged ".json_decode(get_user($challenged))[2]."!","",$uuid);
                    new_notification("You've been challenged!", json_decode(get_user($uuid))[2]." Challenged you to complete ".get_challenge($challenge)['name'], "", $challenged);

                    return "";

                }
                return "Failed to create new battle.";

            }
            return "That person already completed that challenge.";

        }
        return "You must not already have completed that challenge.";

    }
    return "You need to supply the UUID of the person you want to challenge!";

}

function complete_battle($uuid){
    # Determines who completed the battle, if the challenger did it sends the challenged an alert, if the challenged
    # did it sends the challenger an alert and removes the battle.
    # Returns string;

    GLOBAL $connection;

    $challenge = is_posted('challenge');

    if($challenge){

        $battle_state = get_battle_state($uuid, $challenge);
        if($battle_state){

            if($battle_state[0] == "You're the Challenger"){

                new_notification(json_decode(get_user($uuid))[2]." has completed ".get_challenge($challenge)['name']."!", "Now its all up to you, complete the challenge!", "/images/js.png", $battle_state[1]);
                return "Battle Complete.";

            }
            else{

                new_notification(json_decode(get_user($uuid))[2]." has completed the challenge!", "Challenge over.", "/images/js.png", $battle_state[1]);

                $remove = $connection -> prepare("DELETE FROM `user_battles` WHERE `challenged` = ? AND `challenge` = ?");
                $remove -> bind_param("ss", $uuid, $challenge);
                if($remove -> execute()){

                    return "Battle Complete.";

                }
                return "Error completing battle";

            }

        }
        return "That battle doesnt exist.";

    }
    return "You must supply the challenge your referring to.";

}

function update_challenge($ucgid, $ucbid){
    # Updates a given challenge and given code block.
    # Returns the result.

    GLOBAL $connection;

    # The is_posted function checks if a variable has been posted and returns a "safe" version of whatever that string was.
    $d = is_posted(Array("lang", "name", "desc", "code", "function_name", "difficulty"));

    if($d){

        $lang = $d["lang"];
        $name = $d["name"];
        $desc = $d["desc"];
        $code = $d["code"];
        $tests = json_encode($_POST["tests"]);
        $function_name = $d["function_name"];
        $hidden_tests = json_encode($_POST["hidden_tests"]);
        $difficulty = $d["difficulty"];

        $cb = $connection -> prepare("UPDATE `code_block` SET `function_name`=?,`code`=?,`tests`=?,`hidden_tests`=? WHERE `ucbid` = ?");
        $cb -> bind_param("sssss",$function_name,$code,$tests,$hidden_tests,$ucbid);

        if($cb -> execute()) {
            $cb -> close();

            if ($challenge = $connection->prepare("UPDATE `challenges` SET `language`=?, `name`=?, `description`=?, `difficulty`=? WHERE `ucgid`=?")) {

                $challenge->bind_param("sssss", $lang, strip_tags($name), $desc, $difficulty, $ucgid);

                if ($challenge->execute()) {

                    $challenge -> close();
                    return "Updated.";

                }
                $challenge -> close();
                return "Failed to update challenge - 3";

            }
            return "Failed to update challenge - 2";

        }
        $cb -> close();
        return "Failed to update challenge - 1";

    }
    return "Insufficient Data.";

}

function publish_challenge($ucgid){
    # Publishes a particular challenge.
    # Returns string.

    if(check_exists("challenges","ucgid",$ucgid)){

        if(check_perms(10)){
            if(update("challenges","published","1","ucgid",$ucgid)){

                return "Published.";

            }
            return "Error updating.";

        }
        return "No Permission.";

    }
    return "Unknown challenge '$ucgid'.";

}

function challenges(){
    # Main function of the file, acts as the courses endpoint parser.
    # Returns the result of whatever function was called.

    $uuid = get_uuid();
    $method = is_posted('method');

    if($uuid && $method){

        switch($method){

            case "list":
                return list_challenges($uuid);
                break;

            case "battle":
                return new_battle($uuid);
                break;

            case "get_challenge":

                $ucgid = is_posted("ucgid");
                if($ucgid){
                    return json_encode(get_challenge($ucgid));
                }
                return "You must supply the ucgid.";

                break;

            case "complete_battle":
                return complete_battle($uuid);
                break;

            case "update":
                $ucgid = is_posted("ucgid");
                if($ucgid){
                    $ucbid = get_challenge($ucgid)["ucbid"];
                    return update_challenge($ucgid,$ucbid);
                }
                return "You must supply the ucgid.";
                break;

            case "publish":
                $ucgid = is_posted("ucgid");
                if($ucgid){
                    return publish_challenge($ucgid);
                }
                return "You must supply the ucgid.";
                break;

            default:
                return "That method is not defined.";
                break;

        }

    }


}
echo challenges();