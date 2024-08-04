<!-- Info toast -->
<div class="toast-container bottom-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div class="toast" id="infoToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="../logo.ico" class="rounded me-2" alt="..." style="height: 20px; filter: invert(1);">
            <strong class="me-auto" id="infoToastTitle">Projektek</strong>
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

<!-- takeoutSettingsModal Modal -->
<div class="modal fade" id="takeoutSettingsModal" tabindex="-1" role="dialog"
    aria-labelledby="takeoutSettingsModal_Modal_Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="takeoutSettingsModal_Label">Elvitel beállítások</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <?php
                if (in_array("admin", $_SESSION["groups"])):
                    ?>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Név:</span>
                        <input type="text" class="form-control" id="plannedName" placeholder="Projekt/Bérlő neve"
                            aria-label="Projekt/Bérlő neve" aria-describedby="name">
                    </div>
                    <?php
                endif;
                ?>
                <div class="input-group mb-2">
                    <span class="input-group-text"><span style='color: red;'>*</span>Tervezett időtartam:</span>
                    <input class="form-control" type="text" id="datepicker" required />
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Megjegyzés:</span>
                    <textarea class="form-control" aria-label="Comment" id="plannedDesc"
                        placeholder="Rövid infó az elvitel céljáról..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button id="submitTakeoutButton" type="button" class="btn btn-success" onclick="submitTakeout()">
                    Mehet</button>
            </div>
        </div>
    </div>
</div>



<!-- Presets Modal -->
<div class="modal fade" id="presets_Modal" tabindex="-1" role="dialog" aria-labelledby="presets_ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Elérhető presetek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="presetsLoading" class="spinner-grow text-info" role="status"></div>
                <div id="presetsContainer"></div>
                <div class="mt-3" id="notAvailableItems"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Presets Modal -->

<!-- Clear Modal -->
<div class="modal fade" id="clear_Modal" tabindex="-1" role="dialog" aria-labelledby="clear_ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Összes törlése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <a>Biztosan ki akarsz törölni mindent?</a>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger col-lg-auto mb-1" id="clear" data-bs-dismiss="modal"
                    onclick="deselect_all()">Összes törlése</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mégse</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Clear Modal -->

<!-- Scanner Modal -->
<div class="modal fade" id="scanner_Modal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="scanner_ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Szkenner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="pauseCamera()"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="scanner_body">
                <div id="reader" width="600px"></div>
                <!-- Toasts -->
                <div class="toast align-items-center" id="scan_toast" role="alert" aria-live="assertive"
                    aria-atomic="true" style="z-index: 99; display:none;">
                    <div class="d-flex">
                        <div class="toast-body" id="scan_result">
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="scanner_footer">
                <button type="button" class="btn btn-outline-dark" id="ext_scanner" onclick="ExternalScan()">Külső
                    olvasó</button>
                <button type="button" class="btn btn-info" id="zoom_btn" onclick="zoomCamera()"
                    style="display: none;">Zoom: 2x</button>
                <button type="button" class="btn btn-info" id="torch_btn" onclick="startTorch()"
                    style="display: none;">Vaku</button>
                <div class="dropdown dropup">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="true">
                        Kamerák
                    </button>
                    <ul class="dropdown-menu" id="av_cams"></ul>
                </div>
                <button type="button" class="btn btn-success" onclick="pauseCamera()"
                    data-bs-dismiss="modal">Kész</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Scanner Modal -->