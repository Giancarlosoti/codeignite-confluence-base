<?php
class M_sector extends CI_Model
{
	const TABLA_SECTOR = 'proyectos_sector';
	function __construct(){
		parent::__construct();
	}
	   
   function ingresar($datos){
   
   	$this->db->insert('proyectos_sector', $datos);
   	
   }
   function mostrar($id_sector=0){
	if($id_sector!=0 && $id_sector!=""){
		$this->db->where("id_sector", $id_sector);
	}
	$this->db->order_by("Nombre_sector", "asc");
   	$query=$this->db->get('proyectos_sector');
	$result = $query->result();
	return $result;
   	
   }
   function editar_proyecto($id){
   	$query=$this->db->query('SELECT * FROM proyectos_sector WHERE id_sector='.$id.' order by Nombre_sector asc');
	$result = $query->first_row();
	return $result;
   	
   }	
   function guardar_edicion($datos,$id){
   	$this->db->where('id_sector', $id);
   	$this->db->update('proyectos_sector', $datos);
   	
   }
   
   function borrar_id($id){
   	$this->db->where('id_sector', $id);
   	$this->db->delete('proyectos_sector');
   	
   }
   
	function llenar_combo_sector($id=0){
		$id_select=0;
		$this->db->order_by("Nombre_sector", "asc");
		$query = $this->db->get(self::TABLA_SECTOR);
		$arreglo_sector= array();
		$lista_sector = array();
		$lista_sector['']='- Selecciona un Sector -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado_sector){
				$lista_sector[$resultado_sector['id_sector']]= $resultado_sector['Nombre_sector'];
				if($id==$resultado_sector['id_sector']){
					$id_select=$id;
				}
				
			}
			
			if($id){
				$arreglo_sector[0]=$id_select;
				$arreglo_sector[1]=$lista_sector;
				return $arreglo_sector;
			}
			else{
				return $lista_sector;
			}
		
		}
	}	

}
 
?>