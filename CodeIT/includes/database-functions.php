<?php

// Houses global database functions.

// -- Non-Specific functions --

function select_all($table){
    # Selects all of the items in a given table.
    # Returns - MySql object.

    GLOBAL $connection;

    $object = $connection -> prepare("SELECT * FROM `$table`");
    $object -> execute();
    $object -> store_result();

    return $object;

}

function select_uid($table, $uid_name, $uid){
    # Selects all items in a given $table whose $uid_name column matches the key $uid.
    # Returns - MySql object.

    GLOBAL $connection;

    $object = $connection -> prepare("SELECT * FROM `$table` WHERE `$uid_name` = ?");
    $object -> bind_param("s", $uid);
    $object -> execute();
    $object -> store_result();

    return $object;

}

function select_like($table, $column, $like){
    # Selects all items in a given $table whose $uid_name column contains the key $uid.
    # Returns - MySql object.

    GLOBAL $connection;

    $object = $connection -> prepare("SELECT * FROM `$table` WHERE `$column` LIKE ?");
    $object -> bind_param("s", $like);
    $object -> execute();
    $object -> store_result();

    return $object;

}

function update($table, $update_column, $new_value, $uid_name, $uid){
    # Updates an entry in the database.
    # Returns boolean whether of not the update was successful.

    GLOBAL $connection;

    $update = $connection -> prepare("UPDATE `$table` SET `$update_column` = ? WHERE `$uid_name` = ?");
    $update -> bind_param("ss", $new_value, $uid);

    if($update -> execute()){

        $update -> close();
        return true;

    }
    $update -> close();
    return false;

}

function check_exists($table, $key_name, $key){
    # Checks whether or not a specific key exists in a given table.
    # Params: $key_name and $key may be Arrays or Strings. They must be the same type and, if they are Arrays, they must
    #         be of the same size.
    # Returns - Boolean

    GLOBAL $con;

    $query_string = "";

    if (is_array($key_name) or ($key_name instanceof Traversable)){

        if(is_array($key) or ($key instanceof Traversable)){

            if(sizeof($key_name) == sizeof($key)){

                if(sizeof($key_name > 1)){

                    $query_string = "`$key_name[0]` = '$key[0]'";

                    $idx = 0;
                    foreach($key_name as $kn){

                        if($idx != 0){
                            $k = $key[$idx];

                            $query_string .= " AND `$kn` = '$k'";

                        }
                        $idx++;
                    }

                }
                else{

                    if(sizeof($key_name) == 1){

                        $query_string = "`$key_name[0]` = '$key[0]'";

                    }
                    else{

                        echo "You must not pass empty arrays.";

                    }

                }
            }
            else{

                echo "The length of both the key_name and key must be equal.";

            }


        }

    }
    else{
        $query_string = "`$key_name` = '$key'";
    }

    $result = mysqli_query($con, "SELECT 1 FROM `$table` WHERE $query_string");

    if ($result && mysqli_num_rows($result) > 0){

       return true;

    }
    return false;

}

// ----- Specific functions -----

// -- User --
function get_user($uuid){
    # Grabs a user by UUID.
    # Returns - JSON.

    $user = select_uid("users", "uuid", $uuid);
    $user -> bind_result($fn, $ln, $sn, $em, $pw, $salt, $role, $uuid, $id);
    $user -> fetch();
    $user -> close();

    return json_encode(Array($fn, $ln, $sn, $em, $uuid));

}

// -- Progress trackers --

function gen_new_trackers($tracker_table, $tracker_uid_name, $tracker_uid){
    # Generates progress trackers for a new category for every existing user if one doesnt exist.
    # Returns - Number of trackers made.

    GLOBAL $con;

    $users_db = select_all("users");
    $users_db -> bind_result($fn, $ln, $sn, $em, $pw, $salt, $role, $uuid, $id);

    $num_new_trackers = 0;
    # Loop over every registered user.
    while($users_db -> fetch()){

        # Check if that user has a tracker for the specific achievement
        if(!check_exists($tracker_table, Array("uuid", $tracker_uid_name), Array($uuid, $tracker_uid))){

            //echo "\n $tracker_table uuid:$uuid | $tracker_uid_name: $tracker_uid";

            # Create a tracker if they don't.
            if($tracker_table == "user_code_block_progress"){
                mysqli_query($con, "INSERT INTO $tracker_table(`uuid`, `$tracker_uid_name`, `saved_code`) VALUES ('$uuid', '$tracker_uid', '')");
            }
            else{
                mysqli_query($con, "INSERT INTO $tracker_table(`uuid`, `$tracker_uid_name`) VALUES ('$uuid', '$tracker_uid')");
            }
            $num_new_trackers++;

        }

    }

    $users_db -> close();

    return $num_new_trackers;

}

function bind_trackers(){
    # Generates all trackers for every user that doesnt have them.
    # Returns - The total number of trackers made.

    GLOBAL $pdo;

    $tracked_tables = Array(
        Array(
            "achievements",
            "uaid"
        ),
        Array(
            "achievement_tags",
            "name"
        ),
        Array(
            "courses",
            "ucid"
        ),
        Array(
            "lessons",
            "ulid"
        ),
        Array(
            "sections",
            "usid"
        ),
        Array(
            "challenges",
            "ucgid"
        ),
        Array(
            "code_block",
            "ucbid"
        )
    );

    $num_new_trackers = 0;
    # Loop through all of the tracked tables
    foreach ($tracked_tables as $table) {

        $table_name = $table[0];
        $table_uid_name = $table[1];

        $sql  = "SELECT * FROM $table_name";
        $stmt = $pdo->prepare($sql);
        $stmt -> execute();

        $table_name = "user_".rtrim($table_name, "s")."_progress";

        # Loop through all of the tracked fields.
        while($row = $stmt->fetch()){

            $tracker_uid = $row[$table_uid_name];

// These echo statements were my saviour, I spent far too long debugging an issue and these are what helped me solve it.
//            echo "\n{\n".implode("\n  -",$row)."\n}";
//            echo "\n$table_name | $table_uid_name : $tracker_uid\n";

            $num_new_trackers += gen_new_trackers($table_name, $table_uid_name, $tracker_uid);

        }


    }

    return $num_new_trackers;

}

function tests(){

    echo "gen_uid: ".gen_uid("mostlytechguru")."<br />";

    echo "get_user: ".get_user("7795d2b412b2aaf41111cbfbcc2dd408")."<br />";

    #echo "get_all_achievements: (json) ".json_encode(get_all_achievements())."<br />";

    #echo "get_achievement_progress: ".get_achievement_progress("7795d2b412b2aaf41111cbfbcc2dd408")."<br />";

    echo "check_exists: ".check_exists("user_achievement_progress", Array("uuid","uaid"), Array("7795d2b412b2aaf41111cbfbcc2dd408","eae6338e059ced2131850906b420113e"))."<br />";

    echo "new_user_trackers: ".bind_trackers("7795d2b412b2aaf41111cbfbcc2dd408")."<br />";

}

#tests();