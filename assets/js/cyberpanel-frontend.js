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
            if (event.target == modalRebuild) {
                modalRebuild.style.display = "none";
            }
        }
    }

    var btnReboot = document.getElementById("reboot");
    btnReboot.onclick = function () {
        var rebootModal = document.getElementById("rebootModal");
        rebootModal.style.display = "block";
        window.onclick = function (event) {
            if (event.target == rebootModal) {
                rebootModal.style.display = "none";
            }
        }
    }
});

function cancelNOWCB(data) {
    jQuery(document).ready(function ($) {
        $(".loader").hide();

        if (data.status === 1) {
            window.location.reload();
        }

    });
    //window.location.reload();
}

function serverActionsCB(data) {
    jQuery(document).ready(function ($) {
        $(".loader").hide();

        if (data.status === 1) {
            $("#serverActions").html(data.result);
        }
        if(data.running === 1){
            $("#menu").addClass("notClickAble");
            $("#col1").addClass("notClickAble");
            $("#col2").addClass("notClickAble");
            $("#jobRunning").show();
        }else{
            $("#menu").removeClass("notClickAble");
            $("#col1").removeClass("notClickAble");
            $("#col2").removeClass("notClickAble");
            $("#jobRunning").hide();
        }
    });
    //window.location.reload();
}

function GlobalAjax(dataContent, callbackSuccess, callBackFailure) {
    jQuery(document).ready(function ($) {
        $(".loader").show();
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

    $(".loader").hide();
    $("#jobRunning").hide();

    var dataContent;

    $("#cancelNow").click(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'cancelNow',
            serverID: $("#serverID").text()
        }
        GlobalAjax(dataContent, cancelNOWCB, cancelNOWCB);
    });

    $("#rebuildNow").click(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'rebuildNow',
            serverID: $("#serverID").text()
        }
        GlobalAjax(dataContent, cancelNOWCB, cancelNOWCB);
    });

    $("#rebootNow").click(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'rebootNow',
            serverID: $("#serverID").text()
        }
        GlobalAjax(dataContent, cancelNOWCB, cancelNOWCB);
    });

    function fetchStatus(){
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'serverActions',
            serverID: $("#serverID").text()
        }
        GlobalAjax(dataContent, serverActionsCB, serverActionsCB);
    }

    setInterval(fetchStatus, 3000);

});

