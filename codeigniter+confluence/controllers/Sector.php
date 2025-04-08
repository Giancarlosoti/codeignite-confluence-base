<?php
class Sector extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this -> load -> model ('proyecto_sector/m_sector','sector');
		$this->load->model('ingreso/m_ingreso','ingreso');
		$this->load->helper('security');
		
		//validar login
		$this->ingreso->log_in();
	}
	public function index()
	{	
	redirect('ficha/mostrar');	
	}
	public function ingresar(){
		
		$this->ingreso->acceso_pagina();
		
		$this->form_validation->set_rules('nombre', 'nombre', 'trim|required|xss_clean');
		$this->form_validation->set_rules('desc', 'desc', 'xss_clean');
		
		if($this->form_validation->run())
		{
			$nombre = $this->input->post('nombre',true);
			$medida = $this->input->post('medida',true);
			
			
			$datos_ing = array(
				'Nombre_sector'=>$nombre,
				'Medida_sector'=>$medida);
 
         $id_sector=$this -> sector -> ingresar($datos_ing);
		 
		 $usuario=$this->session->userdata('id_login');
				if($usuario){
						$accion_user=array(
						"id_modulo"=>2,
						"id_padre"=>$id_sector,
						"Accion_a_user"=>'agregar',
						"id_user"=>$usuario,
						"Fecha_a_user"=>date("Y-m-d H:i:s"),
						"IP"=>$_SERVER['REMOTE_ADDR']);
						$this->ingreso->agregar_accion_user($accion_user);
				}
				
         redirect('sector/ingresar', 'refresh');
		}
		else
		{	$nombre=array(
				'id'=>'nombre',
				'name'=>'nombre',
				'value'=>set_value('nombre')
			);
			$medida=array(
				'id'=>'medida',
				'name'=>'medida',
				'value'=>set_value('medida')
			);
			
			$datos = array(
				'nombre'=>$nombre,
				'medida'=>$medida);
				
			$datos['title']='Agregar Sector';	
			$datos['body']='proyecto_sector/agregar';
			$this->load->view('template/portalminero/completo',$datos);
		}
		
	}
	public function mostrar(){
		
				$this->ingreso->acceso_pagina();
				$controller=strtolower(get_class($this));
				$datos['permite_editar']=$this->ingreso->buscar_permiso_metodo($this->session->userdata('id_login'),$controller.'/editar');
				$datos['permite_borrar']=$this->ingreso->buscar_permiso_metodo($this->session->userdata('id_login'),$controller.'/borrar');
		
			$datos['sectores']=$this->sector->mostrar();
			$datos['title']='Mostrar Sectores';	
			$datos['body']='proyecto_sector/mostrar';
			$this->load->view('template/portalminero/completo',$datos);
	}
	public function editar($id){

		$this->ingreso->acceso_pagina();
		
		$this->form_validation->set_rules('nombre', 'nombre', 'trim|required|xss_clean');;
		$this->form_validation->set_rules('medida', 'medida', 'xss_clean');
		
		if($this->form_validation->run())
		{
			$nombre = $this->input->post('nombre',true);
			$medida = $this->input->post('medida',true);
			
			$datos_ing = array(
				'Nombre_sector'=>$nombre,
				'Medida_sector'=>$medida);
 
         $this -> sector -> guardar_edicion($datos_ing,$id);
		 
		 $usuario=$this->session->userdata('id_login');
				if($usuario){
						$accion_user=array(
						"id_modulo"=>2,
						"id_padre"=>$id,
						"Accion_a_user"=>'editar',
						"id_user"=>$usuario,
						"Fecha_a_user"=>date("Y-m-d H:i:s"),
						"IP"=>$_SERVER['REMOTE_ADDR']);
						$this->ingreso->agregar_accion_user($accion_user);
				}
				
         redirect('sector/mostrar');
		}
		else
		{	
			$registro=$this->sector->editar_proyecto($id);
			
			
			$nombre=array(
				'id'=>'nombre',
				'name'=>'nombre',
				'value'=>$registro->Nombre_sector
			);
			$medida=array(
				'id'=>'medida',
				'name'=>'medida',
				'value'=>$registro->Medida_sector
			);
			$datos = array(
				'id'=>$registro->id_sector,
				'nombre'=>$nombre,
				'medida'=>$medida);
				
			$datos['title']='Editar Sector';	
			$datos['body']='proyecto_sector/editar';
			$this->load->view('template/portalminero/completo',$datos);
		}
	}
	public function borrar($id){
		
		$this->ingreso->acceso_pagina();
		
			$this->proyect->borrar_id($id);
			redirect('ficha/mostrar');
	}
}