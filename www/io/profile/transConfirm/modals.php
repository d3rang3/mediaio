<!-- Info toast -->
<div class="toast-container bottom-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div class="toast" id="infoToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="../logo.ico" class="rounded me-2" alt="..." style="height: 20px; filter: invert(1);">
            <strong class="me-auto" id="infoToastTitle">IO</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
        </div>
    </div>
</div>


<!-- Are you sure? modal -->

<div class="modal fade" id="areyousureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="areyousureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Biztos vagy benne?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButton">Mégse</button>
                <button type="button" class="btn btn-danger" id="sureButton">Törlés</button>
            </div>
        </div>
    </div>
</div>


<!-- Accept settings modal -->

<div class="modal fade" id="SettingsModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="SettingsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SettingsModalLabel">Részletek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="itemsList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="submitButton">Mentés</button>
            </div>
        </div>
    </div>
</div>