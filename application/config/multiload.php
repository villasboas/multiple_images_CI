<?php defined('BASEPATH') OR exit('No direct script access allowed');

//pasta caminhos dos uploads
$config["upload_path"]   = "uploads/files/";

//tipos permitidos dos arquivos para upload
$config["allowed_types"] = 'gif|png|jpg|jpeg';

//msg de sucesso
$config["success_msg"]   = "O upload dos arquivos foi feito com sucesso";

//mensagem de erro
$config["error_msg"]     = "Não foi possivel fazer o upload dos arquivos";

//nome da tabela onde estao guardados os registros dos arquivos
$config["table"]        = "files";

/*

	Configurações do banco de dados
	

*/
