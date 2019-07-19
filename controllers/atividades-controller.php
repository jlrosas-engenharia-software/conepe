<?php
/**
 * Atividades Controller - Controller de Atividades
 *
 * @package ConepeApp
 * @since 0.1
 */
class AtividadesController extends MainController
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
	 * Carrega a página "/views/atividades-view"
	*/
    
    public function index() {
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo para este view
        $modelo = $this->load_model('atividades-model');
				
		/** Carrega os arquivos do view **/
		
		// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';
		
		// /views/_includes/menu.php
        //require ABSPATH . '/views/_includes/menu.php';
        if (!$parametros || $parametros[0] == 'edit') { 
			// Page title
			$this->title = 'Cadastro de Atividades';

			// /views/user-register/index.php
	        require ABSPATH . '/views/atividades-view.php';
			
        }

        if ($parametros[0] == 'rel') { 
			// Page title
			$this->title = 'Relatório das Estantes';

			// /views/user-register/index.php
    	    require ABSPATH . '/views/atividades-rel-view.php';
        }

		
		// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
    } // index
	
} // class home