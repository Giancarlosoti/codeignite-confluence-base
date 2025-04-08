<?php
class M_equipo extends CI_Model
{
	function __construct(){
		
		parent::__construct();
		$this->load->model("adjudicacion/m_adjudicacion", "adjudicacion");
		$this->load->model("licitacion/m_licitacion", "licitacion");
	}
	   
   function verificar_nombre($datos,$id=0){
		$query = $this->db->where($datos);
		$query = $this->db->get("equipos_principales");
        $cant=$query->num_rows();
		$result = $query->first_row();
		$id2=$this->db->result_id;
		if($id==$id2){
			$cant=0;
		}
		if($cant==0){
			$result=0;
		}
		
		return $result;
   	
   	
   }
   function listar_equipos_all(){
	$this->db->order_by("Nombre_equipo","asc");
   	$query=$this->db->get('equipos_principales');
	$result = $query->result();
	$lista=array();
	if($query->num_rows()>0){
			
			$lista['0']= "- Revisar Equipos -";
			foreach($query->result_array() as $resultado_obras){
				$lista[$resultado_obras['Nombre_equipo']]= $resultado_obras['Nombre_equipo'];
				
			}
			return $lista;
		
		}
	return $result;
   	
   }
   
   function listar_equipos_all_cbo(){
	$this->db->order_by("Nombre_equipo","asc");
   	$query=$this->db->get('equipos_principales');
	$result = $query->result();
	$lista=array();
	if($query->num_rows()>0){
			
			$lista['']= "- Equipos -";
			foreach($query->result_array() as $resultado_obras){
				$lista[$resultado_obras['id_equipo']]= $resultado_obras['Nombre_equipo'];
				
			}
			return $lista;
		
		}
	return $result;
   	
   }
   
   function ingresar($datos){
		$this->db->insert('equipos_principales', $datos);
		return $this->db->insert_id();   	
   	
   }
   
   function verificar_relacion($datos){  
		$query = $this->db->where($datos);
		$query = $this->db->get("obras_x_equipos");
        $cant=$query->num_rows();
		
		return $cant;
   	
   	
   }
   function ingresar_tabla_rel($datos){
   
   	$this->db->insert('obras_x_equipos', $datos);
   	
   }
   function mostrar(){
	$this->db->order_by("Nombre_equipo", "asc");
   	$query=$this->db->get('equipos_principales');
	$result = $query->result();
	return $result;
   	
   }
   function editar_proyecto($id){
	$this->db->order_by("Nombre_equipo", "asc");
   	$this->db->where('id_equipo', $id);
   	$query=$this->db->get('equipos_principales');
	$result = $query->first_row();
	return $result;
   	
   }	
   function guardar_edicion($datos,$id){
   	$this->db->where('id_equipo', $id);
   	$this->db->update('equipos_principales', $datos);
   	
   }
  /* function borrar_id($id){
   	$this->db->where('id_obra', $id);
   	$this->db->delete('obras_principales');
   	
   }*/
   
	function mostrar_obra($id){
   
		$this->db->where('obras_x_equipos.id_equipo', $id);
		$query=$this->db->order_by('proyectos_sector.id_sector','asc');
		$query=$this->db->order_by('proyectos_tipo.id_tipo','asc');
		$query=$this->db->order_by('obras_principales.id_obra','asc');
		$query=$this->db->order_by("obras_principales.Nombre_obra","asc");
		$query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = obras_x_equipos.id_sector");
		$query = $this->db->join("proyectos_tipo", "proyectos_tipo.id_tipo = obras_x_equipos.id_tipo");
		$query = $this->db->join("obras_principales", "obras_principales.id_obra = obras_x_equipos.id_obra");
		$query = $this->db->get("obras_x_equipos");
		$result = $query->result();
		return $result;

	}
   
   function borrar_rel_obra_id($id){
   	$this->db->where('id_obra_x_equipo', $id);
   	$this->db->delete('obras_x_equipos');
   	
   }
   
   function editar_rel_obra($id){
   
   	$query=$this->db->where('id_obra_x_equipo', $id);
	$query=$this->db->get('obras_x_equipos');
	$result = $query->first_row();
	return $result;
   	
   }
   function llenar_checkbox_equipo($datos){
   	
		$this->db->where($datos);
		$checkbox=NULL;
		$this->db->order_by("equipos_principales.Nombre_equipo","asc");
		$query = $this->db->join("equipos_principales", "equipos_principales.id_equipo = obras_x_equipos.id_equipo");
		$query = $this->db->get("obras_x_equipos");
		if($query->num_rows()>0){
			foreach($query->result() as $resultado){					
				$checkbox.="<span style='float:left; padding-left:20px;'><input type='checkbox' name='equipo[]' id='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' value='".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' /><label for='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."'>".$resultado->Nombre_equipo."</label></span>";
			}
			
		}
		return $checkbox;
	}
		
