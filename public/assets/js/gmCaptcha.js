function gmCaptcha(options) {
    let defopt = {
        spellLang: "it-IT",
        type: "graphic"
    }

    options = { ...defopt, ...options };

    let _this = this;
    let gmc = document.getElementById("gmCaptcha");
    var path;
    let loadr;

    this.drawCaptcha = function(dataImg) {
        gmc.setAttribute("style", "text-align: center;");
        gmc.textContent = "";

        const stl = document.createElement("style");
        stl.setAttribute("type", "text/css");
        stl.textContent = ".clearfix::after {content: ''; display: block; clear: both;}";

        gmc.appendChild(stl);

        const cnt = document.createElement("div");

        cnt.setAttribute("style", "width: 165px; border: 0;");
        cnt.className = "clearfix";

        const dimg = document.createElement("div");
        dimg.setAttribute("style", "float: left; text-align: center; padding: 4px;");

        const imgc = document.createElement("img");
        imgc.setAttribute("src", "data:image/png;base64," + dataImg);

        dimg.appendChild(imgc);

        cnt.appendChild(dimg);

        const dsnd = document.createElement("div");
        dsnd.setAttribute("style", "float: right;");

        const dbt = document.createElement("button");
        dbt.setAttribute("type", "button");
        dbt.setAttribute("style", "margin-bottom: 2px;");

        let svgc = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svgc.setAttribute("viewBox", "0 0 576 512");
        svgc.setAttribute("width", "12");
        svgc.setAttribute("height", "12");
        svgc.setAttribute("style", "padding-left: 2px;");

        const svgp = document.createElementNS("http://www.w3.org/2000/svg", "path");
        svgp.setAttribute("d", "M215 71.1L126.1 160H24c-13.3 0-24 10.7-24 24v144c0 13.3 10.7 24 24 24h102.1l89 89c15 15 41 4.5 41-17V88c0-21.5-26-32-41-17zm233.3-51.1c-11.2-7.3-26.2-4.2-33.5 7-7.3 11.2-4.2 26.2 7 33.5 66.3 43.5 105.8 116.6 105.8 195.6 0 79-39.6 152.1-105.8 195.6-11.2 7.3-14.3 22.3-7 33.5 7 10.7 21.9 14.6 33.5 7C528.3 439.6 576 351.3 576 256S528.3 72.4 448.4 20zM480 256c0-63.5-32.1-121.9-85.8-156.2-11.2-7.1-26-3.8-33.1 7.5s-3.8 26.2 7.4 33.4C408.3 166 432 209.1 432 256s-23.7 90-63.5 115.4c-11.2 7.1-14.5 22.1-7.4 33.4 6.5 10.4 21.1 15.1 33.1 7.5C447.9 377.9 480 319.5 480 256zm-141.8-76.9c-11.6-6.3-26.2-2.2-32.6 9.5-6.4 11.6-2.2 26.2 9.5 32.6C328 228.3 336 241.6 336 256c0 14.4-8 27.7-20.9 34.8-11.6 6.4-15.8 21-9.5 32.6 6.4 11.7 21.1 15.8 32.6 9.5 28.2-15.6 45.8-45 45.8-76.9s-17.5-61.3-45.8-76.9z");

        svgc.appendChild(svgp);
        dbt.appendChild(svgc);

        dbt.addEventListener("click", function() {
            _this.callSound();
        });

        dsnd.appendChild(dbt);

        const br = document.createElement("br");
        dsnd.appendChild(br);

        const rbt = document.createElement("button");
        rbt.setAttribute("type", "button");

        let svgr = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svgr.setAttribute("viewBox", "0 0 576 512");
        svgr.setAttribute("width", "12");
        svgr.setAttribute("height", "12");
        svgr.setAttribute("style", "padding-left: 2px;");

        const svgl = document.createElementNS("http://www.w3.org/2000/svg", "path");
        svgl.setAttribute("d", "M142.9 142.9c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5c0 0 0 0 0 0H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5c7.7-21.8 20.2-42.3 37.8-59.8zM16 312v7.6 .7V440c0 9.7 5.8 18.5 14.8 22.2s19.3 1.7 26.2-5.2l41.6-41.6c87.6 86.5 228.7 86.2 315.8-1c24.4-24.4 42.1-53.1 52.9-83.7c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.2 62.2-162.7 62.5-225.3 1L185 329c6.9-6.9 8.9-17.2 5.2-26.2s-12.5-14.8-22.2-14.8H48.4h-.7H40c-13.3 0-24 10.7-24 24z");

        svgr.appendChild(svgl);
        rbt.appendChild(svgr);

        rbt.addEventListener("click", function() {
            _this.callLib();
        });

        dsnd.appendChild(rbt);

        cnt.appendChild(dsnd);
        gmc.appendChild(cnt);
    }

    this.writeCaptcha = function(string) {
        gmc.textContent = "";

        const dts = document.createElement("span");
        dts.textContent = string;

        gmc.appendChild(dts);
    }

    this.callLib = function() {
        let xhr = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        if (xhr) {
            xhr.open('POST', path + "public/index.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let datac = xhr.responseText;
                    datac = datac.trim();
                    if (datac !== null && datac !== '') {
                        if (options.type === "graphic") {
                            _this.drawCaptcha(datac);
                        }

                        if (options.type === "text") {
                            _this.writeCaptcha(datac);
                        }
                    }
                }
            }

            let jd = {
                tps: options.type,
                csm: ("extra" in options) ? JSON.stringify(options.extra) : ""
            };

            let data = new URLSearchParams(jd);

            xhr.send(data);
        }
    }

    this.callSound = function() {
        let xhr = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        if (xhr) {
            xhr.open('POST', path + "public/index.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let datac = xhr.responseText;
                    datac = datac.trim();
                    let s = (datac !== null && datac !== '') ? datac : false;
                    if (s !== false) {
                        _this.textToAudio(s);
                    }
                }
            }

            let jd = {
                tps: "sound"
            };

            let data = new URLSearchParams(jd);

            xhr.send(data);
        }
    }

    this.getLoader = function() {
        let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute("viewBox", "0 0 100 100");
        svg.setAttribute("x", "0px");
        svg.setAttribute("y", "0px");
        svg.setAttribute("enable-background", "new 0 0 0 0");
        svg.setAttribute("xml:space", "preserve");
        svg.setAttribute("style","width: 50px; height: 50px; display:inline-block;");

        const svgp = document.createElementNS("http://www.w3.org/2000/svg", "path");
        svgp.setAttribute("fill","#CCC");
        svgp.setAttribute("d","M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50");

        const svga = document.createElementNS("http://www.w3.org/2000/svg", "animateTransform");
        svga.setAttribute("attributeName","transform");
        svga.setAttribute("attributeType","xml");
        svga.setAttribute("type","rotate");
        svga.setAttribute("dur","1s");
        svga.setAttribute("from","0 50 50");
        svga.setAttribute("to","360 50 50");
        svga.setAttribute("repeatCount","indefinite");

        svgp.appendChild(svga);
        svg.appendChild(svgp);

        return svg;
    }

    this.init = function() {
        let base = location.href;
        path = base.substring(0, base.lastIndexOf('/')) + "/";

        const url = new URL(path);
        if (url.protocol !== 'http:' && url.protocol !== "https:") {
            console.log("gmCaptcha initialization failed!");
            return false;
        }

        loadr = this.getLoader();
        gmc.textContent = "";
        gmc.appendChild(loadr);

        this.callLib();
    }

    this.textToAudio = function(sweetSleep) {
        if (SpeechSynthesisUtterance === undefined) {
            const SpeechSynthesisUtterance =
                window.webkitSpeechSynthesisUtterance ||
                window.mozSpeechSynthesisUtterance ||
                window.msSpeechSynthesisUtterance ||
                window.oSpeechSynthesisUtterance ||
                window.SpeechSynthesisUtterance;
        }

        if (typeof sweetSleep !== "undefined") {
            if (SpeechSynthesisUtterance !== undefined) {

                let msg = atob(sweetSleep);

                let meko = msg.split('');
                msg = meko.join('  ');

                let speech = new SpeechSynthesisUtterance();
                speech.lang = options.spellLang;
                            
                speech.text = msg;
                speech.volume = 1;
                speech.rate = 0.7;
                speech.pitch = 0.9;
                        
                window.speechSynthesis.speak(speech);
            } else {
                console.log("Speech not supported by your Browser");
                return false;
            }
        }
    }

    this.init();
}
