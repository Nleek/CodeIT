<?php

require("../includes/includes.php");

# This file contains the functions that deal with code pages.

function get_content($type, $uid){
    # Gets the description and the code block id of a particular code window.
    # Returns an Array[ description, ucbid ]

    if($type == "course"){

        $content = select_uid("sections", "usid", $uid);
        $content -> bind_result($name, $desc, $code_block, $difficulty, $usid, $id);
        $content -> fetch();
        $content -> close();

    }
    elseif($type == "challenge"){

        $content = select_uid("challenges", "ucgid", $uid);
        $content -> bind_result($lang, $name, $desc, $code_block, $auth, $pub, $difficulty, $ucgid, $id);
        $content -> fetch();
        $content -> close();

    }
    else{
        return Array(null, null);
    }

    return Array($desc, $code_block, $auth);

}

function get_code_block($uuid, $ucbid){
    # Gets a particular code-block for a user and checks if they have stored code for the block.
    # Returns Array[ saved-code, orig-code, tests, hidden_tests ]

    GLOBAL $connection;

    $code_db = select_uid("code_block","ucbid", $ucbid);
    $code_db -> bind_result($ucbid, $function_name, $orig_code, $tests, $hidden_tests, $id);
    $code_db -> fetch();
    $code_db -> close();

    $user_code_db = $connection -> prepare("SELECT * FROM `user_code_block_progress` WHERE `uuid` = ? AND `ucbid` = ?");
    $user_code_db -> bind_param("ss", $uuid, $ucbid);
    $user_code_db -> execute();
    $user_code_db -> bind_result($uuid, $ucbid, $saved_code, $id);
    $user_code_db -> store_result();
    $user_code_db -> fetch();
    $user_code_db -> close();

    if($saved_code == ""){
        $saved_code = $orig_code;
    }

    return Array($saved_code, $orig_code, $tests, $hidden_tests, $ucbid, $function_name);

}

function load($uuid, $type, $uid){
    # Loads the content for a particular code window.
    # Returns a json_encoded Array[ description, [saved-code, orig-code, tests] ]

    $content = get_content($type, $uid);
    $description = $content[0];
    $code_block_id = $content[1];
    $author = $content[2];

    $code_block = get_code_block($uuid, $code_block_id);

    return json_encode(Array($description, $code_block, $author));

}

function get_next_section($usid){

    GLOBAL $connection;

    $lesson_db = $connection -> prepare("SELECT * FROM `lessons` WHERE `sections` LIKE ?");
    $like = "%$usid%";
    $lesson_db -> bind_param("s",$like);
    $lesson_db -> execute();
    $lesson_db -> store_result();
    $lesson_db -> bind_result($lesson_name, $num_lesson_sections, $lesson_sections, $ulid, $id);
    $lesson_db -> fetch();
    $lesson_db -> close();

    $lesson_sections = explode("|", $lesson_sections);
    $number_sections = count($lesson_sections);

    if($number_sections > 1){

        $position_in_lesson = array_search($ucid, $lesson_sections);
        if($position_in_lesson < $number_sections - 1){

            return $lesson_sections[$position_in_lesson+1];

        }

    }

    return "Done.";

}

function save($uuid, $code, $ucbid){
    # Function that saves a user's progress in a particular code block.
    # Returns string.

    GLOBAL $connection;

    $update = $connection -> prepare("UPDATE `user_code_block_progress` SET `saved_code`= ? WHERE `uuid` = ? AND `ucbid` = ?");
    $update -> bind_param("sss", $code, $uuid, $ucbid);
    if($update -> execute()){

        $update -> close();
        return "Saved.";

    }
    $update -> close();
    return "Failed to save.";

}

function increment_progress($table, $has, $needed, $uuid, $uid_name, $uid){

    GLOBAL $connection;

    if($has < $needed){

        $table = "user_".$table."_progress";
        $increment = $connection -> prepare("UPDATE `$table` SET progress = progress + 1 WHERE `uuid` = ? AND `$uid_name` = ?");
        $increment -> bind_param("ss", $uuid, $uid);
        if($increment -> execute()){

            $increment -> close();
            $has += 1;
            if($has == $needed){

                $complete = $connection -> prepare("UPDATE `$table` SET `complete` = 1 WHERE `uuid` = ? AND `$uid_name` = ?");
                $complete -> bind_param("ss", $uuid, $uid);
                if($complete -> execute()){

                    $complete -> close();
                    return "Completed.";

                }
                $complete -> close();
                return "Error Completing.";

            }
            return "Incremented.";

        }
        $increment -> close();
        return "Error Incrementing.";

    }
    return "Already Complete.";


}

