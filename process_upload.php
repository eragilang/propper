<?php

require_once 'Excel/reader.php';
//error_reporting(0);

$link = mysql_connect('localhost', 'root', '');
			// make foo the current db
			$db_selected = mysql_select_db('test', $link);

///// Process Upload Bobot
		$ref = $_POST['ref'];
		$unit_type = $_POST['unit_type'];
		$ref = $ref+1;
		for($i=0;$i<$ref;$i++)
		{
			$isi = "jobfunction$i";
			$proses = "proses$i";
			$perilaku = "perilaku$i";
			$produk = "produk$i";
			
			//echo "<br>$isi - $proses - $perilaku - $produk";
			
			$isi_job = $_POST[$isi];
			$proses_job = $_POST[$proses];
			$perilaku_job = $_POST[$perilaku];
			$produk_job = $_POST[$produk];
			
			//echo "<br>update $isi_job == $proses_job === $perilaku_job === $produk_job";
			$update_bobot = "update job_bobot_new set proses = $proses_job, perilaku = $perilaku_job, produk = $produk_job where unit_type = '$unit_type' and jobfunction = '$isi_job'";
			echo $update_bobot;
			mysql_query($update_bobot);
			
				
		}

// ExcelFile($filename, $encoding);
//$data = new Spreadsheet_Excel_Reader();
$data = new Spreadsheet_Excel_Reader($_FILES['file_source']['tmp_name']);

// Set output Encoding.
$data->setOutputEncoding('CP1251');
//$data->read('jxlrwtest.xls');
$baris = $data->rowcount($sheet_index=0);

for ($i=1; $i<=$baris; $i++)
{
  // membaca data excel dari (kolom ke-1)
	$questions = $data->val($i, 1);
	$sola = $data->val($i, 2);
	$solb = $data->val($i, 3);
	$solc = $data->val($i, 4);
	$sold = $data->val($i, 5);
	$sole = $data->val($i, 6);
	$solf = $data->val($i, 7);
	$solg = $data->val($i, 8);
	$solh = $data->val($i, 9);
	$solution = $data->val($i, 10);
	$level = $data->val($i, 11);
	$difficulty = $data->val($i, 12);
	$source = $data->val($i, 13);
	$mandatory = $data->val($i, 14);
	$type = $data->val($i, 15);	

  // setelah data dibaca, sisipkan ke dalam tabel mhs
  $query = "INSERT INTO question_temp values ('$questions', '$sola', '$solb','$solc', '$sold', '$sole','$solf', '$solg', '$solh','$solution', '$level', '$difficulty','$source', '$mandatory', '$type')";
  $hasil = mysql_query($query);

  // jika proses insert data sukses, maka counter $sukses bertambah
  // jika gagal, maka counter $gagal yang bertambah
  if ($hasil) $sukses++;
  else $gagal++;
  
  
}

echo "berhasil memasukan $sukses data";

?>