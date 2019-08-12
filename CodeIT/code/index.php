<?php require("../includes/includes.php"); ?>
<!DOCTYPE html>

<html lang="en">
<head>
    <?php require("../includes/head.php"); ?>
    <script src="../js/ace-editor/ace.js" type="text/javascript" charset="utf-8"></script>
    <title>DIY Code | CodeIT</title>
    <style>
        header{
            border: none;
            position: absolute;
        }
        #code-block{
            position: relative;
            background-color: #1D1F21;
            overflow: hidden;
        }
        #code-description{
            background-color: #e5e5e5;
            overflow: auto;
            height: calc(100vh - 80px);
        }
        #code-description p{
            color: #585858;
        }
        #code-description h2, h4{
            color: rgba(0, 0, 0, 0.8);
        }
        #code-description .keyword{
            display: inline-block;
            padding: 0 4px;
            background-color: #d7d7d7;
            border: 1px solid #c5c5c5;
            /*color: rgb(2, 167, 191);*/
        }
        #code-editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 75px;
            left: 0;
        }
        #code-menu{
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 75px;
            line-height: 75px;
        }
        #code-results{
            position: absolute;
            top:60%;
            left: 0;
            right: 0;
            bottom: 0;
            overflow-x: hidden;
            overflow-y: auto;
            background-color: #1D1F21;
            font-family: "Open Sans", sans-serif;
            font-weight: 300;
            display: none;
        }
        .code-results{
            overflow-x: hidden;
            overflow-y: auto;
            background-color: #1D1F21;
            font-family: "Open Sans", sans-serif;
            font-weight: 300;
        }
        #code-results li, .code-results li{
            background-color: #25282c;
            color: #f0f0f0;
        }
        #code-results li p, .code-results li p{
            overflow: hidden;
            border-color: inherit;
        }
        .test-pass{
            color: #2acb9a !important;
            border-color: #2acb9a !important;
        }
        .test-fail{
            color: #cb635f !important;
            border-color: #cb635f !important;
        }
        main{
            position: relative;
            height: 100%;
            font-family: "Open Sans", sans-serif;
            overflow: hidden;
        }
        .ace_editor{
            z-index: 0;
        }
        #all-pass{
            float: left;
        }
        #fail{
            float: left;
            overflow: hidden;
            display: none;
            white-space: nowrap;
        }
        #code-actions{
            overflow-y: hidden;
            overflow-x: auto;
            white-space: nowrap;
            height: 75px;
            line-height:75px;
        }
        .modern-button{
            padding:5px 15px;
            background-color: transparent;
            font-family: "SFUltraLight", "Open Sans", sans-serif;
            display: inline-block;
            font-size: 1em;
            border: 1px solid #fff;
            border-radius: 3px;
            color: #fff;
        }
        .modern-button.cursor:not(.highlight):hover{
            background-color: #fff;
            color: #0f0f0f;
        }
        .code-example{
            white-space: pre;
            background-color: #1D1F21;
            overflow: hidden;
            display: block;
            position: relative;
        }
        .code-example .ace_gutter, .code-example .ace_content{
            padding-top: 10px;
        }
        .edit{
            position: absolute;
            bottom: 15px;
            right: 15px;
            height: 50px;
            width: 36px;
            padding-left: 14px;
            text-align: center;
            -webkit-box-shadow: 0 3px 10px 0 rgba(0, 0, 0, 0.49);
            box-shadow: 0 3px 10px 0 rgba(0, 0, 0, 0.49);
            background-color: transparent;
            border: none;
            color: #fff;
        }
        .edit:hover{
            background-color: rgb(199, 200, 209);
            color: #fff;
        }
        @media screen and (max-width: 1000px) and (min-width: 621px){
            #code-description{
                width: 45% !important;
            }
            #code-block{
                width: 55% !important;
            }
        }
        @media screen and (max-width: 620px){
            #code-description, #code-block{
                float: none;
            }
            #code-description{
                width: 100% !important;
                height: auto !important;
            }
            #code-block{
                width: 100% !important;
            }
        }
    </style>
</head>

<body>

<?php require("../includes/header.php"); ?>

