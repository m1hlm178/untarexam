<?php
session_start();
include "../../../database/db_conection.php"; 
if(!$_SESSION['username'])  
{  
    header("Location: ../../index.php");//redirect to login page to secure the welcome page without login access.  
}
if(isset($_POST['Input-Edit'])){
	
	//inlcude atau memasukkan file koneksi ke database
	//jika tombol tambah benar di klik maka lanjut prosesnya
//        echo "fuck";
	$nama           = $_POST['nama'];
    $telephone  	= $_POST['telp'];
	$email          = $_POST['email'];
    $user           = $_SESSION['username'];
	$pass           = $_POST['pass'];
    $npass          = $_POST['cpass'];
    $pass = sha1($pass);
//        echo $user." ".$pass;
        $cek = $dbcon->query("SELECT * FROM login_mhs WHERE id = '$user' AND pass = '$pass'");
	$cek1 = mysqli_num_rows($cek);
	if($cek1>0)
	{  
            $errors = array();
            if($_FILES['image']['size'] > 0){
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
            $filecontent = addslashes($_FILES['image']['tmp_name']);
            $filecontent = file_get_contents($filecontent);
            $filecontent = base64_encode($filecontent);

            $fileType=strtolower(end(explode('.',$_FILES['image']['name'])));
            $expensions= array("jpeg","jpg","png");

            if(in_array($fileType,$expensions)=== false){
            $errors="Format Yang Di UPLOAD Salah!!";
            }elseif ($fileSize > 1457390){
            $errors='File size must be excately 1.5 MB';
            }


            if(empty($errors)==true){
            $r = mysqli_fetch_array(mysqli_query($dbcon,"SELECT * FROM detail_mhs WHERE nim = '".$_SESSION['username']."'"));
            $cekfhoto = $dbcon->query("SELECT * FROM fhoto_mhs WHERE detail_mhs_nim = '$r[0]'");
            $cekfhoto1 = mysqli_num_rows($cekfhoto);
            if($cekfhoto1>0){
                $dbcon->query("UPDATE fhoto_mhs SET name='$fileName',size='$fileSize',type='$fileType',content='$filecontent' WHERE detail_mhs_nim='$r[0]'");
            }else{
                $dbcon->query("INSERT into fhoto_mhs(name, size, type, content, detail_mhs_nim, detail_mhs_idjurusan, detail_mhs_idfalkutas) values ('$fileName', '$fileSize', '$fileType', '$filecontent', '$r[0]', '$r[5]', '$r[6]')");
            }}}
            if(empty($npass)==true AND empty($errors)==true)
            {
                $dbcon->query("UPDATE detail_mhs SET nama='$nama', telephone='$telephone', email='$email' WHERE nim='$user'");
                $_SESSION['notif'] = "Update Data Sukses!!";
                header("Location: {$_SERVER['HTTP_REFERER']}");
            }
            elseif (empty($errors)==true) 
            {
                $npass = sha1($npass);
                $dbcon->query("UPDATE detail_mhs SET nama='$nama', telephone='$telephone', email='$email' WHERE nim='$user'");
                $dbcon->query("UPDATE login_mhs SET pass='$npass', firsttime='NO' WHERE id='$user'");
                $_SESSION['notif'] = "Update Data Sukses!!";
                header("Location: {$_SERVER['HTTP_REFERER']}");
            }else{
                $_SESSION['notif'] = "Data Yang Dimasukan Tidak Valids!!<br>".$errors;
                header("Location: {$_SERVER['HTTP_REFERER']}");
            }
            } else {
                $_SESSION['notif'] = "Password Salah!!";
                header("Location: {$_SERVER['HTTP_REFERER']}");
            }
}
$dbcon->close(); 
?>