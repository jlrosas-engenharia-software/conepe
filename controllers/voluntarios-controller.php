<?php
/**
 * Voluntarios Controller - Controller de Voluntarios
 *
 * @package ConepeApp
 * @since 0.1
 */
class VoluntariosController extends MainController
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
	 * Carrega a página "/views/Voluntarios-view"
	*/
    
    public function index() {
		// Page title
		$this->title = 'Voluntários';
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo para este view
        $modelo = $this->load_model('voluntarios-model');
				
		/** Carrega os arquivos do view **/

		// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';

		// /views/_includes/menu.php
        //require ABSPATH . '/views/_includes/menu.php';
		
		// /views/user-register/index.php
//        require ABSPATH . '/views/voluntarios-view.php';

        if (!$parametros || $parametros[0] == 'edit') { 
			// Page title
			$this->title = 'Voluntários';
        	require ABSPATH . '/views/voluntarios-view.php';
        }

        if ($parametros[0] == 'atv') { 
			// Page title
			$this->title = 'Atividades Realizadas pelo Voluntário';
        	require ABSPATH . '/views/voluntarios-atividades-view.php';
        }

        if ($parametros[0] == 'cer') { 
			// Page title
			$this->title = 'Certificados do Voluntário';
        	require ABSPATH . '/views/voluntarios-certificados-view.php';
        }

        if ($parametros[0] == 'cad') { 
			// Page title
			$this->title = 'Dados Pessoiais do Voluntário';
        	require ABSPATH . '/views/voluntarios-dadospessoais-view.php';
        }

        if ($parametros[0] == 'con') { 
			// Page title
			$this->title = 'Contatos do Voluntário';
        	require ABSPATH . '/views/voluntarios-contatos-view.php';
        }
		
        if ($parametros[0] == 'email') { 
			// Page title
			$this->title = 'Envio de email para o Voluntário';
        	require ABSPATH . '/views/voluntarios-email-view.php';
        }		

        if ($parametros[0] == 'fot') { 
			// Page title
			$this->title = 'Foto do Voluntário';
        	require ABSPATH . '/views/voluntarios-foto-view.php';
        }

        if ($parametros[0] == 'hab') { 
			// Page title
			$this->title = 'Habilidades do Voluntário';
        	require ABSPATH . '/views/voluntarios-habilidades-view.php';
        }		
		
        if ($parametros[0] == 'obs') { 
			// Page title
			$this->title = 'Observações Sobre o Voluntário';
        	require ABSPATH . '/views/voluntarios-observacoes-view.php';
        }		

        if ($parametros[0] == 'red') { 
			// Page title
			$this->title = 'Perfis do Voluntário nas Redes Sociais';
        	require ABSPATH . '/views/voluntarios-redessociais-view.php';
        }		

        if ($parametros[0] == 'rel') { 
			// Page title
			$this->title = 'Relátorio dos Voluntários';
        	require ABSPATH . '/views/voluntarios-rel-view.php';
        }		

		// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
    } // index
	
    public function eventos() {
		// Page title
		$this->title = 'Cadastro Eventos dos Voluntarios';
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo para este view
        $modelo = $this->load_model('voluntarios-model');
				
		/** Carrega os arquivos do view **/
		
		// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';
		
		// /views/_includes/menu.php
        require ABSPATH . '/views/_includes/menu.php';
		
		// /views/user-register/index.php
        require ABSPATH . '/views/voluntarios-eventos-view.php';
		
		// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
    } // eventos

    public function movimento() {
		// Page title
		$this->title = 'Cadastro Movimento dos Voluntarios';
		
		// Parametros da função
		$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
		// Carrega o modelo para este view
        $modelo = $this->load_model('voluntarios-model');
				
		/** Carrega os arquivos do view **/
		
		// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';
		
		// /views/_includes/menu.php
        require ABSPATH . '/views/_includes/menu.php';
		
		// /views/user-register/index.php
        require ABSPATH . '/views/voluntarios-movimentos-view.php';
		
		// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
    } // movimentos

} // class home