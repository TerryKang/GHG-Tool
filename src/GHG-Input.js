var inputTable = null; 
function init(){
    aja()
        .url("tableData.json")
        .on("success",function(data){
            build(data);
        })
    .go();
}

function build(res){
    var root = $("#inputTable");
    root.find("tbody").empty();
    var row = root.find("tbody").append("<tr>");
    var x;
    for(x in res.comps){
        row.last()
            .append("<td>")
            .find("td:last")
            .text(res.comps[x]);
    }
    for(x in res.results){
        row = root
            .find("tbody")
            .append("<tr>");
        row.append("<td>")
            .find("td:last")
            .text(res.results[x].label);
        var c;
        for(c in res.results[x].data){
            row.append("<td>")
                .find("td:last")
                .append("<input type=\"text\">")
                .find("input:last")
                .val(res.results[x].data[c]);
        }
    }
}

function validate(){
    var root = $("#inputTable").find("tbody").first();
    root.find("tr").each(function(){
        var skip = true;
        var sum = 0;
        $(this).find("td").each(function(){
            if(skip){
                skip = false;
            } else {
                var temp = $(this).find("input").first().val();
                if(temp.tbc == "check")
            debugger;
                else
            debugger;
            }
        });
        console.log(sum);
    });
}

function addLoc(){

}

function addDest(){

}
