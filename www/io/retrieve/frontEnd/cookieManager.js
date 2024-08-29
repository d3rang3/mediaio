

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
    document.cookie = "itemsToRetrieve=" + JSON.stringify(selectedItems) + ";" + expires + ";path=/";
}


function reloadSavedSelections() {
    //Try re-selectiong items that are saved in the takeOutItems cookie.

    var selecteditems = getCookie("itemsToRetrieve")
    try {
        selecteditems = JSON.parse(selecteditems);
    } catch (e) {
        console.log("Error parsing cookie: " + e);
        return;
    }
    if (!selecteditems || selecteditems.length === 0) {
        return;
    }
    selecteditems.forEach(element => {
        console.log("Reloading item: " + element);
        toggleSelectItem({ UID: element });
    });
}

function clearSelectionCookie() {
    console.log("Cleared cookie");
    var d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = "itemsToRetrieve=;" + expires + ";path=/";
}