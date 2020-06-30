<?php include 'partials/header.php'; ?>
<h2 class="text-center">Welcome:-<?php echo $username?></h2>
<a href="/logout" class="btn btn-lg btn-danger basicButtons">Logout</a>
<a href="/" class="btn btn-lg btn-success basicButtons">Home</a>
<div class="container content">
    <div>
        <form action="/upload" method="post" enctype="multipart/form-data">
            Select file to upload:
            <input type="file" name="fileToUpload">
            <input type="submit" value="Upload file" class="btn btn-lg btn-primary">
        </form>
    </div>
    <h3 class="text-center my-4">Your Uploaded Files</h3>
    <div>
        <?php
            for($i=0;$i<sizeof($pathArray);$i++){
                $s=substr($pathArray[$i],13);
                echo '<div class="text-center"><a href="/user/'.$s.'" class="btn btn-info my-4">'.$s.'</a></div>';
            }
        ?>
    </div>
</div>
<?php include 'partials/footer.php'; ?>