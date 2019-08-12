<?php require("../includes/includes.php");?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <?php require("../includes/head.php"); ?>
        <title>Challenge | CodeIT</title>
        <style>
            #landingSection{

                margin-top: -80px;
                text-align: center;
                color: #f0f0f0;
                background-size: contain;
                overflow: hidden;
                height: auto !important;
                position: relative;

            }
            #landingSection .parallax{
                position: absolute;
                top:0;
                left:0;
                width: 100%;
                height:50vh;
                background: #191919 url("../images/spacebackground2.png") no-repeat top;
                z-index:-1;
            }
            .overlay{
                float: left;
                display: block;
                width: 100%;
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
            #challengesSection{
                background-color: #f0f0f0;
                margin-bottom: 140px;
            }
            #challengesWrapper{
                position: relative;
            }
            .challenge{
                width: auto;
                min-width: 200px;
                margin: 10px auto;
                background-color: #fff;
                font-family: "Open Sans", SansSerif;
                position: relative;
            }
            .challenge-block{
                min-width: 300px;
                overflow: hidden;
                margin: 0 auto;
                display: block;
            }
            .challenge-block img{
                height: 100px;
                border: none;
                float: left;
                margin: 0 auto;
            }
            .challenge-block div{
                min-width: 200px;
                overflow: hidden;
                float: left;
            }
            .challenge-block div h3{
                margin-top: 0;
                margin-bottom: 10px;
                font-weight: 400;
                font-size: 1.4em;
                padding-left: 10px;
                padding-right: 10px;
            }
            .challenge-block div span{
                font-size: 0.9em;
            }
            .challenge:hover:not(.challenge-active){
                -webkit-transform: translateY(-7px);
                transform: translateY(-7px);
            }
            .challenge .battle{
                position: absolute;
                top: 0px;
                left: 0px;
            }
            .challenge .battle-image{
                height: 52px;
                width: 52px;
                text-align: center;
                border: none;
                overflow: hidden;
                background-position: center;
                background-size: contain;
                position: absolute;
                top:-10px;
                left:-15px;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
            }
            .challenge .battle-title{
                display: none;
            }
            .challenge:hover .battle{
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(80, 169, 232, 0.86);
                color: #fff;
                padding-top: 10px;
            }
            .challenge:hover .battle-image{

            }
            .challenge:hover .battle-title{
                line-height: 80px;
                display: block;
                text-align: center;
            }
            .complete{
                position: absolute;
                top: -10px;
                right: -15px;
                height: 50px;
                width: 36px;
                padding-left: 14px;
                text-align: center;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
            }
            #add{
                position: absolute;
                bottom: 10px;
                right: 10px;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
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
        </style>
    </head>
    <body>
        <?php require("../includes/header.php"); ?>

        <main id="v-root">

            <section id="landingSection" class="parallax-container">
                <div class="parallax" data-parallax-divisor="-2"></div>
                <div class="overlay padding-left padding-right">
                    <h1>Community Posted Challenges</h1>
                    <h4>Test your coding skills</h4>
                </div>
            </section>
            <section v-if="challenges.length > 0" id="challengesSection" class="padding">
                <h3 class="hidden">Challenges</h3>
                <div id="challengesWrapper" class="center-wrapper text-center">
                    <div class="inline-block padding-top">
                        <article v-for="challenge in challenges" :id="challenge['details']['ucgid']" :data-battle="!(challenge['battle']==false)" class="challenge transition cursor">
                            <div onclick="goToCode.call(this)">
                                <div v-if="challenge['battle']" class="battle padding transition">
                                    <div class="battle-image circle" style="background-image: url('../images/default-user.png')" :title="challenge['battle'][1]"></div>
                                    <div class="battle-title" v-if="challenge['battle'][0] == 'You\'ve been Challenged'">
                                        {{ challenge['battle'][1] }} Challenged You To Complete This Challenge!
                                    </div>
                                    <div class="battle-title" v-else>
                                        You Challenged {{ challenge['battle'][1] }} To Complete This Challenge!
                                    </div>
                                </div>
                                <div v-if="challenge['progress'][1]" class="complete material-icon-container circle highlight"><i class="material-icons">check</i></div>
                                <div class="overflow-hidden block challenge-block">
                                    <img src="<?php echo $base_url;?>/images/js.png" alt="JS" />
                                    <div class="block padding-top">
                                        <h3 class="block">{{ challenge['details']['name'] }} - {{ challenge['details']['difficulty'] }}</h3>
                                        <hr class="modern-line" />
                                        <span class="block text-center padding">Author: {{ challenge['details']['author'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="challenge['details']['published'] == 0" class="block text-center">
                                <hr class="modern-line" />
                                <button :data-challenge="challenge['details']['ucgid']" class="modern-button highlight cursor padding margin-bottom" v-on:click="publish">Publish Challenge!</button>
                            </div>
                        </article>
                        <?php if(check_perms(1)){

                            echo "
                            <button id='add' class='circle padding highlight cursor' onclick='window.location.href=\"../code/edit.php?action=new&type=challenge\"'><span class='material-icon-container padding'><i class='material-icons'>add</i></span></button>
                            ";

                        }?>
                    </div>
                </div>
            </section>
            <section id="splash-page" v-if="challenges.length == 0" class="hidden">
                <div class="center-wrapper overflow-hidden half-height">
                    <h2>Login to start your CodeIT journey</h2>
                    <hr class="modern-line" />
                    <p>
                        Our library of community submitted challenges is always growing.<br /><br />
                        Once you log in you will be able to try them all!<br /><br />
                        Test your coding skills with creative challenges made by the CodeIT community.
                    </p>
                    <button class="modern-button cursor highlight padding margin-top" onclick="loginModal.open()">Get Going Now</button>
                </div>
            </section>

        </main>

        <?php

            require("../includes/footer.php");
            if(check_perms(10)){
                echo"
                <script>
                        function publish(ucid){
                            $.post('../php/challenges.php',{'method':'publish','ucgid':ucid},function(response){
                                if(response == 'Published.'){
                                    update_models();
                                }
                                else{
                                    console.log(response);
                                }
                            });
                        }
                </script>
                ";
            }

        ?>

        <script>
            function goToCode(){

                var me = $(this).parent();
                var id = me.attr('id');
                var is_battle = me.data('battle');
                var url = "../code/index.php?type=challenge&challenge="+id+"&battle="+is_battle;

                window.location.href = url;

            }
            var challenges_vue = new Vue({

                el: "#v-root",

                data:{
                    challenges: []
                },

                methods:{

                    publish: function(e){
                        var ucgid = $(e.target).data("challenge");
                        publish(ucgid);
                    }

                },
                mounted: function(){

                    setTimeout(function(){
                        $("#splash-page").removeClass("hidden");
                    },500)

                }

            });
            function update_models(){
                // Updates the challenges

                $.post('../php/challenges.php',{'method':'list'},function(response){
                    try{
                        challenges_vue.challenges = JSON.parse(response);
                    }
                    catch(err){
                        console.log(err + " | " + response);
                    }
                });

            }
            function update_loop(){
                // Enables live updates of the challenges.

                update_models();
                setTimeout(function(){
                    update_loop();
                },5000);

            }
            $(document).ready(function(){

                update_loop();
                var parallax = $("body").parallax();

            });
        </script>
    </body>
</html>