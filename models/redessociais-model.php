<?php

class RedesSociaisModel extends MainModel
{
	/**
	 * Construtor para essa classe
	 *
	 * Configura o DB, o controlador, os parâmetros e dados do usuário.
	 *
	 * @since 0.1
	 * @access public
	 * @param object $db Objeto da nossa conexão PDO
	 * @param object $controller Objeto do controlador
	 */


	public function __construct( $db = false, $controller = null ) {
		// Configura o DB (PDO)
		$this->db = $db;
		
		// Configura o controlador
		$this->controller = $controller;

		// Configura os parâmetros
		$this->parametros = $this->controller->parametros;

		// Configura os dados do usuário
		$this->userdata = $this->controller->userdata;
		
	}

	public function __get($campo) {
		return (strtolower($this->$campo));
    }

    public function __set($campo, $valor) {
                $this->$campo = $value;
    }

	/**
	 * Obtém a lista de redessocials
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_redesocial_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT * FROM redes_sociais 
								   ORDER BY nome');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_redessocial_list

/**
	 * Insere redessocial
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_redesocial() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_redesocial.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_redesocial'] ) ) {
			return;
		}		

/*

		foreach ( $_POST as $key => $value ) {
				echo $key . " - " . $value . " cada um POST </br>";			
			}
/*

		/*
		Para evitar conflitos apenas inserimos valores se o parâmetro edit
		não estiver configurado.
		*/
		if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
			return;
		}
		
		// Só pra garantir que não estamos atualizando nada
		if ( is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
			
		// Remove o campo insere_notica para não gerar problema com o PDO
		unset ($_POST['insere_redesocial']);
		unset ($_POST['id_rede']);			
		unset ($_POST['isalvou']);
		
		// MAIUSCULAS
		$_POST['nome'] = strtoupper($_POST['nome']);

		// Insere os dados na base de dados
		$query = $this->db->insert( 'redes_sociais', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Redes Social Inserida Com Sucesso !';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados !';

	} // insere_redessocial	

	/**
	 * Obtém a redessocial e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma redessocial da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_redesocial () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da redessocial
		$redesocial_id = chk_array( $this->parametros, 1 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_redessocial.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_redesocial'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['isalvou']);
			unset($_POST['insere_redesocial']);

			// MAIUSCULAS
			$_POST['nome'] = strtoupper($_POST['nome']);
			
			// Atualiza os dados
			$query = $this->db->update('redes_sociais', 'id_rede', $redesocial_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Redes Social Atualizada com Sucesso!';
			}			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query('SELECT * FROM redes_sociais
			                       WHERE id_rede = ? LIMIT 1', array($redesocial_id)
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_redessocial

}