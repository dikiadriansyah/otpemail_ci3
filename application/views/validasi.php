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

	<h1>Masukkan Kode OTP</h1>

	<?php if($this->session->flashdata('gagal')){ ?>  
          <span style="color: red;"><?php echo $this->session->flashdata('gagal'); ?></span>
    <?php } ?>

    <?php if($this->session->flashdata('sukses')){ ?>  
          <span style="color: green;"><?php echo $this->session->flashdata('sukses'); ?></span>
    <?php } ?>
	<form action="<?php echo base_url('index.php/welcome/validasiproses') ?>" method="post">
		<input type="text" name="kodeotp" placeholder="Masukkan kode OTP" required><br>
		<button type="submit" name="validasi">Verifikasi</button>
		<p>
			batas waktu <span id="waktu"></span><br>
			Tidak menerima sms kode otp? <a href="<?php echo base_url('index.php/welcome/kirimulang') ?>">kirim ulang</a><br>
			<a href="<?php echo base_url('index.php/welcome') ?>">Kembali Login</a>
		</p>
	</form>


	<script>
	var minutesToAdd=10;
	var currentDate = new Date();
	var countDownDate = new Date(currentDate.getTime() + minutesToAdd*60000);

	var x = setInterval(function() {

	  var now = new Date().getTime();
	    
	  var distance = countDownDate - now;
	    
	  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	    
	  document.getElementById("waktu").innerHTML = minutes + ":" + seconds;
	    
	  if (distance < 0) {
	    clearInterval(x);
	    document.getElementById("waktu").innerHTML = "00:00";
	  }
	}, 1000);
	</script>
</body>
</html>