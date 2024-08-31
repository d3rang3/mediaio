<!-- Info toast -->
<div class="toast-container fixed-bottom start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div class="toast" id="infoToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="../logo.ico" class="rounded me-2" alt="..." style="height: 20px; filter: invert(1);">
            <strong class="me-auto" id="infoToastTitle">Forms</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
        </div>
    </div>
</div>


<!-- Title edit modal -->
<div class="modal fade" id="Title_Modal" tabindex="-1" role="dialog" aria-labelledby="title_ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kérdőív címe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type='text' class='form-control' id='formTitle' placeholder='Új cím'></input>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success col-lg-auto mb-1" id="clear" data-bs-dismiss="modal"
                    onclick="save_title()">Kész</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
            </div>
        </div>
    </div>
</div>
<!-- Title edit modal end -->

<!-- Clear Modal -->
<div class="modal fade" id="delete_Modal" tabindex="-1" role="dialog" aria-labelledby="delete_ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Törlés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <a>Biztosan ki akarod törölni a kérdőívet?</a>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger col-lg-auto mb-1" id="clear" data-bs-dismiss="modal"
                    onclick="deleteForm()">Törlés</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Clear Modal -->

<!-- Settings Modal -->
<div class="modal fade" id="settings_Modal" tabindex="-1" role="dialog" aria-labelledby="settings_ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Beállítások</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="cars">Form állapota:</label>
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="isAcceptingAnswers" data-setting="Anonim">
                    <label class="form-check-label" for="isAcceptingAnswers">Fogad válaszokat</label>
                </div>
                <div class="mb-3" id="accessForm">
                    Elérhetőség:
                    <select class="form-select form-select-sm mb-1" id="accessRestrict" name="accessRestrict">
                        <option value="1">Privát</option>
                        <option value="2">Médiás</option>
                        <option value="3">Csak linkkel elérhető</option>
                        <option value="0">Publikus</option>
                    </select>
                    <div class="input-group" id="linkHolderGroup" style="display: none;">
                        <input type="text" class="form-control" placeholder="Kérdőív link" aria-label="Form link"
                            id="formLinkHolder">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">Másolás</button>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="flexSwitchCheckDefault"
                        data-setting="SingleAnswer">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Korlátozás egy válaszra (még nem
                        működik)</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="flexSwitchCheckDefault" data-setting="Anonim">
                    <label class="form-check-label" for="flexSwitchCheckDefault"><b>Anonymous</b> válaszadás</label>
                </div>
                <label class="mb-2" for="background_img">Háttérkép: <a href="#" id="default-background"
                        data-bs-toggle="popover" data-bs-placement="top">(alapértelmezett)</a></label>
                <div class="input-group">

                    <input type="file" class="form-control" placeholder="Háttérkép feltöltése"
                        aria-label="Background upload" name="fileToUpload" id="background_img" accept="image/*">
                    <button class="btn btn-outline-danger" type="button"
                        onclick="changeBackground(<?php echo $_GET['formId'] ?>,true)">Reset</button>
                    <button class="btn btn-outline-success" type="button"
                        onclick="changeBackground(<?php echo $_GET['formId'] ?>)">Feltöltés</button>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" data-bs-target="#delete_Modal" data-bs-toggle="modal"><i
                        class='fas fa-trash-alt fa-lg'></i></button>
                <button class="btn btn-success col-lg-auto mb-1" id="save" data-bs-dismiss="modal"
                    onclick="saveForm(false)">Mentés</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Mégse</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Settings Modal -->