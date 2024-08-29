async function listDir(path = '/Munka') {

    let response = await $.ajax({
        type: "GET",
        url: "./nasCommunication.php",
        data: { mode: "listDir", path: path },
    });

    return response;

}


async function browseNASFolder(projectID, taskId, type, path = '/Munka') {
    console.log("Browsing NAS folder: " + path);
    let projectRoot;
    if (type == "selectFiles") {
        projectRoot = await fetchProjectRoot(projectID);
    }

    const backButton = document.getElementById("backButton");
    if (type == "projectRoot") {
        backButton.style.display = (path == "/Munka") ? "none" : "block";
    } else if (type == "selectFiles") {
        backButton.style.display = (path == projectRoot) ? "none" : "block";
    }

    const currentFolderTitle = document.getElementById("currentFolder");
    currentFolderTitle.innerHTML = path;

    const fileExplorer = document.getElementById("fileExplorer");
    fileExplorer.innerHTML = "";

    // Add spinner
    let spinner = document.createElement("div");
    spinner.classList.add("spinner-grow", "text-secondary");
    spinner.id = "loadingSpinner";
    spinner.style.margin = "auto";
    spinner.style.display = "block";
    fileExplorer.appendChild(spinner);

    // Load folders
    let response = await listDir(path);
    response = JSON.parse(response);
    console.log(response);

    // Remove spinner
    document.getElementById("loadingSpinner").remove();

    let arrayOfFiles = response.data.files;

    arrayOfFiles.forEach(file => {
        let fileElement = document.createElement("div");
        fileElement.classList.add("fileElement");
        fileElement.innerHTML = `<i class="fas ${file.isdir ? 'fa-folder-open' : 'fa-file'}"></i> ${file.name}`;
        fileElement.setAttribute("data-path", file.path);
        fileElement.style.cursor = "pointer";
        fileElement.onclick = function () {
            if (file.isdir == true) {
                browseNASFolder(projectID, taskId, type, file.path);
            } else {
                this.classList.toggle("selected");
            }
        }
        fileExplorer.appendChild(fileElement);
    });

    // Previous folder button
    backButton.onclick = function () {
        let pathArray = path.split("/");
        pathArray.pop();
        let newPath = pathArray.join("/");
        browseNASFolder(projectID, taskId, type, newPath);
    }

    // Cancel button update
    const cancelButton = document.getElementById("cancelBrowser");
    if (type == "projectRoot") {
        cancelButton.setAttribute("data-bs-target", "#projectSettingsModal");
    } else if (type == "selectFiles") {
        cancelButton.setAttribute("data-bs-target", "#taskEditorModal");
    }

    // Set button update
    const saveButton = document.getElementById("setRootFolder");
    if (type == "projectRoot") {
        saveButton.onclick = function () {
            saveNASPath(projectID, path);
        }
    } else if (type == "selectFiles") {
        saveButton.onclick = function () {
            selectFiles();
        }
    }
}


async function selectFiles() {

    const browser = document.getElementById("fileExplorer");
    let selectedFiles = browser.getElementsByClassName("selected");

    if (selectedFiles.length == 0) {
        alert("Nincs kiválasztva fájl!");
        return;
    }

    const editorFileHolder = document.getElementById("taskFiles");
    Array.from(selectedFiles).forEach(async file => {
        let fileElement = document.createElement("div");
        fileElement.classList.add("fileElement");
        fileElement.innerHTML = `<i class="fas fa-file"></i> ${file.innerText}`;
        let path = file.getAttribute("data-path");
        fileElement.setAttribute("data-link", await getLink(path));
        fileElement.setAttribute("data-path", path);

        let deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-sm", "btn-danger", "float-end");
        deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
        deleteButton.onclick = function () {
            fileElement.remove();
        }
        fileElement.appendChild(deleteButton);
        editorFileHolder.appendChild(fileElement);
    });

    // Close browser
    $("#filebrowserModal").modal("hide");
    $("#taskEditorModal").modal("show");

}


async function getLink(path) {

    let response = await $.ajax({
        type: "GET",
        url: "./nasCommunication.php",
        data: { mode: "getLink", path: path },
    });
    response = JSON.parse(response);
    console.log(response.data.links[0].url);
    return response.data.links[0].url;
}


async function getDownloadLink(path) {
    if (isSAFARIOS()) {
        errorToast("Safari blockolja a popupokat ezért itt (még) nem megy a download! - apple, think different💀!");
        return;
    }

    let response = await $.ajax({
        type: "GET",
        url: "./nasCommunication.php",
        data: { mode: "downloadFile", path: path },
    });

    /*     if (response == 200){
            successToast("Fájl letöltve!");
        } */


    if (isSAFARIOS()) {
        //window.location.href = response;
        return;
    } else {
        window.open(response, "_blank");
    }
}

function isSAFARIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream && /Safari/.test(navigator.userAgent) && !/Chrome|CriOS/.test(navigator.userAgent);
}