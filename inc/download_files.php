<?php
/**
 * Include main functions
 *
 */
 require_once('functions.php');

/**
 * Process the download of files (images)
 *
 */
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

	if( !empty( $_POST['url'] ) && $_POST['download_in_sequence'] === 'false' ){
		echo '<hr><h3><hr><h3><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Baixado o seguinte arquivo:</h3>';
		url_get_contents ( $_POST['url'], $_POST['directory'] );	
	}

	if( $_POST['download_in_sequence'] === 'true' && !empty( $_POST['url_base'] ) ) {
		echo '<hr><h3><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Diretório em sequência, baixado:</h3>';

		for ($i=$_POST['start']; $i <= $_POST['end']; $i++) { 
			url_get_contents ( $_POST['url_base'] . $i . '.jpg', $_POST['directory'] );	
		}

		pdf_conversion( $_POST['directory'] );
	}

	die();
}

?>