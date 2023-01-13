class smartTable{
    /**
     * @param {Element} nestingTable 
     * @param {array} regArray 
     * @param {object} settings 
     * 
     *
     * Not fully portable class ! Pretty universal if Active mode is false
     * fills in a table having id = nestingTable
     * table content is taken from regArray, 
     * settings obj allow to configure the table : 
     *  .headArray defines the header of each column
     *  .active : boolean to add action column (strictly dedicated to E4M)
     *  .IOfieldName : 0/1 value purely dedicated to E4M (hum... can be removed, as it seems...) ========================
     *  .activeHeader : header for last column if active mode
     *  .colData : array containing the property for each column
     *  .colSorted : index of col sorted (-1 if none)
     *  .setCSS : if true, then add specific style for rows not confirmed or waiting
     * Each row contains colData[0], colData[1], ...
     * Clicking the header of a column sorts the table alpha ASC or num DESC
     * If regArray[n] has .rowLink set, clicking the row redirects to the link
     * 
     * required from external : css  
     */
   
    constructor(nestingTable, regArray, settings){
        this.nestingTable = nestingTable;
        this.regArray = regArray;
        this.settings = settings;
        this.Build();
    }

    Update(new_Array){
        /**
         * table is deleted, and rebuilt with new Array
         */
        this.regArray = new_Array;
        let table = document.getElementById(this.nestingTable);
        table.deleteTHead();
        table.removeChild(table.getElementsByTagName("tbody")[0]);
        this.Build();
    }
    
    Build(){
        var table = document.getElementById(this.nestingTable);
        let tableData = this.regArray;
        let Columns = this.settings.colData;
        let nbCol = Columns.length;
        let nbLig = this.regArray.length;
        var isActive = this.settings.active;
        var activeHeader = this.settings.activeHeader;
        //var IOfieldName = this.settings.IOfieldName;
        let colSorted = this.settings.colSorted;
        let header = this.settings.headArray;
        let nbHead = header.length;
        if ( nbCol != nbHead ) {
            throw 'header and body have different number of columns ! '
        } 
        let columns = this.settings.colData;
        var tableHeader = table.createTHead();
        let rowHead = document.createElement('tr');
        for (let k = 0; k< nbHead; k++){
            let headerCell = document.createElement('th');
            headerCell.v_sortKey = Columns[k]; // puts the sort key in a variable attached to the cell
            let sortSign = (k == colSorted) ? str["sort_mark"] :""; 
            //let textNode = document.createTextNode(header[k]);
            //if (k == colSorted) textNode += "!";
            headerCell.appendChild(document.createTextNode(header[k] + sortSign));
            //headerCell.appendChild(textNode);
            headerCell.callback_arg = Columns[k];
            headerCell.index = k;
            headerCell.addEventListener('click', event => {
                let key = event.currentTarget.v_sortKey;
                let sortIndex = event.currentTarget.index;
                this.settings.colSorted = sortIndex;
                let tableData = this.regArray;
                console.log(this.regArray[0][key]);
                console.log(typeof(this.regArray[0][key]));
                switch (typeof(this.regArray[0][key])) { // first data must be representative !
                    case 'number' : 
                        tableData.sort((a,b) => -parseFloat(a[key]) + parseFloat(b[key]));	
                        break;
                    case 'string' : 
                        tableData.sort( (a,b) => a[key].toString().localeCompare(b[key].toString()) );
                        break;	
                    case 'object' :
                        tableData.sort( (a,b) => + new Date(b[key]) - new Date(a[key]) );
                    break;     
                    default :
                        throw ('sort_method handles only numbers and strings');
                }
                this.regArray = tableData;
                let table = document.getElementById(this.nestingTable);
                table.deleteTHead();
                table.removeChild(table.getElementsByTagName("tbody")[0]);
                this.Build();
            });
            headerCell.classList.add("E4M_hoverable_item");
            rowHead.appendChild(headerCell);
        }
        if (isActive) {
            rowHead.appendChild(document.createTextNode(activeHeader))
        };
        tableHeader.appendChild(rowHead);
        let tableBody = document.createElement('tbody');
        
        /* from here, code is specific to RegisterList table if isActive */

        tableData.forEach(function(rowData){
            /* let's put all columns */
            
            let isConfirmed = (rowData.confirmed == "1") ? true : false;
            let isWaiting = (rowData.wait == "1") ? true : false;
            
            let row = document.createElement('tr');
            for (let i = 0; i< nbCol; i++) {
                let cell = document.createElement('td');
                let cell_content = rowData[Columns[i]];
                if (typeof(cell_content) == 'object') {
                    cell_content = cell_content.toLocaleDateString()
                }
                cell.appendChild(document.createTextNode(cell_content));
                row.appendChild(cell);
            }
            /* Add last colunm action (makes sense only for E4M) */
            if (isActive) {
                /*  */
                let lastCol = document.createElement('td');
                let tempchar = "❌";
                let tempaction = "d";
                if ( !isConfirmed ) {
                    tempchar = "✅";
                    tempaction = "c";
                }
                let lastColChar = tempchar;
                lastCol.action = tempaction;
                lastCol.char_arg = rowData.fullname;
                lastCol.number_arg = rowData.regid;
                lastCol.appendChild(document.createTextNode(lastColChar));
                lastCol.addEventListener("click", (event) => {
                    // EditRegistration (reg, action, member_name)
                    let reg_id = event.currentTarget.number_arg;
                    let action = event.currentTarget.action;
                    let member_name = event.currentTarget.char_arg;
                    EditRegistration (reg_id, action, member_name);
                })
                row.appendChild(lastCol)
            };
            if ( rowData.rowLink ) {
                row.addEventListener("click", () => {
                    document.location = rowData.rowLink;
                })
            }
            if ( rowData.css ) {
                row.classList.add(rowData.css); 
            }
            tableBody.appendChild(row);
        });
        table.appendChild(tableBody);
    }   
}   