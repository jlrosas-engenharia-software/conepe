<?php

class CertificadosModel extends MainModel
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
	 * Obtém a lista de Certificados
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_certificado_list() {

		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );

		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT certificados.*,voluntarios.nome
								   FROM certificados 
								   JOIN voluntarios ON voluntarios.id_voluntario=certificados.id_voluntario
								   where certificados.id_voluntario = ?
		 						   ORDER BY id_certificado DESC',array($voluntario_id));
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_certificado_list

/**
	 * Insere certificado
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_certificado() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_certificado.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD']){ // || empty( $_POST['insere_certificado'] ) ) {
			return;
		}		

		unset ($_POST['id_certificado']);
		unset ($_POST['nome']);
		unset ($_POST['alterado']);

//		foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada um POST </br>";}
/*

		/*
		Para evitar conflitos apenas inserimos valores se o parâmetro edit
		não estiver configurado.
		*/
		//if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
		//	return;
		//}
		
		// Só pra garantir que não estamos atualizando nada
		if ( is_numeric( chk_array( $this->parametros, 2 ) ) ) {
			return;
		}

		$voluntario_id = chk_array( $this->parametros , 1);
		
		$_POST['id_voluntario'] = $voluntario_id;
			
		// Remove o campo insere_notica para não gerar problema com o PDO
		unset($_POST['insere_certificado']);

		$data_inicial = $_POST['data_inicial'];
		$data_final = $_POST['data_final'];
		$total_horas = 0;
		
		// Procura atividades e eventos
		$query = $this->db->query('SELECT sum(horas) as horasatividade
								   FROM voluntarios_atividades
			                       WHERE id_voluntario = ? 
			                       and (data>= ? and data<= ?)
			                       and id_certificado is null LIMIT 1', 
			                       array($voluntario_id,$data_inicial,$data_final)
			                  	);
		
		if ($query) {

			// Obtém os dados
			$fetch_data = $query->fetch();
			
			// Se os dados estiverem nulos, não faz nada
			if (! empty( $fetch_data ) ) {
				$total_horas = $total_horas + $fetch_data['horasatividade'];
			}
		}
			
		$query = $this->db->query('SELECT sum(horas) as horaseventos
								   FROM voluntarios_eventos
								   JOIN eventos on eventos.id_evento=voluntarios_eventos.id_evento
			                       WHERE id_voluntario = ? 
			                       and (eventos.data>= ? and eventos.data<= ?)
			                       and participou = \'S\' 
			                       and id_certificado is null LIMIT 1', 
			                       array($voluntario_id,$data_inicial,$data_final)
			                 	);
		
		if ($query) {

			// Obtém os dados
			$fetch_data = $query->fetch();
			
			// Se os dados estiverem nulos, não faz nada
			if (! empty( $fetch_data ) ) {
				$total_horas = $total_horas + $fetch_data['horaseventos'];
			}

			$_POST['horas'] = $total_horas;
		}

		// Insere os dados na base de dados
		$query = $this->db->insert( 'certificados', $_POST );

		$id = $this->db->last_id;
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Certificado Gerado Com Sucesso';

			$query = $this->db->query('UPDATE voluntarios_atividades
										SET id_certificado = ?
				                       WHERE id_voluntario = ? 
				                       and (data>= ? and data<= ?)
			        	               and id_certificado is null', 
			        	               array($id,$voluntario_id,$data_inicial,$data_final)
			        	           	);

			$query = $this->db->query('UPDATE voluntarios_eventos
										JOIN eventos on voluntarios_eventos.id_evento=eventos.id_evento
										SET id_certificado = ?
				                       WHERE id_voluntario = ? 
				                       and (eventos.data>= ? and eventos.data<= ?)
			    	                   and participou = \'S\' 
			        	               and id_certificado is null', 
			        	               array($id,$voluntario_id,$data_inicial,$data_final)
			        	           	);
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_certificado	

	/**
	 * Obtém a certificado e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma certificado da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_certificado () {
		
		// Verifica se o primeiro parâmetro é "edit"
		$acao_form = chk_array($this->parametros,0);

		// Verifica se o primeiro parâmetro é valido
		if ( !$acao_form  || ( $acao_form != 'edit') ) return;
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da certificado
		$voluntario_id = chk_array( $this->parametros, 1 );
		$certificado_id = chk_array($this->parametros, 2 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_certificado.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_certificado'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_certificado']);
			unset($_POST['nome']);

			// Atualiza os dados
			$query = $this->db->update('certificados', 'id_certificado', $certificado_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Certificado Atualizado Com Sucesso!';
			}
			
		}
		
		if ($certificado_id == 0 ) {
		// Faz a consulta para obter o valor
			$query = $this->db->query('SELECT voluntarios.id_voluntario,voluntarios.nome
									   FROM voluntarios 
				                       WHERE id_voluntario= ? LIMIT 1', array($voluntario_id));
	
		}
		else 
		{
			// Faz a consulta para obter o valor
			$query = $this->db->query('SELECT certificados.*,voluntarios.nome
									   FROM certificados 
									   JOIN voluntarios ON voluntarios.id_voluntario=certificados.id_voluntario
				                       WHERE id_certificado = ? LIMIT 1', array($certificado_id));
		}
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
	
	} // obtem_certificado

	/**
	 * Obtém a certificado e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma certificado da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_view_certificado () {

		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 0 ) ) ) {
			return;
		}
		
		// Configura o ID da certificado
		$certificado_id = chk_array( $this->parametros, 0 );

		// Faz a consulta para obter o valor
		$query = $this->db->query('SELECT certificados.*,voluntarios.nome,voluntarios.cpf,voluntarios.rg,voluntarios.sexo
								   FROM certificados 
								   JOIN voluntarios ON voluntarios.id_voluntario=certificados.id_voluntario
			                       WHERE id_certificado = ? LIMIT 1', array($certificado_id)
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
	
	} // obtem_certificado
}