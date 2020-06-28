function jobStatus(data){
    alert('hello world');
    console.log(data);
    if (data.status === 1) {
        $("#jobStatusResult").html(data.result);
        $("#jobsModal").modal('show');
    }
}

function verifyConnectionCB(data){
    $("#WPCPSpinner").hide();
    $("#WPCPSpinnerModal").hide();

    var dataContent = {
        _ajax_nonce: CPWP.nonce,
        action: 'jobStatus',
    }
    GlobalAjax(dataContent, jobStatus, jobStatus);
}

function GlobalAjax(dataContent, callbackSuccess, callBackFailure) {
    jQuery(document).ready(function ($) {
        $("#WPCPSpinner").show();
        $("#WPCPSpinnerModal").show();
        $.post(CPWP.ajax_url, dataContent, callbackSuccess, callBackFailure);
    });
}

jQuery(document).ready(function ($) {
    $("#WPCPSpinner").hide();
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
});

///
