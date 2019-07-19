<?php

class EstantesModel extends MainModel
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
	 * Obtém a lista de Estantes
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_estante_rel() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT estantes.*,voluntarios.nome as responsavel,unidades.descricao as unidade,
									(SELECT sum(arrecadados) 
										FROM estantes_movimento
									 	WHERE estantes_movimento.id_estante=estantes.id_estante) as arrecadados,
									(SELECT sum(devolvidos) 
										FROM estantes_movimento
									 	WHERE estantes_movimento.id_estante=estantes.id_estante) as devolvidos
									FROM estantes 
								   JOIN voluntarios on voluntarios.id_voluntario=estantes.id_responsavel
		 						   JOIN unidades on unidades.id_unidade=estantes.id_unidade
		 						   ORDER BY estantes.descricao');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_estante_list

	/**
	 * Obtém a lista de Estantes
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_estante_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT estantes.*,voluntarios.nome as responsavel,unidades.descricao as unidade FROM estantes 
								   JOIN voluntarios on voluntarios.id_voluntario=estantes.id_responsavel
		 						   JOIN unidades on unidades.id_unidade=estantes.id_unidade
		 						   ORDER BY estantes.descricao');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_estante_list


	/**
	 * Obtém a lista de Movimentos nas Estantes
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_estante_movimento_list() {

		// Configura o ID 
		$estante_id = chk_array( $this->parametros, 1 );
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT estantes_movimento.*,estantes.descricao FROM estantes_movimento
								   JOIN estantes ON estantes.id_estante=estantes_movimento.id_estante
								   WHERE estantes_movimento.id_estante = ?
		 						   ORDER BY data DESC',array($estante_id));
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_estante_movimento_list

/**
	 * Insere estante
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_estante() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_estante.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_estante'] ) ) {
			return;
		}

		if( $_POST['id_responsavel'] == 0) {
			$this->form_msg = 'Responsável Inválido !';
			return;		
		}

		unset ($_POST['id_estante']);
		unset ($_POST['coordenadas']);
		unset ($_POST['responsavel']);
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
		unset($_POST['insere_estante']);
		
		// MAIUSCULAS
		$_POST['descricao'] = strtoupper($_POST['descricao']);


		// Insere os dados na base de dados
		$query = $this->db->insert( 'estantes', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Estante Inserida Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados !';

	} // insere_estante	

/**
	 * Insere Movimento na estante
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_movimento_estante() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_estante.
		*/
//		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_movimento_estante'] ) ) {
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			return;
		}		

		unset($_POST['id_estante_movimento']);
		unset($_POST['descricao']);

		//foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada um POST </br>";}

		/*
		Para evitar conflitos apenas inserimos valores se o parâmetro edit
		não estiver configurado.
		*/
		if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
			return;
		}
		
		// Só pra garantir que não estamos atualizando nada
		if ( is_numeric( chk_array( $this->parametros, 2 ) ) ) {
			return;
		}
			
		// Remove o campo insere_movimento_estante para não gerar problema com o PDO
		unset($_POST['insere_movimento_estante']);
		
		// MAIUSCULAS
		$_POST['observacoes'] = trim(strtoupper($_POST['observacoes']));


		// Insere os dados na base de dados
		$query = $this->db->insert( 'estantes_movimento', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Movimento na Estante Inserido com Sucesso!';
			$this->ultimaacao = 'insere';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao Enviar Dados!';

	} // insere_movimento_estante	

	/**
	 * Obtém o movimento da estante e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma estante da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_movimento_estante () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'movi' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID 
		$estante_id = chk_array( $this->parametros, 1 );
		$movimento_id = chk_array( $this->parametros, 2 );
		$acao = chk_array( $this->parametros,3);		

		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_estante.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_movimento_estante'] ) ) {
		
			unset($_POST['insere_movimento_estante']);
			unset($_POST['descricao']);

			// MAIUSCULAS
			$_POST['observacoes'] = trim(strtoupper($_POST['observacoes']));
			
			// Atualiza os dados
			$query = $this->db->update('estantes_movimento', 'id_estante_movimento', $movimento_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Movimento na Estante Atualizado com Sucesso!';
				$this->ultimaacao = 'obtem';
			}			
		}

		if ($acao == 'del') {

			$query = $this->db->delete('estantes_movimento', 'id_estante_movimento', $movimento_id, $_POST);

			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Movimento na Estante Apagado com Sucesso !';
				$this->ultimaacao = 'deleta';
				$movimento_id=0;
			}
		}
		
		// Faz a consulta para obter o valor
		if ( $movimento_id == 0 ) {
			$query = $this->db->query('SELECT *
								FROM estantes 
								WHERE id_estante = ? LIMIT 1', 
								array($estante_id));
		} else {
			$query = $this->db->query('SELECT estantes_movimento.*,estantes.descricao FROM estantes_movimento 
								   JOIN estantes ON estantes.id_estante=estantes.id_estante
			                       WHERE id_estante_movimento = ? LIMIT 1', array($movimento_id));
		}
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_movimento_estante

	/**
	 * Obtém a estante e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma estante da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_estante () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da estante
		$estante_id = chk_array( $this->parametros, 1 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_estante.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_estante'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_estante']);
			unset($_POST['coordenadas']);
			unset($_POST['responsavel']);

			if( $_POST['id_responsavel'] == 0) {
				$this->form_msg = 'Responsável Inválido !';
				return;		
			}


			// MAIUSCULAS
			$_POST['descricao'] = strtoupper($_POST['descricao']);
			
			// Atualiza os dados
			$query = $this->db->update('estantes', 'id_estante', $estante_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Estante Atualizada Com Sucesso!';
			}
			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query('SELECT estantes.*,voluntarios.nome as responsavel FROM estantes 
								   JOIN voluntarios on voluntarios.id_voluntario=estantes.id_responsavel
			                       WHERE id_estante = ? LIMIT 1', array($estante_id)
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_estante
}