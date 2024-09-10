
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="<?= asset("css/style.css"); ?>">
</head>

<body style="padding: 20px;">

	<form action="" enctype="multipart/form-data" method="post">
		<h2>Multiple files upload</h2>
		<input type="file" name="my_file[]" multiple>
		<br><br>
		<input type="submit" value="Upload">
	</form>

	<br>
	<form action="" enctype="multipart/form-data" method="post">
		<h2>Single file upload</h2>
		<input type="file" name="my_file">
		<br><br>
		<input type="submit" value="Upload">
	</form>

	<br />
	<a href="<?=route("lienhe"); ?>">Nhấn để vào trang liên hệ</a>
</body>

</html>
<?php

// Upload file, sử dụng name của thẻ input
upload_and_return_filename("my_file");

?>