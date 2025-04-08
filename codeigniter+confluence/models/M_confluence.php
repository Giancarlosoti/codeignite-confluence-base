<?php
class M_confluence extends CI_Model{
	function __construct(){
		$this->load->model('params','params');
		$this->load->model('licitacion/m_licitacion','licitacion');
		$this->load->model('empresa/m_empresa','empresa');
	}

	public function cargar_socio($username){
		$this->db->where("username_socio", $username);
		$query=$this->db->get("user_socio");
		$rs=$query->first_row();
		if(is_object($rs)){
			return($rs);
		}else{
			return(false);
		}
	}

	function get_via(){
		$this->db->order_by("id_via", "asc");
		$rs=$this->db->get("adjudicacion_via");
		$r=$rs->result();
		$arr=array();
		if(is_array($r) && sizeof($r)>0){
			foreach($r as $r1){
				$arr[$r1->id_via]=$r1->Nombre_via;
			}
		}
		return($arr);
	}

	public function guardar_inscripcion($datos=""){
		if(is_array($datos)){
			if(isset($datos["rut"])){
				$this->db->where("rut", $datos["rut"]);
				$query=$this->db->get("persona_inscripcion");
				$rs=$query->first_row();
				if(is_object($rs)){
					$this->db->where("rut", $datos["rut"]);
					if($this->db->update("persona_inscripcion", $datos)){
						return(true);
					}else{
						return(false);
					}

				}else{
					if($this->db->insert("persona_inscripcion", $datos)){
						return(true);
					}else{
						return(false);
					}
				}
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}

	public function crear_pago($rut, $monto){
		$orden_compra=date("YmdHis");
		$datos=array();
		$datos["orden_compra_webpay"]=$orden_compra;
		$datos["persona_inscripcion"]=$rut;
		$datos["total_webpay"]=$monto;
		if($this->db->insert("webpay_inscripcion", $datos)){
			return(array("oc"=>$orden_compra, "monto"=>$monto));
		}else{
			return(false);
		}
	}

	function get_wpdata($oc){
		$this->db->where("TBK_ORDEN_COMPRA", $oc);
		$query=$this->db->get("webpay_data");
		return($query->first_row());
	}

	function buscar_usuario_oc($oc){
		$this->db->where("wi.orden_compra_webpay", $oc);
		$this->db->join("persona_inscripcion pi", "pi.rut=wi.persona_inscripcion", "inner");
		$query=$this->db->get("webpay_inscripcion wi");
		$rs=$query->first_row();
		if(is_object($rs)){
			return($rs);
		}else{
			return(false);
		}
	}

	public function generar_js_proyectos($pro, $username){
		$js="";
		foreach($pro as $p){
			$js.=' var w'.$p->id_pagina_pro.'=$action.helper.renderConfluenceMacro(\'{wat:Id='.$p->id_pagina_pro.'}\');';
			//$js.=' var f'.$p->id_pagina_pro.'=$action.helper.renderConfluenceMacro(\'{fav:Id='.$p->id_pagina_pro.'}\');';
		}
		$js.="";
		$name=CONFLUENCE_FILES."vars_fav_".$username.".js";
		file_put_contents($name, $js);
		@chmod($name, 0777);
		return(true);
	}

	public function generar_js_proyectos_todos(){
		$js="";
		$query=$this->db->get("proyectos");
		$pro=$query->result();
		foreach($pro as $p){
			$js.=' var w'.$p->id_pagina_pro.'=$action.helper.renderConfluenceMacro(\'{wat:Id='.$p->id_pagina_pro.'}\');';
			//$js.=' var f'.$p->id_pagina_pro.'=$action.helper.renderConfluenceMacro(\'{fav:Id='.$p->id_pagina_pro.'}\');';
		}
		$js.="";
		$name=CONFLUENCE_FILES."vars_fav_pro.js";
		file_put_contents($name, $js);
		@@chmod($name, 0777);

	}

	public function listar_proyectos_ajx($username, $id_sector, $nro_pag, $search1=""){
		$resultado_busqueda="";
		if($username!="%20"){
			$rs=$this->tipo_socio($username);
		}else{
			$rs=new stdClass();
			$rs->tipo_socio=$this->params->tipo_socio[1];
		}
		if(is_object($rs)){
			$cant=$this->params->total_porpagina;
			$desde=(intval($nro_pag)-1)*intval($cant);
			$tipo_socio=$rs->tipo_socio;
			if($rs->tipo_socio==$this->params->tipo_socio[0]){
				echo "aqui1";
				if($id_sector==0){
					$this->db->or_where("(ps.id_sector=".$this->params->id_sectores["mineria"]." or ps.id_sector=".$this->params->id_sectores["energia"].")");
				}else{
					if($id_sector==$this->params->id_sectores["mineria"] || $id_sector=$this->params->id_sectores["energia"]){
						$this->db->where("ps.id_sector", $id_sector);
					}else{
						return(false);
					}
				}
				$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
				$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
				$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');

				if(isset($search1["id_pais"]) && $search1["id_pais"]!="" && $search1["id_pais"]!="0"){
					$this->db->where("pro.id_pais", $search1["id_pais"]);
				}
				if(isset($search1["id_obra"]) && $search1["id_obra"]!="" && $search1["id_obra"]!="0"){
					$this->db->where("pxo.id_obra", $search1["id_obra"]);
					$this->db->join("proyectos_x_obras pxo", "pxo.id_pro=pro.id_pro and pro.id_sector=pxo.id_sector", 'left');
					$this->db->join("obras_principales op", "op.id_obra = pxo.id_obra", 'left');
				}
				if(isset($search1["id_equipo"]) && $search1["id_equipo"]!="" && $search1["id_equipo"]!="0"){
					$this->db->where("pxe.id_equipo", $search1["id_equipo"]);
					$this->db->join("proyectos_x_equipos pxe", "pro.id_pro = pxe.id_pro", 'left');
					$this->db->join("equipos_principales epr", "pxe.id_equipo = epr.id_equipo", 'left');
				}
				if(isset($search1["id_sumin"]) && $search1["id_sumin"]!="" && $search1["id_sumin"]!="0"){
					$this->db->where("pxsum.id_sumin", $search1["id_sumin"]);
					$this->db->join("proyectos_x_suministros pxsum", "pro.id_pro = pxsum.id_pro", 'left');
					$this->db->join("suministros_principales sump", "pxsum.id_sumin = sump.id_sumin", 'left');
				}

				if(isset($search1["id_serv"]) && $search1["id_serv"]!="" && $search1["id_serv"]!="0"){
					$this->db->where("pxs.id_serv", $search1["id_serv"]);
					$this->db->join("proyectos_x_servicios pxs", "pro.id_pro = pxs.id_pro", 'left');
					$this->db->join("servicios_principales serv", "serv.id_serv = pxs.id_serv", 'left');
				}
				if(isset($search1["id_etapa"]) && $search1["id_etapa"]!="" && $search1["id_etapa"]!="0"){
					$this->db->where("pro.Etapa_actual_pro", $search1["id_etapa"]);
				}

				if(isset($search1["id_responsable"]) && $search1["id_responsable"]!="" && $search1["id_responsable"]=="0"){
					$where="(";
					$where.="emp2.id_emp=". $search1["id_responsable"];
					//$this->db->where("emp2.id_emp", $v[1]);
					if($emp=$this->empresa->get_empresa($search1["id_responsable"])){
						if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
							foreach($emp_comp as $ec){
								$where.=" or emp2.id_emp=". $ec->id_emp;
								//$this->db->or_where("emp2.id_emp", $ec->id_emp);
							}
						}
					}
					$where.=")";
					$this->db->where($where);
					$this->db->join("proyectos_x_etapas pxet", "pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", 'left');
					$this->db->join("empresas emp2", "emp2.id_emp=pxet.id_emp", 'left');
				}

				if(isset($search1["id_region"]) && $search1["id_region"]!="" && $search1["id_region"]!="0"){
					$this->db->where("pro.id_region", $search1["id_region"]);
				}

				if(isset($search1["id_tipo"]) && $search1["id_tipo"]!="" && $search1["id_tipo"]!="0"){
					$this->db->where("pxtip.id_tipo", $search1["id_tipo"]);
					$this->db->join("proyectos_x_tipo pxtip", "pro.id_pro = pxtip.id_pro", 'left');
				}

				if(isset($search1["id_mandante"]) && $search1["id_mandante"]!="" && $search1["id_mandante"]!="0"){
					$this->db->where("pro.id_man_emp", $search1["id_mandante"]);
				}

				if(isset($search1["nombre"]) && $search1["nombre"]!=""){
					$this->db->like("pro.Nombre_pro", str_replace("-_-", " ", $search1["nombre"]));
				}
				$this->db->where("pro.Borrar", "0");
				$query=$this->db->get("proyectos pro", $cant, $desde);
				$rs=$query->result();
				$total=$this->ficha->contar_proy(0, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio, $resultado_busqueda));
			}else if($rs->tipo_socio==$this->params->tipo_socio[1]){
				echo "aqui2";
				if($id_sector!=0){
					$this->db->where("ps.id_sector", $id_sector);
				}
				$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
				$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
				$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
				if(isset($search1["id_pais"]) && $search1["id_pais"]!="" && $search1["id_pais"]!="0"){
					$this->db->where("pro.id_pais", $search1["id_pais"]);
				}
				if(isset($search1["id_obra"]) && $search1["id_obra"]!="" && $search1["id_obra"]!="0"){
					$this->db->where("pxo.id_obra", $search1["id_obra"]);
					$this->db->join("proyectos_x_obras pxo", "pxo.id_pro=pro.id_pro and pro.id_sector=pxo.id_sector", 'left');
					$this->db->join("obras_principales op", "op.id_obra = pxo.id_obra", 'left');
				}
				if(isset($search1["id_equipo"]) && $search1["id_equipo"]!="" && $search1["id_equipo"]!="0"){
					$this->db->where("pxe.id_equipo", $search1["id_equipo"]);
					$this->db->join("proyectos_x_equipos pxe", "pro.id_pro = pxe.id_pro", 'left');
					$this->db->join("equipos_principales epr", "pxe.id_equipo = epr.id_equipo", 'left');
				}
				if(isset($search1["id_sumin"]) && $search1["id_sumin"]!="" && $search1["id_sumin"]!="0"){
					$this->db->where("pxsum.id_sumin", $search1["id_sumin"]);
					$this->db->join("proyectos_x_suministros pxsum", "pro.id_pro = pxsum.id_pro", 'left');
					$this->db->join("suministros_principales sump", "pxsum.id_sumin = sump.id_sumin", 'left');
				}

				if(isset($search1["id_serv"]) && $search1["id_serv"]!="" && $search1["id_serv"]!="0"){
					$this->db->where("pxs.id_serv", $search1["id_serv"]);
					$this->db->join("proyectos_x_servicios pxs", "pro.id_pro = pxs.id_pro", 'left');
					$this->db->join("servicios_principales serv", "serv.id_serv = pxs.id_serv", 'left');
				}
				if(isset($search1["id_etapa"]) && $search1["id_etapa"]!="" && $search1["id_etapa"]!="0"){
					$this->db->where("pro.Etapa_actual_pro", $search1["id_etapa"]);
				}

				if(isset($search1["id_responsable"]) && $search1["id_responsable"]!="" && $search1["id_responsable"]=="0"){
					$where="(";
					$where.="emp2.id_emp=". $search1["id_responsable"];
					//$this->db->where("emp2.id_emp", $v[1]);
					if($emp=$this->empresa->get_empresa($search1["id_responsable"])){
						if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
							foreach($emp_comp as $ec){
								$where.=" or emp2.id_emp=". $ec->id_emp;
								//$this->db->or_where("emp2.id_emp", $ec->id_emp);
							}
						}
					}
					$where.=")";
					$this->db->where($where);
					$this->db->join("proyectos_x_etapas pxet", "pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", 'left');
					$this->db->join("empresas emp2", "emp2.id_emp=pxet.id_emp", 'left');
				}

				if(isset($search1["id_region"]) && $search1["id_region"]!="" && $search1["id_region"]!="0"){
					$this->db->where("pro.id_region", $search1["id_region"]);
				}

				if(isset($search1["id_tipo"]) && $search1["id_tipo"]!="" && $search1["id_tipo"]!="0"){
					$this->db->where("pxtip.id_tipo", $search1["id_tipo"]);
					$this->db->join("proyectos_x_tipo pxtip", "pro.id_pro = pxtip.id_pro", 'left');
				}

				if(isset($search1["id_mandante"]) && $search1["id_mandante"]!="" && $search1["id_mandante"]!="0"){
					$this->db->where("pro.id_man_emp", $search1["id_mandante"]);
				}

				if(isset($search1["nombre"]) && $search1["nombre"]!=""){
					$this->db->like("pro.Nombre_pro", str_replace("-_-", " ", $search1["nombre"]));
				}
				$this->db->where("pro.Borrar", "0");
				$query=$this->db->get("proyectos pro",$cant, $desde);
				$rs=$query->result();
				$total=$this->ficha->contar_proy(1, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio));
			}else if($rs->tipo_socio==$this->params->tipo_socio[2]){
				echo "aqui3";
				$this->db->where("us.username_socio", $username);
				$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
				$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
				$query=$this->db->get("socio_x_sector sxs");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=0;
					$where="";
					foreach($rs as $r){
						if($id_sector!=0){
							if($id_sector==$r->id_sector){
								$cent=1;
								$this->db->or_where("ps.id_sector", $r->id_sector);
							}
						}else{
							$cent=1;
							if($where=="")
								$where.="( ps.id_sector=".$r->id_sector;
							else
								$where.=" or ps.id_sector=".$r->id_sector;

						}
					}
					if($where!=""){
						$where.=")";
						$this->db->where($where);
					}
					if(isset($cent)){
						if($cent==0){
							$this->db->where("ps.id_sector", 0);
						}
					}
				}else
					$this->db->where("ps.id_sector", 0);
				$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
				$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
				$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
				if(isset($search1["id_pais"]) && $search1["id_pais"]!="" && $search1["id_pais"]!="0"){
					$this->db->where("pro.id_pais", $search1["id_pais"]);
				}
				if(isset($search1["id_obra"]) && $search1["id_obra"]!="" && $search1["id_obra"]!="0"){
					$this->db->where("pxo.id_obra", $search1["id_obra"]);
					$this->db->join("proyectos_x_obras pxo", "pxo.id_pro=pro.id_pro and pro.id_sector=pxo.id_sector", 'left');
					$this->db->join("obras_principales op", "op.id_obra = pxo.id_obra", 'left');
				}
				if(isset($search1["id_equipo"]) && $search1["id_equipo"]!="" && $search1["id_equipo"]!="0"){
					$this->db->where("pxe.id_equipo", $search1["id_equipo"]);
					$this->db->join("proyectos_x_equipos pxe", "pro.id_pro = pxe.id_pro", 'left');
					$this->db->join("equipos_principales epr", "pxe.id_equipo = epr.id_equipo", 'left');
				}
				if(isset($search1["id_sumin"]) && $search1["id_sumin"]!="" && $search1["id_sumin"]!="0"){
					$this->db->where("pxsum.id_sumin", $search1["id_sumin"]);
					$this->db->join("proyectos_x_suministros pxsum", "pro.id_pro = pxsum.id_pro", 'left');
					$this->db->join("suministros_principales sump", "pxsum.id_sumin = sump.id_sumin", 'left');
				}

