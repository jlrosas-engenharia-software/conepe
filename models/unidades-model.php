<?php

class UnidadesModel extends MainModel
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
	 * Obtém a lista de unidades
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_unidade_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT unidades.*,voluntarios.nome AS responsavel,municipios.municipio
		                           FROM unidades 
		                           JOIN voluntarios ON voluntarios.id_voluntario=unidades.id_responsavel
		                           JOIN municipios ON municipios.id_municipio=unidades.id_municipio
		                           ORDER BY descricao');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_unidade_list

/**
	 * Insere unidade
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_unidade() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_unidade.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_unidade'] ) ) {
			return;
		}

		if( $_POST['id_responsavel'] == 0) {
			$this->form_msg = 'Responsável Inválido !';
			return;		
		}

		unset ($_POST['id_unidade']);
		unset ($_POST['responsavel']);
		unset ($_POST['municipio']);
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
		unset($_POST['insere_unidade']);
		
		// MAIUSCULAS
		$_POST['descricao'] = strtoupper($_POST['descricao']);
		$_POST['contato'] = strtoupper($_POST['contato']);
		$_POST['localizacao'] = strtoupper($_POST['localizacao']);

		// Insere os dados na base de dados
		$query = $this->db->insert( 'unidades', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Unidade Inserida Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_unidade	

	/**
	 * Obtém a unidade e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma unidade da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_unidade () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}


		// Configura o ID da unidade
		$unidade_id = chk_array( $this->parametros, 1 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_unidade.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_unidade'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_unidade']);
			unset($_POST['responsavel']);
			unset($_POST['municipio']);

			if( $_POST['id_responsavel'] == 0) {
				$this->form_msg = 'Responsável Inválido !';
				return;		
			}

			// MAIUSCULAS
			$_POST['descricao'] = strtoupper($_POST['descricao']);
			$_POST['contato'] = strtoupper($_POST['contato']);
			$_POST['localizacao'] = strtoupper($_POST['localizacao']);
			
			// Atualiza os dados
			$query = $this->db->update('unidades', 'id_unidade', $unidade_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Unidade Atualizada Com Sucesso!';
			}
			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query(
			'SELECT unidades.*,voluntarios.nome AS responsavel,municipios.municipio
		        FROM unidades 
                JOIN voluntarios ON voluntarios.id_voluntario=unidades.id_responsavel
                JOIN municipios ON municipios.id_municipio=unidades.id_municipio
                WHERE id_unidade = ? LIMIT 1',
			array( $unidade_id )
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_unidade

}