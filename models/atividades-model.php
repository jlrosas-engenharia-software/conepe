<?php

class AtividadesModel extends MainModel
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

    private $id,
            $descricao,
            $minimo,
            $maximo,
            $flaga;

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
	 * Obtém a lista de atividades
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_atividade_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT * FROM `atividades` ORDER BY descricao');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_atividade_list

	/**
	 * Obtém a lista de atividades
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_atividade_rel() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT atividades.*,
									(SELECT SUM(horas)
									 FROM voluntarios_atividades AS va
									 WHERE va.id_atividade = atividades.id_atividade) as thoras
								 FROM atividades ORDER BY descricao');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_atividade_list

/**
	 * Insere Atividade
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_atividade() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_atividade.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_atividade'] ) ) {
			return;
		}		

		unset ($_POST['id_atividade']);

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
		unset($_POST['insere_atividade']);
		
		// MAIUSCULAS
		$_POST['descricao'] = strtoupper($_POST['descricao']);

		// Insere os dados na base de dados
		$query = $this->db->insert( 'atividades', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Atividade Inserida Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados !!!';

	} // insere_atividade	

	/**
	 * Obtém a atividade e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma atividade da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_atividade () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da atividade
		$atividade_id = chk_array( $this->parametros, 1 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_atividade.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_atividade'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_atividade']);

			// MAIUSCULAS
			$_POST['descricao'] = strtoupper($_POST['descricao']);
			
			// Atualiza os dados
			$query = $this->db->update('atividades', 'id_atividade', $atividade_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Atividade Atualizada Com Sucesso!';
			}
			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query(
			'SELECT * FROM atividades WHERE id_atividade = ? LIMIT 1',
			array( $atividade_id )
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_atividade

}