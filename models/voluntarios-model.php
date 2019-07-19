<?php

class VoluntariosModel extends MainModel
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
	 * Obtém a lista de voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_rel() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT voluntarios.*,
									(SELECT SUM(horas) FROM voluntarios_atividades AS va 
										WHERE va.id_voluntario=voluntarios.id_voluntario) as ha,
									(SELECT SUM(horas) FROM voluntarios_eventos AS ve
										WHERE ve.id_voluntario=voluntarios.id_voluntario
										AND ve.participou="S") as he,
									(SELECT COUNT(*) FROM voluntarios_eventos AS ve
										WHERE ve.id_voluntario=voluntarios.id_voluntario
										AND ve.participou="S") as ne,
									(SELECT MAX(data) FROM voluntarios_eventos AS ve
                                     JOIN eventos as e on e.id_evento=ve.id_evento
										WHERE ve.id_voluntario=voluntarios.id_voluntario
										AND ve.participou="S") as dataultimoevento,
									(SELECT SUM(horas) FROM certificados AS ce 
										WHERE ce.id_voluntario=voluntarios.id_voluntario) as hc
									FROM voluntarios										
								   ORDER BY nome');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list

	/**
	 * Obtém a lista de voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_atv() {

		// Configura o ID da voluntario
		$evento_id = chk_array( $this->parametros, 1 );

		$data = getdate();

		if (isset($evento_id)) {
			// Verifica data do evento
			$sql = "SELECT * FROM eventos WHERE id_evento=" . $evento_id;
			$query = $this->db->query(trim($sql));
		
			// Obtém os dados
			$test_data = $query->fetch();
		
			// Se os dados estiverem nulos, não faz nada
			if (!empty( $test_data ) ) {
				$data = $test_data['data'];
				if ($test_data['nova_data'] > $test_data['data']) $data = $test_data['nova_data'];
			}		
		}
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT voluntarios.*,
									(SELECT SUM(horas) FROM voluntarios_atividades AS va 
										WHERE va.id_voluntario=voluntarios.id_voluntario) as ha,
									(SELECT SUM(horas) FROM voluntarios_eventos AS ve
										WHERE ve.id_voluntario=voluntarios.id_voluntario
										AND ve.participou="S") as he,
									(SELECT COUNT(*) FROM voluntarios_eventos AS ve
										WHERE ve.id_voluntario=voluntarios.id_voluntario
										AND ve.participou="S") as ne,
									(SELECT MAX(data) FROM voluntarios_eventos AS ve
                                     JOIN eventos as e on e.id_evento=ve.id_evento
										WHERE ve.id_voluntario=voluntarios.id_voluntario
										AND ve.participou="S") as dataultimoevento,
									(SELECT SUM(horas) FROM certificados AS ce 
										WHERE ce.id_voluntario=voluntarios.id_voluntario) as hc
									FROM voluntarios
									WHERE voluntarios.status=1 
								   ORDER BY nome');

//									AND voluntarios.id_voluntario NOT IN 
//									    (SELECT id_voluntario FROM voluntarios_movimento as vm
//									     WHERE vm.id_voluntario=voluntarios.id_voluntario
//									     and vm.data>=' . $data . ' and vm.data_final<=' . $data .')
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list

	/**
	 * Obtém a lista de voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT * FROM voluntarios
								   ORDER BY voluntarios.status desc,voluntarios.nome');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list

	/**
	 * Obtém a lista de atividades dos voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_atividade_list() {

		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT voluntarios_atividades.*,voluntarios.nome,atividades.descricao
								   FROM voluntarios_atividades 
								   JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_atividades.id_voluntario
								   JOIN atividades ON atividades.id_atividade=voluntarios_atividades.id_atividade 
								   WHERE voluntarios_atividades.id_voluntario = ?
								   ORDER BY id_certificado,id_voluntario_atividade DESC',array($voluntario_id));
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list
	
	/**
	 * Obtém a lista de eventos dos voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_evento_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT voluntarios_eventos.*,voluntarios.nome,eventos.descricao,eventos.data,eventos.hora,eventos.duracao
								   FROM voluntarios_eventos
								   JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_eventos.id_voluntario
								   JOIN eventos ON eventos.id_evento=voluntarios_eventos.id_evento
								   ORDER BY id_voluntario_evento DESC');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list
	
	
	/**
	 * Obtém a lista de eventos dos voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_movimento_list() {
	
		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT voluntarios_movimento.*,voluntarios.nome
								   FROM voluntarios_movimento
								   JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_movimento.id_voluntario
								   ORDER BY id_voluntario_movimento DESC');
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list

	/**
	 * Obtém a lista de Telefones dos voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_fone_list() {

		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );

		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT * FROM telefones
								   WHERE id_voluntario = ?
								   ORDER BY id_fone',array($voluntario_id));
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do voluntario
		return $query->fetchAll();
	} // get_voluntario_fone_list

	/**
	 * Obtém a lista de atividades dos voluntarios
	 * 
	 * @since 0.1
	 * @access public
	 */
	public function get_voluntario_redesocial_list() {
	
		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );

		// Simplesmente seleciona os dados na base de dados 
		$query = $this->db->query('SELECT voluntarios_redes.*,voluntarios.nome,redes_sociais.nome
								   FROM voluntarios_redes 
								   JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_redes.id_voluntario
								   JOIN redes_sociais ON redes_sociais.id_rede=voluntarios_redes.id_rede
								   WHERE voluntarios_redes.id_voluntario = ? ORDER BY id_rede',array($voluntario_id));
		
		// Verifica se a consulta está OK
		if ( ! $query ) {
			return array();
		}
		// Preenche a tabela com os dados do usuário
		return $query->fetchAll();
	} // get_voluntario_list
	
	private function maisculas() {
		if (isset($_POST['nome'])) $_POST['nome'] = strtoupper($_POST['nome']);
		if (isset($_POST['apelido'])) $_POST['apelido'] = strtoupper($_POST['apelido']);
		if (isset($_POST['endereco'])) $_POST['endereco'] = strtoupper($_POST['endereco']);
		if (isset($_POST['bairro'])) $_POST['bairro'] = strtoupper($_POST['bairro']);
		if (isset($_POST['observacoes'])) $_POST['observacoes'] = strtoupper($_POST['observacoes']);
		if (isset($_POST['habilidades'])) $_POST['habilidades'] = trim(strtoupper($_POST['habilidades']));
	}

	private function limpa_post() {
			// Remove o campo insere_voluntario para não gerar problema com o PDO
			unset($_POST['insere_voluntario']);
			unset($_POST['municipio']);
			unset($_POST['arqfoto']);
			unset($_POST['id_fone']);
			unset($_POST['ddd']);
			unset($_POST['numero']);
			unset($_POST['local']);
			unset($_POST['id_rede']);
			unset($_POST['perfil']);
	}

