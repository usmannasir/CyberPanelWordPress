<?php include("header.php"); ?>

    <div class="container-fluid WPCPContainer">
        <div class="card-header">
            <h4 class="my-0 font-weight-normal">Emails</h4>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="emailTemplate" class="sr-only">Select Template</label>
                    <select style="max-width: 100%" class="form-control" id="emailTemplate">
                        <option>Select</option>
                        <option>New Server Created</option>
                        <option>Server Cancelled</option>
                        <option>Server Suspended</option>
                        <option>Server Terminated</option>
                    </select>
                    <small id="Name" class="form-text text-muted">Select and configure email templates used by CyberPanel plugin.</small>
                </div>
                <div class="form-group">
                    <label for="templateContent">Content of template</label>
                    <textarea class="form-control" id="templateContent" rows="3"></textarea>
                </div>
                <button id="saveTemplate" type="button" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

<?php include("footer.php"); ?>



