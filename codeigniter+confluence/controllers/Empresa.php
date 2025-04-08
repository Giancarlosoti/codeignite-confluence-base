<?php
class Empresa extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this -> load -> model ('empresa/m_empresa','empresa');
		$this->load->model('m_pais','pais');
		$this->load->model('m_region','region');
		$this->load->model('m_comuna','comuna');
		$this->load->model('ingreso/m_ingreso','ingreso');
		$this->load->helper('security');
		//validar login
		//$this->ingreso->log_in();
	}

	function buscar_empresas_compuestas(){
		echo "<pre>";
		var_dump($this->empresa->buscar_empresas_compuestas(" "));
		echo "</pre>";
	}

	function no_rel(){
		$data=array(2240, 2429, 1719, 1720, 2615, 1903, 1901, 1558, 1559, 1525, 1526, 991, 958, 1248, 2460, 2535, 1133, 2427, 2339, 2754, 8, 165, 2007, 2080, 2041, 2326, 2092, 2093, 2021, 2722, 2306, 2354, 2720, 2126, 2127, 929, 2425, 922, 952, 1893, 1199, 2314, 874, 300, 254, 997, 2026, 1553, 1546, 15, 612, 1093, 318, 2275, 1924, 912, 1102, 1635, 1501, 1515, 2057, 1029, 1217, 2055, 409, 659, 2056);
		foreach($data as $id_emp){
			$cent=0;
			$this->db->where("id_emp", $id_emp);
			$query=$this->db->get("empresas");
			$emp=$query->first_row();
			if(is_object($emp)){
				echo "<strong>( ".$emp->id_emp." ) ".utf8_decode($emp->Razon_social_emp)."</strong><br>";
				$this->db->where("id_man_emp", $id_emp);
				$query=$this->db->get("proyectos");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=1;
					echo "SE RELACIONA CON PROYECTOS.<br>";
				}else{
					echo "NO SE RELACIONA CON PROYECTOS.<br>";
				}
				$this->db->where("id_emp", $id_emp);
				$query=$this->db->get("proyectos_x_etapas");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=1;
					echo "SE RELACIONA CON ETAPAS.<br>";
				}else{
					echo "NO SE RELACIONA CON ETAPAS.<br>";
				}
				$this->db->where("id_emp", $id_emp);
				$query=$this->db->get("proyectos_x_empresas");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=1;
					echo "SE RELACIONA CON PROPIETARIOS.<br>";
				}else{
					echo "NO SE RELACIONA CON PROPIETARIOS.<br>";
				}
				$this->db->where("id_mandante", $id_emp);
				$query=$this->db->get("licitaciones");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=1;
					echo "SE RELACIONA CON LICITACIONES.<br>";
				}else{
					echo "NO SE RELACIONA CON LICITACIONES.<br>";
				}
				$this->db->or_where("emp_adj", $id_emp);
				$this->db->or_where("emp_compra_adj", $id_emp);
				$query=$this->db->get("adjudicaciones");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=1;
					echo "SE RELACIONA CON ADJUDICACIONES.<br>";
				}else{
					echo "NO SE RELACIONA CON ADJUDICACIONES.<br>";
				}
				if($cent==0){
					echo "SE BORRARÁ LA EMPRESA DE ID \"".$id_emp."\"<br>";
					$this->db->where("id_emp", $id_emp);
					$this->db->delete("empresas");
				}
				echo "<br>";
			}
		}
	}
	
	function norm_empresas($secun, $princ){
		$datos=array();
		$datos=array("id_man_emp"=>$princ);
		$this->db->where("id_man_emp", $secun); 
		if($this->db->update("proyectos", $datos)){
			$datos=array();
			$datos=array("id_emp"=>$princ);
			$this->db->where("id_emp", $secun); 
			if($this->db->update("proyectos_x_etapas", $datos)){
				$datos=array();
				$datos=array("emp_adj"=>$princ);
				$this->db->where("emp_adj", $secun); 
				if($this->db->update("adjudicaciones", $datos)){
					$datos=array();
					$datos=array("emp_compra_adj"=>$princ);
					$this->db->where("emp_compra_adj", $secun); 
					if($this->db->update("adjudicaciones", $datos)){
						$datos=array();
						$datos=array("id_mandante"=>$princ);
						$this->db->where("id_mandante", $secun); 
						if($this->db->update("licitaciones", $datos)){
							$this->db->where("id_emp", $secun);
							if($this->db->delete("empresas")){
								echo "OK<br>";
							}else{
								echo "error<br>";
							}
						}else{
							echo "error<br>";
						}
					}else{
						echo "error<br>";
					}
				}else{
					echo "error<br>";
				}
			}else{
				echo "error<br>";
			}
		}else{
			echo "error<br>";
		}
	}

	public function buscar_nombre_compuesto(){
		$this->db->like("Razon_social_emp", utf8_encode("UNIÓN"));
		$this->db->or_like("Razon_social_emp", utf8_encode("UNION"));
		$this->db->or_like("Razon_social_emp", utf8_encode("union"));
		$this->db->or_like("Razon_social_emp", utf8_encode("unión"));
		//$query=$this->db->get("empresas", 1, 0);
		$query=$this->db->get("empresas");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $res){
				$this->empresa->buscar_nombre_compuesto($res->Razon_social_emp);
			}
		}else{
			echo "CODIFICAR UNIÓN";
		}
	}
	
	public function ingresar($popup=""){
		$this->ingreso->acceso_pagina();
		$this->form_validation->set_rules('fantasia', 'fantasia', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'email', 'trim|valid_email');
		
		//$this->form_validation->set_message('required','* Requerido');
		
		if($this->form_validation->run())
		{
			return(0);
			$razon = $this->input->post('razon',true);
			$fantasia = $this->input->post('fantasia',true);
			$direccion = $this->input->post('direccion',true);
			$region = $this->input->post('region',true);
			$pais = $this->input->post('pais',true);
			$comuna = $this->input->post('comuna',true);
			$telefono = $this->input->post('telefono',true);
			$rut = $this->input->post('rut',true);
			$email = $this->input->post('email',true);
			
			$tipo_empresa=$this->input->post('tipo_empresa', true);

			//usuario
			$usuario=$this->session->userdata('id_login');
			
			$datos_ing = array(
				'Razon_social_emp'=>$razon,
				'Nombre_fantasia_emp'=>$fantasia,
				'Direccion_emp'=>$direccion,
				'id_region'=>$region,
				'id_comuna'=>$comuna,
				'id_pais'=>$pais,
				'Rut_emp'=>$rut,
				'Email_emp'=>$email,
				'Telefono_emp'=>$telefono,
				'tipo_empresa'=>$tipo_empresa,
				'User_creador'=>$usuario
			);
			
         $this -> empresa -> ingresar($datos_ing);
		 
         redirect('empresa/ingresar', 'refresh');
		}else{	
			$razon=array(
				'id'=>'razon',
				'name'=>'razon',
				'size'=>'100',
				'value'=>set_value('razon'),
				'class'=>"required"
			);
			$fantasia=array(
				'id'=>'fantasia',
				'name'=>'fantasia',
				'size'=>'100',
				'value'=>set_value('fantasia'),
				'class'=>"required"
			);
			$direccion=array(
				'id'=>'direccion',
				'name'=>'direccion',
				'size'=>'80',
				'value'=>set_value('direccion'),
				'class'=>"required"
			);

			$telefono=array(
				'id'=>'telefono',
				'name'=>'telefono',
				'value'=>set_value('telefono'),
				'class'=>"required"
			);

			$rut=array(
				'id'=>'rut',
				'name'=>'rut',
				'value'=>set_value('rut'),
				'class'=>""
			);

			$email=array(
				'id'=>'email',
				'name'=>'email',
				'size'=>'50',
				'value'=>set_value('email')
			);

			$pais= $this->pais->llenar_combo_pais();

			$tipos_emp=$this->empresa->carga_tipo_empresa();

			$tipo_empresa=array(
				"name"=>"tipo_empresa[]",
				"options"=>$tipos_emp,
				"select"=>"",
				"others"=>"id='tipo_empresa' style='width:50%' size='5' multiple class='required'"
			);

			$datos = array(
				'razon'=>$razon,
				'fantasia'=>$fantasia,
				'direccion'=>$direccion,
				'pais'=>$pais,
				'telefono'=>$telefono,
				'rut'=>$rut,
				'email'=>$email,
				'tipo_empresa'=>$tipo_empresa
			);

			if($popup!=""){
				$datos["popup"]=$popup;
				$this->load->view('empresa/agregar',$datos);
			}else{
				$datos['title']='Ingresar Empresa';	
				$datos['body']='empresa/agregar';
				$this->load->view('template/portalminero/completo',$datos);
			}
		}
	}

	public function ingresar_popup(){
		$estado="ok";
		$mensaje="";
		$razon = $this->input->post('razon',true);
		$fantasia = $this->input->post('fantasia',true);
		$direccion = $this->input->post('direccion',true);
		$region = $this->input->post('region',true);
		$pais = $this->input->post('pais',true);
		$comuna = $this->input->post('comuna',true);
		$telefono = $this->input->post('telefono',true);
		$rut = $this->input->post('rut',true);
		$email = $this->input->post('email',true);

		$tipo_empresa=$this->input->post('tipo_empresa', true);

		//usuario
			$usuario=$this->session->userdata('id_login');

		$datos_ing = array(
			'Razon_social_emp'=>$razon,
			'Nombre_fantasia_emp'=>$fantasia,
			'Direccion_emp'=>$direccion,
			'id_region'=>$region,
			'id_comuna'=>$comuna,
			'id_pais'=>$pais,
			'Rut_emp'=>$rut,
			'Email_emp'=>$email,
			'Telefono_emp'=>$telefono,
			'tipo_empresa'=>$tipo_empresa,
			'User_creador'=>$usuario
		);
		$id_emp="";
		$a=$this->empresa->busca_por_nombre_razon($razon);
		if(is_object($a)){
			$estado="error";
			$mensaje="Ya existe la empresa";
		}else{
			$id_emp=$this->empresa->ingresar($datos_ing);
		}
		echo json_encode(array("estado"=>$estado, "mensaje"=>$mensaje,"id_emp"=>$id_emp));
	}

	public function mostrar($param_onden='id',$tipo_orden='asc'){
		$this->ingreso->acceso_pagina();
		$controller=strtolower(get_class($this));
		$datos['permite_editar']=$this->ingreso->buscar_permiso_metodo($this->session->userdata('id_login'),$controller.'/editar');
		$datos['permite_borrar']=$this->ingreso->buscar_permiso_metodo($this->session->userdata('id_login'),$controller.'/borrar');
		if($param_onden=='id')$param_onden='id_emp';
		if($param_onden=='razon')$param_onden='Razon_social_emp';
		if($param_onden=='nombre')$param_onden='Nombre_fantasia_emp	';
		if($param_onden=='rut')$param_onden='Rut_emp';
		if($param_onden=='direccion')$param_onden='Direccion_emp';
		if($param_onden=='pais')$param_onden='Nombre_pais';
		if($param_onden=='region')$param_onden='Nombre_region';
		if($param_onden=='comuna')$param_onden='Nombre_comuna';
		//if(empty($tipo_onden))$tipo_onden='desc';
	
		$datos['empresas']=$this->empresa->mostrar($param_onden,$tipo_orden);
		$datos['new_orden']= ($tipo_orden == 'asc' ? 'desc' : 'asc');
		$datos['title']='Mostrar Empresas';	
		$datos['body']='empresa/mostrar';
		$this->load->view('template/portalminero/completo',$datos);
	}

	public function editar($id){
		$this->ingreso->acceso_pagina();
		$this->form_validation->set_rules('fantasia', 'fantasia', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'email', 'trim|valid_email');

		if($this->form_validation->run()){
			$razon = $this->input->post('razon',true);
			$fantasia = $this->input->post('fantasia',true);
			$direccion = $this->input->post('direccion',true);
			$region = $this->input->post('region',true);
			$pais = $this->input->post('pais',true);
			$comuna = $this->input->post('comuna',true);
			$telefono = $this->input->post('telefono',true);
			$rut = $this->input->post('rut',true);
			$email = $this->input->post('email',true);
			$tipo_empresa=$this->input->post('tipo_empresa', true);
			$datos_ing = array(
				'Razon_social_emp'=>$razon,
				'Nombre_fantasia_emp'=>$fantasia,
				'Direccion_emp'=>$direccion,
				'id_region'=>$region,
				'id_comuna'=>$comuna,
				'id_pais'=>$pais,
				'Rut_emp'=>$rut,
				'Email_emp'=>$email,
				'Telefono_emp'=>$telefono,
				'tipo_empresa'=>$tipo_empresa);
			$this -> empresa -> guardar_edicion($datos_ing,$id);

			$usuario=$this->session->userdata('id_login');
			if($usuario){
				$accion_user=array(
				"id_modulo"=>10,
				"id_padre"=>$id,
				"Accion_a_user"=>'editar',
				"id_user"=>$usuario,
				"Fecha_a_user"=>date("Y-m-d H:i:s"),
				"IP"=>$_SERVER['REMOTE_ADDR']);
				$this->ingreso->agregar_accion_user($accion_user);
			}
			redirect('empresa/mostrar');
		}else{	
			$registro=$this->empresa->editar_proyecto($id);

			$razon=array(
				'id'=>'razon',
				'name'=>'razon',
				'size'=>'100',
				'value'=>$registro->Razon_social_emp
			);

			$fantasia=array(
				'id'=>'fantasia',
				'name'=>'fantasia',
				'size'=>'100',
				'value'=>$registro->Nombre_fantasia_emp
			);

			$direccion=array(
				'id'=>'direccion',
				'name'=>'direccion',
				'size'=>'80',
				'value'=>$registro->Direccion_emp
			);

			$telefono=array(
				'id'=>'telefono',
				'name'=>'telefono',
				'value'=>$registro->Telefono_emp
			);

			$rut=array(
				'id'=>'rut',
				'name'=>'rut',
				'value'=>$registro->Rut_emp
			);

			$email=array(
				'id'=>'email',
				'name'=>'email',
				'size'=>'50',
				'value'=>$registro->Email_emp
			);

			if(($registro->id_pais!=0)&&($registro->id_pais!='')){
				$pais= $this->pais->llenar_combo_pais($registro->id_pais);
				$arreglo_pais=$pais[1];
				$id_select_pais=$pais[0];
			}

			else{
				$pais= $this->pais->llenar_combo_pais();
				$arreglo_pais=$pais;
				$id_select_pais=0;
			}

			if(($registro->id_region!=0)&&($registro->id_region!='')){
				$region= $this->region->llenar_combo_region_id($registro->id_region,$registro->id_pais);
				$arreglo_region=$region[1];
				$id_select_region=$region[0];
			}else{
				$arreglo_region[]='';
				$id_select_region=0;
			}

			if(($registro->id_region!=0)&&($registro->id_region!='')){
				$comuna= $this->comuna->llenar_combo_comuna_id($registro->id_comuna,$registro->id_region,$registro->id_pais);
				$arreglo_comuna=$comuna[1];
				$id_select_comuna=$comuna[0];
			}else{
				$lista[]='--';
				$arreglo_comuna[]='';
				$id_select_comuna=0;
			}
			$tipos_emp=$this->empresa->carga_tipo_empresa();
			$tipos_emp2=$this->empresa->cargar_tipo_empresa_sel($registro->id_emp);
			$tipos_emp_sel=$tipos_emp2;
			$tipo_empresa=array(
				"name"=>"tipo_empresa[]",
				"options"=>$tipos_emp,
				"select"=>$tipos_emp_sel,
				"others"=>"id='tipo_empresa' style='width:50%' size='5' multiple"
			);

			$datos = array(
				'id'=>$registro->id_emp,
				'razon'=>$razon,
				'fantasia'=>$fantasia,
				'direccion'=>$direccion,
				'pais'=>$arreglo_pais,
				'id_pais'=>$id_select_pais,
				'region'=>$arreglo_region,
				'id_region'=>$id_select_region,
				'comuna'=>$arreglo_comuna,
				'id_comuna'=>$id_select_comuna,
				'rut'=>$rut,
				'email'=>$email,
				'telefono'=>$telefono,
				'tipo_empresa'=>$tipo_empresa
				);

			$datos['title']='Editar Empresa';	
			$datos['body']='empresa/editar';
			$this->load->view('template/portalminero/completo',$datos);
		}
	}

	public function borrar($id){
			$this->ingreso->acceso_pagina();

			$this->empresa->borrar_id($id);

			$usuario=$this->session->userdata('id_login');
			if($usuario){
				$accion_user=array(
				"id_modulo"=>10,
				"id_padre"=>$id,
				"Accion_a_user"=>'borrar',
				"id_user"=>$usuario,
				"Fecha_a_user"=>date("Y-m-d H:i:s"),
				"IP"=>$_SERVER['REMOTE_ADDR']);
				$this->ingreso->agregar_accion_user($accion_user);
			}
			redirect('empresa/mostrar');
	}

	public function ver_regiones(){
		echo $data['dt_region']=$this->region->llenar_combo_region($this->input->post('pais_id'));
	}

	public function ver_comunas(){
		echo $data['dt_comuna']=$this->comuna->llenar_combo_comuna($this->input->post('region_id'));
	}

	public function cargar_empresa(){
		$data=$this->empresa->llenar_combo_empresa();
		$msg="";
		$estado="ok";
		$mensaje="";
		if(is_array($data) && sizeof($data)>0){
			//$msg.="<option value=''>- Empresa -</option>";
			foreach($data as $key=>$val){
				$msg.="<option value='".$key."'>".$val."</option>";
			}
		}
		echo json_encode(array("estado"=>$estado, "mensaje"=>$mensaje, "datos"=>$msg));
	}
	
	function autocomplete_empresa(){
		$q = strtolower($_GET["term"]);
		$data=$this->empresa->autocomplete_empresa($q);
		echo json_encode($data);
	}
	
	
	function buscar_empresa_fantasia(){
		$empresa=$_POST['pushemp'];
		if($this->empresa->busca_por_nombre($empresa))
			$resp=true;
		else
			$resp=false;
		$data['resp']=$resp;
		echo $resp;
		
	}
}
?>