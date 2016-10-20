var $ = jQuery;
console.log(trello_token);
var trello_token = typeof (trello_token) === "string" ? trello_token : false;


var authenticationSuccess = function() { console.log("Trello Success"); };
var authenticationFailure = function() { console.log("Trello Fail"); };
if (trello_token) {
    Trello.authorize({
        type: 'popup',
        name: 'Getting Started Application',
        scope: {
            read: 'true',
            write: 'true' },
        expiration: 'never',
        success: authenticationSuccess,
        error: authenticationFailure
    });
}

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
        props : ['rapports', 'trello'],
        data : function() {
            return {
                stateClass : {
                    1 : {
                        'css' : 'bg-success',
                        'text' : 'Success'
                    },
                    2 : {
                        'css' : 'bg-warning',
                        'text' : 'Warning'
                    },
                    3 : {
                        'css' : 'bg-danger',
                        'text' : 'Danger'
                    }
                }
            }
        },
        template : '<table class="table table-sm table-inverse">'
        +'<thead>'
            +'<tr>'
                +'<th>#</th>'
                +'<th>State</th>'
                +'<th>Description</th>'
                +'<th v-if="trello">Trello</th>'
            +'</tr>'
        +'</thead>'
        +'<tbody>'
            +'<tr v-for="rapport in rapports" v-bind:class="stateClass[rapport.state].css">'
                +'<th scope="row">{{ rapport.id }}</th>'
                +'<td>{{ stateClass[rapport.state].text }}</td>'
                +'<td><span v-for="desc in rapport.description" style="display: block;">{{ desc }}</span></td>'
                + '<td v-if="trello">'
                    +'<div class="checkbox" v-if="rapport.state != 1">'
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
            'hasHistoric' : false,
            'displayChecking' : false,
            'action' : false,
            'rapports' : {},
            'trello' : trello_token,
            'displayConfigTrello' : false,
            'model_token' : null
        },
        methods : {
            start: function () {
                var self = this;

                self.launch = true;
                self.loading = true;

                var data = {};
                data.action = 'launch_test';

                $.post(keyprod_ajax_url.ajax_url, data, function (response) {
                    self.loading = false;
                    self.rapports = JSON.parse(response);
                    self.displayChecking = true;
                    console.log(self.rapports);
                    self.action = "start";

                });
            },
            historic: function () {
                var self = this;
                self.action = "historic";

                self.launch = true;
                self.loading = true;

                var data = {};
                data.action = 'launched_test';

                $.post(keyprod_ajax_url.ajax_url, data, function (response) {
                    self.loading = false;
                    var historics = JSON.parse(response);
                    console.log('historics', historics);
                    for (var i = 0; i < historics.length; i++) {
                        console.log('i', i);
                        historics[i].errors = 0;
                        historics[i].success = 0;
                        historics[i].warnings = 0;
                        for (var y = 0; y < historics[i].rapports.length; y++) {
                            if  (historics[i].rapports[y].state == 1)
                                historics[i].success++;
                            else if  (historics[i].rapports[y].state == 2)
                                historics[i].warnings++;
                            else
                                historics[i].errors++;
                        }
                    }

                    console.log("hasHisto", historics);

                    self.hasHistoric = historics;
                });

            },
            displayHistoric : function (historic) {
                var self = this;
                console.log('historic', historic);
                self.action = "displayHistoric";

                self.rapports = historic.rapports;
                self.hasHistoric = false;
                self.displayChecking = true;

                console.log(self.rapports);
            },
            configTrello: function () {
                var self = this;
                self.action = "config_trello";
                self.launch = true;
                self.loading = false;
                self.hasHistoric = false;
                self.displayChecking = false;
                self.displayConfigTrello = true;
            },
            backTo : function() {
                var self = this;
                self.action = false;
                self.launch = false;
                self.loading = false;
                self.hasHistoric = false;
                self.displayChecking = false;
                self.displayConfigTrello = false;
            },
            validationToken : function() {
                console.log("in click", this.model_token);
                self.loading = true;
                $.post(keyprod_ajax_url.ajax_url, { action : "ajax_set_trello_token", token : this.model_token }, function (response) {
                    self.loading = false;
                    if (JSON.parse(response) == "error") {
                        alert("erreur");
                    } else {
                        window.location.href = "?page=keyprod_page_options";
                    }

                });

            }
        }
    });



});