/**
	 * Insere voluntario
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_voluntario() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_voluntario'] ) ) {
			return;
		}		

		unset ($_POST['id_voluntario']);

/*		$status = $_POST['status'];

		echo "<br>". $status . "</br>";

		if ($status == "on" ) {
			$status = 0;
		} else {
			$status = 1;
		}

		$_POST['status'] = $status;

*/
//		foreach ( $_POST as $key => $value ) {
//				echo $key . " - " . $value . " cada POST Insere</br>";			
//			}

		
		/*Para evitar conflitos apenas inserimos valores se o parâmetro edit
		não estiver configurado.
		*/
		if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
			return;
		}
		
		// Só pra garantir que não estamos atualizando nada
		if ( is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
			
		$this->limpa_post();

		// MAIUSCULAS
		$this->maisculas();

		// MINUSCULAS
		$_POST['email'] = strtolower($_POST['email']);

		if (isset($_POST['status'])) {
			$_POST['status'] = 1;
		}
		else {
			$_POST['status'] = 0;
		}

		// Insere os dados na base de dados
		$query = $this->db->insert( 'voluntarios', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Voluntário Inserido Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_voluntario	

/**
	 * Insere atividade do voluntario
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_atividade_voluntario() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD']) { /*|| empty( $_POST['insere_atividade_voluntario'] ) ) {*/
			return;
		}		

		unset ($_POST['insere_atividade_voluntario']);
		unset ($_POST['id_voluntario_atividade']);
		unset ($_POST['nome']);
		unset ($_POST['descricao']);
		unset ($_POST['min']);
		unset ($_POST['max']);


/*		foreach ( $_POST as $key => $value ) {
				echo $key . " - " . $value . " cada um POST </br>";			
			}

		foreach ( $this->parametros as $key => $value ) {
				echo $key . " - " . $value . " cada parametro </br>";			
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

		if ($_POST['id_atividade'] == 0 ) {
			$this->form_msg = 'Selecione uma atvidade !';
			return;
		}
			
		// Insere os dados na base de dados
		$query = $this->db->insert( 'voluntarios_atividades', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Atividade Realizada pelo Voluntário Inserida Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_voluntario_atividade	

/**


/**
	 * Insere contato do voluntario
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_contato_voluntario() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_contato_voluntario.
		*/

//		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_contato_voluntario'] ) ) {
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if (chk_array($this->parametros,2) != 0) return;

		unset ($_POST['insere_contato_voluntario']);
		unset ($_POST['nome']);
		unset ($_POST['apelido']);
		unset ($_POST['id_fone']);
		unset ($_POST['tipo']);   // Fixo ou celular verificar !!!

		if (isset($_POST['whatsapp'])) {
			$_POST['whatsapp'] = "S";
		}
		else
		{
			$_POST['whatsapp'] = 'N';
		}

		/*
		Para evitar conflitos apenas inserimos valores se o parâmetro edit
		não estiver configurado.
		*/
/*		if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
			return;
		}
		
		// Só pra garantir que não estamos atualizando nada
		if ( is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
*/			
//		foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada POST Insere Contato</br>";}

		// Insere os dados na base de dados
		$query = $this->db->insert( 'telefones', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Contato Inserido Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_contato_evento	

/**
	 * Insere perfil do voluntario
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_redesocial_voluntario() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_contato_voluntario.
		*/

//		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_contato_voluntario'] ) ) {
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if (chk_array($this->parametros,2) != 0) return;

		unset ($_POST['insere_redesocial_voluntario']);
		unset ($_POST['nome']);
		unset ($_POST['apelido']);

		/*
		Para evitar conflitos apenas inserimos valores se o parâmetro edit
		não estiver configurado.
		*/
 /*		if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
			return;
		}
		
		// Só pra garantir que não estamos atualizando nada
		if ( is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
*/			
		// Insere os dados na base de dados
		$query = $this->db->insert( 'voluntarios_redes', $_POST );
		
		// Verifica a consulta
		if ( $query ) {		
			// Retorna uma mensagem
			$this->form_msg = 'Perfil Inserido Com Sucesso!';
			return;			
		} 
		
		// :(
		$this->form_msg = 'Erro ao enviar dados!';

	} // insere_perfil_evento	

		/**
	 * Obtém a voluntario e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma voluntario da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_email_voluntario () {
		
		// Verifica se o primeiro parâmetro é "edit"

		$acao_form = chk_array($this->parametros,0);

		// Verifica se o primeiro parâmetro é valido
		if ( !$acao_form  || ( $acao_form != 'email') ) return;
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );

		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_voluntario'] ) ) {
		    
//		    $this->limpa_post();

//			foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada POST </br>";}

			// MAIUSCULAS
//			$this->maisculas();

			// MINUSCULAS
			if (isset($_POST['email'])) $_POST['email'] = strtolower($_POST['email']);

			// Dados para o email

			$para = $_POST['nome'] . '<' . $_POST['email'] . '>';
			$assunto = $_POST['assunto'];
			$de = 'Projeto Pegai Leitura Gratis';
			$email = 'sistemapegai@conepe.info';
			$mensagem = $_POST['mensagem'];
			$headers  = 'MIME-Version: 1.0' . "\r\n";
		    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      		$headers .= 'From:' .  $de . '<' . $email . '>' . "\r\n";

      		if ( mail($para, $assunto, $mensagem, $headers) ) {
      			$this->form_msg = 'e-mail Enviado com Sucesso !';
      		}			
      		else
      		{
      			$this->form_msg = 'Falha ao enviar o e-mail !';
      		}

			// Atualiza os dados
			//$query = $this->db->update('voluntarios', 'id_voluntario', $voluntario_id, $_POST);
			
			// Verifica a consulta
			//if ( $query ) {
				// Retorna uma mensagem
			//	$this->form_msg = 'Voluntário Atualizado Com Sucesso!';
			//}			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query('SELECT voluntarios.* FROM voluntarios
			                       WHERE id_voluntario = ? LIMIT 1', array($voluntario_id));
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_voluntario



	/**
	 * Obtém a voluntario e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma voluntario da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_voluntario () {
		
		// Verifica se o primeiro parâmetro é "edit"

		$acao_form = chk_array($this->parametros,0);

		// Verifica se o primeiro parâmetro é valido
		if ( !$acao_form  || ( $acao_form != 'edit' 
							&& $acao_form != 'cad' 
							&& $acao_form != 'fot' 
							&& $acao_form != 'hab' 
							&& $acao_form != 'obs' 
							)) {
								return;
							}	

		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );

		if (isset($_FILES['foto'])) {

			$foto = $_FILES['foto'];
	
			// Se a foto estiver sido selecionada
			if (!empty($foto["name"])) {
			
				// Largura máxima em pixels
				$largura = 250;
				// Altura máxima em pixels
				$altura = 250;
				// Tamanho máximo do arquivo em bytes
				$tamanho = 1000;

				$error = array();

		    	// Verifica se o arquivo é uma imagem
		    	if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $foto["type"])){
		     	   $error[1] = "Arquivo selecionado não é uma imagem !";
		   	 	} 
			
				// Pega as dimensões da imagem
				$dimensoes = getimagesize($foto["tmp_name"]);
			
				// Verifica se a largura da imagem é maior que a largura permitida
				if($dimensoes[0] > $largura) {
					$error[2] = "A largura da imagem não deve ultrapassar ".$largura." pixels";
				}

				// Verifica se a altura da imagem é maior que a altura permitida
				if($dimensoes[1] > $altura) {
					$error[3] = "Altura da imagem não deve ultrapassar ".$altura." pixels";
				}
				
				// Verifica se o tamanho da imagem é maior que o tamanho permitido
				//if($foto["size"] > $tamanho) {
		   		// 	$error[4] = "A imagem deve ter no máximo ".$tamanho." bytes";
				//}

				// Se não houver nenhum erro
				if (count($error) == 0) {
				
					// Pega extensão da imagem
					preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $foto["name"], $ext);

		        	// Gera um nome único para a imagem
		        	$nome_imagem = md5(uniqid(time())) . "." . $ext[1];

		        	// Caminho de onde ficará a imagem
		        	$caminho_imagem = $_SERVER['DOCUMENT_ROOT'] . "/views/_images/" . $nome_imagem;

					// Faz o upload da imagem para seu respectivo caminho
					if (move_uploaded_file($foto['tmp_name'], $caminho_imagem)) {
						chmod($caminho_imagem, 777);
						$_POST['foto'] = trim($nome_imagem);
					}
					else
					{
						$error[5] = "Nao foi feito o Upload do Arquivo !";
					}
				}
			
				// Se houver mensagens de erro, exibe-as
				if (count($error) != 0) {
					foreach ($error as $erro) {
						$this->form_msg = $this->form_msg . $erro . '\n';
					}
					return;
				}
			}		
		}
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( ('POST' == $_SERVER['REQUEST_METHOD']) && (! empty( $_POST['insere_voluntario'] ) ) ) {
		    
		    $this->limpa_post();

//			foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada POST </br>";}

			// MAIUSCULAS
			$this->maisculas();

			// MINUSCULAS
			if (isset($_POST['email'])) $_POST['email'] = strtolower($_POST['email']);

			if (isset($_POST['status'])) {
				$_POST['status'] = 1;
			}
			else {
				$_POST['status'] = 0;
			}

			// Atualiza os dados
			$query = $this->db->update('voluntarios', 'id_voluntario', $voluntario_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Voluntário Atualizado Com Sucesso!';
			}
			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query('SELECT voluntarios.*,municipios.municipio FROM voluntarios
									JOIN municipios ON municipios.id_municipio=voluntarios.id_municipio
			                       WHERE id_voluntario = ? LIMIT 1', array($voluntario_id));
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_voluntario

	/**
	 * Obtém a voluntario e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma voluntario da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_atividade_voluntario () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'atv' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );
		$atividade_id = chk_array( $this->parametros, 2 );
		$acao = chk_array( $this->parametros,3);		
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_atividade_voluntario'] ) ) {
		    
		    unset ($_POST['insere_atividade_voluntario']);
		    unset ($_POST['nome']);
		    unset ($_POST['descricao']);
		    unset ($_POST['min']);
		    unset ($_POST['max']);

			//foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada POST </br>";}

			// Atualiza os dados
			if ($atividade_id != 0) {
				$query = $this->db->update('voluntarios_atividades', 'id_atividade', $atividade_id, $_POST);
			
				// Verifica a consulta
				if ( $query ) {
					// Retorna uma mensagem
					$this->form_msg = 'Atividade Realizada pelo Voluntário Atualizada com Sucesso !';
				}
			}
		}

		if ($acao == 'del') {

			$query = $this->db->delete('voluntarios_atividades', 'id_voluntario_atividade', $atividade_id);

			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Atividade Realizada pelo Voluntário Apagada com Sucesso !';
				$atividade_id=0;
			}
		}

		// Faz a consulta para obter o valor
		if ( $atividade_id == 0 ) {
			$query = $this->db->query('SELECT id_voluntario,nome,data_inicio
										FROM voluntarios 
										WHERE id_voluntario = ? LIMIT 1', 
										array($voluntario_id));
		} else {
			$query = $this->db->query('SELECT voluntarios_atividades.*,voluntarios.nome,voluntarios.data_inicio
									FROM voluntarios_atividades
									JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_atividades.id_voluntario
			                       WHERE id_voluntario_atividade = ? LIMIT 1', array($atividade_id));
		}
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_voluntario_atividade

	/**
	 * Obtém a voluntario e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma voluntario da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_redesocial_voluntario () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'red' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da voluntario
		$voluntario_id = chk_array( $this->parametros, 1 );
		$redesocial_id = chk_array ($this->parametros, 2 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_redesocial_voluntario'] ) ) {
		    
		    unset ($_POST['nome']);
		    unset ($_POST['descricao']);

			//foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada POST </br>";}

			// Atualiza os dados
			$sql = "UPDATE voluntarios_redes SET perfil='" . trim($_POST['perfil']) . "' WHERE id_voluntario=" . $_POST['id_voluntario'] . " AND id_rede=" . $_POST['id_rede'];

			$query = $this->db->query($sql);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = 'Perfil do Voluntario Atualizado com Sucesso!';
			}			
		}

		// Faz a consulta para obter o valor
		if ( $redesocial_id == 0 ) {
			$query = $this->db->query('SELECT id_voluntario,nome,apelido 
										FROM voluntarios 
										WHERE id_voluntario = ? LIMIT 1', 
										array($voluntario_id));
		} else {
			$query = $this->db->query('SELECT voluntarios_redes.*,voluntarios.id_voluntario,voluntarios.nome,voluntarios.apelido
										FROM voluntarios_redes
					        			JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_redes.id_voluntario
				                       	WHERE voluntarios_redes.id_voluntario = ? 
			    	                   	AND voluntarios_redes.id_rede = ? LIMIT 1', 
			    	                   	array($voluntario_id,$redesocial_id));
		}

		
		// Faz a consulta para obter o valor
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_voluntario_atividade

	/**
	 * Obtém a voluntario e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma voluntario da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_contato_voluntario () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'con' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
//		if ( ! is_numeric( chk_array( $this->parametros, 2 ) ) ) {
//			return;
//		}

		// Configura o ID do contato

		$voluntario_id = chk_array( $this->parametros,1);
		$contato_id = chk_array( $this->parametros,2);
		$acao = chk_array( $this->parametros,3);

//		echo "<p>" . $voluntario_id . " - " . $contato_id . "</p></br>";
		//foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " POST Obtem contato </br>";}


		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_contato_voluntario'] ) ) {
		    
		    unset ($_POST['id_fone']);
		    //unset ($_POST['id_voluntario']);
		    unset ($_POST['nome']);
		    unset ($_POST['apelido']);
		    unset ($_POST['insere_contato_voluntario']);
		    unset ($_POST['tipo']);

		    if (isset($_POST['whatsapp'])) {
		    	$_POST['whatsapp'] = 'S';
		    }
		    else
		    {
		    	$_POST['whatsapp'] = "N";
		    }

			// Atualiza os dados
			if ($contato_id != 0) {

				$query = $this->db->update('telefones', 'id_fone', $contato_id, $_POST);

				// Verifica a consulta
				if ( $query ) {
					// Retorna uma mensagem
					$this->form_msg = 'Contato do Voluntário Atualizado com Sucesso';
				}
			}
		}

		if ($acao == 'del') {

			$query = $this->db->delete('telefones', 'id_fone', $contato_id, $_POST);

			if ($query) {
				$this->form_msg = 'Contato do Voluntário Apagado com Sucesso';
				$contato_id=0;
			}

		}

		// Faz a consulta para obter o valor
		if ( $contato_id == 0 ) {
			$query = $this->db->query('SELECT id_voluntario,nome,apelido 
										FROM voluntarios 
										WHERE id_voluntario = ? LIMIT 1', 
										array($voluntario_id));
		} else {
			$query = $this->db->query('SELECT telefones.*,voluntarios.id_voluntario,voluntarios.nome,voluntarios.apelido 
										FROM telefones JOIN voluntarios ON voluntarios.id_voluntario=telefones.id_voluntario
										WHERE telefones.id_fone = ? LIMIT 1',
										array($contato_id));
		}

		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_contato_voluntario

	/**
	 * Obtém a voluntario e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma voluntario da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_evento_voluntario () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da voluntario
		$evento_id = chk_array( $this->parametros,1);
		$voluntario_evento_id = chk_array( $this->parametros, 2 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_evento_voluntario'] ) ) {
		    
			unset ($_POST['insere_evento_voluntario']);
		    unset ($_POST['nome']);
		    unset ($_POST['descricao']);
		    unset ($_POST['data']);
		    unset ($_POST['hora']);
		    unset ($_POST['duracao']);

		    if (isset($_POST['participou'])) {
		    	$_POST['participou'] = 'S';
		    }
		    else
		    {
		    	$_POST['participou'] = 'N';
		    }

			//foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada POST </br>";}

			// Atualiza os dados
			$query = $this->db->update('voluntarios_eventos', 'id_voluntario_evento', $voluntario_evento_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = '<p class="success">Evento do Voluntario Atualizado com Sucesso!</p>';
			}
			
		}
		
		if ($voluntario_evento_id == 0) {
			// Faz a consulta para obter o valor
			$query = $this->db->query('SELECT eventos.*
										FROM eventos
				                       	WHERE id_evento = ? LIMIT 1', array($evento_id));
		}
		else {
			// Faz a consulta para obter o valor
			$query = $this->db->query('SELECT voluntarios_eventos.*,voluntarios.nome,eventos.*
										FROM voluntarios_eventos
										JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_eventos.id_voluntario
										JOIN eventos ON eventos.id_evento=voluntarios_eventos.id_evento
			                       		WHERE id_voluntario_evento = ? LIMIT 1', array($voluntario_evento_id));
		}

		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_voluntario_evento

/**
	 * Obtém a voluntario e atualiza os dados se algo for postado
	 *
	 * Obtém apenas uma voluntario da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_movimento_voluntario () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da voluntario
		$voluntario_movimento_id = chk_array( $this->parametros, 1 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_voluntario.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['insere_movimento_voluntario'] ) ) {
		    
			unset ($_POST['insere_movimento_voluntario']);
		    unset ($_POST['nome']);

		    if (($_POST['data_final'])=="") unset($_POST['data_final']);

			//foreach ( $_POST as $key => $value ) {echo $key . " - " . $value . " cada POST </br>";}

			// Atualiza os dados
			$query = $this->db->update('voluntarios_movimento', 'id_voluntario_movimento', $voluntario_movimento_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = '<p class="success">Movimento do Voluntario Atualizado com Sucesso!</p>';
			}
			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query('SELECT voluntarios_movimento.*,voluntarios.nome
									FROM voluntarios_movimento
									JOIN voluntarios ON voluntarios.id_voluntario=voluntarios_movimento.id_voluntario
			                       WHERE id_voluntario_movimento = ? LIMIT 1', array($voluntario_movimento_id));
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_voluntario_movimento

}