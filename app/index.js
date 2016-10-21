var $ = jQuery;
console.log(trello_token);
var trello_token = typeof (trello_token) === "string" ? trello_token : false;


var authenticationSuccess = function() { console.log("Trello Success"); };
var authenticationFailure = function() { console.log("Trello Fail"); };
if (trello_token) {
    const apiTrello = Trello.authorize({
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
        props : {
            rapports: {type: Array},
            trello: {}
        },
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
                },
                trelloInterface : false,
                assigned : {},
                trelloTables : false,
                trelloTableChosen : false,
                trelloLists : false,
                trelloListChosen : false,
                trelloMembers : false,
                trelloMemberChosen : false,
                successAssign : false

            }
        },
        template : '<div>'
        +'<div v-if="successAssign" class="alert alert-success" role="alert">'
            +'<strong>Well done!</strong> You successfully assign the rapport'
        +'</div>'
        +'<table class="table table-sm table-inverse">'
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
                    +'<div class="checkbox"  v-if="rapport.state != 1">'
                    +'<input type="checkbox" v-on:click="_assignRapport(rapport)" value="">'
                    +'</div>'
                +'</td>'
            +'</tr>'
        +'</tbody></table><div class="row"><div class="col-sm-12"><button v-on:click="_exportToCsv" type="button" class="btn btn-outline-success" style="margin-left: 20px;margin-bottom: 20px;">Export CSV</button></div></div>'
            +'<div v-if="trelloInterface"><div class="row"><div class="col-sm-6"><div class="row">'
                +'<div class="col-sm-12"><div class="card" style="margin: auto;margin-bottom: 5px!important;">'
                    +'<ul class="list-group list-group-flush">'
                        +'<li v-for="assi in assigned" class="list-group-item">Rapport # {{ assi.id}}</li>'
                    +'</ul>'
                +'</div></div>'
                +'<div v-if="trelloTables" class="col-sm-12"><div class="card" style="margin: auto;margin-bottom: 5px!important;"><p>Choose a Table</p>'
                    +'<div class="list-group">'
                        +'<a href="" v-for="trelloTable in trelloTables" class="list-group-item list-group-item-action" v-on:click.prevent="_trelloChooseTable(trelloTable)">{{ trelloTable.name }}</a>'
                    +'</div>'
                +'</div></div>'
                +'<div v-if="trelloLists" class="col-sm-12"><div class="card" style="margin: auto;margin-bottom: 5px!important;"><p>Choose a List</p>'
                    +'<div class="list-group">'
                        +'<p v-if="trelloLists.length == 0">Create your own list in your account</p>'
                        +'<a href="" v-for="trelloList in trelloLists" class="list-group-item list-group-item-action" v-on:click.prevent="_trelloChooseList(trelloList)">{{ trelloList.name }}</a>'
                    +'</div>'
                +'</div></div>'
                +'<div v-if="trelloMembers" class="col-sm-12"><div class="card" style="margin: auto;margin-bottom: 5px!important;"><p>Choose a Member</p>'
                    +'<div class="list-group">'
                        +'<a href="" v-for="trelloMember in trelloMembers" class="list-group-item list-group-item-action" v-on:click.prevent="_trelloChooseMember(trelloMember)">{{ trelloMember.fullName }}</a>'
                    +'</div>'
                +'</div></div>'
            +'</div></div><div class="col-sm-6"><div class="col-sm-12"><div class="card" v-if="assigned" style="margin: auto;margin-bottom: 5px!important;background: rgba(0, 0, 0, 0.2);"><p v-if="trelloTableChosen">Board : {{ trelloTableChosen.name }} </p><p v-if="trelloListChosen">List : {{ trelloListChosen.name}}</p><p v-if="trelloMemberChosen">To {{trelloMemberChosen.fullName}}</p><p class="trello_corp" >Keyprod wordpress : <span v-for="assign in assigned">Rapport #{{ assign.id }}<span v-for="desc in assign.description">{{ desc }}</span></p></div><div class="col-sm-12" style="text-align: center;"><button v-on:click="_assignTo" v-if="trelloMemberChosen" type="button" class="btn btn-outline-primary" style="margin-left: 20px">Assign to</button></div></div></div></div>'
        +'</div>',
        methods: {
            _assignRapport : function (rapport) {
                var self = this;
                self.trelloInterface = false;
                console.log("click 2",rapport);
                if (self.assigned[rapport.id] === undefined) {
                    self.assigned[rapport.id] = rapport;
                } else {
                    delete self.assigned[rapport.id];
                }
                console.log(self.assigned);
                if (self.trelloTables === false)
                    Trello.get('/members/me/boards/', function (tables) {
                        console.log('tables', tables);
                        self.trelloTables = tables;
                    }, function () { alert('fail')});
                self.trelloInterface = true;
                console.log(self.trelloTables);
            },
            _trelloChooseTable : function (table) {
                var self = this;
                this.trelloLists = false;
                this.trelloMembers = false;
                this.trelloMemberChosen =false;
                console.log(this.trelloTableChosen = table);
                console.log('table', table);
               Trello.get('boards/'+table.id+'/lists', function (lists) {
                    console.log('lists', self.trelloLists = lists);
                }, function () { alert('fail')});
            },
            _trelloChooseList : function (list) {
                var self = this;
                this.trelloMembers = false;
                this.trelloMemberChosen =false;
                console.log(this.trelloListChosen = list);
                console.log('list', list);
                Trello.get('/boards/'+list.idBoard+'/members/', function (members) {
                    console.log('member', self.trelloMembers = members);
                }, function () { alert('fail')});
            },
            _trelloChooseMember : function (member) {
                var self = this;
                console.log(self.trelloMemberChosen = member);
            },
            _assignTo : function () {
                var self = this;

                if (self.assigned != false && self.trelloTableChosen != false && self.trelloListChosen != false && self.trelloMemberChosen != false) {
                    console.log('assigned', self.assigned);
                    var desc = "Keyprod assignment, ";
                    for (var a in self.assigned)
                        desc += 'rapport : #' + self.assigned[a].id + ' "'+self.assigned[a].description.join() +'"';
                    console.log('self.trelloMemberChosen.id', self.trelloMemberChosen.id);
                    Trello.post('/cards/', {
                        name : "Keyprod rapport (Wordpress)",
                        desc : desc,
                        idList : self.trelloListChosen.id,
                        idMembers : [ self.trelloMemberChosen.id ]
                    }, function (responses) {
                        if (responses){
                            console.log('in the if');
                            self.trelloInterface = false;
                            self.successAssign = true;
                        }

                    })
                }

            },
            _exportToCsv : function () {
                //var data = [["name1", "city1", "some other info"], ["name2", "city2", "more info"]];
                var array = [['id', 'state' , 'description']];
                for (var ar = 0; ar < this.rapports.length; ar++) {
                    console.log(this.rapports[ar]);
                    array.push([
                        this.rapports[ar].id,
                        this.rapports[ar].state === 1? 'success' : this.rapports[ar].state === 2 ? "warning" : "danger",
                        this.rapports[ar].description.join()
                    ]);
                }

                var data = array;
                console.log("data", data);
                var csvContent = "data:text/csv;charset=utf-8,\uFEFF";
                data.forEach(function(infoArray, index){
                    dataString = infoArray.join(";");
                    csvContent += index < data.length ? dataString+ "\n" : dataString;
                });
                var encodedUri = encodeURI(csvContent);
                window.open(encodedUri);

            }

        }
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

