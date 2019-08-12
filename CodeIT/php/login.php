<?php
/*
File: Login.php
Desc: Match a username and password to users in the database. Print the result.
      Passwords are sent to the server encrypted with one round of SHA512. Their password is stored
      encrypted with 1001 rounds of SHA512, so to authenticate, we must run the other 1000 rounds.
*/

// -- Get all required files --
require("../includes/includes.php");

// -- Grab sent items --

if (isset($_POST["un"])){

    $un = $_POST["un"];

}
else{

    if(isset($_SESSION["em"])){

        $un = $_SESSION["em"];

    }

}

$pw = $_POST["pw"];

// -- Main login logic --

function register_user($username_field, $username){
    # Registers the user from the database in session variables.

    GLOBAL $connection;

    if($user = $connection->prepare("SELECT * FROM `users` WHERE `$username_field`=?")){
        $user->bind_param("s", $username);
        $user->execute();
        $user->store_result();
        $user->bind_result($first_name, $last_name, $screen_name, $email, $password, $salt, $role, $uuid, $id);
        $user->fetch();

        if($user -> num_rows == 1){ // Check if the user exists.

            // Log the user in.
            $_SESSION["em"] = $email;
            $_SESSION["fn"] = $first_name;
            $_SESSION["ln"] = $last_name;
            $_SESSION["sn"] = $screen_name;
            $_SESSION["role"] = $role;
            $_SESSION["salt"] = $salt;
            $_SESSION["uuid"] = $uuid;

            return "Logged In Successfully!";

        }
        else{

            return "An Error Occurred. Please contact support. Error: 2+U";

        }

        $user->close();

    }
    else{

        return "An Error Occurred, please try again.";

    }

}


function get_salt($username_field, $username){
    # Returns the salt of the user given or false.

    GLOBAL $connection;

    if($user = $connection -> prepare("SELECT `salt` FROM `users` WHERE `$username_field` = ?")){

        $user -> bind_param("s", $username);
        $user -> execute();
        $user -> store_result();
        $user -> bind_result($salt);
        $user -> fetch();

        return $salt;

    }
    return false;

}

if(isset($un) && isset($pw)){

    //Password Hashing. Passwords are sent encoded with one round of SHA512

    if(check_exists("users", "email", $un)){

        $salt = get_salt("email", $un);

        $pw = pw_encrypt($pw, $salt);

        if(check_exists("users", Array("email", "password"), Array($un, $pw))){


            echo register_user("email", $un);

        }
        else{

            echo"Incorrect Email or Password";

        }

    }
    elseif(check_exists("users", "screen_name", $un)){

        $salt = get_salt("screen_name", $un);

        $pw = pw_encrypt($pw, $salt);

        if(check_exists("users", Array("screen_name", "password"), Array($un, $pw))){

            echo register_user("screen_name", $un);

        }
        else{

            echo"Incorrect Screen Name or Password";

        }

    }
    else{

        echo"Looks like you aren't registered. Don't worry, it's easy and only takes a minute to do.";

    }

}
?>