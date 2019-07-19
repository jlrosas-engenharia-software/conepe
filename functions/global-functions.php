<?php
/**
 * Verifica chaves de arrays
 *
 * Verifica se a chave existe no array e se ela tem algum valor.
 * Obs.: Essa função está no escopo global, pois, vamos precisar muito da mesma.
 *
 * @param array  $array O array
 * @param string $key   A chave do array
 * @return string|null  O valor da chave do array ou nulo
 */
function chk_array ( $array, $key ) {
	// Verifica se a chave existe no array
	if ( isset( $array[ $key ] ) && ! empty( $array[ $key ] ) ) {
		// Retorna o valor da chave
		return $array[ $key ];
	}
	
	// Retorna nulo por padrão
	return null;
} // chk_array

/**
 * Busca Coordenadas
 *
 *
 * @param array  $array O array
 * @param string $key   A chave do array
 * @return string|null  O valor da chave do array ou nulo
 */
function busca_coordenadas ( $endereco ) {
   $tipo_output = "xml";
   $google_api = MAPS_KEY;
   $endereco_desejado = urlencode(utf8_encode($endereco));
   echo $endereco_desejado;
   $endereco_final = "https://maps.google.com/maps/geo?q=". $endereco_desejado ."&output=". $tipo_output ."&key=$google_api";
   $page = file_get_contents($endereco_final);   
   $xml = new SimpleXMLElement($page);
   return $xml->Response->Placemark->Point->coordinates;
}

/**
 * Função para carregar automaticamente todas as classes padrão
 * Ver: http://php.net/manual/pt_BR/function.autoload.php.
 * Nossas classes estão na pasta classes/.
 * O nome do arquivo deverá ser class-NomeDaClasse.php.
 * Por exemplo: para a classe ConepeApp, o arquivo vai chamar class-ConepeApp.php
 
function __autoload($class_name) {
	$file = ABSPATH . '/classes/class-' . $class_name . '.php';
	
	if ( ! file_exists( $file ) ) {
		require_once ABSPATH . '/includes/404.php';
		return;
	}
	
	// Inclui o arquivo da classe
    require_once $file;
} // __autoload
*/

function my_autoload ($ClassName) {
	$file = ABSPATH . '/classes/class-' . $ClassName . '.php';
	
	if ( ! file_exists( $file ) ) {
		require_once ABSPATH . '/includes/404.php';
		return;
	}

    // Inclui o arquivo da classe
    require_once $file;
}

spl_autoload_register("my_autoload");
?>


