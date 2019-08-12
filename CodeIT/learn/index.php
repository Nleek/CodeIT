<?php require("../includes/includes.php"); ?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <?php require("../includes/head.php"); ?>
        <title>Learn | CodeIT</title>

        <style>
            .sub-header-spacer{
                height: 60px;
            }
            #courses{
                background-color: #f0f0f0;
                color: #0f0f0f;
                text-align: center;
                font-family: "Open Sans", SansSerif, Arial;
                margin-bottom: 140px;
            }
            #courses h2{
                font-family: "Raleway", Arial;
                font-weight: 300;
            }
            .course-container{
                position: relative;
            }
            .course{
                display: inline-block;
                padding-top: 20px;
                padding-bottom: 20px;
                margin-bottom: 20px;
                position: relative;
            }
            .course figure{
                margin: 0;
                height: 250px;
                overflow: hidden;
            }
            .course figure img{
                margin: 0 auto;
                width: 200px;
                border: none;
                display: block;
            }
            .course figure figcaption{
                background-color: #fff;
                display: block;
                width: 200px;
                margin: 0 auto;
            }
            .course:hover:not(.course-active){
                -webkit-transform: translateY(-7px);
                transform: translateY(-7px);
            }
            .course-content{
                text-align: left;
                display: none;
            }
            .course-content ul{
                margin:5px;
                padding:0;
                border: 1px solid #ddd;
                border-left: 3px solid #ddd;
            }
            .course-content ul ul{
                padding: 10px;
            }
            .course-content ul li{
                padding: 10px;
                background-color: #fefefe;
                list-style-type: none;
            }
            .course-content ul li li{
                padding: 0;
                border-bottom: 1px solid #ddd;
            }
            .course-content ul li li:first-child{
                border-top: 1px solid #ddd;
            }
            .course-content a{
                color: #0f0f0f;
                display: block;
                padding: 10px;
                text-decoration: none;
            }
            .course-content a:hover{
                background-color: #fff;
                border-left: 3px solid #2acb9a;
            }
            .course-content a:hover .hover-text-decoration, .editable.hover-text-decoration:hover{
                text-decoration: underline;
            }
            .course-active{
                display: block !important;
                width: calc(100% - 10px);
                margin: 0 auto 20px;
                overflow: hidden;
                background-color: #fff;
                -webkit-box-shadow: 0 3px 10px 0 rgba(208, 218, 225, 0.59);
                box-shadow: 0 3px 10px 0 rgba(208, 218, 225, 0.59);
            }
            .course-active:hover{
                cursor: auto !important;
            }
            .course-active figure{
                display: block;
                width: 100%;
                height: auto;
            }
            .course-active figure img{
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
            }
            .course-active figure figcaption{
                display: none;
            }
            .course-active .course-content{
                display: block;
                width: 90%;
                margin: 0 auto;
            }
            .complete{
                position: absolute;
                top: 5px;
                right: -15px;
                height: 50px;
                width: 36px;
                padding-left: 14px;
                text-align: center;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
            }
            .course-active .complete{
                right: 5px;
            }

            .edit{
                position: absolute;
                top: 5px;
                left: -15px;
                height: 50px;
                width: 36px;
                padding-left: 14px;
                text-align: center;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                background-color: #fff;
                border: none;
                color: #0f0f0f;
            }
            .edit:hover{
                background-color: rgb(199, 200, 209);
                color: #fff;
            }

            .course-active .edit{
                left: 5px;
            }

            #splash-page{
                background-color: #fff;
                text-align: center;
                font-family: "Open Sans", SansSerif;
            }
            #splash-page h2{
                font-family: "Raleway", Arial;
                font-weight: 300;
            }
            #add{
                position: absolute;
                bottom: 10px;
                right: 10px;
                -webkit-box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
                box-shadow: 0 3px 10px 0 rgba(151,161,168,0.59) ;
            }
            .form-error{
                border-color: red;
            }
            .editable{
                border: 1px dashed #ccc !important;
            }
        </style>
    </head>
    <body>
        <?php require("../includes/header.php"); ?>
        <div class="sub-header-spacer"></div>
        <main id="v-root">
            <section id="courses" v-if="courses.length > 0">
                <div class="center-wrapper overflow-hidden">
                    <h2>Courses</h2>
                    <hr class="modern-line" />
                    <div class="course-container">
                        <article :id="course[0][4]" v-for="course in courses" v-on:click="click" class="course cursor transition">
                            <?php
                            if(check_perms(10)){
                                echo"
                                    <div class='edit cursor material-icon-container circle white-background' onclick='edit.call($(this))'><i class='material-icons'>edit</i></div>
                                ";
                            }
                            ?>
                            <div v-if="course[2][0] == course[2][1]" class="complete material-icon-container circle highlight"><i class="material-icons">check</i></div>
                            <figure>
                                <img :src="course[0][1]" :alt="course[0][0]" />
                                <figcaption class="padding-top padding-bottom new-transition">
                                    {{ course[0][2] }} <span v-if="course[3] == 0">- Unpublished</span>
                                </figcaption>
                            </figure>
                            <div class="course-content">
                                <h4 class="admin-edit" data-edit="course_title" :data-edit-id="course[0][4]">{{ course[0][2] }}</h4>
                                <hr class="modern-line" />
                                <p class="admin-edit" data-edit="course_description" :data-edit-id="course[0][4]">{{ course[0][3] }}</p>
                                <ul class="admin-add" data-add="lesson" :data-add-id="course[0][4]">
                                    <li v-for="lesson in course[1]"><span v-if="lesson[2][0] == lesson[2][1]" class="material-icon-container green-text padding-right"><i class="material-icons">check</i></span><span class="admin-edit" data-edit="lesson_title" :data-edit-id="lesson[0][1]">{{ lesson[0][0] }}</span><span class="right">{{ lesson[2][0] }} / {{ lesson[2][1] }} Sections Completed</span>
                                        <ul class="admin-add" data-add="section" :data-add-id="lesson[0][1]">
                                            <li v-for="section in lesson[1]"><a :href="section[0][2]"><span v-if="section[1] == 1" class="material-icon-container green-text padding-right"><i class="material-icons">check</i></span><span class="hover-text-decoration admin-edit" data-edit="section_title" :data-edit-id="section[0][3]">{{ section[0][0] }} - {{ section[0][1] }}</span></a></li>
                                        </ul>
                                    </li>
                                </ul>
                                <div v-if="course[3] == 0" class="block text-center margin-top">
                                    <button :data-course="course[0][4]" class="modern-button highlight cursor padding" v-on:click="publish">Publish Course!</button>
                                </div>
                            </div>
                        </article>
                        <?php if(check_perms(10)){

                            echo "
                            <button id='add' class='circle padding highlight cursor' onclick='new_course()'><span class='material-icon-container padding'><i class='material-icons'>add</i></span></button>
                            
                            ";

                        }?>
                    </div>
                </div>
            </section>
            <section id="splash-page" v-if="courses.length == 0" class="hidden">
                <div class="center-wrapper overflow-hidden half-height">
                    <h2>Login to start your CodeIT journey</h2>
                    <hr class="modern-line" />
                    <p>
                        Our library of courses is always growing, dedicated coding masters work to create more every day.<br /><br />
                        When you login you will have free access to them, forever.<br /><br />
                        Learn how to code from industry experts and start your CodeIT journey here, so what are you waiting for?
                    </p>
                    <button class="modern-button cursor highlight padding margin-top" onclick="loginModal.open()">Get Started Now</button>
                </div>
            </section>
        </main>
        <?php require("../includes/footer.php")?>
        <?php
            if(check_perms(10)){

                echo"
                    <script>
                        var mainCourseModal = new Modal({
                                icon: false,
                                headerClass: 'padding',
                                headerContent: \"<h3>Create A New Course</h3><label for='new_course_name' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                                \"<input type='text' id='new_course_name' placeholder='Course Name' class='modern-input padding'></input><span>Course Name:</span>\" +
                                \"</div></label><label for='new_course_description' class='modern-label'><div class='inline-block full-width relative margin-center'>\" +
                                \"<input type='text' id='new_course_description' placeholder='Briefly Describe The Course Goals.' class='modern-input padding'></input><span>Briefly Describe The Course Goals:</span>\" +
                                \"</div></label>\",
                                bodyButtons: [
                                    [\"Next\", \"modern-button margin-left white-text white-border margin-bottom margin-top transition cursor one-third-width\", 'new_course_lessons()']
                                ]
                        });
                        var lessonsModal = new Modal({
                                icon: false,
                                headerClass: 'padding',
                                headerContent: \"<h3>Add Lessons</h3>\",
                                bodyButtons: [
                                    [\"Next\", \"modern-button margin-left white-text white-border margin-bottom margin-top transition cursor one-third-width\", 'new_course_lessons()']
                                ]
                        });
                        var editModal = new Modal();
                        function publish(ucid){
                            $.post('../php/courses.php',{'method':'publish','ucid':ucid},function(response){
                                if(response == 'published '+ucid){
                                    update_models();
                                }
                                else{
                                    console.log(response);
                                }
                            });
                        }
                        function open_edit(){
                            
                            var me = this;
                            
                            this.addClass('highlight');
                            
                            var course = this.closest('.course');
                            
                            course.find('a').each(function(){
                            
                                var href = $(this).attr('href');
                                $(this).addClass('cursor');
                                $(this).data('href', href).removeAttr('href');
                            
                            });
                            
                            course.find('.admin-edit').each(function(){
                                $(this).click(function(){
                                
                                    var edit_uid = $(this).data('edit-id');
                                    var edit_type = $(this).data('edit');
                                    var pretty_type = edit_type.replace('_', ' ');
                                    
                                    var content = $(this).html();
                                    if(content.indexOf(' - ') > -1){
                                        content = content.substring(0,content.indexOf(' - '));
                                    }
                                    
                                    editModal.settings.icon = false;
                                    editModal.settings.headerClass = 'padding',
                                    editModal.settings.headerContent = '<h3>Edit '+pretty_type+'</h3>',
                                    editModal.settings.bodyClass = 'padding',
                                    editModal.settings.bodyContent = '<label for=\"'+edit_type+'\" class=\"modern-label\"><div class=\"inline-block full-width relative margin-center\">' +
                                    '<input type=\"text\" id=\"'+edit_type+'\" placeholder=\"'+pretty_type+'\" value=\"'+content+'\" class=\"modern-input padding\"></input><span>'+pretty_type+'</span></div>',
                                    editModal.settings.bodyButtons =[
                                        [\"Save\", \"modern-button margin-left black-text margin-bottom margin-top transition cursor one-third-width\", 'admin_edit_save(\"'+edit_type+'\",\"'+edit_uid+'\")']
                                    ]
                                    editModal.open();
                                    
                                });
                                $(this).addClass('cursor editable hover-text-decoration');
                            });
                            
                            course.find('.admin-add').each(function(){
                                var add_type = $(this).data('add');
                                var add_id = $(this).data('add-id');
                                $(this).append(\"<li class='add-event'><a class='cursor hover-text-decoration'>Add \"+add_type+\"</a></li>\");
                                $(this).find(\".add-event\").click(function(){
                                    $.post('../php/new-functions.php',{'action':add_type},function(response) {
                                        console.log('new -> '+response);
                                        if(response){
                                            if(add_type == 'section'){
                                                $.post('../php/courses.php',{'method':'add_section_to_lesson','usid':response,'ulid':add_id},function(add_response){
                                                    if(add_response == 'Updated.'){
                                                        close_edit.call(me);
                                                        setTimeout(function(){open_edit.call(me)},400);
                                                        update_models();
                                                        window.open('../code/edit.php?action=new&type=section&usid='+response);
                                                    }
                                                    else{
                                                        console.log(add_response);
                                                    }
                                                });
                                            }
                                            if(add_type == 'lesson'){
                                                $.post('../php/courses.php',{'method':'add_lesson_to_course','ulid':response,'ucid':add_id},function(add_response){
                                                    if(add_response == 'Updated.'){
                                                        close_edit.call(me);
                                                        setTimeout(function(){open_edit.call(me)},400);
                                                        update_models();
                                                    }
                                                    else{
                                                        console.log(add_response);
                                                    }
                                                });
                                            }
                                        }
                                    });
                                });
                            });
                        }
                        function close_edit(){
                            this.removeClass('highlight');
                        
                            var course = this.closest('.course');
                            
                            course.find('.admin-edit').each(function(){
                                $(this).unbind('click');
                                $(this).removeClass('cursor editable hover-text-decoration');
                            });
                            
                            course.find('a').each(function(){
                               
                                var href = $(this).data('href');
                                $(this).removeClass('cursor');
                                $(this).addClass('hover-text-decoration');
                                $(this).attr('href', href);
                            
                            });    
                            
                            $('.add-event').each(function(){
                                $(this).unbind('click');
                                $(this).remove();
                            });
                        }
                        function edit(){
                        
                            if(this.hasClass('highlight')){
                            
                                close_edit.call(this);
                                
                            }
                            else{
                                
                                open_edit.call(this);
                                
                            }
                        
                        }
                        function admin_edit_save(element, uid){
                            var value = $('#'+element).val();
                            console.log('Saving: '+value + '| element: '+element);
                            $.post('../php/courses.php',{'method':'save','property':element,'value':value,'uid':uid},function(response){
                                if(response == 'Updated.'){
                                    update_models();
                                    editModal.close();
                                }
                                else{
                                    console.log(response);
                                }
                            });
                        }
                        function new_course(){
                        
                            $.post('../php/new-functions.php',{'action':'course'},function(){
                                update_models();
                            });
                        
                        }
                    </script>
                ";

            }
        ?>
        <script>
            var learn = new Vue({

                el: "#v-root",

                data: {

                    courses: []

                },
                methods:{

                    click: function(e){

                        $(".course-active").each(function(){$(this).removeClass("course-active");});
                        $(e.target).closest(".course").addClass("course-active");

                    },
                    publish: function(e){

                        var ucid = $(e.target).data("course");
                        publish(ucid);

                    }

                },
                mounted: function(){

                    setTimeout(function(){
                        $("#splash-page").removeClass("hidden");
                    },500)

                }

            });

            function update_models(){

                $.post("../php/courses.php",{"method":"list"},function(response){


                    if(response != "You must supply a UUID or be logged in to do that."){
                        try{
                            learn.courses = JSON.parse(response);
                        }
                        catch(err){
                            console.log(err + " | "+ response );
                        }
                    }
                    else{
                        loginModal.settings.headerContent = "<h3>Please Login or Register<br /> to view our courses</h3><div id='login-message' class='padding hidden'></div>";
                    }


                });

            }

            $(document).ready(function(){

                update_models();

            });
        </script>
    </body>
</html>