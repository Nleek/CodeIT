<?php

/*
 *
 * This file handles the creation of new entities throughout the site.
 * It makes sure all of the trackers are in place for whatever had been created.
 *
 * */

require("../includes/includes.php");


function new_user(){
    # Generates a new user and creates all of their required trackers.
    # Returns - None.

    GLOBAL $connection;

    # The is_posted function checks if a variable has been posted and returns a "safe" version of whatever that string was.
    $d = is_posted(Array("fn","ln","sn","em","pw"));
    if($d){

        $fn = $d["fn"];
        $ln = $d["ln"];
        $sn = $d["sn"];
        $em = $d["em"];
        $pw = $d["pw"];
        $salt = substr(str_replace('+','.',base64_encode(md5(mt_rand().time(), true))),0,16); // generate a 16-character salt string

        $pw = pw_encrypt($pw, $salt);

        $uuid = gen_uid($sn);

        if(!check_exists("users", "screen_name", $sn)){

            if(!check_exists("users", "email", $em)){

                if($user = $connection -> prepare("INSERT INTO `users`(`first_name`, `last_name`, `screen_name`, `email`, `password`, `salt`, `role`, `uuid`) VALUES (?,?,?,?,?,?,'0',?)")){

                    $user -> bind_param("sssssss", $fn, $ln, $sn, $em, $pw, $salt, $uuid);

                    if($user -> execute()){

                        if(bind_trackers()){

                            return $uuid;

                        }
                        return "Failed to create user trackers";

                    }
                    return "Failed to create new user - 2";

                }
                return "Failed to create new user - 1";

            }
            return "Sorry, that email is already registered.";

        }
        return "Oops, looks like that Screen Name is already taken.";

    }
    return "Insufficient Data.";

}

function new_code_block($function_name, $code, $hidden_tests, $tests){
    # Generates a new code block.
    # Returns - The code block id or false if it failed to be created.

    GLOBAL $connection;

    if($code && $hidden_tests && $tests){

        $ucbid = gen_uid($code);

        if($code_block = $connection -> prepare("INSERT INTO `code_block`(`ucbid`, `function_name`, `code`, `tests`, `hidden_tests`) VALUES (?,?,?,?,?)")){

            $code_block -> bind_param("sssss", $ucbid, $function_name, $code,  $tests, $hidden_tests);

            if($code_block -> execute()){

                return $ucbid;

            }

        }

    }
    return false;

}

function new_challenge(){
    # Generates a new challenge and creates new trackers for each user.
    # Returns the ucgid of the challenge created.

    GLOBAL $connection;

    # The is_posted function checks if a variable has been posted and returns a "safe" version of whatever that string was.
    $d = is_posted(Array("lang", "name", "desc", "code", "function_name", "difficulty"));

    if($d){

        $lang = $d["lang"];
        $name = $d["name"];
        $desc = $d["desc"];
        $code = $d["code"];
        $tests = $_POST["tests"];
        $function_name = $d["function_name"];
        $hidden_tests = $_POST["hidden_tests"];
        $author = get_uuid();
        $difficulty = $d["difficulty"];

        $ucid = gen_uid($name);

        $code_block = new_code_block($function_name, str_replace("\n","",$code), json_encode($hidden_tests), json_encode($tests));

        if($code_block) {

            if ($challenge = $connection->prepare("INSERT INTO `challenges`(`language`, `name`, `description`, `code_block`, `author`, `published`, `difficulty`, `ucgid`) VALUES (?,?,?,?,?,'0',?,?)")) {

                $challenge->bind_param("sssssss", $lang, strip_tags($name), $desc, $code_block, $author, $difficulty, $ucid);

                if ($challenge->execute()) {

                    $challenge -> close();

                    if(bind_trackers()){

                        return $ucid;

                    }
                    return "Failed to create new challenge - 4";

                }
                return "Failed to create new challenge - 3";

            }
            return "Failed to create new challenge - 2";

        }
        return "Failed to create new challenge - 1";
        
    }
    return "Insufficient Data.";

}

