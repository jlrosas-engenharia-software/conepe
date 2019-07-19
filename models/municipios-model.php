<?php

class MunicipiosModel extends MainModel
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

    private $id_municipio,
            $codigo_uf,
            $uf,
            $municipio;

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
	 * Obtém a lista de municipios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_municipio_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT * FROM `municipios` ORDER BY id_municipio DESC');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_municipio_list

/**
	 * Insere municipio
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_municipio() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_municipio.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_municipio'] ) ) {
			return;
		}		

		unset ($_POST['id_municipio']);

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
		unset($_POST['insere_municipio']);
		
		// MAIUSCULAS
		$_POST['municipio'] = strtoupper($_POST['municipio']);

		// Insere os dados na base de dados
		$query = $this->db->insert( 'municipios', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
//			$this->form_msg = '<p class="success">municipio atualizada com sucesso!</p>';
			return;			
		} 
		
		// :(
		$this->form_msg = '<p class="error">Erro ao enviar dados!</p>';

	} // insere_municipio	

	/**
	 * Obtém a municipio e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma municipio da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_municipio () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da municipio
		$municipio_id = chk_array( $this->parametros, 1 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_municipio.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_municipio'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_municipio']);

			// MAIUSCULAS
			$_POST['municipio'] = strtoupper($_POST['municipio']);
			
			// Atualiza os dados
			$query = $this->db->update('municipios', 'id_municipio', $municipio_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = '<p class="success">municipio atualizada com sucesso!</p>';
			}
			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query(
			'SELECT * FROM municipios WHERE id_municipio = ? LIMIT 1',
			array( $municipio_id )
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_municipio

}