//input table
var inputRoot;
var inputScenario;
//destination table
var destinationRoot;
var destinationScenario
//initial data from the server
var serverData;
//spaceing between the tables left and right respectivly
var spL = 7,spR = 5;
function init(){
    inputRoot = $("#inputTable").find("tbody");
    inputScenario = $('#inputScenario');
    destinationScenario = $('#destinationScenario');
    destinationRoot = $("#destinationTable").find("tbody");
    aja()
        .url("/input/last")
        .type("json")
        .on("success",function(data){
            serverData=data;
            buildComp();
            buildDest();
        })
    .go();

    //load historys for source
    aja()
        .url('input/history/source')
        .on('success', function(data){
            for(var i=0; i<data.length;i++){
                var newOption = $('<option>');
                var text = data[i].scenarioName + ", " + new Date(data[i].date.date)
            newOption.attr('value', data[i].scenarioName).text(text);
        inputScenario.append(newOption);
            }
            inputScenario.change(function () {
                var scenarioName = $('#inputScenario :selected').val();
                //selectScenario(scenarioName);//handle change
            });
            if($('#inputScenario option').size()>0){
                var scenarioName = $('#inputScenario :selected').val();
                //selectScenario(scenarioName);
            }
        })
    .go();

//load historys for source
    aja()
        .url('input/history/destination')
        .on('success', function(data){
            for(var i=0; i<data.length;i++){
                var newOption = $('<option>');
                var text = data[i].scenarioName + ", " + new Date(data[i].date.date)
                newOption.attr('value', data[i].scenarioName).text(text);
                destinationScenario.append(newOption);
            }
            destinationScenario.change(function () {
                var scenarioName = $('#destinationScenario :selected').val();
                //selectScenario(scenarioName);//handle change
            });
            if($('#destinationScenario option').size()>0){
                var scenarioName = $('#destinationScenario :selected').val();
                //selectScenario(scenarioName);
            }
        })
    .go();
}

function buildComp(){
    var res = serverData;
    $("#inputTable").css("overflow","auto");
    inputRoot.find("tbody").empty();
    var row = inputRoot.append("<tr>").find("tr:last");
    var x;
    //first titles
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
    //empty td for space above check
    row.append("<td>");
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
                    .append("<input type=\"text\" readonly>")
                    .find("input:last");
                temp.val(res.results[x].data[c]);
            }
        }
        //check column
        var temp = inputRoot.find("tr:last");
        if(temp.find("input").length!=0)
            temp.append("<b>")
                .find("b:last")
                .text("Loading...");
    }
}
function buildDest(){
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
    destinationRoot.find("tr").each(function(index){
        //skip labels
        if(index){
            var full = base.results[index-1].dest;
            for(var current in full){
                $(this).append("<td>").find("td:last")
                    .append("<input type=\"text\">").find("input:last")
                    .val(full[current].percent);
                //
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

function saveData(){
    aja()
        .url("/input/")
        .type("json")
        .method("post")
        .body(getData())
        .on("success",function(data){})
        .go();

}


function getData(){
    var result = {"source":{}};
    var j=1;
    destinationRoot.find("tr").each(function(index,elem){
        if(index==0)
            return;
        var i = 1;
        result.source[j]={dest:{}};
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

function mvL() {
    if(spL>2){
        $("#inputDiv").removeClass("col-xs-"+spL).addClass("col-xs-"+(--spL));
        $("#destDiv").removeClass("col-xs-"+spR).addClass("col-xs-"+(++spR));
    }
}
function mvR() {
    if(spR>2){
        $("#inputDiv").removeClass("col-xs-"+spL).addClass("col-xs-"+(++spL));
        $("#destDiv").removeClass("col-xs-"+spR).addClass("col-xs-"+(--spR));
    }
}
