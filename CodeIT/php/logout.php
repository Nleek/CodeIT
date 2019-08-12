<?php

/*
File: Logout.php
Desc: Destroy the session with the user.
*/

require("../includes/includes.php");
session_destroy();
header('Location:'.$base_url.'/'.$_GET["return"]);

?>