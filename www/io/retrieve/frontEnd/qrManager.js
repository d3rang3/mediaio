//Scanner

let canScan = true;

const qrOnSuccess = (decodedText, decodedResult) => {
    console.log(`Code matched = ${decodedText}`, decodedResult);

    if (!canScan) {
        return;
    }

    let selectedItem = document.getElementById(decodedText);

    if (selectedItem) {
        canScan = false;
        toggleSelectItem({ UID: decodedText });
        showToast(decodedText, "green");
        scan_succes_sfx.play();

        setTimeout(() => {
            canScan = true;
        }, 2000);
    } else {
        canScan = false;
        showToast("Nem található ilyen eszköz!", "red");
        scan_fail_sfx.play();
        setTimeout(() => {
            canScan = true;
        }, 2000);
    }
};