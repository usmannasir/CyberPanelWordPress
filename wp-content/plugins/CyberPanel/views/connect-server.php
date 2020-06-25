<?php include("header.php"); ?>

<div style="margin-top: 3%" class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <form class="form-signin">
            <h1 class="h3 mb-3 font-weight-normal">Connect Servers</h1>
            <label for="hostname" class="sr-only">Hostname</label>
            <input type="text" id="hostname" class="form-control" placeholder="Hostname" required autofocus>
            <label for="inputEmail" class="sr-only">Username</label>
            <input type="text" id="username" class="form-control" placeholder="Username" required>
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" id="password" class="form-control" placeholder="Password" required>
            <button id="connectServer" class="btn btn-lg btn-primary btn-block" type="button">Connect</button>
        </form>
    </div>

</div>