<main>
    <section id="code-description" class="one-third-width left">
        <div class="padding-left padding-right"></div>
    </section>
    <section id="code-block" class="two-third-width full-height left">
        <div id="original-code" class="hidden"></div>
        <div id="code-editor" class="full-width"></div>
        <div id="code-menu" class="full-width">
            <div id="all-pass" class="hidden material-icon-container margin-left test-pass"><i class="material-icons margin-left margin-right">check</i><span class="padding-right">Great Work!</span><button id="success-action" class="modern-button cursor highlight transition margin-left right">Next Section</button></burron></div>
            <div id="code-actions" class="padding-left">
                <div id="fail" class="test-fail"><span class="material-icon-container"><i class="material-icons margin-left margin-right">close</i><span class="padding-right">One or more tests failed.</span></span></div>
                <button id="test" class="modern-button cursor margin-left highlight transition">Submit Code</button>
                <button id="reset" class="modern-button cursor margin-left transition">Reset Code</button>
            </div>
        </div>
        <div id="code-results" class="hidden">
            <ul id="tests" class="list">
                <li class="block-list-item padding margin-bottom hidden test-fail" id="code-error">
                    <p></p>
                </li>
                <li class="block-list-item padding-top margin-bottom test hidden" id="test-template">
                    <p class="block-list-header cursor" data-state="open">
                        <span class="left inline-block">
                            Test <span class="test-number"></span>
                        </span>
                        <span class="right"><i class="material-icons">remove</i></span>
                    </p>
                    <span class="block padding test-results">
                        <span class="result-container block padding-left margin-top">When passed <span class="test-input"></span> as parameters,</span>
                        <span class="result-container block padding-left">your function returned <span class="test-result"></span>.</span>
                        <span class="expected-container block padding-left">To pass the test, it should have returned <span class="test-expected"></span>.</span>
                    </span>
                </li>
            </ul>
        </div>
    </section>
    <script class="execution-template"></script>
    <div id="execution-block"></div>
    <script>
        function get_content_id(type){
            var content_id;

            switch(type){
                case "course":
                    content_id = getUrl("section");
                    break;
                case "challenge":
                    content_id = getUrl("challenge");
                    break;
                default:
                    console.log("supplied type not recognised")
                    break;
            }
            return content_id;
        }

        function arrayOfStringsToInts(array){

            for(var idx in array){

                var element = array[idx];
                if(typeof element === 'string'){
                    if(!isNaN(element)){
                        // Check if the string can be represented as a number.
                        array[idx] = Number(element);
                    }
                }
                else{
                    array[idx] = arrayOfStringsToInts(element);
                }


            }

            return array;

        }

        function setup(){
            var type = getUrl("type");
            if(type){

                var content_id = get_content_id(type);

                if(content_id){

                    $.post("../php/code.php",{"method":"load","type":type,"content_id":content_id},function(response){

                        if(response && response != "You must supply a UUID or be logged in to do that."){

                            try{
                                var content = JSON.parse(response);

                                $("#code-description").find("div").html(content[0].replace(/\\n/g, "\n").replace(/\\'/g, "'").replace(/\\"/g, "\""));
                                $("#original-code").html(content[1][1].replace(/\\n/g, "\n").replace(/\\'/g, "'").replace(/\\"/g, "\""));
                                $("#code-editor").html(content[1][0].replace(/\\n/g, "\n").replace(/\\'/g, "'").replace(/\\"/g, "\""));
                                $("#test").data("code_block", content[1][4]);

                                var tests = [JSON.parse(content[1][2]),JSON.parse((content[1][3]))];
                                tests = arrayOfStringsToInts(tests);

                                setup_tests(tests[0]);
                                after_content_loaded(content[1][5],tests);

                                if(type == "challenge"){

                                    if(content[2] == "<?php echo get_uuid();?>"){
                                        $("main").append("<div class='edit circle highlight cursor material-icon-container' onclick='window.location.href=\"edit.php?action=edit&type=challenge&ucgid=\"+get_content_id(\"challenge\")'><i class='material-icons'>edit</i></div>");
                                    }

                                }
                                else if(<?php echo (check_perms(10)) ? 1 : 0;?>){
                                    $("main").append("<div class='edit circle highlight cursor material-icon-container' onclick='window.location.href=\"edit.php?action=edit&type=\"+((getUrl(\"type\") == \"course\") ? \"section\" : \"challenge\" )+\"&\"+((getUrl(\"type\") == \"course\") ? \"usid\" : \"ucgid\")+\"=\"+get_content_id(getUrl(\"type\"))'><i class='material-icons'>edit</i></div>");
                                }

                            }
                            catch(err){
                                console.log(err + " | " + response);
                            }

                        }
                        else{

                            window.location.href="<?php echo$base_url;?>";

                        }


                    });

                }
            }
        }
        function next_section(){

            var type = getUrl("type");
            if(type == "course"){
                $.post("../php/code.php",{"method":"get_next_section","usid":getUrl("section")},function(response){

                    if(response && response != "Done."){

                        var prevURL = window.location.pathname; //Get the current URL from the window.
                        window.history.pushState("object or string", "Title", prevURL+"?type=course&section="+response);
                        window.location.reload();

                    }
                    else if(response == "Done."){
                        window.location.href = '../learn';
                    }

                });
            }
            else if(type == "challenge"){

                window.location.href = "<?php echo $base_url;?>/challenge";
            }

        }
        function arrayToArrayString(array){
            // This function is used for converting an array to a string that represents the array.

            console.log(array);

            for(var idx in array){

                // Loop over every element in the array

                var element = array[idx];
                console.log("element "+element);
                if(element.constructor === Array){ // check if the current element is an array.

                    for(var cidx in element){ // if it is, loop through each of its child elements.

                        var child_elem = element[cidx];
                        if(child_elem.constructor === Array){ // check if the child is an array to.

                            array[idx] = arrayToArrayString(element); // if it is recursively call this function.

                        }

                    }
                    array[idx] = "["+element.join(", ")+"]";

                }
                else{
                    array[idx] = "'"+element+"'";
                }

            }
            return array;

        }
        function run_tests(function_name, tests){

            var allPass = true;

            for(var test_index = 0; test_index < tests.length; test_index++) {

                var test = tests[test_index];

                //var input_values = test[0].constructor == Array ? test[0].join(", ") : test[0];

                var input_values = "";

                console.log(test[0]);
                if(test[0].constructor == Array){

                    test[0] = arrayToArrayString(test[0]);

                }

                input_values = test[0];

                console.log(input_values);

                var ce = $("#code-error");
                var test_element = $("#test-"+test_index);

                try{
                    var result = eval(function_name + "(" + String(input_values) + ")");
                    ce.slideUp("fast");
                    if(result == test[1]){
                        test_element.removeClass("test-fail").addClass("test-pass");
                        test_element.find(".expected-container").html('This was the correct output.');
                        test_element.find("i").html("check");
                    }
                    else{
                        test_element.removeClass("test-pass").addClass("test-fail");
                        test_element.find("i").html("close");
                        allPass = false;
                    }

                    test_element.find(".test-input").html(input_values);
                    test_element.find(".test-result").html(result);
                    test_element.find(".test-expected").html(test[1]);
                }
                catch(error){
                    ce.find("p").html(error);
                    ce.slideDown("fast");
                    test_element.removeClass("test-pass").removeClass("test-fail");
                    test_element.find("i").html("remove");
                    allPass = false;
                }
            }

            return allPass;

        }
        function setup_tests(tests) {

            for (var test_index = 0; test_index < tests.length; test_index++) {

                var template = $("#test-template");
                var test_block = template.clone().attr("id", "test-" + test_index).appendTo("#tests");

                var test = tests[test_index];

                test_block.find(".test-number").html(test_index + 1);
                var input_values = test[0].constructor == Array ? test[0].join(", ") : test[0];
                test_block.find(".test-input").html(input_values);
                test_block.find(".test-expected").html(test[1]);
                test_block.removeClass("hidden");
            }

            $("#code-error").removeClass("hidden").slideUp(0);

        }

        function open_fail(failModal){
            $("#fail").animate({"width":"toggle"});

            failModal.open();

            $(".test").find(".block-list-header").click(function () {
                var me = $(this);
                if (me.data("state") == "open") {
                    me.parent().find(".test-results").slideUp('fast');
                    me.data("state", "closed");
                    me.find("i").html("expand_more");
                }
                else {
                    me.parent().find(".test-results").slideDown('fast');
                    me.data("state", "open");
                    me.find("i").html("remove");
                }
            });
        }

        function after_content_loaded(function_name, tests){

            var editor = ace.edit("code-editor");
            editor.setTheme("ace/theme/tomorrow_night");
            editor.setStyle("margin-top");
            editor.getSession().setMode("ace/mode/javascript");
            editor.setShowPrintMargin(false);

            var idx = 0;
            $(".code-example").each(function(){
                idx += 1;
                var id = "code-example-"+idx;
                var content = $(this).html().replace(/&nbsp;/g,"");
                $(this).html(content).attr("id", id);
                var example = ace.edit(id);
                example.setTheme("ace/theme/tomorrow_night");
                example.setReadOnly(true);
                example.setStyle("code-example padding-top");
                example.getSession().setMode("ace/mode/javascript");
                example.setHighlightActiveLine(false);
                var lines = example.session.getLength();
                $(this).css({"height":(lines*15)+"px","padding-top":"10px","padding-bottom":"15px"});
            });

            $("#test").click(function(){
                // Execute user-typed code.
                var code = editor.getValue();    // Grab the code

                // Save user code
                var code_block = $(this).data("code_block");
                if(code_block && code){

                    $.post("../php/code.php",{"method":"save", "code":code, "ucbid":code_block},function(response){

                        if(response != "Saved."){
                            console.log(response);
                        }

                    });

                }

                $("#execution-block").html("");  // Clear the execution block to remove old code.
                $(".execution-template").clone().prependTo("#execution-block"); // Clone and add the script tag.
                $("#execution-block").find(".execution-template").html(code);   // Add the code to the new script.

                var type = getUrl("type");

                if(type && type == "challenge"){
                    $.post("../php/code.php",{"method":"challenge_attempt","ucgid":get_content_id(type)},function(response){
                        console.log("Increment Challenge Progress -> "+response);
                    });
                }

                if(run_tests(function_name, tests[0])) { // The first parameter is the function name. The second is a list of the tests for the function.

                    if(run_tests(function_name, tests[1])){ // Runs the "hidden" tests.

                        $("#code-actions").fadeOut("fast",function(){
                            $("#all-pass").removeClass("hidden").cssanimate("fadeInLeft");
                        });

                        var button_text;
                        if(type == "course"){
                            button_text = "Next Section";
                        }
                        else if(type == "challenge"){
                            button_text = "Back To Challenges";
                        }

                        var allPassModal = new Modal({

                            bodyButtons: [[button_text, "modern-button margin-bottom margin-top transition cursor",'next_section()']]

                        });
                        allPassModal.open();


                        $("#success-action").html(button_text);

                        // Mark the current section as complete.
                        $.post("../php/code.php",{"method":"complete","type":type,"content_id":get_content_id(type)},function(complete_response){

                            if(complete_response != "Already Complete." && complete_response != "Error Updating."){

                                complete_response = JSON.parse(complete_response);

                                // Get Achievements.
                                var completed = complete_response["Completed"];
                                if(completed){

                                    for(var idx in completed) {

                                        var tag = "";

                                        switch (completed[idx]) {

                                            case "Section":
                                                tag = "sections completed";
                                                break;
                                            case "Lesson":
                                                tag = "lessons completed";
                                                break;
                                            case "Course":
                                                tag = "courses completed";
                                                break;
                                            case "Challenge":
                                                tag = "challenges completed";
                                                break;
                                            default:
                                                break;

                                        }

                                        if(tag != ""){

                                            getAchievement(tag);

                                        }

                                    }

                                }

                            }

                        });

                        var is_battle = getUrl("battle");
                        if(is_battle){

                            $.post('../php/challenges.php',{'method':'complete_battle','challenge':get_content_id(type)},function(response){

                                if(response == "Battle Complete."){

                                    getAchievement("battles completed");

                                }

                            });

                        }

                        $("#success-action").click(function(){
                            next_section();
                        });

                    }
                    else{

                        var failModal = new Modal({
                            iconContainerClass: " circle red-border red-text",
                            iconText: "close",
                            headerClass: "padding-left padding-right",
                            headerContent: "<h2>You're almost there!</h2>",
                            bodyClass: "padding-bottom",
                            bodyContent: "<hr class='modern-line' /><h3>Keep working, one or more hidden tests failed.</h3>",
                            bodyButtons: []
                        });

                        open_fail(failModal);

                    }

                }
                else{



                    var failModal = new Modal({
                        iconContainerClass: " circle red-border red-text",
                        iconText: "close",
                        headerContent: "<h2>Keep Working, One or more tests failed.</h2><p>View The Results Below.</p>",
                        bodyClass: "code-results",
                        bodyContent: $("#code-results").html(),
                        bodyButtons: []
                    });

                    open_fail(failModal);

                }
            });

            $("#reset").click(function(){
                editor.setValue($("#original-code").html());
            });

        }


        $(document).ready(function(){

           setup();

        });
    </script>
</main>


</body>
</html>
