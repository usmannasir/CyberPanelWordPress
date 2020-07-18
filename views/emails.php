<?php include("header.php"); ?>

    <div class="container-fluid WPCPContainer">
        <div class="card-header">
            <h4 class="my-0 font-weight-normal">Emails</h4>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="provider" class="sr-only">Select Template</label>
                    <select style="max-width: 100%" class="form-control" id="provider">
                        <option>Select</option>
                        <option>New Server Created</option>
                        <option>Server Cancelled</option>
                        <option>Server Suspended</option>
                        <option>Server Terminated</option>
                    </select>
                    <small id="Name" class="form-text text-muted">Select cloud provider from the list.</small>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">Example textarea</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    <small class="form-text text-muted">Give Name to this API.</small>
                </div>
                <button id="connectProvider" type="button" class="btn btn-primary">Save</button>
            </form>

            <table style="margin-top: 2%" class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Provider</th>
                    <th scope="col">API Details</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody id="wpcp_providerapis">
                </tbody>
            </table>
        </div>
    </div>

<?php include("footer.php"); ?>



