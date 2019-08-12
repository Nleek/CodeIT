<?php

# This file contains functions relating to a user's persistent notifications.

function new_notification($title, $description, $image, $uuid){
    # Creates a new notification for a particular user.
    # Returns "" if successful

    GLOBAL $connection;

    if(!check_exists('user_notifications',Array('title','description','image','uuid'),Array($title,$description,$image,$uuid))) {
        $notification = $connection->prepare("INSERT INTO `user_notifications`(`title`, `description`, `image`, `viewed`, `uuid`) VALUES (?, ?, ?, '0', ?)");
        $notification->bind_param("ssss", $title, $description, $image, $uuid);
        if ($notification->execute()) {

            return "";

        }
        return "Error adding notification.";
    }
    return "User Already Has This Notification.";
}