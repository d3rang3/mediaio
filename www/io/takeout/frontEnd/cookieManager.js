

function getCookie(cName) {
    const name = cName + "=";
    const cDecoded = decodeURIComponent(document.cookie); //to be careful
    const cArr = cDecoded.split('; ');
    let res;
    cArr.forEach(val => {
        if (val.indexOf(name) === 0) res = val.substring(name.length);
    })
    return res
}

function updateSelectionCookie() {
    console.log("Updating selection cookie");

    //Set cookie expire date to 1 day
    var d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();

    //get IDs of selected items
    let selectedItems = document.getElementsByClassName("selected");
    selectedItems = Array.from(selectedItems).map(item => item.id);
    //console.log(selectedItems);
    document.cookie = "selectedItems=" + JSON.stringify(selectedItems) + ";" + expires + ";path=/";
}


function reloadSavedSelections() {
    //Try re-selectiong items that are saved in the takeOutItems cookie.

    var selecteditems = getCookie("selectedItems")
    selecteditems = JSON.parse(selecteditems);
    if (!selecteditems || selecteditems.length === 0) {
        return;
    }
    selecteditems.forEach(element => {
        console.log("Reloading item: " + element);
        document.getElementById(element).click();
    });
}