<?php
/*
File: includes.php
Desc: Fetches the bare-bones required php files for use on every page of the site.
*/

session_start();
ob_start();
$base_url = "http://www.massivedev.us/projects/bpa";

require("database.php");
require("database-functions.php");

function clean_var($var){
    # Cleans a variable from running potentially malicious code.
    # Returns - The variable sent, just cleaned.

    GLOBAL $con;

    return mysqli_real_escape_string($con, stripslashes($var));

}

function pw_encrypt($pw, $salt){
    # Encrypts a given password with 1000 iterations of SHA512 with a given salt.

    return crypt(clean_var($pw), '$6$rounds=10000$'.$salt);

}

function is_posted($variable){
    # Checks if a variable, or array of variables, are set.
    # Returns - True if all supplied variables are set, false otherwise.

    if (is_array($variable) or ($variable instanceof Traversable)){

        $exists = Array();

        foreach ($variable as $var){

            if(!isset($_POST[(string)$var]) || $_POST[(string)$var] == ""){
                return false;
            }
            else{

                $exists[$var] = clean_var($_POST[$var]);

            }

        }

        return $exists;

    }

    elseif (isset($_POST[$variable])){

        return $_POST[$variable];

    }

    return false;

}

function check_perms($required){
    # Checks if a logged-in user's role is equal to or higher than the sent requirement.
    # Returns - True / false

    if(isset($_SESSION["role"]) && $_SESSION["role"] >= $required){

        return true;

    }
    return false;

}

function gen_uid($sent_string){
    # Generates a Unique ID from a string and the current Epoch time.
    # Returns - String

    $time = time();
    return md5((string)$time.$sent_string);

}

function get_uuid(){
    // Gets the user's unique ID as either a Post argument or stored in the user's session.
    // Returns the UUID or false.

    $uuid = is_posted("uuid");
    if($uuid){

        return $uuid;

    }
    elseif(isset($_SESSION["uuid"])){

        $uuid = $_SESSION["uuid"];
        return $uuid;

    }

    return false;

}