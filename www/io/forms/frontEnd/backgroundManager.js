//Function to change background
function changeBackground(id, clear = false) {

    if (clear) {
        $.ajax({
            type: "POST",
            url: "upload-handler.php",
            data: { mode: "deleteBackground", formId: id },
            success: function (data) {

                if (data == 200) {

                    $.ajax({
                        type: "POST",
                        url: "../formManager.php",
                        data: { mode: "changeBackground", id: id, name: "default.jpg" },
                        success: function (data) {

                            if (data == 200) {
                                saveForm(false);
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            }

                        }
                    });
                }
            }
        });
        return;
    }


    let fileInput = document.getElementById("background_img");
    if (fileInput.files.length === 0) {
        console.log("No file selected");
        return;
    }

    let file = document.getElementById("background_img").files[0];
    let formData = new FormData();
    formData.append('fileToUpload', file);
    formData.append('formId', id);
    formData.append('mode', 'uploadBackground');

    $.ajax({
        type: "POST",
        url: "upload-handler.php",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            //console.log(data);
            if (data != 500) {
                $.ajax({
                    type: "POST",
                    url: "../formManager.php",
                    data: { mode: "changeBackground", id: id, name: data },
                    success: function (data) {
                        console.log(data);
                        saveForm(false);
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            } else if (data == 500) {
                console.log("Upload failed!");
            } else if (data == 400) {
                console.log("Not an image!");
            }
        }
    });
}