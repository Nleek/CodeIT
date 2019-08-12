<?php require("../includes/includes.php");?>
<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/html">
    <head>
        <?php require("../includes/head.php"); ?>
        <script src="../js/ace-editor/ace.js" type="text/javascript" charset="utf-8"></script>
        <script src="http://cloud.tinymce.com/stable/tinymce.min.js?apiKey=4lx81j4uyzom6by09512kw3fqsrmp0bylzg0lvrcx037y35l"></script>
        <title>Edit Code | CodeIT</title>
        <style>
            header{
                border: none;
                position: absolute;
            }
            main{
                 background-color: #1D1F21;
                 overflow: hidden;
                font-family: "Open Sans", SansSerif;
            }
            main section{
                position: relative;
            }
            #editor{
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 75px;
            }
            #description-block{
                background-color: #e5e5e5;
                overflow: auto;
                height: calc(100vh - 80px);
            }
            #description-block p{
                color: #585858;
            }
            #description-block h2, h4{
                color: rgba(0, 0, 0, 0.8);
            }
            #code-block{
                overflow: auto;
                height: calc(100vh - 80px);
            }
            .code-example{
                white-space: pre;
                background-color: #1D1F21;
                overflow: hidden;
                display: block;
                position: relative;
                color: #fff;
                padding-top: 10px;
                padding-bottom: 10px;
                width: 100%;
            }
            #actions{
                position: absolute;
                left: 0;
                right: 0;
                bottom: 0;
                height: 75px;
                line-height: 80px;
                padding-left: 15px;
            }
            .test{
                background-color: #fff;
            }
            .form-error{
                border-bottom-color: red !important;
            }
        </style>
    </head>
    <body>
        <?php require("../includes/header.php"); ?>

        <main id="v-root">

            <section id="description-block" class="one-third-width left">
                <div>
                    <div id="name">
                        <h2 class="nameTitle margin-left margin-right">
                            <span class="cursor">Click me to edit the title</span>
                        </h2>
                    </div>
                    <div id="description" class="padding">
                        <div class="empty">
                            <p>
                                Click me to edit this content.
                                <br />
                                This is where you describe the premise of the code. You describe any background information needed
                                to give the user the tools they need to implement it correctly and pass all of your tests.
                            </p>
                        </div>
                        <hr class="modern-line" />
                        <h4>Your Task:</h4>
                        <div id="task">
                            <p>
                                Click me to edit this content.
                                <br />
                                Provide a detailed explanation of what the code is supposed to do. Include the number of parameters,
                                any examples, etc.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="padding text-center">
                    <label for="difficulty">
                        Choose an accurate difficulty level.
                        <select id="difficulty">
                            <option value="Easy" selected="selected">Easy</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Hard">Hard</option>
                            <option value="Nearly Impossible">Nearly Impossible</option>
                        </select>
                    </label>
                </div>
                <div id="tests" class="padding">
                    <hr class="modern-line" />
                    <h3>Tests:</h3>
                    <p>Tests are how we check to see if the user has implemented the code correctly. The more tests you add the better!</p>
                    <div v-for="(test, idx) in tests" class="padding margin test overflow-hidden" :data-test-idx="idx" data-is-hidden="false">
                        <div class="remove-test material-icon-container margin right cursor" onclick="removeTest.call($(this))"><i class="material-icons">delete</i></div>
                        <div class="test-data left">
                            <div>Parameters: <span v-for="parameter in test[0]">{{ parameter }},</span></div>
                            <div>Expected result: {{ test[1] }}</div>
                        </div>
                    </div>
                    <div v-for="(test, idx) in hiddenTests" class="padding margin test overflow-hidden" :data-test-idx="idx" data-is-hidden="true">
                        <div class="remove-test material-icon-container margin right cursor" onclick="removeTest.call($(this))"><i class="material-icons">delete</i></div>
                        <div class="test-data left">
                            <div>Parameters: <span v-for="parameter in test[0]">{{ test[0][parameter-1] }},</span></div>
                            <div>Expected result: {{ test[1] }}</div>
                            <span>Hidden</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-center">Add tests below</h4>
                        <label for="parameters" class="modern-label">
                            <div class="inline-block full-width relative margin-center">
                                <input type="text" id="parameters" placeholder="Comma separated list of parameters" class="modern-input padding" />
                                <span class="padding-left">Comma separated list of parameters</span>
                            </div>
                        </label>
                        <label for="result" class="modern-label">
                            <div class="inline-block full-width relative margin-center">
                                <input type="text" id="result" placeholder="The expected result" class="modern-input padding" />
                                <span class="padding-left">The expected result</span>
                            </div>
                        </label>
                        <button class="modern-button highlight padding block cursor" onclick="addTest()">Add Test.</button>
                        <label for="hidden" class="padding-left">Make test hidden?<input class="padding-left" type="checkbox" id="hidden" /></label>
                        <p>
                            It is recommended to create 1-3 hidden tests. Hidden tests are not visible to the user, and are used to further verify their code.
                        </p>
                    </div>
                </div>
            </section>
            <section id="code-block" class="two-third-width left">
                <div id="editor"></div>
                <div id="actions">
                    <button id="save" class="modern-button highlight cursor padding">Save</button>
                </div>
            </section>
        </main>
        <script>

            var codeBlock = new Vue({
                el: '#v-root',
                data:{
                    tests: [],
                    hiddenTests: [],
                    options: {},
                }
            });

            var errorModal = new Modal({
                iconContainerClass: " circle red-border red-text",
                iconText: "close",
                headerClass: "padding-left padding-right",
                headerContent: "<h2>Whoops something isnt right.</h2>",
                bodyClass: "padding-bottom",
                bodyContent: "",
                bodyButtons: []
            });

            tinymce.init({
                selector: '#name',
                inline: true,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code'
                ],
                toolbar: 'undo redo | styleselect | bold italic underline',
                content_css: '../css/main.css'
            });
            tinymce.init({
                selector: '#description',
                inline: true,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code'
                ],
                toolbar: 'undo redo | insert | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | codeexample',
                content_css: '../css/main.css',
                setup: function(editor){
                    function insertCodeExample(){

                        var id = $('.code-example').length;
                        var html = "<div id='code-example-"+id+"' class='code-example'>&nbsp;This will be formatted correctly when loaded by the user.</span></div>";

                        editor.insertContent(html);

                    }
                    editor.addButton('codeexample', {
                        icon: 'code',
                        tooltip: 'Insert a code example',
                        onclick: insertCodeExample
                    });
                }
            });

            function addTest(){

                var test = [[],null];

                var params = JSON.parse("["+$("#parameters").val().trim()+"]");
                params = params.length > 1 ? params : params[0];
                var result = JSON.parse("["+$("#result").val().trim()+"]");
                result = result.length > 1 ? result : result[0];
                var hidden = $("#hidden").is(':checked');

                console.log(params + result)

                if( !(params.length == 1 && params[0] == "" ) && result.length != 0){

                    test[0] = params;
                    test[1] = result;


                    if(hidden){
                        codeBlock.hiddenTests.push(test);
                    }
                    else{
                        codeBlock.tests.push(test);
                    }

                    $("#parameters, #result").val("");

                }
                else{

                    if(params.length == 1 && params[0] == ""){

                        $("#parameters").addClass("form-error");

                    }
                    if(result.length == 0){

                        $("#result").addClass("form-error");

                    }

                }

            }

            function removeTest(){

                var element = $(this).parent();
                var idx = element.data("test-idx");
                var isHidden = element.data("is-hidden");

                if(isHidden){
                    codeBlock.hiddenTests.splice(idx, 1);
                }
                else{
                    codeBlock.tests.splice(idx, 1);
                }

            }

            function get_description(){

                var title = tinyMCE.get('name').getContent();
                var description = tinyMCE.get('description').getContent();

                return title + description;

            }
            function get_code(editor){

                var code = editor.getValue();
                var test_object = {
                    "normal": codeBlock.tests,
                    "hidden": codeBlock.hiddenTests
                };

                return {
                    "code": code,
                    "tests": test_object
                }

            }

            function save(action, type, editor){

                var description = get_description();
                var code = get_code(editor);
                var difficulty = $("#difficulty").val();

                console.log(code);

                if(code["tests"]["normal"].length != 0 && code["tests"]["hidden"].length != 0){
                    if(type == "section"){

                        var usid = getUrl("usid");
                        if(usid){

                            $.post("../php/courses.php",{
                                "method":"save",
                                "property":"section_code_block",
                                "value":code["code"],
                                "uid":usid,
                                "tests":code["tests"]["normal"],
                                "hiddenTests":code["tests"]["hidden"]
                            },function(response){

                                console.log("Save CodeBlock -> "+response);
                                if(response == "Updated."){

                                    $.post("../php/courses.php",{
                                        "method":"save",
                                        "property":"section_description",
                                        "value":description,
                                        "uid":usid
                                    },function(response){

                                        console.log("Save Section Description -> "+response);

                                        if(response == "Updated."){

                                            $.post("../php/courses.php",{
                                                "method":"save",
                                                "property":"section_difficulty",
                                                "value":difficulty,
                                                "uid":usid
                                            },function(response){

                                                console.log("Save Section Difficulty -> "+response);
                                                if(response == "Updated."){
                                                    window.location.href="index.php?type=course&section="+usid;
                                                }

                                            });

                                        }

                                    });

                                }

                            });

                        }


                    }

                    else if(action == "new"){

                        if(type == "challenge"){

                            $.post("../php/new-functions.php",{
                                "action":"challenge",
                                "lang":codeBlock.options["lang"],
                                "name":tinyMCE.get('name').getContent(),
                                "desc":description,
                                "code":editor.getValue(),
                                "tests":code["tests"]["normal"],
                                "function_name":codeBlock.options["function_name"],
                                "hidden_tests":code["tests"]["hidden"],
                                "difficulty":difficulty
                            },function (response) {
                                console.log(response);
                            });

                        }

                    }
                    else if(action == "edit"){

                        if(type == "challenge"){

                            var ucgid = getUrl("ucgid");
                            $.post("../php/challenges.php",{
                                "method":"update",
                                "ucgid":ucgid,
                                "lang":codeBlock.options["lang"],
                                "name":$("#description").find("h2.margin-left.margin-right").html(),
                                "desc":description,
                                "code":editor.getValue(),
                                "tests":code["tests"]["normal"],
                                "function_name":codeBlock.options["function_name"],
                                "hidden_tests":code["tests"]["hidden"],
                                "difficulty":difficulty
                            },function (response) {
                                if(response == "Updated."){
                                    window.location.href="index.php?type=challenge&challenge="+ucgid;
                                }
                            });

                        }

                    }
                }
                else{

                    if(code["tests"]["normal"].length == 0 && code["tests"]["hidden"].length != 0){
                        errorModal.settings.bodyContent = "<h3>You need to specify at least one normal test.</h3>";
                    }
                    else if(code["tests"]["normal"].length != 0 && code["tests"]["hidden"].length == 0){
                        errorModal.settings.bodyContent = "<h3>You need to specify at least one hidden test.</h3>";
                    }
                    else{
                        errorModal.settings.bodyContent = "<h3>You need to specify at least one visible and one hidden test.</h3>";
                    }

                    errorModal.open();
                }

            }

            function loaded(){
                var editor = ace.edit("editor");
                editor.setTheme("ace/theme/tomorrow_night");
                editor.setStyle("margin-top");
                editor.getSession().setMode("ace/mode/javascript");
                editor.setShowPrintMargin(false);
                return editor;
            }

            $(document).ready(function(){

                var action = getUrl("action");
                var type = getUrl("type");

                console.log(action + type);

                if(action && type){

                    switch(action){

                        case "new":

                            $("#editor").html("function main(){}");
                            $("#description").prepend("<h3>Note: Don't change the function name 'main'! " +
                                "This is the function that will be called to execute your tests. Add any parameters needed " +
                                "and any code you want to start the user with.</h3>");

                            codeBlock.options["function_name"] = "main";
                            codeBlock.options["lang"] = "JavaScript";

                            editor = loaded();

                            break;


                        case "edit":

                            if(type == "challenge"){

                                var ucgid = getUrl("ucgid");
                                if(ucgid){

                                    $.post("../php/challenges.php",{"method":"get_challenge","ucgid":ucgid},function(response){

                                        try{

                                            response = JSON.parse(response);
                                            $("#name").addClass("hidden").html("");
                                            $("#description").html(response["description"].replace(/\\n/g, "\n").replace(/\\'/g, "'").replace(/\\"/g, "\""));
                                            codeBlock.options["lang"] = response["language"];

                                            var difficulty = response["difficulty"];
                                            $("#difficulty").find("option").each(function(){
                                                console.log(difficulty + $(this).val());
                                                $(this).removeAttr("selected");
                                                if($(this).val() == difficulty){
                                                    $(this).attr("selected","selected");
                                                }
                                            });

                                            $.post("../php/code.php",{"method":"get_code_block","ucbid":response["ucbid"]},function(code_block_response){

                                                try{

                                                    code_block_response = JSON.parse(code_block_response);
                                                    $("#editor").html(code_block_response[1].replace(/\\n/g, "\n").replace(/\\'/g, "'").replace(/\\"/g, "\""));

                                                    console.log(code_block_response[2]);
                                                    codeBlock.tests = JSON.parse(code_block_response[2]);
                                                    codeBlock.hiddenTests = JSON.parse(code_block_response[3]);
                                                    codeBlock.options["function_name"] = code_block_response[5];

                                                    editor = loaded();

                                                }
                                                catch(err){
                                                    console.log("Error Getting CodeBlock Data: "+err+" | response: "+code_block_response);
                                                }


                                            });

                                        }
                                        catch(err){
                                            console.log("Error Getting Challenge Data: "+err+" | response: "+response);
                                        }


                                    });

                                }

                            }
                            else if(type == "section"){

                                var usid = getUrl("usid");
                                if(usid){

                                    $.post("../php/code.php",{"method":"load","type":"course","content_id":usid},function(response){

                                        if(response && response != "You must supply a UUID or be logged in to do that."){

                                            try{
                                                var content = JSON.parse(response);
                                                console.log(content);

                                                $("#name").addClass("hidden").html("");
                                                $("#description").html(content[0].replace(/\\n/g, "\n").replace(/\\'/g, "'").replace(/\\"/g, "\""));
                                                $("#editor").html(content[1][1].replace(/\\n/g, "\n").replace(/\\'/g, "'").replace(/\\"/g, "\""));


                                                var tests = [JSON.parse(content[1][2]),JSON.parse((content[1][3]))];

                                                codeBlock.tests = tests[0];
                                                codeBlock.hiddenTests = tests[1];

                                                editor = loaded();

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

                            break;

                    }

                }



                $("#save").click(function(){
                    save(action, type, editor);
                });

            });
        </script>
    </body>
</html>