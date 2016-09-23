//constructor for dynamic table
//@param rootTable root table element to add rows and columns to.
//@param rows inital rows to create table with.
//@param columns initial columns to create table with.
function DynamicTable(rootTable, rows, columns, callback){
    //check params
    if(rows < 1){
        throw {message:"Invalid row number", value:rows};
    }
    if(columns < 1){
        throw {message:"Invalid column number", value:columns};
    }
    callback = (typeof callback != "undefined")? callback : "";

    //setup internal varables
    this.internal = {
        //[row number decending][column number acending]
        elements:[[]],
        rowNum:rows,
        colNum:columns,
        root:rootTable,
    }

    for(var i = 0; i < this.internal.rowNum; i++){
        var row = this.internal.elements[i] = [];
        for(var j = 0; j < this.internal.colNum; j++){
            var col = row[j] = callback;
        }
    }

    //function declarations
    this.size = function(){
        return [this.internal.rowNum,this.internal.colNum];
    }

    //reflow DOM with new changes
    this.update = function(){
        var table = this.internal.root;
        var tbody = document.createElement("tbody");
        for(var i = 0; i < this.internal.rowNum; i++){
            var row = tbody.insertRow(i);
            for(var j = 0; j < this.internal.colNum; j++){
                var col = row.insertCell(j);
                var value = this.internal.elements[i][j];
                col.innerHTML = (typeof value == "function")? value(i,j) : value;
            }
        }
        table.innerHTML="";
        table.appendChild(tbody);
    }

    //add a row to this table
    //@param pos the index of the row to add, default append row
    //@param callback the fill or function to insert into the newly created row
    this.addRow = function(pos, callback){
        callback = (typeof callback != "undefined")? callback : "";
        var func = (typeof callback == "function");
        pos = (typeof pos != "undefined" && pos >= 0)? pos : this.internal.rowNum;
        this.internal.rowNum++;

        this.internal.elements.splice(pos,0,[]);
        this.internal.elements[pos];
        for(var j = 0; j < this.internal.colNum; j++){
            this.internal.elements[pos][j] = callback;
        }
        this.update();
    }

    //add a column to this table
    //@param pos the index of the row to add, default append column
    //@param callback the fill or function to insert into the newly created column
    this.addColumn = function(pos, callback){
        callback = (typeof callback != "undefined")? callback : "";
        var func = (typeof callback == "function");
        pos = (typeof pos != "undefined" && pos >= 0)? pos : this.internal.colNum;
        this.internal.colNum++;

        for(var i = 0; i < this.internal.rowNum; i++){
            this.internal.elements[i].splice(pos, 0, callback);
        }
        this.update();
    }

    //remove a row to this table
    //@param pos the index of the row to add, default append row
    this.removeRow = function(pos){
        pos = (typeof pos != "undefined" && pos >= 0)? pos : this.internal.rowNum;
        this.internal.rowNum--;
        this.internal.elements.splice(pos,1);
        this.update();
    }

    //remove a column to this table
    //@param pos the index of the row to add, default append column
    this.removeColumn = function(pos){
        pos = (typeof pos != "undefined" && pos >= 0)? pos : this.internal.colNum;
        this.internal.colNum--;
        for(var i = 0; i < this.internal.rowNum; i++){
            this.internal.elements[i].splice(pos, 1);
        }
        this.update();
    }


    //set a single table cell value
    //@param row the row number to set
    //@param column the column number to set
    //@param callback the fill or function to set the cell to, default ""
    this.set = function(row, column, callback){
        if(row < 0)
            throw {message:"Invalid row number", value:row};
        if(column < 0)
            throw {message:"Invalid column number", value:column};
        callback = (typeof callback != "undefined")? callback : "";
        this.internal.elements[row][column] = callback;
        this.update();
    }

    //Stringify data for sending to the API
    this.toJSON = function(){
        var retObject = [];
        for(var i = 0; i < this.internal.rowNum; i++){
            retObject[i] = [];
            for(var j = 0; j < this.internal.colNum; j++){
                //why we use children and not childNodes
                //http://stackoverflow.com/a/7935719/5539918
                //children is only elements but childNodes is everything
                var cell = this.internal.root.tBodies[0].children[i].children[j];
                retObject[i][j] = (cell.children.length > 0)? cell.children[0].value : cell.innerText;
            }
        }
        return JSON.stringify(retObject);
    }

    this.update();
    //return handle to table incase table needs to be modified externally
    return this;
}
