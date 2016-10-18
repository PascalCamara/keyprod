var $ = jQuery;

$(document).ready(function() {

    var fakeLoader = {
        template : '<div id="fakeLoader"></div>',
        mounted : function() {
            $("#fakeLoader").fakeLoader({
                timeToHide: 9999999999999, //Time in milliseconds for fakeLoader disappear
                zIndex:"0",//Default zIndex
                spinner:"spinner9",//Options: 'spinner1', 'spinner2', 'spinner3', 'spinner4', 'spinner5', 'spinner6', 'spinner7'
                bgColor:"rgba(2, 117, 216, 0.30)", //Hex, RGB or RGBA colors
            });
        }
    };

    var checkingArray = {
        props : ['rapports'],
        template : '<table class="table">'
        +'<thead>'
            +'<tr>'
                +'<th>#</th>'
                +'<th>State</th>'
                +'<th>Description</th>'
                +'<th>Trello</th>'
            +'</tr>'
        +'</thead>'
        +'<tbody>'
            +'<tr :for="rapport in rapports">'
                +'<th scope="row">{{ rapport.id }}</th>'
                +'<td>{{ rapport.state}}</td>'
                +'<td>{{ rapport.description}}</td>'
                + '<td>'
                    +'<div class="checkbox">'
                    +'<input type="checkbox" value="">'
                    +'</div>'
                +'</td>'
            +'</tr>'
        +'</tbody>'
        +'</table>'
    };

    var keyprodApp = new Vue({
        el: '#keyprod-app',
        components : {
            fakeLoader : fakeLoader,
            checkingArray: checkingArray
        },
        data: {
            'launch' : false,
            'loading': false,
            'rapports' : false,
            'displayChecking' : false
        },
        methods : {
            start: function () {
                var self = this;
                self.launch = true;
                // self.loading = true;

                var data = {};
                data.action = 'launch_test';

                self.rapports = [{'id' : 1 , 'state': 1, 'description' : "blabla"}];
                self.displayChecking = true;
                // $.post(keyprod_ajax_url.ajax_url, data, function (response) {
                //     self.loading = false;
                //     self.rapports = JSON.parse(response);
                //     self.displayChecking = true;
                //     console.log(self.rapports);
                // })
            },
            progress: function () {

            }
        }
    });



});

