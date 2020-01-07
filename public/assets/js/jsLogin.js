    (function (cjs, an) {

        var p; // shortcut to reference prototypes
        var lib={};var ss={};var img={};
        lib.ssMetadata = [
            {name:"logo_atlas_", frames: [[0,0,294,178],[231,344,89,76],[0,180,122,174],[296,0,128,170],[426,0,37,209],[296,172,128,170],[124,282,105,156],[124,180,169,100]]}
        ];


        // symbols:



        (lib.CachedBmp_27 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(0);
        }).prototype = p = new cjs.Sprite();



        (lib.CachedBmp_26 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(1);
        }).prototype = p = new cjs.Sprite();



        (lib.CachedBmp_25 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(2);
        }).prototype = p = new cjs.Sprite();



        (lib.CachedBmp_24 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(3);
        }).prototype = p = new cjs.Sprite();



        (lib.CachedBmp_23 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(4);
        }).prototype = p = new cjs.Sprite();



        (lib.CachedBmp_22 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(5);
        }).prototype = p = new cjs.Sprite();



        (lib.CachedBmp_13 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(6);
        }).prototype = p = new cjs.Sprite();



        (lib.CachedBmp_28 = function() {
            this.initialize(ss["logo_atlas_"]);
            this.gotoAndStop(7);
        }).prototype = p = new cjs.Sprite();
// helper functions:

        function mc_symbol_clone() {
            var clone = this._cloneProps(new this.constructor(this.mode, this.startPosition, this.loop));
            clone.gotoAndStop(this.currentFrame);
            clone.paused = this.paused;
            clone.framerate = this.framerate;
            return clone;
        }

        function getMCSymbolPrototype(symbol, nominalBounds, frameBounds) {
            var prototype = cjs.extend(symbol, cjs.MovieClip);
            prototype.clone = mc_symbol_clone;
            prototype.nominalBounds = nominalBounds;
            prototype.frameBounds = frameBounds;
            return prototype;
        }


        (lib.Symbole1 = function(mode,startPosition,loop) {
            this.initialize(mode,startPosition,loop,{});

            // Calque_1
            this.instance = new lib.CachedBmp_27();
            this.instance.setTransform(0,0,0.1686,0.1686);

            this.timeline.addTween(cjs.Tween.get(this.instance).wait(1));

            this._renderFirstFrame();

        }).prototype = getMCSymbolPrototype(lib.Symbole1, new cjs.Rectangle(0,0,49.6,30), null);


        (lib.orange = function(mode,startPosition,loop) {
            this.initialize(mode,startPosition,loop,{});

            // Calque_1
            this.instance = new lib.CachedBmp_26();
            this.instance.setTransform(0,0,0.1669,0.1669);

            this.timeline.addTween(cjs.Tween.get(this.instance).wait(1));

            this._renderFirstFrame();

        }).prototype = getMCSymbolPrototype(lib.orange, new cjs.Rectangle(0,0,14.9,12.7), null);


        (lib.nom = function(mode,startPosition,loop) {
            this.initialize(mode,startPosition,loop,{});

            // Calque_1
            this.instance = new lib.CachedBmp_25();
            this.instance.setTransform(66.15,6.5,0.1686,0.1686);

            this.instance_1 = new lib.CachedBmp_24();
            this.instance_1.setTransform(39.8,6.5,0.1686,0.1686);

            this.instance_2 = new lib.CachedBmp_23();
            this.instance_2.setTransform(27.55,0,0.1686,0.1686);

            this.instance_3 = new lib.CachedBmp_22();
            this.instance_3.setTransform(0,6.5,0.1686,0.1686);

            this.timeline.addTween(cjs.Tween.get({}).to({state:[{t:this.instance_3},{t:this.instance_2},{t:this.instance_1},{t:this.instance}]}).wait(1));

            this._renderFirstFrame();

        }).prototype = getMCSymbolPrototype(lib.nom, new cjs.Rectangle(0,0,86.7,35.9), null);


        (lib.bleu = function(mode,startPosition,loop) {
            this.initialize(mode,startPosition,loop,{});

            // Calque_1
            this.instance = new lib.CachedBmp_13();
            this.instance.setTransform(0,0,0.1681,0.1681);

            this.timeline.addTween(cjs.Tween.get(this.instance).wait(1));

            this._renderFirstFrame();

        }).prototype = getMCSymbolPrototype(lib.bleu, new cjs.Rectangle(0,0,17.7,26.3), null);


        (lib.vert = function(mode,startPosition,loop) {
            this.initialize(mode,startPosition,loop,{});

            // Calque_1
            this.instance = new lib.Symbole1();
            this.instance.setTransform(49.6,15,1,1,0,0,0,49.6,15);

            this.timeline.addTween(cjs.Tween.get(this.instance).wait(1).to({regX:24.8,x:24.8},0).wait(23));

            this._renderFirstFrame();

        }).prototype = p = new cjs.MovieClip();
        p.nominalBounds = new cjs.Rectangle(0,0,49.6,30);


