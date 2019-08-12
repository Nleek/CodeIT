<?php

require("../includes/includes.php");

# This file contains the functions that deal with courses.

function get_sections($uuid, $usids){
    # Gets all sections in the usids array.
    # Returns an Array[ Array[section], complete ]

    GLOBAL $connection;

    $sections_array = Array();

    foreach($usids as $usid){

        $section_db = select_uid("sections", "usid", $usid);
        $section_db -> bind_result($name, $desc, $code_block, $difficulty, $usid, $id);
        $section_db -> fetch();
        $section_db -> close();


        $section_complete_db = $connection -> prepare("SELECT * FROM `user_section_progress` WHERE `uuid` = ? AND `usid` = ?");
        $section_complete_db -> bind_param("ss", $uuid, $usid);
        $section_complete_db -> execute();
        $section_complete_db -> bind_result($uuid, $usid, $complete, $id);
        $section_complete_db -> store_result();
        $section_complete_db -> fetch();
        $section_complete_db -> close();

        $section = Array($name, $difficulty, "../code/index.php?type=course&section=$usid", $usid);

        array_push($sections_array, Array($section, $complete));

    }

    return $sections_array;

}
function all_sections_complete($sections){
    # Checks how many sections have been completed.
    # Returns Int

    $complete = 0;
    foreach($sections as $section){

        if($section[1] == 1){
            $complete++;
        }

    }
    return $complete;

}
function get_lessons($uuid, $ulids){
    # Gets all lessons in the ulids array.
    # Returns an Array[ lesson, [get_sections], completed ]

    $lessons_array = Array();

    foreach($ulids as $ulid){

        $lesson_db = select_uid("lessons", "ulid", $ulid);
        $lesson_db -> bind_result($name, $num_sections, $lesson_sections, $ulid, $id);
        $lesson_db -> fetch();
        $lesson_db -> close();

        $sections = get_sections($uuid, explode("|", $lesson_sections));
        $complete = Array(all_sections_complete($sections), $num_sections);

        $lesson = Array($name, $ulid);

        array_push($lessons_array, Array($lesson, $sections, $complete));

    }

    return $lessons_array;

}
function all_lessons_complete($lessons){
    # Checks how many lessons have been completed.
    # Returns Int.

    $complete = 0;
    foreach($lessons as $lesson){

        if($lesson[2][0] == $lesson[2][1]){
            $complete++;
        }

    }
    return $complete;

}
function get_courses($uuid){
    # Gets all available sections.
    # Returns the JSON Encoded Array[ Array[course], [get_lessons], completed ]

    $courses_array = Array();

    $courses = select_all("courses");
    $courses -> bind_result($lang, $name, $desc, $image, $published, $num_lessons, $lessons, $ucid, $id);
    while($courses -> fetch()){

        $continue = false;

        if(check_perms(10)){
            $continue = true;
        }
        elseif($published != 0){
            $continue = true;
        }

        if($continue){

            $course = Array($lang, $image, $name, $desc, $ucid);

            $lessons = get_lessons($uuid, explode("|", $lessons));

            $complete = Array(all_lessons_complete($lessons), $num_lessons);

            array_push($courses_array, Array($course, $lessons, $complete, $published));

        }

    }
    $courses -> close();

    return json_encode($courses_array);

}

function publish($ucid){
    # Publishes a particular course.
    # Returns string.

    GLOBAL $connection;

    if(check_exists("courses","ucid",$ucid)){

        $update = $connection -> prepare("UPDATE `courses` SET `published`= 1 WHERE `ucid` = ?");
        $update -> bind_param("s", $ucid);
        if($update -> execute()){
            $update -> close();
            return "published $ucid";

        }
        $update -> close();
        return "Error updating.";

    }
    return "Unknown course '$ucid'.";

}

function add_section_to_lesson($usid, $ulid){
    # This function adds a particular section, given it exists, to a particular lesson.
    # Returns String.

    GLOBAL $connection;

    if(check_exists("sections","usid",$usid) && check_exists("lessons","ulid",$ulid)){

        $usid = "|$usid";

        $update = $connection -> prepare("UPDATE `lessons` SET sections = CONCAT(sections, ?), num_sections = num_sections + 1 WHERE `ulid` = ?");
        $update -> bind_param("ss", $usid, $ulid);
        if($update -> execute()){

            $update -> close();
            return "Updated.";

        }
        $update -> close();
        return "Error Updating.";

    }
    return "The supplied usid and ulid must exist.";

}

