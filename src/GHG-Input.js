var inputTable = null; 
function init(){
    //inputTable = DynamicTable(document.getElementById("inputTable"),row,col);
    aja()
        .url("tableData.json")
        .on("success",function(data){
            inputTable = data;
            var root = $("#inputTable").find("tbody");
            for(var i = 0; i<data.compositions.length; ++i){
                var row = root.append('<tr>');
                for(var j = 0; j<data.compositions[i].length; ++j){
                    row.append("<td>").append("<input type=\"text\">");
                    var lst = row.find("input").last();
                    lst.val(data.compositions[i][j]);
                    if(typeof data.compositions[i][j] == "number"){
                        lst.attr("tbc","check")
                        lst.keypress(function(){validate();});
                    }
                }
                row.append("<td>").append("<p>");
                var lst = row.find("p").last();
                lst.text("Loading...");
                lst.attr("tbc","check");
            }
        })
    .go();

    // $("#inputTable tr:first td:first").innerHTML("");

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