// stage content:
        (lib.logo = function(mode,startPosition,loop) {
            if (loop == null) { loop = false; }	this.initialize(mode,startPosition,loop,{});

            // CMS
            this.instance = new lib.CachedBmp_28();
            this.instance.setTransform(94.75,156.05,0.5,0.5);
            this.instance._off = true;

            this.timeline.addTween(cjs.Tween.get(this.instance).wait(40).to({_off:false},0).wait(5));

            // vert
            this.instance_1 = new lib.vert();
            this.instance_1.setTransform(222.95,177.2,2.9654,2.9654,90,0,0,50.6,12.6);

            this.timeline.addTween(cjs.Tween.get(this.instance_1).wait(1).to({regX:24.8,regY:15,rotation:78.4884,x:200.7,y:103.65},0).wait(1).to({rotation:67.8096,x:187.5,y:109.05},0).wait(1).to({rotation:57.8298,x:176.25,y:116.25},0).wait(1).to({rotation:48.5432,x:167.05,y:124.6},0).wait(1).to({rotation:39.9821,x:159.85,y:133.55},0).wait(1).to({rotation:32.2059,x:154.6,y:142.5},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:25.3005,x:150.95,y:150.95},0).wait(1).to({rotation:19.3842,x:148.6,y:158.5},0).wait(1).to({rotation:14.6191,x:147.3,y:164.8},0).wait(1).to({rotation:11.232,x:146.8,y:169.25},0).wait(1).to({scaleX:2.9654,scaleY:2.9654,rotation:9.0328,x:146.5,y:172.25},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:7.5774,x:146.4,y:174.15},0).wait(1).to({rotation:6.7584,y:175.25},0).wait(1).to({rotation:6.4475,y:175.65},0).wait(1).to({rotation:6.5146,x:146.35,y:175.6},0).wait(1).to({rotation:6.8422,y:175.1},0).wait(1).to({rotation:7.3323,x:146.4,y:174.5},0).wait(1).to({scaleX:2.9654,scaleY:2.9654,rotation:7.9082,x:146.45,y:173.7},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:8.5123,y:172.95},0).wait(1).to({rotation:9.1027,x:146.5,y:172.1},0).wait(1).to({scaleX:2.9654,scaleY:2.9654,rotation:9.6499,x:146.55,y:171.4},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:10.1336,x:146.6,y:170.75},0).wait(1).to({scaleX:2.9654,scaleY:2.9654,rotation:10.5403,x:146.65,y:170.2},0).wait(1).to({rotation:10.8616,y:169.8},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:11.0925,x:146.7,y:169.45},0).wait(1).to({scaleX:2.9654,scaleY:2.9654,rotation:11.2304,x:146.8,y:169.3},0).wait(1).to({rotation:11.2748,x:146.75,y:169.2},0).wait(1).to({rotation:11.2261,y:169.3},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:11.0024,x:146.7,y:169.6},0).wait(1).to({scaleX:2.9654,scaleY:2.9654,rotation:10.5043,x:146.65,y:170.25},0).wait(1).to({rotation:9.7196,x:146.6,y:171.3},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:8.6321,x:146.45,y:172.8},0).wait(1).to({scaleX:2.9654,scaleY:2.9654,rotation:7.2183,x:146.35,y:174.65},0).wait(1).to({rotation:5.443,y:177.05},0).wait(1).to({scaleX:2.9655,scaleY:2.9655,rotation:3.2429,x:146.4,y:179.95},0).wait(1).to({rotation:0.4355,x:146.65,y:183.7},0).wait(1).to({rotation:0.4355},0).wait(8));

            // bleu
            this.instance_2 = new lib.bleu();
            this.instance_2.setTransform(218.85,192.75,2.9739,2.9739,-65.0004,0,0,19.4,44.2);
            this.instance_2._off = true;

            this.timeline.addTween(cjs.Tween.get(this.instance_2).wait(11).to({_off:false},0).wait(1).to({regX:8.8,regY:13.1,scaleX:2.974,scaleY:2.974,rotation:-49.8707,x:127.8,y:157.2},0).wait(1).to({rotation:-40.2343,x:135.1,y:142.5},0).wait(1).to({rotation:-32.7201,x:142.35,y:131.95},0).wait(1).to({rotation:-26.5346,x:149.4,y:124.05},0).wait(1).to({rotation:-21.3044,x:156,y:118.05},0).wait(1).to({rotation:-16.8132,x:162,y:113.35},0).wait(1).to({rotation:-12.92,x:167.55,y:109.6},0).wait(1).to({rotation:-9.5266,x:172.6,y:106.7},0).wait(1).to({rotation:-6.5607,x:177.1,y:104.45},0).wait(1).to({scaleX:2.9741,scaleY:2.9741,rotation:-3.9667,x:181.15,y:102.65},0).wait(1).to({scaleX:2.974,scaleY:2.974,rotation:-1.7013,x:184.75,y:101.25},0).wait(1).to({scaleX:2.9741,scaleY:2.9741,rotation:0.2709,x:187.9,y:100.1},0).wait(1).to({rotation:1.3892,x:189.7,y:99.55},0).wait(1).to({rotation:1.4412,x:189.8,y:99.5},0).wait(1).to({scaleX:2.974,scaleY:2.974,rotation:0.8058,x:188.75,y:99.8},0).wait(1).to({scaleX:2.9741,scaleY:2.9741,rotation:-0.2655,x:187.05,y:100.45},0).wait(1).to({rotation:-1.5727,x:184.95,y:101.2},0).wait(1).to({scaleX:2.974,scaleY:2.974,rotation:-2.9207,x:182.85,y:101.95},0).wait(1).to({scaleX:2.9741,scaleY:2.9741,rotation:-4.0665,x:181,y:102.7},0).wait(1).to({scaleX:2.974,scaleY:2.974,rotation:-4.6043,x:180.2,y:103.1},0).wait(1).to({rotation:-4.3052,x:180.6,y:102.9},0).wait(1).to({rotation:-3.7289,x:181.55,y:102.5},0).wait(1).to({scaleX:2.9741,scaleY:2.9741,rotation:-3.0276,x:182.65,y:102},0).wait(1).to({rotation:-2.2479,x:183.9,y:101.55},0).wait(1).to({rotation:-1.4099,x:185.2,y:101.05},0).wait(1).to({rotation:-0.5184,x:186.65,y:100.5},0).wait(8));

            // orange
            this.instance_3 = new lib.orange();
            this.instance_3.setTransform(188.15,175.85,2.9953,2.9953,-140.0002,0,0,-11.8,18.1);
            this.instance_3._off = true;

            this.timeline.addTween(cjs.Tween.get(this.instance_3).wait(10).to({_off:false},0).wait(1).to({regX:7.4,regY:6.3,rotation:-100.6259,x:142.9,y:125.85},0).wait(1).to({scaleX:2.9954,scaleY:2.9954,rotation:-79.1463,x:164.4,y:112.85},0).wait(1).to({scaleX:2.9953,scaleY:2.9953,rotation:-63.8269,x:182,y:108.75},0).wait(1).to({rotation:-52.2311,x:195.6,y:108.95},0).wait(1).to({rotation:-43.256,x:206,y:110.9},0).wait(1).to({rotation:-36.2774,x:213.75,y:113.55},0).wait(1).to({rotation:-30.8969,x:219.5,y:116.25},0).wait(1).to({scaleX:2.9954,scaleY:2.9954,rotation:-26.8395,x:223.7,y:118.65},0).wait(1).to({scaleX:2.9953,scaleY:2.9953,rotation:-23.9051,x:226.55,y:120.5},0).wait(1).to({scaleX:2.9954,scaleY:2.9954,rotation:-21.9421,x:228.45,y:121.85},0).wait(1).to({rotation:-20.8321,x:229.45,y:122.65},0).wait(1).to({scaleX:2.9953,scaleY:2.9953,rotation:-20.4801,x:229.8,y:122.9},0).wait(1).to({scaleX:2.9954,scaleY:2.9954,rotation:-20.8776,x:229.4,y:122.6},0).wait(1).to({scaleX:2.9953,scaleY:2.9953,rotation:-22.1191,x:228.3,y:121.75},0).wait(1).to({rotation:-24.0706,x:226.4,y:120.4},0).wait(1).to({rotation:-26.2575,x:224.3,y:118.95},0).wait(1).to({rotation:-28.1438,x:222.35,y:117.8},0).wait(1).to({rotation:-29.4643,x:221.05,y:117.05},0).wait(1).to({rotation:-30.1991,x:220.25,y:116.6},0).wait(1).to({scaleX:2.9954,scaleY:2.9954,rotation:-30.4238,x:220,y:116.45},0).wait(1).to({scaleX:2.9953,scaleY:2.9953,rotation:-29.5419,x:220.95,y:116.9},0).wait(1).to({rotation:-27.29,x:223.2,y:118.3},0).wait(1).to({rotation:-24.1548,x:226.3,y:120.3},0).wait(1).to({rotation:-20.5204,x:229.75,y:122.85},0).wait(1).to({rotation:-16.6994,x:233.25,y:125.7},0).wait(1).to({scaleX:2.9954,scaleY:2.9954,rotation:-12.9528,x:236.45,y:128.8},0).wait(1).to({rotation:-9.5044,x:239.15,y:131.75},0).wait(1).to({scaleX:2.9953,scaleY:2.9953,rotation:-7.0212,x:241.05,y:134.05},0).wait(1).to({rotation:-5.307,x:242.25,y:135.6},0).wait(1).to({scaleX:2.9954,scaleY:2.9954,rotation:-3.9685,x:243.2,y:136.9},0).wait(1).to({rotation:-2.8703,x:243.95,y:137.95},0).wait(1).to({rotation:-1.9486,x:244.55,y:138.85},0).wait(1).to({rotation:-1.1715,x:245.05,y:139.6},0).wait(1).to({rotation:-0.5369,x:245.45,y:140.25},0).wait(1));

            // nom
            this.instance_4 = new lib.nom();
            this.instance_4.setTransform(172.05,291.45,2.9655,2.9655,0,0,0,43.4,17.9);

            this.timeline.addTween(cjs.Tween.get(this.instance_4).wait(45));

            this._renderFirstFrame();

        }).prototype = p = new cjs.MovieClip();
        p.nominalBounds = new cjs.Rectangle(218.4,197.8,82.1,146.89999999999998);
