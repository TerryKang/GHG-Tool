//input table
var inputRoot;
//selector
var inputScenario;
//destination table
var destinationRoot;
//selector
var destinationScenario
//initial data from the server
var serverData;
//list of source histories
var sourceHist;
//list of destination histories
var destHist;
//spaceing between the tables left and right respectivly for the wells
var spL = 7,spR = 5;
//current starting history ids
var sourceId = 1;
var destId = 1;

//a recallable method to update the tables when the selection boxes are changed
function tableFetch(url){
    aja()
        .url(url)
        .type("json")
        .on("success",function(data){
            //clear so we can reaload
            inputRoot.empty();
            destinationRoot.empty();
            
            //update global data
            serverData=data;
            
            //rebuild
            buildComp();
            buildDest();
        })
    .go();
}

//the function that is called on page load to set everything up initially
function init(){
    //find needed elements
    inputRoot = $("#inputTable").find("tbody");
    inputScenario = $('#inputScenario');

    destinationScenario = $('#destinationScenario');
    destinationRoot = $("#destinationTable").find("tbody");
    //load the table data
    tableFetch('/input/last');
    //load historys for source
    aja()
        .url('input/history/source')
        .on('success', function(data){
            sourceHist = data;
            //build options to menu
            for(var i=0; i<data.length;i++){
                var newOption = $('<option>');
                var text = data[i].scenarioName + ", " + new Date(data[i].date.date);
                newOption.attr('value', data[i].scenarioName).text(text);
                //add options to menu
                inputScenario.append(newOption);
            }
            //add listener for if they change the selection
            inputScenario.change(function () {
                var name = $('#inputScenario :selected').val().split(',')[0];
                for(var x in sourceHist){
                    if(sourceHist[x].scenarioName == name){
                        sourceId = sourceHist[x].historyId;
                        tableFetch("/input/"+destId+'/'+sourceId);
                        return;
                    }
                }
            });
        })
    .go();

    //load histories for source
    aja()
        .url('input/history/destination')
        .on('success', function(data){
            destHist = data;
            //build options for menu
            for(var i=0; i<data.length;i++){
                var newOption = $('<option>');
                var text = data[i].scenarioName + ", " + new Date(data[i].date.date)
                newOption.attr('value', data[i].scenarioName).text(text);
                //add options to menu
                destinationScenario.append(newOption);
            }
            //add listener for if they change the selection
            destinationScenario.change(function () {
                var name = $('#destinationScenario :selected').val();
                for(var x in destHist){
                    if(destHist[x].scenarioName == name){
                        destId = destHist[x].historyId;
                        tableFetch("/input/"+destId+'/'+sourceId);
                        return;
                    }
                }
            });
        })
    .go();
}

//build the left table from the data returned by the api
function buildComp(){
    //clear and start the table
    var res = serverData;
    $("#inputTable").css("overflow","auto");
    inputRoot.find("tbody").empty();
    var row = inputRoot.append("<tr>").find("tr:last");
    var x;

    //add titles
    row.append("<td>")
        .find("td:last")
        .text("Sources\\Compositions");
    for(x in res.comps){
        if(x != 'key'){
            row.append("<td>")
                .find("td:last")
                .text(res.comps[x]);
        }
    }
    //each row
    for(x in res.results){
        //row label
        inputRoot.append("<tr>")
            .find("tr:last")
            .append("<td>")
            .find("td:last")
            .text(res.results[x].label);
        //row data
        for(var c in res.results[x].data){
            if(c!='key'){
                var temp = inputRoot.find("tr:last")
                    .append("<td>")
                    .find("td:last")
                    .append("<input type=\"text\" readonly>")//this is for viewing only
                    .find("input:last");
                temp.val(res.results[x].data[c]);
            }
        }
    }
}
//build the right table from the data returned by the api
function buildDest(){
    //add row for labels
    var base = serverData;
    destinationRoot.append("<tr>");
    //add data rows
    for(var x in base.results){
        destinationRoot.append("<tr>");
    }
    //add labels
    for(var x in base.facility){
        if(x!='key'){
            destinationRoot.find("tr:first")
                .append("<td>")
                .find("td:last")
                .text(base.facility[x]);
            destinationRoot.find("tr:first")
                .append("<td>")
                .find("td:last")
                .text("Vehicle");
        }
    }
    //add data to rows
    destinationRoot.find("tr").each(function(index){
        //skip labels
        if(index){
            var full = base.results[index-1].dest;
            for(var current in full){
                $(this).append("<td>").find("td:last")
                    .append("<input type=\"text\">").find("input:last")
                    .val(full[current].percent);
                //add truck dropdown
                $(this).append("<td>").find("td:last")
                    .append(function(){
                    //create the selection 
                    var sel = $("<select>");
                    for(var x in base.trucks) {
                        if(x!='key'){
                            //add the trucks
                            sel.append($("<option>",{value:x,text:base.trucks[x]}));
                        }
                    }
                    return sel;
                    });
                //pick the vehicle selected
                $(this).find("select:last").val(full[current].vehicle);
            }
        }
    });
}

//calls getData and returns it as a post to the api for saving
function saveData(){
    aja()
        .url("/input/")
        .type("json")
        .method("post")
        .body(getData())
        .on("success",function(data){})
        .go();
}


//loop through the destinations to get the data and turn it into json
function getData(){
    //create base retutn template
    var result = {"source":{"key":"sourceId"}};
    var j=1;
    //go through each row
    destinationRoot.find("tr").each(function(index,elem){
        if(index==0)//skip the labels
            return;
        var i = 1;
        result.source[j]={dest:{}};//add the data
        $(this).find("td").each(function(index2,elem2){
            if(index2%2==0){//percent
                result.source[j].dest[i] = {percent:$(this).find("input").val()};
            } else {//truck
                result.source[j].dest[i].vehicle = $(this).find("select").val();
                ++i;
            }
        });
        ++j;
    });
    return JSON.stringify(result);
}

//change classes to move left
function mvL() {
    if(spL>2){
        $("#inputDiv").removeClass("col-xs-"+spL).addClass("col-xs-"+(--spL));
        $("#destDiv").removeClass("col-xs-"+spR).addClass("col-xs-"+(++spR));
    }
}
//change classes to move right
function mvR() {
    if(spR>2){
        $("#inputDiv").removeClass("col-xs-"+spL).addClass("col-xs-"+(++spL));
        $("#destDiv").removeClass("col-xs-"+spR).addClass("col-xs-"+(--spR));
    }
}
