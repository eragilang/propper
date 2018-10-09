<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bootstrap 4, from LayoutIt!</title>

    <meta name="description" content="Source code generated using layoutit.com">
    <meta name="author" content="LayoutIt!">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

  </head>
  <body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
				 
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="navbar-toggler-icon"></span>
				</button> 
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="navbar-nav">
						<li class="nav-item active">
							 <a class="nav-link" href="#">Link <span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
							 <a class="nav-link" href="#">Link</a>
						</li>
						<li class="nav-item dropdown">
							 <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown">Dropdown link</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
								 <a class="dropdown-item" href="#">Action</a> <a class="dropdown-item" href="#">Another action</a> <a class="dropdown-item" href="#">Something else here</a>
								<div class="dropdown-divider">
								</div> <a class="dropdown-item" href="#">Separated link</a>
							</div>
						</li>
					</ul>
					<form class="form-inline">
						<input class="form-control mr-sm-2" type="text" /> 
						<button class="btn btn-primary my-2 my-sm-0" type="submit">
							Search
						</button>
					</form>
					<ul class="navbar-nav ml-md-auto">
						<li class="nav-item active">
							 <a class="nav-link" href="#">Link <span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item dropdown">
							 <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown">Dropdown link</a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
								 <a class="dropdown-item" href="#">Action</a> <a class="dropdown-item" href="#">Another action</a> <a class="dropdown-item" href="#">Something else here</a>
								<div class="dropdown-divider">
								</div> <a class="dropdown-item" href="#">Separated link</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>
			
			<?php

			$link = mysql_connect('localhost', 'root', '');
			// make foo the current db
			$db_selected = mysql_select_db('test', $link);
			
			?>
			<div class="row">
				<div class="col-md-12">
				<form id="formSubmit" name="form" action="process_upload.php" method="post" enctype="multipart/form-data">
				<table width="581" border="0" align="center" cellpadding="7" cellspacing="0">
					<tr>
						<td width="102" colspan="2">Upload Soal <b><?php  $isi = $_GET['unit_type']; echo $isi; ?></b><span class="style2"> *</span> </td>
						
					</tr>
					<tr>
						<td width="102">Unit Type <span class="style2">*</span> </td>
						<td width="451">
						<select name="unittype" id="unittype">
							<option selected>--
							<option value="GRAPARI">Walk-In</option>
							<option value="CALLCENTER">Callcenter</option>
							<option value="WEBSERVICE">eCare</option>
						</select>
						</td>
					</tr>
					  <tr>
    <td>File Upload <span class="style2">*</span> </td>
    <td><input name="file_source" type="file" class="edt" id="file_source" value="<?php $file_source?>" size="45"></td>
    </tr>
  <tr>
    <td></td>
    <td></td>
  </tr>
 </table>
				<table width="581" border="2" align="center" cellpadding="7" cellspacing="0"> 
<tr>
		<td>Job Function</td>
		<td>Proses</td>
		<td>Perilaku</td>
		<td>Produk</td>
		<td>Total</td>
</tr><input type="hidden" name="unit_type" value="<?php echo $isi; ?>" /><?php
$slc_qs_active 	= "select * from job_bobot_new where unit_type = '$isi' order by jobfunction";
	$result = mysql_query($slc_qs_active);
	$ref = 0;
	while ($row = mysql_fetch_array($result))
	{
		
		?>
		<tr><input type="hidden" name="jobfunction<?php echo $ref; ?>" value="<?php echo $row[1]; ?>" />
		<td><?php echo $row[1]; ?><input type="hidden" name="ref" value="<?php echo $ref; ?>" /></td>
		<td><input type="text" value="<?php echo $row[2]; ?>" name="proses<?php echo $ref; ?>" id="proses" /></td>
		<td><input type="text" value="<?php echo $row[3]; ?>" name="perilaku<?php echo $ref; ?>" id="perilaku" /></td>		
		<td><input type="text" value="<?php echo $row[4]; ?>" name="produk<?php echo $ref; ?>" id="produk" /></td>	
		<td width="30"><?php $total = $row[2]+$row[3]+$row[4]; echo $total; ?></td>
	</tr>
<?php
		$ref++;
	}
	?>
  	<tr> 
<tr>
		<td colspan="5">
				<input name="submit" type="submit" value="Upload" align="center" /></td>
		
</tr>
				</table>
				</form>
				
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>

<script>
	$( document ).ready(function() {
			$('#unittype').change(function() {
				
    window.location = "upload_soal.php?unit_type=" + $(this).val();
});
});
		
</script>