function get_progress($table, $uuid, $uid_name, $uid){

    GLOBAL $connection;

    $table = "user_".$table."_progress";
    $progress = $connection -> prepare("SELECT * FROM `$table` WHERE `uuid` = ? AND `$uid_name` = ?");
    $progress -> bind_param("ss", $uuid, $uid);
    $progress -> execute();
    $progress -> store_result();

    return $progress;

}

function mark_tracker_complete($table, $uuid, $uid_name, $uid){
    # Marks a particular progress tracker as complete.
    # Returns Boolean, if it updated or not.

    GLOBAL $connection;

    $table = "user_".$table."_progress";

    $complete = $connection -> prepare("UPDATE `$table` SET `complete`= 1 WHERE `uuid` = ? AND `$uid_name` = ?");
    $complete -> bind_param("ss", $uuid, $uid);

    if($complete -> execute()){

        $complete -> close();
        return true;

    }
    $complete -> close();
    return false;

}

function complete($uuid, $type, $uid){
    # Function that Handles the completion of a section or a challenge.

    GLOBAL $connection;

    if($type == "course"){

        $section = get_progress("section", $uuid, "usid", $uid);
        $section -> bind_result($uuid, $usid, $complete, $id);
        $section -> fetch();
        $section -> close();

        $lesson_db = $connection -> prepare("SELECT * FROM `lessons` WHERE `sections` LIKE ?");
        $like = "%$usid%";
        $lesson_db -> bind_param("s", $like);
        $lesson_db -> execute();
        $lesson_db -> store_result();
        $lesson_db -> bind_result($lesson_name, $num_lesson_sections, $lesson_sections, $ulid, $id);
        $lesson_db -> fetch();
        $lesson_db -> close();

        $course_db = $connection -> prepare("SELECT * FROM `courses` WHERE `lessons` LIKE ?");
        $like = "%$ulid%";
        $course_db -> bind_param("s", $like);
        $course_db -> execute();
        $course_db -> store_result();
        $course_db -> bind_result($lang, $name, $desc, $img, $pub, $num_lessons, $lessons, $ucid, $id);
        $course_db -> fetch();
        $course_db -> close();

        if($complete != 1){ // Section has not been completed before.

            if(mark_tracker_complete("section",$uuid,"usid",$usid)){ // Mark the section as complete.

                $lesson_progress_db = get_progress("lesson", $uuid, "ulid", $ulid);
                $lesson_progress_db -> bind_result($uuid, $ulid, $lesson_progress, $lesson_complete, $lid);
                $lesson_progress_db -> fetch();
                $lesson_progress_db -> close();

                $course_progress_db = get_progress("course", $uuid, "ucid", $ucid);
                $course_progress_db -> bind_result($uuid, $ucid, $course_progress, $course_complete, $cid);
                $course_progress_db -> fetch();
                $course_progress_db -> close();

                if($lesson_complete != 1 && $lesson_progress < $num_lesson_sections){

                    $lesson_progress_increment = increment_progress("lesson", $lesson_progress, $num_lesson_sections, $uuid, "ulid", $ulid);
                    if($lesson_progress_increment == "Completed."){

                        if($course_complete != 1 && $course_progress < $num_lessons){

                            $course_progress_increment = increment_progress("course", $course_progress, $num_lessons, $uuid, "ucid", $ucid);

                            if($course_progress_increment == "Completed."){

                                mark_tracker_complete("course", $uuid, "ucid", $ucid);
                                return json_encode(Array("Completed"=>Array("Section","Lesson","Course")));

                            }

                        }
                        elseif($course_complete != 1){

                            if(mark_tracker_complete("course", $uuid, "ucid", $ucid)){

                                return json_encode(Array("Completed"=>Array("Section","Lesson","Course")));

                            }
                            return json_encode(Array("Completed"=>Array("Section"),"Error"=>Array("Lesson")));

                        }
                        return json_encode(Array("Completed"=>Array("Section","Lesson"),"Error"=>Array("Course")));

                    }
                    return json_encode(Array("Completed"=>Array("Section"),"Progressed"=>Array("Lesson")));

                }
                elseif($lesson_complete != 1){

                    if(mark_tracker_complete("lesson",$uuid,"ulid",$ulid)){

                        return json_encode(Array("Completed"=>Array("Section","Lesson")));

                    }
                    return json_encode(Array("Completed"=>Array("Section"),"Error"=>Array("Lesson")));

                }
                return json_encode(Array("Completed"=>Array("Section")));

            }
            return json_encode(Array("Error"=>Array("Section")));

        }
        return "Already Complete.";

    }
    elseif($type == "challenge"){

        $challenge = get_progress("challenge", $uuid, "ucgid", $uid);
        $challenge -> bind_result($uuid, $ucgid, $challenge_progress, $challenge_complete, $cgid);
        $challenge -> fetch();
        $challenge -> close();

        if($challenge_complete != 1){

            if(mark_tracker_complete("challenge", $uuid, "ucgid", $ucgid)){

                return json_encode(Array("Completed"=>Array("Challenge")));

            };
            return json_encode(Array("Error"=>Array("Challenge")));

        }
        return "Already Complete.";

    }
    return "That type is not known.";

}

