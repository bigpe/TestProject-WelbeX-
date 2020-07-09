var hashedNode = {};
var formData = {};


function appendHashedNode(node){
    let nodeID = node.getAttribute("append_to");
    if(hashedNode[nodeID]['appendLimit'] === hashedNode[nodeID]['appendCurrent'])
        return;
    if(++hashedNode[nodeID]['appendCurrent'] === hashedNode[nodeID]['appendLimit'])
        node.remove();
    let SInode = document.getElementsByClassName(nodeID);
    let selectedIndex = SInode[SInode.length - 1].selectedIndex;
    let optionList = hashedNode[nodeID]
        .getElementsByClassName(nodeID)[0]
        .getElementsByTagName('option');
    optionList[selectedIndex].remove();
    for(let i = 0; i < SInode.length; i++) {
        let oldOptionList =  SInode[i].getElementsByTagName("option");
        for(let j = 0; j < oldOptionList.length; j++){
            if (!oldOptionList[j].selected)
                oldOptionList[j].remove();
        }
    }
    let nodeToAppend = document.getElementById(nodeID);
    let tempNode = hashedNode[nodeID].cloneNode(true);
    nodeToAppend.append(tempNode);
    updateFormData(nodeID);

}
function loadAppendLimit(nodeID, l = '0') {
    hashedNode[nodeID] = document.getElementById(nodeID).cloneNode(true);
    hashedNode[nodeID]['appendLimit'] = parseInt(l);
    hashedNode[nodeID]['appendCurrent'] = 1;
    hashedNode[nodeID]['paramsCount'] = $('#' + nodeID + '_Form').serializeArray().length;
    document.getElementsByClassName(nodeID)[0].selectedIndex = 0; //Reset Selected Index
    formData[nodeID] = [];
    updateFormData(nodeID);
}
function updateFormData(nodeID) {
    let result = {};
    let j = 0;
    let n = hashedNode[nodeID]['paramsCount'];
    let l = hashedNode[nodeID]['appendLimit'];
    $.each($('#' + nodeID + '_Form').serializeArray(), function(i) {
        result[this.name] = this.value;
        formData[nodeID][j] = result;
        if(!l) //Single Value Box
            formData[nodeID] = result;
        if(!(++i % n)) {
            j++;
            result = {};
        }
    });
}
function refreshPage(node, nodeID, force = false){
    let si = node.selectedIndex;
    let nc = node.getAttribute('class');
    if(force)
        node.selectedIndex = 0;
    updateFormData(nodeID);
    $.ajax({
        url: '/',
        method: 'POST',
        data: formData[nodeID],
        complete: function (data) {
            let parser = new DOMParser();
            let table = parser.parseFromString(data.responseText, 'text/html').body;
            document.body.innerHTML = table.innerHTML;
            particlesJS.load('particles-js', '/configs/particles.json',
                function() {}); //Load Particles
            if(!force)
                document.getElementsByClassName(nc)[0].selectedIndex = si;
            for(let box in formData) { //Update Every Box Before Sending
                if(hashedNode[box]['appendLimit']) //Exclude 0 length
                    loadAppendLimit(box, document.getElementsByClassName(box)[0]
                        .getElementsByTagName('option').length);
            }
        }
    })
}

function pagination(node){
    let processOld = loadSpinner('process');
    formData['tableBox']['offset'] = parseInt(node.innerText) - 1;
    $.ajax({
        url: '/table.php',
        method: 'POST',
        data: {'tableData': formData},
        complete: function (data) {
            let parser = new DOMParser();
            let table = parser.parseFromString(data.responseText, 'text/html').getElementById('mainTable');
            let mainTable = document.getElementById('mainTable');
            mainTable.innerHTML = table.innerHTML;
            let pB = document.getElementsByClassName("pagination");
            for (let i = 0; i < pB.length; i++)
                pB[i].getElementsByClassName("page-item")[formData['tableBox']['offset']]
                    .setAttribute("class", "page-item active disabled");
            returnSpinner('process', processOld);
        }
    })
}

function loadSpinner(nodeID){
    let process = document.getElementById(nodeID);
    let processOld = process.innerHTML;
    process.innerHTML = "<div class=\"spinner-border text-light\" role=\"status\"></div>";
    process.setAttribute("disabled", "disabled");
    return(processOld);
}

function returnSpinner(nodeID, processOld){
    let process = document.getElementById(nodeID);
    setTimeout(function () {
        process.removeAttribute("disabled");
        process.innerHTML = processOld;
    }, 300)
}

document.onsubmit = function(e){
    let processOld = loadSpinner('process');
    e.preventDefault(); //Disable OnSubmit Event
    for(let box in formData) //Update Every Box Before Sending
        updateFormData(box, true);
    $.ajax({
        url: '/table.php',
        method: 'POST',
        data: {'tableData': formData},
        complete: function (data) {
            let parser = new DOMParser();
            let table = parser.parseFromString(data.responseText, 'text/html').getElementById('mainTable');
            let mainTable = document.getElementById('mainTable');
            mainTable.innerHTML = table.innerHTML;
            returnSpinner('process', processOld);
        }
    })
};