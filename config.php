<?php
/**
 * Configuração geral
 */

// Caminho para a raiz
define( 'ABSPATH', dirname( __FILE__ ) );

// URL da home no servidor web a home_uri = ''
define( 'HOME_URI', 'http://localhost' );
define( 'IMGS_URI', 'http://localhost/views/_images/' );

//define( 'HOME_URI', '' );
//define( 'IMGS_URI', 'views/_images/' );

define( 'MAPS_KEY', 'AIzaSyClM9uysiX0bCNJbkQqTHoTPpQNTxMX5bY');

// Constantes do Banco de dados
define( 'HOSTNAME', 'localhost' );
define( 'DB_NAME', 'bznlqzus_conepedb' );
define( 'DB_USER', 'bznlqzus_root' );
define( 'DB_PASSWORD', 'conepe2017' );
define( 'DB_CHARSET', 'utf8' );

define( 'DEBUG', true );

// Carrega o loader, que vai carregar a aplicação inteira
require_once ABSPATH . '/loader.php';
?>