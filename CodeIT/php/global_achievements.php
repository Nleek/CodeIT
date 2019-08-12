e<?php

# This file holds global achievement functions that are used in multiple backend files

function get_all_achievements(){
    # Grabs all of the achievements from the database.
    # Returns: Array.

    $achievements = Array();

    $achievements_db = select_all("achievements");
    $achievements_db -> bind_result($name, $desc, $badge, $required, $num_required, $tags, $uaid, $id);
    while($achievements_db -> fetch()){

        array_push($achievements, Array($name, $desc, $badge, $required, $num_required, $uaid));

    };
    $achievements_db -> close();

    return $achievements;

}

function get_achievement_progress($uuid, $uaid){
    # Grabs a particular user's progress in a particular achievement
    # Returns - Array

    GLOBAL $connection;

    $achievement_progress_db = $connection -> prepare("SELECT * FROM `user_achievement_progress` WHERE `uuid` = ? AND `uaid` = ?");
    $achievement_progress_db -> bind_param("ss", $uuid, $uaid);
    $achievement_progress_db -> execute();
    $achievement_progress_db -> store_result();
    $achievement_progress_db -> bind_result($uuid, $uaid, $progress, $complete, $id);
    $achievement_progress_db -> fetch();
    $achievement_progress_db -> close();

    return Array($progress, $complete, $uuid);

}

function get_all_achievement_progress($uuid){
    # Grabs the sent user's progress in all of the achievements.
    # Returns - JSON.

    $all_achievements = get_all_achievements();

    $achievement_index = 0;
    foreach($all_achievements as $achievement){

        $achievement_uid = $achievement[5];

        array_push($all_achievements[$achievement_index], get_achievement_progress($uuid, $achievement_uid));

        $achievement_index++;

    }

    return json_encode($all_achievements);


}

function get_num_completed($uuid){
    // Gets the number of achievements a user has completed.
    // Returns - Int.

    $all_achievements = json_decode(get_all_achievement_progress($uuid));

    $completed_achievements = 0;

    foreach ($all_achievements as $achievement){

        if($achievement[6][1]){
            $completed_achievements += 1;
        }

    }

    return $completed_achievements;

}