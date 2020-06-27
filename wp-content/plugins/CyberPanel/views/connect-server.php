<?php include("header.php"); ?>

<div class="container">
    <div class="card-header">
        <h4 class="my-0 font-weight-normal">Connect</h4>
    </div>
    <div class="card-body">
        <form class="form-signin">
            <h1 class="h3 mb-3 font-weight-normal">Connect Servers</h1>
            <label for="hostname" class="sr-only">Hostname</label>
            <input type="text" id="hostname" class="form-control" placeholder="Hostname" required>
            <label for="inputEmail" class="sr-only">Username</label>
            <input type="text" id="username" class="form-control" placeholder="Username" required>
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" id="password" class="form-control" placeholder="Password" required>
            <button id="connectServer" class="btn btn-lg btn-primary btn-block" type="button">Connect</button>
        </form>
    </div>
</div>