//to interact with a table create one with
//(the table element, row number, column number)
//
//  NOTE: row precieds column so in terms of a carteesian plane its y,x
//
//from the base table function there are three main sections:
//>add
//  row(start, length, object with node properties)
//  col(start, length, object with node properties)
//
//>rem
//  row(start, length)
//  col(start, length)
//
//>set
//  row(start, length, object with node properties)
//  col(start, length, object with node properties)
//  cell(row,column, object with node properties)
//
//>debug - is avalible but its use is discuraged as it is only ment to diagnose issues
//  get() 
//      the whole internal structure
//  set(x)
//      force internal structure to be x

function DynamicTable(rootTable, rows, cols, auto_update){
    return (function(rootTable, rows, cols, auto_update){
        //var inside aunomymous function to hide data
        var internal = {
            root:rootTable,
           rows:rows,
           cols:cols,
           elements:[[]],
           autoupdate: auto_update || true,
        };
        internal.root.parentElement.setAttribute("style","overflow:auto");
        //mock default init
        for(var i=0;i<rows;i++){
            internal.elements[i] = [];
            for(var j=0;j<cols;j++){
                internal.elements[i][j] = new Node(
                    "input",//type
                    '',//TODO tag not yet implemented
                    "",//starting value
                    {attr:"class",value:"text-info text-center"}//attribute
                    );
            }
        }
        var table = {
            debug:{
                get:function(){
                    return internal;
                },
                set:function(x){
                    internal = x;
                }
            },
            add:{
                row:function(start,number,node){
                    for(var i = start; i<start+number; i++){
                        var col = internal.elements.splice(i,0,[]);
                        for(var j=0; j < internal.cols; j++){
                            internal.elements[i][j] = new Node(node.type, node.tag, node.value, node.attr);
                        }
                    }
                    internal.rows+=number;
                    if(internal.autoupdate)
                        table.update();
                },
                col:function(start,number,node){
                    for(var j = 0; j< internal.rows; j++){
                        for(var i = start; i<start+number; i++){
                            var col = internal.elements[j].splice(i,0,[]);
                            internal.elements[j][i] = new Node(node.type, node.tag, node.value, node.attr);
                        }
                    }
                    internal.cols+=number;
                    if(internal.autoupdate)
                        table.update();
                },
            },
            rem:{
                row:function(start,number){
                    internal.elements.splice(start, number);
                    internal.rows-=number;
                    if(internal.autoupdate)
                        table.update();
                },
                col:function(start,number){
                    for(var j = 0; j< internal.rows; j++){
                        internal.elements[j].splice(start,number);
                    }
                    internal.cols-=number;
                    if(internal.autoupdate)
                        table.update();
                },
            },
            set:{
                row:function(start,number,node){
                    for(var i = start; i<start+number; i++){
                        for(var j=0; j < internal.cols; j++){
                            internal.elements[i][j] = new Node(node.type, node.tag, node.value, node.attr);
                        }
                    }
                    if(internal.autoupdate)
                        table.update();
                },
                col:function(start,number,node){
                    for(var j = 0; j< internal.rows; j++){
                        for(var i = start; i<start+number; i++){
                            internal.elements[j][i] = new Node(node.type, node.tag, node.value, node.attr);
                        }
                    }
                    if(internal.autoupdate)
                        table.update();
                },
                cell:function(row, column, node){
                    internal.elements[row][column] = new Node(node.type, node.tag, node.value, node.attr);
                    if(internal.autoupdate)
                        table.update();
                }
            },
            update:function(){
                var table = internal.root;
                var tbody = document.createElement("tbody");
                for(var i = 0; i < internal.rows; i++){
                    var row = tbody.insertRow(i);
                    for(var j = 0; j < internal.cols; j++){
                        var col = row.insertCell(j);
                        var value = internal.elements[i][j];
                        col.appendChild(internal.elements[i][j].get());
                    }
                }
                table.innerHTML="";
                table.appendChild(tbody);
            },
            toJSON:function(){
                var baseTable = [];
                
                for (var i = 0; i < internal.elements.length; i++) {
                    baseTable[i]=[];
                    for (var j = 0; j < internal.elements[i].length; j++) {
                        baseTable[i][j] = internal.elements[i][j];
                    }
                }
                return {table:baseTable};
            },
        }
        if(internal.autoupdate)
            table.update();
        return table;
    })(rootTable, rows, cols, auto_update);
}
function Node(type, tag, value, attr){
    this.type = type;//.split(",");
    this.tag = tag;
    this.value = value || "";
    this.el;
    this.get = function(){
        if(typeof this.el == "undefined"){
            var _this = this;
            if(this.type == "input"){
                this.el = document.createElement("input");
                this.el.value = this.value;
                this.el.onkeypress = function(){
                    _this.value = (_this.type=="input")?
                        this.value : this.innerText;
                }
            } else if (this.type == "label") {
                this.el = document.createElement("h4");
                this.el.innerText = this.value;
            }
            if(attr)
                this.el.setAttribute(attr.attr,attr.value);
        }
        return this.el;
    }
    this.toJSON = function(){return this.value;}
}
