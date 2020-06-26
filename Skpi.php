<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Afif Wijaya
 * Date: 16/10/2019
 * Time: 8:25
 */
class Skpi extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library(array('template', 'form_validation'));
		$this->load->model(array('Admin', 'Count', 'Import'));
		$this->load->helper('tgl_indo');
	}

	public function index() {
        $this->cek_login();
		
		$sessionmahasiswa=array('id_prodi','id_tahun_akademik');
		$this->session->unset_userdata($sessionmahasiswa);
		$id_fkl=$this->session->userdata('id_fkl');

		$data['prodi']=$this->Admin->get_data_where('tbl_program_studi', array('id_fakultas'=> $id_fkl), 'nama_prodi');
		$data['angkatan']=$this->Admin->get_desc_groupby('tbl_mahasiswa', 'angkatan', 'angkatan');
		
		$table = "tbl_mahasiswa fk JOIN
		tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
		tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas) JOIN 
		tbl_skpi skpi on(skpi.id_mahasiswa=fk.id_mahasiswa)";
		$data['data']=$this->Admin->get_whereorder_asc($table, ['fk.id_pengguna' => $id_fkl] ,'fk.npm');
		$data['data_edit']=$this->Admin->get_whereorder_asc($table, ['fk.id_pengguna' => $id_fkl] ,'fk.npm');
		$data['data_hapus']=$this->Admin->get_whereorder_asc($table, ['fk.id_pengguna' => $id_fkl] ,'fk.npm');

		$tablemhs="
		tbl_mahasiswa fk JOIN
		tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
		tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
		$data['mahasiswa1']=$this->Admin->get_data_asc($tablemhs, 'npm');

		
		if($this->input->post('submit', TRUE)=='Submit') {	
			
            $id_prodi=$this->input->post('id_prodi',TRUE);
            $id_tahun_akademik=$this->input->post('id_tahun_akademik', TRUE);
            
            if(isset($_POST['id_prodi']))
            {
                $datafilter=array (
                        'id_prodi'=> $this->input->post('id_prodi'),
                        'id_tahun_akademik'=> $this->input->post('id_tahun_akademik'));

                $this->session->set_userdata($datafilter);
            }

            echo '<script type="text/javascript">window.location.replace("'.base_url().'admin/skpi/filter/'.$id_prodi.'/'.$id_tahun_akademik.'")</script>';
			
		}

        $this->template->admin('admin/mahasiswa/skpi', $data);
	}

	public function add_mahasiswa() {
        $this->cek_login();
		
		$data['prodi']=$this->Admin->get_data_asc('tbl_program_studi', 'nama_prodi');
		$data['angkatan']=$this->Admin->get_desc_groupby('tbl_mahasiswa', 'angkatan', 'angkatan');
		
		$tablemhs="
		tbl_mahasiswa fk JOIN
		tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
		tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
		$data['mahasiswa']=$this->Admin->get_data_asc($tablemhs, 'npm');

		$this->cek_login();
		if($this->input->post('submit', TRUE)=='Submit') {
			//insert
			$id_mhs=$this->input->post('id_mahasiswa');
			
			$mhs = array(
				'tgl_lulus' => $this->input->post('tgl_lulus'),
				'tgl_masuk'=> $this->input->post('tgl_masuk', TRUE),
			);

			$this->Admin->update('tbl_mahasiswa', $mhs, array('id_mahasiswa' => $id_mhs));
			
			$skpi=array (
				'id_mahasiswa'=> $this->input->post('id_mahasiswa', TRUE),
				'nomor_ijazah'=> $this->input->post('nomor_ijazah', TRUE),
				'nomor_skpi'=> $this->input->post('nomor_skpi', TRUE),
			);
			
			$this->Admin->insert('tbl_skpi', $skpi);
			$this->session->set_flashdata('tambah','Data Berhasil ditambah');
			redirect('admin/skpi');
		}
	}

	function perbarui_skpi() {
		$this->cek_login();
		$id_prodi=$_SESSION['id_prodi'];
		$id_tahun_akademik=$_SESSION['id_tahun_akademik'];
		if ($this->input->post('perbarui', TRUE)=='perbarui') {

			$id_mhs=$this->input->post('id_mahasiswa');

			$mhs = array(
				'tgl_lulus' => $this->input->post('tgl_lulus'),
				'tgl_masuk'=> $this->input->post('tgl_masuk', TRUE),
			);

			$skpi = array (
				'nomor_ijazah'=> $this->input->post('nomor_ijazah', TRUE),
				'nomor_skpi'=> $this->input->post('nomor_skpi', TRUE),
			);
			
			$this->Admin->update('tbl_skpi', $skpi, array('id_mahasiswa'=> $id_mhs));
			$this->Admin->update('tbl_mahasiswa', $mhs, array('id_mahasiswa' => $id_mhs));
			$this->session->set_flashdata('ubah_mhs','Data Berhasil diperbarui');
			echo '<script type="text/javascript">window.location.replace("'.base_url().'admin/skpi/filter/'.$id_prodi.'/'.$id_tahun_akademik.'")</script>';
		}
	}

	public function add_mahasiswa_filter() {
        $this->cek_login();
		$id_prodi=$_SESSION['id_prodi'];
		$id_tahun_akademik=$_SESSION['id_tahun_akademik'];
		
		$tablemhs="
		tbl_mahasiswa fk JOIN
		tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
		tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
		$data['mahasiswa']=$this->Admin->get_data_asc($tablemhs, 'npm');

		$this->cek_login();
		if($this->input->post('submit', TRUE)=='Submit') {
			//insert

			$id_mhs=$this->input->post('id_mahasiswa');
			
			$mhs = array('tgl_lulus' => $this->input->post('tgl_lulus'),
			'tgl_masuk'=> $this->input->post('tgl_masuk', TRUE),);

			$this->Admin->update('tbl_mahasiswa', $mhs, array('id_mahasiswa' => $id_mhs));
			
			$skpi=array ('id_mahasiswa'=> $this->input->post('id_mahasiswa', TRUE),
				'nomor_ijazah'=> $this->input->post('nomor_ijazah', TRUE),
				'nomor_skpi'=> $this->input->post('nomor_skpi', TRUE),
				
			);

			$this->Admin->insert('tbl_skpi', $skpi);

			$this->session->set_flashdata('tambah','Data Berhasil ditambah');
			echo '<script type="text/javascript">window.location.replace("'.base_url().'admin/skpi/filter/'.$id_prodi.'/'.$id_tahun_akademik.'")</script>';
		}
	}

	public function delete_skpi() {
		$this->cek_login();
		$id=$this->session->userdata('id_fkl');
		$id_prodi=$_SESSION['id_prodi'];
		$id_tahun_akademik=$_SESSION['id_tahun_akademik'];
		if($this->input->post('Hapus', TRUE)=='Hapus') {

			$id_skpi = $this->input->post('id_skpi');

			$this->Admin->delete('tbl_skpi', array('id_skpi'=>$id_skpi));
			echo '<script type="text/javascript">window.location.replace("'.base_url().'admin/skpi/filter/'.$id_prodi.'/'.$id_tahun_akademik.'")</script>';
		}
	}

	public function cetak_data_mahasiswa() {
		$this->cek_login();
		$id=$this->uri->segment(4);
		$table_informasi="tbl_mahasiswa fk JOIN 
		tbl_program_studi pn ON (pn.id_prodi = fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas = pn.id_fakultas)";
		$informasi=$this->Admin->get_where($table_informasi, array('fk.id_mahasiswa'=> $id));
		foreach ($informasi->result() as $key) {
			$data['sistem_penilaian']=$key->sistem_penilaian;
            $data['jenis_pendidikan']=$key->jenis_pendidikan;
			$data['type_pendidikan']=$key->type_pendidikan;
			$data['jenjang_pendidikan_skpi']=$key->jenjang_pendidikan_skpi;
			$data['level_education']=$key->level_education;
            $data['pendidikan_lanjutan']=$key->pendidikan_lanjutan;
			$data['further_study']=$key->further_study;
			$data['bahasa_pengantar']=$key->bahasa_pengantar;
			$data['language']=$key->language;
			$data['level_kkni']=$key->level_kkni;
			$data['id_prodi']=$key->id_prodi;
		}
		$universitas=$this->Admin->get_all('tbl_informasi');
		foreach ($universitas->result() as $key) {
			$data['nama_universitas']=$key->nama_universitas;
			$data['nomor_sk_pt']=$key->nomor_sk_pt;
			$data['gambar']=$key->gambar;
		}
        $table="tbl_mahasiswa fk JOIN 
		tbl_program_studi pn ON (pn.id_prodi = fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas = pn.id_fakultas) JOIN
		tbl_skpi sk ON (sk.id_mahasiswa = fk.id_mahasiswa)";
		$data['datam']=$this->Admin->get_where($table, array('fk.id_mahasiswa'=> $id));
		$table1="tbl_prestasi ps JOIN
		tbl_detail_prestasi ds ON (ds.id_prestasi=ps.id_prestasi) JOIN
		tbl_mahasiswa fk ON (fk.id_mahasiswa=ds.id_mahasiswa) JOIN
		tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
        tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
        $data['prestasi']=$this->Admin->get_whereorder_desc($table1, array('ds.file_pendukung_prestasi !=' => '', 'ps.status_prestasi' => 'Y','fk.id_mahasiswa'=> $id), 'ps.tanggal_prestasi');
		$prestasi = $this->Count->count_where($table1, ['fk.id_mahasiswa'=> $id, 'ps.status_prestasi' => 'Y', 'file_pendukung_prestasi !=' => '']);
		$data['prestasi1'] = $prestasi;
		$table2="tbl_keorganisasian ko JOIN
        tbl_pengurus_organisasi po on (po.id_keorganisasian=ko.id_keorganisasian) JOIN
        tbl_detail_pengurus dp on (dp.id_pengurus=po.id_pengurus) JOIN
        tbl_mahasiswa fk on (fk.id_mahasiswa=dp.id_mahasiswa) JOIN 
        tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
        tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
		$data['pengurus']=$this->Admin->get_whereorder_asc($table2, array('dp.status_pengurus' => 'Y','fk.id_mahasiswa'=> $id), 'dp.jabatan_organisasi');
		$pengurus = $this->Count->count_where($table2, ['fk.id_mahasiswa'=> $id, 'dp.status_pengurus' => 'Y']);
		// $pengurus = 0;
		$data['pengurus1'] = $pengurus;
		$table3="tbl_kegiatan_kemahasiswaan ps JOIN
        tbl_panitia_kegiatan_kemahasiswaan ds ON (ds.id_kegiatan_kemahasiswaan=ps.id_kegiatan_kemahasiswaan) JOIN
        tbl_mahasiswa fk ON (fk.id_mahasiswa=ds.id_mahasiswa) JOIN 
        tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
        tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
		$data['panitia']=$this->Admin->get_whereorder_asc($table3, array('ps.status_kegiatan_kemahasiswaan' => 'Y','ds.status_panitia' => 'Y' ,'fk.id_mahasiswa'=> $id),'ds.jabatan_panitia');
		$panitia = $this->Count->count_where($table3, ['fk.id_mahasiswa'=> $id, 'ds.status_panitia' => 'Y']);
		$data['panitia1'] = $panitia;
		$table4="tbl_seminar ps JOIN
		tbl_detail_seminar ds ON (ds.id_seminar=ps.id_seminar) JOIN
		tbl_mahasiswa fk ON (fk.id_mahasiswa=ds.id_mahasiswa) JOIN
		tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
		tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
        $data['seminar']=$this->Admin->get_whereorder_asc($table4, array('ps.status_seminar' => 'Y','ds.status_seminar_mhs' => 'Y' ,'fk.id_mahasiswa'=> $id) ,'ps.tanggal_swp');
		$seminar = $this->Count->count_where($table4, ['fk.id_mahasiswa'=> $id, 'ds.status_seminar_mhs' => 'Y']);
		$data['seminar1'] = $seminar;
		$table5="tbl_uji_kompetensi ps JOIN
		tbl_detail_uji_kompetensi ds ON (ds.id_uji_kompetensi=ps.id_uji_kompetensi) JOIN
		tbl_mahasiswa fk ON (fk.id_mahasiswa=ds.id_mahasiswa) JOIN
		tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas)";
		$data['uji_kompetensi']=$this->Admin->get_whereorder_asc($table5, array('ps.status_uji_kompetensi' => 'Y','ds.status_uji_kompetensi_mhs' => 'Y' ,'fk.id_mahasiswa'=> $id )  ,'fk.npm' );
		$uji_kompetensi = $this->Count->count_where($table5, ['fk.id_mahasiswa'=> $id, 'ds.status_uji_kompetensi_mhs' => 'Y']);
		$data['uji_kompetensi1'] = $uji_kompetensi;
		$table6="tbl_mahasiswa fk JOIN 
		tbl_program_studi pn ON (pn.id_prodi = fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas = pn.id_fakultas) JOIN
		tbl_sikap sk ON (sk.id_prodi= pn.id_prodi)";
		$data['data_sikap']=$this->Admin->get_whereorder_asc($table6, array('fk.id_mahasiswa'=>$id, 'status' => 'Y'), 'sk.id_sikap');
		$sikap = $this->Count->count_where($table6, ['fk.id_mahasiswa'=> $id, 'sk.status' => 'Y']);
		$data['data_sikap1'] = $sikap;
		$table7="tbl_mahasiswa fk JOIN 
		tbl_program_studi pn ON (pn.id_prodi = fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas = pn.id_fakultas) JOIN
		tbl_keterampilan_umum sk ON (sk.id_prodi= pn.id_prodi)";
        $data['data_ku']=$this->Admin->get_whereorder_asc($table7, array('fk.id_mahasiswa'=>$id, 'status' => 'Y'), 'sk.id_keterampilan_umum');
		$ku = $this->Count->count_where($table7, ['fk.id_mahasiswa'=> $id, 'sk.status' => 'Y']);
		$data['data_ku1'] = $ku;
		$table8="tbl_mahasiswa fk JOIN 
		tbl_program_studi pn ON (pn.id_prodi = fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas = pn.id_fakultas) JOIN
		tbl_keterampilan_khusus sk ON (sk.id_prodi= pn.id_prodi)";
		$data['data_kk']=$this->Admin->get_whereorder_asc($table8, array('fk.id_mahasiswa'=>$id, 'status' => 'Y'),'sk.id_keterampilan_khusus');
		$kk = $this->Count->count_where($table8, ['fk.id_mahasiswa'=> $id, 'sk.status' => 'Y']);
		$data['data_kk1']  = $kk;
		$table9="tbl_mahasiswa fk JOIN 
		tbl_program_studi pn ON (pn.id_prodi = fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas = pn.id_fakultas) JOIN
		tbl_pengetahuan sk ON (sk.id_prodi= pn.id_prodi)";
		$data['data_pengetahuan']=$this->Admin->get_whereorder_asc($table9, array('fk.id_mahasiswa'=>$id, 'status' => 'Y'), 'sk.id_pengetahuan');
		$pengetahuan = $this->Count->count_where($table9, ['fk.id_mahasiswa'=> $id, 'sk.status' => 'Y']);
		$data['data_pengetahuan1']= $pengetahuan;
		$table10="tbl_mahasiswa fk JOIN 
		tbl_program_studi pn ON (pn.id_prodi = fk.id_prodi) JOIN 
		tbl_fakultas fkl ON (fkl.id_fakultas = pn.id_fakultas) JOIN
		tbl_dekan dk on (dk.id_fakultas = fkl.id_fakultas) join
		tbl_dosen ds on (ds.id_dosen=dk.id_dosen)";
		$dekan=$this->Admin->get_where($table10, array('fk.id_mahasiswa'=>$id, 'dk.status_dekan'=> "Y"));
		foreach ($dekan->result() as $key) {
			$data['nama_dosen']=$key->nama_dosen;
			$data['gelar_depan']=$key->gelar_depan;
			$data['gelar_belakang']=$key->gelar_belakang;
			$data['nik']=$key->nik;
			$data['nama_fakultas']=$key->nama_fakultas;
			$data['english_fakultas']=$key->english_fakultas;
		}
		if($sikap>0){
			$data['nomor_sikap'] = 'A';
		}

		if($ku>0 && $sikap==0 ) {
			$data['nomor_ku'] = 'A';
		}
		else if($sikap>0 && $ku>0){
			$data['nomor_ku'] = 'B';
		}

		if($sikap==0 && $ku==0 && $kk>0){
			$data['nomor_kk'] = 'A';
		}
		else if($sikap>0 && $ku==0 && $kk>0){
			$data['nomor_kk'] = 'B';
		}
		else if($sikap==0 && $ku>0 && $kk>0){
			$data['nomor_kk'] = 'B';
		}
		else if($sikap>0 && $ku>0 && $kk>0){
			$data['nomor_kk'] = 'C';
		}

		if($sikap==0 && $ku==0 && $kk==0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'A';
		}

		else if($sikap>0 && $ku==0 && $kk==0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'B';
		}
		else if($sikap==0 && $ku>0 && $kk==0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'B';
		}
		else if($sikap==0 && $ku==0 && $kk>0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'B';
		}

		else if($sikap>0 && $ku>0 && $kk==0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'C';
		}
		else if($sikap>0 && $ku==0 && $kk>0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'C';
		}
		else if($sikap==0 && $ku>0 && $kk>0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'C';
		}

		else if($sikap>0 && $ku>0 && $kk>0 && $pengetahuan>0){
			$data['nomor_pengetahuan'] = 'D';
		}

		$organisasi = $pengurus + $panitia;

		if($prestasi>0 ){
			$data['nomor_prestasi'] = '1';
		}

		if($prestasi==0 && $organisasi > 0) {
			$data['nomor_panitia'] = '1';
		}
		else if($prestasi > 0 && $organisasi>0){
			$data['nomor_panitia'] = '2';
		}

		if($prestasi==0 && $organisasi == 0 && $uji_kompetensi>0){
			$data['nomor_uji_kompetensi'] = '1';
		}
		else if($prestasi>0 && $organisasi == 0 && $uji_kompetensi>0){
			$data['nomor_uji_kompetensi'] = '2';
		}
		else if($prestasi==0 && $organisasi > 0 && $uji_kompetensi>0){
			$data['nomor_uji_kompetensi'] = '2';
		}

		else if($prestasi>0 && $organisasi > 0 && $uji_kompetensi>0){
			$data['nomor_uji_kompetensi'] = '3';
		}

		if($prestasi==0 && $organisasi==0 && $uji_kompetensi==0 && $seminar>0){
			$data['nomor_seminar'] = '1';
		}
		else if($prestasi>0 && $organisasi==0 && $uji_kompetensi==0 && $seminar>0){
			$data['nomor_seminar'] = '2';
		}
		else if($prestasi==0 && $organisasi>0 && $uji_kompetensi==0 && $seminar>0){
			$data['nomor_seminar'] = '2';
		}
		else if($prestasi==0 && $organisasi==0 && $uji_kompetensi>0 && $seminar>0){
			$data['nomor_seminar'] = '2';
		}
		
		else if($prestasi>0 && $organisasi>0 && $uji_kompetensi==0 && $seminar>0){
			$data['nomor_seminar'] = '3';
		}
		else if($prestasi>0 && $organisasi==0 && $uji_kompetensi>0 && $seminar>0){
			$data['nomor_seminar'] = '3';
		}
		else if($prestasi==0 && $organisasi>0 && $uji_kompetensi>0 && $seminar>0){
			$data['nomor_seminar'] = '3';
		}
		else if($prestasi>0 && $organisasi>0 && $uji_kompetensi>0 && $seminar>0){
			$data['nomor_seminar'] = '4';
		}

		$data['jabatan']=$this->Admin->get_all('tb_jabatan_organisasi');
        $this->load->view('admin/mahasiswa/cetak_data_mahasiswa', $data);
	}

	public function filter() {
		$id_fkl=$this->session->userdata('id_fkl');

		$data['prodi']=$this->Admin->get_data_where('tbl_program_studi', array('id_fakultas'=> $id_fkl), 'nama_prodi');
		$data['angkatan']=$this->Admin->get_desc_groupby('tbl_mahasiswa', 'angkatan', 'angkatan');
		$data['mahasiswa1']=$this->Admin->get_data_where('tbl_mahasiswa',array('id_prodi' => $_SESSION['id_prodi'], 'angkatan' => $_SESSION['id_tahun_akademik']) ,'npm');
		
		if($this->input->post('submit', TRUE)=='Submit') {	
			
            $id_prodi=$this->input->post('id_prodi',TRUE);
            $id_tahun_akademik=$this->input->post('id_tahun_akademik', TRUE);
            
            if(isset($_POST['id_prodi']))
            {
                $datafilter=array (
                        'id_prodi'=> $this->input->post('id_prodi'),
                        'id_tahun_akademik'=> $this->input->post('id_tahun_akademik'));

                $this->session->set_userdata($datafilter);
            }

            echo '<script type="text/javascript">window.location.replace("'.base_url().'admin/skpi/filter/'.$id_prodi.'/'.$id_tahun_akademik.'")</script>';
			
		}
        
		if($this->uri->segment(4)==0){
			if($this->uri->segment(5)==0){

				$table = "tbl_mahasiswa fk JOIN
				tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
				tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas) JOIN 
				tbl_skpi skpi on(skpi.id_mahasiswa=fk.id_mahasiswa)";
				$data['data']=$this->Admin->get_whereorder_asc($table, ['fk.id_pengguna' => $id_fkl] ,'fk.npm');

				$this->template->admin('admin/mahasiswa/skpi', $data);
            }
            
			else {
                $id_tahun_akademik = $this->uri->segment(5);
				
				$table = "tbl_mahasiswa fk JOIN
				tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
				tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas) JOIN 
				tbl_skpi skpi on(skpi.id_mahasiswa=fk.id_mahasiswa)";
				$data['data']=$this->Admin->get_whereorder_asc($table, ['fk.id_pengguna' => $id_fkl, 'angkatan'=> $id_tahun_akademik] ,'fk.npm');


				$this->template->admin('admin/mahasiswa/skpi', $data);

			}
		}
		else {
			if($this->uri->segment(5)==0){
				$id_prodi=$this->uri->segment(4);

				$table = "tbl_mahasiswa fk JOIN
				tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
				tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas) JOIN 
				tbl_skpi skpi on(skpi.id_mahasiswa=fk.id_mahasiswa)";
				$data['data']=$this->Admin->get_whereorder_asc($table, ['fk.id_pengguna' => $id_fkl, 'fk.id_prodi'=> $id_prodi] ,'fk.npm');


				$this->template->admin('admin/mahasiswa/skpi', $data);
			}
			else {
				$id_prodi=$this->uri->segment(4);
				$id_tahun_akademik=$this->uri->segment(5);
				$table = "tbl_mahasiswa fk JOIN
				tbl_program_studi pn ON (pn.id_prodi=fk.id_prodi) JOIN
				tbl_fakultas fkl ON (fkl.id_fakultas=pn.id_fakultas) JOIN 
				tbl_skpi skpi on(skpi.id_mahasiswa=fk.id_mahasiswa)";
				$data['data']=$this->Admin->get_whereorder_asc($table, ['fk.id_pengguna' => $id_fkl, 'fk.id_prodi'=> $id_prodi, 'angkatan'=> $id_tahun_akademik] ,'fk.npm');


				$this->template->admin('admin/mahasiswa/skpi', $data);
			}
		}
	}

	public function setting_skpi(){
		$id_fkl=$this->session->userdata('id_fkl');
        $pengaturan=$this->Admin->get_where('tbl_skpi_pengaturan', array('id_fakultas'=>$id_fkl));
		foreach ($pengaturan->result() as $key) {
			$data['id_pengaturan']=$key->id_pengaturan;
			$data['id_informasi']=$key->id_informasi;
			$data['id_fakultas']=$key->id_fakultas;
			$data['sistem_penilaian']=$key->sistem_penilaian;
            $data['jenis_jenjang_pendidikan']=$key->jenis_jenjang_pendidikan;
            $data['type_level_education']=$key->type_level_education;
            $data['persyaratan_penerimaan']=$key->persyaratan_penerimaan;
			$data['entry_requirement']=$key->entry_requirement;
			$data['bahasa_pengantar']=$key->bahasa_pengantar;
			$data['language']=$key->language;
			$data['level_kkni']=$key->level_kkni;
        }

		$this->template->admin('admin/mahasiswa/manage_setting', $data);
	}

	function cek_login() {
		if (!$this->session->userdata('level')) {
			redirect('login');
		}
	}
	public function upload(){
		$id=$this->session->userdata('id_fkl');
		$id_prodi=$_SESSION['id_prodi'];
		$id_tahun_akademik=$_SESSION['id_tahun_akademik'];
		include './admin_assets/ex/PHPExcel/IOFactory.php';
		if(isset($_FILES["file"]["name"])) {
			$path = $_FILES["file"]["tmp_name"];
			$object = PHPExcel_IOFactory::load($path);
			foreach($object->getWorksheetIterator() as $worksheet){
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				for($row=2; $row<=$highestRow; $row++){   

					$npm = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
					
					$tgl_masuk = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$tgl_lulus = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$no_ijazah = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$no_skpi = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

					$msk = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($tgl_masuk));
					$lls = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($tgl_lulus));

					$mhs=$this->Admin->get_where("tbl_mahasiswa",array("npm"=>$npm));
					foreach($mhs->result() as $up){
						$id_mhs=$up->id_mahasiswa;
						$get = array(
							'tgl_masuk'=> $msk,
							'tgl_lulus'=> $lls,
						);
						
						$skpi=array (
							'id_mahasiswa'=>$id_mhs,
							'nomor_ijazah'=>$no_ijazah,
							'nomor_skpi'=>$no_skpi,
						);
	
						$cek_npm=$this->Admin->get_where("tbl_skpi",array("id_mahasiswa"=>$id_mhs));
						if(count($cek_npm->result()) > 0) {
							
						}
						else {
							$this->Import->update('tbl_mahasiswa', $get, array('npm'=> $npm));
							$this->Import->insert('tbl_skpi', $skpi);
							
						}
					}
				}
			}
			$this->session->set_flashdata('tambah_mhs','Data Berhasil ditambah');
			echo '<script type="text/javascript">window.location.replace("'.base_url().'admin/skpi/filter/'.$id_prodi.'/'.$id_tahun_akademik.'")</script>';
		}
	}
	
	function import_skpi() {
    	$a=$_FILES['file_upload']['name'];
        //echo "$a";exit();
        $file=$_FILES['file_upload']['name'];
        $config['upload_path'] = './admin_assets/ex/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '10000';
        $config['overwrite'] = TRUE;
        $config['encrypt_name'] = FALSE;
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ( ! $this->upload->do_upload('file_upload')){
        $error = array('error' => $this->upload->display_errors());
            var_dump($error);
        }
        else{
		error_reporting(E_ALL);
            date_default_timezone_set('Asia/Jakarta');

            include './admin_assets/ex/PHPExcel/IOFactory.php';

            $inputFileName = './admin_assets/ex/' .$file;
            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

            //$objPHPExcel=$objReader->load(FCPATH.'uploads/excel/'.$file_name);
            $totalrows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Numbe of rows avalable in excel
            $objWorksheet=$objPHPExcel->setActiveSheetIndex(0);
        	for($i=2;$i<=$totalrows;$i++)
            {
				$npm= $objWorksheet->getCellByColumnAndRow(0,$i)->getValue();  
				$tahun_masuk= $objWorksheet->getCellByColumnAndRow(1,$i)->getValue(); 
				$tahun_keluar= $objWorksheet->getCellByColumnAndRow(2,$i)->getValue(); 
				$noijazah = $objWorksheet->getCellByColumnAndRow(3,$i)->getValue();        
                $noskpi= $objWorksheet->getCellByColumnAndRow(4,$i)->getValue();
				
				$mhs=$this->db->get_where("tbl_mahasiswa",array("npm"=>$npm));
            	foreach($mhs->result() as $up){
                     $id_mhs=$up->id_mahasiswa;
				}
				
            	// $resultData=array(
	            //     'id_mahasiswa'=>$id_mhs,
	            //     'nomor_ijazah'=>$noijazah,
	            //     'nomor_skpi'=>$noskpi,
	            //     'tahun_lulus'=>$tahun
				// );

				$get=array(
					'tgl_masuk'=> $tahun_masuk,
					'tgl_lulus'=> $tahun_keluar,
				);

				// $this->Import->update('tbl_mahasiswa', $get, array('npm'=> $npm));
				
				$skpi=array (
					'id_mahasiswa'=>$id_mhs,
	                'nomor_ijazah'=>$noijazah,
	                'nomor_skpi'=>$noskpi,
				);
				// $this->Import->insert('tbl_mahasiswa', $skpi);

            	$cek=$this->db->get_where("tbl_skpi",array("id_mahasiswa"=>$id_mhs));
            	if (count($cek->result())>0) {
                	
                }else{
					$this->Import->update('tbl_mahasiswa', $get, array('npm'=> $npm));
					$this->Import->insert('tbl_skpi', $skpi);
                	// $this->db->insert("tbl_skpi",$resultData);
                }
            	redirect("baak/skpi");
            }
       }
	}
}
