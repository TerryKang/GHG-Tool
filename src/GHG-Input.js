//input table
var inputRoot;
//destination table
var destinationRoot;
//initial data from the server
var serverData;
//spaceing between the tables left and right respectivly
var spL = 7,spR = 5;
function init(){
    inputRoot = $("#inputTable").find("tbody");
    destinationRoot = $("#destinationTable").find("tbody");
    aja()
        .url("tableData.json")
        .type("json")
        .on("success",function(data){
            serverData=data;
            buildComp();
            buildDest();
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
    for(x in res.comps){
        row.append("<td>")
            .find("td:last")
            .text(res.comps[x]);
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
        var c;
        //row data
        for(c in res.results[x].data){
            var temp = inputRoot.find("tr:last")
                .append("<td>")
                .find("td:last")
                .append("<input type=\"text\">")
                .find("input:last");
            temp.val(res.results[x].data[c]);
            temp.bind("keypress focusout focusin",function(){validateComp();});
        }
        //check column
        var temp = inputRoot.find("tr:last");
        if(temp.find("input").length!=0)
            temp.append("<b>")
                .find("b:last")
                .text("Loading...");
    }
    validateComp();
}
//
function validateComp(){
    inputRoot.find("tr").each(function(){
        sum = -1;
        $(this).find("td").each(function(){
            $(this).find("input").each(function(){
                if(sum == -1){
                    sum = 0;
                    return;
                }
                sum+=Number($(this).val());
            });
        });
        $(this).find("b").text(sum==100?"OK":"Check Values");
    });
}
function buildDest(){
    var base = serverData;
    destinationRoot.append("<tr>");
    //add data rows
    for(x in base.results){
        destinationRoot.append("<tr>");
    }
    //add labels
    destinationRoot.find("tr:first")
        .append("<td>")
        .find("td:last")
        .text("Facility");
    destinationRoot.find("tr:first")
        .append("<td>")
        .find("td:last")
        .text("%Transfer");
    destinationRoot.find("tr:first")
        .append("<td>")
        .find("td:last")
        .text("Vehicle");

    destinationRoot.find("tr").each(function(index){
        //skip labels
        if(index){
        var current = base.results[index-1].dest[0];//only the first dest right now
            $(this).append("<td>")
        .find("td:last")
        .append(function(){
            //create the selection 
            var sel = $("<select>");
            for(var i = 0; i < base.facility.length;++i) {
                //add the options
                sel.append($("<option>",{value:i,text:base.facility[i]}));
            }
            return sel;
        })
        .find("select:last")   
        .val(current.facility);
    $(this).append("<td>")
        .find("td:last")
        .append("<input type=\"text\">")
        .find("input:last")
        .val(current.percent);
    $(this).append("<td>")
        .find("td:last")
        .append(function(){
            //create the selection 
            var sel = $("<select>");
            for(var i = 0; i < base.trucks.length;++i) {
                //add the options
                sel.append($("<option>",{value:i,text:base.trucks[i]}));
            }
            return sel;
        })
        .find("select:last")
        .val(current.vehicle);
        }
    });
}

function addDest(){
    var base = serverData;
    //add labels
    destinationRoot.find("tr:first")
        .append("<td>")
        .find("td:last")
        .text("Facility");
    destinationRoot.find("tr:first")
        .append("<td>")
        .find("td:last")
        .text("%Transfer");
    destinationRoot.find("tr:first")
        .append("<td>")
        .find("td:last")
        .text("Vehicle");

    destinationRoot.find("tr").each(function(index){
        //skip labels
        if(index){
            $(this).append("<td>")
        .find("td:last")
        .append(function(){
            //create the selection 
            var sel = $("<select>");
            for(var i = 0; i < base.facility.length;++i) {
                //add the options
                sel.append($("<option>",{value:i,text:base.facility[i]}));
            }
            return sel;
        })
        .find("select:last")   
        .val(0);
    $(this).append("<td>")
        .find("td:last")
        .append("<input type=\"text\">")
        .find("input:last")
        .val(0);
    $(this).append("<td>")
        .find("td:last")
        .append(function(){
            //create the selection 
            var sel = $("<select>");
            for(var i = 0; i < base.trucks.length;++i) {
                //add the options
                sel.append($("<option>",{value:i,text:base.trucks[i]}));
            }
            return sel;
        })
        .find("select:last")
        .val(0);
        }
    });
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
    }}
