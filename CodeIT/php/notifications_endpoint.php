<?php

require("../includes/includes.php");

# This file is the notifications endpoint for the frontend.

function get_notifications($uuid){
    # Function gets all of a user's notifications.
    # Returns JSON parsed array.

    $notifications = Array();

    $notifications_db = select_uid("user_notifications", "uuid", $uuid);
    $notifications_db -> bind_result($title, $description, $image, $viewed, $uuid, $id);
    while($notifications_db -> fetch()){

        array_push($notifications,Array($title, $description, $image, $viewed));

    }
    $notifications_db -> close();
    return json_encode($notifications);

}

function remove_notification($uuid){
    # Removes a user's notification that has a specific description.
    # Returns a string.

    GLOBAL $connection;

    $description = is_posted("description");
    if($description){

        $remove = $connection -> prepare("DELETE FROM `user_notifications` WHERE `description` = ? AND `uuid` = ?");
        $remove -> bind_param("ss", $description, $uuid);
        if($remove -> execute()){

            $remove -> close();
            return "Removed Notification.";

        }
        $remove -> close();
        return "Error Removing Notification.";
    }
    return "You must supply the description of the notification you wish to delete.";

}

function notifications(){
    # Main function for the file. Parses the posted data and acts as the backend endpoint.

    $uuid = get_uuid();
    $method = is_posted("method");

    if($uuid){

        if($method){

            switch($method){

                case "get":
                    return get_notifications($uuid);
                    break;

                case "remove":
                    return remove_notification($uuid);
                    break;

            }

        }
        return "You must supply a Method";

    }
    return "You must supply a UUID.";

}
echo notifications();