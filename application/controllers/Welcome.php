<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	public $emailHost	= "adrdiki67@gmail.com";
	public $passHost 	= "";
	public $smptHost 	= "smtp.gmail.com";
	public $smtpPort 	= 465;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('string');
	}

	public function index()
	{
		$this->load->view('masuk');
	}

	function session1()
	{
		if ($this->session->userdata('statusloginaplikasiotp') == "pendinglogin") {
			redirect('welcome/validasi');
		}
	}

	function session2()
	{
		if ($this->session->userdata('statusloginaplikasiotp') != "pendinglogin" && $this->session->userdata('statusloginaplikasiotp') != "sukseslogin") {
			redirect('welcome');
		}
	}

	public function login()
	{
		date_default_timezone_set('Asia/Jakarta');

		$user = $this->input->post('email', TRUE);
		$pass = md5($this->input->post('pass', TRUE));

		$cek = $this->db->get_where('akunuser', array('email' => $user));

		if ($cek->num_rows() > 0) {
			$cek = $cek->row();
			if ($cek->pass == $pass) {

				$datauser = $this->db->get_where('akunuser', array('email' => $user, 'pass' => $pass))->row();

				$datasession = array(
					'emailaplikasiotp' 			=> $datauser->email,
					'namaaplikasiotp' 			=> $datauser->nama,
					'nomorhandphoneaplikasiotp' => $datauser->nomor_handphone,
					'statusloginaplikasiotp' 	=> 'pendinglogin',
				);

				$this->session->set_userdata($datasession);


				$kodeOtp =  random_string('numeric', 4);
				$tanggalSekarang = date('Y-m-d H:i:s');
				$datetime = new DateTime($tanggalSekarang);
				$datetime->modify('+10 minute');
				$tanggalKadaluarsa = $datetime->format('Y-m-d H:i:s');

				$data = array(
					'email' 				=> $user,
					'kode' 					=> $kodeOtp,
					'tanggal_kadaluarsa' 	=> $tanggalKadaluarsa,
					'status' 				=> 'Y'
				);

				$this->db->insert('kodeotp', $data);

				// Konfigurasi email
				$config = [
					'mailtype'	=> 'html',
					'charset'	=> 'utf-8',
					'protocol'	=> 'smtp',
					'smtp_host'	=> $this->smptHost,
					'smtp_user'	=> $this->emailHost,
					'smtp_pass'	=> $this->passHost,
					'smtp_crypto' => 'ssl',
					'smtp_port'	=> $this->smtpPort,
					'crlf'		=> "\r\n",
					'newline'	=> "\r\n"
				];

				// Load library email dan konfigurasinya
				$this->load->library('email', $config);

				// Email dan nama pengirim
				$this->email->from($this->emailHost, "Nama Web");

				// Email penerima
				$this->email->to($user); // Ganti dengan email tujuan

				// Lampiran email, isi dengan url/path file
				//$this->email->attach('');

				$isipesan = "Kode OTP : " . $kodeOtp;

				// Subject email
				$this->email->subject("Kode OTP anda");

				// Isi email
				$this->email->message($isipesan);

				// Kirim Email
				if ($this->email->send()) {
					redirect('welcome/validasi');
				} else {
					$this->db->set('status', 'Y');
					$this->db->where('email', $user);
					$this->db->update('kodeotp');

					$this->session->set_flashdata('gagal', "kode otp gagal dikirim, silahkan coba login kembali");
					redirect('welcome');
				}
			} else {
				$this->session->set_flashdata('gagal', "Kata sandi salah");
				redirect('welcome');
			}
		} else {
			$this->session->set_flashdata('gagal', "Nama Pengguna Ditolak");
			redirect('welcome');
		}
	}

	public function validasi()
	{
		$this->session2();
		$this->load->view('validasi');
	}

	public function validasiproses()
	{
		date_default_timezone_set('Asia/Jakarta');
		$user = $this->session->userdata('emailaplikasiotp');
		$kode = $this->input->post('kodeotp', TRUE);
		$waktuSekarang = date('Y-m-d H:i:s');

		$cek = $this->db->get_where('kodeotp', array('email' => $user, 'kode' => $kode, 'status' => 'Y'));
		if ($cek->num_rows() > 0) {
			$cek = $cek->row();

			if ($waktuSekarang > $cek->tanggal_kadaluarsa) {
				$this->session->set_flashdata('gagal', "Kode OTP tidak valid");
				redirect('welcome/validasi');
			} else {

				$datasession = array(
					'statusloginaplikasiotp' 	=> 'sukseslogin',
				);

				$this->session->set_userdata($datasession);

				$this->db->set('status', 'N');
				$this->db->where('email', $user);
				$this->db->update('kodeotp');

				redirect('welcome/dashboard');
			}
		} else {
			$this->session->set_flashdata('gagal', "Kode OTP tidak valid");
			redirect('welcome/validasi');
		}
	}

	public function kirimulang()
	{
		date_default_timezone_set('Asia/Jakarta');
		$kodeOtp =  random_string('numeric', 4);
		$tanggalSekarang = date('Y-m-d H:i:s');
		$datetime = new DateTime($tanggalSekarang);
		$datetime->modify('+10 minute');
		$tanggalKadaluarsa = $datetime->format('Y-m-d H:i:s');

		$this->db->set('status', 'N');
		$this->db->where('email', $this->session->userdata('emailaplikasiotp'));
		$this->db->update('kodeotp');


		$data = array(
			'email' 				=> $this->session->userdata('emailaplikasiotp'),
			'kode' 					=> $kodeOtp,
			'tanggal_kadaluarsa' 	=> $tanggalKadaluarsa,
			'status' 				=> 'Y'
		);

		$this->db->insert('kodeotp', $data);


		// Konfigurasi email
		$config = [
			'mailtype'	=> 'html',
			'charset'	=> 'utf-8',
			'protocol'	=> 'smtp',
			'smtp_host'	=> $this->smptHost,
			'smtp_user'	=> $this->emailHost,
			'smtp_pass'	=> $this->passHost,
			'smtp_crypto' => 'ssl',
			'smtp_port'	=> $this->smtpPort,
			'crlf'		=> "\r\n",
			'newline'	=> "\r\n"
		];

		// Load library email dan konfigurasinya
		$this->load->library('email', $config);

		// Email dan nama pengirim
		$this->email->from($this->emailHost, "Nama Web");

		// Email penerima
		$this->email->to($user); // Ganti dengan email tujuan

		// Lampiran email, isi dengan url/path file
		//$this->email->attach('');

		$isipesan = "Kode OTP : " . $kodeOtp;

		// Subject email
		$this->email->subject("Kode OTP anda");

		// Isi email
		$this->email->message($isipesan);

		// Kirim Email
		if ($this->email->send()) {

			$this->session->set_flashdata('sukses', "kode otp berhasil dikirim ulang");
			redirect('welcome/validasi');
		} else {
			$this->db->set('status', 'N');
			$this->db->where('email', $this->session->userdata('emailaplikasiotp'));
			$this->db->update('kodeotp');

			$this->session->set_flashdata('gagal', "kode otp gagal dikirim, silahkan coba login kembali");
			redirect('welcome/validasi');
		}
	}

	public function dashboard()
	{
		$this->session1();
		$this->session2();
		$this->load->view('dashboard');
	}

	public function keluar()
	{
		$this->session->sess_destroy();
		redirect('welcome');
	}
}
