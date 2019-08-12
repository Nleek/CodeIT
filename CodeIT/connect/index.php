<?php require("../includes/includes.php");?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <?php require("../includes/head.php"); ?>
        <title>Connect | CodeIT</title>
        <style>

            body{
                font-family: "Open Sans", Arial;
                background-color: #191919;
            }
            #landingSection{

                margin-top: -80px;
                text-align: center;
                color: #f0f0f0;
                overflow: hidden;
                height: auto !important;
                position: relative;

            }
            #landingSection .parallax{
                position: absolute;
                top:0;
                left:0;
                width: 100%;
                height:65vh;
                background-size: cover !important;
                background: #191919 url("../images/spacebackground.png") no-repeat top;
                z-index: -1;
            }
            .overlay{
                float: left;
                display: block;
                width: calc(100% - 20px);
                height: 100%;
                padding-top: 15vh;
                padding-bottom: 15vh;
                background-color: rgba(0,0,0,0.6);
                text-shadow: 0 2px 7px rgba(107, 107, 107, 0.89) ;
                z-index:2;
            }
            #landingSection h1, #landingSection h4{
                font-weight: 300 !important;
            }
            #landingSection h1{
                font-family: "Montserrat", Arial;
                font-size: 2.5em;
            }
            #landingSection h4{
                font-family: "Raleway", SansSerif;
                font-size: 1.3em;
            }

            #communitySection{
                background-color: #191919;
                color: #0f0f0f;
                font-family: "Open Sans";
            }
            .user{
                width: calc(33.33% - 20px);
                background-color:  #fff;
                overflow: hidden;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                text-align: left;
            }
            .user_rank, .user_screen_name{
                width: 50%;
            }
            .user_screen_name{
                font-family: "Montserrat", Arial;
                font-size: 1.2em;
                padding-top: 20px;
            }
            .user_rank{
                text-decoration: underline;
                font-size: 0.9em;
                height: 20px;
            }
            .user_picture{
                padding-left: 10px;
                padding-right: 10px;
                display: inline-block;
                height: 90px;
                margin-bottom: 10px;
            }
            .user_picture img{
                height: 90px;
                width: 90px;
                border: none;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
            }
            .user_achievements{
                border-top: 1px solid #ccc;
                border-bottom: 1px solid #ccc;
                clear: both;
            }
            #splash-page{
                background-color: #fff;
                font-family: "Open Sans", SansSerif;
                text-align: center;
            }
            #splash-page h2{
                font-family: "Raleway", Arial;
                font-weight: 300;
            }
            .form-error{
                border-bottom-color: red !important;
            }
            @media screen and (max-width: 800px) and (min-width: 671px){
                .user{
                    width: calc(50% - 20px);
                }
            }
            @media screen and (max-width: 670px){
                .user{
                    width: calc(100% - 20px);
                }
            }
        </style>
    </head>
    <body>

        <?php require("../includes/header.php"); ?>

        <main id="v-root">

            <section id="landingSection" class="parallax-container">
                <div class="parallax" data-parallax-divisor="-2"></div>
                <div class="overlay padding-left padding-right">
                    <h1>The CodeIT Community</h1>
                    <h4>Ordered by achievement progression</h4>
                </div>

            </section>
            <section id="communitySection" v-if="people.length > 0" class="padding">

                <div id="accountsWrapper" class="center-wrapper text-center">

                    <div class="inline-block">
                        <article :id="person['uuid']" class="user margin inline-block padding-top padding-bottom" v-if="people.length" v-for="(person, index) in people">
                            <div class="user_picture right"><img class="circle" src="../images/default-user.png" alt="Default user" /></div>
                            <div class="user_rank inline-block left padding-left">Rank {{ index+1 }}</div>
                            <div class="user_screen_name inline-block left padding-left">{{ person["sn"] }}</div>
                            <div class="user_achievements text-center padding-top padding-bottom">{{ person["num_achievements"] }} Achievements</div>
                            <div class="user_button_container padding-top text-center" v-if="signed_in">
                                <button class="modern-button padding highlight cursor" v-if="!person['is_friend'] && !person['is_self']" v-on:click='addFriend'>Add Friend</button>
                                <button class="modern-button padding black-text" v-if="person['is_friend'] == 'pending' && !person['is_self']">Request Sent</button>
                                <button class="modern-button padding highlight cursor" v-if="person['is_friend'] == 'requested' && !person['is_self']" v-on:click='acceptRequest'>Accept Friend Request</button>
                                <button class="modern-button padding highlight cursor" v-if="person['is_friend'] == true && !person['is_self'] && !person['is_challenged']" v-on:click='challenge'>Challenge</button>
                                <button class="modern-button padding highlight cursor" v-if="person['is_friend'] == true && !person['is_self'] && person['is_challenged']" :onclick="person['is_challenged'][1]">{{person['is_challenged'][0]}}</button>
                                <button class="modern-button padding black-text" v-if="person['is_self']"><span class="material-icon-container"><i class="material-icons">star_border</i>&nbsp; You &nbsp;<i class="material-icons">star_border</i></span></button>
                            </div>
                        </article>
                    </div>
                </div>

            </section>
            <section id="splash-page" v-if="people.length == 0" class="hidden">
                <div class="center-wrapper overflow-hidden half-height">
                    <h2>Login to start your CodeIT journey</h2>
                    <hr class="modern-line" />
                    <p>
                        Our community of members is visible only to those who have logged in.<br /><br />
                        Once you log in you will be able to friend and challenge them all!<br /><br />
                        Gain achievements, make friends, and challenge them to complete a challenge you both have not completed yet!
                    </p>
                    <button class="modern-button cursor highlight padding margin-top" onclick="loginModal.open()">Get Started Now</button>
                </div>
            </section>

        </main>

        <?php require("../includes/footer.php")?>
        <script>

            var challengeModal = new Modal({
                icon: false,
                headerClass:'padding highlight',
                bodyClass:'padding',
                bodyContent:'<p>Challenges you have already completed are not shown.</p>'
            });

            var community = new Vue({

                el: "#v-root",

                data: {

                    people: [],
                    signed_in: <?php echo isset($_SESSION["uuid"]) ? "true" : "false";?>

                },

                methods:{

                    addFriend: function(e){

                        var to = $(e.target).parent().parent().attr('id');
                        $.post("../php/connect.php",{"method":"add_friend","to":to},function(response){
                            console.log(response);
                            updateModels();
                        });

                    },
                    acceptRequest: function(e){

                        var from = $(e.target).parent().parent().attr('id');
                        $.post("../php/connect.php",{"method":"accept_request","from":from},function(response){
                            console.log(response);
                            updateModels();
                            getAchievement("friends made");
                        });

                    },
                    challenge: function(e){

                        var profile = $(e.target).parent().parent();
                        var user = profile.attr('id');
                        var user_name = profile.find(".user_screen_name").html();
                        $.post("../php/challenges.php",{"method":"list"},function(response){

                            challengeModal.settings.headerContent="<h3>Select a Challenge for "+user_name+" to complete!</h3>";
                            challengeModal.settings.bodyButtons=[
                                ["Send challenge!","modern-button padding highlight cursor transition margin-top","send_challenge(\""+user+"\")"]
                            ];
                            try{

                                response = JSON.parse(response);

                                challengeModal.settings.bodyContent += "<label for='challenge-select'><select id='challenge-select'>";
                                for(var challenge_idx = 0; challenge_idx < response.length; challenge_idx++){

                                    var challenge = response[challenge_idx];
                                    var name = challenge["details"]["name"];
                                    var difficulty = challenge["details"]["difficulty"];
                                    var author = challenge["details"]["author"];
                                    var published = challenge["details"]["published"];
                                    var ucgid = challenge["details"]["ucgid"];

                                    if(published){

                                        challengeModal.settings.bodyContent += "<option value=\""+ucgid+"\">'"+name+"' by "+author+" ("+difficulty+")</option>";

                                    }

                                }
                                challengeModal.settings.bodyContent += "</select></label>";
                                challengeModal.open();

                            }
                            catch(err){
                                console.log("Challenge load -> " + err + " | " + response);
                            }

                        });


                    }

                },
                mounted: function(){

                    setTimeout(function(){
                        $("#splash-page").removeClass("hidden");
                    },500)

                }


            });

            function send_challenge(user){

                var challenge = $("#challenge-select").val();
                if(challenge != ""){
                    $.post("../php/challenges.php",{"method":"battle","challenged":user,"challenge":challenge},function(response){

                        if(response == ""){

                            getNotifications();

                        }
                        else{
                            console.log(response)
                        }

                        challengeModal.close();
                        updateModels();

                    });
                }
                else{
                    $("#challenge-select").addClass("form-error");
                }

            }

            function updateModels(){

                $.post("../php/connect.php",{"method":"list"},function(response){

                    if(response){

                        try{
                            response = response.replace("e","");
                            community.people = JSON.parse(response);
                        }
                        catch(err){
                            console.log(err + " | " + response);
                        }

                    }

                });

            }

            function updateLoop(){

                updateModels();

                setTimeout(function(){
                    updateLoop();
                }, 5000)

            }

            $(document).ready(function(){

                updateLoop();
                var parallax = $("body").parallax();

            });

        </script>
    </body>
</html>