define(
    [], function ($) {
        'use strict';
        return function (config) {
            require(
                [], function () {
                    window.EhAPI = window.EhAPI || {};
                    EhAPI.after_load = function () {
                        EhAPI.set_account(config.engagebay_js_api_key, config.engagebay_domain);
                        if(config.engagebay_webpopups === "1") {
                            EhAPI.execute('rules');
                        }
                    };(function (d,s,f) {
                        var sc=document.createElement(s);sc.type='text/javascript';
                        sc.async=true;sc.src=f;var m=document.getElementsByTagName(s)[0];
                        m.parentNode.insertBefore(sc,m);
                    })(document, 'script', '//d2p078bqz5urf7.cloudfront.net/jsapi/ehform.js');
                }
            )
        }
    }
)
