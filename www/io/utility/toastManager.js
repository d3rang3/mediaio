const option = {
    animation: true,
    delay: 3000
};

const toastList = document.querySelector('.toast');
const toast = new bootstrap.Toast(toastList, option);


function serverErrorToast() {
    //Remove all classes except the toast class
    toastList.classList.remove('text-bg-warning');
    toastList.classList.remove('text-bg-danger');
    toastList.classList.remove('text-bg-info');
    toastList.classList.remove('text-bg-success');

    toastList.classList.add('text-bg-danger');
    let toastHeader = toastList.querySelector('.toast-header');
    toastHeader.querySelector('#infoToastTitle').innerHTML = "Hiba";
    toastList.querySelector('.toast-body').innerHTML = "Valamilyen szerver hiba történt! Kérlek próbáld újra később!";
    toast.show();
}

function noAccessToast() {
    //Remove all classes except the toast class
    toastList.classList.remove('text-bg-danger');
    toastList.classList.remove('text-bg-warning');
    toastList.classList.remove('text-bg-info');
    toastList.classList.remove('text-bg-success');

    toastList.classList.add('text-bg-danger');
    let toastHeader = toastList.querySelector('.toast-header');
    toastHeader.querySelector('#infoToastTitle').innerHTML = "Hiba";
    toastList.querySelector('.toast-body').innerHTML = "Nincs jogosultságod ehhez a művelethez!";
    toast.show();
}

function successToast(message) {
    //Remove all classes except the toast class
    toastList.classList.remove('text-bg-danger');
    toastList.classList.remove('text-bg-warning');
    toastList.classList.remove('text-bg-info');
    toastList.classList.remove('text-bg-success');

    toastList.classList.add('text-bg-success');
    let toastHeader = toastList.querySelector('.toast-header');
    toastHeader.querySelector('#infoToastTitle').innerHTML = "Siker";
    toastList.querySelector('.toast-body').innerHTML = message;
    toast.show();
}

function errorToast(message) {
    //Remove all classes except the toast class
    toastList.classList.remove('text-bg-danger');
    toastList.classList.remove('text-bg-warning');
    toastList.classList.remove('text-bg-info');
    toastList.classList.remove('text-bg-success');

    toastList.classList.add('text-bg-danger');
    let toastHeader = toastList.querySelector('.toast-header');
    toastHeader.querySelector('#infoToastTitle').innerHTML = "Hiba";
    toastList.querySelector('.toast-body').innerHTML = message;
    toast.show();
}

function warningToast(message) {
    //Remove all classes except the toast class
    toastList.classList.remove('text-bg-danger');
    toastList.classList.remove('text-bg-warning');
    toastList.classList.remove('text-bg-info');
    toastList.classList.remove('text-bg-success');

    toastList.classList.add('text-bg-warning');
    let toastHeader = toastList.querySelector('.toast-header');
    toastHeader.querySelector('#infoToastTitle').innerHTML = "Figyelem";
    toastList.querySelector('.toast-body').innerHTML = message;
    toast.show();
}

function simpleToast(message) {
    //Remove all classes except the toast class
    toastList.classList.remove('text-bg-danger');
    toastList.classList.remove('text-bg-warning');
    toastList.classList.remove('text-bg-info');
    toastList.classList.remove('text-bg-success');

    let toastHeader = toastList.querySelector('.toast-header');
    toastHeader.querySelector('#infoToastTitle').innerHTML = "Információ";
    toastList.querySelector('.toast-body').innerHTML = message;
    toast.show();
}



// Tooltips 

function toolTipRender() {
    var tooltipList = null;
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
}