function new_achievement(){
    # This function generates a new achievement from posted variables.
    # Returns: String.

    GLOBAL $connection;

    $d = is_posted(Array("name","description","badge","required","num_required","tags"));

    if($d){

        $name = $d["name"];
        $desc = $d["description"];
        $badge = $d["badge"];
        $required = $d["required"];
        $num_required = $d["num_required"];
        $tags = $d["tags"];

        $uaid = gen_uid($name);

        if($achievement = $connection->prepare("INSERT INTO `achievements`(`name`, `description`, `badge`, `required`, `num_required`, `tags`, `uaid`) VALUES (?, ?, ?, ?, ?, ?, ?)")){

            $achievement -> bind_param("sssssss", $name, $desc, $badge, $required, $num_required, $tags, $uaid);

            if($achievement -> execute()){

                $achievement -> close();

                if(bind_trackers()){

                    return $uaid;

                }
                return "Failed to create new achievement - 3";

            }
            return "Failed to create new achievement - 2";

        }
        return "Failed to create new achievement - 1";

    }
    return "Insufficient Data.";

}

function new_section(){
    # This function makes a new blank code block and a new blank section. It also binds trackers.
    # Returns the USID of the new section.

    GLOBAL $connection;

    $usid = gen_uid("section");

    $ucbid = new_code_block('main','function main(){}','[]','[]');

    if($section = $connection -> prepare("INSERT INTO `sections`(`name`, `description`, `code_block`, `difficulty`, `usid`) VALUES ('New Section', 'Section Description', ?, 'Easy', ?)")){

        $section -> bind_param("ss", $ucbid, $usid);
        if($section -> execute()){

            $section -> close();

            if(bind_trackers()){

                return $usid;

            }
            return "Failed to create new section - 2.";

        }
        return "Failed to create new section - 1.";

    }
    return "Failed to create new section.";

}

function new_lesson(){
    # This function makes a new blank lesson and calls new_section. Also binds trackers.
    # Returns the ULID of the new section.

    GLOBAL $connection;

    $ulid = gen_uid("lesson");
    $usid = new_section();

    if($lesson = $connection -> prepare("INSERT INTO `lessons`(`name`, `num_sections`, `sections`, `ulid`) VALUES ('New Lesson', '1', ? , ?)")){

        $lesson -> bind_param("ss", $usid, $ulid);
        if($lesson -> execute()){

            $lesson -> close();
            if(bind_trackers()){

                return $ulid;

            }
            return "Failed to create new lesson - 2.";

        }
        $lesson -> close();
        return "Failed to create new lesson - 1.";

    }
    return "Failed to create new lesson.";

}

function new_course(){
    # This function makes a new blank course and calls new_lesson. Also binds trackers.
    # Returns the UCID of the new course.

    GLOBAL $connection;

    $ucid = gen_uid("course");
    $ulid = new_lesson();

    if($course = $connection -> prepare("INSERT INTO `courses`(`language`, `name`, `description`, `image`, `published`, `num_lessons`, `lessons`, `ucid`) VALUES ('JavaScript', 'New JS Course', 'Course Description', '../images/js.png', '0', '1', ?, ?)")){

        $course -> bind_param("ss", $ulid, $ucid);
        if($course -> execute()){

            $course -> close();
            if(bind_trackers()){

                return $ucid;

            }
            return "Failed to create new course - 2.";

        }
        $course -> close();
        return "Failed to create new course - 1.";

    }
    return "Failed to create new course.";

}

function new_main(){
    # Main function of the file, acts as the courses endpoint parser.
    # Returns none, echos the result of whatever function was called.

    $action = is_posted("action");
    if($action){

        switch($action){

            case "user":
                echo new_user();
                break;

            case "challenge":
                if(check_perms(1)){
                    echo new_challenge();
                }
                else{
                    if(check_perms(0)){
                        echo "You must complete the first lesson to progress";
                    }
                    else{
                        echo "You must be logged in to make a new challenge.";
                    }
                }
                break;

            case "course":
                if(check_perms(10)){
                    echo new_course();
                }
                else{
                    echo "You must be an Admin to create a new course.";
                }
                break;

            case "lesson":
                if(check_perms(10)){
                    echo new_lesson();
                }
                else{
                    echo "You must be an Admin to create a new course.";
                }
                break;

            case "section":
                if(check_perms(10)){
                    echo new_section();
                }
                else{
                    echo "You must be an Admin to create a new section.";
                }
                break;

            case "achievement":
                if(check_perms(10)){
                    echo new_achievement();
                }
                else{
                    echo "You must be an Admin to create a new Achievement.";
                }
                break;

            case "manual-trackers":
                if(check_perms(10)){
                    echo bind_trackers();
                }
                else{
                    echo "You must be an Admin to bind new trackers.";
                }
                break;

            default:
                echo "That action is not defined.";
                break;
        }

    }
    else{
        echo "An action must be specified.";
    }


}

new_main();
