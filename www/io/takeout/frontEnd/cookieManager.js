
let reloading = false;

//If set to false, saved cookies are bypassed to allow project-specific selection
var cookiesEnabled = true;

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
    if(!cookiesEnabled) {
        return;
    }
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


async function reloadSavedSelections() {
    if(!cookiesEnabled) {
        return;
    }
    reloading = true;
    //Try re-selectiong items that are saved in the takeOutItems cookie.
    try {
        var selecteditems = getCookie("selectedItems")
        selecteditems = JSON.parse(selecteditems);
    } catch (error) {
        reloading = false;
        console.log("No saved items found");
        return;
    }
    if (!selecteditems || selecteditems.length === 0) {
        reloading = false;
        return;
    }
    selecteditems.forEach(element => {
        console.log("Reloading item: " + element);
        document.getElementById(element).click();
    });
    reloading = false;
}