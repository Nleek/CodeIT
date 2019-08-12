<?php
if(!preg_match('/www/', $_SERVER['HTTP_HOST']))header('Location: '.$base_url); // This line redirects users that are not at the correct URL.
?>
<!--
 | This website was made with love from the ground up by the Charles H. Mccann Technical Highschool's BPA Web design team for the 2017 Web design competition.
 | Authors: Nikki Kirk and Emily Shanley.
 | All content on this site is copyright free, and in the public domain. All *custom* code on this site is the sole property of the authors stated above, reuse is forbidden.
 | The creation and design of this website is the sole work of the authors above, and was 100% hand crafted (hand written).
 | The front end of this site is written in HTML, CSS and JavaScript. The back end is written in PHP.
 | Most of the graphics on this site were also designed by the team.
 | Some Javascript plugins were used in the making of this website, all are in the public domain as free-to-use. We do not take credit for the design / coding of any part(s) of them.
 | Copyright 2017 McCann Tech BPA. All rights reserved.
-->
<meta charset="UTF-8">
<meta name=viewport content="width=device-width, initial-scale=1">
<link rel="icon" type="image/x-icon" href="<?php echo $base_url;?>/favicon.ico" />
<link rel='stylesheet' type='text/css' href='<?php echo $base_url;?>/css/main.css'/>
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:300,400,600|Raleway:300,400" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<script src="<?php echo $base_url;?>/js/main.js"></script>