				if(isset($search1["id_serv"]) && $search1["id_serv"]!="" && $search1["id_serv"]!="0"){
					$this->db->where("pxs.id_serv", $search1["id_serv"]);
					$this->db->join("proyectos_x_servicios pxs", "pro.id_pro = pxs.id_pro", 'left');
					$this->db->join("servicios_principales serv", "serv.id_serv = pxs.id_serv", 'left');
				}
				if(isset($search1["id_etapa"]) && $search1["id_etapa"]!="" && $search1["id_etapa"]!="0"){
					$this->db->where("pro.Etapa_actual_pro", $search1["id_etapa"]);
				}

				if(isset($search1["id_responsable"]) && $search1["id_responsable"]!="" && $search1["id_responsable"]=="0"){
					$where="(";
					$where.="emp2.id_emp=". $search1["id_responsable"];
					//$this->db->where("emp2.id_emp", $v[1]);
					if($emp=$this->empresa->get_empresa($search1["id_responsable"])){
						if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
							foreach($emp_comp as $ec){
								$where.=" or emp2.id_emp=". $ec->id_emp;
								//$this->db->or_where("emp2.id_emp", $ec->id_emp);
							}
						}
					}
					$where.=")";
					$this->db->where($where);
					$this->db->join("proyectos_x_etapas pxet", "pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", 'left');
					$this->db->join("empresas emp2", "emp2.id_emp=pxet.id_emp", 'left');
				}

				if(isset($search1["id_region"]) && $search1["id_region"]!="" && $search1["id_region"]!="0"){
					$this->db->where("pro.id_region", $search1["id_region"]);
				}

				if(isset($search1["id_tipo"]) && $search1["id_tipo"]!="" && $search1["id_tipo"]!="0"){
					$this->db->where("pxtip.id_tipo", $search1["id_tipo"]);
					$this->db->join("proyectos_x_tipo pxtip", "pro.id_pro = pxtip.id_pro", 'left');
				}

				if(isset($search1["id_mandante"]) && $search1["id_mandante"]!="" && $search1["id_mandante"]!="0"){
					$this->db->where("pro.id_man_emp", $search1["id_mandante"]);
				}

				if(isset($search1["nombre"]) && $search1["nombre"]!=""){
					$this->db->like("pro.Nombre_pro", str_replace("-_-", " ", $search1["nombre"]));
				}
				$this->db->where("pro.Borrar", "0");
				$query=$this->db->get("proyectos pro",$cant, $desde);
				$rs=$query->result();
				$total=$this->ficha->contar_proy(2, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio));
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}

	public function listar_proyectos($username, $id_sector, $nro_pag, $search1="", $cant = null){
		//$this->output->enable_profiler(true);
		$resultado_busqueda="";
		if($username!="%20"){
			$rs=$this->tipo_socio($username);
		}else{
			$rs=new stdClass();
			$rs->tipo_socio=$this->params->tipo_socio[1];
		}
		if(is_object($rs)){
			if(is_null($cant)){
				$cant=$this->params->total_porpagina;
			}else{
				$cant = $this->params->total_porpagina_movil;
			}

			$desde=(intval($nro_pag)-1)*intval($cant);
			$tipo_socio=$rs->tipo_socio;
			if($rs->tipo_socio==$this->params->tipo_socio[0]){
				if($id_sector==0){
					$this->db->or_where("(ps.id_sector=".$this->params->id_sectores["mineria"]." or ps.id_sector=".$this->params->id_sectores["energia"].")");
				}else{
					if($id_sector==$this->params->id_sectores["mineria"] || $id_sector=$this->params->id_sectores["energia"]){
						$this->db->where("ps.id_sector", $id_sector);
					}else{
						return(false);
					}
				}
				//$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				//$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, pro.Estado_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, pro.Estado_pro, pro.Revision_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
				$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
				$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
				$this->db->where('pro.Estado_pro !=', 'N');
				$this->db->where('pro.Estado_pro !=', 'R');
				//$this->db->where('pro.Revision_pro', 'revisado');
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("pro.id_pais", $v[1]);
							}
							if($v[0]=="obra"){
								$this->db->where("pxo.id_obra", $v[1]);
								$this->db->join("proyectos_x_obras pxo", "pxo.id_pro=pro.id_pro and pro.id_sector=pxo.id_sector", 'left');
								$this->db->join("obras_principales op", "op.id_obra = pxo.id_obra", 'left');
							}
							if($v[0]=="equipo"){
								$this->db->where("pxe.id_equipo", $v[1]);
								$this->db->join("proyectos_x_equipos pxe", "pro.id_pro = pxe.id_pro", 'left');
								$this->db->join("equipos_principales epr", "pxe.id_equipo = epr.id_equipo", 'left');
							}
							if($v[0]=="suministro"){
								$this->db->where("pxsum.id_sumin", $v[1]);
								$this->db->join("proyectos_x_suministros pxsum", "pro.id_pro = pxsum.id_pro", 'left');
								$this->db->join("suministros_principales sump", "pxsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="servicio"){
								$this->db->where("pxs.id_serv", $v[1]);
								$this->db->join("proyectos_x_servicios pxs", "pro.id_pro = pxs.id_pro", 'left');
								$this->db->join("servicios_principales serv", "serv.id_serv = pxs.id_serv", 'left');
							}
							if($v[0]=="etapa"){
								$this->db->where("pro.Etapa_actual_pro", $v[1]);
							}

							if($v[0]=="responsable"){
								$where="(";
								$where.="emp2.id_emp=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or emp2.id_emp=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
								$this->db->join("proyectos_x_etapas pxet", "pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", 'left');
								$this->db->join("empresas emp2", "emp2.id_emp=pxet.id_emp", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("pro.id_region", $v[1]);
							}

							if($v[0]=="tipo"){
								$this->db->where("pxtip.id_tipo", $v[1]);
								$this->db->join("proyectos_x_tipo pxtip", "pro.id_pro = pxtip.id_pro", 'left');
							}

							if($v[0]=="mandante"){
								$this->db->where("pro.id_man_emp", $v[1]);
							}

							if($v[0]=="nombre"){
								$this->db->like("pro.Nombre_pro", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="estado"){
								if ($v[1] != 'O'){
									$this->db->where("pro.Estado_pro", $v[1]);
									$this->db->where("pro.Etapa_actual_pro <>", '8');
								}else{
									$this->db->where("pro.Etapa_actual_pro", '8');
								}
							}


							if($v[0]=="ordernombre"){
								$this->db->order_by("pro.Nombre_pro", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfecha"){
								$this->db->order_by("pro.Fecha_actualizacion_pro",$v[1]);
								$order=1;
							}

							if($v[0]=="orderinversion"){
								$this->db->order_by("pro.Inversion_pro", $v[1]);
								$order=1;
							}
							if(!isset($order)){
								$this->db->order_by("pro.id_sector", "asc");
								$this->db->order_by("pro.Nombre_pro", "asc");
							}
						}
					}
					
				}
				else{
					
				$this->db->order_by('pro.Fecha_actualizacion_pro','DESC');
				$this->db->order_by("pro.Inversion_pro", "DESC");
				}
				// if(is_array($this->db->ar_orderby) && sizeof($this->db->ar_orderby)>0){
				// 	$x=0;
				// 	foreach($this->db->ar_orderby as $o){
				// 		$this->db->ar_orderby[$x]=str_replace("`", "", $o);
				// 		++$x;
				// 	}
				// }

				$this->db->where("pro.Borrar", "0");
				$query=$this->db->get("proyectos pro", $cant, $desde);
				$rs=$query->result();
				$total=$this->ficha->contar_proy(0, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio, $resultado_busqueda));
				
			}else if($rs->tipo_socio==$this->params->tipo_socio[1]){
				if($id_sector!=0){
					$this->db->where("ps.id_sector", $id_sector);
				}
				
				//$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				//$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, pro.Estado_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, pro.Estado_pro, pro.Revision_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
				$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
				$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
				$this->db->where('pro.Estado_pro !=', 'N');
				$this->db->where('pro.Estado_pro !=', 'R');
				/*$this->db->where('pro.Revision_pro', 'revisado');*/
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("pro.id_pais", $v[1]);
							}
							if($v[0]=="obra"){
								$this->db->where("pxo.id_obra", $v[1]);
								$this->db->join("proyectos_x_obras pxo", "pxo.id_pro=pro.id_pro and pro.id_sector=pxo.id_sector", 'left');
								$this->db->join("obras_principales op", "op.id_obra = pxo.id_obra", 'left');
							}
							if($v[0]=="equipo"){
								$this->db->where("pxe.id_equipo", $v[1]);
								$this->db->join("proyectos_x_equipos pxe", "pro.id_pro = pxe.id_pro", 'left');
								$this->db->join("equipos_principales epr", "pxe.id_equipo = epr.id_equipo", 'left');
							}
							if($v[0]=="suministro"){
								$this->db->where("pxsum.id_sumin", $v[1]);
								$this->db->join("proyectos_x_suministros pxsum", "pro.id_pro = pxsum.id_pro", 'left');
								$this->db->join("suministros_principales sump", "pxsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="servicio"){
								$this->db->where("pxs.id_serv", $v[1]);
								$this->db->join("proyectos_x_servicios pxs", "pro.id_pro = pxs.id_pro", 'left');
								$this->db->join("servicios_principales serv", "serv.id_serv = pxs.id_serv", 'left');
							}
							if($v[0]=="etapa"){
								$this->db->where("pro.Etapa_actual_pro", $v[1]);
							}

							/*if($v[0]=="responsable"){
								$this->db->where("emp2.id_emp", $v[1]);
								$this->db->join("proyectos_x_etapas pxet", "pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", 'left');
								$this->db->join("empresas emp2", "emp2.id_emp=pxet.id_emp", 'left');
							}*/

							if($v[0]=="responsable"){
								$where="(";
								$where.="emp2.id_emp=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or emp2.id_emp=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
								$this->db->join("proyectos_x_etapas pxet", "pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", 'left');
								$this->db->join("empresas emp2", "emp2.id_emp=pxet.id_emp", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("pro.id_region", $v[1]);
							}

							if($v[0]=="tipo"){
								$this->db->where("pxtip.id_tipo", $v[1]);
								$this->db->join("proyectos_x_tipo pxtip", "pro.id_pro = pxtip.id_pro", 'left');
							}

							if($v[0]=="mandante"){
								$this->db->where("pro.id_man_emp", $v[1]);
							}

							if($v[0]=="nombre"){
								$this->db->like("pro.Nombre_pro", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="estado"){
								if ($v[1] != 'O'){
									$this->db->where("pro.Estado_pro", $v[1]);
									$this->db->where("pro.Etapa_actual_pro <>", '8');
								}else{
									$this->db->where("pro.Etapa_actual_pro", '8');
								}
							}


							if($v[0]=="ordernombre"){
								$this->db->order_by("pro.Nombre_pro", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfecha"){
								$this->db->order_by('pro.Fecha_actualizacion_pro',$v[1]);
								$order=1;
							}

							if($v[0]=="orderinversion"){
								$this->db->order_by('pro.Inversion_pro', $v[1]);
								$order=1;
							}
							if(!isset($order)){
								$this->db->order_by("pro.id_sector", "asc");
								$this->db->order_by("pro.Nombre_pro", "asc");
							}
						}
					}
				}
				else{
				$this->db->order_by("pro.Fecha_actualizacion_pro","DESC");
				$this->db->order_by("pro.Inversion_pro", "DESC");
				}

				// if(is_array($this->db->ar_orderby) && sizeof($this->db->ar_orderby)>0){
				// 	$x=0;
				// 	foreach($this->db->ar_orderby as $o){
				// 		$this->db->ar_orderby[$x]=str_replace("`", "", $o);
				// 		++$x;
				// 	}
				// }

				$this->db->where("pro.Borrar", "0");
				$query=$this->db->get("proyectos pro",$cant, $desde);
				$rs=$query->result();
				$total=$this->ficha->contar_proy(1, $id_sector, $search1, $username);

				return(array($rs, $total, $tipo_socio));
			}else if((($rs->tipo_socio==$this->params->tipo_socio[2])||($rs->tipo_socio==$this->params->tipo_socio[4]))){
				$this->db->where("us.username_socio", $username);
				$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
				$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
				$query=$this->db->get("socio_x_sector sxs");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=0;
					$where="";
					foreach($rs as $r){
						if($id_sector!=0){
							if($id_sector==$r->id_sector){
								$cent=1;
								$this->db->or_where("ps.id_sector", $r->id_sector);
							}
						}else{
							$cent=1;
							if($where=="")
								$where.="( ps.id_sector=".$r->id_sector;
							else
								$where.=" or ps.id_sector=".$r->id_sector;

						}
					}
					if(isset($cent)){
						if($cent==0){
							$this->db->where("ps.id_sector", 0);
						}
					}
					if($where!=''){
						$where.=")";
						$this->db->or_where($where);
					}
				}else
					$this->db->where("ps.id_sector", 0);
				//$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				//$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, pro.Estado_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->select("distinct(pro.id_pro), pro.id_sector as pid_sector, pro.Etapa_actual_pro, pro.Nombre_generico_pro, pro.id_pagina_pro, pro.Fecha_actualizacion_pro, p.Nombre_pais, r.Nombre_region, pro.url_confluence_pro, pro.Nombre_pro, c.Nombre_comuna, pro.Inversion_pro, pro.Estado_pro, pro.Revision_pro, (SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=pro.id_pro ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) ultimo_hito");
				$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
				$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
				$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
				$this->db->where('pro.Estado_pro !=', 'N');
				$this->db->where('pro.Estado_pro !=', 'R');
				/*$this->db->where('pro.Revision_pro', 'revisado');*/
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("pro.id_pais", $v[1]);
							}
							if($v[0]=="obra"){
								$this->db->where("pxo.id_obra", $v[1]);
								$this->db->join("proyectos_x_obras pxo", "pxo.id_pro=pro.id_pro and pro.id_sector=pxo.id_sector", 'left');
								$this->db->join("obras_principales op", "op.id_obra = pxo.id_obra", 'left');
							}
							if($v[0]=="equipo"){
								$this->db->where("pxe.id_equipo", $v[1]);
								$this->db->join("proyectos_x_equipos pxe", "pro.id_pro = pxe.id_pro", 'left');
								$this->db->join("equipos_principales epr", "pxe.id_equipo = epr.id_equipo", 'left');
							}
							if($v[0]=="suministro"){
								$this->db->where("pxsum.id_sumin", $v[1]);
								$this->db->join("proyectos_x_suministros pxsum", "pro.id_pro = pxsum.id_pro", 'left');
								$this->db->join("suministros_principales sump", "pxsum.id_sumin = sump.id_sumin", 'left');
							}
							if($v[0]=="servicio"){
								$this->db->where("pxs.id_serv", $v[1]);
								$this->db->join("proyectos_x_servicios pxs", "pro.id_pro = pxs.id_pro", 'left');
								$this->db->join("servicios_principales serv", "serv.id_serv = pxs.id_serv", 'left');
							}
							if($v[0]=="etapa"){
								$this->db->where("pro.Etapa_actual_pro", $v[1]);
							}
							if($v[0]=="responsable"){
								$where="(";
								$where.="emp2.id_emp=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or emp2.id_emp=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
								$this->db->join("proyectos_x_etapas pxet", "pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", 'left');
								$this->db->join("empresas emp2", "emp2.id_emp=pxet.id_emp", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("pro.id_region", $v[1]);
							}

							if($v[0]=="tipo"){
								$this->db->where("pxtip.id_tipo", $v[1]);
								$this->db->join("proyectos_x_tipo pxtip", "pro.id_pro = pxtip.id_pro", 'left');
							}

							if($v[0]=="mandante"){
								$this->db->where("pro.id_man_emp", $v[1]);
							}

							if($v[0]=="nombre"){
								$this->db->like("pro.Nombre_pro", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="estado"){
								if ($v[1] != 'O'){
									$this->db->where("pro.Estado_pro", $v[1]);
									$this->db->where("pro.Etapa_actual_pro <>", '8');
								}else{
									$this->db->where("pro.Etapa_actual_pro", '8');
								}
							}

							if($v[0]=="ordernombre"){
								$this->db->order_by("pro.Nombre_pro", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfecha"){
								$this->db->order_by('pro.Fecha_actualizacion_pro',$v[1]);
								$order=1;
							}

							if($v[0]=="orderinversion"){
								
								if($username=="claudia.fuentes"){
									
									$this->db->order_by("pro.Inversion_pro", $v[1]);
								}else{
									
									$this->db->order_by("pro.Inversion_pro", $v[1]);
								}
								
								
								
								$order=1;
							}
							/* epf 2021 */
					
							if(!isset($order)){
								$this->db->order_by("pro.id_sector", "asc");
								$this->db->order_by("pro.Nombre_pro", "asc");
							}
						}
					}
				}
				else{
					
				$this->db->order_by("pro.Fecha_actualizacion_pro","DESC");
				$this->db->order_by("pro.Inversion_pro", "DESC");
				}
				// if(is_array($this->db->ar_orderby) && sizeof($this->db->ar_orderby)>0){
				// 	$x=0;
				// 	foreach($this->db->ar_orderby as $o){
				// 		$this->db->ar_orderby[$x]=str_replace("`", "", $o);
				// 		++$x;
				// 	}
				// }
				$this->db->where("pro.Borrar", "0");
						//echo "ddddddddkikodddd";
					//exit;
					
				$query=$this->db->get("proyectos pro",$cant, $desde);
				$rs=$query->result();
				$total=$this->ficha->contar_proy(2, $id_sector, $search1, $username);

				return(array($rs, $total, $tipo_socio));
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}

	public function listar_licitaciones($username, $id_sector, $nro_pag, $search1="", $origen = null){
		//$this->output->enable_profiler(true);
		
		$resultado_busqueda="";
		if($username!="%20"){
			$rs=$this->tipo_socio($username);
		}else{
			$rs=new stdClass();
			$rs->tipo_socio='publico';
		}
		
		if(is_object($rs)){
			if(is_null($origen)){
				$cant = $this->params->total_porpagina;
			}else{
				//mobile
				$cant = $this->params->total_porpagina_movil;
			}

			$desde=(intval($nro_pag)-1)*intval($cant);
			$tipo_socio=$rs->tipo_socio;
			if($rs->tipo_socio=='publico'){
				if($id_sector!=0){
					$this->db->where("ps.id_sector", $id_sector);
				}
				$this->db->where("((lici.id_lici_tipo=".$this->params->tipo_lici["definida"]." and lici.Compra_base_lici_fin>now()) or lici.id_lici_tipo=".$this->params->tipo_lici["enproceso"].")");
				$this->db->select("distinct(lici.id_lici), p.Nombre_pais, r.Nombre_region, lici.url_confluence_lici, lici.Nombre_lici, lici.Nombre_lici_completo, ltp.Nombre_lici_tipo, lici.Compra_base_lici_fin, ps.Nombre_sector, lici.Compra_base_lici_estimada_anno, lici.Compra_base_lici_estimada_trim");
				/*$this->db->order_by("lici.id_sector", "asc");
				$this->db->order_by("lici.Nombre_lici_completo", "asc");*/
				$this->db->join("proyectos_sector ps", "ps.id_sector = lici.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = lici.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = lici.id_region", 'left');
				$this->db->join("empresas emp", "emp.id_emp = lici.id_mandante", 'left');
				$this->db->join("licitaciones_tipos ltp", "ltp.id_lici_tipo = lici.id_lici_tipo", 'left');

				$this->db->where("lici.Borrar", "0");
				$query=$this->db->get("licitaciones lici",$cant, $desde);
				$rs=$query->result();
				$total=$this->licitacion->contar_lici('publico', $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio));
			}else if($rs->tipo_socio==$this->params->tipo_socio[0]){

				if($id_sector==0){
					$this->db->or_where("(ps.id_sector=".$this->params->id_sectores["mineria"]." or ps.id_sector=".$this->params->id_sectores["energia"].")");
				}else{
					if($id_sector==$this->params->id_sectores["mineria"] || $id_sector=$this->params->id_sectores["energia"]){
						$this->db->where("ps.id_sector", $id_sector);
					}else{
						return(false);
					}
				}

				$this->db->where("((lici.id_lici_tipo=".$this->params->tipo_lici["definida"]." and lici.Compra_base_lici_fin>now()) or lici.id_lici_tipo!=".$this->params->tipo_lici["definida"].")");
				$this->db->select("distinct(lici.id_lici), p.Nombre_pais, r.Nombre_region, lici.url_confluence_lici, lici.Nombre_lici, lici.Nombre_lici_completo, ltp.Nombre_lici_tipo, lici.Compra_base_lici_fin, ps.Nombre_sector, lici.Compra_base_lici_estimada_anno, lici.Compra_base_lici_estimada_trim");
				/*$this->db->order_by("lici.Nombre_lici_completo", "asc");*/
				$this->db->join("proyectos_sector ps", "ps.id_sector = lici.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = lici.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = lici.id_region", 'left');
				$this->db->join("empresas emp", "emp.id_emp = lici.id_mandante", 'left');
				$this->db->join("licitaciones_tipos ltp", "ltp.id_lici_tipo = lici.id_lici_tipo", 'left');
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("lici.id_pais", $v[1]);
							}

							if($v[0]=="obra"){
								$this->db->where("lxo.id_obra", $v[1]);
								$this->db->join("licitaciones_x_obras lxo", "lxo.id_lici=lici.id_lici", 'left');
								$this->db->join("obras_principales op", "op.id_obra = lxo.id_obra", 'left');
							}

							if($v[0]=="equipo"){
								$this->db->where("lxe.id_equipo", $v[1]);
								$this->db->join("licitaciones_x_equipos lxe", "lici.id_lici = lxe.id_lici", 'left');
								$this->db->join("equipos_principales epr", "lxe.id_equipo = epr.id_equipo", 'left');
							}

							if($v[0]=="suministro"){
								$this->db->where("lxsum.id_sumin", $v[1]);
								$this->db->join("licitaciones_x_suministros lxsum", "lici.id_lici = lxsum.id_lici", 'left');
								$this->db->join("suministros_principales sump", "lxsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="servicio"){
								$this->db->where("lxs.id_serv", $v[1]);
								$this->db->join("licitaciones_x_servicios lxs", "lici.id_lici = lxs.id_lic", 'left');
								$this->db->join("servicios_principales serv", "serv.id_serv = lxs.id_serv", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("lici.id_region", $v[1]);
							}

							if($v[0]=="ordernombre"){
								$this->db->order_by("lici.Nombre_lici_completo", $v[1]);
								$order=1;
							}

							if($v[0]=="ordersector"){
								$this->db->order_by("ps.Nombre_sector", $v[1]);
								$order=1;
							}

							if($v[0]=="orderestado"){
								$this->db->order_by("ltp.Nombre_lici_tipo", $v[1]);
								$order=1;
							}

							if($v[0]=="orderpais"){
								$this->db->order_by("p.Nombre_pais", $v[1]);
								$order=1;
							}

							if($v[0]=="orderregion"){
								$this->db->order_by("r.Nombre_region", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfet"){
								$this->db->order_by("lici.Compra_base_lici_estimada_anno", $v[1]);
								$this->db->order_by("lici.Compra_base_lici_estimada_trim", $v[1]);
								$order=1;
							}

							if($v[0]=="orderflcb"){
								$this->db->order_by("lici.Compra_base_lici_fin", $v[1]);
								$order=1;
							}

							if($v[0]=="tipo"){
								if(!isset($order)){
									if($v[1]==$this->params->tipo_lici["estimada"]){
										$this->db->order_by("lici.Compra_base_lici_estimada_anno", "desc");
										$this->db->order_by("lici.Compra_base_lici_estimada_trim", "desc");
									}else if($v[1]==$this->params->tipo_lici["definida"])
										$this->db->order_by("lici.Compra_base_lici_fin", "asc");
									else if($v[1]==$this->params->tipo_lici["adjudicada"]){
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_anno", "desc");
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_trim", "desc");
									}else if($v[1]==$this->params->tipo_lici["enproceso"]){
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_anno", "desc");
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_trim", "desc");
									}
								}
								$this->db->where("lxtip.id_tipo", $v[1]);
								$this->db->join("licitaciones_x_tipo lxtip", "lici.id_lici = lxtip.id_lici", 'left');
							}

							if($v[0]=="mandante"){
								$where="(";
								$where.="lici.id_mandante=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or lici.id_mandante=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
							}

							if($v[0]=="nombre"){
								$this->db->like("lici.Nombre_lici_completo", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="rubro"){
								$this->db->like("lxrub.id_rubro", $v[1]);
								$this->db->join("licitaciones_x_rubros lxrub", "lici.id_lici = lxrub.id_lici", 'left');
								$this->db->join("rubros rub", "lxrub.id_rubro = rub.id_rubro", 'left');
							}

							if($v[0]=="licitipo"){
								$this->db->where("lici.id_lici_tipo", $v[1]);
							}

							if($v[0]=="regprov"){
								$this->db->like("lxrp.id_registro", $v[1]);
								$this->db->join("licitaciones_x_registro_proveedores lxrp", "lici.id_lici = lxrp.id_lici", 'left');
								$this->db->join("registro_proveedores rp", "lxrp.id_registro = rp.id_registro", 'left');
							}
						}
					}
				}
				$this->db->where("lici.Borrar", "0");
				if(!isset($order)){ $this->db->order_by("lici.Fecha_creacion_lici", "DESC"); }
				$query=$this->db->get("licitaciones lici", $cant, $desde);
				$rs=$query->result();
				$total=$this->licitacion->contar_lici(0, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio, $resultado_busqueda));
			}else if($rs->tipo_socio==$this->params->tipo_socio[1]){
				if($id_sector!=0){
					$this->db->where("ps.id_sector", $id_sector);
				}
				$this->db->where("((lici.id_lici_tipo=".$this->params->tipo_lici["definida"]." and lici.Compra_base_lici_fin>now()) or lici.id_lici_tipo!=".$this->params->tipo_lici["definida"].")");
				$this->db->select("distinct(lici.id_lici), p.Nombre_pais, r.Nombre_region, lici.url_confluence_lici, lici.Nombre_lici, lici.Nombre_lici_completo, ltp.Nombre_lici_tipo, lici.Compra_base_lici_fin, ps.Nombre_sector, lici.Compra_base_lici_estimada_anno, lici.Compra_base_lici_estimada_trim");
				/*$this->db->order_by("lici.id_sector", "asc");
				$this->db->order_by("lici.Nombre_lici_completo", "asc");*/
				$this->db->join("proyectos_sector ps", "ps.id_sector = lici.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = lici.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = lici.id_region", 'left');
				$this->db->join("empresas emp", "emp.id_emp = lici.id_mandante", 'left');
				$this->db->join("licitaciones_tipos ltp", "ltp.id_lici_tipo = lici.id_lici_tipo", 'left');
				echo ".,,,.,.";
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("lici.id_pais", $v[1]);
							}
							if($v[0]=="obra"){
								$this->db->where("lxo.id_obra", $v[1]);
								$this->db->join("licitaciones_x_obras lxo", "lxo.id_lici=lici.id_lici", 'left');
								$this->db->join("obras_principales op", "op.id_obra = lxo.id_obra", 'left');
							}
							if($v[0]=="equipo"){
								$this->db->where("lxe.id_equipo", $v[1]);
								$this->db->join("licitaciones_x_equipos lxe", "lici.id_lici = lxe.id_lici", 'left');
								$this->db->join("equipos_principales epr", "lxe.id_equipo = epr.id_equipo", 'left');
							}
							if($v[0]=="suministro"){
								$this->db->where("lxsum.id_sumin", $v[1]);
								$this->db->join("licitaciones_x_suministros lxsum", "lici.id_lici = lxsum.id_lici", 'left');
								$this->db->join("suministros_principales sump", "lxsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="servicio"){
								$this->db->where("lxs.id_serv", $v[1]);
								$this->db->join("licitaciones_x_servicios lxs", "lici.id_lici = lxs.id_lic", 'left');
								$this->db->join("servicios_principales serv", "serv.id_serv = lxs.id_serv", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("lici.id_region", $v[1]);
							}

							if($v[0]=="ordernombre"){
								$this->db->order_by("lici.Nombre_lici_completo", $v[1]);
								$order=1;
							}

							if($v[0]=="ordersector"){
								$this->db->order_by("ps.Nombre_sector", $v[1]);
								$order=1;
							}

							if($v[0]=="orderestado"){
								$this->db->order_by("ltp.Nombre_lici_tipo", $v[1]);
								$order=1;
							}

							if($v[0]=="orderpais"){
								$this->db->order_by("p.Nombre_pais", $v[1]);
								$order=1;
							}

							if($v[0]=="orderregion"){
								$this->db->order_by("r.Nombre_region", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfet"){
								$this->db->order_by("lici.Compra_base_lici_estimada_anno", $v[1]);
								$this->db->order_by("lici.Compra_base_lici_estimada_trim", $v[1]);
								$order=1;
							}

							if($v[0]=="orderflcb"){
								$this->db->order_by("lici.Compra_base_lici_fin", $v[1]);
								$order=1;
							}

							if($v[0]=="tipo"){
								if(!isset($order)){
									if($v[1]==$this->params->tipo_lici["estimada"]){
										$this->db->order_by("lici.Compra_base_lici_estimada_anno", "desc");
										$this->db->order_by("lici.Compra_base_lici_estimada_trim", "desc");
									}else if($v[1]==$this->params->tipo_lici["definida"])
										$this->db->order_by("lici.Compra_base_lici_fin", "asc");
									else if($v[1]==$this->params->tipo_lici["adjudicada"]){
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_anno", "desc");
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_trim", "desc");
									}else if($v[1]==$this->params->tipo_lici["enproceso"]){
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_anno", "desc");
										$this->db->order_by("lici.Fecha_estimada_adjudicacion_trim", "desc");
									}
								}
								$this->db->where("lxtip.id_tipo", $v[1]);
								$this->db->join("licitaciones_x_tipo lxtip", "lici.id_lici = lxtip.id_lici", 'left');
							}

							if($v[0]=="mandante"){
								$where="(";
								$where.="lici.id_mandante=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or lici.id_mandante=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
							}

							if($v[0]=="nombre"){
								$this->db->like("lici.Nombre_lici_completo", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="rubro"){
								$this->db->like("lxrub.id_rubro", $v[1]);
								$this->db->join("licitaciones_x_rubros lxrub", "lici.id_lici = lxrub.id_lici", 'left');
								$this->db->join("rubros rub", "lxrub.id_rubro = rub.id_rubro", 'left');
							}

							if($v[0]=="licitipo"){
								$this->db->where("lici.id_lici_tipo", $v[1]);
							}

							if($v[0]=="regprov"){
								$this->db->like("lxrp.id_registro", $v[1]);
								$this->db->join("licitaciones_x_registro_proveedores lxrp", "lici.id_lici = lxrp.id_lici", 'left');
								$this->db->join("registro_proveedores rp", "lxrp.id_registro = rp.id_registro", 'left');
							}

							if($v[0]=="sector"){
								$this->db->like("ps.id_sector", $v[1]);
							}
						}
					}
				}
				$this->db->where("lici.Borrar", "0");
				if(!isset($order)){ $this->db->order_by("lici.Fecha_creacion_lici", "DESC"); }
				$query=$this->db->get("licitaciones lici",$cant, $desde);
				$rs=$query->result();
				$total=$this->licitacion->contar_lici(1, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio));
			}else if(($rs->tipo_socio==$this->params->tipo_socio[2])||($rs->tipo_socio==$this->params->tipo_socio[4])){
				$this->db->where("us.username_socio", $username);
				$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
				$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
				$query=$this->db->get("socio_x_sector sxs");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=0;
					$where="";
					foreach($rs as $r){
						if($id_sector!=0){
							if($id_sector==$r->id_sector){
								$cent=1;
								$this->db->or_where("ps.id_sector", $r->id_sector);
							}
						}else{
							$cent=1;
							if($where=="")
								$where.="( ps.id_sector=".$r->id_sector;
							else
								$where.=" or ps.id_sector=".$r->id_sector;

						}
					}
					if($where!=""){
						$where.=")";
						$this->db->or_where($where);
					}
					if(isset($cent)){
						if($cent==0){
							$this->db->where("ps.id_sector", 0);
						}
					}
				}else{
					$this->db->where("ps.id_sector", 0);
				}
				$this->db->where("((lici.id_lici_tipo=".$this->params->tipo_lici["definida"]." and lici.Compra_base_lici_fin>now()) or lici.id_lici_tipo!=".$this->params->tipo_lici["definida"].")");
				$this->db->select("distinct(lici.id_lici), p.Nombre_pais, r.Nombre_region, lici.url_confluence_lici, lici.Nombre_lici, lici.Nombre_lici_completo, ltp.Nombre_lici_tipo, lici.Compra_base_lici_fin, ps.Nombre_sector, lici.Compra_base_lici_estimada_anno, lici.Compra_base_lici_estimada_trim");
				/*$this->db->order_by("lici.id_sector", "asc");
				$this->db->order_by("lici.Nombre_lici_completo", "asc");*/
				$this->db->join("proyectos_sector ps", "ps.id_sector = lici.id_sector", 'inner');
				$this->db->join("u_pais p", "p.id_pais = lici.id_pais", 'left');
				$this->db->join("u_region r", "r.id_region = lici.id_region", 'left');
				$this->db->join("empresas emp", "emp.id_emp = lici.id_mandante", 'left');
				$this->db->join("licitaciones_tipos ltp", "ltp.id_lici_tipo = lici.id_lici_tipo", 'left');
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("lici.id_pais", $v[1]);
							}

							if($v[0]=="obra"){
								$this->db->where("lxo.id_obra", $v[1]);
								$this->db->join("licitaciones_x_obras lxo", "lxo.id_lici=lici.id_lici", 'left');
								$this->db->join("obras_principales op", "op.id_obra = lxo.id_obra", 'left');
							}

							if($v[0]=="equipo"){
								$this->db->where("lxe.id_equipo", $v[1]);
								$this->db->join("licitaciones_x_equipos lxe", "lici.id_lici = lxe.id_lici", 'left');
								$this->db->join("equipos_principales epr", "lxe.id_equipo = epr.id_equipo", 'left');
							}

							if($v[0]=="suministro"){
								$this->db->where("lxsum.id_sumin", $v[1]);
								$this->db->join("licitaciones_x_suministros lxsum", "lici.id_lici = lxsum.id_lici", 'left');
								$this->db->join("suministros_principales sump", "lxsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="servicio"){
								$this->db->where("lxs.id_serv", $v[1]);
								$this->db->join("licitaciones_x_servicios lxs", "lici.id_lici = lxs.id_lic", 'left');
								$this->db->join("servicios_principales serv", "serv.id_serv = lxs.id_serv", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("lici.id_region", $v[1]);
							}

							if($v[0]=="ordernombre"){
								$this->db->order_by("lici.Nombre_lici_completo", $v[1]);
								$order=1;
							}

							if($v[0]=="ordersector"){
								$this->db->order_by("ps.Nombre_sector", $v[1]);
								$order=1;
							}

							if($v[0]=="orderestado"){
								$this->db->order_by("ltp.Nombre_lici_tipo", $v[1]);
								$order=1;
							}

							if($v[0]=="orderpais"){
								$this->db->order_by("p.Nombre_pais", $v[1]);
								$order=1;
							}

							if($v[0]=="orderregion"){
								$this->db->order_by("r.Nombre_region", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfet"){
								$this->db->order_by("lici.Compra_base_lici_estimada_anno", $v[1]);
								$this->db->order_by("lici.Compra_base_lici_estimada_trim", $v[1]);
								$order=1;
							}

							if($v[0]=="orderflcb"){
								$this->db->order_by("lici.Compra_base_lici_fin", $v[1]);
								$order=1;
							}

							if($v[0]=="tipo"){
								$this->db->where("lxtip.id_tipo", $v[1]);
								$this->db->join("licitaciones_x_tipo lxtip", "lici.id_lici = lxtip.id_lici", 'left');
							}

							if($v[0]=="mandante"){
								$where="(";
								$where.="lici.id_mandante=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or lici.id_mandante=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
							}

							if($v[0]=="nombre"){
								$this->db->like("lici.Nombre_lici_completo", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="rubro"){
								$this->db->like("lxrub.id_rubro", $v[1]);
								$this->db->join("licitaciones_x_rubros lxrub", "lici.id_lici = lxrub.id_lici", 'left');
								$this->db->join("rubros rub", "lxrub.id_rubro = rub.id_rubro", 'left');
							}

							if($v[0]=="licitipo"){
								$this->db->where("lici.id_lici_tipo", $v[1]);
							}

							if($v[0]=="reg_prov"){
								$this->db->like("lxrp.id_registro", $v[1]);
								$this->db->join("licitaciones_x_registro_proveedores lxrp", "lici.id_lici = lxrp.id_lici", 'left');
								$this->db->join("registro_proveedores rp", "lxrub.id_registro = rp.id_registro", 'left');
							}

						}
					}
				}
				$this->db->where("lici.Borrar", "0");
				if(!isset($order)){ $this->db->order_by("lici.Fecha_creacion_lici", "DESC"); }
				$query=$this->db->get("licitaciones lici",$cant, $desde);
				$rs=$query->result();
				$total=$this->licitacion->contar_lici(2, $id_sector, $search1, $username);
				
				return(array($rs, $total, $tipo_socio));
			}else{
				return(false);
			}
		}else{
			
			return(false);
		}
	}

	public function listar_adjudicaciones($username, $id_sector, $nro_pag, $search1="", $origen = null){
		//$this->output->enable_profiler(TRUE);
		$resultado_busqueda="";
		if($username!="%20"){
			$rs=$this->tipo_socio($username);
		}else{
			$rs=new stdClass();
			$rs->tipo_socio=$this->params->tipo_socio[1];
		}

		if(is_object($rs)){
			if(is_null($origen)){
				//llamada PM normal
				$cant=$this->params->total_porpagina;
			}else{
				//movil
				$cant = $this->params->total_porpagina_movil;
			}

			$desde=(intval($nro_pag)-1)*intval($cant);
			$tipo_socio=$rs->tipo_socio;
			if($rs->tipo_socio==$this->params->tipo_socio[0]){
				if($id_sector==0){
					$this->db->or_where("(ps.id_sector=".$this->params->id_sectores["mineria"]." or ps.id_sector=".$this->params->id_sectores["energia"].")");
				}else{
					if($id_sector==$this->params->id_sectores["mineria"] || $id_sector=$this->params->id_sectores["energia"]){
						$this->db->where("ps.id_sector", $id_sector);
					}else{
						return(false);
					}
				}

				$this->db->select("distinct(adj.id_adj), adj.nombre_adj, adj.url_confluence_adj, emp_adj.Nombre_fantasia_emp emp_adj, emp_comp.Nombre_fantasia_emp emp_comp, adj.trim_fecha_adj, adj.ano_fecha_adj, pro.Nombre_pro");
				$this->db->join("empresas emp_adj", "emp_adj.id_emp=adj.emp_adj", "left");
				$this->db->join("proyectos pro", "adj.id_proy_adj=pro.id_pro", "left");
				$this->db->join("licitaciones lici", "adj.id_lici_adj=lici.id_lici", "left");
				$this->db->join("empresas emp_comp", "emp_comp.id_emp=adj.emp_compra_adj", "left");
				$this->db->join("proyectos_sector ps", "ps.id_sector = adj.id_sector", 'inner');
				/*$this->db->order_by("adj.ano_fecha_adj", "desc");
				$this->db->order_by("adj.trim_fecha_adj", "desc");*/
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("adj.id_pais", $v[1]);
								$this->db->join("u_pais p", "p.id_pais=adj.id_pais", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("adj.id_region", $v[1]);
								$this->db->join("u_region r", "r.id_region=adj.id_region", 'left');
							}

							if($v[0]=="via"){
								$this->db->where("adj.id_via", $v[1]);
							}

							/*if($v[0]=="empadj"){
								$where="(";
								$where.="emp_adj.id_emp=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or emp_adj.id_emp=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
							}*/

							/*
							if($v[0]=="comprador"){
								$where="(";
								$where.="emp_comp.id_emp=". $v[1];
								//$this->db->where("emp2.id_emp", $v[1]);
								if($emp=$this->empresa->get_empresa($v[1])){
									if($emp_comp=$this->empresa->buscar_empresas_compuestas($emp->Nombre_fantasia_emp)){
										foreach($emp_comp as $ec){
											$where.=" or emp_comp.id_emp=". $ec->id_emp;
											//$this->db->or_where("emp2.id_emp", $ec->id_emp);
										}
									}
								}
								$where.=")";
								$this->db->where($where);
							}*/

						if($id_sector!=0)
							$this->db->where("adj.id_sector", $id_sector);
							if($v[0]=="empadj"){
								$emps=$this->dir_prov->buscar_empresas_uniones($v[1]);
								$where="emp_adj.id_emp in (".implode(",", $emps).")";
								$this->db->where($where);
								/*$this->db->where('axe.id_emp',$v[1]);
								$this->db->join("adjudicaciones_x_emp_adj axe", "axe.id_adj=adj.id_adj", 'INNER');*/
							}

							if($v[0]=="comprador"){
								$emps=$this->dir_prov->buscar_empresas_uniones($v[1]);
								$where="emp_comp.id_emp in (".implode(",", $emps).")";
								$this->db->where($where);
							}

							if($v[0]=="obra"){
								$this->db->where("axo.id_obra", $v[1]);
								$this->db->join("adjudicaciones_x_obras axo", "axo.id_adj=adj.id_adj", 'left');
								$this->db->join("obras_principales op", "op.id_obra = axo.id_obra", 'left');
							}

							if($v[0]=="equipo"){
								$this->db->where("axe.id_equipo", $v[1]);
								$this->db->join("adjudicaciones_x_equipos axe", "adj.id_adj = axe.id_adj", 'left');
								$this->db->join("equipos_principales epr", "axe.id_equipo = epr.id_equipo", 'left');
							}

							if($v[0]=="suministro"){
								$this->db->where("axsum.id_sumin", $v[1]);
								$this->db->join("adjudicaciones_x_suministros axsum", "adj.id_adj = axsum.id_adj", 'left');
								$this->db->join("suministros_principales sump", "axsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="catservicio"){
								$this->db->where("axs.id_cat_serv", $v[1]);
								$this->db->join("adjudicaciones_x_servcat axs", "adj.id_adj = axs.id_adj", 'left');
								$this->db->join("servicios_princ_cat serv", "serv.id_cat_serv = axs.id_cat_serv", 'left');
							}

							if($v[0]=="subcatservicio"){
								$this->db->where("axss.id_sub_serv", $v[1]);
								$this->db->join("adjudicaciones_x_servsubcat axss", "adj.id_adj = axss.id_adj", 'left');
								$this->db->join("servicios_princ_subcat servs", "servs.id_sub_serv = axss.id_sub_serv", 'left');
							}

							if($v[0]=="tipo"){
								$this->db->where("axtip.id_tipo", $v[1]);
								$this->db->join("adjudicaciones_x_tipos axtip", "adj.id_adj = axtip.id_adj", 'left');
							}

							if($v[0]=="nombre"){
								$this->db->like("adj.Descripcion_adj", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="ordernombre"){
								$this->db->order_by("adj.nombre_adj", $v[1]);
								$order=1;
							}

							if($v[0]=="orderempresa"){
								$this->db->order_by("emp_adj.Nombre_fantasia_emp", $v[1]);
								$order=1;
							}

							if($v[0]=="ordercomprador"){
								$this->db->order_by("emp_comp.Nombre_fantasia_emp", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfecha"){
								$this->db->order_by("adj.ano_fecha_adj", $v[1]);
								$this->db->order_by("adj.trim_fecha_adj", $v[1]);
								$this->db->order_by("adj.fecha_ingreso_adj", $v[1]);
								$order=1;
							}

						}
					}
				}
				$this->db->where("adj.Borrar", "0");
				if(!isset($order)){
					$this->db->order_by("adj.ano_fecha_adj", "DESC");
					$this->db->order_by("adj.trim_fecha_adj", "DESC");
					$this->db->order_by("adj.fecha_ingreso_adj", "DESC");
				}
				$query=$this->db->get("adjudicaciones adj", $cant, $desde);
				$rs=$query->result();
				$total=$total=$this->adjudicacion->contar_adj(0, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio, $resultado_busqueda));
			}else if($rs->tipo_socio==$this->params->tipo_socio[1]){
				if($id_sector!=0){
					$this->db->where("ps.id_sector", $id_sector);
				}
				$this->db->select("distinct(adj.id_adj), adj.nombre_adj, adj.url_confluence_adj, emp_adj.Nombre_fantasia_emp emp_adj, emp_comp.Nombre_fantasia_emp emp_comp, adj.trim_fecha_adj, adj.ano_fecha_adj, pro.Nombre_pro");
				$this->db->join("empresas emp_adj", "emp_adj.id_emp=adj.emp_adj", "left");
				$this->db->join("proyectos pro", "adj.id_proy_adj=pro.id_pro", "left");
				$this->db->join("licitaciones lici", "adj.id_lici_adj=lici.id_lici", "left");
				$this->db->join("empresas emp_comp", "emp_comp.id_emp=adj.emp_compra_adj", "left");
				$this->db->join("proyectos_sector ps", "ps.id_sector = adj.id_sector", 'inner');
				/*$this->db->order_by("adj.ano_fecha_adj", "desc");
				$this->db->order_by("adj.trim_fecha_adj", "desc");*/
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("adj.id_pais", $v[1]);
								$this->db->join("u_pais p", "p.id_pais=adj.id_pais", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("adj.id_region", $v[1]);
								$this->db->join("u_region r", "r.id_region=adj.id_region", 'left');
							}

							if($v[0]=="via"){
								$this->db->where("adj.id_via", $v[1]);
							}

							if($v[0]=="empadj"){
								$emps=$this->dir_prov->buscar_empresas_uniones($v[1]);
								$where="emp_adj.id_emp in (".implode(",", $emps).")";
								$this->db->where($where);
							}

							if($v[0]=="comprador"){
								$emps=$this->dir_prov->buscar_empresas_uniones($v[1]);
								$where="emp_comp.id_emp in (".implode(",", $emps).")";
								$this->db->where($where);
							}

							if($v[0]=="obra"){
								$this->db->where("axo.id_obra", $v[1]);
								$this->db->join("adjudicaciones_x_obras axo", "axo.id_adj=adj.id_adj", 'left');
								$this->db->join("obras_principales op", "op.id_obra = axo.id_obra", 'left');
							}

							if($v[0]=="equipo"){
								$this->db->where("axe.id_equipo", $v[1]);
								$this->db->join("adjudicaciones_x_equipos axe", "adj.id_adj = axe.id_adj", 'left');
								$this->db->join("equipos_principales epr", "axe.id_equipo = epr.id_equipo", 'left');
							}

							if($v[0]=="suministro"){
								$this->db->where("axsum.id_sumin", $v[1]);
								$this->db->join("adjudicaciones_x_suministros axsum", "adj.id_adj = axsum.id_adj", 'left');
								$this->db->join("suministros_principales sump", "axsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="catservicio"){
								$this->db->where("axs.id_cat_serv", $v[1]);
								$this->db->join("adjudicaciones_x_servcat axs", "adj.id_adj = axs.id_adj", 'left');
								$this->db->join("servicios_princ_cat serv", "serv.id_cat_serv = axs.id_cat_serv", 'left');
							}

							if($v[0]=="subcatservicio"){
								$this->db->where("axss.id_sub_serv", $v[1]);
								$this->db->join("adjudicaciones_x_servsubcat axss", "adj.id_adj = axss.id_adj", 'left');
								$this->db->join("servicios_princ_subcat servs", "servs.id_sub_serv = axss.id_sub_serv", 'left');
							}

							if($v[0]=="tipo"){
								$this->db->where("axtip.id_tipo", $v[1]);
								$this->db->join("adjudicaciones_x_tipos axtip", "adj.id_adj = axtip.id_adj", 'left');
							}

							if($v[0]=="nombre"){
								$this->db->like("adj.Descripcion_adj", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="ordernombre"){
								$this->db->order_by("adj.nombre_adj", $v[1]);
								$order=1;
							}

							if($v[0]=="orderempresa"){
								$this->db->order_by("emp_adj.Nombre_fantasia_emp", $v[1]);
								$order=1;
							}

							if($v[0]=="ordercomprador"){
								$this->db->order_by("emp_comp.Nombre_fantasia_emp", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfecha"){
								$this->db->order_by("adj.ano_fecha_adj", $v[1]);
								$this->db->order_by("adj.trim_fecha_adj", $v[1]);
								$this->db->order_by("adj.fecha_ingreso_adj", $v[1]);
								$order=1;
							}

						}
					}
				}
				$this->db->where("adj.Borrar", "0");
				if(!isset($order)){
					$this->db->order_by("adj.ano_fecha_adj", "DESC");
					$this->db->order_by("adj.trim_fecha_adj", "DESC");
					$this->db->order_by("adj.fecha_ingreso_adj", "DESC");
				}
				$query=$this->db->get("adjudicaciones adj", $cant, $desde);
				$rs=$query->result();
				$total=$total=$this->adjudicacion->contar_adj(1, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio));
			}else if(($rs->tipo_socio==$this->params->tipo_socio[2])||($rs->tipo_socio==$this->params->tipo_socio[4])){
				$this->db->where("us.username_socio", $username);
				$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
				$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
				$query=$this->db->get("socio_x_sector sxs");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					$cent=0;
					$where="";
					foreach($rs as $r){
						if($id_sector!=0){
							if($id_sector==$r->id_sector){
								$cent=1;
								$this->db->or_where("ps.id_sector", $r->id_sector);
							}
						}else{
							$cent=1;
							if($where=="")
								$where.="( ps.id_sector=".$r->id_sector;
							else
								$where.=" or ps.id_sector=".$r->id_sector;

						}
					}
					if($where!=""){
						$where.=")";
						$this->db->or_where($where);
					}
					if(isset($cent)){
						if($cent==0){
							$this->db->where("ps.id_sector", 0);
						}
					}
				}else{
					$this->db->where("ps.id_sector", 0);
				}
				if($id_sector!=0)
                      $this->db->where("adj.id_sector", $id_sector);


				$this->db->select("distinct(adj.id_adj), adj.nombre_adj, adj.url_confluence_adj, emp_adj.Nombre_fantasia_emp emp_adj, emp_comp.Nombre_fantasia_emp emp_comp, adj.trim_fecha_adj, adj.ano_fecha_adj, pro.Nombre_pro");
				$this->db->join("empresas emp_adj", "emp_adj.id_emp=adj.emp_adj", "left");
				$this->db->join("proyectos pro", "adj.id_proy_adj=pro.id_pro", "left");
				$this->db->join("licitaciones lici", "adj.id_lici_adj=lici.id_lici", "left");
				$this->db->join("empresas emp_comp", "emp_comp.id_emp=adj.emp_compra_adj", "left");
				$this->db->join("proyectos_sector ps", "ps.id_sector = adj.id_sector", 'inner');
				/*$this->db->order_by("adj.ano_fecha_adj", "desc");
				$this->db->order_by("adj.trim_fecha_adj", "desc");*/
				if($search1!=""){
					if($search1!="*"){
						$search=explode("-",$search1);
						$where="";
						foreach($search as $x=>$f){
							$v=explode("_", $f);
							if($v[0]=="pais"){
								$this->db->where("adj.id_pais", $v[1]);
								$this->db->join("u_pais p", "p.id_pais=adj.id_pais", 'left');
							}

							if($v[0]=="region"){
								$this->db->where("adj.id_region", $v[1]);
								$this->db->join("u_region r", "r.id_region=adj.id_region", 'left');
							}

							if($v[0]=="via"){
								$this->db->where("adj.id_via", $v[1]);
							}

							if($v[0]=="empadj"){
								$emps=$this->dir_prov->buscar_empresas_uniones($v[1]);
								$where="emp_adj.id_emp in (".implode(",", $emps).")";
								$this->db->where($where);
							}

							if($v[0]=="comprador"){
								$emps=$this->dir_prov->buscar_empresas_uniones($v[1]);
								$where="emp_comp.id_emp in (".implode(",", $emps).")";
								$this->db->where($where);
							}

							if($v[0]=="obra"){
								$this->db->where("axo.id_obra", $v[1]);
								$this->db->join("adjudicaciones_x_obras axo", "axo.id_adj=adj.id_adj", 'left');
								$this->db->join("obras_principales op", "op.id_obra = axo.id_obra", 'left');
							}

							if($v[0]=="equipo"){
								$this->db->where("axe.id_equipo", $v[1]);
								$this->db->join("adjudicaciones_x_equipos axe", "adj.id_adj = axe.id_adj", 'left');
								$this->db->join("equipos_principales epr", "axe.id_equipo = epr.id_equipo", 'left');
							}

							if($v[0]=="suministro"){
								$this->db->where("axsum.id_sumin", $v[1]);
								$this->db->join("adjudicaciones_x_suministros axsum", "adj.id_adj = axsum.id_adj", 'left');
								$this->db->join("suministros_principales sump", "axsum.id_sumin = sump.id_sumin", 'left');
							}

							if($v[0]=="catservicio"){
								$this->db->where("axs.id_cat_serv", $v[1]);
								$this->db->join("adjudicaciones_x_servcat axs", "adj.id_adj = axs.id_adj", 'left');
								$this->db->join("servicios_princ_cat serv", "serv.id_cat_serv = axs.id_cat_serv", 'left');
							}

							if($v[0]=="subcatservicio"){
								$this->db->where("axss.id_sub_serv", $v[1]);
								$this->db->join("adjudicaciones_x_servsubcat axss", "adj.id_adj = axss.id_adj", 'left');
								$this->db->join("servicios_princ_subcat servs", "servs.id_sub_serv = axss.id_sub_serv", 'left');
							}

							if($v[0]=="tipo"){
								$this->db->where("axtip.id_tipo", $v[1]);
								$this->db->join("adjudicaciones_x_tipos axtip", "adj.id_adj = axtip.id_adj", 'left');
							}

							if($v[0]=="nombre"){
								$this->db->like("adj.Descripcion_adj", str_replace("-_-", " ", $v[1]));
							}

							if($v[0]=="ordernombre"){
								$this->db->order_by("adj.nombre_adj", $v[1]);
								$order=1;
							}

							if($v[0]=="orderempresa"){
								$this->db->order_by("emp_adj.Nombre_fantasia_emp", $v[1]);
								$order=1;
							}

							if($v[0]=="ordercomprador"){
								$this->db->order_by("emp_comp.Nombre_fantasia_emp", $v[1]);
								$order=1;
							}

							if($v[0]=="orderfecha"){
								$this->db->order_by("adj.ano_fecha_adj", $v[1]);
								$this->db->order_by("adj.trim_fecha_adj", $v[1]);
								$this->db->order_by("adj.fecha_ingreso_adj", $v[1]);
								$order=1;
							}

						}
					}
				}
				$this->db->where("adj.Borrar", "0");
				if(!isset($order)){
					$this->db->order_by("adj.ano_fecha_adj", "DESC");
					$this->db->order_by("adj.trim_fecha_adj", "DESC");
					$this->db->order_by("adj.fecha_ingreso_adj", "DESC");
				}
				$query=$this->db->get("adjudicaciones adj", $cant, $desde);
				$rs=$query->result();
				$total=$total=$this->adjudicacion->contar_adj(2, $id_sector, $search1, $username);
				return(array($rs, $total, $tipo_socio));
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}

	public function generar_ficha_html($username, $id_pro, $titulo){
		if($rs=$this->tipo_socio($username)){
			$this->db->where("pro.id_pro", $id_pro);
			$query=$this->db->get("proyectos pro");
			$pro=$query->first_row();
			if(is_object($pro)){
				if(($rs->tipo_socio==$this->params->tipo_socio[0] && ($pro->id_sector=$this->params->id_sectores["mineria"] || $pro->id_sector=$this->params->id_sectores["energia"])) || $rs->tipo_socio==$this->params->tipo_socio[1]){
					$labels=$this->ficha->generar_labels($pro->id_pro);
					return($this->ficha->generar_ficha_html($pro->id_pro, $titulo));
				}else{
					return(false);
				}
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}

	public function tipo_socio($username){
		//$this->db->select("soc.tipo_socio");
		$this->db->where("us.username_socio", $username);
		$this->db->join("user_socio us", "us.id_socio=soc.id_socio", "inner");
		$query=$this->db->get("socio soc");
		$rs=$query->first_row();
		return($rs);
	}

	public function lista_rubros($id_user){
		$query = $this->db->get('rubros');
		$result=$query->result();
		$rubros_select=$this->rubros_user($id_user);
		$select='';
		$check_box='';
		foreach($query->result() as $rubro){
			if((in_array($rubro->id_rubro,$rubros_select)&&(is_array($rubros_select)))){
				$select="checked='checked'";
			}
			else{
				$select="";
			}
			$check_box.="<input type='checkbox' class='enviar_rubro' name='lista' id='rubro_".$rubro->id_rubro."' value='".$rubro->id_rubro."' ".$select."  /><label for='rubro_".$rubro->id_rubro."'>".$rubro->Nombre_rubro."</label><br>";
		}
		return $check_box;
	}

	public function rubros_select($id_user){
		$this->db->where('user_socio_rubro.id_user_socio',$id_user);
		$this->db->join('rubros','rubros.id_rubro=user_socio_rubro.id_rubro');
		$query=$this->db->get('user_socio_rubro');
		$rubros='';
		foreach($query->result() as $rubro_select){
			$rubros.='<div>'.$rubro_select->Nombre_rubro.'</div>';
		}
		return $rubros;
	}

	function verificar_rubro($id_user,$id_rubro){
		$query = $this->db->where("id_user_socio",$id_user);
		$query = $this->db->where("id_rubro",$id_rubro);
		$query = $this->db->get("user_socio_rubro");
        $cant=$query->num_rows();
		if($cant>0){
			return true;
		}
		else{
			return false;
		}
	}

	function ingresar_user_rubro($id_user,$id_rubro){
		$datos['id_user_socio']=$id_user;
		$datos['id_rubro']=$id_rubro;
		$this->db->insert('user_socio_rubro', $datos);
		return $this->db->insert_id();
	}

	function borrar_user_rubro($id_rubro,$id_user){
		$this->db->where('id_rubro',$id_rubro);
		$this->db->where('id_user_socio',$id_user);
		$this->db->delete('user_socio_rubro');
	}


	public function datos_user($username){
		//$this->db->select("soc.tipo_socio");
		$this->db->where("username_socio", $username);
		$query=$this->db->get("user_socio");
		$rs=$query->first_row();
		return($rs);
	}

	public function rubros_user($id_user){
		$query=$this->db->where('id_user_socio',$id_user);
		$query = $this->db->get('user_socio_rubro');
		$sum=0;
		$lista=array();
		foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_rubro'];
				$sum ++;
			}

		return $lista;
	}

	function oportunidades(){

		/*$fecha_mes=date('m');
		echo $fecha_mes;*/
		$query=$this->db->query("SELECT *, DATE_FORMAT(oportunidadesNegocios.Fecha_oport, '%d-%m-%Y') AS fecha  FROM oportunidadesNegocios ORDER BY Fecha_oport DESC LIMIT 0,50");
		/*$this->db->order_by('Fecha_oport','desc');
		$query=$this->db->get('oportunidadesNegocios',50);*/
		$result=$query->result();
		return $result;

	}

	function datos_vendedor($id_vendedor){
		$this->db->where('id_user',$id_vendedor);
		$query=$this->db->get('m_user');
		$result=$query->first_row();
		return $result;

	}

	function usuarios_socio($id_socio){
		$this->db->where('id_socio',$id_socio);
		$this->db->order_by('nombre_completo_socio');
		$query=$this->db->get('user_socio');
		$result=$query->result();
		return $result;

	}
	function usuario_contacto($id_socio,$id_user_socio){
		$this->db->where('id_socio',$id_socio);
		$this->db->where('contacto_admin_socio',1);
		$this->db->where('id_user_socio',$id_user_socio);
		$query=$this->db->get('user_socio');
		$result=$query->first_row();
		return $result;
	}

	function mostrar_sectores($membresia=0,$id_socio=0){
		if($membresia=='Preferencial'){
			$this->db->or_where('id_sector',1);
			$this->db->or_where('id_sector',2);
			$query=$this->db->get('proyectos_sector');
		}
		else if($membresia=='Premium'){
			$query=$this->db->get('proyectos_sector');
		}
		else if((($membresia=='Mandante')||($membresia=='Especial'))&&($id_socio!=0)){
			$this->db->where('socio_x_sector.id_socio',$id_socio);
			$this->db->join('proyectos_sector','proyectos_sector.id_sector=socio_x_sector.id_sector');
			$query=$this->db->get('socio_x_sector');
		}
		$result=$query->result();
		return $result;
	}

	public function lista_sectores($id_user,$datos_socio){
		$membresia=$datos_socio->tipo_socio;
		$this->db->where("mem.Nombre_mem",$membresia);
	    $this->db->join('membresias mem',"mem.id_mem=mxs.id_mem",'left');
	    $query=$this->db->get('membresia_x_sectores mxs');
	    $existe=$query->num_rows();
	    if($existe>0){
			$this->db->where("mem.Nombre_mem", $membresia);
			$this->db->join("proyectos_sector ps", "ps.id_sector=mxs.id_sector", "inner");
			$this->db->join("membresias mem", "mem.id_mem=mxs.id_mem", "inner");
			$query = $this->db->get('membresia_x_sectores mxs');
		}
		else{
			$this->db->where("sxs.id_socio", $datos_socio->id_socio);
			$this->db->join("proyectos_sector ps", "ps.id_sector=sxs.id_sector", "inner");
			$query = $this->db->get('socio_x_sector sxs');
		}
		$result=$query->result();
		$sectores_select=$this->sectores_user($id_user);
		$select='';
		$check_box='';
		foreach($query->result() as $sector){
			if((in_array($sector->id_sector,$sectores_select)&&(is_array($sectores_select)))){
				$select="checked='checked'";
			}
			else{
				$select="";
			}
			$check_box.="<input type='checkbox' class='enviar_sector' name='lista' id='sector_".$sector->id_sector."' value='".$sector->id_sector."' ".$select."  /><label for='sector_".$sector->id_sector."'>".$sector->Nombre_sector."</label><br>";
		}
		return $check_box;
	}


	public function sectores_user($id_user){
		$query=$this->db->where('id_user_socio',$id_user);
		$query = $this->db->get('user_socio_sector');
		$sum=0;
		$lista=array();
		foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_sector'];
				$sum ++;
			}

		return $lista;
	}


	public function sectores_select($id_user,$id_socio){
		$this->db->where('uss.id_user_socio',$id_user);
		$this->db->join('proyectos_sector ps','ps.id_sector=uss.id_sector');
		$query=$this->db->get('user_socio_sector uss');
		$sectores='';

		$array_sectores=$this->sectores_permitidos($id_socio);

		foreach($query->result() as $sector_select){
			if((!in_array($sector_select->id_sector,$array_sectores))&&(is_array($array_sectores))){
				$this->borrar_user_sector($sector_select->id_sector,$id_user);
			}
			else{
				$sectores.='<div>'.$sector_select->Nombre_sector.'</div>';
			}
		}
		return $sectores;
	}

	public function sectores_permitidos($id_socio){
		$this->db->where('id_socio',$id_socio);
		$query_socio = $this->db->get('socio');
		$result=$query_socio->first_row();
		$membresia=$result->tipo_socio;
		$this->db->where('mem.Nombre_mem',$membresia);
		$this->db->join('membresias mem','mem.id_mem=mxs.id_mem');
		$query_mxs=$this->db->get('membresia_x_sectores mxs');
	   	$existe=$query_mxs->num_rows();
	   	if($existe>0){
	   		$this->db->where("mem.Nombre_mem",$membresia);
			$this->db->join('membresias mem',"mem.id_mem=mxs.id_mem",'left');
			$query=$this->db->get('membresia_x_sectores mxs');
	   }
	   else{
		    $this->db->where("sxs.id_socio",$id_socio);
			$query=$this->db->get('socio_x_sector sxs');
	   }
		$sum=0;
		$lista=array();
		foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_sector'];
				$sum ++;
			}

		return $lista;
	}

	function verificar_sector($id_user,$id_sector){
		$query = $this->db->where("id_user_socio",$id_user);
		$query = $this->db->where("id_sector",$id_sector);
		$query = $this->db->get("user_socio_sector");
        $cant=$query->num_rows();
		if($cant>0){
			return true;
		}
		else{
			return false;
		}
	}

	function ingresar_user_sector($id_user,$id_sector){
		$datos['id_user_socio']=$id_user;
		$datos['id_sector']=$id_sector;
		$this->db->insert('user_socio_sector', $datos);
		return $this->db->insert_id();
	}

	function borrar_user_sector($id_sector,$id_user){
		$this->db->where('id_sector',$id_sector);
		$this->db->where('id_user_socio',$id_user);
		$this->db->delete('user_socio_sector');
	}

	function ingresa_visita($data){
		$data['Direccion_ip_visit']=$_SERVER['REMOTE_ADDR'];
		if(($data['PageId_visit']!=0)&&($data['User_visit']!='$req.remoteUser')){
		$this->db->insert('visitas_portal',$data);
		}
	}

	//04-10-2013
	function valida_usuario_dir_proveedores($username){
		$this->db->select('id_socio');
		$this->db->from('user_socio');
		$this->db->where('username_socio', $username);

		$rs = $this->db->get();

		if($rs->num_rows() == 0){
			return false;
		}else{
			$fila = $rs->row_array();
			$id_socio = $fila['id_socio'];

			$this->db->select('count(*) as contador', false);
			$this->db->from('emp_prov');
			$this->db->where('id_socio', $id_socio);

			$fila = $this->db->get()->row_array();


			if((int)$fila['contador'] == 0){

				return 'sin_portafolio';
			}else{

				return true;
			}
		}
	}

	function valida_sector_usuario($username){
		$array_sector = array();

		$this->db->select('tipo_socio');
		$this->db->from('socio');
		$this->db->join('user_socio', 'socio.id_socio = user_socio.id_socio', 'inner');
		$this->db->where('username_socio', $username);

		$fila = $this->db->get()->row_array();
		
		


		if($fila['tipo_socio'] === 'Preferencial'){
			//minera - energia
			$array_sector = array(1,2);

		}else if($fila['tipo_socio'] === 'Premium'){
			//todos
			$array_sector = array(1,2,3,4,5,6,7);

		}else if(($fila['tipo_socio'] === 'Mandante')||($fila['tipo_socio'] === 'Especial')){
			//by default
			$array_sector = array(1,2,3,4,5,6,7);

		}else if($fila['tipo_socio'] === 'Directorio'){
			//by default
			$array_sector = array(1,2,3,4,5,6,7);

		}


		return $array_sector;
	}

	function contar_registro_total_query($id_emp_prov, $origen, $sectores, $array_filtros){
		$sql_joins = '';
		$condiciones_sql = '';

		if($origen == 0){
			if($array_filtros['pais'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_pais = ".$array_filtros['pais'];
			}
		}else if($origen == 1){
			//arma filtros en la query principal
			if($array_filtros['tipo_proyecto'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_tipo pxt on p.id_pro = pxt.id_pro";
				$condiciones_sql = $condiciones_sql." and pxt.id_tipo = ".$array_filtros['tipo_proyecto'];
			}

			if($array_filtros['mandante'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_man_emp = ".$array_filtros['mandante'];
			}

			if($array_filtros['pais'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_pais = ".$array_filtros['pais'];
			}

			if($array_filtros['region'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_region = ".$array_filtros['region'];
			}

			if($array_filtros['obras'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_obras pxo on p.id_pro = pxo.id_pro";
				$condiciones_sql = $condiciones_sql." and pxo.id_obra = ".$array_filtros['obras'];
			}

			if($array_filtros['equipos'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_equipos pxeq on p.id_pro = pxeq.id_pro";
				$condiciones_sql = $condiciones_sql." and pxeq.id_equipo = ".$array_filtros['equipos'];
			}

			if($array_filtros['suministros'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_suministros pxsu on p.id_pro = pxsu.id_pro";
				$condiciones_sql = $condiciones_sql." and pxsu.id_tipo = ".$array_filtros['suministros'];
			}

			if($array_filtros['servicios'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_servicios pxser on p.id_pro = pxser.id_pro";
				$condiciones_sql = $condiciones_sql." and pxser.id_serv = ".$array_filtros['servicios'];
			}

			if($array_filtros['etapa'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.Etapa_actual_pro = ".$array_filtros['etapa'];
			}

			if($array_filtros['responsable'] <> 0){
				$sql_joins = $sql_joins." left join proyectos_x_etapas pxet on p.id_pro = pxet.id_pro and p.Etapa_actual_pro = pxet.id_etapa
										  left join empresas emp on emp.id_emp = pxet.id_emp";

				$condiciones_sql = $condiciones_sql." and emp.id_emp = ".$array_filtros['responsable'];
			}

			if($array_filtros['busqueda'] <> 'null'){
				$condiciones_sql = $condiciones_sql." and p.Nombre_pro like '%".$this->db->escape_like_str($array_filtros['busqueda'])."%'";

			}

		}

		//arma tabla con relacion entre socio y proyectos en base a los campos que tengan en comun
		//Equipos o Suministros o Servicios
		//luego se obtienen los id proyectos

		$sql = "select count(*) as totalizador
				from ((
						select p.id_pro as id_proyecto, p.fecha_actualizacion_pro as fecha,
						count(DISTINCT pxs.id_sumin) as contador_coincidencias, '1' as a
						from proyectos p
						inner join proyectos_x_suministros pxs ON p.id_pro = pxs.id_pro
						inner join emp_prov_x_suministros epxs ON pxs.id_sumin = epxs.id_sumin
						where id_emp_prov = $id_emp_prov
						group by p.id_pro
					)union(
						select p.id_pro as id_proyecto, p.fecha_actualizacion_pro as fecha,
						count(distinct pxe.id_equipo) as contador_coincidencias, '2' as a
						from proyectos p
						inner join proyectos_x_equipos pxe ON p.id_pro = pxe.id_pro
						inner join emp_prov_x_equipos epxe ON pxe.id_equipo = epxe.id_equipo
						where id_emp_prov = $id_emp_prov
						group by p.id_pro
					)union(
						select p.id_pro as id_proyecto,p.fecha_actualizacion_pro as fecha,
						count(distinct pxse.id_sub_serv) as contador_coincidencias, '3' as a
						from proyectos p
						inner join proyectos_x_servsubcat pxse ON p.id_pro = pxse.id_pro
						inner join emp_prov_x_servsubcat epxse ON (pxse.id_serv = epxse.id_serv
						and pxse.id_cat_serv = epxse.id_cat_serv and pxse.id_sub_serv = epxse.id_sub_serv)
						where id_emp_prov = $id_emp_prov
						group by p.id_pro)
					) as rel_prov_proy, proyectos p
				".$sql_joins."
				where p.id_pro = rel_prov_proy.id_proyecto
				and p.id_sector in (".implode(',', $sectores).")
				".$condiciones_sql."
				group by id_proyecto";

		//$fila = $this->db->_compile_select();
		//echo $fila;
		//echo $sql;

		$rs = $this->db->query($sql);

		if($rs->num_rows() == 0){
			return false;
		}else{

			return $rs->num_rows();
		}

	}

	function procesa_listado_sugeridos_completo($username="", $origen=0, $array_filtros, $pagina=0, $order_by ="", $cantidad_resultados = null){
		//origen indica origen del llamado de esta funcion = 0 -> vista 1-> ajax (con filtros)
	  
		//valida sectores de los proyectos donde se centrara la busqueda
		$sectores = $this->valida_sector_usuario($username);

		//busca id de directorio de proveedores del socio logueado
		$this->db->select('Codigo');
		$this->db->from('user_socio us');
		$this->db->join('emp_prov ep', 'us.id_socio = ep.id_socio', 'inner');
		$this->db->where('username_socio', $username);
		
		$fila = $this->db->get()->row_array();
		$id_emp_prov = (int)$fila['Codigo'];
		
		//arma paginacion de los resultados
		
		$total_registros_query = $this->contar_registro_total_query($id_emp_prov, $origen, $sectores, $array_filtros);

		//muestra nro resultados x defecto
		//09-12-2013 si viene nulo es porque no se llamo desde mobile (funciona de manera estandar)
		if(is_null($cantidad_resultados)){
			$cantidad_resultados = $this->params->total_porpagina;
		}else{
			$cantidad_resultados = $this->params->total_porpagina_movil;
		}


		$offset = ($pagina - 1) * $cantidad_resultados;
		$total_nro_paginas= ceil($total_registros_query / $cantidad_resultados);


		$sql_joins = '';
		$condiciones_sql = '';
		$orden_resultados = '';

		if($origen == 0){
			if($array_filtros['pais'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_pais = ".$array_filtros['pais'];

				//$orden_resultados = " order by field(p.id_pais, ".$array_filtros['pais'].") desc, nro_coincidencias desc, fecha desc";
			}
		}else if($origen == 1){
			//arma filtros en la query principal
			if($array_filtros['tipo_proyecto'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_tipo pxt on p.id_pro = pxt.id_pro";
				$condiciones_sql = $condiciones_sql." and pxt.id_tipo = ".$array_filtros['tipo_proyecto'];
			}

			if($array_filtros['mandante'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_man_emp = ".$array_filtros['mandante'];
			}

			if($array_filtros['pais'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_pais = ".$array_filtros['pais'];
			}

			if($array_filtros['region'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.id_region = ".$array_filtros['region'];
			}

			if($array_filtros['obras'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_obras pxo on p.id_pro = pxo.id_pro";
				$condiciones_sql = $condiciones_sql." and pxo.id_obra = ".$array_filtros['obras'];
			}

			if($array_filtros['equipos'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_equipos pxeq on p.id_pro = pxeq.id_pro";
				$condiciones_sql = $condiciones_sql." and pxeq.id_equipo = ".$array_filtros['equipos'];
			}

			if($array_filtros['suministros'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_suministros pxsu on p.id_pro = pxsu.id_pro";
				$condiciones_sql = $condiciones_sql." and pxsu.id_tipo = ".$array_filtros['suministros'];
			}

			if($array_filtros['servicios'] <> 0){
				$sql_joins = $sql_joins." inner join proyectos_x_servicios pxser on p.id_pro = pxser.id_pro";
				$condiciones_sql = $condiciones_sql." and pxser.id_serv = ".$array_filtros['servicios'];
			}

			if($array_filtros['etapa'] <> 0){
				$condiciones_sql = $condiciones_sql." and p.Etapa_actual_pro = ".$array_filtros['etapa'];
			}

			if($array_filtros['responsable'] <> 0){
				$sql_joins = $sql_joins." left join proyectos_x_etapas pxet on p.id_pro = pxet.id_pro and p.Etapa_actual_pro = pxet.id_etapa
										  left join empresas emp on emp.id_emp = pxet.id_emp";

				$condiciones_sql = $condiciones_sql." and emp.id_emp = ".$array_filtros['responsable'];
			}

			if($array_filtros['busqueda'] <> 'null'){
				$condiciones_sql = $condiciones_sql." and p.Nombre_pro like '%".$this->db->escape_like_str($array_filtros['busqueda'])."%'";

			}

		}

		//arma ordenador de resultados
		if(sizeof($order_by) > 0 & $order_by !=""){
			if($order_by['campo'] == 'default'){
				//echo $order_by['campo'];
				$orden_resultados = " order by field(p.id_pais, ".$array_filtros['pais_ordenar'].") desc, nro_coincidencias desc, fecha desc";
			}else{
				$orden_resultados = " order by ".$order_by['campo']." ".$order_by['sentido'];
			}
		}


		//arma tabla con relacion entre socio y proyectos en base a los campos que tengan en comun
		//Equipos o Suministros o Servicios
		//luego se obtienen los id proyectos


		$sql = "select id_proyecto, fecha, sum(contador_coincidencias) as nro_coincidencias
				from (((
						select p.id_pro as id_proyecto, p.Fecha_actualizacion_pro as fecha, count(DISTINCT pxs.id_sumin) as contador_coincidencias, '1' as a
						from proyectos p
						inner join proyectos_x_suministros pxs ON p.id_pro = pxs.id_pro
						inner join emp_prov_x_suministros epxs ON pxs.id_sumin = epxs.id_sumin
						where id_emp_prov = $id_emp_prov
						group by p.id_pro
					)union(
						select p.id_pro as id_proyecto, p.Fecha_actualizacion_pro as fecha, count(distinct pxe.id_equipo) as contador_coincidencias, '2' as a
						from proyectos p
						inner join proyectos_x_equipos pxe ON p.id_pro = pxe.id_pro
						inner join emp_prov_x_equipos epxe ON pxe.id_equipo = epxe.id_equipo
						where id_emp_prov = $id_emp_prov
						group by p.id_pro
					)union(
						select p.id_pro as id_proyecto,p.Fecha_actualizacion_pro as fecha, count(distinct pxse.id_sub_serv) as contador_coincidencias, '3' as a
						from proyectos p
						inner join proyectos_x_servsubcat pxse ON p.id_pro = pxse.id_pro
						inner join emp_prov_x_servsubcat epxse ON (pxse.id_serv = epxse.id_serv
						and pxse.id_cat_serv = epxse.id_cat_serv and pxse.id_sub_serv = epxse.id_sub_serv)
						where id_emp_prov = $id_emp_prov
						group by p.id_pro)
					) as rel_prov_proy, proyectos p $sql_joins , u_pais pa)
					join proyectos_sector ps on ps.id_sector = p.id_sector
				where p.id_pro = rel_prov_proy.id_proyecto
				and p.id_pais = pa.id_pais
				and p.id_sector in (".implode(',', $sectores).")
				$condiciones_sql
				group by id_proyecto
				$orden_resultados
				limit $offset, $cantidad_resultados";
		//order by nro_coincidencias desc, fecha desc
		
	
		$rs = $this->db->query($sql);

		if($rs->num_rows() == 0){
			return false;
		}else{

			$contenedor = array();
			foreach($rs->result_array() as $fila){

				$this->db->select("id_pro, Nombre_pro, url_confluence_pro,
									Nombre_generico_pro, Nombre_pais, Nombre_region, Inversion_pro, id_pagina_pro,
									proyectos.id_sector, Nombre_sector,
									(SELECT id_hito FROM  `proyectos_x_hitos`  WHERE id_pro=proyectos.id_pro
									ORDER BY ano_hito DESC , trim_hito DESC LIMIT 1) as ultimo_hito, Etapa_actual_pro,date_format(Fecha_actualizacion_pro,'%d-%m-%Y') as fecha_actualizacion", false);
				$this->db->from('proyectos');
				$this->db->join("u_region", 'proyectos.id_region = u_region.id_region', 'inner');
				$this->db->join('u_pais', 'proyectos.id_pais = u_pais.id_pais', 'inner');
				$this->db->join('proyectos_sector', 'proyectos.id_sector = proyectos_sector.id_sector', 'inner');
				$this->db->where('id_pro', $fila['id_proyecto']);
				/*$this->db->where('Estado_pro !=', 'N');
				$this->db->where('Estado_pro !=', 'R');
				$this->db->where('Revision_pro', 'revisado');*/

				$fila_int = $this->db->get()->row_array();
				$contenedor[] = array(
										'id_proyecto' 		=> $fila_int['id_pro'],
										'nombre_proyecto' 	=> $fila_int['Nombre_pro'],
										'nombre_completo'	=> $fila_int['Nombre_generico_pro'],
										'url' 				=> $fila_int['url_confluence_pro'],
										'fecha'				=> $fila_int['fecha_actualizacion'],
										'inversion'			=> $fila_int['Inversion_pro'],
										'region'			=> $fila_int['Nombre_region'],
										'pais'				=> $fila_int['Nombre_pais'],
										'id_pagina_pro'		=> $fila_int['id_pagina_pro'],
										'sector'			=> $fila_int['id_sector'],
										'nombre_sector'		=> $fila_int['Nombre_sector'],
										'ultimo_hito'		=> $fila_int['ultimo_hito'],
										'etapa_actual'		=> $fila_int['Etapa_actual_pro']
									);
			}

			$envio = array(
								'contenedor' => $contenedor,
								'paginador'	=> array(
														'pagina' 				=> $pagina,
														'total_registros_query' => $total_registros_query,
														'cantidad_resultados' 	=> $cantidad_resultados,
														'offset' 				=> $offset,
														'total_nro_paginas' 	=> $total_nro_paginas
													)
						  );
			return $envio;
		}
	}

	
	
	function busca_info_userlog($username){
		$this->db->select('socio.tipo_socio, socio.id_pais');
		$this->db->from('socio');
		$this->db->join('user_socio', 'socio.id_socio = user_socio.id_socio', 'inner');
		$this->db->where('username_socio', $username);

		//$fila = $this->db->_compile_select();
		//echo $fila;
		$rs = $this->db->get();

		if($rs->row_array() == 0){
			return array(
							'tipo_socio' => 0,
							'id_pais'	=> 0
						);
		}else{
			$fila = $rs->row_array();

			return array(
							'tipo_socio' => $fila['tipo_socio'],
							'id_pais'	=> $fila['id_pais']
						);
		}

	}

	//version que lee desde array
	public function generar_js_proyectos_version_array($pro, $username, $origen){
		$js="";
		foreach($pro as $p){
			$js.=' var w'.$p['id_pagina_pro'].'=$action.helper.renderConfluenceMacro(\'{wat:Id='.$p['id_pagina_pro'].'}\');';
		}

		$js.="";

		if($origen == 0){
			//se ejecuta mantenedor de manera normal
			$name=CONFLUENCE_FILES."vars_fav_".$username.".js";
			file_put_contents($name, $js);
			@chmod($name, 0777);
			return(true);

		}else if($origen == 1){
			//se ejecuta al usar ajax
			//return $js;

			$name=CONFLUENCE_FILES."vars_fav_".$username.".js";
			file_put_contents($name, $js, FILE_APPEND | LOCK_EX);
			@chmod($name, 0777);
			return(true);
		}
	}

	//16-10-2013 licitaciones sugeridas
	//function procesa_listado_licitaciones_sugeridas($username, $origen, $array_filtros, $pagina){
	function procesa_listado_licitaciones_sugeridas($username, $origen, $array_filtros, $pagina, $order_by, $cantidad_resultados = null){
		$sectores = $this->valida_sector_usuario($username);

		//busca id de directorio de proveedores del socio logueado
		$this->db->select('Codigo');
		$this->db->from('user_socio us');
		$this->db->join('emp_prov ep', 'us.id_socio = ep.id_socio', 'inner');
		$this->db->where('username_socio', $username);

		$fila = $this->db->get()->row_array();
		$id_emp_prov = (int)$fila['Codigo'];

		//arma paginacion de los resultados
		//$total_registros_query = $this->contar_registro_total_query_licitacion($id_emp_prov, $origen, $sectores, $array_filtros);
		//$total_registros_query = $this->get_query_licitacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, 'contador', null, null);
		$total_registros_query = $this->get_query_licitacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, 'contador', null, null, null);

		//muestra nro resultados x defecto
		if(is_null($cantidad_resultados)){
			$cantidad_resultados = $this->params->total_porpagina;
		}else{
			$cantidad_resultados = $this->params->total_porpagina_movil;
		}


		$offset = ($pagina - 1) * $cantidad_resultados;
		$total_nro_paginas= ceil($total_registros_query / $cantidad_resultados);

		//$rs = $this->db->query($sql);
		//$datos_sugeridos = $this->get_query_licitacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, 'normal', $offset, $cantidad_resultados);
		$datos_sugeridos = $this->get_query_licitacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, 'normal', $offset, $cantidad_resultados, $order_by);
		if($datos_sugeridos == false){
			return false;
		}else{

			$contenedor = array();
			foreach($datos_sugeridos as $fila){

				$this->db->select("id_lici, Nombre_lici, url_confluence_lici, Nombre_lici_completo, Nombre_pais, Nombre_region, Nombre_lici_tipo, Compra_base_lici_fin, Nombre_sector", false);
				$this->db->from('licitaciones');
				$this->db->join("u_region", 'licitaciones.id_region = u_region.id_region', 'inner');
				$this->db->join('u_pais', 'licitaciones.id_pais = u_pais.id_pais', 'inner');
				$this->db->join('licitaciones_tipos', 'licitaciones.id_lici_tipo = licitaciones_tipos.id_lici_tipo', 'inner');
				$this->db->join("proyectos_sector", "proyectos_sector.id_sector = licitaciones.id_sector", 'inner');
				$this->db->where('id_lici', $fila['id_licitacion']);

				$fila_int = $this->db->get()->row_array();

				$contenedor[] = array(
										'id_licitacion' 		=> $fila_int['id_lici'],
										'nombre_licitacion' 	=> $fila_int['Nombre_lici'],
										'nombre_completo'		=> $fila_int['Nombre_lici_completo'],
										'url' 					=> $fila_int['url_confluence_lici'],
										'region'				=> $fila_int['Nombre_region'],
										'pais'					=> $fila_int['Nombre_pais'],
										'estado_licitacion'		=> $fila_int['Nombre_lici_tipo'],
										'fecha_limite'			=> $fila_int['Compra_base_lici_fin'],
										'nombre_sector'			=> $fila_int['Nombre_sector']
									);
			}

			$envio = array(
								'contenedor' => $contenedor,
								'paginador'	=> array(
														'pagina' 				=> $pagina,
														'total_registros_query' => $total_registros_query,
														'cantidad_resultados' 	=> $cantidad_resultados,
														'offset' 				=> $offset,
														'total_nro_paginas' 	=> $total_nro_paginas
													)
						  );
			return $envio;
		}

	}

	//arma query sql
	function get_query_licitacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, $modo, $offset, $cantidad_resultados, $order_by){
		$sql_joins = '';
		$condiciones_sql = '';
		if ($sectores != null) {
			# code...
			if($origen == 0){
				if($array_filtros['pais'] <> 0){
					$condiciones_sql = $condiciones_sql." and l.id_pais = ".$array_filtros['pais'];
				}
			}else if($origen == 1){
				//arma filtros en la query principal
				if($array_filtros['sector'] <> 0){
					$condiciones_sql = $condiciones_sql." and l.id_sector = ".$array_filtros['sector'];
				}

				if($array_filtros['mandante'] <> 0){
					$condiciones_sql = $condiciones_sql." and l.id_mandante = ".$array_filtros['mandante'];
				}

				if($array_filtros['pais'] <> 0){
					$condiciones_sql = $condiciones_sql." and l.id_pais = ".$array_filtros['pais'];
				}

				if($array_filtros['region'] <> 0){
					$condiciones_sql = $condiciones_sql." and l.id_region = ".$array_filtros['region'];
				}

				if($array_filtros['reg_prov'] <> 0){
					$sql_joins = $sql_joins." inner join licitaciones_x_registro_proveedores lxrp on l.id_lici = lxrp.id_lici";
					$condiciones_sql = $condiciones_sql." and lxrp.id_registro = ".$array_filtros['reg_prov'];
				}

				if($array_filtros['tipo_lici'] <> 0){
					$condiciones_sql = $condiciones_sql." and l.id_lici_tipo = ".$array_filtros['tipo_lici'];
				}

				if($array_filtros['obra'] <> 0){
					$sql_joins = $sql_joins." inner join licitaciones_x_obras lxo on lxo.id_lici = l.id_lici";
					$condiciones_sql = $condiciones_sql." and lxo.id_obra = ".$array_filtros['obra'];
				}

				if($array_filtros['equipo'] <> 0){
					$sql_joins = $sql_joins." inner join licitaciones_x_equipos lxe on l.id_lici = lxe.id_lici";
					$condiciones_sql = $condiciones_sql." and lxe.id_equipo = ".$array_filtros['equipo'];
				}

				if($array_filtros['suministro'] <> 0){
					$sql_joins = $sql_joins." inner join licitaciones_x_suministros lxsum on l.id_lici = lxsum.id_lici";
					$condiciones_sql = $condiciones_sql." and lxsum.id_sumin = ".$array_filtros['suministro'];
				}

				if($array_filtros['servicio'] <> 0){
					$sql_joins = $sql_joins." inner join licitaciones_x_servicios lxs on l.id_lici = lxs.id_lic";
					$condiciones_sql = $condiciones_sql." and lxs.id_serv = ".$array_filtros['servicio'];
				}

				if($array_filtros['tipo'] <> 0){
					$sql_joins = $sql_joins." inner join licitaciones_x_tipo lxtip on l.id_lici = lxtip.id_lici";
					$condiciones_sql = $condiciones_sql." and lxtip.id_tipo = ".$array_filtros['tipo'];
				}

				if($array_filtros['rubro'] <> 0){
					$sql_joins = $sql_joins." inner join licitaciones_x_rubros lxrub on l.id_lici = lxrub.id_lici";
					$condiciones_sql = $condiciones_sql." and lxrub.id_rubro = ".$array_filtros['rubro'];
				}

				if($array_filtros['busqueda'] <> 'null'){
					$condiciones_sql = $condiciones_sql." and l.Nombre_lici_completo like '%".$this->db->escape_like_str($array_filtros['busqueda'])."%'";

				}
			}

			//arma select
			$enc_select = '';
			$limite = "";
			$orden_resultados = "";
			switch ($modo) {
				case 'normal':
					$enc_select = "select id_licitacion, fecha, sum(contador_coincidencias) as nro_coincidencias";

					//arma ordenador de resultados
					if(sizeof($order_by) > 0){
						if($order_by['campo'] == 'default'){
							//echo $order_by['campo'];
							$orden_resultados = " order by field(l.id_pais, ".$array_filtros['pais_ordenar'].") desc, nro_coincidencias desc, fecha desc";
						}else{
							$orden_resultados = " order by ".$order_by['campo']." ".$order_by['sentido'];
						}
					}
					$limite = "limit $offset, $cantidad_resultados";
					break;

				case 'contador':
					$enc_select = "select count(*) as totalizador";
					break;
			}



			$sql = $enc_select." from (((
										select l.id_lici as id_licitacion, l.Fecha_creacion_lici as fecha, count(DISTINCT lxs.id_sumin) as contador_coincidencias, '1' as a
										from licitaciones l
										inner join licitaciones_x_suministros lxs ON l.id_lici = lxs.id_lici
										inner join emp_prov_x_suministros epxs ON lxs.id_sumin = epxs.id_sumin
										where id_emp_prov = $id_emp_prov
										group by l.id_lici
									)union(
										select l.id_lici as id_licitacion, l.Fecha_creacion_lici as fecha, count(distinct lxe.id_equipo) as contador_coincidencias, '2' as a
										from licitaciones l
										inner join licitaciones_x_equipos lxe ON l.id_lici = lxe.id_lici
										inner join emp_prov_x_equipos epxe ON lxe.id_equipo = epxe.id_equipo
										where id_emp_prov = $id_emp_prov
										group by l.id_lici
									)union(
										select l.id_lici as id_licitacion, l.Fecha_creacion_lici as fecha, count(distinct lxse.id_sub_serv) as contador_coincidencias, '3' as a
										from licitaciones l
										inner join licitaciones_x_servsubcat lxse ON l.id_lici = lxse.id_lic
										inner join emp_prov_x_servsubcat epxse ON (lxse.id_serv = epxse.id_serv
										and lxse.id_cat_serv = epxse.id_cat_serv and lxse.id_sub_serv = epxse.id_sub_serv)
										where id_emp_prov = $id_emp_prov
										group by l.id_lici)
									) as rel_prov_lic, licitaciones l $sql_joins , u_pais pa, licitaciones_tipos lt)
									join proyectos_sector ps ON ps.id_sector = l.id_sector
								where l.id_lici = rel_prov_lic.id_licitacion
								and l.id_pais = pa.id_pais
								and l.id_lici_tipo = lt.id_lici_tipo
								and l.id_sector in (".implode(',', $sectores).")
								$condiciones_sql
								group by id_licitacion
								$orden_resultados
								$limite";

			$rs = $this->db->query($sql);

			if($rs->num_rows() == 0){
				return false;
			}else{

				switch ($modo) {
					case 'normal':
						return $rs->result_array();
						break;

					case 'contador':
						return $rs->num_rows();
						break;
				}
			}
		}
		else{
			return "No hay datos";
		}
	}

	function valida_resultado_licitaciones_sugeridas($username){
		$sectores = $this->valida_sector_usuario($username);

		//busca id de directorio de proveedores del socio logueado
		$this->db->select('Codigo');
		$this->db->from('user_socio us');
		$this->db->join('emp_prov ep', 'us.id_socio = ep.id_socio', 'inner');
		$this->db->where('username_socio', $username);

		$fila = $this->db->get()->row_array();
		$id_emp_prov = (int)$fila['Codigo'];

		$array_filtros['pais'] = 0;

		//arma paginacion de los resultados
		//$total_registros_query = $this->contar_registro_total_query_licitacion($id_emp_prov, $origen, $sectores, $array_filtros);
		$total_registros_query = $this->get_query_licitacion_sugerida($id_emp_prov, 0, $sectores, $array_filtros, 'contador', null, null, null);

		return $total_registros_query;

	}

	//21-10-2013
	//adjudicaciones sugeridos
	function procesa_listado_adjudicaciones_sugeridas($username, $origen, $array_filtros, $pagina, $order_by, $es_mobile = null){
		$sectores = $this->valida_sector_usuario($username);

		//busca id de directorio de proveedores del socio logueado
		$this->db->select('Codigo');
		$this->db->from('user_socio us');
		$this->db->join('emp_prov ep', 'us.id_socio = ep.id_socio', 'inner');
		$this->db->where('username_socio', $username);

		$fila = $this->db->get()->row_array();
		$id_emp_prov = (int)$fila['Codigo'];

		//arma paginacion de los resultados
		$total_registros_query = $this->get_query_adjudicacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, 'contador', null, null, null);

		//muestra nro resultados x defecto
		if(is_null($es_mobile)){
			$cantidad_resultados = $this->params->total_porpagina;
		}else{
			$cantidad_resultados = $this->params->total_porpagina_movil;
		}

		//$cantidad_resultados = 10;

		$offset = ($pagina - 1) * $cantidad_resultados;
		$total_nro_paginas= ceil($total_registros_query / $cantidad_resultados);

		$datos_sugeridos = $this->get_query_adjudicacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, 'normal', $offset, $cantidad_resultados, $order_by);

		if($datos_sugeridos == false){
			return false;
		}else{

			$contenedor = array();
			foreach($datos_sugeridos as $fila){

				$this->db->select("a.id_adj, a.nombre_adj, a.url_confluence_adj, ecomp.Nombre_fantasia_emp AS nombre_comprador, eadj.Nombre_fantasia_emp as nombre_adjudicado, p.Nombre_pro, a.trim_fecha_adj, a.ano_fecha_adj, Nombre_sector", false);
				$this->db->from('adjudicaciones a');
				$this->db->join("empresas eadj", "a.emp_adj = eadj.id_emp", "left");
				$this->db->join("empresas ecomp", 'a.emp_compra_adj = ecomp.id_emp', 'left');
				$this->db->join('proyectos p', 'a.id_proy_adj = p.id_pro', 'left');
				$this->db->join("proyectos_sector ps", "ps.id_sector = a.id_sector", 'inner');
				$this->db->where('a.id_adj', $fila['id_adjudicacion']);

				$fila_int = $this->db->get()->row_array();

				$contenedor[] = array(
										'id_adjudicacion' 		=> $fila_int['id_adj'],
										'nombre_adjudicacion' 	=> $fila_int['nombre_adj'],
										'nombre_comprador'		=> $fila_int['nombre_comprador'],
										'url' 					=> $fila_int['url_confluence_adj'],
										'nombre_proyecto'		=> $fila_int['Nombre_pro'],
										'fecha_adj'				=> $fila_int['trim_fecha_adj'],
										'anio_adj'				=> $fila_int['ano_fecha_adj'],
										'nombre_adjudicado'		=> $fila_int['nombre_adjudicado'],
										'nombre_sector'			=> $fila_int['Nombre_sector']
									);
			}

			$envio = array(
								'contenedor' => $contenedor,
								'paginador'	=> array(
														'pagina' 				=> $pagina,
														'total_registros_query' => $total_registros_query,
														'cantidad_resultados' 	=> $cantidad_resultados,
														'offset' 				=> $offset,
														'total_nro_paginas' 	=> $total_nro_paginas
													)
						  );
			return $envio;
		}

	}

	function get_query_adjudicacion_sugerida($id_emp_prov, $origen, $sectores, $array_filtros, $modo, $offset, $cantidad_resultados, $order_by){
		$sql_joins = '';
		$condiciones_sql = '';

		if($origen == 0){
			if($array_filtros['pais'] <> 0){
				$condiciones_sql = $condiciones_sql." and a.id_pais = ".$array_filtros['pais'];
			}
		}else if($origen == 1){
			//arma filtros en la query principal
			if($array_filtros['empadj'] <> 0){
				//$sql_joins = $sql_joins." inner join adjudicaciones_x_emp_adj axea on a.id_adj = axea.id_adj";
				//$condiciones_sql = $condiciones_sql." and axea.id_emp = ".$array_filtros['empadj'];
				$condiciones_sql .= "and a.emp_adj = ".$array_filtros['empadj'];
			}

			if($array_filtros['via'] <> 0){
				$condiciones_sql = $condiciones_sql." and a.id_via = ".$array_filtros['via'];
			}

			if($array_filtros['comprador'] <> 0){
				$condiciones_sql = $condiciones_sql." and a.emp_compra_adj = ".$array_filtros['comprador'];
			}

			if($array_filtros['equipo'] <> 0){
				$sql_joins = $sql_joins." inner join adjudicaciones_x_equipos axe on a.id_adj = axe.id_adj";
				$condiciones_sql = $condiciones_sql." and axe.id_equipo = ".$array_filtros['equipo'];
			}

			if($array_filtros['suministro'] <> 0){
				$sql_joins = $sql_joins." inner join adjudicaciones_x_suministros axsum on a.id_adj = axsum.id_adj";
				$condiciones_sql = $condiciones_sql." and axsum.id_sumin = ".$array_filtros['suministro'];
			}

			if($array_filtros['pais'] <> 0){
				$condiciones_sql = $condiciones_sql." and a.id_pais = ".$array_filtros['pais'];
			}

			if($array_filtros['region'] <> 0){
				$condiciones_sql = $condiciones_sql." and a.id_region = ".$array_filtros['region'];
			}

			if($array_filtros['catservicio'] <> 0){
				$sql_joins = $sql_joins." inner join adjudicaciones_x_servcat axserc on a.id_adj = axserc.id_adj";
				$condiciones_sql = $condiciones_sql." and axserc.id_cat_serv = ".$array_filtros['catservicio'];
			}

			if($array_filtros['subcatservicio'] <> 0){
				$sql_joins = $sql_joins." inner join adjudicaciones_x_servsubcat axsersub on a.id_adj = axsersub.id_adj";
				$condiciones_sql = $condiciones_sql." and axsersub.id_sub_serv = ".$array_filtros['subcatservicio'];
			}

			if($array_filtros['obra'] <> 0){
				$sql_joins = $sql_joins." inner join adjudicaciones_x_obras axo on a.id_adj = axo.id_adj";
				$condiciones_sql = $condiciones_sql." and axo.id_obra = ".$array_filtros['obra'];
			}

			if($array_filtros['tipo'] <> 0){
				$sql_joins = $sql_joins." inner join adjudicaciones_x_tipos axt on a.id_adj = axt.id_adj";
				$condiciones_sql = $condiciones_sql." and axt.id_tipo = ".$array_filtros['tipo'];
			}

			if($array_filtros['busqueda'] <> 'null'){
				$condiciones_sql = $condiciones_sql." and a.Descripcion_adj like '%".$this->db->escape_like_str($array_filtros['busqueda'])."%'";

			}
		}

		//arma select
		$enc_select = '';
		$limite = "";
		$orden_resultados = "";
		switch ($modo) {
			case 'normal':
				$enc_select = "select id_adjudicacion, fecha, sum(contador_coincidencias) as nro_coincidencias";

				//arma ordenador de resultados
				if(sizeof($order_by) > 0){
					if($order_by['campo'] == 'default'){
						//echo $order_by['campo'];
						$orden_resultados = " order by field(a.id_pais, ".$array_filtros['pais_ordenar'].") desc, nro_coincidencias desc, fecha desc";
					}else{
						$orden_resultados = " order by ".$order_by['campo']." ".$order_by['sentido'];
					}
				}
				$limite = "limit $offset, $cantidad_resultados";
				break;

			case 'contador':
				$enc_select = "select count(*) as totalizador";
				break;
		}



		$sql = $enc_select." from (((
									select a.id_adj as id_adjudicacion, a.fecha_ingreso_adj as fecha, count(distinct axs.id_sumin) as contador_coincidencias, '1' as a
									from adjudicaciones a
									inner join adjudicaciones_x_suministros axs ON a.id_adj = axs.id_adj
									inner join emp_prov_x_suministros epxs ON axs.id_sumin = epxs.id_sumin
									where id_emp_prov = $id_emp_prov
									group by a.id_adj
								)union(
									select a.id_adj as id_adjudicacion, a.fecha_ingreso_adj as fecha, count(distinct axe.id_equipo) as contador_coincidencias, '2' as a
									from adjudicaciones a
									inner join adjudicaciones_x_equipos axe ON a.id_adj = axe.id_adj
									inner join emp_prov_x_equipos epxe ON axe.id_equipo = epxe.id_equipo
									where id_emp_prov = $id_emp_prov
									group by a.id_adj
								)union(
									select a.id_adj as id_adjudicacion, a.fecha_ingreso_adj as fecha, count(distinct axse.id_serv) as contador_coincidencias, '3' as a
									from adjudicaciones a
									inner join adjudicaciones_x_servsubcat axse ON a.id_adj = axse.id_adj
									inner join emp_prov_x_servsubcat epxse ON (axse.id_serv = epxse.id_serv
									and axse.id_cat_serv = epxse.id_cat_serv and axse.id_sub_serv = epxse.id_sub_serv)
									where id_emp_prov = $id_emp_prov
									group by a.id_adj)
								) as rel_prov_adj, adjudicaciones a $sql_joins , u_pais pa, empresas eadj, empresas ecomp)
								join proyectos_sector ps ON ps.id_sector = a.id_sector
							where a.id_adj = rel_prov_adj.id_adjudicacion
							and a.id_pais = pa.id_pais
							and a.emp_adj = eadj.id_emp
							and a.emp_compra_adj = ecomp.id_emp
							and a.id_sector in (".implode(',', $sectores).")
							$condiciones_sql
							group by id_adjudicacion
							$orden_resultados
							$limite";

		$rs = $this->db->query($sql);

		if($rs->num_rows() == 0){
			return false;
		}else{

			switch ($modo) {
				case 'normal':
					return $rs->result_array();
					break;

				case 'contador':
					return $rs->num_rows();
					break;
			}
		}

	}

	//24-10-2013 valida nro resultados
	function valida_resultado_adjudicaciones_sugeridas($username){
		$sectores = $this->valida_sector_usuario($username);

		//busca id de directorio de proveedores del socio logueado
		$this->db->select('Codigo');
		$this->db->from('user_socio us');
		$this->db->join('emp_prov ep', 'us.id_socio = ep.id_socio', 'inner');
		$this->db->where('username_socio', $username);

		$fila = $this->db->get()->row_array();
		$id_emp_prov = (int)$fila['Codigo'];

		$array_filtros['pais'] = 0;

		//arma paginacion de los resultados
		$total_registros_query = $this->get_query_adjudicacion_sugerida($id_emp_prov, 0, $sectores, $array_filtros, 'contador', null, null, null);

		return $total_registros_query;

	}
	function listado_boletines_premium(){
		$this->db->order_by('id_bol','DESC');
		$query=$this->db->get('socio_boletin_premium');
		if($query->num_rows()>0){
			return $query->result();
		}
		else
			return false;
	}

	function listado_boletines_preferencial(){
		$this->db->order_by('id_bol','DESC');
		$query=$this->db->get('socio_boletin_preferencial');
		if($query->num_rows()>0){
			return $query->result();
		}
		else
			return false;
	}
}
?>