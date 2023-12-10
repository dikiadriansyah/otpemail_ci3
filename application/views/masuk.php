<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Masuk</title>
	<style>
		input,button{
			margin-top: 10px;
		}
	</style>
</head>
<body>

	<h1>Login Dengan OTP</h1>

	<?php if($this->session->flashdata('gagal')){ ?>  
          <span style="color: red;"><?php echo $this->session->flashdata('gagal'); ?></span>
    <?php } ?>

    <?php if($this->session->flashdata('sukses')){ ?>  
          <span style="color: green;"><?php echo $this->session->flashdata('sukses'); ?></span>
    <?php } ?>
	<form action="<?php echo base_url('index.php/welcome/login') ?>" method="post">
		<input type="email" name="email" placeholder="Masukkan Email" required><br>
		<input type="password" name="pass" placeholder="Masukkan Password" required><br>
		<button type="submit" name="masuk">Login</button>
	</form>
</body>
</html>