// library properties:
        lib.properties = {
            id: 'E9AAB67CB2804D38AD9E295A2B8FD13F',
            width: 350,
            height: 350,
            fps: 24,
            color: "#FFFFFF",
            opacity: 1.00,
            manifest: [
                {src:"assets/img/login/logo_nina_.png?1576156667737", id:"logo_atlas_"}
            ],
            preloads: []
        };



// bootstrap callback support:

        (lib.Stage = function(canvas) {
            createjs.Stage.call(this, canvas);
        }).prototype = p = new createjs.StageGL();

        p.setAutoPlay = function(autoPlay) {
            this.tickEnabled = autoPlay;
        }
        p.play = function() { this.tickEnabled = true; this.getChildAt(0).gotoAndPlay(this.getTimelinePosition()) }
        p.stop = function(ms) { if(ms) this.seek(ms); this.tickEnabled = false; }
        p.seek = function(ms) { this.tickEnabled = true; this.getChildAt(0).gotoAndStop(lib.properties.fps * ms / 1000); }
        p.getDuration = function() { return this.getChildAt(0).totalFrames / lib.properties.fps * 1000; }

        p.getTimelinePosition = function() { return this.getChildAt(0).currentFrame / lib.properties.fps * 1000; }

        an.bootcompsLoaded = an.bootcompsLoaded || [];
        if(!an.bootstrapListeners) {
            an.bootstrapListeners=[];
        }

        an.bootstrapCallback=function(fnCallback) {
            an.bootstrapListeners.push(fnCallback);
            if(an.bootcompsLoaded.length > 0) {
                for(var i=0; i<an.bootcompsLoaded.length; ++i) {
                    fnCallback(an.bootcompsLoaded[i]);
                }
            }
        };

        an.compositions = an.compositions || {};
        an.compositions['E9AAB67CB2804D38AD9E295A2B8FD13F'] = {
            getStage: function() { return exportRoot.stage; },
            getLibrary: function() { return lib; },
            getSpriteSheet: function() { return ss; },
            getImages: function() { return img; }
        };

        an.compositionLoaded = function(id) {
            an.bootcompsLoaded.push(id);
            for(var j=0; j<an.bootstrapListeners.length; j++) {
                an.bootstrapListeners[j](id);
            }
        }

        an.getComposition = function(id) {
            return an.compositions[id];
        }


        an.makeResponsive = function(isResp, respDim, isScale, scaleType, domContainers) {
            var lastW, lastH, lastS=1;
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();
            function resizeCanvas() {
                var w = lib.properties.width, h = lib.properties.height;
                var iw = window.innerWidth, ih=window.innerHeight;
                var pRatio = window.devicePixelRatio || 1, xRatio=iw/w, yRatio=ih/h, sRatio=1;
                if(isResp) {
                    if((respDim=='width'&&lastW==iw) || (respDim=='height'&&lastH==ih)) {
                        sRatio = lastS;
                    }
                    else if(!isScale) {
                        if(iw<w || ih<h)
                            sRatio = Math.min(xRatio, yRatio);
                    }
                    else if(scaleType==1) {
                        sRatio = Math.min(xRatio, yRatio);
                    }
                    else if(scaleType==2) {
                        sRatio = Math.max(xRatio, yRatio);
                    }
                }
                domContainers[0].width = w * pRatio * sRatio;
                domContainers[0].height = h * pRatio * sRatio;
                domContainers.forEach(function(container) {
                    container.style.width = w * sRatio + 'px';
                    container.style.height = h * sRatio + 'px';
                });
                stage.scaleX = pRatio*sRatio;
                stage.scaleY = pRatio*sRatio;
                lastW = iw; lastH = ih; lastS = sRatio;
                stage.tickOnUpdate = false;
                stage.update();
                stage.tickOnUpdate = true;
            }
        }


    })(createjs = createjs||{}, AdobeAn = AdobeAn||{});
    var createjs, AdobeAn;

