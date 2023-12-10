<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	
		<h1>Selamat datang <b> <?php echo $this->session->userdata('namaaplikasiotp') ?> </b></h1>
		<a href="<?php echo base_url('index.php/welcome/keluar') ?>">Keluar</a>
</body>
</html>