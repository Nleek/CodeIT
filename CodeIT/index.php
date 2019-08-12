<?php require("includes/includes.php"); ?>
<!DOCTYPE html>

<html lang="en">
<head>
    <?php require("includes/head.php"); ?>
    <title>Home | CodeIT</title>
    <style>
        #landingSection{
            background-color: #1E1B30;
            overflow: hidden;
            margin-top: -80px;
            height: 90vh;
        }
        #learnSection{
            background-color: #fff;
        }
        #connectSection{
            overflow: hidden;
            padding-top: 30px;
            padding-bottom: 30px;
            background-color: #524B80;
        }
        #signUpSection{
            background-image: url('images/purplenoise.png');
            background-color: #6C53C4;
        }
        #contributeSection{
            background-color: #EBEBEB;
            padding-top: 55px;
            padding-bottom: 55px;
        }
        #reviewSection{
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            background: #7E89CF url('images/desk.png') no-repeat center;
        }
        #landingTitle{
            font-family: 'Montserrat', sans-serif;
            font-size: 7em;
            text-align: center;
            color: #fff;
            margin-top: 10vh;
            font-weight: 700;
        }
        #landingSubtitle{
            font-family: 'Open Sans', sans-serif;
            text-align: center;
            color: #fff;
            font-size: 1.7em;
            font-weight: 100;
            -webkit-font-smoothing: antialiased;
        }
        #buttonContainer{
            padding-top: 30px;
        }
        #learnContainer{
            padding: 10px 50px;
        }
        .learnBoxContainer{
            display: block;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .learnBoxTitle{
            font-size: 1.2em;
            color: #525253;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
        }
        .learnBox{
            width: 33.33%;
            height: auto;
            overflow: hidden;
            float: left;
            margin-top: 10px;
        }
        .learnBoxContent{
            font-size: .8em;
            color: #676767;
            font-family: 'Open Sans', sans-serif;
            font-weight: 300;
            line-height: 27px;
        }
        .learnIcons{
            width: 20%;
            text-align: center;
            padding-top: 10px;
        }
        .learnContentWrap{
            width: 75%;
            margin-right: 5%;
        }
        .learnIcons img{
            height: 30px;
        }
        .signUpSectionTitle{
            text-align: center;
            font-size: 1.7em;
            color: #fff;
            font-family: 'Open Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            font-weight: 100;
            padding-top: 50px;
            padding-bottom: 50px;
            margin:0;
        }
        #contributeParagraph{
            text-align: center;
            font-family: 'Open Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #2a2a2a;
            font-size: 1.1em;

        }
        #contributeParagraph img{
            width: 350px;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        @media screen and (max-width: 940px){
            .learnBox{
                width: 50%;
            }

        }
        @media screen and (max-width: 620px){
            .learnBox{
                width: 100%;
                font-size: 1.1em;
            }
        }
        #reviewBox{
            width: 33.33%;
            background-color: black;
            float: left;
            opacity: 0.8;
        }
        #connectSection{
            height: auto !important;
            position: relative;
        }
        #connectSection img{
            float: right;
            max-height: 300px;
            width: 33%;
            max-width: 376px;
            min-width: calc(376px - 33%);
            margin: 0 60px 0px 0;
        }
        .connectContent{
            width: 45.314%;
            float: left;
            margin-left: 9.686%;
        }
        .connectHeader{
            width: 300px;
            font-family: 'Open Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 1.7em;
            color: #fff;
            border-bottom: 1px solid white;
            /*margin-top: 40px;*/
            /*margin-left: 60px;*/
        }
        .connectWords{
            font-family: 'Open Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 1em;
            color: #fff;
            margin-top: 20px;
            /*margin-left: 60px;*/
        }
        @media screen and (max-width: 940px){
            .connectContent{
                width: 85%;
                float: none;
                display: block;
                margin: 0 auto 30px;
                padding-top: 60px;
            }
            .connectHeader{
                margin: 30px auto 30px;
                width: 85%;
            }
            .connectWords{
                margin: 0 auto;
                width: 85%;
            }
            #connectSection img{
                float: none;
                display: block;
                margin: 0 auto;
                width: 60%;
            }
        }
        @media screen and (max-width: 620px){
            #landingTitle{
                font-size: 5em;
            }
            #connectSection{
                font-size: 1.1em;
            }
            .connectContent{
                margin: 0px auto;
                text-align: center;
            }
            .signUpSectionTitle{
                padding-left: 10px;
                padding-right: 10px;
            }
        }
        @media screen and (max-width: 400px){
            #landingTitle{
                font-size: 3.5em;
                margin-top: 10px;
            }
        }
        .parallax {
            transition: all 0.25s linear;
            -moz-transition: all 0.25s linear;
            -ms-transition: all 0.25s linear;
            -o-transition: all 0.25s linear;
        }
        #parallax-wrapper div{
            background: no-repeat center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1;
            height: 95vh;
        }
        #background{
            background-size: cover !important;
            background-image: url("images/background.png") !important;
        }
        #back-clouds{
            background-size: cover !important;
            background-image: url("images/toplayer.png") !important;
        }
        #rocket{
            background-image: url("images/rocket.png") !important;
        }
        #front-clouds{
            background-size: cover !important;
            background-image: url("images/realtoplayer.png") !important;
        }
        #landing-content{
            position: relative;
            z-index: 2;
            padding-top: 80px;
        }
    </style>
