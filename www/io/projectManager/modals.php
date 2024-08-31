<!-- Info toast -->
<div class="toast-container fixed-bottom start-50 translate-middle-x p-3" style="z-index: 9999;">
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

<!-- FILE SELECTOR modal -->

<div class="modal fade" id="filebrowserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="filebrowserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex">
                    <button type="button" class="btn" id="backButton"><i class="fas fa-undo"></i></button>
                    <h5 class="modal-title" id="currentFolder">Munka</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="fileExplorer">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBrowser" data-bs-dismiss="modal"
                    data-bs-toggle="modal">Mégse</button>
                <button type="button" class="btn btn-success" id="setRootFolder">Beállítás</button>
            </div>
        </div>
    </div>
</div>



<!-- Project settings modal -->

<div class="modal fade" id="projectSettingsModal" tabindex="-1" aria-labelledby="projectSettingsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectSettingsModalLabel">Projekt beállítások</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-1 input-group">
                        <span class="input-group-text">Név: </span>
                        <input type="text" class="form-control" id="projectName" placeholder="Projekt leírása...">
                    </div>
                    <div id="textEditorButtons"></div>
                    <div class="mb-3 input-group" id="projectDescriptionDiv">
                        
                    </div>
                    <div class="mb-3 input-group">
                        <span for="projectVisibility" class="input-group-text">Láthatóság:</span>
                        <select class="form-select" id="projectVisibility">
                            <option value="0">Mindenki</option>
                            <option value="1">Médiás</option>
                            <option value="2">Stúdiós</option>
                            <option value="3">Admin</option>
                            <option value="4">Hozzáadott emberek</option>
                        </select>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">Határidő: </span>
                        <input type="date" class="form-control" id="projectDate">
                        <input type="time" class="form-control" id="projectTime">
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">NAS elérési út: </span>
                        <input type="text" class="form-control" id="pathToProject" disabled>
                        <button type="button" class="btn btn-outline-secondary" id="browseRootFolder"><i
                                class="fas fa-folder-open"></i></button>
                    </div>
                    <!-- <div class="mb-3 input-group">
                        <span class="input-group-text">Projekt törlése: </span>
                        <input type="text" class="form-control" id="deleteText">
                        <button type="button" class="btn btn-outline-danger" id="deleteButton">Törlés</button>
                    </div> -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="archiveButton" data-bs-dismiss="modal">Befejezés
                    (archiválás)</button>
                <button type="button" class="btn btn-success" id="saveButton">Mentés</button>
            </div>
        </div>
    </div>
</div>

<!-- Task modal -->
<div class="modal fade" id="taskEditorModal" tabindex="-1" aria-labelledby="taskEditorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskTitle">Új feladat hozzáadása</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-1 input-group" id="taskName">
                        <span class="input-group-text">Név: </span>
                        <input type="text" class="form-control" id="textTaskName" placeholder="Név">
                    </div>
                    <div class="mb-1" id="taskData">
                    </div>
                    <div class="mb-3 input-group" id="taskFileManager">
                        <span for="taskFiles" class="input-group-text">NAS fájlok: </span>

                        <div class="memberSelect" id="taskFiles">

                        </div>
                        <button class="btn btn-outline-success" data-bs-dismiss="modal"
                            data-bs-target="#filebrowserModal" data-bs-toggle="modal" id="browseProjectFiles"><i
                                class="far fa-plus-square"></i></button>
                    </div>
                    <div class="mb-3 input-group" style="flex-wrap: nowrap;">
                        <span for="taskMembers" class="input-group-text">Tagok:</span>
                        <div class="memberSelect" id="taskMembers" style="max-height: 90px;">
                        </div>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">Határidő: </span>
                        <input type="date" class="form-control" id="taskDate" placeholder="Nap">
                        <input type="time" class="form-control" id="taskTime" placeholder="Időpont">
                    </div>
                    <div class="mb-2 input-group" id="submissionSettings">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <span id="taskEditorIDspan" style="font-style: italic;"></span>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-target="#areyousureModal"
                    data-bs-toggle="modal" id="deleteTask" style="display: none;">Törlés</button>
                <button type="button" class="btn btn-success" id="saveNewTask">Mentés</button>
            </div>
        </div>
    </div>
</div>

<!-- UI modal -->
<div class="modal fade" id="taskFillModal" tabindex="-1" aria-labelledby="taskFillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskFillTitle">Feladat kitöltése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3" id="taskFillData">
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div id="taskFillDeadline">
                </div>
                <button type="button" class="btn btn-success" id="submitAnswer">Mentés</button>
            </div>
        </div>
    </div>
</div>

<!-- Answers modal -->
<div class="modal fade" id="taskAnswersModal" tabindex="-1" aria-labelledby="taskAnswersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskAnswerTitle">Válaszok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3" id="taskAnswerData">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div id="taskFillDeadline">
                </div>
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Archive modal -->
<div class="modal fade" id="taskArchiveModal" tabindex="-1" aria-labelledby="taskArchiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archivált projektek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3" id="archivedTasks">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- New member modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tagok szerkesztése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="projectMembers" class="col-form-label">Projekt tagjai:</label>
                        <div class="memberSelect" id="projectMembersSelect"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-success" id="saveProjectMembers">Mentés</button>
            </div>
        </div>
    </div>
</div>


<!-- Expand image modal -->
<div class="modal modal-xl fade" id="expandImageModal" tabindex="-1" aria-labelledby="expandImageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="">
            <div class="modal-body">
                <img src="" alt="" id="expandedImage">
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="imgDownloadButton"><i
                        class="fas fa-download fa-lg"></i></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
            </div>
        </div>
    </div>
</div>