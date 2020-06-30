<?php include 'partials/header.php'; ?>
<h2 class="text-center">Register Page</h2>
<a href="/" class="btn btn-lg btn-success basicButtons">Home</a>
<div class="container content">
    <form action="/register" method="post" class="text-center border border-light p-5" style="width:40%;">
        <input type="username" name="username" class="form-control mb-4" placeholder="E-mail" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" class="btn btn-info my-4 btn-block">Register</button>
    </form>
</div>
<?php include 'partials/footer.php'; ?>
            