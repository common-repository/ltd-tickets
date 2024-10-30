var $ = jQuery.noConflict();
var _ukds = {
    body: $("body"),
    w: window.innerWidth,
    selectors: {
        nav: "#nav-icon",
        navopen: "nav-open"
    },
    helpers: {
        validators: {
            email: function (val) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(val);
            }
        },
        strings: {
            trim: function (str, len, toWord, suffix) {
                if (str.length <= len) return str;
                toWord = (typeof toWord == "undefined" ? false : toWord);
                suffix = (typeof suffix == "undefined" ? '...' : suffix);
                var newStr = str.substr(0, len);
                if (toWord) newStr = newStr.substr(0, Math.min(newStr.length, newStr.lastIndexOf(" ")));
                if (suffix != false) newStr += suffix;
                return newStr;
            },
            removeFromArray: function (arr) {
                var what, a = arguments, L = a.length, ax;
                while (L > 1 && arr.length) {
                    what = a[--L];
                    while ((ax = arr.indexOf(what)) !== -1) {
                        arr.splice(ax, 1);
                    }
                }
                return arr;
            },
            toCurrency: function (val, fixed) {
                fixed = (typeof fixed == "undefined" ? 2 : fixed);
                val = (typeof val == "string" ? parseFloat(val) : val);
                return '&pound;' + val.toFixed(fixed).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            },
            escapeUnsafe: function (str) {
                if (str == "" || typeof str == "undefined") return "";
                return str
                         .replace(/&/g, "&amp;")
                         .replace(/</g, "&lt;")
                         .replace(/>/g, "&gt;")
                         .replace(/"/g, "&quot;")
                         .replace(/'/g, "&#039;");
            }
        },
        urls: {
            isHash: function (sel) {
                var href = (sel).attr("href").split("/");
                var path = href[href.length - 1];
                var hash = path.split("#");
                return (hash.length > 1);
            },
            queryString: function () {
                var vars = [], hash;
                var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                for (var i = 0; i < hashes.length; i++) {
                    hash = hashes[i].split('=');
                    vars.push(hash[0]);
                    vars[hash[0]] = hash[1];
                }
                return vars;
            }
        },
        random: function () {
            return Math.floor(Math.random() * 9999);
        }

    },
    mailto: false
}


var ui = {
    init: function () {

        $("#ukds-per-page").change(function () {
            $(this).siblings("#ItemsPerPage").val($(this).val());
            $(this).parents("form").submit();
        });
        $("#ukds-order").change(function () {
            $(this).siblings("#ItemOrder").val($(this).val());
            $(this).parents("form").submit();
        });
        if ($.fn.magnificPopup) {
            $('[data-ui="popover"]').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                closeBtnInside: false,
                fixedContentPos: true,
                mainClass: 'mfp-no-margins mfp-with-zoom',
                image: {
                    verticalFit: true
                },
                zoom: {
                    enabled: false,
                    duration: 300
                }
            });

            $('[data-ui="popover-map"]').magnificPopup({
                type: 'iframe',
                closeOnContentClick: true,
                closeBtnInside: false,
                fixedContentPos: true,
                mainClass: 'mfp-no-margins mfp-with-zoom',
                image: {
                    verticalFit: true
                },
                zoom: {
                    enabled: false,
                    duration: 300
                }
            });
            $('.product-gallery-item').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        }
        $("[ukds-src]").each(function () {
            $(this).css('background-image', 'url(' + $(this).attr("ukds-src") + ')');
        })
    },
    direct: {
        makeCopy : function(id) {
            var txt = document.getElementById(id).textContent;
            var inputId = id + "-val";
            $("#" + inputId).remove();
            $("body").append("<input type='text' value='" + txt + "' id='" + inputId + "' style='position:absolute;left:-9999em'  />");
            ui.direct.copy(inputId);
        },
        copy: function (id) {
            var succeed;

            var target = document.getElementById(id);
            var origSelectionStart, origSelectionEnd;

            var currentFocus = document.activeElement;
            target.focus();
            target.setSelectionRange(0, target.value.length);

            try {
                succeed = document.execCommand("copy");
            } catch (e) {
                succeed = false;
            }
            if (currentFocus && typeof currentFocus.focus === "function") {
                currentFocus.focus();
            }

            if (succeed) {
                ui.toast.create("Copied to clipboard!");
            } else {
                ui.toast.create("Unable to copy to clipboard");
            }
        }
    },
    toast: {
        obj: $("<div class='toast-item' style='opacity:0' />"),
        create: function (msg, retain, forceTimeout) {
            forceTimeout = (typeof forceTimeout == "undefined" ? false : forceTimeout);
            var l = $(".toast-item").length;
            var t = (retain && l > 0 ? $(".toast-item") : ui.toast.obj.clone());
            if (retain && l > 0) {
                t.html(_ukds.helpers.strings.escapeUnsafe(msg));
            } else {
                t.append(_ukds.helpers.strings.escapeUnsafe(msg));
                // t.css("transform", "translateY(" + (80*l) + "px)")
                $("body").append(t);
            }
            t.css("opacity", 1);
            if (!retain || forceTimeout) {
                setTimeout(function () {
                    t.animate({ "opacity": 0 }, 300, function () {
                        t.remove();
                    });
                }, 2000);
            }
        }
    }

}

$(function () {
    ui.init();
});