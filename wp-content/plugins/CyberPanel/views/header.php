<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm WPCPHeader">
    <img src="https://cyberpanel.net/wp-content/uploads/2018/07/logo-e1532873145641.png"> <h4
            class="my-0 mr-md-auto font-weight-normal"><span style="margin-left: 5%">CyberPanel</span></h4>
    <nav class="my-2 my-md-0 mr-md-3">
        <div id="WPCPSpinner" class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <a class="p-2 text-dark" href="<?php echo admin_url("admin.php?page=cyberpanel"); ?>">Add Servers</a>
        <!--<a class="p-2 text-dark" href="#">Enterprise</a>
        <a class="p-2 text-dark" href="#">Support</a>
        <a class="p-2 text-dark" href="#">Pricing</a>-->
    </nav>
    <a class="btn btn-outline-primary" href="<?php echo admin_url("admin.php?page=cyberpanel-manage"); ?>">Manage
        Servers</a>
    <a style="margin-left: 1%" id="viewJobs" class="btn btn-outline-info" href="#">View Jobs</a>
</div>

<div id="jobsModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jobs Pending/Completed
                    <div id="WPCPSpinnerModal" class="spinner-border text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="jobStatusResult" class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="viewJobsModal" type="button" class="btn btn-primary">Refresh Status</button>
            </div>
        </div>
    </div>
</div>