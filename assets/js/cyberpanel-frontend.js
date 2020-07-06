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

// When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    }

// When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

jQuery(document).ready(function ($) {

    $("#WPCPSpinner").hide();
    $("#wpcp_providerplans_label").hide();
    $("#wpcp_providerplans").hide();

    var dataContent;

    $("#connectServer").click(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'connectServer',
            hostname: $("#hostname").val(),
            username: $("#username").val(),
            password: $("#password").val()
        }
        GlobalAjax(dataContent, verifyConnectionCB, verifyConnectionCB);
    });
    $("#viewJobs").click(function () {

        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'jobStatus',
        }

        GlobalAjax(dataContent, jobStatus, jobStatus);

    });
    $("#viewJobsModal").click(function () {

        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'jobStatus'
        }

        GlobalAjax(dataContent, jobStatus, jobStatus);

    });

    /// Hetzner

    $("#connectProvider").click(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'connectProvider',
            provider: $("#provider").val(),
            name: $("#name").val(),
            token: $("#token").val(),
            imageID: $("#imageID").val(),
        }
        GlobalAjax(dataContent, verifyConnectionCB, verifyConnectionCB);
    });

    /// Providers

    $("#wpcp_provider").change(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'fetchProviderPlans',
            wpcp_provider: $(this).children("option:selected").val(),
        }
        GlobalAjax(dataContent, fetchProviderPlansCallBack, fetchProviderPlansCallBack);
    });

    $("#provider").change(function () {
        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'fetchProviderAPIs',
            provider: $(this).children("option:selected").val(),
        }
        GlobalAjax(dataContent, fetchProviderAPIs, fetchProviderAPIs);
    });


});

