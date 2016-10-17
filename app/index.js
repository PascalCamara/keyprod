var $ = jQuery;
$(document).ready(function() {

    var keyprodApp = new Vue({
        el: '#keyprod-app',
        data: {
            'launch' : false
        },
        methods : {
            start : function () {
                this.launch = true;
                var data = {};
                data.action = 'launch_test';
                $.post(keyprod_ajax_url.ajax_url, data, function(response) {
                    console.log(response);
                })
            },
            progress : function () {

            }
        }
    });

});

