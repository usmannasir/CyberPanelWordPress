function jobStatus(data) {
    jQuery(document).ready(function ($) {
        $("#WPCPSpinner").hide();
        $("#WPCPSpinnerModal").hide();
        if (data.status === 1) {
            $("#jobStatusResult").html(data.result);
            $("#jobsModal").modal('show');
        }
    });
}

function verifyConnectionCB(data) {
    var dataContent = {
        _ajax_nonce: CPWP.nonce,
        action: 'jobStatus',
    }
    GlobalAjax(dataContent, jobStatus, jobStatus);
}

function fetchProviderPlansCallBack(data) {

    jQuery(document).ready(function ($) {
        $("#WPCPSpinner").hide();
        $("#WPCPSpinnerModal").hide();
        $("#wpcp_providerplans_label").show();
        $("#wpcp_providerplans").show();

        if (data.status === 1) {
            $("#wpcp_providerplans").html(data.result);
        }
    });
}

function fetchProviderAPIs(data) {
    jQuery(document).ready(function ($) {
        $("#WPCPSpinner").hide();
        $("#WPCPSpinnerModal").hide();
        $("#wpcp_providerplans_label").show();
        $("#wpcp_providerplans").show();

        if (data.status === 1) {
            $("#wpcp_providerapis").html(data.result);
        }
    });
}

function GlobalAjax(dataContent, callbackSuccess, callBackFailure) {
    jQuery(document).ready(function ($) {
        $("#WPCPSpinner").show();
        $("#WPCPSpinnerModal").show();
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

function deleteAPIDetails(id) {
    jQuery(document).ready(function ($) {
        alert(id);
    });
}

///
