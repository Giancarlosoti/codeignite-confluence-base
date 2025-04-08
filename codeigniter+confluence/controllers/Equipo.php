<?php
class Equipo extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this -> load -> model ('obra/m_obra','obra');
		$this -> load -> model ('proyecto_tipo/m_tipo','tipo');
		$this -> load -> model ('proyecto_sector/m_sector','sector');
		$this -> load -> model ('equipo/m_equipo','equipo');
		$this->load->model('ingreso/m_ingreso','ingreso');
		$this->load->helper('security');
		//validar login
		$this->ingreso->log_in();
	}
	public function index()
	{	
	redirect('equipo/mostrar');	
	}

	public function agregar_popup(){
		$this->load->view('equipo/agregar_popup', "");
	}

	public function guardar(){
		$estado="ok";
		$mensaje="";
		$method=isset($_GET["method"]) ? $_GET["method"] : "";
		$arr["Nombre_equipo"]=isset($_GET["Nombre_equipo"]) ? $_GET["Nombre_equipo"] : "";
		if($arr["Nombre_equipo"]!=""){
			$this->equipo->ingresar($arr);
		}else{
			$estado="error";
			$mensaje="No se pudo Ingresar";
		}
		echo $method."(".json_encode(array("estado"=>$estado, "mensaje"=>$mensaje)).")";
	}

	public function ingresar(){
		$this->ingreso->acceso_pagina();
		$this->form_validation->set_rules('nombre', 'nombre', 'trim|required|xss_clean');
		$this->form_validation->set_rules('sector', 'sector', 'required');
		$this->form_validation->set_rules('tipo', 'tipo', 'required');
		$this->form_validation->set_rules('obra', 'obra', 'required');

		if($this->form_validation->run()){
			$nombre = $this->input->post('nombre',true);
			$sector = $this->input->post('sector',true);
			$tipo = $this->input->post('tipo',true);
			$obra = $this->input->post('obra',true);
			$all_obra = $this->input->post('all_obra',true);		

			$datos_ing = array(
				'Nombre_equipo'=>$nombre);
 			$result_nombre=$this -> equipo -> verificar_nombre($datos_ing);

			if(!$result_nombre){
				$id_equipo=$this -> equipo -> ingresar($datos_ing);
			}
         	else{
				$id_equipo=$result_nombre->id_equipo;
			}

		 $datos_ing_rel=array(
			'id_equipo'=>$id_equipo,
			'id_obra'=>$obra,
			'id_tipo'=>$tipo,
			'id_sector'=>$sector);

		 $result_relacion=$this->equipo->verificar_relacion($datos_ing_rel);
			
			if(!$result_relacion){
				$this -> equipo -> ingresar_tabla_rel($datos_ing_rel);
			}

		if($all_obra=='all'){
			$tipos=$this -> obra -> mostrar_rel_tipo($obra);
			foreach($tipos as $tipo_id){
				$sector = $tipo_id->id_sector;
				$tipo = $tipo_id->id_tipo;

		 		$datos_ing_rel = array(
					'id_equipo'=>$id_equipo,
					'id_obra'=>$obra,
					'id_tipo'=>$tipo,
					'id_sector'=>$sector);

		 		$result_relacion=$this->equipo->verificar_relacion($datos_ing_rel);
			
				if(!$result_relacion){
					$this -> equipo -> ingresar_tabla_rel($datos_ing_rel);
				}
			}
		}

		$usuario=$this->session->userdata('id_login');
				if($usuario){
					$accion_user=array(
					"id_modulo"=>5,
					"id_padre"=>$id_equipo,
					"Accion_a_user"=>'agregar',
					"id_user"=>$usuario,
					"Fecha_a_user"=>date("Y-m-d H:i:s"),
					"IP"=>$_SERVER['REMOTE_ADDR']);
					$this->ingreso->agregar_accion_user($accion_user);
				}

         redirect('equipo/ingresar', 'refresh');
		}
		else
		{	$nombre=array(
				'id'=>'nombre',
				'name'=>'nombre',
				'size'=>'70',
				'value'=>set_value('nombre')
			);
			
			$sector= $this->sector->llenar_combo_sector();
			$listado_equipos= $this->equipo->listar_equipos_all();
			
			$datos = array(
				'nombre'=>$nombre,
				'sector'=>$sector,
				'equipos'=>$listado_equipos);
				
			$datos['title']='Agregar Equipo';	
			$datos['body']='equipo/agregar';
			$this->load->view('template/portalminero/completo',$datos);
		}
		
	}
	public function mostrar(){
				$this->ingreso->acceso_pagina();
				$controller=strtolower(get_class($this));
				$datos['permite_editar']=$this->ingreso->buscar_permiso_metodo($this->session->userdata('id_login'),$controller.'/editar');
				$datos['permite_borrar']=$this->ingreso->buscar_permiso_metodo($this->session->userdata('id_login'),$controller.'/borrar');
				
			$datos['equipos']=$this->equipo->mostrar();
			$datos['title']='Mostrar Equipos';	
			$datos['body']='equipo/mostrar';
			$this->load->view('template/portalminero/completo',$datos);
	}
	public function editar($id){
				$this->ingreso->acceso_pagina();
		
		$this->form_validation->set_rules('nombre', 'Nombre', 'trim|required|xss_clean');
		
		if($this->form_validation->run())
		{
			$nombre = $this->input->post('nombre',true);
			
			
			$datos_ing = array(
				'Nombre_equipo'=>$nombre);
				
			//valida si ya existe otro con el mismo nombre	
 			$result_nombre=$this -> equipo -> verificar_nombre($datos_ing,$id);
			
			if(!$result_nombre){
				$this -> equipo -> guardar_edicion($datos_ing,$id);
			}
			
			$usuario=$this->session->userdata('id_login');
				if($usuario){
						$accion_user=array(
						"id_modulo"=>5,
						"id_padre"=>$id,
						"Accion_a_user"=>'editar',
						"id_user"=>$usuario,
						"Fecha_a_user"=>date("Y-m-d H:i:s"),
						"IP"=>$_SERVER['REMOTE_ADDR']);
						$this->ingreso->agregar_accion_user($accion_user);
				}
			
         redirect('equipo/mostrar');
		}
		else
		{	
			$registro=$this->equipo->editar_proyecto($id);
			
			
			$nombre=array(
				'id'=>'nombre',
				'name'=>'nombre',
				'size'=>'100',
				'value'=>$registro->Nombre_equipo
			);
			$datos = array(
				'id_equipo'=>$registro->id_equipo,
				'nombre'=>$nombre);
			$datos['title']='Editar Equipo';	
			$datos['body']='equipo/editar';
			$this->load->view('template/portalminero/completo',$datos);
		}
	}
	/*public function borrar($id){
				$this->ingreso->acceso_pagina();
			$this->equipo->borrar_id($id);
			redirect('equipo/mostrar');
	}*/
	
	public function ver_tipos(){
		echo $data['dt_tipo']=$this->tipo->llenar_combo_tipo($this->input->post('sector_id'));
		//echo "<option value='0'>- Selecciona Tipo -</option>";
	}
	
	public function ver_obras(){
		echo $data['dt_obra']=$this->obra->llenar_combo_obra($this->input->post('tipo_id'));
		//echo "<option value='0'>- Selecciona Tipo -</option>";
	}
	
	public function mostrar_obra($id){
		
			$datos['equipos_obra']=$this->equipo->mostrar_obra($id);
			$result_nombre=$this->equipo->editar_proyecto($id);
			$datos['nombre_equipo']=$result_nombre->Nombre_equipo;
			$datos['title']='Mostrar Relación de Equipos con Obras';	
			$datos['body']='equipo/mostrar_obra';
			$this->load->view('template/portalminero/completo',$datos);
	}
	
	public function borrar_obra($id){
		
			$consult=$this->equipo->editar_rel_obra($id);
			$id_equipo=$consult->id_equipo;
			$this->equipo->borrar_rel_obra_id($id);
			redirect('equipo/mostrar_obra/'.$id_equipo);
	}
}
?>