	function llenar_checkbox_equipo_edit($datos,$array_select){
   	
			$this->db->where($datos);
			$checkbox=NULL;
			$this->db->order_by("equipos_principales.Nombre_equipo", "asc");
			$query = $this->db->join("equipos_principales", "equipos_principales.id_equipo = obras_x_equipos.id_equipo");
			$query = $this->db->get("obras_x_equipos");
 			if($query->num_rows()>0){
 				foreach($query->result() as $resultado){
					if(in_array($resultado->id_equipo,$array_select)){
						$select="checked='checked'";
					}
					else{$select=NULL;}
					
										
 					$checkbox.="<span style='float:left; padding-left:20px;'><input type='checkbox' name='equipo[]' id='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' value='".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' ".$select." /><label for='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."'>".$resultado->Nombre_equipo."</label></span>";
 				}
				
 			}
		return $checkbox;
		}
 		
 	function equipo_id_pro($datos,$id){
		$query=$this->db->where($datos);
		$query=$this->db->where('id_pro',$id);
		$sum=0;
		$query=$this->db->get('proyectos_x_equipos');
		//$lista[""]='- Empresa -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_equipo'];
				$sum ++;
			}
					return $lista;
		
		}
	}	
	
	
	function llenar_checkbox_equipo_adj($datos, $id){
		$eq_adj=$this->adjudicacion->cargar_equipos_adj($id);
		$this->db->where($datos);
		$checkbox=NULL;
		$this->db->order_by("equipos_principales.Nombre_equipo","asc");
		$query = $this->db->join("equipos_principales", "equipos_principales.id_equipo = obras_x_equipos.id_equipo");
		$query = $this->db->get("obras_x_equipos");
		if($query->num_rows()>0){
			foreach($query->result() as $resultado){	
				if(in_array($resultado->id_equipo, $eq_adj)){
					$checked="checked='checked'";
				}else{
					$checked="";
				}
				$checkbox.="<span style='float:left; padding-left:20px;'><input type='checkbox' name='equipo[]' ".$checked." id='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' value='".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' /><label for='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."'>".$resultado->Nombre_equipo."</label></span>";
			}
			
		}
		return $checkbox;
	}
	
	function llenar_checkbox_equipo_pro($datos, $id){
		$eq_pro=$this->proyecto->cargar_equipos_pro($id, $datos['id_obra'], $datos['id_tipo']);
		$this->db->where($datos);
		$checkbox=NULL;
		$this->db->order_by("equipos_principales.Nombre_equipo","asc");
		$query = $this->db->join("equipos_principales", "equipos_principales.id_equipo = obras_x_equipos.id_equipo");
		$query = $this->db->get("obras_x_equipos");
		if($query->num_rows()>0){
			foreach($query->result() as $resultado){	
				if(in_array($resultado->id_equipo, $eq_pro)){
					$checked="checked='checked'";
				}else{
					$checked="";
				}
				$checkbox.="<span style='float:left; padding-left:20px;'><input type='checkbox' name='equipo[]' ".$checked." id='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' value='".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' /><label for='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."'>".$resultado->Nombre_equipo."</label></span>";
			}
			
		}
		return $checkbox;
	}
	
	function llenar_checkbox_equipo_lic($datos, $id){
		$eq_adj=$this->licitacion->cargar_equipos_lic($id);
		$this->db->where($datos);
		$checkbox=NULL;
		$this->db->order_by("equipos_principales.Nombre_equipo","asc");
		$query = $this->db->join("equipos_principales", "equipos_principales.id_equipo = obras_x_equipos.id_equipo");
		$query = $this->db->get("obras_x_equipos");
		if($query->num_rows()>0){
			foreach($query->result() as $resultado){	
				if(in_array($resultado->id_equipo, $eq_adj)){
					$checked="checked='checked'";
				}else{
					$checked="";
				}
				$checkbox.="<span style='float:left; padding-left:20px;'><input type='checkbox' name='equipo[]' ".$checked." id='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' value='".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."' /><label for='Equipo_".$resultado->id_tipo."_".$resultado->id_obra."_".$resultado->id_equipo."'>".$resultado->Nombre_equipo."</label></span>";
			}
			
		}
		return $checkbox;
	}

} 
?>