<?php
echo '<html><body><h1>XLSX to HTML</h1>';

if (isset($_FILES['file'])) {
	
	require_once __DIR__ . '/simplexlsx.class.php';
	
	if ( $xlsx = SimpleXLSX::parse( $_FILES['file']['tmp_name'] ) ) {

		echo '<h2>Parsing Result</h2>';
	
		list( $cols, ) = $xlsx->dimension();

		foreach ( $xlsx->rows() as $k => $r ) {
			//		if ($k == 0) continue; // skip first row
		for ( $i = 0; $i < $cols; $i ++ ) {
				//echo '<td>' . ( ( isset( $r[ $i ] ) ) ? $r[ $i ] : '&nbsp;' ) . '</td>';
				$kolom = $r[ $i ];
				if($i==0)
				{
					$isi = "('$kolom'";
				}else if(($i+1)==$cols){
					$isi .= "'$kolom')";
				}else{
					$isi .= "'$kolom',";
				}
			}
			echo "insert into question_temp values$isi<br />";
		}
		
	} else {
		echo SimpleXLSX::parse_error();
	}
}
echo '<h2>Upload form</h2>
<form method="post" enctype="multipart/form-data">
*.XLSX <input type="file" name="file"  />&nbsp;&nbsp;<input type="submit" value="Parse" />
</form>';
echo '</body></html>';
