<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller
{
	public function  __construct() 
	{
		parent::__construct();
		$this->load->model('arquivo_model');
	}
	
	public function index(){

		$data = array();

		//carrega a lib de multiplos uploads
		$this->load->library('multiload');

		if($this->input->post('fileSubmit') && !empty($_FILES['userFiles']['name'])){

			$this->multiload->use_database(true);
			$this->multiload->do_uploads($_FILES);

			$this->session->set_flashdata('statusMsg', $this->multiload->status_msg());

		}

		// Busca os arquivos no banco de dados
        $data['files'] = $this->arquivo_model->getRows();

		// Envia os arquivos para a view
		$this->load->view('index', $data);

	}

}