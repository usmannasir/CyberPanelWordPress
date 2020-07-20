<?php include("header.php"); ?>

    <div class="container-fluid WPCPContainer">
        <div class="card-header">
            <h4 class="my-0 font-weight-normal">Configure Cloud Providers</h4>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="provider" class="sr-only">Select Cloud Company</label>
                    <select style="max-width: 100%" class="form-control" id="provider">
                        <option>Select</option>
                        <option>Hetzner</option>
                        <option>DigitalOcean</option>
                    </select>
                    <small id="Name" class="form-text text-muted">Select cloud provider from the list.</small>
                </div>
                <div class="form-group">
                    <label for="hostname" class="sr-only">Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Name" required>
                    <small class="form-text text-muted">Give Name to this API.</small>
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Token</label>
                    <input type="text" id="token" class="form-control" placeholder="Token" required>
                    <small class="form-text text-muted">Actual API Token From Hetzner/DigitalOcean</small>
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Image ID</label>
                    <input type="text" id="imageID" class="form-control" placeholder="Image ID" required>
                    <small class="form-text text-muted">Image ID that will be used to create the server.</small>
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



