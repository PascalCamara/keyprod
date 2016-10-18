var $ = jQuery;

$(document).ready(function() {
    //var appended = $("body").append('');
    //var fakeLoader = $(appended).find("#fakeLoader");

    //$(fakeLoader).fakeLoader();
    Vue.component('fakeloader', {
        template : '<div id="fakeLoader"></div>',
        mounted : function() {
            $("#fakeLoader").fakeLoader({
                timeToHide: 9999999999999, //Time in milliseconds for fakeLoader disappear
                zIndex:"0",//Default zIndex
                spinner:"spinner9",//Options: 'spinner1', 'spinner2', 'spinner3', 'spinner4', 'spinner5', 'spinner6', 'spinner7'
                bgColor:"rgba(2, 117, 216, 0.30)", //Hex, RGB or RGBA colors
            });
        }
    });
    var keyprodApp = new Vue({
        el: '#keyprod-app',
        data: {
            'launch' : false
        },
        methods : {
            start: function () {
                var self = this;
                self.launch = true;
                
                var data = {};
                data.action = 'launch_test';
                $.post(keyprod_ajax_url.ajax_url, data, function (response) {
                    console.log(response);
                    self.launch = false;
                })
            },
            progress: function () {

            }
        }
    });



});

