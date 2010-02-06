(function ($) {
	$.textLabels = function(){
		return this;
	}
	$.fn.removeAttrs = function(attr){
		var attrs = attr.split(' ');
		return $(this).each(function(){
			 var item = $(this);
			_.each(attrs, function(k){
				item.removeAttr(k);
			});
		});
		
	};

    $.live = function (selector, type, fn) {
        var r = $([]);
        r.selector = selector;
        if (type && fn) {
            r.live(type, fn);
        }
        return r;
    };

    $.fn.disable = function () {
        return this.each(function () {
            $(this).attr('disabled', 'disabled');
        });
    };
    $.fn.enable = function () {
        return this.each(function () {
            $(this).removeAttr('disabled');
        });
    };

	$.smoothScroll = function() {
		$("a[href^=#][href!=#]").live('click',function(e){
		    $('html,body').animate({'scrollTop': $($(this).attr('href')).offset().top+'px'}, 1000); 
		    e.preventDefault();
		});
	};


	$.checkOptions = function(opt){
        if (typeof(opt) === 'string') {
            var options = $.extend({}, {
                text: opt
            });		 
		return options;
        }
		return opt;
	};
    $.success = function (opt) {
		var options = $.checkOptions(opt);
        options = $.extend(options, {
            type: 'success'
        });
        $.noticeAdd(options,'success');
    };
    $.error = function (opt) {
		var options = $.checkOptions(opt);
        options = $.extend(options,
        {
            type: 'error'
        });
        $.noticeAdd(options,'error');
    };
    $.warn = function (opt) {
		var options = $.checkOptions(opt);
        options = $.extend(options,
 		{
            type: 'warning'
        });
        $.noticeAdd(options,'warn');
    };
    $.notice = $.system = $.info = function (opt) {
		var options = $.checkOptions(opt);
        $.noticeAdd(options,'notice');
    };
    $.noticeAdd = function (opt, via) {              
		//console.log('Called via shorthand', via, 'with opt', opt);
        var defaults = {
            inEffect: {
                height: 'toggle'
            },
            // in effect
            inEffectDuration: 200,
            // in effect duration in miliseconds
            stayTime: 2000,
            // time in miliseconds before the item has to disappear
            text: '',
            // content of the item
            stay: false,
            // should the notice item stay or not?
            type: 'notice',
            // could also be error, success or warning
            icon: null
        };
        // declare varaibles
        var options, noticeWrapAll, pre, noticeItemOuter, noticeItemInner, noticeItemClose;
        options = $.extend({},
        defaults, opt);
        if (options.type === 'error') {
            pre = 'error';
            options.stay = true;
        } else {
            pre = 'notice';
        }
        noticeWrapAll = (!$('.' + pre + '-wrap').length) ? $('<div></div>').addClass(pre + '-wrap').prependTo('body') : $('.' + pre + '-wrap');
        noticeItemOuter = $('<div></div>').addClass(pre + '-item-wrapper');
        noticeItemInner = $('<div></div>').hide().addClass(pre + '-item ' + options.type).appendTo(noticeWrapAll).html('<p><span class="ui-icon"></span>' + options.text + '</p>').animate(options.inEffect, options.inEffectDuration).wrap(noticeItemOuter);
        /* noticeItemClose = $('<div></div>').addClass(pre + '-item-close').prependTo(noticeItemInner).html('x').bind('click', function () {
            $.hideSlow(noticeItemInner);
        });*/
        // hmmmz, zucht
        if (navigator.userAgent.match(/MSIE 6/i)) {
            noticeWrapAll.css({
                top: document.documentElement.scrollTop
            });
        }
        if (!options.stay) {
            setTimeout(function () {
                $.hideSlow(noticeItemInner);
            },
            options.stayTime);
        }
    };
    $.hideSlow = $.noticeRemove = function (obj) {
        obj.animate({
            opacity: '0'
        },
        100, function () {
            obj.parent().animate({
                height: '0'
            },
            100, function () {
                obj.parent().remove();
                if (obj.attr('id') === 'dialog') {
                    $('body').css('overflow', 'auto');
                    $('.dialog-wrap').animate({
                        opacity: '0'
                    },
                    200, function () {
                        $(this).remove();
                    });
                }
            });
        });
    };
    $.fn.dialogBox = function(opt){
	  	return this.each(function(){
			new $.dialogBox($.extend({},opt,{text: $(this)}));
		});
	};
    $.dialogBox = function (opt) {
        var defaults = {
            inEffect: {
                opacity: 1
            },
            // in effect
            inEffectDuration: 100,
            // in effect duration in miliseconds
            stayTime: 3000,
            // time in miliseconds before the item has to disappear
            measurement: 'px',
            // width measurement could be 'px' or '%'
            width: 250,
            // width size
            padding: 8,
            // value of padding left and right combined
            text: '',
            // content of the item
            onload: null,
            // script of the content
            onclose: null
        };
        // declare variables
        var options, dialogWrapAll, dialogItemOuter, dialogItemInner;
        options = $.extend({},
        defaults, opt);
        dialogWrapAll = (!$('.dialog-wrap').length) ? $('<div></div>').addClass('dialog-wrap').appendTo('body') : $('.dialog-wrap');
        dialogItemPosition = $('<div></div>').addClass('dialog-item-position').css({
            'width': options.width + options.measurement
        });
        dialogItemOuter = $('<div></div>').addClass('dialog-item-wrapper').css('width', options.width + options.measurement);
        dialogContent = $('<div></div>').addClass('dialog-item-content').css({
            'width': (options.width - options.padding) + options.measurement,
            'margin-left': (options.padding / 2) + options.measurement
        });
        dialogItemInner = $('<div></div>').css('opacity', 0).attr('id', 'dialog').addClass('dialog-item');
        if (typeof(options.text) === 'object') {
            dialogItemInner.append(options.text);
        } else {
            dialogItemInner.html(options.text);
        }
        dialogItemInner.appendTo(dialogWrapAll).wrap(dialogItemPosition).wrap(dialogItemOuter).wrap(dialogContent);
        $('body').css('overflow', 'hidden');
        $('<div></div>').addClass('dialog-wrap ' + ($.support.opacity ? 'overlay' : 'ie') + ' unselect').appendTo('body');
        if (navigator.userAgent.match(/MSIE 6/i)) {
            dialogWrapAll.css({
                top: document.documentElement.scrollTop
            });
        }
        if (options.onload) {
            options.onload(dialogItemInner, options.onclose);
        }
        $.dialogPosition();
        dialogItemInner.animate(options.inEffect, options.inEffectDuration);
        // Bind the escape button to close dialog
        $(document).bind('keydown', function (e) {
            var code = e.keyCode;
            if (code === 27) {
                $.dialogRemove(dialogItemInner, options.onclose);
            }
        });
        window.onresize = function () {
            $.dialogPosition();
        };
    };
    $.dialogRemove = function (obj, close) {
        if (typeof(close) === 'function') {
            close();
        }
        $.hideSlow(obj);
    };
    $.dialogPosition = function () {
        var num = Math.round($(window).height() / 2) - ($('.dialog-item-wrapper').height() / 2);
        if (num < 0) {
            num = 0;
        } else if (num > 175) {
            num = 175;
        }
        $('.dialog-item-position').css('margin-top', num + 'px');
    };
    $.fn.confirm = function (opt, str) {
        return this.each(function () {
            (new $.confirm(this, opt, str));
        });
    };
    $.alert = function (text) {
        var element = $(el);
        $.dialogBox({
            width: 300,
            text: text,
            onload: function (confirm) {
                $(document).bind('keydown click', function (e) {
                    $.hideSlow(confirm);
                });
            }
        });
    };
    $.confirm = function (el, opt, str) {
        var element = $(el);
        var strings = $.extend({},
        {
            'header': 'Warning',
            'text': 'Are you sure?',
            'dont': 'CANCEL',
            'ok': 'Ok'
        },
        str);
        var options = $.extend({},
        {
            action: null,
            cancel: null
        },
        opt);
        $.dialogBox({
            width: 300,
            text: '<h2>' + strings.header + '</h2>' + '<p>' + strings.text + '</p>' + '<div class="line buttons">' + '<button type="button" id="cancel_delete" class="cancel"><span>' + strings.dont + '</span></button>' + '<button type="button" id="confirm_delete"><span>' + strings.ok + '</span></button>' + '</div>',
            onload: function (confirm) {
                $(document).bind('keydown', function (e) {
                    var code = e.keyCode;
                    if (code === 13) {
                        $('#confirm_delete').trigger('click');
                    }
                });
                $('#cancel_delete').bind('click', function (e) {
                    if (typeof(options.cancel) === 'function') {
                        options.cancel(element);
                    }
                    $(document).unbind('keydown');
                    $.hideSlow(confirm);
                });
                $('#confirm_delete').bind('click', function (e) {
                    options.action(element);
                    $(document).unbind('keydown');
                    $.hideSlow(confirm);
                }).focus();
            }
        });
    };


    // 
    // Cookie plugin
    // 
    // Copyright (c) 2006 Klaus Hartl (stilbuero.de)
    // Dual licensed under the MIT and GPL licenses:
    // http://www.opensource.org/licenses/mit-license.php
    // http://www.gnu.org/licenses/gpl.html
    // 
    // 
    $.cookie = function (name, value, options) {
        if (typeof value != 'undefined') { // name and value given, set cookie
            options = options || {};
            if (value === null) {
                value = '';
                options.expires = -1;
            }
            var expires = '';
            if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
                var date;
                if (typeof options.expires == 'number') {
                    date = new Date();
                    date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
                } else {
                    date = options.expires;
                }
                expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
            }
            // CAUTION: Needed to parenthesize options.path and options.domain
            // in the following expressions, otherwise they evaluate to undefined
            // in the packed version for some reason...
            var path = options.path ? '; path=' + (options.path) : '';
            var domain = options.domain ? '; domain=' + (options.domain) : '';
            var secure = options.secure ? '; secure' : '';
            document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
        } else { // only name given, get cookie
            var cookieValue = null;
            if (document.cookie && document.cookie !== '') {
                var cookies = document.cookie.split(';');
                for (var i = 0; i < cookies.length; i++) {
                    var cookie = jQuery.trim(cookies[i]);
                    // Does this cookie string begin with the name we want?
                    if (cookie.substring(0, name.length + 1) == (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }
    };
    // 
    // jQuery JSON Plugin
    // version: 1.0 (2008-04-17)
    // 
    // This document is licensed as free software under the terms of the
    // MIT License: http://www.opensource.org/licenses/mit-license.php
    // 
    // Brantley Harris technically wrote this plugin, but it is based somewhat
    // on the JSON.org website's http://www.json.org/json2.js, which proclaims:
    // "NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.", a sentiment that
    // I uphold. I really just cleaned it up.
    // 
    // It is also based heavily on MochiKit's serializeJSON, which is 
    // copywrited 2005 by Bob Ippolito.
    // 

    function toIntegersAtLease(n)
    // Format integers to have at least two digits.
    {
        return n < 10 ? '0' + n : n;
    }
    Date.prototype.toJSON = function (date)
    // Yes, it polutes the Date namespace, but we'll allow it here, as
    // it's damned usefull.
    {
        return this.getUTCFullYear() + '-' + toIntegersAtLease(this.getUTCMonth()) + '-' + toIntegersAtLease(this.getUTCDate());
    };
    var escapeable = /["\\\x00-\x1f\x7f-\x9f]/g;
    var meta = { // table of character substitutions
        '\b': '\\b',
        '\t': '\\t',
        '\n': '\\n',
        '\f': '\\f',
        '\r': '\\r',
        '"': '\\"',
        '\\': '\\\\'
    };
    $.quoteString = function (string)
    // Places quotes around a string, inteligently.
    // If the string contains no control characters, no quote characters, and no
    // backslash characters, then we can safely slap some quotes around it.
    // Otherwise we must also replace the offending characters with safe escape
    // sequences.
    {
        if (escapeable.test(string)) {
            return '"' + string.replace(escapeable, function (a) {
                var c = meta[a];
                if (typeof c === 'string') {
                    return c;
                }
                c = a.charCodeAt();
                return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
            }) + '"';
        }
        return '"' + string + '"';
    };
    $.toJSON = function (o, compact) {
        var type = typeof(o);
        var ret = [];
        if (type == "undefined") {
            return "undefined";
        } else if (type == "number" || type == "boolean") {
            return o + "";
        } else if (o === null) {
            return "null";
        }
        // Is it a string?
        if (type == "string") {
            return $.quoteString(o);
        }
        // Does it have a .toJSON function?
        if (type == "object" && typeof o.toJSON == "function") {
            return o.toJSON(compact);
        }
        // Is it an array?
        if (type != "function" && typeof(o.length) == "number") {
            for (var i = 0; i < o.length; i++) {
                ret.push($.toJSON(o[i], compact));
            }
            if (compact) {
                return "[" + ret.join(",") + "]";
            } else {
                return "[" + ret.join(", ") + "]";
            }
        }
        // If it's a function, we have to warn somebody!
        if (type == "function") {
            throw new TypeError("Unable to convert object of type 'function' to json.");
        }
        // It's probably an object, then.
        for (var k in o) {
            var name;
            type = typeof(k);
            if (type == "number") {
                name = '"' + k + '"';
            } else if (type == "string") {
                name = $.quoteString(k);
            } else {
                continue; //skip non-string or number keys
            }
            var val = $.toJSON(o[k], compact);
            if (typeof(val) != "string") {
                // skip non-serializable values
                continue;
            }
            if (compact) {
                ret.push(name + ":" + val);
            } else {
                ret.push(name + ": " + val);
            }
        }
        return "{" + ret.join(", ") + "}";
    };
    $.compactJSON = function (o) {
        return $.toJSON(o, true);
    };
    $.evalJSON = function (src)
    // Evals JSON that we know to be safe.
    {
        return eval("(" + src + ")");
    };
    $.secureEvalJSON = function (src)
    // Evals JSON in a way that is *more* secure.
    {
        var filtered = src;
        filtered = filtered.replace(/\\["\\\/bfnrtu]/g, '@');
        filtered = filtered.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
        filtered = filtered.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
        if (/^[\],:{}\s]*$/.test(filtered)) {
            return eval("(" + src + ")");
        } else {
            throw new SyntaxError("Error parsing JSON, source is not valid.");
        }
    };
    $.fn.maxLength = function (max) {
        this.each(function () {
            var type = this.tagName.toLowerCase();
            var inputType = this.type ? this.type.toLowerCase() : null;
            // if its a input field, we can just set its maxLength to max
            if (type == "input" && inputType == "text" || inputType == "password") {
                this.maxLength = max;
                // if however its a textarea, we need a bit of extra magic
            } else if (type == "textarea") {
                this.onkeypress = function (e) {
                    var ob = e | window.event;
                    var keyCode = ob.keyCode;
                    var hasSelection = document.selection ? document.selection.createRange().text.length > 0 : this.selectionStart != this.selectionEnd;
                    return ! (this.value.length >= max && (keyCode > 50 || keyCode == 32 || keyCode === 0 || keyCode == 13) && !ob.ctrlKey && !ob.altKey && !hasSelection);
                };
                this.onkeyup = function () {
                    if (this.value.length > max) {
                        this.value = this.value.substring(0, max);
                    }
                };
            }
        });
    };
    $.fn.alphanumeric = function (p) {
        p = $.extend({
            ichars: "!@#$%^&*()+=[]\\\';,/{}|\":<>?~`.- ",
            nchars: "",
            allow: ""
        },
        p);
        return this.each(

        function () {
            if (p.nocaps) {
                p.nchars += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            }
            if (p.allcaps) {
                p.nchars += "abcdefghijklmnopqrstuvwxyz";
            }
            var s = p.allow.split('');
            for (i = 0; i < s.length; i++) {
                if (p.ichars.indexOf(s[i]) != -1) {
                    s[i] = "\\" + s[i];
                }
            }
            p.allow = s.join('|');
            var reg = new RegExp(p.allow, 'gi');
            var ch = p.ichars + p.nchars;
            ch = ch.replace(reg, '');
            $(this).keypress(

            function (e) {
                if (!e.charCode) {
                    k = String.fromCharCode(e.which);
                } else {
                    k = String.fromCharCode(e.charCode);
                }
                if (ch.indexOf(k) != -1) {
                    e.preventDefault();
                }
                if (e.ctrlKey && k == 'v') {
                    e.preventDefault();
                }
            });
            $(this).bind('contextmenu', function () {
                return false;
            });
        });
    };
    $.fn.numeric = function (p) {
        var az = "abcdefghijklmnopqrstuvwxyz";
        az += az.toUpperCase();
        p = $.extend({
            nchars: az
        },
        p);
        return this.each(function () {
            $(this).alphanumeric(p);
        });
    };
    $.fn.alpha = function (p) {
        var nm = "1234567890";
        p = $.extend({
            nchars: nm
        },
        p);
        return this.each(function () {
            $(this).alphanumeric(p);
        });
    };  
})(jQuery);
/*
(c) Copyrights 2007 - 2008

Original idea by by Binny V A, http://www.openjs.com/scripts/events/keyboard_shortcuts/
 
jQuery Plugin by Tzury Bar Yochay 
tzury.by@gmail.com
http://evalinux.wordpress.com
http://facebook.com/profile.php?id=513676303

Project's sites: 
http://code.google.com/p/js-hotkeys/
http://github.com/tzuryby/hotkeys/tree/master

License: same as jQuery license. 

USAGE:
 // simple usage
 $(document).bind('keydown', 'Ctrl+c', function(){ alert('copy anyone?');});
 
 // special options such as disableInIput
 $(document).bind('keydown', {combi:'Ctrl+x', disableInInput: true} , function() {});
 
Note:
 This plugin wraps the following jQuery methods: $.fn.find, $.fn.bind and $.fn.unbind
 
*/
(function (jQuery) {
    // keep reference to the original $.fn.bind and $.fn.unbind
    jQuery.fn.__bind__ = jQuery.fn.bind;
    jQuery.fn.__unbind__ = jQuery.fn.unbind;
    jQuery.fn.__find__ = jQuery.fn.find;
    var hotkeys = {
        version: '0.7.8',
        override: /keydown|keypress|keyup/g,
        triggersMap: {},
        specialKeys: {
            27: 'esc',
            9: 'tab',
            32: 'space',
            13: 'return',
            8: 'backspace',
            145: 'scroll',
            20: 'capslock',
            144: 'numlock',
            19: 'pause',
            45: 'insert',
            36: 'home',
            46: 'del',
            35: 'end',
            33: 'pageup',
            34: 'pagedown',
            37: 'left',
            38: 'up',
            39: 'right',
            40: 'down',
            112: 'f1',
            113: 'f2',
            114: 'f3',
            115: 'f4',
            116: 'f5',
            117: 'f6',
            118: 'f7',
            119: 'f8',
            120: 'f9',
            121: 'f10',
            122: 'f11',
            123: 'f12'
        },
        shiftNums: {
            "`": "~",
            "1": "!",
            "2": "@",
            "3": "#",
            "4": "$",
            "5": "%",
            "6": "^",
            "7": "&",
            "8": "*",
            "9": "(",
            "0": ")",
            "-": "_",
            "=": "+",
            ";": ":",
            "'": "\"",
            ",": "<",
            ".": ">",
            "/": "?",
            "\\": "|"
        },
        newTrigger: function (type, combi, callback) {
            // i.e. {'keyup': {'ctrl': {cb: callback, disableInInput: false}}}
            var result = {};
            result[type] = {};
            result[type][combi] = {
                cb: callback,
                disableInInput: false
            };
            return result;
        }
    };
    // add firefox num pad char codes
    if (jQuery.browser.mozilla) {
        hotkeys.specialKeys = jQuery.extend(hotkeys.specialKeys, {
            96: '0',
            97: '1',
            98: '2',
            99: '3',
            100: '4',
            101: '5',
            102: '6',
            103: '7',
            104: '8',
            105: '9'
        });
    }
    // a wrapper around of $.fn.find 
    // see more at: http://groups.google.com/group/jquery-en/browse_thread/thread/18f9825e8d22f18d
    jQuery.fn.find = function (selector) {
        this.query = selector;
        return jQuery.fn.__find__.apply(this, arguments);
    };
    jQuery.fn.unbind = function (type, combi, fn) {
        if (jQuery.isFunction(combi)) {
            fn = combi;
            combi = null;
        }
        if (combi && typeof combi === 'string') {
            var selectorId = ((this.prevObject && this.prevObject.query) || (this[0].id && this[0].id) || this[0]).toString();
            var hkTypes = type.split(' ');
            for (var x = 0; x < hkTypes.length; x++) {
                delete hotkeys.triggersMap[selectorId][hkTypes[x]][combi];
            }
        }
        // call jQuery original unbind
        return this.__unbind__(type, fn);
    };
    jQuery.fn.bind = function (type, data, fn) {
        // grab keyup,keydown,keypress
        var handle = type.match(hotkeys.override);
        if (jQuery.isFunction(data) || !handle) {
            // call jQuery.bind only
            return this.__bind__(type, data, fn);
        } else {
            // split the job
            var result = null,
            // pass the rest to the original $.fn.bind
            pass2jq = jQuery.trim(type.replace(hotkeys.override, ''));
            // see if there are other types, pass them to the original $.fn.bind
            if (pass2jq) {
                // call original jQuery.bind()
                result = this.__bind__(pass2jq, data, fn);
            }
            if (typeof data === "string") {
                data = {
                    'combi': data
                };
            }
            if (data.combi) {
                for (var x = 0; x < handle.length; x++) {
                    var eventType = handle[x];
                    var combi = data.combi.toLowerCase(),
                        trigger = hotkeys.newTrigger(eventType, combi, fn),
                        selectorId = ((this.prevObject && this.prevObject.query) || (this[0].id && this[0].id) || this[0]).toString();
                    //trigger[eventType][combi].propagate = data.propagate;
                    trigger[eventType][combi].disableInInput = data.disableInInput;
                    // first time selector is bounded
                    if (!hotkeys.triggersMap[selectorId]) {
                        hotkeys.triggersMap[selectorId] = trigger;
                    }
                    // first time selector is bounded with this type
                    else if (!hotkeys.triggersMap[selectorId][eventType]) {
                        hotkeys.triggersMap[selectorId][eventType] = trigger[eventType];
                    }
                    // make trigger point as array so more than one handler can be bound
                    var mapPoint = hotkeys.triggersMap[selectorId][eventType][combi];
                    if (!mapPoint) {
                        hotkeys.triggersMap[selectorId][eventType][combi] = [trigger[eventType][combi]];
                    } else if (mapPoint.constructor !== Array) {
                        hotkeys.triggersMap[selectorId][eventType][combi] = [mapPoint];
                    } else {
                        hotkeys.triggersMap[selectorId][eventType][combi][mapPoint.length] = trigger[eventType][combi];
                    }
                    // add attribute and call $.event.add per matched element
                    this.each(function () {
                        // jQuery wrapper for the current element
                        var jqElem = jQuery(this);
                        // element already associated with another collection
                        if (jqElem.attr('hkId') && jqElem.attr('hkId') !== selectorId) {
                            selectorId = jqElem.attr('hkId') + ";" + selectorId;
                        }
                        jqElem.attr('hkId', selectorId);
                    });
                    result = this.__bind__(handle.join(' '), data, hotkeys.handler);
                }
            }
            return result;
        }
    };
    // work-around for opera and safari where (sometimes) the target is the element which was last 
    // clicked with the mouse and not the document event it would make sense to get the document
    hotkeys.findElement = function (elem) {
        if (!jQuery(elem).attr('hkId')) {
            if (jQuery.browser.opera || jQuery.browser.safari) {
                while (!jQuery(elem).attr('hkId') && elem.parentNode) {
                    elem = elem.parentNode;
                }
            }
        }
        return elem;
    };
    // the event handler
    hotkeys.handler = function (event) {
        var target = hotkeys.findElement(event.currentTarget),
            jTarget = jQuery(target),
            ids = jTarget.attr('hkId');
        if (ids) {
            ids = ids.split(';');
            var code = event.which,
                type = event.type,
                special = hotkeys.specialKeys[code],
            // prevent f5 overlapping with 't' (or f4 with 's', etc.)
            character = !special && String.fromCharCode(code).toLowerCase(),
                shift = event.shiftKey,
                ctrl = event.ctrlKey,
            // patch for jquery 1.2.5 && 1.2.6 see more at: 
            // http://groups.google.com/group/jquery-en/browse_thread/thread/83e10b3bb1f1c32b
            alt = event.altKey || event.originalEvent.altKey,
                mapPoint = null;
            for (var x = 0; x < ids.length; x++) {
                if (hotkeys.triggersMap[ids[x]][type]) {
                    mapPoint = hotkeys.triggersMap[ids[x]][type];
                    break;
                }
            }
            //find by: id.type.combi.options 
            if (mapPoint) {
                var trigger;
                // event type is associated with the hkId
                if (!shift && !ctrl && !alt) { // No Modifiers
                    trigger = mapPoint[special] || (character && mapPoint[character]);
                } else {
                    // check combinations (alt|ctrl|shift+anything)
                    var modif = '';
                    if (alt) {modif += 'alt+';}
                    if (ctrl) {modif += 'ctrl+';}
                    if (shift) {modif += 'shift+';}
                    // modifiers + special keys or modifiers + character or modifiers + shift character or just shift character
                    trigger = mapPoint[modif + special];
                    if (!trigger) {
                        if (character) {
                            trigger = mapPoint[modif + character] || mapPoint[modif + hotkeys.shiftNums[character]]
                            // '$' can be triggered as 'Shift+4' or 'Shift+$' or just '$'
                            || (modif === 'shift+' && mapPoint[hotkeys.shiftNums[character]]);
                        }
                    }
                }
                if (trigger) {
                    var result = false;
                    for (var y = 0; y < trigger.length; y++) {
                        if (trigger[y].disableInInput) {
                            // double check event.currentTarget and event.target
                            var elem = jQuery(event.target);
                            if (jTarget.is("input") || jTarget.is("textarea") || elem.is("input") || elem.is("textarea")) {
                                return true;
                            }
                        }
                        // call the registered callback function
                        result = result || trigger[y].cb.apply(this, [event]);
                    }
                    return result;
                }
            }
        }
    };
    // place it under window so it can be extended and overridden by others
    window.hotkeys = hotkeys;
    return jQuery;
})(jQuery);

(function($) {
		var self = null;
		$.fn.siteSearch = function(settings, site) {
			return $.each(this,
			function(i, el) {
				new $.siteSearch(el, settings, site)
			})
		};
		$.siteSearch = function(element, settings, site) {
			this.cache = [];
			this.chosen = 0;
			this.scores = [];
			this.score = [];
			this.rows = [];
			this.items = [];
			this.site = site;
			this.markup = [];
//			this.loc = document.location.protocol + '//' + document.location.host + document.location.pathname;
			this.settings = $.extend({
				"id": "tagsearch",
				"name": "tagsearch",
				"field": "insert",
				"location": "aside",
				"label": " ",
				"noresults": "",
				"force": true,
				"limit": 10
			},
			settings);  
			this.markup.push('<div id="'+this.settings.id+'">');
			this.markup.push('<div class="search_results">');
			this.markup.push('<ul id="search_list">');
			this.markup.push('<\/ul><\/div></div>');
			$(this.markup.join('')).appendTo('#holder');
			this.init()
		};
		$.siteSearch.prototype = {
			init: function() {
				var self = this;
				this.container = $('#' + self.settings.id + ' .search_results');
				this.field = $('#'+this.settings.field);
				this.list = $('#search_list');
				this.setupCache();
				this.list.bind('listUpdate', function(){
				   	self.setupCache();
				});
				this.field.live('keypress', function(ev){
					if(ev.keyCode === 9 || ev.keyCode === 13){
						ev.preventDefault();
						ev.stopPropagation();					
					}				
				});

				this.field.live('keydown', function(e) {
					self.handleKeys(e)
				});
				$('body').click(function(){
					self.container.hide(); 
				});
				self.container.hide();
				var search_from = location.search;
				if (search_from && search_from.substring(0, 7) === '?topic=') {
					var string = location.search.substring(7);
					self.field.val(string);
					self.filter();
					self.container.hide();

					if ($(self.list).find('li.selected').length) {
//						self.site.loadSiteItem($(self.list).find('li.selected > a').attr('name').substring(1));
						location.search = '';
					}else{
//						self.site.loadSiteItem($('#navigation a:first').attr('name'));
					}
					
					$('body').click(function(e) {
						self.container.hide()
					});
				   }
					$('.search_results li').live('click',
						function(e) {
						self.field.val($(this).text());
							
						self.container.hide();
					});
				},
				setupCache: function() {
					var self = this;
					self.items = $('aside').find('li');
					$('aside').find('li').each(function() {
						self.cache.push($(this).text().toLowerCase()+'')
					});
					self.cache_length = self.cache.length         
					//console.log('cache', self.cache, self.cache.length);
				},
				filter: function() {					
					if ($.trim(this.field.val()) === '') {
						this.list.find('li').show();
						this.container.hide();
						return true;
					}
					var trigger = this.field.val().substr(0,1); 
					if($.inArray(trigger, $(document).data('codes')) !== -1){
						this.displayResults(this.getScores(this.field.val()))						
					}
//					console.log('FILTER !! SCORES', this.getScores(this.field.val()), 'trigger:', trigger, 'in ? ', $.inArray(trigger, $(document).data('codes')), $(document).data('codes'));					
				},
				displayResults: function(scores) {
					var self = this;
					this.scores = [];
					this.chosen = 0;
					$('#search_list').empty();
					if (scores.length > 0) {
						$.each(scores,
						function(i, score) {    
							if(i<self.settings.limit){
							var item = $('aside').find('li:eq(' + score[1] + ')');
							var html = '<li class="result"><span>' + $(item).text() + '<\/span><\/li>';
							$(html).appendTo('#search_list')
							}
						});
						self.list.find('li.selected').removeClass('selected');
						self.list.find('li:first').addClass('selected')
					} else {
						var html = '<li><div class="desc">' + self.settings.noresults + '<\/div><\/li>';
						$(html).appendTo('#search_list')
					}
					$(self.container).show()
				},
				getScores: function(term) {
					var scores = [];
					while (this.cache_length--) {
						var score = this.cache[this.cache_length].score(term);
						if (score > 0) {
							scores.push([score, this.cache_length])
						}
					}
					this.cache_length = this.cache.length;
					return scores.sort(function(a, b) {
						return b[0] - a[0]
					})
				},
				handleKeys: function(e) {
					if(e.ctrlKey){
						return false;
					}
					var self = this;
					switch (e.keyCode) {
					case 40:
						self.nextResult();
						return false;
					case 38:
						self.prevResult();
						return false;
					case 224:
					case 17:
					case 16:
					case 18:
						return true;
					case 8:
						if (self.field.value === '') {
							self.filter()
						}
						self.filter();
						break;
					case 9:
					case 13:   
						e.preventDefault();
						e.stopPropagation();											
						var sel = self.list.find('li.selected');
						if(sel.length && sel.is(':visible')){
							self.field.val($(self.list).find('li.selected').text());								
						}
						break;
					case 188:
						return true;
					case 27:
						return true;
					case 32:
						return true;
					default:
							self.filter();
						break
					}
					self.filter();					
				},
				nextResult: function() {
					if (this.chosen == (this.scores.length - 1)) {
						this.chosen = 0
					} else {
						this.chosen++
					}
					this.list.find('li.selected').removeClass('selected').next().addClass('selected')
				},
				prevResult: function() {
					if (this.chosen === 0) {
						this.chosen = this.scores.length
					}
					this.list.find('li.selected').removeClass('selected').prev().addClass('selected');
					this.chosen--
				}
			}
		})(jQuery);
		String.prototype.score = function(abbreviation, offset) {
			offset = offset || 0;
			if (abbreviation.length === 0) {
				return 0.9
			}
			if (abbreviation.length > this.length) {
				return 0.0
			}
			for (var i = abbreviation.length; i > 0; i--) {
				var sub_abbreviation = abbreviation.substring(0, i);
				var index = this.indexOf(sub_abbreviation);
				if (index < 0) {
					continue
				}
				if (index + abbreviation.length > this.length + offset) {
					continue
				}
				var next_string = this.substring(index + sub_abbreviation.length);
				var next_abbreviation = null;
				if (i >= abbreviation.length) {
					next_abbreviation = ''
				} else {
					next_abbreviation = abbreviation.substring(i)
				}
				var remaining_score = next_string.score(next_abbreviation, offset + index);
				if (remaining_score > 0) {
					var score = this.length - next_string.length;
					if (index !== 0) {
						var c = this.charCodeAt(index - 1);
						if (c == 32 || c == 9) {
							for (var j = (index - 2); j >= 0; j--) {
								c = this.charCodeAt(j);
								score -= ((c == 32 || c == 9) ? 1 : 0.15)
							}
						} else {
							score -= index
						}
					}
					score += remaining_score * next_string.length;
					score /= this.length;
					return score
				}
			};
			return 0.0
};


/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 */

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};
