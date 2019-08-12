<?php

require("../includes/includes.php");
require("global_achievements.php");
require("notifications_backend.php");

# This file contains the functions that deal with the community page.

function is_friend($uuid, $friend_uuid){
    # This function checks if a user is another user's friend.
    # Returns weather they are or not.

    GLOBAL $connection;

    $friend_db = $connection -> prepare("SELECT COUNT(*) FROM `user_friends` WHERE `uuid` = ? AND `friend_uuids` LIKE ?");
    $friend_like = "%$friend_uuid%";
    $friend_db -> bind_param("ss", $uuid, $friend_like);
    if($friend_db -> execute()){

        $friend_db -> bind_result($count);
        $friend_db -> fetch();
        $friend_db -> close();
        if($count == 1){

            return true;

        }
        else{

            # If the user is not a friend of the requested user, check if they sent the requested user a friend request.
            if(check_exists("user_friend_requests", Array("from_uuid", "to_uuid"), Array($uuid, $friend_uuid))){

                return "pending";

            }
            else{

                # If the user is not a friend of the requested user and they did not send a request, check if they got a request.
                if(check_exists("user_friend_requests", Array("from_uuid", "to_uuid"), Array($friend_uuid, $uuid))){

                    return "requested";

                }

            }

        }
        return false;

    }
    $friend_db -> close();
    return false;

}

function compare_achievements($a, $b){
    # Sorting function used to order users.

    $a_ach = $a["num_achievements"];
    $b_ach = $b["num_achievements"];
    if($a_ach == $b_ach){

        return 0;

    }
    return ($a_ach > $b_ach) ? -1 : 1;

}

function list_users($uuid){
    # This function lists everyone in the community in order of how many achievements they have accomplished.

    GLOBAL $connection;

    if($uuid){

        $users_array = Array();

        $users_db = select_all("users");
        $users_db -> bind_result($first_name, $last_name, $screen_name, $email, $pw, $slt, $role, $uid, $id);
        $users_db -> store_result();

        while($users_db -> fetch()){

            $is_friend = false;
            $is_self = false;
            $is_challenged = false;
            if($uuid){

                $is_friend = is_friend($uuid, $uid);
                if($uuid == $uid){
                    $is_self = true;
                }
                if(check_exists('user_battles',Array('challenger','challenged'),Array($uuid,$uid))){

                    $battle = $connection -> prepare("SELECT * FROM `user_battles` WHERE `challenger` = ? AND `challenged` = ?");
                    $battle -> bind_param("ss", $uuid, $uid);
                    $battle -> execute();
                    $battle -> store_result();
                    $battle -> bind_result($challenger, $challenged, $challenge, $id);
                    $battle -> fetch();
                    $battle -> close();

                    $is_challenged = Array('Challenge Sent.', "window.location.href='../code/index.php?type=challenge&challenge=$challenge&battle=true'");

                }
                elseif(check_exists('user_battles',Array('challenged','challenger'),Array($uid, $uuid))){

                    $battle = $connection -> prepare("SELECT * FROM `user_battles` WHERE `challenger` = ? AND `challenged` = ?");
                    $battle -> bind_param("ss", $uid, $uuid);
                    $battle -> execute();
                    $battle -> store_result();
                    $battle -> bind_result($challenger, $challenged, $challenge, $id);
                    $battle -> fetch();
                    $battle -> close();

                    $is_challenged = Array('Sent You A Challenge.', "window.location.href='../code/index.php?type=challenge&challenge=$challenge&battle=true'");
                }

            }

            $num_achievements = get_num_completed($uid);

            $user_array = Array("sn" => $screen_name, "num_achievements" => $num_achievements,"is_friend" => $is_friend, "is_self" => $is_self, "is_challenged" => $is_challenged, "uuid" => $uid);
            array_push($users_array, $user_array);

        }

        $users_db -> close();

        usort($users_array, 'compare_achievements');

        return json_encode($users_array);

    }

    return json_encode(Array());

}

function add_friend($from_uuid){
    # Sends a friend request to a user.
    # Returns "" if successful.

    GLOBAL $connection;

    $to_uuid = is_posted("to");
    if($from_uuid && $to_uuid){

        # Step 1: Add a request if one is not already created.
        if(!check_exists("user_friend_requests",Array("from_uuid","to_uuid"),Array($from_uuid, $to_uuid))){

            $friend_request = $connection -> prepare("INSERT INTO `user_friend_requests`(`from_uuid`, `to_uuid`) VALUES (?, ?)");
            $friend_request -> bind_param("ss", $from_uuid, $to_uuid);
            if($friend_request -> execute()){

                # Step 2: Send notification.
                $friend_request -> close();
                $from_user = json_decode(get_user($from_uuid));
                new_notification("You Got A Friend Request!","$from_user[2] would like to be your CodeIT friend.","/images/default-user.png",$to_uuid);
                return "";

            }
            $friend_request -> close();
            return "Error Sending Friend Request";

        }
        return "You have already sent this person a friend request";

    }
    return "No UUID sent for friend to add";

}

