
function getUrl(variable){ //Checks the URL for a specified variable and returns it's value.
    try{
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if(pair[0] == variable){return pair[1];}
        }
        return(false);
    }
    catch(err){
        console.log("Error: " +err);
    }
}

function inViewport (el) {

    var r, html;
    if ( !el || 1 !== el.nodeType ) { return false; }
    html = document.documentElement;

    r = el.getBoundingClientRect();

    return ( !!r
        && r.bottom >= 0
        && r.right >= 0
        && r.top <= html.clientHeight
        && r.left <= html.clientWidth
    );

}

function getAchievement(tag){
    $.post('../php/achievements.php',{'method':'get','tag':tag},function(achievement_response){

        // console.log("Achievement Response -> "+achievement_response);
        if(achievement_response.indexOf("Completed") > -1){ // An achievement was completed.
            var space_index = achievement_response.indexOf(" ");
            var uaid = achievement_response.substring(space_index+1);

            $.post("../php/achievements.php",{"method":"lookup","uaid":uaid},function(achievement_json){

                try {
                    achievement_json = JSON.parse(achievement_json);
                    newAlert(["You Got A New Achievement!",achievement_json[0],achievement_json[2].replace("../","")]);
                }
                catch(err){
                    console.log(err+" | "+achievement_json);
                }

            });

        }
    
    });
}

function toggleMobileNav(){
    var icon = $("#mobile-nav-button").find("i");
    if(icon.html() == "menu"){
        icon.html("close");
        $("header").css({"height":"auto","line-height":"auto"});
    }
    else{
        icon.html("menu");
    }
    $("#mobile-nav").toggleClass("hidden");
}

$(document).ready(function(){
    $("#mobile-nav-button").on("click tap", function() {
        toggleMobileNav();
        $("header").css({"height":"auto"});
    });
    $(document).keyup(function(e) {
        if (e.keyCode === 27 && $("#mobile-nav-button").find("i").html() == "close") {
            toggleMobileNav();
            $("header").css({"height":"auto","line-height":"auto"});
        }
    });
});


