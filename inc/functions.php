<?php
/**
 * Allow the process of slow requisitions
 * A.K.A. Huge files
 *
 */
ini_set('max_execution_time', 0);

/**
 * Handle the download transaction
 * 
 */
function url_get_contents ($Url, $dir = '') {
    // Check for required lib
	if (!function_exists('curl_init')){ 
		die('CURL is not installed!');
	}

    // Set and Create (if doesnt exist) the download dir
	$dir = $dir ? '../downloads/' . $dir : '../downloads/';
	$ch = curl_init($Url);
	$file_name = basename($Url);
	$dirname = dirname($dir);
	if (!is_dir($dir))
	{
	    mkdir($dir, 0755, true);
	}
	$fp = fopen($dir .'/'. $file_name, 'wb');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);

	// Get the HTML or whatever is linked in $url
	$response = curl_exec($ch);

	// Check for 404 (file not found)
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if($httpCode == 404) {
        // Close conection
        curl_close($ch);
        fclose($fp); 

        // Delete file if 404
        @chmod( $dir .'/'. $file_name, 0777 );
        @unlink( $dir .'/'. $file_name );

	    // Send message
	    echo '<p class="bg-danger" style="padding:15px;">Arquivo não encontrado (Erro 404)</p>';
	} else {
        // Close conection
        curl_close($ch);
        fclose($fp); 

        // Send message
		echo '<p class="bg-success" style="padding:15px;"><b>'. $file_name .'</b> baixado com sucesso!</p>';
	}

	// return $output;
}

function pdf_conversion($dir = '') {
	$dir = $dir ? '../downloads/' . $dir : '../downloads/';
	ob_end_clean();
	require('fpdf-master/fpdf.php');
	$pdf = new FPDF();
	$folder_to_convert = glob($dir.'/*.jpg');
	natsort( $folder_to_convert );
	foreach ($folder_to_convert as $file):
		list($width, $height) = getimagesize($file);

		/*
		// Debug page and image info
		$pdf->SetFont( 'Arial', 'B', 14 );
		$pdf->Write( 5, ' GetPageWidth ' . $pdf->GetPageWidth() );
		$pdf->Write( 5, ' GetPageHeight ' . $pdf->GetPageHeight() );
		$pdf->Write( 5, ' File width: ' . $width );
		$pdf->Write( 5, ' File Height: ' . $height );
		*/

		// Check if the image is on landscape
		if( $width > $height ){
			$pdf->AddPage('L');
			$pdf->Image( $file, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight() );
		}else{
			$pdf->AddPage('P');
			$pdf->Image( $file, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight() );
		}
	endforeach;
	// $pdf->Output('D', $_POST['directory'] . '.pdf');
	$pdf->Output('F',  '../downloads/'. $_POST['directory'] . '/' . $_POST['directory'] . '.pdf');
    // $pdf->Output('F', '../downloads/'. $dir . '/'. $dir . '.pdf');

    echo '<p class="conversion-success bg-success" style="padding:15px;">Imagens baixadas e convertidas no arquivo <b>'. $_POST['directory'] .'</b>! Visualizar <a href="home_url/downloads/'. $_POST['directory']  . '/'. $_POST['directory'] . '.pdf" target="_blank">aqui</a>.</p>';
}

function convert_folder_to_pdf($dir){
	ob_end_clean();
	require('fpdf-master/fpdf.php');
	$pdf = new FPDF();
	$folder_to_convert = glob('../downloads/'. $dir .'/*.jpg');
	natsort( $folder_to_convert );
	foreach ( $folder_to_convert as $file):
		$pdf->AddPage();
		$pdf->Image( $file, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight() );
	endforeach;
	// $pdf->Output('D', $dir . '.pdf');
	$pdf->Output('F', '../downloads/'. $dir . '/'. $dir . '.pdf');

    echo '<p class="conversion-success bg-success" style="padding:15px;"><b>'. $dir .'</b> convertido com sucesso! Visualizar <a href="home_url/downloads/'. $dir  . '/'. $dir . '.pdf" target="_blank">aqui</a>.</p>';
}