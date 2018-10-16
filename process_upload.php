<?php
require_once __DIR__ . '/simplexlsx.class.php';
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
			//echo $update_bobot;
			mysql_query($update_bobot);
			
				
		}

if ( $xlsx = SimpleXLSX::parse( $_FILES['file_source']['tmp_name'] ) ) {


		list( $cols, ) = $xlsx->dimension();
		$date = date("Y-m-d H:i:s");
		foreach ( $xlsx->rows() as $k => $r ) {
			if ($k == 0) continue; // skip first row
			for ( $i = 0; $i < $cols; $i ++ ) {
					//echo '<td>' . ( ( isset( $r[ $i ] ) ) ? $r[ $i ] : '&nbsp;' ) . '</td>';
					$kolom = $r[ $i ];
					if($i==0)
					{
						$isi = "('$kolom',";
					}else if(($i+1)==$cols){
						$isi .= "'$kolom',NOW())";
					}else{
						$isi .= "'$kolom',";
					}
				}
				$query = "insert into question_temp values$isi";
				$hasil = mysql_query($query)or die($query);;
			}		
	} else {
		echo SimpleXLSX::parse_error();
	}
  
echo "berhasil memasukan $sukses data";

?>