var inputRoot;
var destinationRoot;
var serverData;
function init(){
    inputRoot = $("#inputTable").find("tbody");
    destinationRoot = $("#destinationTable").find("tbody");
    aja()
        .url("tableData.json")
        .type("json")
        .on("success",function(data){
            serverData=data;
            build();
            addDest();
        })
    .go();
}

function build(){
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
            temp.bind("keypress focusout",function(){validate();});
        }
        //check column
        var temp = inputRoot.find("tr:last");
        if(temp.find("input").length!=0)
            temp.append("<b>")
                .find("b:last")
                .text("Loading...");
    }
    validate();
}

function validate(){
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

function addLoc(){

}

function addDest(){
    var base = serverData;
    var dest = "Vancouver Landfill";
    var truck = "Truck A";
    var x;

    //no rows yet
    if(destinationRoot.find("tr").length==0){
        for(x in base.results){
            destinationRoot.append("<tr>");
        }
    }
    //rows already so add defaults
    if(destinationRoot.find("tr").length!=0){
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

    }
    destinationRoot.find("tr").each(function(){
        if($(this).find("td:contains(Facility)").length == 0){
            $(this).append("<td>")
        .find("td:last")
        .append("<input type=\"text\">")
        .find("input:last")
        .val(dest);
    $(this).append("<td>")
        .find("td:last")
        .append("<input type=\"text\">")
        .find("input:last")
        .val(100);
    $(this).append("<td>")
        .find("td:last")
        .append("<input type=\"text\">")
        .find("input:last")
        .val(truck);
        }
    });
}