</head>

<body>

    <?php require("includes/header.php"); ?>

    <main>
        <section id="landingSection" class="three-quarter-height parallax-container">
            <h3 class="hidden">Home</h3>
            <div id="parallax-wrapper">
                <div id="background" class="full-width full-height" data-parallax-divisor="-5"></div>
                <div id="back-clouds" class="parallax full-width full-height" data-parallax-divisor="-3"></div>
                <div id="rocket" class="parallax full-width full-height" data-parallax-divisor="2" data-parallax-offset="240"></div>
                <div id="front-clouds" class="parallax full-width full-height" data-parallax-divisor="-2.5"  data-parallax-offset="100"></div>
            </div>
            <div id="landing-content">
                <div id="landingTitle">DIY CODING</div>
                <div id="landingSubtitle">Launch into CodeIT and teach yourself code the easy way!</div>
                <div  id="buttonContainer" class="full-width text-center">
                    <div class="inline-block overflow-hidden margin-top">
                        <?php
                            if(!isset($_SESSION["uuid"])){
                                echo"<button class=\"flat-button transition cursor margin-right transparent\" onclick='loginModal.open()'>SIGN UP</button>";
                            }
                            else{
                                echo"<button class=\"flat-button transition cursor margin-right transparent\" onclick=\"window.location.href='profile'\">View Account</button>";
                            }
                        ?>
                        <button id='learn-button' class="flat-button transition cursor">LEARN ABOUT US</button>
                    </div>
                </div>
            </div>
        </section>
        <section id="learnSection" class="">
            <h3 class="hidden">Learn</h3>
            <div id="learnContainer">
                <div class="learnBoxContainer">
                    <div class="learnBox">
                        <div class="left learnIcons"><img src="images/friend.png" alt="icon" class="material-icon-container"/></div>
                        <div class="right learnContentWrap">
                            <div class="learnBoxTitle">Connect With Friends</div>
                            <div class="learnBoxContent"> Connecting with friends and family is a great way to stay motivated to learn code. Race to see who earns more badges!</div>
                        </div>
                    </div>
                    <div class="learnBox">
                        <div class="left learnIcons"><img src="images/user.png" alt="icon" class="material-icon-container"/></div>
                        <div class="right learnContentWrap">
                            <div class="learnBoxTitle">Interactive Learning</div>
                            <div class="learnBoxContent">With our interactive lessons, you will be fully immersed in the skill you are learning, giving you a hands-on learning experience!</div>
                        </div>
                    </div>
                    <div class="learnBox">
                        <div class="left learnIcons"><img src="images/share.png" alt="icon" class="material-icon-container"/></div>
                        <div class="right learnContentWrap">
                            <div class="learnBoxTitle">Share In The Community</div>
                            <div class="learnBoxContent">Have you discovered a great trick to learning code? Share it with other users! If you have questions, the CodeIT member forum will allow you to find answers, and, if nobody has asked your question yet, don't be afraid to be the first!</div>
                        </div>
                    </div>
                    <div class="learnBox">
                        <div class="left learnIcons"><img src="images/codeTags.png" alt="icon" class="material-icon-container"/></div>
                        <div class="right learnContentWrap">
                            <div class="learnBoxTitle">Learn Clean Code</div>
                            <div class="learnBoxContent">Our lessons will provide you with the newest, most up-to-date coding skills in order to better prepare you for the ever-changing world of modern programming.</div>
                        </div>
                    </div>
                    <div class="learnBox">
                        <div class="left learnIcons"><img src="images/DIY.png" alt="icon" class="material-icon-container"/></div>
                        <div class="right learnContentWrap">
                            <div class="learnBoxTitle">Powerful DIY</div>
                            <div class="learnBoxContent">Learning is always more rewarding when you've taught the skill to yourself! CodeIT allows you to work at your own pace to learn the right way - and you can always backtrack to refresh your memory!</div>
                        </div>
                    </div>
                    <div class="learnBox">
                        <div class="left learnIcons"><img src="images/user.png" alt="icon" class="material-icon-container"/></div>
                        <div class="right learnContentWrap">
                            <div class="learnBoxTitle">Code It Yourself</div>
                            <div class="learnBoxContent">Hands on projects ingrained in each lesson allow the student to see the skills they're learning in action - the most rewarding part about the process!</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="connectSection" class="overflow-hidden">
            <h3 class="hidden">Connect</h3>
            <img src="images/computer.png" alt="computer" />
            <div class="connectContent">
                <div class="connectHeader">How It All Works...</div>
                <div class="connectWords">CodeIT uses a variation of lessons and step-by-step challenges to teach users the basics of coding. Lessons range from beginner to more advanced, preventing the limitation of more advanced coders who want to brush up on their skills. The site allows for users to practice their skills, add friends, and test their skills by challenging each other. Users are rewarded with achievements upon the completion of each lesson.  These achievements are displayed on their profile to other users. Users can return to any lesson whenever they please, allowing them to refresh their memory after some time off. Sign up to get started today!</div>
            </div> 
        </section>
        <section id="signUpSection">
            <h3 class="signUpSectionTitle">Ready to launch your coding career? Sign up today and get going!</h3>
        </section>
        <section id="contributeSection">
            <h3 class="hidden">Contribute</h3>
            <div id="contributeParagraph" class="text-center overflow-hidden">
                <p class="three-quarter-width inline-block">The founders here at CodeIT are dedicated to learning. In today's world, it goes unnoticed how much code affects everyday life. From the alarm on a cellphone that wakes you up every morning, to the website you are viewing right now, to the subtitles on the TV in the restaurant that you're eating dinner at. Surrounding you right now is countless opportunities - all available to you with the knowledge of code. That's why we, as the founders of CodeIT, feel that it is important for the generations of tomorrow to learn the language of programming. CodeIT is the vessel for that education. We hope that you are able to take what you have learned from us and use it to better your future and the future of those surrounding you.</p>
                <img src="images/sign.png" alt="Founders Signature" />
            </div>
        </section>
        <section id="reviewSection" class="half-height">
        </section>
    </main>
    <?php require("includes/footer.php")?>
    <script>
        function adjustPage(){
            var maxHeight = -1;
            $('.learnBox').each(function() {
                $(this).height("auto");
                maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
            });
            $('.learnBox').each(function() {
                $(this).height(maxHeight);
            });
        }
        $(window).resize(function(){
            adjustPage();
        });
        $(window).bind("load",function(){
            // Scripts to run BEFORE page is completely loaded.


        });
        $(document).ready(function(){
            // Scripts to run AFTER page is completely loaded.
            var parallax = $("body").parallax();
            adjustPage();
            $("#learn-button").click(function(){
                var to_elem = $("#learnSection");
                var offset = to_elem.offset().top;
                offset -= 60;
                $('html, body').stop().animate({'scrollTop': offset+"px"}, 700);
            });
        });
    </script>

</body>
</html>
