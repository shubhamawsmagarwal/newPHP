<?php
session_start();
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
$method=$_SERVER['REQUEST_METHOD'];
$url=$_GET['url'];
$isLoggedIn=(isset($_SESSION['username']));
$databaseConnection=mysqli_connect(getenv('host'),getenv('user'),getenv('password'),getenv('database'));
if (mysqli_connect_errno())
	die("Failed to connect to database server". mysqli_connect_error());
if($method==='GET'){
    if($url==='index.php'){
        include "views/home.php";
    }else if(($url==='register' && !$isLoggedIn)){
            include "views/register.php";
    }else if(($url==='login' && !$isLoggedIn)||($url==='user' && !$isLoggedIn)){
        include "views/login.php";
    }else if(($url==='user' && $isLoggedIn)||($url==='login' && $isLoggedIn)||($url==='register' && $isLoggedIn)){
        $username=$_SESSION['username'];
        $query="SELECT `path` FROM `s3Table` WHERE `username`='$username'";
        @$queryResult=mysqli_query($databaseConnection,$query);
        $pathArray =array();
        while($row = mysqli_fetch_assoc($queryResult)) {
            array_unshift($pathArray,$row['path']);
        }
        include "views/user.php";
    }else if($url==='logout'){
        if($isLoggedIn){
            unset($_SESSION['username']);
            header("Refresh:0; url=login");
        }else{
            echo "Login Dude First!!!!!!!";
        }
    }else if(substr($url,0,5)==='user/' && strlen($url)!==5){
        $s='test_example/'.substr($url,5);
        $username=$_SESSION['username'];
        $query="SELECT `id` FROM `s3Table` WHERE `username`='$username' AND `path`='$s'";
        @$queryResult=mysqli_query($databaseConnection,$query);
        if(mysqli_num_rows($queryResult)===1){
            try {
                $s3 = S3Client::factory(
                  array(
                    'credentials' => array(
                      'key' => getenv('IAM_PUBLIC'),
                      'secret' => getenv('IAM_PRIVATE')
                    ),
                    'version' => 'latest',
                    'region'  => 'us-east-1'
                  )
                );
                $result = $s3->getObject(array(
                  'Bucket' => getenv('bucketName'),
                  'Key'    => $s
                ));
                header("Content-Type: {$result['ContentType']}");
                header('Content-Disposition: filename="' . basename($s) . '"');
                echo $result['Body'];
            } catch (Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }else{
            include "views/error.php";
        }
    }
    else{
        include "views/error.php";
    }
}else if($method==='POST'){
    if($url==='register' && !$isLoggedIn){
        $username=$_POST['username'];
        $password=$_POST['password'];
        $query="SELECT `id` FROM `users` WHERE `username`='$username'";
        @$queryResult=mysqli_query($databaseConnection,$query);
        if(mysqli_num_rows($queryResult)===1){
            $message='Email already exists!!!!!!!!!!';
            echo "<script type='text/javascript'>alert('$message');</script>";
            header("Refresh:0; url=register");
        }else{
            $query="INSERT INTO `users`(`username`, `password`) VALUES ('$username','$password')";
            @$queryResult=mysqli_query($databaseConnection,$query);
            $_SESSION['username']=$username;
            header("Refresh:0; url=user");
        }
    }else if($url==='login' && !$isLoggedIn){
        $username=$_POST['username'];
        $password=$_POST['password'];
        $query="SELECT `id` FROM `users` WHERE `username`='$username' AND `password`='$password'";
        @$queryResult=mysqli_query($databaseConnection,$query);
        if(mysqli_num_rows($queryResult)===1){
            $_SESSION['username']=$username;
            header("Refresh:0; url=user");
        }else{
            $message='Email or password incorrect!!!!!!!';
            echo "<script type='text/javascript'>alert('$message');</script>";
            header("Refresh:0; url=login");
        }
    }else if($url==='upload' && $isLoggedIn){
    	try {
    		$s3 = S3Client::factory(
    			array(
    				'credentials' => array(
    					'key' => getenv('IAM_PUBLIC'),
    					'secret' => getenv('IAM_PRIVATE')
    				),
    				'version' => 'latest',
    				'region'  => 'us-east-1'
    			)
    		);
    	} catch (Exception $e) {
    		die("Error: " . $e->getMessage());
    	}
    	//test_example is folder name keyname is path of file inside s3
    	$path= 'test_example/' . basename($_FILES["fileToUpload"]['name']);
    	try {
    		$file = $_FILES["fileToUpload"]['tmp_name'];
    		$s3->putObject(
    			array(
    				'Bucket'=>getenv('bucketName'),
    				'Key' =>  $path,
    				'SourceFile' => $file,
    				'StorageClass' => 'REDUCED_REDUNDANCY'
    			)
    		);
    	} catch (S3Exception $e) {
    		die('Error:' . $e->getMessage());
    	} catch (Exception $e) {
    		die('Error:' . $e->getMessage());
    	}
    	$username=$_SESSION['username'];
    	$query="INSERT INTO `s3Table`(`username`, `path`) VALUES ('$username','$path')";
        @$queryResult=mysqli_query($databaseConnection,$query);
        header("Refresh:0; url=user");
    }else{
        header("Refresh:0; url=error");
    }
}
?>