var root;
var sum;
function init(){
    root = $("#inputTable").find("tbody");
    aja()
        .url("tableData.json")
        .on("success",function(data){
            build(data);
        })
    .go();
}

function build(res){
    $("#inputTable").css("overflow","auto");
    root.find("tbody").empty();
    var row = root.append("<tr>").find("tr:last");
    var x;
    for(x in res.comps){
        row.append("<td>")
            .find("td:last")
            .text(res.comps[x]);
    }
    for(x in res.results){
        root.append("<tr>")
            .find("tr:last")
            .append("<td>")
            .find("td:last")
            .text(res.results[x].label);
        var c;
        for(c in res.results[x].data){
            var temp = root.find("tr:last")
                .append("<td>")
                .find("td:last")
                .append("<input type=\"text\">")
                .find("input:last");
            temp.val(res.results[x].data[c]);
            temp.bind("keypress focusout",function(){validate();});
        }
        var temp = root.find("tr:last");
        if(temp.find("input").length!=0)
            temp.append("<b>")
            .find("b:last")
            .text("Loading...");
    }
    validate();
}

function validate(){
    root.find("tr").each(function(in1){
        sum = -1;
        $(this).find("td").each(function(in2){
            $(this).find("input").each(function(in3){
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

}
