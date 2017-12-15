<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Download Script</title>

	<!-- Bootstrap -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<style>
		.glyphicon-refresh-animate {
		    -animation: spin .7s infinite linear;
		    -webkit-animation: spin2 .7s infinite linear;
		}

		@-webkit-keyframes spin2 {
		    from { -webkit-transform: rotate(0deg);}
		    to { -webkit-transform: rotate(360deg);}
		}

		@keyframes spin {
		    from { transform: scale(1) rotate(0deg);}
		    to { transform: scale(1) rotate(360deg);}
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div id="main" class="col-md-8 col-md-push-2">
				<h1>Escolha a URL para fazer os downloads</h1>		
				<form id="downloader-form" method="POST" action="">
					<div class="form-group">
						<label for="url">URL do arquivo</label>
						<input name="url" type="text" class="form-control" id="url" placeholder="URL" value="<?php if( !empty( $_POST['url'] ) ) echo $_POST['url']; ?>">
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" id="download_in_sequence" name="download_in_sequence" checked> Baixar uma sequência de arquivos
						</label>
					</div>
					<div class="form-group">
						<label for="url_base">URL Base (Diretório dos arquivos)</label>
						<input type="text" class="form-control" id="url_base" name="url_base" placeholder="URL Base" value="<?php if( !empty( $_POST['url_base'] ) ) echo $_POST['url_base']; ?>">
					</div>
					<div class="row">
						<div class="form-group col-md-6">
							<label for="start">Início</label>
							<input type="number" class="form-control" id="start" name="start" placeholder="URL Base" value="<?php if( !empty( $_POST['start'] ) ) echo $_POST['start']; ?>">
						</div>
						<div class="form-group col-md-6">
							<label for="end">Fim</label>
							<input type="number" class="form-control" id="end" name="end" placeholder="URL Base" value="<?php if( !empty( $_POST['end'] ) ) echo $_POST['end']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="directory">Diretório para salvar</label>
						<input id="directory" name="directory" type="text" class="form-control" id="directory" placeholder="Downloads" value="<?php if( !empty( $_POST['directory'] ) ) echo $_POST['directory']; ?>">
					</div>
					<button type="submit" class="btn btn-default">Baixar</button>
				</form>	

				<h2>Ou escolha um diretório para converter em PDF</h2>
				<form id="convert-folder-form" method="POST" action="">
					
					<div class="form-group">
						<label for="directory_to_convert">Diretório para salvar</label>
						<select name="directory_to_convert" class="form-control" id="directory_to_convert">
							<option value="">Selecione</option>
							<?php 	
							$folders = scandir('./downloads');
							foreach ( $folders as $folder):
								if (!in_array($folder,array(".",".."))) 
								echo '<option value="'. $folder .'">'. $folder .'</option>';
							endforeach; 
							?>
						</select>
					</div>
					<button type="submit" class="btn btn-default">Converter</button>
				</form>
			</div>

			<div id="response" class="col-md-8 col-md-push-2" style="margin-top: 30px;">
			</div>
		</div>
	</div>


	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="assets/js/bootstrap.min.js"></script>
	<script>
		(function($){
			$('#downloader-form').on('submit', function(e){
				e.preventDefault();

				var url =  $('#url').val() !== '' ? $('#url').val() : '',
					download_in_sequence =  $('#download_in_sequence').prop('checked'),
					url_base =  $('#url_base').val() !== '' ? $('#url_base').val() : '',
					start =  $('#start').val() !== '' ? $('#start').val() : '',
					end =  $('#end').val() !== '' ? $('#end').val() : '',
					directory =  $('#directory').val() !== '' ? $('#directory').val() : '';
				
				// console.log('Ajax Call', url, download_in_sequence, url_base, start, end, directory);

				$.ajax({
					method: 'POST',
					url: 'inc/download_files.php',
					data: {
						url: url,
						download_in_sequence: download_in_sequence,
						url_base: url_base,
						start: start,
						end: end,
						directory: directory
					},
					beforeSend: function(){
						$('#downloader-form button').text('Baixando').attr('disabled', true);
						$('#response').html('<hr><h3><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Baixando as imagens, por favor aguarde.</h3>');
					},
					success: function(result){
						// console.log('Resultado: ', result);
						// $('#response').html('').append(result . '<br><a href="#">Iniciar conversão para PDF</>');
						$('#response').html('').append(result);
						var correctUrl = $('#response .conversion-success a').attr('href');
						var newUrl = correctUrl.replace( 'home_url/', window.location.href );
						$('#response .conversion-success a').attr('href', newUrl);
						$('#downloader-form button').text('Baixar').removeAttr('disabled');
					}
				})
			})

			$('#convert-folder-form').on('submit', function(e){
				e.preventDefault();
				var directory_to_convert =  $('#directory_to_convert').val() !== '' ? $('#directory_to_convert').val() : '';

				// console.log(directory_to_convert);

				if( directory_to_convert ){
					convert_folder(directory_to_convert);
				}
				
			})

			function convert_folder(directory_to_convert){
				$.ajax({
					method: 'POST',
					url: 'inc/convert_folder.php',
					data: {
						directory_to_convert: directory_to_convert
					},
					beforeSend: function(){
						$('#response').html('<hr><h3>Processando, por favor aguarde.</h3>');
					},
					success: function(result){
						// console.log('Resultado: ', result);
						$('#response').html('').append(result);
						var correctUrl = $('#response .conversion-success a').attr('href');
						var newUrl = correctUrl.replace( 'home_url/', window.location.href );
						$('#response .conversion-success a').attr('href', newUrl);

					}
				})
			}
		})(jQuery);
	</script>
</body>
</html>