<?php
/**
 * Include main functions
 *
 */
 require_once('functions.php');

/**
 * Process the conversion
 *
 */
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

	if( !empty( $_POST['directory_to_convert'] ) ){
        echo '<hr><h3>Diretório convertido (' . $_POST['directory_to_convert'] . ')</h3>';
        convert_folder_to_pdf($_POST['directory_to_convert']);
    }

	die();
}

?>