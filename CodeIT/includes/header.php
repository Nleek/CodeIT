<script>
    $(window).scroll(function(){
        var scrollPos = window.pageYOffset || document.documentElement.scrollTop;
        if(scrollPos > 80){
            $("header").addClass("header-scrolled");
            $("#logo").find("img").attr("src","<?php echo$base_url;?>/images/CodeITBlue.png");
        }
        else{
            $("header").removeClass("header-scrolled");
            $("#logo").find("img").attr("src","<?php echo$base_url;?>/images/CodeIT.png");
        }
    });
</script>

<div id="modal-template" class="">
    <div class="modal hidden">
        <div class="modal-overlay hidden"></div>
        <div class="modal-container">
            <button class="modal-close-button hidden">&times;</button>
            <div class="modal-header text-center padding-top">
                <span class="material-icon-container padding hidden"><i class="material-icons"></i></span>
                <div class="modal-header-content overflow-hidden"></div>
            </div>
            <div class="modal-body">
                <div class="modal-content"></div>
                <div class="modal-buttons hidden"></div>
            </div>
        </div>
    </div>
</div>
<header class="transition">
    <div id="mobile-nav-button" data-open="false">
        <ul class="list transition">
            <li class="material-icon-container"><i class="material-icons margin-left cursor">menu</i></li>
        </ul>
    </div>
    <div id="logo" class="left"><img onclick="window.location.href='<?php echo$base_url;?>'" src="<?php echo$base_url;?>/images/CodeIT.png" alt="CodeIT logo" id="logoImage"/></div>
    <nav id='nav' class="right">
        <ul class="list transition">
            <li class="list-item margin-right"><a href="<?php echo$base_url;?>/">Home</a></li>
            <?php if(isset($_SESSION["uuid"])){
                echo "
                    <li class=\"list-item margin-right\"><a href=\"$base_url/learn\">Learn</a></li>
                    <li class=\"list-item margin-right\"><a href=\"$base_url/challenge\">Challenge</a></li>
                    <li class=\"list-item\"><a href=\"$base_url/connect\">Connect</a></li>
                    <li class=\"list-item margin-right\"></li>
                    <li class=\"material-icon-container\">
                        <i id=\"userAlerts\" class=\"material-icons cursor padding-right padding-left\" v-if='num_alerts!=0'>notifications</i>
                        <i id=\"userAlerts\" class=\"material-icons padding-right padding-left\" v-else>notifications_none</i>
                        <i id=\"userIcon\" class=\"material-icons cursor margin-left\">person</i>
                    </li>
                    ";
                }else{
                echo"
                    <li class=\"material-icon-container\">
                        <i id=\"userIcon\" class=\"material-icons cursor margin-left\">person</i>
                    </li>
                ";
            }?>
        </ul>
        <?php if(isset($_SESSION["uuid"])){
            echo"
                <div v-if='num_alerts!=0' id='alerts'>
                    <ul class='block'>
                        <li class='block new-transition' v-for='alert in alerts' :id='alert[3]'>
                            <img v-if=\"alert[2]!=''\" :src='alert[2]' :alt='alert[0]' />
                            <p>{{alert[0]}}<br /><br />{{alert[1]}}</p>
                            <a class='cursor' v-on:click='removeAlert'>&times;</a>
                        </li>
                    </ul>
                </div>
            ";
        }?>
    </nav>
    <div id="user" class="right">
        <?php
        if(!isset($_SESSION["uuid"])){
            echo"
                <script>
                    var loginModal = new Modal({
            
                        iconContainerClass: \"margin-top\",
                        iconText: \"person\",
                        headerContent: \"<h3>Login or Register</h3><div id='login-message' class='padding hidden'></div>\",
                        bodyClass: \"padding\",
                        bodyContent: \"\" +
                        \"<label for='UN' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                        \"<input type='text' id='UN' placeholder='Screen Name or Email' class='modern-input padding'></input><span>Screen Name or Email:</span>\" +
                        \"</div></label>\" +
                        \"<label for='PW' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                        \"<input type='password' id='PW' placeholder='Password' class='modern-input padding'></input><span>Password:</span>\" +
                        \"</div></label>\",
                        bodyButtons: [
                            [\"Get Coding!\", \"modern-button margin-bottom margin-top transition cursor highlight one-third-width\", 'login()'],
                            [\"Register\", \"modern-button margin-left margin-bottom margin-top transition cursor one-third-width black-text\", 'register()']]
            
                    });
                    var registerModal = new Modal({
                        
                        iconContainerClass: \"margin-top\",
                        iconText: \"person\",
                        headerContent: \"<h3>Welcome to CodeIT!</h3><p>We just need some basic information to create your account.</p><div id='register-message' class='padding hidden'></div>\",
                        bodyClass: \"padding\",
                        bodyContent: \"\" +
                        \"<label for='new_fn' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                        \"<input type='text' id='new_fn' placeholder='First Name' class='modern-input padding'></input><span>First Name:</span>\" +
                        \"</div></label>\" +
                        \"<label for='new_ln' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                        \"<input type='text' id='new_ln' placeholder='Last Name' class='modern-input padding'></input><span>Last Name:</span>\" +
                        \"</div></label>\" +
                        \"<label for='new_sn' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                        \"<input type='text' id='new_sn' placeholder='Screen Name' class='modern-input padding'></input><span>Screen Name:</span>\" +
                        \"</div></label>\" +
                        \"<label for='new_em' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                        \"<input type='email' id='new_em' placeholder='Email' class='modern-input padding'></input><span>Email:</span>\" +
                        \"</div></label>\" +
                        \"<label for='new_pw' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                        \"<input type='password' id='new_pw' placeholder='Password' class='modern-input padding'></input><span>New Password:</span>\" +
                        \"</div></label>\",
                        bodyButtons: [
                            [\"Register\", \"modern-button margin-left highlight margin-bottom margin-top transition cursor one-third-width\", 'register_user()']]
            
                    
                    });
            
                    $(document).ready(function(){
            
                        $(\"#userIcon\").click(function(){
            
                            loginModal.open();
            
                        });
            
                    });
                    
                    function login(){
                        var UN = $('#UN').val(); //Grab the typed username.
                        var PW = $('#PW').val(); //Grab the typed password.
                        var PWHash = CryptoJS.SHA512(PW); //Iterate SHA512 Once..
                        PWHash = PWHash.toString();
                        $.post('$base_url/php/login.php', { 'un': UN, 'pw': PWHash }, function(response) {//Post the data to the server for authentication
                            $('#login-message').html(response).removeClass('hidden');
                            if(response=='Logged In Successfully!'){ //If logged in successful..
                                setTimeout(function(){window.location.href='$base_url/profile';},500);//Refresh the page to get all user data.
                            }
                        });
                    }
                    function register(){
                        
                        loginModal.close();
                        registerModal.open();
                    
                    }
                    function register_user(){
                    
                        var fn = $('#new_fn').val();
                        var ln = $('#new_ln').val();
                        var sn = $('#new_sn').val();
                        var em = $('#new_em').val();
                        var pw =  CryptoJS.SHA512($('#new_pw').val()).toString();
            
                        $.post(\"$base_url/php/new-functions.php\",{\"action\":\"user\",\"fn\":fn,\"ln\":ln,\"sn\":sn,\"em\":em,\"pw\":pw},function(response){
            
                            var failed_responses = ['Failed to create user trackers','Failed to create new user - 2', 'Failed to create new user - 1'];
                            var error_responses = ['Sorry, that email is already registered.','Oops, looks like that Screen Name is already taken.','Insufficient Data.'];
                            if(failed_responses.indexOf(response) > -1){
                                console.log(response);
                            }
                            else if(error_responses.indexOf(response) > -1){
                                $('#register-message').html(response).removeClass('hidden');
                            }
                            else{
                                $('#register-message').html('Account Created Successfully!').removeClass('hidden');
                                setTimeout(function(){
                                    registerModal.close();
                                    loginModal.open();
                                },700);
                            }
                
                        });
                    
                    }
            
                </script>
            ";
        }
        else{
            $name = $_SESSION["fn"];
            echo"
           <script>
                    var profileModal = new Modal({
            
                        iconContainerClass: \"margin-top circle white-border\",
                        iconText: \"person\",
                        headerClass: \"highlight\",
                        headerContent: \" <h3>Hi there, $name</h3 >\",
                        bodyClass: \"\",
                        bodyContent: \"\",
                        bodyButtons: [
                            [\"View Account\", \"modern-button margin-left margin-bottom margin-top transition cursor one-third-width black-text\", 'to_profile()'],
                            [\"Log out\", \"modern-button margin-left margin-bottom margin-top transition cursor one-third-width black-text\", 'logout()']
                        ]
            
                    });
            
                    var notifications = new Vue({
                        
                        el: '#nav',
                        
                        data:{
                        
                            num_alerts: 0,
                            alerts:[]
                        
                        },
                        
                        methods:{
                            removeAlert: function(e){
                    
                                var id = $(e.target).parent().attr('id');
                                id = id.replace('alert-','');
                                
                                this.alerts.splice(Number(id), 1);
                                
                                this.num_alerts-=1;
                                
                                for(var alert in this.alerts){
                                
                                    this.alerts[alert][3] = 'alert-'+alert;
                                    
                                }
                                
                            }
                        }
                        
                    });
            
                    function newAlert(data){
                    
                        var title = data[0];
                        var description = data[1];
                        var image = data[2];
                        
                        if(image != ''){
                            image = '$base_url/'+image;
                        }
                        
                        var alert = [title, description, image, 'alert-'+notifications.num_alerts];
                        
                        notifications.alerts.push(alert);
                        notifications.num_alerts+=1;
                    
                    }
            
                    function getNotifications(){
                    
                        $.post('$base_url/php/notifications_endpoint.php',{'method':'get'},function(response){
                        
                            response = JSON.parse(response);
                            if(response != []){
                            
                                for(var x in response){
                                
                                    if(response[x][0].indexOf('Accepted Your Friend Request!') > -1){
                                    
                                        getAchievement('friends made');
                                    
                                    }
                                
                                    newAlert([response[x][0],response[x][1],response[x][2]]);
                                    
                                    $.post('$base_url/php/notifications_endpoint.php',{'method':'remove','description':response[x][1]});
                                
                                }
                            
                            }
                        
                        });
                    
                    }
                    function notificationLoop(){
                    
                        getNotifications();
                        setTimeout(function(){
                            notificationLoop();
                        }, 5000);
                    
                    }
                    
                    $(document).ready(function(){
            
                        $(\"#userIcon\").click(function(){
            
                            profileModal.open();
            
                        });
                        notificationLoop();

                    });
                    
                    function to_profile (){
                    
                        window.location.href = \"$base_url/profile\";
                    
                    }
                    function logout(){
                    
                        window.location.href = \"$base_url/php/logout.php\";
                    
                    }
                    
                    $(document).ready(function(){
                    
                        /*  
                            
                            This looks at the title of the current 
                            page and at each of the page links and
                            selects the tab that contains the string
                            before the | in the title.
                            
                         */
                        
                        var title = $('title').text();
                        var page_name = title.substring(0, title.indexOf('|')).toLowerCase().trim();
                        $('nav').find('.list-item').each(function(){
                        
                            var tab = $(this).text().toLowerCase().trim();
                            if(tab.indexOf(page_name) > -1){
                                
                                $(this).addClass('current-page');
                                
                            }
                        
                        });
                    
                    });

            </script >
            ";
        }
        ?>
    </div>
    <div id="mobile-nav" class="hidden transition">
        <nav>
            <ul class="list">
                <li class="list-item transition"><a class="padding" href="<?php echo$base_url;?>/">Home</a></li>
                <li class="list-item transition"><a class="padding" href="<?php echo$base_url;?>/learn">Learn</a></li>
                <li class="list-item transition"><a class="padding" href="<?php echo$base_url;?>/challenge">Challenge</a></li>
                <li class="list-item transition"><a class="padding" href="<?php echo$base_url;?>/connect">Connect</a></li>
            </ul>
        </nav>
    </div>
</header>
<div class="header-spacer"></div>