function accept_request($uuid){
    # Accepts a user's friend request and adds both users as each others friends.
    # Returns "" if successful

    GLOBAL $connection;

    $from = is_posted("from");
    if($from){

        # Step 1: make sure there was a request.
        if(check_exists("user_friend_requests",Array("from_uuid", "to_uuid"),Array($from, $uuid))){

            # Step 2: check if you have an entry in the friends table.
            if(!check_exists("user_friends","uuid",$uuid)){

                $new_friends_list = $connection -> prepare("INSERT INTO `user_friends`(`uuid`, `friend_uuids`) VALUES (?, '')");
                $new_friends_list -> bind_param("s", $uuid);
                $new_friends_list -> execute();
                $new_friends_list -> close();

            }
            # Step 2a: check if the sender has an entry in the friends table.
            if(!check_exists("user_friends","uuid",$from)){

                $new_friends_list = $connection -> prepare("INSERT INTO `user_friends`(`uuid`, `friend_uuids`) VALUES (?, '')");
                $new_friends_list -> bind_param("s", $from);
                $new_friends_list -> execute();
                $new_friends_list -> close();

            }

            # Step 3: add the sender to the your friends list.
            $your_friends_db = $connection -> prepare("UPDATE `user_friends` SET friend_uuids = CONCAT(friend_uuids,?) WHERE `uuid` = ?");
            $sender = "|$from";
            $your_friends_db -> bind_param("ss", $sender, $uuid);
            if($your_friends_db -> execute()){

                # Step 4: add you to the sender's friends list.
                $your_friends_db -> close();
                $sender_friends_db = $connection -> prepare("UPDATE `user_friends` SET friend_uuids = CONCAT(friend_uuids,?) WHERE `uuid` = ?");
                $your = "|$uuid";
                $sender_friends_db -> bind_param("ss", $your, $from);
                if($sender_friends_db -> execute()){

                    # Step 5: remove the request.
                    $sender_friends_db -> close();
                    $remove_request = $connection -> prepare("DELETE FROM `user_friend_requests` WHERE `from_uuid` = ? AND `to_uuid` = ?");
                    $remove_request -> bind_param("ss", $from, $uuid);
                    if($remove_request -> execute()){

                        $remove_request -> close();
                        $you = json_decode(get_user($uuid));
                        new_notification("$you[2] Accepted Your Friend Request!","Challenge them on the connect page!","/images/default-user.png",$from);
                        return "";

                    }
                    $remove_request -> close();
                    return "Error removing the friend request.";

                }
                $sender_friends_db -> close();
                return "Error updating THEIR friends list.";

            }
            $your_friends_db -> close();
            return "Error updating YOUR friends list.";

        }
        return "No such friend request exists.";

    }
    return "You must supply the UUID of the person of whom's request you are accepting.";

}

function get_friends($uuid){
    # This function gets all of the friends a user has.
    # Returns JSON Array.

    if($uuid){

        $friends = Array();

        if(check_exists("user_friends","uuid",$uuid)){
            $friends_db = select_uid("user_friends","uuid",$uuid);
            $friends_db -> bind_result($uid, $friend_uuids, $id);
            $friends_db -> execute();
            $friends_db -> fetch();

            $friend_uuids = explode("|",$friend_uuids);
            foreach ($friend_uuids as $friend){

                if($friend != '' && check_exists("users","uuid",$friend)){

                    array_push($friends, $friend);

                }
            }

        }

        return json_encode($friends);

    }
    return "You need to supply a UUID to lookup.";

}

function connect(){
    # Main function of the file, acts as the courses endpoint parser.
    # Returns none, echos the result of whatever function was called.

    $uuid = get_uuid();
    $method = is_posted("method");

    if($method){

        switch($method){

            case "list":
                return list_users($uuid);
                break;

            case "add_friend":
                return add_friend($uuid);
                break;

            case "accept_request":
                return accept_request($uuid);
                break;

            case "get_friends":
                return get_friends($uuid);
                break;

            default:
                return "Method '$method' is not recognised.'";
                break;

        }

    }
    return "You must supply a method";

}
echo connect();