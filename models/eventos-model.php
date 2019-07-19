<?php

class EventosModel extends MainModel
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
	 * Obtém a lista de Eventos
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_evento_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT * FROM eventos 
		 						   ORDER BY status,data DESC');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}

		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_evento_list

	/**
	 * Obtém a lista de voluntarios por evento
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_evento_voluntario_list() {

		// Configura o ID da voluntario
		$evento_id = chk_array( $this->parametros, 1 );

		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT voluntarios_eventos.*,voluntarios.nome
								   FROM voluntarios_eventos
								   JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_eventos.id_voluntario
								   WHERE voluntarios_eventos.id_evento = ?
								   ORDER BY flaga desc,nome',array($evento_id));
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list

	/**
	 * Obtém a lista de voluntarios por evento
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_evento_voluntario_rel() {

		// Configura o ID da voluntario
		$evento_id = chk_array( $this->parametros, 1 );

		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT ve.id_voluntario,ve.participou,ve.horas,
											v1.nome as coordenador,
											v2.nome as voluntario,
											ev.descricao,ev.local,ev.data,ev.hora
								   FROM voluntarios_eventos as ve
								   JOIN eventos as ev on ev.id_evento=ve.id_evento
								   JOIN voluntarios as v1 ON v1.id_voluntario=ev.id_responsavel
								   JOIN voluntarios as v2 ON v2.id_voluntario=ve.id_voluntario
								   WHERE ve.id_evento = ?',array($evento_id));
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list

/**
	 * Insere evento
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_evento() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_evento.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_evento'] ) ) {
			return;
		}		

		if( $_POST['id_responsavel'] == 0) {
			$this->form_msg = 'Responsável Inválido !';
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
		unset($_POST['insere_evento']);

		unset ($_POST['id_evento']);
		unset ($_POST['status']);
		unset ($_POST['coordenadas']);
		unset ($_POST['participantes']);
		unset ($_POST['antigo']);

		// MAIUSCULAS
		$_POST['descricao'] = strtoupper($_POST['descricao']);
		$_POST['local'] = strtoupper($_POST['local']);

		// Configura a data
		/*
		$data = chk_array( $_POST, 'data' );
		$nova_data = $this->inverte_data( $data );
				
		// Adiciona a data no POST
		$_POST['data'] = $nova_data;
		*/

		// Insere os dados na base de dados
		$query = $this->db->insert( 'eventos', $_POST );
		
		// Verifica a consulta
		if ( $query ) {

			$_EVVOL = array();
			$_EVVOL['id_voluntario'] = $_POST['id_responsavel'];
			$_EVVOL['id_evento'] = $this->db->last_id();
			$_EVVOL['observacoes'] = 'COORDENADOR DO EVENTO';
			$_EVVOL['flaga'] = '1';

			$query = $this->db->insert('voluntarios_eventos', $_EVVOL );
			unset($_EVVOL);		
			
			// Retorna uma mensagem
			$this->form_msg = 'Evento Inserido Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_evento	

	 /* Insere evento do voluntario
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_evento_voluntario() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD']){ //|| empty( $_POST['insere_evento_voluntario'] ) ) {
			return;
		}		

		unset ($_POST['insere_evento_voluntario']);
		unset ($_POST['id_voluntario_evento']);
		unset ($_POST['descricao']);
		unset ($_POST['local']);
		unset ($_POST['coordenadas']);
		unset ($_POST['status']);
		unset ($_POST['data']);
		unset ($_POST['hora']);
		unset ($_POST['duracao']);
		unset ($_POST['participantes']);
		unset ($_POST['id_responsavel']);
		unset ($_POST['dataultimoevento']);


/*		foreach ( $_POST as $key => $value ) {
				echo $key . " - " . $value . " cada um POST </br>";			
			}

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

		if ($_POST['id_voluntario'] == 0 ) {
			$this->form_msg = 'Selecione o Voluntário !';
			return;
		}

		$evento_id = $_POST['id_evento'];
		$voluntario_id = $_POST['id_voluntario'];

		// Verifica se o Voluntario ja esta Atribuido ao Evento
		$sql = "SELECT * FROM voluntarios_eventos WHERE id_evento=" . $evento_id . " and id_voluntario=" . $voluntario_id; 

		$query = $this->db->query(trim($sql));
		
		// Obtém os dados
		$test_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if (!empty( $test_data ) ) {
			$this->form_msg = 'Voluntário já atribuído ao evento!';
			return;
		}		

		// Insere os dados na base de dados
		$query = $this->db->insert( 'voluntarios_eventos', $_POST );
		return;
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = ''; //'Voluntário atribuido ao evento com sucesso!';
			return;			
		} 
				// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_voluntario_evento	


	/**
	 * Obtém a evento e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma evento da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_evento () {
		
		$acao_form = chk_array($this->parametros,0);

		// Verifica se o primeiro parâmetro é edit ou trf
		if ( !$acao_form  || ( $acao_form != 'edit' 
							&& $acao_form != 'add' 
							&& $acao_form != 'can' 
							&& $acao_form != 'trf' ) ) {
			return;
		}

		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da evento

		$evento_id     = chk_array( $this->parametros, 1 );
		$voluntario_id = chk_array( $this->parametros, 2 );
		$acao          = chk_array( $this->parametros, 3 );

		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_evento.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_evento'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_evento']);
			unset($_POST['coordenadas']);
			unset($_POST['participantes']);

			if( $_POST['id_responsavel'] == 0) {
				$this->form_msg = 'Responsável Inválido !';
				return;		
			}

			$status = chk_array ($_POST,'status');

			if ($status) {
				switch ($status) {
					case 'PENDENTE':
						# code...
					    $_POST['status'] = 0;
						break;
					
					case 'FECHADO':
						# code...
					    $_POST['status'] = 1;
						break;

					case 'TRANSFERIDO':
						# code...
					    $_POST['status'] = 2;
						break;

					case 'CANCELADO':
						# code...
					    $_POST['status'] = 3;
						break;

					default:
						# code...
					    $_POST['status'] = 0;
						break;
				}
			}

			if (isset($_POST['concluido'])) {
				$_POST['status'] = 1;
				unset($_POST['concluido']);
			}

			if ($acao_form == 'can') {
				$_POST['status'] = 3;
				unset($_POST['nova_data']);
				unset($_POST['novo_local']);
				unset($_POST['nova_hora']);
				unset($_POST['novas_coordenadas']);
			}

			if ($acao_form == 'trf') $_POST['status'] = 2;

			// Verifica se a data foi enviada
			$data = chk_array( $_POST, 'data' );
			
			/*
			Inverte a data para os formatos dd-mm-aaaa hh:mm:ss
			ou aaaa-mm-dd hh:mm:ss
			*/
			//$nova_data = $this->inverte_data( $data );
			
			// Adiciona a data no $_POST		
			//$_POST['data'] = $nova_data;


			// MAIUSCULAS
			$_POST['descricao'] = strtoupper($_POST['descricao']);
			$_POST['local'] = strtoupper($_POST['local']);
			if (isset($_POST['novo_local'])) $_POST['novo_local'] = strtoupper($_POST['novo_local']);

			if (isset($_POST['motivoantigo'])) {
				$_POST['motivo'] = $_POST['motivoantigo'] . "\n" . $_POST['motivo'];
			} 

			unset($_POST['motivoantigo']);

			// COORDENADOR DO EVENTO
			$coordenador = $_POST['antigo'];
			unset ($_POST['antigo']);

			// Atualiza os dados
			$query = $this->db->update('eventos', 'id_evento', $evento_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {

				// Atualiza o Coordenador do Evento

				if ($coordenador) {
					if ($coordenador != $_POST['id_responsavel']) {
						$sql = "UPDATE voluntarios_eventos SET flaga=1,id_voluntario=" . $_POST['id_responsavel'] . " WHERE id_evento=" . $evento_id . " 	AND id_voluntario=" . $coordenador;
						$query = $this->db->query(trim($sql));
					}
				}

				if ($acao_form == 'can') {
					$sql = "UPDATE voluntarios_eventos SET flagb=2,participou='N',observacoes=CONCAT(observacoes,'\nEVENTO CANCELADO') 
							WHERE id_evento=" . $evento_id;
					$query = $this->db->query(trim($sql));
				}

				// Retorna uma mensagem
				if ($acao_form == 'can') $this->form_msg  = 'Evento Cancelado Com Sucesso !!!';
				if ($acao_form == 'edit') $this->form_msg = 'Evento Atualizado Com Sucesso !!!';
				if ($acao_form == 'trf') $this->form_msg  = 'Evento Transferido Com Sucesso !!!';
			}
		}

		// Faz a consulta para obter o valor
		$query = $this->db->query('SELECT eventos.*,voluntarios.nome,
								   (SELECT count(*) 
								   	FROM voluntarios_eventos 
								   	WHERE id_evento=eventos.id_evento) as participantes
								   FROM eventos 
								   JOIN voluntarios ON voluntarios.id_voluntario=eventos.id_responsavel
			                       WHERE id_evento = ? LIMIT 1', array($evento_id)
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;

	} // obtem_evento

	/**
	 * Obtém a evento e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma evento da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_voluntario_evento () {
		
		$acao_form = chk_array($this->parametros, 0 );

		// Verifica se o primeiro parâmetro é edit ou trf
		if ( !$acao_form  || ( $acao_form != 'add'  && $acao_form != 'end' ) ) {
			return;
		}

		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID 
		$evento_id = chk_array( $this->parametros, 1 );
		$vol_ev_id = chk_array( $this->parametros, 2 );
		$acao      = chk_array( $this->parametros, 3 );

		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_evento.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_voluntario_evento'] ) ) {
		*/

		if ( 'POST' == $_SERVER['REQUEST_METHOD']) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_evento_voluntario']);
			unset($_POST['descricao']);
			unset($_POST['local']);
			unset($_POST['coordenadas']);
			unset($_POST['participantes']);
			unset($_POST['data']);
			unset($_POST['hora']);
			unset($_POST['duracao']);
			unset($_POST['status']);
			unset($_POST['dataultimoevento']);

			if (isset($_POST['participou'])) {
				$_POST['participou'] = "S";
			}
			else
			{
				$_POST['participou'] = "N";
			}

			$_POST['flagb'] = "1";

			// Verifica se a data foi enviada

			// Atualiza os dados
			$query = $this->db->update('voluntarios_eventos', 'id_voluntario_evento', $vol_ev_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg  = 'Voluntário Atualizado no Evento Com Sucesso !!!';
			}
		}

		if ($acao == 'del') {

			$query = $this->db->delete('voluntarios_eventos', 'id_voluntario_evento', $vol_ev_id);

			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Voluntário Removido do Evento com Sucesso !';
				$vol_ev_id=0;
			}
		}

		if ($vol_ev_id == 0 ) {
			// Faz a consulta para obter o valor
			$query = $this->db->query('SELECT eventos.*,voluntarios.nome as nomeresponsavel,
									   (SELECT count(*) 
									   	FROM voluntarios_eventos 
									   	WHERE voluntarios_eventos.id_evento=eventos.id_evento) as participantes
									   FROM eventos 
									   JOIN voluntarios ON voluntarios.id_voluntario=eventos.id_responsavel
				                       WHERE eventos.id_evento = ? LIMIT 1', array($evento_id));
		}
		else {
			// Faz a consulta para obter o valor
			$query = $this->db->query('SELECT voluntarios_eventos.*,voluntarios_eventos.motivo as mve,voluntarios_eventos.id_voluntario as mveid,eventos.*,voluntarios.nome, v1.nome as nomeresponsavel,
										(SELECT count(*) 
									   		FROM voluntarios_eventos as ve
									   		WHERE ve.id_evento=voluntarios_eventos.id_evento) as participantes				
									   FROM voluntarios_eventos
									   JOIN eventos on eventos.id_evento=voluntarios_eventos.id_evento 
									   JOIN voluntarios as v1 on v1.id_voluntario=eventos.id_responsavel
									   JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_eventos.id_voluntario
			        	               WHERE voluntarios_eventos.id_voluntario_evento = ? LIMIT 1', array($vol_ev_id));

		}

		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;

	} // obtem_evento
}