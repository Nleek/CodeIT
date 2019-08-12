<?php

require("../includes/includes.php");
require("global_achievements.php");

# This file contains the functions that deal with achievements.

function mark_complete($uuid, $uaid){
    // Marks a particular achievement as complete for a given user.
    // Returns "Completed!" if it succeeded, false otherwise.

    GLOBAL $connection;

    $update = $connection -> prepare("UPDATE `user_achievement_progress` SET `complete` = '1' WHERE `uuid` = ? AND `uaid` = ?");
    $update -> bind_param("ss", $uuid, $uaid);
    if($update -> execute()){

        $update -> close();
        return "Completed $uaid";

    }
    $update -> close();
    return false;

}

function increment_tag_progress($uuid, $tag){
    // Increments a user's progress in a certain achievement tag.
    // Return - null

    GLOBAL $connection;

    // Select the user's tracker for the particular tag
    $update = $connection -> prepare("UPDATE `user_achievement_tag_progress` SET progress = progress + 1 WHERE `uuid` = ? AND `name` = ?");
    $update -> bind_param("ss", $uuid, $tag);
    if(! ($update -> execute())){

        $update -> close();
        echo "An Error has occurred.";

    }
    $update -> close();

    return null;

}

function increment_progress($uuid, $uaid){
    # Increments a user's progress in a particular achievement by one.
    # If the achievement has been fulfilled already, return false.
    # Return - The ratio ( has / needs ) of the achivements Num_required and U_A progress or false

    GLOBAL $connection;

    $achievement = select_uid("achievements", "uaid", $uaid);
    $achievement -> bind_result($name, $desc, $badge, $required, $num_required, $tags, $uaid, $id);
    $achievement -> fetch();
    $achievement -> close();

    $progress = get_achievement_progress($uuid, $uaid);

    $completed = $progress[1];
    $progress = $progress[0];

    $tags = explode("|", $tags);

    foreach ($tags as $tag){

        increment_tag_progress($uuid, $tag);

    }

    // If the achievement is less than the requirement for completion and not already marked complete.
    if($num_required > $progress && !$completed){

        $new_progress = $progress + 1;

        $update = $connection -> prepare("UPDATE `user_achievement_progress` SET `progress` = '$new_progress' WHERE `uuid` = ? AND `uaid` = ?");
        $update -> bind_param("ss", $uuid, $uaid);
        if($update -> execute()){

            $update -> close();

            if($new_progress == $num_required){

                $has_completed = mark_complete($uuid, $uaid);

                if($has_completed == "Completed eae6338e059ced2131850906b420113e"){
                    // User has completed the first lesson achievement.

                    $user = select_uid("users","uuid",$uuid);
                    $user -> bind_result($fn, $ln, $sn, $em, $pw, $st, $role, $uuid, $id);
                    $user -> fetch();
                    $user -> close();
                    if($role == 0){
                        $increment_role = $connection -> prepare("UPDATE `users` SET role = role + 1 WHERE `uuid` = ?");
                        $increment_role -> bind_param("s", $uuid);
                        $increment_role -> execute();
                        $increment_role -> close();
                    }

                }
                
                return $has_completed;

            }

            return " $new_progress / $num_required ";

        }

    }
    // If the achievement is equal to or more than the requirement and not marked complete.
    elseif(!$completed){

        return mark_complete($uuid, $uaid);

    }

    return false;

}

function get_achievements_by_tag($tag){
    // Looks up all of the achievements with a given tag.
    // Returns an array of Unique Achievement ID's

    GLOBAL $connection;

    $array = Array();

    $tag = "%".$tag."%";

    $achievements = $connection -> prepare("SELECT * FROM `achievements` WHERE `tags` LIKE ?");
    $achievements -> bind_param("s", $tag);
    $achievements -> execute();
    $achievements -> store_result();
    $achievements -> bind_result($name, $desc, $badge, $required, $num_required, $tags, $uaid, $id);
    while($achievements -> fetch()){

        array_push($array, $uaid);

    }

    $achievements -> close();

    echo json_encode($array);
    return $array;

}

function get_achievement($uaid){
    // Looks up a particular achievement.
    // Returns a JSON parsed array of it's properties

    $achievement = select_uid("achievements", "uaid", $uaid);
    $achievement -> bind_result($name, $desc, $badge, $required, $num_required, $tags, $uaid, $aid);
    $achievement -> fetch();
    $achievement -> close();

    return json_encode(Array($name, $desc, $badge, $required, $num_required, $tags, $uaid));

}

function ach_tests(){
    // Tests the functions relating to user achievements.

    echo "get_all_achievement_progress: ".get_all_achievement_progress("7795d2b412b2aaf41111cbfbcc2dd408")."<br />";

    echo "Increment_Progress: ".increment_progress("7795d2b412b2aaf41111cbfbcc2dd408", "eae6338e059ced2131850906b420113e")."<br />";

}

function achievements(){
    # Main function of the file, acts as the courses endpoint parser.
    # Returns none, echos the result of whatever function was called.

    $method = is_posted("method");
    if($method){

        switch($method){

            case "list_all":
                $uuid = get_uuid();
                if($uuid){

                    echo get_all_achievement_progress($uuid);

                }
                else{

                    echo "[]";

                }
                break;

            case "get":
                // Achievement get. This is how you increment the progress of a user's achievements
                // Parameters needed: uuid (or session), tag
                $uuid = get_uuid();
                $tag = is_posted("tag");
                if($tag){

                    if($uuid){

                        $achievements = get_achievements_by_tag($tag);
                        foreach ($achievements as $uaid){

                            echo increment_progress($uuid, $uaid);

                        }

                    }
                    else{

                        echo "You must be logged in to gain achievement levels.";

                    }

                }
                else{

                    echo "You must supply a 'tag' argument in the post request";

                }
                break;

            case "num_completed":

                    $uuid = get_uuid();
                    if($uuid){
                        echo get_num_completed($uuid);
                    }
                    else{
                        echo "";
                    }

                break;

            case "lookup":

                $uaid = is_posted("uaid");
                if($uaid){
                    echo get_achievement($uaid);
                }
                else{
                    echo "You must supply the UAID of the achievement.";
                }

                break;

            default:
                echo "Method '$method' is not known.";
                break;

        }

        return true;

    }
    echo "You must supply a method.";
}

#ach_tests();
achievements();