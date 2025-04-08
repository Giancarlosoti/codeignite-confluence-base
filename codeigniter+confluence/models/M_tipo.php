<?php
class M_tipo extends CI_Model
{
	private $table = 'proyectos_tipo';
	function __construct(){
		parent::__construct();
		$this->load->model("adjudicacion/m_adjudicacion", "adjudicacion");
		$this->load->model("licitacion/m_licitacion", "licitacion");
	}
	   
   function ingresar($datos){
   
   	$this->db->insert($this->table, $datos);
	return $this->db->insert_id();
   	
   }
   function mostrar(){
    $query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos_tipo.id_sector");
   	$query=$this->db->get($this->table);
	$result = $query->result();
	return $result;
   	
   }
   function editar_proyecto($id){
    $query=$this->db->where('id_tipo',$id);
   	$query=$this->db->get($this->table);
	$result = $query->first_row();
	return $result;
   	
   }	
   function guardar_edicion($datos,$id){
   	$this->db->where('id_tipo', $id);
   	$this->db->update($this->table, $datos);
   	
   }
   function borrar_id($id){
   	$this->db->where('id_tipo', $id);
   	$this->db->delete($this->table);
   	
   }	
	function llenar_combo_tipo($sector){
		$query=$this->db->order_by('Nombre_tipo','asc');
 		$query=$this->db->where('id_sector',$sector);
 		$lista_tipo=$this->db->get($this->table);
		$combo_tipo="";
 		if($lista_tipo->num_rows()>0){
 			$combo_tipo.="<option value=''>- Tipo -</option>";
 			foreach($lista_tipo->result() as $resultado_tipo){
 				$combo_tipo.="<option value='".$resultado_tipo->id_tipo."'>".$resultado_tipo->Nombre_tipo."</option>";
 			}
 		}
		else{
 			$combo_tipo="no hay Resultados";
 		}
 		return $combo_tipo;
 	}
	
	function llenar_combo_tipo_edit($sector=""){
		if($sector!=""){
			$this->db->where('id_sector',$sector);
		}
 		$lista_tipo=$this->db->get("proyectos_tipo");
		$combo_tipo['']="- Tipo -";
 		if($lista_tipo->num_rows()>0){
			foreach($lista_tipo->result_array() as $resultado){
				$combo_tipo[$resultado['id_tipo']]= $resultado['Nombre_tipo'];
			}
			return $combo_tipo;
		}
	}
	
	function tipo_id_pro($id_pro){
		$query=$this->db->where('id_pro',$id_pro);
		$sum=0;
		$query = $this->db->get('proyectos_x_tipo');
		//$lista[""]='- Empresa -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_tipo'];
				$sum ++;
			}
					return $lista;
			
		
		}
	}	
	
	function llenar_add_check_box_tipo($tipo){
 		$query=$this->db->where('id_tipo',$tipo);
 		$lista_tipo=$this->db->get($this->table);
		$resultado = $lista_tipo->first_row();
 		if($lista_tipo->num_rows()>0){
 			$checkbox="<input type='checkbox' name='add_tipo' class='add_tipo' id='tipo_".$resultado->id_tipo."' value='".$resultado->id_tipo."' checked='checked'  disabled='disabled' class='required' /><input type='text' name='name_tipo[".$resultado->id_tipo."]' id='".$resultado->id_tipo."' value='".$resultado->Nombre_tipo."' disabled='disabled' size='40' /><br>";
 		}
		else{
 			$checkbox="no hay Resultados";
 		}
 		return $checkbox;
 	}
	
	function llenar_combo_tipo_adj($sector, $id=""){
		if($id!="")
			$tipos_adj=$this->adjudicacion->cargar_tipos_adj($id);
		$query=$this->db->order_by('Nombre_tipo','asc');
 		$query=$this->db->where('id_sector',$sector);
 		$lista_tipo=$this->db->get($this->table);
		$combo_tipo="";
 		if($lista_tipo->num_rows()>0){
 			$combo_tipo.="<option value=''>- Tipo -</option>";
 			foreach($lista_tipo->result() as $resultado_tipo){
				if($id!= ""&& in_array($resultado_tipo->id_tipo, $tipos_adj)){
					$selected="selected='selected'";
				}else{
					$selected="";
				}
 				$combo_tipo.="<option ".$selected." value='".$resultado_tipo->id_tipo."'>".$resultado_tipo->Nombre_tipo."</option>";
 			}
 		}
		else{
 			$combo_tipo="no hay Resultados";
 		}
 		return $combo_tipo;
 	}
	
	function llenar_combo_tipo_lic($sector, $id){
		$tipos_lic=$this->licitacion->cargar_tipos_lic($id);
		$query=$this->db->order_by('Nombre_tipo','asc');
 		$query=$this->db->where('id_sector',$sector);
 		$lista_tipo=$this->db->get($this->table);
		$combo_tipo="";
 		if($lista_tipo->num_rows()>0){
 			$combo_tipo.="<option value=''>- Tipo -</option>";
 			foreach($lista_tipo->result() as $resultado_tipo){
				if(in_array($resultado_tipo->id_tipo, $tipos_lic)){
					$selected="selected='selected'";
				}else{
					$selected="";
				}
 				$combo_tipo.="<option ".$selected." value='".$resultado_tipo->id_tipo."'>".$resultado_tipo->Nombre_tipo."</option>";
 			}
 		}else{
 			$combo_tipo="no hay Resultados";
 		}
 		return $combo_tipo;
 	}

	function llenar_combo_tipo_pro($sector, $id=""){
		if($id!="")
			$tipos_pro=$this->proyecto->cargar_tipos_pro($id);
		$query=$this->db->order_by('Nombre_tipo','asc');
 		$query=$this->db->where('id_sector',$sector);
 		$lista_tipo=$this->db->get($this->table);
		$combo_tipo="";
 		if($lista_tipo->num_rows()>0){
 			$combo_tipo.="<option value=''>- Tipo -</option>";
 			foreach($lista_tipo->result() as $resultado_tipo){
				if($id!= ""&& in_array($resultado_tipo->id_tipo, $tipos_pro)){
					$selected="selected='selected'";
				}else{
					$selected="";
				}
 				$combo_tipo.="<option ".$selected." value='".$resultado_tipo->id_tipo."'>".$resultado_tipo->Nombre_tipo."</option>";
 			}
 		}
		else{
 			$combo_tipo="no hay Resultados";
 		}
 		return $combo_tipo;
 	}
}
 
?>