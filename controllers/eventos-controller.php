<?php
/**
 * Eventos Controller - Controller de Eventos
 *
 * @package ConepeApp
 * @since 0.1
 */
class EventosController extends MainController
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
	 * Carrega a página "/views/Eventos-view"
	*/
    
    public function index() {
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo para este view
        $modelo = $this->load_model('eventos-model');
				
		/** Carrega os arquivos do view **/
		
		// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';
		
		// /views/_includes/menu.php
        //require ABSPATH . '/views/_includes/menu.php';
		
		// /views/user-register/index.php

        if (!$parametros || $parametros[0] == 'edit') { 
			// Page title
			$this->title = 'Cadastro de Eventos';
        	require ABSPATH . '/views/eventos-view.php';
        }

        if ($parametros[0] == 'add') { 
			// Page title
			$this->title = 'Atribui Voluntários aoEvento';
        	require ABSPATH . '/views/eventos-add-view.php';
        }
		
        if ($parametros[0] == 'can') { 
			// Page title
			$this->title = 'Cancela Evento';
        	require ABSPATH . '/views/eventos-cancela-view.php';
        }

        if ($parametros[0] == 'end') { 
			// Page title
			$this->title = 'Conclui o Evento';
        	require ABSPATH . '/views/eventos-end-view.php';
        }

        if ($parametros[0] == 'trf') { 
			// Page title
			$this->title = 'Transfere Evento';
        	require ABSPATH . '/views/eventos-transfere-view.php';
        }

        if ($parametros[0] == 'ver') { 
			// Page title
			$this->title = 'Visualiza o Evento';
        	require ABSPATH . '/views/eventos-rel-view.php';
        }

		// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
    } // index
	
} // class home