function get_course_from_section($usid){
    # Grabs the course associated with a particular section
    # Returns: JSON Array[ course => [details], lessons => [ ulid => [details], sections => [ usid ] ] ]
    # Precondition: Must be called with a course that exists.

    $parent_lesson_db = select_like("lessons", "sections", "%$usid%");
    $parent_lesson_db -> bind_result($lesson_name, $num_lesson_sections, $lesson_sections, $ulid, $lid);
    $parent_lesson_db -> fetch();
    $parent_lesson_db -> close();

    $parent_course_db = select_like("courses", "lessons", "%$ulid%");
    $parent_course_db -> bind_result($course_lang, $course_name, $course_desc, $course_img, $course_published, $course_num_lessons, $course_lessons, $ucid, $cid);
    $parent_course_db -> fetch();
    $parent_course_db -> close();

    $course = Array(
        "name" => $course_name,
        "description" => $course_desc,
        "image" => $course_img,
        "published" => $course_published,
        "ucid" => $ucid
    );

    $lessons = Array();

    $lesson_uids = explode("|", $course_lessons);

    foreach ($lesson_uids as $lesson){

        $lesson_db = select_uid("lessons", "ulid", $lesson);
        $lesson_db -> bind_result($lesson_name, $num_lesson_sections, $lesson_sections, $ulid, $lid);
        $lesson_db -> fetch();
        $lesson_db -> close();

        $sections = explode("|", $lesson_sections);

        $lessons[$ulid] = Array("name" => $lesson_name, "sections" => $sections);

    }

    return json_encode(Array("course" => $course, "lessons" => $lessons));

}

function code(){
    # Main function of the file, acts as the code endpoint parser.
    # Returns none, echos the result of whatever function was called.

    $method = is_posted("method");
    if($method){
        $uuid = get_uuid();
        if($uuid){
            switch($method) {

                case "load":

                    $type = is_posted("type");
                    $content_uid = is_posted("content_id");
                    if($type && $content_uid){
                        echo load($uuid, $type, $content_uid);
                    }
                    else{
                        echo "You must supply both the type and content_id.";
                    }
                    break;

                case "save":
                    $code = is_posted("code");
                    $ucbid = is_posted("ucbid");

                    if($code && $ucbid){
                        echo save($uuid, $code, $ucbid);
                    }
                    else{
                        echo "You must supply both the code and the ucbid";
                    }

                    break;

                case "get_next_section":
                    $usid = is_posted("usid");
                    if($usid){
                        echo get_next_section($usid);
                    }
                    else{
                        echo "You must supply the USID of the current section.";
                    }
                    break;

                case "complete":
                    $type = is_posted("type");
                    $content_uid = is_posted("content_id");
                    if($type && $content_uid){
                        echo complete($uuid, $type, $content_uid);
                    }
                    else{
                        echo "You must supply both the type and content_id.";
                    }
                    break;

                case "challenge_attempt":
                    $ucgid = is_posted("ucgid");
                    if($ucgid){
                        echo increment_progress("challenge", 0, 10, $uuid, "ucgid", $ucgid);
                    }
                    else{
                        echo "You must supply the ucgid of the challenge";
                    }
                    break;

                case "get_course":
                    $usid = is_posted("usid");
                    if($usid){
                        echo get_course_from_section($usid);
                    }
                    else{
                        echo "You must supply the usid of the section.";
                    }
                    break;

                case "get_code_block":
                    $ucbid = is_posted("ucbid");
                    if($ucbid){
                        echo json_encode(get_code_block("",$ucbid));
                    }
                    else{
                        echo "You must supply the ucbid for the code block.";
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
code();