var canvas, stage, exportRoot, anim_container, dom_overlay_container, fnStartAnimation;
function init() {
    canvas = document.getElementById("canvas");
    anim_container = document.getElementById("animation_container");
    dom_overlay_container = document.getElementById("dom_overlay_container");
    var comp=AdobeAn.getComposition("E9AAB67CB2804D38AD9E295A2B8FD13F");
    var lib=comp.getLibrary();
    var loader = new createjs.LoadQueue(false);
    loader.addEventListener("fileload", function(evt){handleFileLoad(evt,comp)});
    loader.addEventListener("complete", function(evt){handleComplete(evt,comp)});
    var lib=comp.getLibrary();
    loader.loadManifest(lib.properties.manifest);
}
function handleFileLoad(evt, comp) {
    var images=comp.getImages();
    if (evt && (evt.item.type == "image")) { images[evt.item.id] = evt.result; }
}
function handleComplete(evt,comp) {
    //This function is always called, irrespective of the content. You can use the variable "stage" after it is created in token create_stage.
    var lib=comp.getLibrary();
    var ss=comp.getSpriteSheet();
    var queue = evt.target;
    var ssMetadata = lib.ssMetadata;
    for(i=0; i<ssMetadata.length; i++) {
        ss[ssMetadata[i].name] = new createjs.SpriteSheet( {"images": [queue.getResult(ssMetadata[i].name)], "frames": ssMetadata[i].frames} )
    }
    exportRoot = new lib.logo();
    stage = new lib.Stage(canvas);
    //Registers the "tick" event listener.
    fnStartAnimation = function() {
        stage.addChild(exportRoot);
        createjs.Ticker.framerate = lib.properties.fps;
        createjs.Ticker.addEventListener("tick", stage);
    }
    //Code to support hidpi screens and responsive scaling.
    AdobeAn.makeResponsive(false,'both',false,1,[canvas,anim_container,dom_overlay_container]);
    AdobeAn.compositionLoaded(lib.properties.id);
    fnStartAnimation();
}

var waitForFinalEvent = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout (timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

$(function(){
    largeur = $(window).width();
    if(largeur > 499){
        init();
    }

    $(window).on('orientationchange resize', function(){
        largeur = $(window).width();
        if(largeur > 499){
            $('#animation_container').css({
                width: 'auto',
                height: 'auto'
            });
            waitForFinalEvent(function(){
                init()
            }, 500, "animLogo");
        }
    })
});