(function($){

    // Custom Modal Plugin
    // Relies on jQuery, animate.css and my custom cssanimate jQuery plugin.
    function buildModal(){
        // Constructs a new modal from the modal template and configures it based on user settings.

        if(this.settings.debug){
            console.log(this);
        }

        this.element = $("#modal-template").find(".modal").clone();

        if(this.settings.exitButton){

            this.exitButton = this.element.find(".modal-close-button");
            this.exitButton.removeClass("hidden");
            this.exitButton = this.exitButton[0];

        }
        if(this.settings.overlay){

            this.overlay = this.element.find(".modal-overlay");
            this.overlay.removeClass("hidden");
            this.overlay = this.overlay[0];

        }

        if(this.settings.icon){

            this.element.find(".material-icon-container").addClass(this.settings.iconContainerClass).removeClass("hidden");
            this.element.find(".material-icons").html(this.settings.iconText);

        }

        this.element.find(".modal-header").addClass(this.settings.headerClass);
        this.element.find(".modal-header-content").html(this.settings.headerContent);

        this.element.find(".modal-body").addClass(this.settings.bodyClass);
        if(this.settings.bodyContent){
            this.element.find(".modal-content").html(this.settings.bodyContent);
        }

        if(this.settings.bodyButtons){

            var buttonsContainer = this.element.find(".modal-buttons").removeClass("hidden");

            for(bid in this.settings.bodyButtons){
                var button = this.settings.bodyButtons[bid];
                buttonsContainer.append("<button class='"+button[1]+"' onclick='"+button[2]+"'>"+button[0]+"</button>");
            }

        }

        $("body").append(this.element);

        var me = this;

        me.element.removeClass("hidden");

        var elementHeight = $(me.element).find(".modal-container").height();
        var windowHeight = $(window).height();
        var topOffset = (windowHeight / 2) - (elementHeight / 2);
        topOffset = topOffset > 0 ? topOffset : 0;
        me.element.find(".modal-container").css({"top":topOffset+"px"});

        $(window).resize(function(){
            var elementHeight = $(me.element).find(".modal-container").height();
            var windowHeight = $(window).height();
            var topOffset = (windowHeight / 2) - (elementHeight / 2);
            topOffset = topOffset > 0 ? topOffset : 0;
            me.element.find(".modal-container").css({"top":topOffset+"px"});
        });

        return this;

    }
    function bindModalEvents(){
        // Function that binds the exit events to the modal.
        if(this.settings.exitButton){
            this.exitButton.addEventListener('click', this.close.bind(this));
        }
        if(this.settings.overlay && this.settings.exitOverlayOnClick){
            this.overlay.addEventListener('click', this.close.bind(this));
        }
    }
    function unbindModalEvents() {
        // Function that unbinds the exit events from the modal.
        if (this.exitButton) {
            this.exitButton.removeEventListener('click');
        }
        if (this.settings.overlay && this.settings.exitOverlayOnClick) {
            this.overlay.removeEventListener('click');
        }
    }

    this.Modal = function(options){
        // Requirements:
        //     - Overlay:                 On/Off
        //     - Exit Button:             On/Off
        //     - Exit on overlay click:   On/Off
        //     - Material Icon Container: On/Off + Class Selection
        //     - Material Icon:           Content Replace
        //     - Header Content:          Content Replace + Class Selection
        //     - Body:                    Class Selection
        //     - Body Content:            Content Replace
        //     - Body Buttons (wrapper):  On/Off + Class Selection
        //     - Body Buttons:            Content Replace + Class Selection + Click Action.
        this.element = null;
        this.exitButton = null;
        this.overlay = null;

        this.settings = $.extend({
            exitButton: true,
            overlay: true,
            exitOverlayOnClick: true,
            inAnimation: "fadeInUp",
            outAnimation: "fadeOutDown",
            animationDuration: 400,
            icon: true,
            iconContainerClass: " circle green-border green-text",
            iconText: "check",
            headerContent: "<h2>Great Work!</h2><p>All Tests Have Passed!</p>",
            headerClass: "",
            bodyClass: "padding highlight",
            bodyContent: null,
            bodyButtons: [["Next Section", "modern-button margin-bottom margin-top transition cursor",function(){}]],
            debug: false
        },options);

        return this;

    }
    Modal.prototype.open = function(){

        var me = this;

        buildModal.call(me);
        bindModalEvents.call(me);

        if(me.overlay){
            $(me.overlay).fadeIn(me.settings.animationDuration);
        }
        me.element.find(".modal-container").cssanimate(me.settings.inAnimation,{duration: me.settings.animationDuration});

    };
    Modal.prototype.close = function(){

        if(this.overlay){
            $(this.overlay).fadeOut(this.settings.animationDuration);
        }

        var me = this;

        me.element.find(".modal-container").cssanimate(this.settings.outAnimation,{duration: this.settings.animationDuration * 2},function(){
            me.element.remove();
        });

    }
    // Custom parallax element plugin.
    $.fn.parallax = function(options){
        var parent = $(this);
        var settings = $.extend({
            default_divisor: -2.5,
            parallax_class: "parallax",
            debug: false
        }, options);

        function updateParallax(){
            // Function that updates all of the elements with the parallax_class's translateY value to add a parallax effect.

            if(settings.debug){
                console.log("update");
            }

            var scrollPos = window.pageYOffset || document.documentElement.scrollTop;

            // Only update the element's parallax position if it is currently visible
            parent.find("."+settings.parallax_class).each(function(){

                var element = $(this);
                var position = element.position();

                if(inViewport(element[0])){
                    var divisor = element.data("parallax-divisor") ? element.data("parallax-divisor") : settings.default_divisor;
                    var offset = element.data("parallax-offset") ? element.data("parallax-offset") : 0;
                    var newTop = Math.floor((1+(position.top - scrollPos) / divisor) + offset);

                    if(newTop < 2){
                        newTop = 0;
                    }

                    if(settings.debug){
                        console.log("("+position.top + "-" + scrollPos + "/" + divisor +")+"+offset);
                    }

                    element.css({"transform":"translateY("+newTop+"px)"});
                }

            });

        }

        var stutter_value = 1;
        var lag_reduce_counter = 0;

        function reduceLag(){
            // Function that waits two calls before actually calling the parallax updating function.

            if(settings.debug){
                console.log("ReduceLag: " + lag_reduce_counter);
            }

            if(lag_reduce_counter >= stutter_value){

                parallaxAnimationFrame = requestAnimationFrame(updateParallax);
                lag_reduce_counter = 0;

            }
            else{

                lag_reduce_counter++;

            }

        }

        var timer, leave;

        function idleAnimationLoop(){
            // Run the parallax effect when the user stops scrolling, this adds "silky smooth" endings.

            if(!leave){

                idleAnimationFrame = requestAnimationFrame(updateParallax);

                setTimeout(function(){

                    cancelAnimationFrame(idleAnimationFrame);
                    idleAnimationLoop();

                },150);

            }
            else{

                cancelAnimationFrame(idleAnimationFrame);

            }

        }

        window.addEventListener('scroll', function () {
            // Bind to the scroll event.

            leave = true;
            //parallaxAnimationFrame = requestAnimationFrame(updateParallax); // <- Having it update every cycle caused a lot of lag
            reduceLag(); // <- So now we update every other cycle.
            clearTimeout(timer);
            timer = setTimeout( function(){

                leave = false;
                idleAnimationLoop();

            }, 150 );

        });

        window.addEventListener('resize', function(){
            // Update the parallax on resize, but keep it fairly efficient by canceling the animation after 10microseconds.

            setTimeout(function(){

                resizeAnimationFrame = requestAnimationFrame(updateParallax);

            },10);
            setTimeout(function(){

                cancelAnimationFrame(resizeAnimationFrame);

            },20);

        });

        idleAnimationLoop();

        return this;

    }
    // Custom CSS animations plugin.
    $.fn.cssanimate = function( effect, options, sentCallback ){

        var element = $(this);
        var settings = $.extend({
            duration: 400,
            hide: true,
            inline: false,
            debug: false
        }, options);
        var callBack = sentCallback ? sentCallback : function(){};

        function stripAnimationClasses(element){
            //This function strips all CSS Animation Classes from the given element.
            var classesToStrip = ["animated","bounce","flash","pulse","rubberBand","shake","headShake","swing","tada","wobble","jello","bounceIn","bounceInDown","bounceInLeft","bounceInRight","bounceInUp","bounceOut","bounceOutDown","bounceOutLeft","bounceOutRight","bounceOutUp","fadeIn","fadeInDown","fadeInDownBig","fadeInLeft","fadeInLeftBig","fadeInRight","fadeInRightBig","fadeInUp","fadeInUpBig","fadeOut","fadeOutDown","fadeOutDownBig","fadeOutLeft","fadeOutLeftBig","fadeOutRight","fadeOutRightBig","fadeOutUp","fadeOutUpBig","flipInX","flipInY","flipOutX","flipOutY","lightSpeedIn","lightSpeedOut","rotateIn","rotateInDownLeft","rotateInDownRight","rotateInUpLeft","rotateInUpRight","rotateOut","rotateOutDownLeft","rotateOutDownRight","rotateOutUpLeft","rotateOutUpRight","hinge","rollIn","rollOut","zoomIn","zoomInDown","zoomInLeft","zoomInRight","zoomInUp","zoomOut","zoomOutDown","zoomOutLeft","zoomOutRight","zoomOutUp","slideInDown","slideInLeft","slideInRight","slideInUp","slideOutDown","slideOutLeft","slideOutRight","slideOutUp"];
            for(i=0;i<=classesToStrip.length;i++){
                if(element.hasClass(classesToStrip[i])){
                    element.removeClass(classesToStrip[i]);
                    if(settings.debug) console.log("Element '"+element.attr("class")+"' Had the class "+classesToStrip[i]+". It has been removed.");
                }
                else{
                    // console.log("Element '"+element+"' Does not have class "+classesToStrip[i]);
                }
            }
        }

        stripAnimationClasses(element);
        if(settings.hide){
            element.css({"display":"none"});
        }
        if(!settings.inline){
            element.css({"display":"block"});
        }
        else{
            element.css({"display":"inline-block"});
        }
        element.addClass("animated "+effect);

        setTimeout(function(){
            if(typeof callBack === "function"){
                callBack();
            }
        }, settings.duration);


    }
    // Custom slideshow plugin.
    $.fn.slideshow = function( options ){
        var selector = $(this);
        var settings = $.extend({
            slideClass:'slide',     // String:  The class of the slides in the slide show.
            slideDuration:5000,     // Integer: How long to display one slide. (ms)
            transition:'dissolve',  // String:  The transition to use. (only 'dissolve' currently)
            transitionDuration:500, // Integer: How long the transition takes. (ms)
            numSlides:0,            // Integer: PRIVATE. The number of slides.
            ontransition:function(){} // Function: Executed on slide transition.
        },options)
        function setup(){
            selector.find('.'+settings.slideClass).each(function(){
                var me = $(this);
                console.log(me.data("backgroundimage"));
                var bg = "url('"+$(this).data("backgroundimage")+"')";
                console.log(bg);
                $(this).css({
                    "background-image":bg,
                    "background-size":"cover",
                    "background-position":"center",
                    "background-repeat":"no-repeat",
                    "position":"absolute",
                    "top:":0,
                    "left":0,
                    "opacity":0
                });
            });
            setTimeout(function(){
                switch(settings.transition){
                    case "dissolve":
                        selector.css({
                            "height":"100vh"
                        });
                        selector.find('.'+settings.slideClass).css({
                            "position":"absolute",
                            "top":0,
                            "left":0
                        });
                        var slide_num = 0;
                        selector.find('.'+settings.slideClass).each(function(){
                            var slide = $(this);
                            slide_num++;
                            if(slide_num > 1){
                                slide.css({"opacity":0});
                            }
                            else{
                                slide.css({"opacity":1});
                            }
                        });
                        settings.numSlides = slide_num;
                        break;
                    default:
                        console.log("Slide Show: Transition '"+settings.transition+"' was not found.");
                        break;
                }
                selector.find("."+settings.slideClass).css({

                    "transition":"all "+settings.transitionDuration+"ms linear",
                    "-moz-transition":"all "+settings.transitionDuration+"ms linear",
                    "-ms-transition":"all "+settings.transitionDuration+"ms linear",
                    "-o-transition":"all "+settings.transitionDuration+"ms linear",

                });
            },1);
        }
        current_slide = 0;
        function rotate(){
            if(inViewport(selector[0])){
                current_slide++;
                switch(settings.transition) {
                    case "dissolve":
                        var slide_selector = 0;
                        selector.find('.'+settings.slideClass).each(function(){
                            var slide = $(this);
                            slide_selector++;
                            if(current_slide < settings.numSlides){
                                if( slide_selector == current_slide ){
                                    // Fade out old slide
                                    slide.css({"opacity":0});
                                }
                                if( slide_selector == current_slide + 1){
                                    // Fade in the new slide
                                    slide.css({"opacity":1});
                                    settings.ontransition(slide);
                                }
                            }
                        });
                        if(current_slide >= settings.numSlides){
                            var slide_num = 0;
                            selector.find('.'+settings.slideClass).each(function(){
                                var slide = $(this);
                                slide_num++;
                                if(slide_num > 1){
                                    slide.css({"opacity":0});
                                }
                                else{
                                    slide.css({"opacity":1});
                                    settings.ontransition(slide);
                                }
                            });
                            current_slide = 0;
                        }
                        break;

                    default:
                        break;
                }
            }
            setTimeout(function(){rotate();},settings.slideDuration);
        }
        setup();
        rotate();
    }
}(jQuery));

