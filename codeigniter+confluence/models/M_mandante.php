<?php
class M_mandante extends CI_Model
{
	function __construct(){
		
		parent::__construct();
	}
	   
   function ingresar($datos){
   
   	$this->db->insert('proyectos_mandante', $datos);
   	
   }
   function mostrar(){
   
   	$query=$this->db->query('SELECT * FROM proyectos_mandante');
	$result = $query->result();
	return $result;
   	
   }
   function editar_proyecto($id){
   
   	$query=$this->db->query('SELECT * FROM proyectos_mandante WHERE id_man='.$id);
	$result = $query->first_row();
	return $result;
   	
   }	
   function guardar_edicion($datos,$id){
   	$this->db->where('id', $id);
   	$this->db->update('proyectos_mandante', $datos);
   	
   }
   function borrar_id($id){
   	$this->db->where('id', $id);
   	$this->db->delete('proyectos_mandante');
   	
   }
   
	

} 
?>