function add_lesson_to_course($ulid, $ucid){
    # This function adds a particular lesson, given it exists, to a particular course.
    # Returns String.

    GLOBAL $connection;

    if(check_exists("courses","ucid",$ucid) && check_exists("lessons","ulid",$ulid)){

        $ulid = "|$ulid";

        $update = $connection -> prepare("UPDATE `courses` SET lessons = CONCAT(lessons, ?), num_lessons = num_lessons + 1 WHERE `ucid` = ?");
        $update -> bind_param("ss", $ulid, $ucid);
        if($update -> execute()){

            $update -> close();
            return "Updated.";

        }
        $update -> close();
        return "Error Updating.";

    }
    return "The supplied usid and ulid must exist.";
}

function save($property, $value, $uid){
    # This function handles the saving of particular course attributes like names, descriptions and so fourth.
    # Returns a string of the result.

    switch($property){

        case "course_title":

            if(update("courses","name",$value,"ucid",$uid)){
                return "Updated.";
            }
            return false;

            break;

        case "course_description":

            if(update("courses","description",$value,"ucid",$uid)){
                return "Updated.";
            }
            return false;

            break;

        case "lesson_title":

            if(update("lessons","name",$value,"ulid",$uid)){
                return "Updated.";
            }
            return false;

            break;

        case "section_title":

            if(update("sections","name",$value,"usid",$uid)){
                return "Updated.";
            }
            return false;

            break;

        case "section_description":

            if(update("sections","description",$value,"usid",$uid)){
                return "Updated.";
            }
            return false;

            break;

        case "section_difficulty":

            if(update("sections","difficulty",$value,"usid",$uid)){
                return "Updated.";
            }
            return false;

            break;

        case "section_code_block":

            $section = select_uid("sections","usid",$uid);
            $section -> bind_result($name, $desc, $cbid, $diff, $usid, $sid);
            $section -> fetch();
            $section -> close();
            if(update("code_block","code",$value,"ucbid",$cbid)) {

                $tests = is_posted("tests");
                $hidden_tests = is_posted("hiddenTests");
                if($tests && $hidden_tests){
                    if (update("code_block", "tests", json_encode($tests), "ucbid", $cbid)) {

                        if (update("code_block", "hidden_tests", json_encode($hidden_tests), "ucbid", $cbid)) {

                            return "Updated.";

                        }
                        return "Error updating hidden tests.";

                    }
                    return "Error updating tests.";

                }

            }
            return "Error updating code.";

            break;

        default:
            return "Property '$property' not known.'";
            break;

    }

}

function courses(){
    # Main function of the file, acts as the courses endpoint parser.
    # Returns none, echos the result of whatever function was called.

    $method = is_posted("method");
    if($method){
        $uuid = get_uuid();
        if($uuid){
            switch($method) {

                case "list":
                    echo get_courses($uuid);
                    break;

                case "publish":
                    if(check_perms(10)){

                        $ucid = is_posted("ucid");
                        if($ucid){

                            echo publish($ucid);

                        }
                        else{

                            echo "You must supply the UCID to publish.";

                        }

                    }
                    else{
                        echo "No permission.";
                    }
                    break;

                case "add_section_to_lesson":

                    if(check_perms(10)){

                        $usid = is_posted("usid");
                        $ulid = is_posted("ulid");

                        if($usid && $ulid){

                            echo add_section_to_lesson($usid, $ulid);

                        }
                        else{
                            echo "You must supply both a USID and a ULID.";
                        }

                    }
                    else{

                        echo "No permission";

                    }
                    break;

                case "add_lesson_to_course":

                    if(check_perms(10)){

                        $ulid = is_posted("ulid");
                        $ucid = is_posted("ucid");

                        if($ulid && $ucid){

                            echo add_lesson_to_course($ulid, $ucid);

                        }
                        else{
                            echo "You must supply both a ULID and a UCID.";
                        }

                    }
                    else{

                        echo "No permission";

                    }
                    break;

                case "save":

                    if(check_perms(10)){

                        $property = is_posted("property");
                        $value = is_posted("value");
                        $uid = is_posted("uid");

                        if($property && $value && $uid){

                            echo save($property, $value, $uid);

                        }
                        else{

                            echo "You must supply both a property and a value.";

                        }

                    }
                    else{

                        echo "No permission";

                    }

                    break;

                default:
                    echo "Method '$method' is not known.";
                    break;

            }
            return true;

        }
        echo "You must supply a UUID or be logged in to do that.";
        return false;

    }
    echo "You must supply a method.";

}

courses();