/*
CryptoJS v3.1.2
code.google.com/p/crypto-js
(c) 2009-2013 by Jeff Mott. All rights reserved.
code.google.com/p/crypto-js/wiki/License
*/
var CryptoJS=CryptoJS||function(a,b){var c={},d=c.lib={},e=function(){},f=d.Base={extend:function(a){e.prototype=this;var b=new e;return a&&b.mixIn(a),b.hasOwnProperty("init")||(b.init=function(){b.$super.init.apply(this,arguments)}),b.init.prototype=b,b.$super=this,b},create:function(){var a=this.extend();return a.init.apply(a,arguments),a},init:function(){},mixIn:function(a){for(var b in a)a.hasOwnProperty(b)&&(this[b]=a[b]);a.hasOwnProperty("toString")&&(this.toString=a.toString)},clone:function(){return this.init.prototype.extend(this)}},g=d.WordArray=f.extend({init:function(a,c){a=this.words=a||[],this.sigBytes=c!=b?c:4*a.length},toString:function(a){return(a||i).stringify(this)},concat:function(a){var b=this.words,c=a.words,d=this.sigBytes;if(a=a.sigBytes,this.clamp(),d%4)for(var e=0;e<a;e++)b[d+e>>>2]|=(c[e>>>2]>>>24-8*(e%4)&255)<<24-8*((d+e)%4);else if(65535<c.length)for(e=0;e<a;e+=4)b[d+e>>>2]=c[e>>>2];else b.push.apply(b,c);return this.sigBytes+=a,this},clamp:function(){var b=this.words,c=this.sigBytes;b[c>>>2]&=4294967295<<32-8*(c%4),b.length=a.ceil(c/4)},clone:function(){var a=f.clone.call(this);return a.words=this.words.slice(0),a},random:function(b){for(var c=[],d=0;d<b;d+=4)c.push(4294967296*a.random()|0);return new g.init(c,b)}}),h=c.enc={},i=h.Hex={stringify:function(a){var b=a.words;a=a.sigBytes;for(var c=[],d=0;d<a;d++){var e=b[d>>>2]>>>24-8*(d%4)&255;c.push((e>>>4).toString(16)),c.push((15&e).toString(16))}return c.join("")},parse:function(a){for(var b=a.length,c=[],d=0;d<b;d+=2)c[d>>>3]|=parseInt(a.substr(d,2),16)<<24-4*(d%8);return new g.init(c,b/2)}},j=h.Latin1={stringify:function(a){var b=a.words;a=a.sigBytes;for(var c=[],d=0;d<a;d++)c.push(String.fromCharCode(b[d>>>2]>>>24-8*(d%4)&255));return c.join("")},parse:function(a){for(var b=a.length,c=[],d=0;d<b;d++)c[d>>>2]|=(255&a.charCodeAt(d))<<24-8*(d%4);return new g.init(c,b)}},k=h.Utf8={stringify:function(a){try{return decodeURIComponent(escape(j.stringify(a)))}catch(a){throw Error("Malformed UTF-8 data")}},parse:function(a){return j.parse(unescape(encodeURIComponent(a)))}},l=d.BufferedBlockAlgorithm=f.extend({reset:function(){this._data=new g.init,this._nDataBytes=0},_append:function(a){"string"==typeof a&&(a=k.parse(a)),this._data.concat(a),this._nDataBytes+=a.sigBytes},_process:function(b){var c=this._data,d=c.words,e=c.sigBytes,f=this.blockSize,h=e/(4*f),h=b?a.ceil(h):a.max((0|h)-this._minBufferSize,0);if(b=h*f,e=a.min(4*b,e),b){for(var i=0;i<b;i+=f)this._doProcessBlock(d,i);i=d.splice(0,b),c.sigBytes-=e}return new g.init(i,e)},clone:function(){var a=f.clone.call(this);return a._data=this._data.clone(),a},_minBufferSize:0});d.Hasher=l.extend({cfg:f.extend(),init:function(a){this.cfg=this.cfg.extend(a),this.reset()},reset:function(){l.reset.call(this),this._doReset()},update:function(a){return this._append(a),this._process(),this},finalize:function(a){return a&&this._append(a),this._doFinalize()},blockSize:16,_createHelper:function(a){return function(b,c){return new a.init(c).finalize(b)}},_createHmacHelper:function(a){return function(b,c){return new m.HMAC.init(a,c).finalize(b)}}});var m=c.algo={};return c}(Math);!function(a){var b=CryptoJS,c=b.lib,d=c.Base,e=c.WordArray,b=b.x64={};b.Word=d.extend({init:function(a,b){this.high=a,this.low=b}}),b.WordArray=d.extend({init:function(b,c){b=this.words=b||[],this.sigBytes=c!=a?c:8*b.length},toX32:function(){for(var a=this.words,b=a.length,c=[],d=0;d<b;d++){var f=a[d];c.push(f.high),c.push(f.low)}return e.create(c,this.sigBytes)},clone:function(){for(var a=d.clone.call(this),b=a.words=this.words.slice(0),c=b.length,e=0;e<c;e++)b[e]=b[e].clone();return a}})}(),function(){function a(){return e.create.apply(e,arguments)}for(var b=CryptoJS,c=b.lib.Hasher,d=b.x64,e=d.Word,f=d.WordArray,d=b.algo,g=[a(1116352408,3609767458),a(1899447441,602891725),a(3049323471,3964484399),a(3921009573,2173295548),a(961987163,4081628472),a(1508970993,3053834265),a(2453635748,2937671579),a(2870763221,3664609560),a(3624381080,2734883394),a(310598401,1164996542),a(607225278,1323610764),a(1426881987,3590304994),a(1925078388,4068182383),a(2162078206,991336113),a(2614888103,633803317),a(3248222580,3479774868),a(3835390401,2666613458),a(4022224774,944711139),a(264347078,2341262773),a(604807628,2007800933),a(770255983,1495990901),a(1249150122,1856431235),a(1555081692,3175218132),a(1996064986,2198950837),a(2554220882,3999719339),a(2821834349,766784016),a(2952996808,2566594879),a(3210313671,3203337956),a(3336571891,1034457026),a(3584528711,2466948901),a(113926993,3758326383),a(338241895,168717936),a(666307205,1188179964),a(773529912,1546045734),a(1294757372,1522805485),a(1396182291,2643833823),a(1695183700,2343527390),a(1986661051,1014477480),a(2177026350,1206759142),a(2456956037,344077627),a(2730485921,1290863460),a(2820302411,3158454273),a(3259730800,3505952657),a(3345764771,106217008),a(3516065817,3606008344),a(3600352804,1432725776),a(4094571909,1467031594),a(275423344,851169720),a(430227734,3100823752),a(506948616,1363258195),a(659060556,3750685593),a(883997877,3785050280),a(958139571,3318307427),a(1322822218,3812723403),a(1537002063,2003034995),a(1747873779,3602036899),a(1955562222,1575990012),a(2024104815,1125592928),a(2227730452,2716904306),a(2361852424,442776044),a(2428436474,593698344),a(2756734187,3733110249),a(3204031479,2999351573),a(3329325298,3815920427),a(3391569614,3928383900),a(3515267271,566280711),a(3940187606,3454069534),a(4118630271,4000239992),a(116418474,1914138554),a(174292421,2731055270),a(289380356,3203993006),a(460393269,320620315),a(685471733,587496836),a(852142971,1086792851),a(1017036298,365543100),a(1126000580,2618297676),a(1288033470,3409855158),a(1501505948,4234509866),a(1607167915,987167468),a(1816402316,1246189591)],h=[],i=0;80>i;i++)h[i]=a();d=d.SHA512=c.extend({_doReset:function(){this._hash=new f.init([new e.init(1779033703,4089235720),new e.init(3144134277,2227873595),new e.init(1013904242,4271175723),new e.init(2773480762,1595750129),new e.init(1359893119,2917565137),new e.init(2600822924,725511199),new e.init(528734635,4215389547),new e.init(1541459225,327033209)])},_doProcessBlock:function(a,b){for(var c=this._hash.words,d=c[0],e=c[1],f=c[2],i=c[3],j=c[4],k=c[5],l=c[6],c=c[7],m=d.high,n=d.low,o=e.high,p=e.low,q=f.high,r=f.low,s=i.high,t=i.low,u=j.high,v=j.low,w=k.high,x=k.low,y=l.high,z=l.low,A=c.high,B=c.low,C=m,D=n,E=o,F=p,G=q,H=r,I=s,J=t,K=u,L=v,M=w,N=x,O=y,P=z,Q=A,R=B,S=0;80>S;S++){var T=h[S];if(16>S)var U=T.high=0|a[b+2*S],V=T.low=0|a[b+2*S+1];else{var U=h[S-15],V=U.high,W=U.low,U=(V>>>1|W<<31)^(V>>>8|W<<24)^V>>>7,W=(W>>>1|V<<31)^(W>>>8|V<<24)^(W>>>7|V<<25),X=h[S-2],V=X.high,Y=X.low,X=(V>>>19|Y<<13)^(V<<3|Y>>>29)^V>>>6,Y=(Y>>>19|V<<13)^(Y<<3|V>>>29)^(Y>>>6|V<<26),V=h[S-7],Z=V.high,$=h[S-16],_=$.high,$=$.low,V=W+V.low,U=U+Z+(V>>>0<W>>>0?1:0),V=V+Y,U=U+X+(V>>>0<Y>>>0?1:0),V=V+$,U=U+_+(V>>>0<$>>>0?1:0);T.high=U,T.low=V}var Z=K&M^~K&O,$=L&N^~L&P,T=C&E^C&G^E&G,aa=D&F^D&H^F&H,W=(C>>>28|D<<4)^(C<<30|D>>>2)^(C<<25|D>>>7),X=(D>>>28|C<<4)^(D<<30|C>>>2)^(D<<25|C>>>7),Y=g[S],ba=Y.high,ca=Y.low,Y=R+((L>>>14|K<<18)^(L>>>18|K<<14)^(L<<23|K>>>9)),_=Q+((K>>>14|L<<18)^(K>>>18|L<<14)^(K<<23|L>>>9))+(Y>>>0<R>>>0?1:0),Y=Y+$,_=_+Z+(Y>>>0<$>>>0?1:0),Y=Y+ca,_=_+ba+(Y>>>0<ca>>>0?1:0),Y=Y+V,_=_+U+(Y>>>0<V>>>0?1:0),V=X+aa,T=W+T+(V>>>0<X>>>0?1:0),Q=O,R=P,O=M,P=N,M=K,N=L,L=J+Y|0,K=I+_+(L>>>0<J>>>0?1:0)|0,I=G,J=H,G=E,H=F,E=C,F=D,D=Y+V|0,C=_+T+(D>>>0<Y>>>0?1:0)|0}n=d.low=n+D,d.high=m+C+(n>>>0<D>>>0?1:0),p=e.low=p+F,e.high=o+E+(p>>>0<F>>>0?1:0),r=f.low=r+H,f.high=q+G+(r>>>0<H>>>0?1:0),t=i.low=t+J,i.high=s+I+(t>>>0<J>>>0?1:0),v=j.low=v+L,j.high=u+K+(v>>>0<L>>>0?1:0),x=k.low=x+N,k.high=w+M+(x>>>0<N>>>0?1:0),z=l.low=z+P,l.high=y+O+(z>>>0<P>>>0?1:0),B=c.low=B+R,c.high=A+Q+(B>>>0<R>>>0?1:0)},_doFinalize:function(){var a=this._data,b=a.words,c=8*this._nDataBytes,d=8*a.sigBytes;return b[d>>>5]|=128<<24-d%32,b[(d+128>>>10<<5)+30]=Math.floor(c/4294967296),b[(d+128>>>10<<5)+31]=c,a.sigBytes=4*b.length,this._process(),this._hash.toX32()},clone:function(){var a=c.clone.call(this);return a._hash=this._hash.clone(),a},blockSize:32}),b.SHA512=c._createHelper(d),b.HmacSHA512=c._createHmacHelper(d)}();
