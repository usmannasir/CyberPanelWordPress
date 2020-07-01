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
                        <option>Hetzner</option>
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
                    <small class="form-text text-muted">Actual API Token From Hetzner</small>
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Image ID</label>
                    <input type="text" id="imageID" class="form-control" placeholder="Token" required>
                    <small class="form-text text-muted">Image ID that will be used to create the server.</small>
                </div>
                <button id="connectProvider" type="button" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

<?php include("footer.php"); ?>