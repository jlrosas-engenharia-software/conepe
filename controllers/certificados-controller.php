<?php
/**
 * Certificados Controller - Controller de Certificados
 *
 * @package ConepeApp
 * @since 0.1
 */
class CertificadosController extends MainController
{

/**
	 * $login_required
	 *
	 * Se a página precisa de login
	 *
	 * @access public
	 */
	public $login_required = false;

	/**
	 * $permission_required
	 *
	 * Permissão necessária
	 *
	 * @access public
	 */
	public $permission_required;

	/**
	 * Carrega a página "/views/Certificados-view"
	*/
    
    public function index() {
		// Page title
		$this->title = 'Geracao de Certificados';
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo para este view
        $modelo = $this->load_model('certificados-model');
				
		/** Carrega os arquivos do view **/
		
		// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';
		
		// /views/_includes/menu.php
        //require ABSPATH . '/views/_includes/menu.php';
		
		// /views/user-register/index.php
        require ABSPATH . '/views/certificados-view.php';
		
		// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
    } // index
	
    public function view() {
		// Page title
		$this->title = 'Visualização de Certificado';
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo para este view
        $modelo = $this->load_model('certificados-model');
				
		/** Carrega os arquivos do view **/
		
		// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header2.php';
		
		// /views/_includes/menu.php
//        require ABSPATH . '/views/_includes/menu.php';
		
		// /views/user-register/index.php
        require ABSPATH . '/views/certificados-view2.php';
		
		// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
    } // index
} // class home