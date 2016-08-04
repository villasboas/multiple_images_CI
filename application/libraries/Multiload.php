<?php defined('BASEPATH') OR exit('No direct script access allowed');

//biblioteca de multiplos uploads
class Multiload {

	//variavel com a instancia do ci
	public $_CI;

	//guarda o path de upload
	private $__upload_path;

	//guarda o status da ultima tentativa de upload
	private $__upload_status = "error";

	//array com os dados de todos os uploads
	private $__uploads_data = array();

	//guarda se os uploads devem ser guardados no banco de dados ou não
	private $__use_database  = false;

	//metodo construtor
	public function __construct() {

		//carrega a instancia do ci
		$this->_CI =& get_instance();

		//carrega as library usadas na lib
		$this->_CI->load->database();

		//carrega as configurações
		$this->_CI->config->load('multiload');

		//seta as variaveis da classe
		$this->__upload_path = $this->_CI->config->item('upload_path');
	}

	//faz o upload multiplo
	public function do_uploads($files = false) {

		//verifica se a variavel $files existe
		if(!$files)
			return false;

		//seta o numero de arquivos a serem subidos
		$file_count = count($files['userFiles']['name']);

		//percorre cada um dos arquivos
		for($i = 0; $i < $file_count; $i++){

			//seta o arquivo de upload
			$_FILES['userFile']['name'] 	= md5($files['userFiles']['name'][$i].date('Y-m-d H:i:s')).'.jpg';
			$_FILES['userFile']['type'] 	= $files['userFiles']['type'][$i];
			$_FILES['userFile']['tmp_name'] = $files['userFiles']['tmp_name'][$i];
			$_FILES['userFile']['error'] 	= $files['userFiles']['error'][$i];
			$_FILES['userFile']['size'] 	= $files['userFiles']['size'][$i];

			//configurações da lib
			$config['upload_path'] 	 = $this->__upload_path;
			$config['allowed_types'] = $this->_CI->config->item('allowed_types');
				
			//inicializa a lib	
			$this->_CI->load->library('upload', $config);
			$this->_CI->upload->initialize($config);

			//faz o upload do arquivo
			$upload = $this->_CI->upload->do_upload('userFile');
		
			//se o upload nao foi feito
			if(!$upload) {
				$this->__upload_status = "error";
				return false;
			}

			//se a opcao para guardar no banco de dados está ativa
			if($this->__use_database)

				//guarda os dados do novo upload
				$this->__setNewUploadData();
		}

		//verifica se deve inserir os dados na tabela de uploads
		if($this->__use_database)
			$this->__insertUploadRegister();

		$this->__upload_status = "success";
		return true;
	}

	//seta o uso do banco de dados
	public function use_database($set = false) {

		//seta a global
		$this->__use_database = $set;
	}

	//volta o status do ultimo upload
	public function status() {
		return $this->__upload_status;
	}

	//volta a mensagem de status
	public function status_msg(){
		if($this->__upload_status === "success")
			return $this->_CI->config->item($this->__upload_status."_msg");
		else {
			$msg = $this->_CI->config->item($this->__upload_status."_msg");
			$msg .= "<br>".$this->_CI->upload->display_errors();
			return $msg;
		}
	}

	//seta os dados do upload
	private function __setNewUploadData() {

		//pega os dados do upload
		$fileData = $this->_CI->upload->data();

		//guarda na propriedade da classe
		$temp_array = array(
			"file_name" => $fileData['file_name'],
			"created" 	=> date("Y-m-d H:i:s"),
			"modified"  => date("Y-m-d H:i:s")
		);

		$this->__uploads_data[] = $temp_array;

		return true;
	}

	//faz o registro do upload no banco de dados
	private function __insertUploadRegister(){

		//insere e retorna os dados
		$insert = $this->_CI->db->insert_batch($this->_CI->config->item('table'), $this->__uploads_data);
		return $insert;
	}

}