jQuery(document).ready(function ($) {

    var btn = document.getElementById("myBtn");
    btn.onclick = function () {
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }
    var btnRebuild = document.getElementById("rebuild");
    btnRebuild.onclick = function () {
        var modalRebuild = document.getElementById("rebuildModal");
        modalRebuild.style.display = "block";
        window.onclick = function (event) {
            if (event.target == modal) {
                modalRebuild.style.display = "none";
            }
        }
    }
});

function cancelNOWCB(data) {
    jQuery(document).ready(function ($) {
        $("#loader").hide();

        if (data.status === 1) {
            window.location.reload();
        }

    });
    //window.location.reload();
}

function GlobalAjax(dataContent, callbackSuccess, callBackFailure) {
    jQuery(document).ready(function ($) {
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
            serverID: $("#serverID").text()
        }
        GlobalAjax(dataContent, cancelNOWCB, cancelNOWCB);
    });


});

