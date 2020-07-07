function triggerModal() {
    var modal = document.getElementById("myModal");

// Get the button that opens the modal
    var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
    btn.onclick = function () {
        modal.style.display = "block";
    }


// When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

function cancelNOWCB(data) {
    jQuery(document).ready(function ($) {
        $("#loader").hide();
    });
    //window.location.reload();
}

function GlobalAjax(dataContent, callbackSuccess, callBackFailure) {
    jQuery(document).ready(function ($) {
        alert('hello world');
        $("#loader").show();
        $.ajax({
            type: "POST",
            url: CPWP.ajax_url,
            data: dataContent,
            success: callbackSuccess,
            error: callBackFailure
        });
    });
}

jQuery(document).ready(function ($) {

    $("#loader").hide();

    var dataContent;

    $("#cancelNow").click(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'cancelNow',
            serverID: $("#serverID").val()
        }
        GlobalAjax(dataContent, cancelNOWCB, cancelNOWCB);
    });



});

