<?php
class M_ficha extends CI_Model
{
	function __construct(){
		parent::__construct();
		$this->load->model('params','params');
	}

	
	
	
	
	
	
	
	/*===============================================================================================================================*/

public function sector_del_proyecto($id)
	{
	   
	    $query = $this->db->query("SELECT id_sector FROM proyectos  WHERE id_pro = ".$id);
	    foreach ($query->result() as $row)
	    {
	        $respuesta  = $row->id_sector;
	        
	    }
	    
	    $query->free_result();
	    $this->db->close();
	    return($respuesta);
	}	
	
	
	public function usuarios_del_sector_seleccinado($sector)
	{
	    
	    $respuesta = array();
	   	    
        $sql  = " SELECT
  user_socio.nombre_completo_socio
    ,user_socio.email_socio
    , socio.Estado_socio
    , user_socio_sector.id_sector
FROM
    portalminero.socio
    INNER JOIN portalminero.user_socio 
        ON (socio.id_socio = user_socio.id_socio)
    INNER JOIN portalminero.user_socio_sector 
        ON (user_socio.id_user_socio = user_socio_sector.id_user_socio)
WHERE (socio.Estado_socio = 'A'
    AND user_socio_sector.id_sector =  ".$sector.");";
		
		
	    $query = $this->db->query($sql);
	    foreach ($query->result() as $row)
	    {
	        $respuesta[$i][0]  = $row->nombre_completo_socio;
	        $respuesta[$i][1]  = $row->email_socio;
	        $i++;
	    }
	    
	    $query->free_result();
	   // $this->db->close();
	    return($respuesta);
	}
	
	
	
	
	
	public function usuarios_sector_premium()
	{
		$respuesta = array();
		$sql="SELECT
			user_socio.nombre_completo_socio
			, user_socio.email_socio
		   
		FROM
			portalminero.socio
			INNER JOIN portalminero.user_socio 
				ON (socio.id_socio = user_socio.id_socio)
		WHERE (socio.Estado_socio = 'A'
			AND socio.tipo_socio = 'Premium') AND (  email_socio <> '');";
				
	    //echo $sql;
	    
	     $query = $this->db->query($sql);
	    foreach ($query->result() as $row)
	    {
	        $respuesta[$i][0]  = $row->nombre_completo_socio;
	        $respuesta[$i][1]  = $row->email_socio;
	        $i++;
	    }
	    
	    $query->free_result();
	   // $this->db->close();
	    return($respuesta);
	}
	
	
	
	
		function envio_de_usuario_proyecto_nuevo($id){
		 $rcarr       =  array();
		 $rcarP       =  array();
		 $respuestas  =  array();
		 $sector      =  $this->sector_del_proyecto($id);
		 $rcarr       =  $this->usuarios_del_sector_seleccinado($sector);
		// $rcarrP      =  $this->usuarios_sector_premium();
		// $respuestas = $rcarr+$rcarrP;
		 
		 $respuestas = $rcarr;
		 
		//  $respuestas = array_merge((array)$rcarrP, (array)$rcarr);
		 
		 $proyecto=$this->editar_proyecto($id);
		 
		  
		 if(!empty($respuestas)) {
	        for($i = 0; $i < sizeof($respuestas);$i++)
	        {
				
				      $nombre       = htmlentities($respuestas[$i][0]);
				      $email_socio  = $respuestas[$i][1];
				      $datos=array();
					  $contenido='';
					  $datos['tipo_envio']='Proyecto de Sector '.$proyecto->Nombre_sector;
					  $datos['url_titulo']=$proyecto->id_pagina_pro;
		 			  $datos['titulo']=$proyecto->Nombre_pro;
					  $datos['url_confluence']=URL_PUBLICA_CONFLUENCE;
					  $datos['url_sector']='/display/'.$this->params->spaces_proy[$proyecto->id_sector];
					  $datos['imagen_envio']=URL_PUBLICA_CONFLUENCE.'/sitio_portal/images/portal-06.png';
					  $datos['url_add_rubro']=URL_PUBLICA_CONFLUENCE.'/pages/viewpage.action?pageId=29786340';
					  $datos['nombre_user']=$nombre;
					  if($email_socio!=""){
						  $contenido =$this->load->view('proyectos/formato_correo_proyecto', $datos, true);
						 // echo $nombre."-> ".$email_socio. "<br>";
						 
						//  echo $contenido;
						//  echo "<br>";
						 
						  if($email_socio!=""){
						        $this->enviar_correo_mail($proyecto->Nombre_sector,$email_socio,$nombre,$contenido);
						  }
						 
					  }
				
		    }
			
		}
		$this->enviar_correo_mail($proyecto->Nombre_sector,"aoyarzun@portalminero.com","Alda Oyarzun",$contenido);
		 
	}
	
	
	function avisa_mandar_crreos(){
		$para      = 'aoyarzun@portalminero.com';
		$titulo    = "Fin Envio Correos";
		$mensaje   = "----- Fin Envio Correos Proyecto Nuevo ----";
		$cabeceras = 'From: Portal Minero <eventos@portalminero.com>' . "\r\n" .
		    'Content-type: text/html; charset=utf-8' . "\r\n" .
			'Reply-To: eventos@portalminero.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		mail($para, $titulo, $mensaje, $cabeceras);
	}
/*==============================================================================================================================*/

	
	/************************************************** INICIO PERSONALIZAD **************************************************************************/
/*PERSONALISADO EPF*/


function generar_ficha_html_personalizado($id, $titulo=""){
		$html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/"."template/ficha_personalizado.php");

		$proyecto=$this->mostrar_proyecto_full($id);
		$url=$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';



             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/



		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);

		/*Fin mano de obra*/

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  htmlentities($this->william($ht->Nombre_hito)), $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", $html_hitos, $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  htmlentities($this->william($st->Nombre_etapa)), $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row1("", $html_etapas, "etapas", $template, "si",1);
				}else{
					$template=$this->create_row1("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row1("Etapas", "", "etapas", $template, "no");
			}



			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($licitacion->Nombre_lici_completo))));
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", $html_lic, "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}


			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($adjudicacion->nombre_adj))));
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", $html_adj, "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}


			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($prop->Nombre_fantasia_emp))));
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", $html_propietarios, "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}


			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					//$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($tipo->Nombre_tipo))));
					$value=htmlentities(html_entity_decode($this->params->br2nl($tipo->Nombre_tipo)));
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", $html_tipos, "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($obra->Nombre_obra))));
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

		if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($equipo->Nombre_equipo))));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($suministro->Nombre_sumin))));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($servicio->Nombre_serv))));
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=htmlentities(html_entity_decode($this->william($this->params->br2nl($c->Nombre_cat_serv))));
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=htmlentities(html_entity_decode($this->william($this->params->br2nl($sc->Nombre_sub_serv))));
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", $html_servicios, "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}


	
			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=htmlentities(html_entity_decode($this->william($etapa->Nombre_etapa)));
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row1("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row1("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}
																								
				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){

					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($etapa->Nombre_fantasia_emp))));

					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);

					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=htmlentities(html_entity_decode($this->william($e->Nombre_fantasia_emp)));
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}


			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("&", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row1("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row1("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", $this->william($proyecto->Nombre_generico_pro), "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				//$value1=htmlentities(html_entity_decode($this->william($proyecto->Nombre_sector)));
				$value1=htmlentities(html_entity_decode($proyecto->Nombre_sector));
				$value="<a href='".$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				//echo ($value);
				//exit;
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=html_entity_decode($this->william($proyecto->Nombre_pais));
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=htmlentities(html_entity_decode($this->william($proyecto->Nombre_region)));
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=html_entity_decode($proyecto->Nombre_comuna);
				$template=$this->create_row("Comuna",  $this->william($value), "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", $this->william($proyecto->Direccion_pro), "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}

			if($proyecto->Nombre_fantasia_emp!=""){
				$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($proyecto->Nombre_fantasia_emp))));
				$filtro="mandante_".$proyecto->id_man_emp;
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("Mandante", $value, "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", htmlentities(html_entity_decode($this->william($proyecto->Nombre_pro))), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", htmlentities(html_entity_decode($this->params->br2nl($proyecto->Desc_pro))), "descripcion", $template, "si", 1);
			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", $this->william($proyecto->detalle_equipos), "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", $this->william($proyecto->detalle_suministros), "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

			$tmp_contact='';
			$item=0;
			
			
			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Nombre_contact)).'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Empresa_contact)).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Cargo_contact)).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Email_contact)).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Telefono_contact)).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Direccion_contact)).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Nombre_contact)).'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Empresa_contact)).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Cargo_contact)).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Email_contact)).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Telefono_contact)).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Direccion_contact)).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", $this->william($tmp_contact) , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}

			/*if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}*/


			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row1("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row1("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row1("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row1("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row1("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row1("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row1("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row1("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities($this->william($etapa_act)), $template);
				$template=$this->create_row("", htmlentities($this->william($etapa_act)), "nombre_etapa", $template, "si");
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", htmlentities(html_entity_decode($this->william($proyecto->Nombre_pro))), $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			//$this->guardar_html_proyecto($proyecto->id_pro, $template);
//echo $template;
//exit;

$template= str_replace('href="/display/pmin/viewpage','',$template);
$template= str_replace('href','',$template);





			return($template);
		}else{
			return(false);
		}
	}


	
	/****************** FUNCIONES PERSOVALIZADO*******************************/
	
	public function get_personalizados($id=0){
		if($id!="" && $id!=NULL){
			$query="
				SELECT
					socio_personalizado.id_proyecto
					, proyectos.Nombre_pro
					, socio_personalizado.id_socio
				FROM
					portalminero.socio_personalizado
					INNER JOIN portalminero.proyectos 
						ON (socio_personalizado.id_proyecto = proyectos.id_pro)
				WHERE (socio_personalizado.id_socio = ".$id.")
				ORDER BY proyectos.Nombre_pro ASC; ";
				
			$this->db->cache_on();
			$query=$this->db->query($query);
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)){
				return($rs);
			}else{
				return(false);
			}
		}else{
			return(false);
		}
		
	}

	public function valida_personalizado($id_usuario=0,$id_proyecto=0)
	{
	    $respuesta = 0;
	    $query = $this->db->query("SELECT id_proyecto FROM socio_personalizado WHERE id_socio = ".$id_usuario." AND id_proyecto = ".$id_proyecto);
		
		if($query->num_rows()>0){
			 $row         =  $query->row();
			 $respuesta   =  $row->id_proyecto;
		} 
		
		$query->free_result(); 
		return $respuesta;
	   
	}
	
	
	
	public function trae_id_socio($usuario)
	{
		
	    $respuesta = 0;
		$sql="
			SELECT
				user_socio.id_socio
			FROM
				portalminero.user_socio
				INNER JOIN portalminero.socio_personalizado 
					ON (user_socio.id_socio = socio_personalizado.id_socio)
			WHERE (user_socio.username_socio = '".$usuario."') LIMIT 0, 1;";
			
			
	    $query = $this->db->query($sql);
		
		if($query->num_rows()>0){
			 $row         =  $query->row();
			 $respuesta   =  $row->id_socio;
		} 
		
		$query->free_result(); 
		return $respuesta;
	   
	}
	
	

/************************************************** FIN PERSONALIZADO***************************************************************************************************************/	
	
	
	
	
	
	
		











	
	
	
	
	
	function mostrar_oportunidad($id){
		$query = $this->db->where('id_tipo_oport', 1);
		$query = $this->db->where('id_padre', $id);
		$query=$this->db->get("oportunidadesNegocios");
		$result = $query->first_row();
		return $result;
	}

	function verificar_nombre_proyecto($datos){
		$query = $this->db->where("Nombre_pro",$datos);
		$query = $this->db->get("proyectos");
		return $query;
	}

	function ingresar($datos){
		$this->db->insert('proyectos', $datos);
		return $this->db->insert_id();
	}

	function verificar_relacion_tipo($datos){
		$query = $this->db->where($datos);
		$query = $this->db->get("proyectos_x_tipo");
        $cant=$query->num_rows();
		return $cant;
	}

	function ingresar_tipo($datos){
		$this->db->insert('proyectos_x_tipo', $datos);
	}


   function  tiene_rca($id_pro){
   	/* Ve si el proyecto tiene el hito RCA  EPF */
     $query = $this->db->query('SELECT id_hito  AS rca FROM proyectos_x_hitos WHERE id_pro = '.$id_pro.' AND id_hito = 1');
     return $query->num_rows();
   }
	

   function total_rel_tipo($datos){
		$query=$this->db->where($datos);
		$sum=0;
		$query = $this->db->get('proyectos_x_tipo');
		//$lista[""]='- Empresa -'; Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_tipo'];
				$sum ++;
			}
			return $lista;
		}
	}

   function borrar_rel_tipo($datos,$id){
	    $this->db->where($datos);
		$this->db->where('id_tipo', $id);
		$this->db->delete('proyectos_x_tipo');
	}

	function borrar_rel_tipo_all($datos){
	    $this->db->where($datos);
		$this->db->delete('proyectos_x_tipo');
	}

	function verificar_relacion_obra($datos){
		$query = $this->db->where($datos);
		$query = $this->db->get("proyectos_x_obras");
        $cant=$query->num_rows();
		return $cant;
	}

	function ingresar_obra($datos){

		$this->db->insert('proyectos_x_obras', $datos);
	}

	function total_rel_obra($datos){
		$query=$this->db->where($datos);
		$sum=0;
		$query = $this->db->get('proyectos_x_obras');
		//$lista[""]='- Empresa -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_obra'];
				$sum ++;
			}
					return $lista;


		}
	}

	function borrar_rel_obra($datos,$id){
	    $this->db->where($datos);
		$this->db->where('id_obra', $id);
		$this->db->delete('proyectos_x_obras');
	}

	function borrar_rel_obra_all($datos){
	    $this->db->where($datos);
		$this->db->delete('proyectos_x_obras');
	}

	function verificar_relacion_equipo($datos){
		$query = $this->db->where($datos);
		$query = $this->db->get("proyectos_x_equipos");
		$cant=$query->num_rows();
		return $cant;
	}

	function ingresar_equipo($datos){

		$this->db->insert('proyectos_x_equipos', $datos);
	}

   function total_rel_equipo($datos){
		$query=$this->db->where($datos);
		$sum=0;
		$query = $this->db->get('proyectos_x_equipos');
		//$lista[""]='- Empresa -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_equipo'];
				$sum ++;
			}
					return $lista;


		}
	}

   function borrar_rel_equipo($datos,$id){
	    $this->db->where($datos);
		$this->db->where('id_equipo', $id);
		$this->db->delete('proyectos_x_equipos');
	}

	function borrar_rel_equipo_all($datos){
	    $this->db->where($datos);
		$this->db->delete('proyectos_x_equipos');
	}

   function verificar_relacion_suministro($datos){
		$query = $this->db->where($datos);
		$query = $this->db->get("proyectos_x_suministros");
        $cant=$query->num_rows();
		return $cant;
	}

   function ingresar_suministro($datos){

   	$this->db->insert('proyectos_x_suministros', $datos);
   }

   function total_rel_suministro($datos){
		$query=$this->db->where($datos);
		$sum=0;
		$query = $this->db->get('proyectos_x_suministros');
		//$lista[""]='- Empresa -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_sumin'];
				$sum ++;
			}
					return $lista;
		}
	}

   function borrar_rel_suministro($datos,$id){
	    $this->db->where($datos);
		$this->db->where('id_sumin', $id);
		$this->db->delete('proyectos_x_suministros');
	}

	function borrar_rel_suministro_all($datos){
	    $this->db->where($datos);
		$this->db->delete('proyectos_x_suministros');
	}

	function borrar_rel_servicio($datos,$id){
	    $this->db->where($datos);
		$this->db->where('id_serv', $id);
		$this->db->delete('proyectos_x_servicios');
	}
	function borrar_rel_servicio_all($datos){
	    $this->db->where($datos);
		$this->db->delete('proyectos_x_servicios');
	}

	function borrar_rel_servcat($datos,$id){
	    $this->db->where($datos);
		$this->db->where('id_cat_serv', $id);
		$this->db->delete('proyectos_x_servcat');
	}
	function borrar_rel_servcat_all($datos){
	    $this->db->where($datos);
		$this->db->delete('proyectos_x_servcat');
	}

	function borrar_rel_servsub($datos,$id){
	    $this->db->where($datos);
		$this->db->where('id_sub_serv ', $id);
		$this->db->delete('proyectos_x_servsubcat');
	}
	function borrar_rel_servsub_all($datos){
	    $this->db->where($datos);
		$this->db->delete('proyectos_x_servsubcat');
	}

   function verificar_relacion_propietario($datos){
		$query = $this->db->where($datos);
		$query = $this->db->get("proyectos_x_empresas");
        $cant=$query->num_rows();
		return $cant;
	}

   function ingresar_propietario($datos){
   	$this->db->insert('proyectos_x_empresas', $datos);
   }

   function total_rel_propietario($id_pro){
		$query=$this->db->where('id_pro',$id_pro);
		$sum=0;
		$query = $this->db->get('proyectos_x_empresas');
		//$lista[""]='- Empresa -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$sum]= $resultado['id_emp'];
				$sum ++;
			}
					return $lista;


		}
	}

	 function borrar_rel_propietario($id,$id_emp){
		$this->db->where('id_pro', $id);
		$this->db->where('id_emp', $id_emp);
		$this->db->delete('proyectos_x_empresas');
	}

	function editar_propietario($datos,$where){
		$this->db->where($where);
		$this->db->update('proyectos_x_empresas', $datos);
	}

	function mostrar($param_orden,$tipo_orden){
	    $result=array();
	    // EPF
		$query = $this->db->where('Borrar',0);
		$query = $this->db->order_by($param_orden,$tipo_orden);
		$query = $this->db->select("proyectos.*,m_user.Nombre_completo_user, proyectos_sector.*, u_pais.*, u_region.*, u_comuna.*, (SELECT id_hito FROM `proyectos_x_hitos` WHERE id_pro= proyectos.id_pro ORDER BY ano_hito  ,`trim_hito` , id_proyxhito DESC LIMIT 1) ultimo_hito");
		$query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector");
		$query = $this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais");
		$query = $this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
		$query = $this->db->join("m_user", "m_user.id_user = proyectos.id_usuario_modifica", 'left');
		$query = $this->db->join("u_comuna", "u_comuna.id_comuna = proyectos.id_comuna", 'left');
		$query = $this->db->get("proyectos");
		$result = $query->result();
		return $result;
	}
	function mostrar_sector($param_orden,$tipo_orden,$sector){
		$query = $this->db->where('Borrar',0);
		$query = $this->db->where('pro.id_sector',$sector);
		$query = $this->db->order_by($param_orden,$tipo_orden);
		$query = $this->db->select("pro.*,m_user.Nombre_completo_user, proyectos_sector.*, u_pais.*, u_region.*, u_comuna.*, (SELECT id_hito FROM `proyectos_x_hitos` where id_pro=pro.id_pro order by ano_hito desc, trim_hito desc, id_proyxhito desc limit 1) ultimo_hito");
		$query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = pro.id_sector");
		$query = $this->db->join("u_pais", "u_pais.id_pais = pro.id_pais");
		$query = $this->db->join("u_region", "u_region.id_region = pro.id_region", 'left');
		$query = $this->db->join("m_user", "m_user.id_user = pro.id_usuario_modifica", 'left');
		$query = $this->db->join("u_comuna", "u_comuna.id_comuna = pro.id_comuna", 'left');
		$query = $this->db->get("proyectos pro");
		$result = $query->result();
		return $result;
	}
	function mostrar_vigentes($param_orden,$tipo_orden){
		$this->db->where("Borrar = 0 AND Etapa_actual_pro != 8 AND((SELECT count(id_hito) FROM proyectos_x_hitos WHERE proyectos_x_hitos.id_pro=proyectos.id_pro)=0 OR (((SELECT id_hito FROM proyectos_x_hitos where proyectos_x_hitos.id_pro=proyectos.id_pro order by ano_hito desc, trim_hito desc, id_proyxhito desc limit 1) <> ".$this->params->hito_desistido.") AND ((SELECT id_hito FROM proyectos_x_hitos where proyectos_x_hitos.id_pro=proyectos.id_pro order by ano_hito desc, trim_hito desc, id_proyxhito desc limit 1) !=".$this->params->hito_desistido2.")))");
		$query = $this->db->order_by($param_orden,$tipo_orden);
		$query = $this->db->select("proyectos.*,m_user.Nombre_completo_user, proyectos_sector.*, u_pais.*, u_region.*, u_comuna.*, (SELECT id_hito FROM `proyectos_x_hitos` where id_pro=proyectos.id_pro order by ano_hito desc, trim_hito desc, id_proyxhito desc limit 1) ultimo_hito", FALSE);
		$query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector","left");
		$query = $this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais","left");
		$query = $this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
		$query = $this->db->join("m_user", "m_user.id_user = proyectos.id_usuario_modifica", 'left');
		$query = $this->db->join("u_comuna", "u_comuna.id_comuna = proyectos.id_comuna", 'left');
		$query = $this->db->get("proyectos");
		$result = $query->result();
		return $result;
	}
	function mostrar_borrados($param_orden,$tipo_orden){
		$query = $this->db->where('Borrar',1);
		$query = $this->db->order_by($param_orden,$tipo_orden);
		$query = $this->db->select("proyectos.*, proyectos_sector.*, u_pais.*, u_region.*, u_comuna.*, (SELECT id_hito FROM `proyectos_x_hitos` where id_pro=proyectos.id_pro order by ano_hito desc, trim_hito desc, id_proyxhito desc limit 1) ultimo_hito",FALSE);
		$query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector");
		$query = $this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais");
		$query = $this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
		$query = $this->db->join("u_comuna", "u_comuna.id_comuna = proyectos.id_comuna", 'left');
		$query = $this->db->get("proyectos");
		$result = $query->result();
		return $result;
	}


   function editar_proyecto($id){
		$query = $this->db->where('id_pro', $id);
		$query = $this->db->select('proyectos_sector.Nombre_sector, pmp.Sigla_med, proyectos_sector.Space_sector, u_pais.Nombre_pais, u_region.Nombre_region, u_comuna.Nombre_comuna, empresas.Nombre_fantasia_emp, proyectos.*');
		$query = $this->db->join("proyectos_medicion_produccion pmp", "pmp.id_med = proyectos.Medicion_produccion_pro", 'left');
		$query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector", 'left');
		$query = $this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais", 'left');
		$query = $this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
		$query = $this->db->join("u_comuna", "u_comuna.id_comuna = proyectos.id_comuna", 'left');
		$query = $this->db->join("empresas", "empresas.id_emp = proyectos.id_man_emp", 'left');
		$query=$this->db->get("proyectos");
		$result = $query->first_row();
		return $result;
	}

	function editar_proyecto_suma($id){

		$query = $this->db->where('id_pro', $id);
		$query = $this->db->select('proyectos_sector.Nombre_sector, pmp.Sigla_med, proyectos_sector.Space_sector, u_pais.Nombre_pais, u_region.Nombre_region, u_comuna.Nombre_comuna, empresas.Nombre_fantasia_emp, proyectos.*');
		$query = $this->db->join("proyectos_medicion_produccion pmp", "pmp.id_med = proyectos.Medicion_produccion_pro", 'left');
		$query = $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector", 'left');
		$query = $this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais", 'left');
		$query = $this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
		$query = $this->db->join("u_comuna", "u_comuna.id_comuna = proyectos.id_comuna", 'left');
		$query = $this->db->join("empresas", "empresas.id_emp = proyectos.id_man_emp", 'left');
		$query=$this->db->get("proyectos");
		$result = $query->first_row();
		return $result;
	}

	function pagina_conf($id){
		$query = $this->db->where('id_pro', $id);
		$query=$this->db->get("proyectos");
		$result = $query->first_row()->id_pagina_pro;
		return $result;
	}

	function mostrar_tipos($id){
		$query = $this->db->where('id_pro', $id);
		$query = $this->db->join("proyectos_tipo", "proyectos_tipo.id_tipo = proyectos_x_tipo.id_tipo", "inner");
		$query=$this->db->get("proyectos_x_tipo");
		$result = $query->result();
		return $result;
	}

	function mostrar_obras($id){
		$query = $this->db->where('id_pro', $id);
		$query = $this->db->group_by('proyectos_x_obras.id_obra');
		$query = $this->db->join("obras_principales", "obras_principales.id_obra = proyectos_x_obras.id_obra", "inner");
		$query=$this->db->get("proyectos_x_obras");
		$result = $query->result();
		return $result;
	}

	function mostrar_equipos($id){
		$query = $this->db->where('id_pro', $id);
		$query = $this->db->group_by('proyectos_x_equipos.id_equipo');
		$query = $this->db->join("equipos_principales", "equipos_principales.id_equipo = proyectos_x_equipos.id_equipo", "inner");
		$query=$this->db->get("proyectos_x_equipos");
		$result = $query->result();
		return $result;
	}

	function mostrar_suministros($id){
		$query = $this->db->where('id_pro', $id);
		$query = $this->db->group_by('proyectos_x_suministros.id_sumin');
		$query = $this->db->join("suministros_principales", "suministros_principales.id_sumin = proyectos_x_suministros.id_sumin", "inner");
		$query=$this->db->get("proyectos_x_suministros");
		$result = $query->result();
		return $result;
	}

	function mostrar_servicios($id){
		$this->db->select("ps.*");
		$query = $this->db->where('pro.id_pro', $id);
		$query = $this->db->join("servicios_principales ps", "ps.id_serv = pxs.id_serv", "inner");
		$query = $this->db->join("proyectos pro", "pro.id_pro=pxs.id_pro", "inner");
		$query=$this->db->get("proyectos_x_servicios pxs");
		$result = $query->result();
		return $result;
	}

	function mostrar_propietarios($id){
		$query = $this->db->where('proyectos.id_pro', $id);
		$query = $this->db->join("empresas", "empresas.id_emp = proyectos_x_empresas.id_emp", "inner");
		$query = $this->db->join("proyectos", "proyectos.id_pro = proyectos_x_empresas.id_pro", "inner");
		$query=$this->db->get("proyectos_x_empresas");
		$result = $query->result();
		return $result;
	}

	function guardar_edicion($datos,$id){
		$this->db->where('id_pro', $id);
		$resp=$this->db->update('proyectos', $datos);
		return $resp;
	}
    function borrar_estado($id){
		$this->db->where('id_pro',$id);
		$query=$this->db->get('proyectos');
		$pro=$query->first_row();
		$id_pagina=$pro->id_pagina_pro;
		$this->db->where('id_pro',$id);
		$datos['Borrar']=1;
		$datos['id_pagina_pro']=NULL;
		$datos['nro_version']=NULL;
		$datos['url_confluence_pro']=NULL;
		$datos['titulo_confluence_pro']=NULL;

		if($this->db->update('proyectos',$datos)){
			return $this->soap->removePage($id_pagina);
		}
		return false;
	}
	function borrar_id($id){
		$this->db->where('id_pro', $id);
		if($this->db->delete('proyectos')){
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_contratos');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_empresas');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_equipos');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_etapas');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_hitos');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_obras');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_servcat');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_servicios');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_servsubcat');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_suministros');
			$this->db->where('id_pro', $id);
			$this->db->delete('proyectos_x_tipo');
			$this->db->where('id_pro', $id);
			$this->db->delete('licitaciones');
			$this->db->where('id_proy_adj', $id);
			$this->db->delete('adjudicaciones');
			$this->db->where('id_tipo_oport', 1);
			$this->db->where('id_padre', $id);
			$this->db->delete('oportunidadesNegocios');
			return(true);
			exit;
		}
		return(false);
	}

	function mostrar_historial($id){
		$query = $this->db->where('id_pro', $id);
		$query=$this->db->get("proyectos");
		$result = $query->first_row();
		return $result;


	}

	function guardar_historial($id, $fecha, $anterior, $descripcion, $borrar_ultima){
		if(intval($borrar_ultima)==1){
			$this->db->where('id_pro', $id);
			$datos=array();
			$datos["ultima_informacion_pro"]="";
			$this->db->update('proyectos', $datos);
		}
		if($descripcion!=""){
			$datos=array();
			$query = $this->db->where('id_pro', $id);
			$query=$this->db->get("proyectos");
			$result = $query->first_row();
			if(is_object($result)){
				if($result->ultima_informacion_pro!="" && $result->ultima_informacion_pro!=NULL){
					$upd=$fecha." ".$result->ultima_informacion_pro."\r\n\n".$anterior;
				}else{
					$upd=$anterior;
				}
				if($descripcion!=""){
					//$datos["ultima_informacion_pro"]="(".$this->params->mes[intval(str_replace("0", "", date("m")))]." de ".date("Y").") : ".$descripcion;
					$datos["ultima_informacion_pro"]=$descripcion;
				}
				$this->db->where('id_pro', $id);
				$datos["Historial_pro"]=$upd;
				$this->db->update('proyectos', $datos);
				return(array($datos["ultima_informacion_pro"], $upd));
			}
		}else{
			$this->db->where('id_pro', $id);
			$datos["Historial_pro"]=$anterior;
			$this->db->update('proyectos', $datos);
			return (array("", $datos["Historial_pro"]));
		}
	}

	function mostrar_proyecto($id){
		$query = $this->db->where('id_pro', $id);
		$query=$this->db->get("proyectos");
		$result = $query->first_row();
		return $result;
	}

	function buscar_por_pag($id){
		$query = $this->db->where('id_pagina_pro', $id);
		$query=$this->db->get("proyectos");
		$result = $query->first_row();
		return $result;
	}

	function mostrar_proyecto_full($id){
		$this->db->select("pro.*, ps.*, c.*, emp.*,pmp.*, p.id_pais id_pais_p, p.Nombre_pais, r.id_region id_region_p, r.Nombre_region");
		$query = $this->db->where('pro.id_pro', $id);
		$query = $this->db->join("proyectos_medicion_produccion pmp", "pmp.id_med = pro.Medicion_produccion_pro", 'left');
		$query = $this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'left');
		$query = $this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
		$query = $this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
		$query = $this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
		$query = $this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
		$query=$this->db->get("proyectos pro");
		$result = $query->first_row();
		return $result;
	}


	function guardar_proyectosxetapas($id, $etapa, $data){
		$query = $this->db->where('id_pro', $id);
		$query = $this->db->where('id_etapa', $etapa);
		$query=$this->db->get("proyectos_x_etapas");
		$result = $query->first_row();
		if(is_object($result)){
			$this->db->where('id_pro', $id);
			$this->db->where('id_etapa', $etapa);
			$this->db->update('proyectos_x_etapas', $data);
			return(1);
		}else{
			$data["id_pro"]=$id;
			$data["id_etapa"]=$etapa;
			$this->db->insert('proyectos_x_etapas', $data);
			return $this->db->insert_id();
		}
	}

	function mostrar_contrato($id){
		$this->db->where('id_pro', $id);
		$query=$this->db->get("proyectos_x_contratos");
		$result = $query->first_row();
		return $result;
	}

	function mostrar_serv_cat($id){
		$this->db->select("spc.*");
		$query = $this->db->where('pro.id_pro', $id);
		//$query = $this->db->group_by('id_proxcat');
		$query = $this->db->join("servicios_princ_cat spc", "spc.id_cat_serv = pxs.id_cat_serv", "inner");
		$query = $this->db->join("proyectos pro", "pro.id_pro = pxs.id_pro", "inner");
		$query=$this->db->get("proyectos_x_servcat pxs");
		$result = $query->result();
		return $result;
	}

	function mostrar_serv_subcat($id){
		$this->db->select("spc.*");
		$query = $this->db->where('pxs.id_pro', $id);
		$query = $this->db->join("servicios_princ_subcat spc", "spc.id_sub_serv=pxs.id_sub_serv", "inner");
		$query = $this->db->join("proyectos pro", "pro.id_pro = pxs.id_pro", "inner");
		$query=$this->db->get("proyectos_x_servsubcat pxs");
		$result = $query->result();
		return $result;
	}

	function traer_tipos_contrato(){
		$query=$this->db->get("tipos_contratos");
		if($query->num_rows()>0){
			$lista[""]="- Selecciona Tipo Contrato -";
			foreach($query->result_array() as $resultado){
				$lista[$resultado['id_tipo_contrato']]= $resultado['Abreviacion_tipo_contrato'];
			}
			return $lista;
		}
	}

	function mostrar_licitaciones($id){
		$this->db->where("id_pro", $id);
		$query=$this->db->get("licitaciones");
		$result=$query->result();
		return($result);
	}

	function mostrar_adjudicaciones($id){
		$this->db->where("id_proy_adj", $id);
		$query=$this->db->get("adjudicaciones");
		$result=$query->result();
		return($result);
	}

	function guardar_contrato($id, $datos){
		$query = $this->db->where('id_pro', $id);
		$query=$this->db->get("proyectos_x_contratos");
		$result = $query->first_row();
		if(is_object($result)){
			$this->db->where('id_pro', $id);
			$data=array('id_cont'=>$datos["tipo_contrato"], 'id_emp'=>$datos["empresa_contrato"]);
			$this->db->update('proyectos_x_contratos', $data);
			return(1);
		}else{
			$data=array();
			$data["id_pro"]=$id;
			$data["id_emp"]=$datos["empresa_contrato"];
			$data["id_cont"]=$datos["tipo_contrato"];
			$this->db->insert('proyectos_x_contratos', $data);
			return $this->db->insert_id();
		}
	}

	function mostrar_hitos(){
		$query=$this->db->get("proyectos_hitos");
		if($query->num_rows()>0){
			$lista[""]="- Hitos -";
			foreach($query->result_array() as $resultado){
				$lista[$resultado['id_hito']]= $resultado['Nombre_hito'];
			}
			return $lista;
		}
		return 1;
	}

	function llenar_combo_proyecto(){
		$query = $this->db->order_by('Nombre_pro','asc');
		$query = $this->db->where('Estado_pro !=','N');
		$query = $this->db->where('Estado_pro !=','R');
		$query = $this->db->get('proyectos');
		$lista = array();
		$lista['']='- Selecciona un proyecto -'; // Opción sin valor, servirá de selección por defecto.
		$lista[0]='Otro'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$resultado['id_pro']]= $resultado['Nombre_pro'];
			}
		}
		return $lista;
	}

	function cargar_tipos_pro($id){
		$this->db->where("id_pro", $id);
		$query=$this->db->get("proyectos_x_tipo");
		$result=$query->result();
		if(is_array($result) && sizeof($result)>0){
			$return=array();
			foreach($result as $rs){
				$return[]=$rs->id_tipo;
			}
			return($return);
		}else{
			return(array());
		}
	}

	function cargar_equipos_pro($id,$obra,$tipo){
		$this->db->where("id_pro", $id);
		$this->db->where("id_obra", $obra);
		$this->db->where("id_tipo", $tipo);
		$query=$this->db->get("proyectos_x_equipos");
		$result=$query->result();
		if(is_array($result) && sizeof($result)>0){
			$return=array();
			foreach($result as $rs){
				$return[]=$rs->id_equipo;
			}
			return($return);
		}else{
			return(array());
		}
	}

	function cargar_suministros_pro($id,$obra,$tipo){
		$this->db->where("id_pro", $id);
		$this->db->where("id_obra", $obra);
		$this->db->where("id_tipo", $tipo);
		$query=$this->db->get("proyectos_x_suministros");
		$result=$query->result();
		if(is_array($result) && sizeof($result)>0){
			$return=array();
			foreach($result as $rs){
				$return[]=$rs->id_sumin;
			}
			return($return);
		}else{
			return(array());
		}
	}

	function cargar_obras_pro($id,$tipo){
		$this->db->where("id_pro", $id);
		$this->db->where("id_tipo", $tipo);
		$query=$this->db->get("proyectos_x_obras");
		$result=$query->result();
		if(is_array($result) && sizeof($result)>0){
			$return=array();
			foreach($result as $rs){
				$return[]=$rs->id_obra;
			}
			return($return);
		}else{
			return(array());
		}
	}

	function cargar_categorias_pro($id){
		$this->db->where("id_pro", $id);
		$query=$this->db->get("proyectos_x_servcat");
		$result=$query->result();
		if(is_array($result) && sizeof($result)>0){
			$return=array();
			foreach($result as $rs){
				$return[]=$rs->id_cat_serv;
			}
			return($return);
		}else{
			return(array());
		}
	}

	function cargar_subcategorias_pro($id){
		$this->db->where("id_pro", $id);
		$query=$this->db->get("proyectos_x_servsubcat");
		$result=$query->result();
		if(is_array($result) && sizeof($result)>0){
			$return=array();
			foreach($result as $rs){
				$return[]=$rs->id_sub_serv;
			}
			return($return);
		}else{
			return(array());
		}
	}

	function cargar_servicios($id){
		$this->db->where("id_pro", $id);
		$query=$this->db->get("proyectos_x_servicios");
		$result=$query->result();
		if(is_array($result) && sizeof($result)>0){
			$return=array();
			foreach($result as $rs){
				$return[]=$rs->id_serv;
			}
			return($return);
		}else{
			return(array());
		}
	}
/********JOMP*/
	function consulta_diferido($id){
		$this->db->where('id_pro',$id);
		$consulta = $this->db->get("proyectos");
		$result=$consulta->result();
		return $result;
	}
	function actualiza_diferido($id, $data){
		$this->db->where('id_pro', $id);
		$this->db->update('proyectos', $data);
	}

//*******JOMP
	function cargar_hitos($id){
		$query=$this->db->where('proyectos.id_pro', $id);
		$query=$this->db->join("proyectos_x_hitos", "proyectos_x_hitos.id_pro = proyectos.id_pro");
		$query=$this->db->join("proyectos_hitos", "proyectos_hitos.id_hito = proyectos_x_hitos.id_hito");
		$query=$this->db->get("proyectos");
		$result=$query->result();
		return $result;
	}

	function agregar_hitos($datos){
		$this->db->insert('proyectos_x_hitos', $datos);
		return $this->db->insert_id();
	}

	function borrar_hitos($id){
		if($id!=""){
			$query=$this->db->where('id_proyxhito',$id);
			if($this->db->delete('proyectos_x_hitos')){
				return(true);
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}

	function agregar_oportunidad($datos){
		$datos['id_user']=$this->session->userdata('id_login');
		$this->db->insert('oportunidadesNegocios', $datos);
		return $this->db->insert_id();
	}

	function guardar_html_proyecto($id, $html){
		$datos=array();
		$datos["html_confluence_pro"]=$html;
		$this->db->where('id_pro', $id);
		$this->db->update('proyectos', $datos);
		return(true);
	}


function trae_tipo_proyecto($id){
	$sql="SELECT
   proyectos_tipo.Nombre_tipo
FROM
    portalminero.proyectos_x_tipo
    INNER JOIN portalminero.proyectos_tipo 
        ON (proyectos_x_tipo.id_tipo = proyectos_tipo.id_tipo)
WHERE (proyectos_x_tipo.id_pro = $id);";
$linea="";
 $query = $this->db->query($sql);
			
            foreach ($query->result() as $row)
			{					
				$linea=$linea.$row->Nombre_tipo."";

			}
return($linea);

}



/*********************************************************************************************************************************************/
/*****************************************  PRUEBA EPF                          *************************************************************/
/*********************************************************************************************************************************************/
function generar_ficha_html_EPF($id, $titulo=""){


 $html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);

		$proyecto=$this->mostrar_proyecto_full($id);
	
		$url=$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';

		


             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/





$template=str_replace("@ultima_informacion",  utf8_decode($proyecto->ultima_informacion_pro) ,$template);
$template=str_replace("@descripcion",  utf8_decode($proyecto->Desc_pro) ,$template);

$template=str_replace("@titulo_inversion",    "Inversion" ,$template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);



if($proyecto->Etapa_actual_pro==1){ $laetapa="Exploracion";}
if($proyecto->Etapa_actual_pro==2){ $laetapa="Ingenieria Conceptual o Prefactibilidad ";}
if($proyecto->Etapa_actual_pro==3){ $laetapa="Ingenieria Basica o Factibilidad ";}
if($proyecto->Etapa_actual_pro==6){ $laetapa="Ingenieria de Detalle";}
if($proyecto->Etapa_actual_pro==7){ $laetapa="Construccion y Montaje";}
if($proyecto->Etapa_actual_pro==8){ $laetapa="Operacion";}

 $template=str_replace("@etapa_actual",  $laetapa ,$template);




		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);


		$dire = utf8_decode($proyecto->Direccion_pro);
		$template=str_replace("@direccion", $dire, $template);


		
		
		$template=str_replace("@nombre_sector", utf8_decode($proyecto->Nombre_sector), $template);
        $template=str_replace("@region", utf8_decode($proyecto->Nombre_region), $template);
        $template=str_replace("@comuna", utf8_decode($proyecto->Nombre_comuna), $template);
        $template=str_replace("@pais", utf8_decode($proyecto->Nombre_pais), $template);
        $linea = $this->trae_tipo_proyecto($id);
        $linea = "<a href='#'>$linea</a>";
        $template=str_replace("@tipos", utf8_decode($linea), $template);

		/*Fin mano de obra*/
		
		

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		//echo $params_confluence;
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence_dns.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  $ht->Nombre_hito, $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", utf8_decode($html_hitos), $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}
			
		

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  $st->Nombre_etapa, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row("", utf8_decode($html_etapas), "etapas", $template, "si",1);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			
			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=$this->params->br2nl($licitacion->Nombre_lici_completo);

					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", utf8_decode($html_lic), "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			
		
			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=$this->params->br2nl($adjudicacion->nombre_adj);
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", utf8_decode($html_adj), "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=$this->params->br2nl($prop->Nombre_fantasia_emp);
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", utf8_decode($html_propietarios), "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			
		
			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					$value=$this->params->br2nl($tipo->Nombre_tipo);
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", utf8_decode($html_tipos), "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

	
		
			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=$this->params->br2nl($obra->Nombre_obra);
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", utf8_decode($html_obras), "obras", $template, "si", 1);
				//$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

		
			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=utf8_decode($this->params->br2nl($equipo->Nombre_equipo));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=utf8_decode($this->params->br2nl($suministro->Nombre_sumin));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			
			
			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=$this->params->br2nl($servicio->Nombre_serv);
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=$this->params->br2nl($c->Nombre_cat_serv);
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=$this->params->br2nl($sc->Nombre_sub_serv);
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", utf8_decode($html_servicios), "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}
			

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=utf8_decode($etapa->Nombre_etapa);
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=$e->Nombre_fantasia_emp;
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}
			
						
			
		
			
//echo $proyecto->Historial_pro;
			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", $proyecto->Nombre_generico_pro, "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				$value1=$proyecto->Nombre_sector;
				$value="<a href='".$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", utf8_decode($proyecto->Oport_neg_pro), "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=$proyecto->Nombre_pais;
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=$proyecto->Nombre_region;
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=$proyecto->Nombre_comuna;
				$template=$this->create_row("Comuna",  $value, "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", $proyecto->Direccion_pro, "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}

			if($proyecto->Nombre_fantasia_emp!=""){
				$value=$this->params->br2nl($proyecto->Nombre_fantasia_emp);
				$filtro="mandante_".$proyecto->id_man_emp;
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				$template=$this->create_row("Mandante", utf8_decode($value), "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", utf8_decode($proyecto->Nombre_pro), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

		
			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", $this->params->br2nl($proyecto->Desc_pro), "descripcion", $template, "si", 1);

			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", $proyecto->detalle_equipos, "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", $proyecto->detalle_suministros, "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

		
			$tmp_contact='';
			$item=0;
			
			

			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							
							
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Nombre_contact).'</td>
							
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Empresa_contact).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Cargo_contact).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Email_contact).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Telefono_contact).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Direccion_contact).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

							

			
					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", $tmp_contact , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}


			

			if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}

			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

		
		

			
			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
				$template=$this->create_row("", $etapa_act, "nombre_etapa", $template, "si");
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", $proyecto->Nombre_pro, $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			
		$template = utf8_encode($template);
         //   $template = str_replace("?", "-**0.*",$template);
            $template = str_replace("é", "-**1.*",$template);
			$template = str_replace("á", "-**2.*",$template);
			$template = str_replace("í", "-**3.*",$template);
			$template = str_replace("ó", "-**4.*",$template);
			$template = str_replace("ú", "-**5.*",$template);
			$template = str_replace("ñ", "-**6.*",$template);
			$template = str_replace("à", "-**7.*",$template);
			$template = str_replace("Á", "-**8.*",$template);

			$template = str_replace("É", "-**9.*",$template);
			$template = str_replace("Í", "-**10.*",$template);
			$template = str_replace("Ó", "-**11.*",$template);
			$template = str_replace("Ú", "-**12.*",$template);
			$template = str_replace("/", "-**13.*",$template);
			$template = str_replace("--", "-**14.*",$template);



			
				

            $template = preg_replace('/[\x80-\xFF]/', '', $template);

      //      $template = str_replace('-**0.*', '"', $template);
            $template = str_replace("-**1.*", "é", $template);
			$template = str_replace("-**2.*", "á", $template);
			$template = str_replace("-**3.*", "í", $template);
			$template = str_replace("-**4.*", "ó", $template);
			$template = str_replace("-**5.*", "ú", $template);
			$template = str_replace("-**6.*", "ñ", $template);
			$template = str_replace("-**7.*", "à", $template);
            $template = str_replace("-**8.*", "Á", $template);



			$template = str_replace("-**9.*", "É", $template);
			$template = str_replace("-**10.*", "Í", $template);
			$template = str_replace("-**11.*", "Ó", $template);
			$template = str_replace("-**12.*", "Ú", $template);
			$template = str_replace("-**13.*", "/", $template);
$template = str_replace("-**14.*", "/",$template);
$template = str_replace("-**13.*", "/", $template);
$template = str_replace("/ /", "/",$template);
			$template = str_replace("Fecha Actualizacion", "Fecha Actualización", $template);



           /* $this->guardar_html_proyecto($proyecto->id_pro, $template);*/


echo  $template ;
exit;

			return($template);
		}else{
			return(false);
		}
	}


/******************************************************************************************************************************************************************************************/
/********************************************************************* FIN PRUEBA EPF ************************************************************************************************/
/******************************************************************************************************************************************************************************************/



	function generar_ficha_html_vieja($id, $titulo=""){
		$html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);
		$template=file_get_contents(base_url().$this->params->ficha_template);

		$template = preg_replace('/[\x80-\xFF]/', '', $template);

		
		$proyecto=$this->mostrar_proyecto_full($id);
		$url=$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];
//print_r($url) ;
//die();	
		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';
		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}
		
		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);
		
		/*Fin mano de obra*/
		
		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;idedita=".$proyecto->id_usuario_modifica;
			
			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  htmlentities(utf8_decode($ht->Nombre_hito)), $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", $html_hitos, $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  htmlentities(utf8_decode($st->Nombre_etapa)), $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					$template=str_replace("@etapas", $html_etapas, $template);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($licitacion->Nombre_lici_completo))));
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
						
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";
				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", $html_lic, "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}
			
			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($adjudicacion->descripcion_adj))));
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", $html_adj, "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}
			
			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($prop->Nombre_fantasia_emp))));
					if($html_propietarios!=""){
						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", $html_propietarios, "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}
			
			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($tipo->Nombre_tipo))));
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", $html_tipos, "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}
			
			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($obra->Nombre_obra))));
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}
			
			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($equipo->Nombre_equipo))));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}
			
			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($suministro->Nombre_sumin))));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}
			
			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($servicio->Nombre_serv))));
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";
					
					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($c->Nombre_cat_serv))));
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($sc->Nombre_sub_serv))));
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";							
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", $html_servicios, "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}
	
			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=htmlentities(html_entity_decode(utf8_decode($etapa->Nombre_etapa)));
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}
				
				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp))));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=htmlentities(html_entity_decode(utf8_decode($e->Nombre_fantasia_emp)));
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}
			
			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}
			
			if($proyecto->Fecha_actualizacion_pro!=""){
				$template=$this->create_row("Fecha Actualización", $proyecto->Fecha_actualizacion_pro, "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}
			
			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}
			
			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", utf8_decode($proyecto->Nombre_generico_pro), "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}
			
			if($proyecto->Nombre_sector!=""){
				$value1=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_sector)));
				$value="<a href='".$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}
			
			if($proyecto->Produccion_pro!=""){
				$template=$this->create_row("Producción", $proyecto->Produccion_pro, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}
			
			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}
			
			if($proyecto->Nombre_pais!=""){
				$value=html_entity_decode(utf8_decode($proyecto->Nombre_pais));
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
//print_r($value);
//die();
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_region)));
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=html_entity_decode($proyecto->Nombre_comuna);
				$template=$this->create_row("Comuna",  utf8_decode($value), "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}
			
			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", utf8_decode($proyecto->Direccion_pro), "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}
			
			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}
			
			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}
			
			if($proyecto->Nombre_fantasia_emp!=""){
				$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($proyecto->Nombre_fantasia_emp))));
				$filtro="mandante_".$proyecto->id_man_emp;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("Mandante", $value, "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}
			
			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", utf8_decode($proyecto->Nombre_pro), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			} 
			
			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($proyecto->Desc_pro)))), "descripcion", $template, "si", 1);
			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}
			
			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", utf8_decode($proyecto->detalle_equipos), "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}
			
			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", utf8_decode($proyecto->detalle_suministros), "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}
			
			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión US$ MM", $proyecto->Inversion_pro, "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión US$ MM", "", "inversion", $template, "no");
			}
			
			if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}
			
			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}
			
			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}
			
			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}
			
			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}
			
			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}
			
			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}
			
			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}
			
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{ 
				$template=$this->create_row("", "", "region", $template, "no");
			}
			
			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}
			
			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

			if(isset($etapa_act))
				$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);
			$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", utf8_encode($proyecto->Nombre_pro), $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			//$this->guardar_html_proyecto($proyecto->id_pro, $template);


            
            

			echo $template;
			exit;
			//return($template);
		}else{
			return(false);
		}		
	}

    function william($texto){

        return($texto);
    }
	function generar_ficha_html_12_06_19($id, $titulo=""){
		$html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);

		$proyecto=$this->mostrar_proyecto_full($id);
		$url=$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';



             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/



		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);

		/*Fin mano de obra*/

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  htmlentities(utf8_decode($ht->Nombre_hito)), $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", $html_hitos, $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  htmlentities(utf8_decode($st->Nombre_etapa)), $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row("", $html_etapas, "etapas", $template, "si",1);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($licitacion->Nombre_lici_completo))));
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", $html_lic, "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($adjudicacion->nombre_adj))));
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", $html_adj, "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($prop->Nombre_fantasia_emp))));
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", $html_propietarios, "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					//$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($tipo->Nombre_tipo))));
					$value=htmlentities(html_entity_decode($this->params->br2nl($tipo->Nombre_tipo)));
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", $html_tipos, "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($obra->Nombre_obra))));
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($equipo->Nombre_equipo))));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($suministro->Nombre_sumin))));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($servicio->Nombre_serv))));
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($c->Nombre_cat_serv))));
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($sc->Nombre_sub_serv))));
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", $html_servicios, "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=htmlentities(html_entity_decode(utf8_decode($etapa->Nombre_etapa)));
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp))));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=htmlentities(html_entity_decode(utf8_decode($e->Nombre_fantasia_emp)));
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}

			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", utf8_decode($proyecto->Nombre_generico_pro), "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				//$value1=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_sector)));
				$value1=htmlentities(html_entity_decode($proyecto->Nombre_sector));
				$value="<a href='".$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				//echo ($value);
				//exit;
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=html_entity_decode(utf8_decode($proyecto->Nombre_pais));
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_region)));
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=html_entity_decode($proyecto->Nombre_comuna);
				$template=$this->create_row("Comuna",  utf8_decode($value), "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", utf8_decode($proyecto->Direccion_pro), "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}

			if($proyecto->Nombre_fantasia_emp!=""){
				$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($proyecto->Nombre_fantasia_emp))));
				$filtro="mandante_".$proyecto->id_man_emp;
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("Mandante", $value, "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_pro))), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", htmlentities(html_entity_decode($this->params->br2nl($proyecto->Desc_pro))), "descripcion", $template, "si", 1);
			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", utf8_decode($proyecto->detalle_equipos), "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", utf8_decode($proyecto->detalle_suministros), "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

			$tmp_contact='';
			$item=0;
			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Nombre_contact)).'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Empresa_contact)).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Cargo_contact)).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Email_contact)).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Telefono_contact)).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Direccion_contact)).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Nombre_contact)).'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Empresa_contact)).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Cargo_contact)).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Email_contact)).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Telefono_contact)).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Direccion_contact)).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", utf8_decode($tmp_contact) , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}

			/*if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}*/

			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
				$template=$this->create_row("", htmlentities(utf8_decode($etapa_act)), "nombre_etapa", $template, "si");
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_pro))), $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			//$this->guardar_html_proyecto($proyecto->id_pro, $template);
echo $template;
exit;
			return($template);
		}else{
			return(false);
		}
	}
/************************************************** */
/***************cambio desde 12-06-2019   ********* */
/************************************************** */
	function generar_ficha_html($id, $titulo=""){
		$html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);

		$proyecto=$this->mostrar_proyecto_full($id);
		$url=$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';



             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/



		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);

		/*Fin mano de obra*/

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  htmlentities($this->william($ht->Nombre_hito)), $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", $html_hitos, $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  htmlentities($this->william($st->Nombre_etapa)), $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row1("", $html_etapas, "etapas", $template, "si",1);
				}else{
					$template=$this->create_row1("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row1("Etapas", "", "etapas", $template, "no");
			}

			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($licitacion->Nombre_lici_completo))));
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", $html_lic, "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($adjudicacion->nombre_adj))));
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", $html_adj, "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($prop->Nombre_fantasia_emp))));
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", $html_propietarios, "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					//$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($tipo->Nombre_tipo))));
					$value=htmlentities(html_entity_decode($this->params->br2nl($tipo->Nombre_tipo)));
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", $html_tipos, "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($obra->Nombre_obra))));
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($equipo->Nombre_equipo))));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($suministro->Nombre_sumin))));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($servicio->Nombre_serv))));
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=htmlentities(html_entity_decode($this->william($this->params->br2nl($c->Nombre_cat_serv))));
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=htmlentities(html_entity_decode($this->william($this->params->br2nl($sc->Nombre_sub_serv))));
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", $html_servicios, "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=htmlentities(html_entity_decode($this->william($etapa->Nombre_etapa)));
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row1("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row1("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($etapa->Nombre_fantasia_emp))));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=htmlentities(html_entity_decode($this->william($e->Nombre_fantasia_emp)));
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}

			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("&", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row1("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row1("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", $this->william($proyecto->Nombre_generico_pro), "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				//$value1=htmlentities(html_entity_decode($this->william($proyecto->Nombre_sector)));
				$value1=htmlentities(html_entity_decode($proyecto->Nombre_sector));
				$value="<a href='".$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				//echo ($value);
				//exit;
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=html_entity_decode($this->william($proyecto->Nombre_pais));
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=htmlentities(html_entity_decode($this->william($proyecto->Nombre_region)));
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=html_entity_decode($proyecto->Nombre_comuna);
				$template=$this->create_row("Comuna",  $this->william($value), "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", $this->william($proyecto->Direccion_pro), "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}

			if($proyecto->Nombre_fantasia_emp!=""){
				$value=htmlentities(html_entity_decode($this->william($this->params->br2nl($proyecto->Nombre_fantasia_emp))));
				$filtro="mandante_".$proyecto->id_man_emp;
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("Mandante", $value, "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", htmlentities(html_entity_decode($this->william($proyecto->Nombre_pro))), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", htmlentities(html_entity_decode($this->params->br2nl($proyecto->Desc_pro))), "descripcion", $template, "si", 1);
			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", $this->william($proyecto->detalle_equipos), "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", $this->william($proyecto->detalle_suministros), "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

			$tmp_contact='';
			$item=0;
			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Nombre_contact)).'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Empresa_contact)).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Cargo_contact)).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Email_contact)).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Telefono_contact)).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Direccion_contact)).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Nombre_contact)).'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Empresa_contact)).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Cargo_contact)).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Email_contact)).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Telefono_contact)).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities($this->william($ct->Direccion_contact)).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", $this->william($tmp_contact) , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}

			/*if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}*/

			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row1("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row1("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row1("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row1("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row1("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row1("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row1("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row1("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities($this->william($etapa_act)), $template);
				$template=$this->create_row("", htmlentities($this->william($etapa_act)), "nombre_etapa", $template, "si");
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", htmlentities(html_entity_decode($this->william($proyecto->Nombre_pro))), $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			//$this->guardar_html_proyecto($proyecto->id_pro, $template);
//echo $template;
//exit;
			return($template);
		}else{
			return(false);
		}
	}


/*******************************************************************************************************************************************************************/
/**********************************************************  VALIDO PORTAL *******************************************************************************/
/*******************************************************************************************************************************************************************/
function generar_ficha_html_valido($id, $titulo=""){


 $html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);

		$proyecto=$this->mostrar_proyecto_full($id);
	
		//$url=$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];
		$url=$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';

		


             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/





$template=str_replace("@ultima_informacion",  utf8_decode($proyecto->ultima_informacion_pro) ,$template);
$template=str_replace("@descripcion",  utf8_decode($proyecto->Desc_pro) ,$template);

$template=str_replace("@titulo_inversion",    "Inversion" ,$template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);



if($proyecto->Etapa_actual_pro==1){ $laetapa="Exploracion";}
if($proyecto->Etapa_actual_pro==2){ $laetapa="Ingenieria Conceptual o Prefactibilidad ";}
if($proyecto->Etapa_actual_pro==3){ $laetapa="Ingenieria Basica o Factibilidad ";}
if($proyecto->Etapa_actual_pro==6){ $laetapa="Ingenieria de Detalle";}
if($proyecto->Etapa_actual_pro==7){ $laetapa="Construccion y Montaje";}
if($proyecto->Etapa_actual_pro==8){ $laetapa="Operacion";}

 $template=str_replace("@etapa_actual",  $laetapa ,$template);




		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);


		$dire = utf8_decode($proyecto->Direccion_pro);
		$template=str_replace("@direccion", $dire, $template);


		
		
		$template=str_replace("@nombre_sector", utf8_decode($proyecto->Nombre_sector), $template);
        $template=str_replace("@region", utf8_decode($proyecto->Nombre_region), $template);
        $template=str_replace("@comuna", utf8_decode($proyecto->Nombre_comuna), $template);
        $template=str_replace("@pais", utf8_decode($proyecto->Nombre_pais), $template);
        $linea = $this->trae_tipo_proyecto($id);
        $linea = "<a href='#'>$linea</a>";
        $template=str_replace("@tipos", utf8_decode($linea), $template);

		/*Fin mano de obra*/
		
		

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		//echo $params_confluence;
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence_dns.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  $ht->Nombre_hito, $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", utf8_decode($html_hitos), $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}
			
		

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  $st->Nombre_etapa, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row("", utf8_decode($html_etapas), "etapas", $template, "si",1);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			
			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=$this->params->br2nl($licitacion->Nombre_lici_completo);
			;
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", utf8_decode($html_lic), "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			
		
			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=$this->params->br2nl($adjudicacion->nombre_adj);
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", utf8_decode($html_adj), "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=$this->params->br2nl($prop->Nombre_fantasia_emp);
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", utf8_decode($html_propietarios), "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			
		
			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					$value=$this->params->br2nl($tipo->Nombre_tipo);
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", utf8_decode($html_tipos), "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

	
		
			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=$this->params->br2nl($obra->Nombre_obra);
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", utf8_decode($html_obras), "obras", $template, "si", 1);
				//$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

		
			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=utf8_decode($this->params->br2nl($equipo->Nombre_equipo));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=utf8_decode($this->params->br2nl($suministro->Nombre_sumin));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			
			
			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=$this->params->br2nl($servicio->Nombre_serv);
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=$this->params->br2nl($c->Nombre_cat_serv);
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=$this->params->br2nl($sc->Nombre_sub_serv);
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", utf8_decode($html_servicios), "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}
			

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=utf8_decode($etapa->Nombre_etapa);
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=$e->Nombre_fantasia_emp;
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}
			
						
			
		
			
//echo $proyecto->Historial_pro;
			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", $proyecto->Nombre_generico_pro, "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				$value1=$proyecto->Nombre_sector;
				$value="<a href='".$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", utf8_decode($proyecto->Oport_neg_pro), "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=$proyecto->Nombre_pais;
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=$proyecto->Nombre_region;
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=$proyecto->Nombre_comuna;
				$template=$this->create_row("Comuna",  $value, "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", $proyecto->Direccion_pro, "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}
// URL DE LINKS MANDANTE
			if($proyecto->Nombre_fantasia_emp!=""){
				$value=$this->params->br2nl($proyecto->Nombre_fantasia_emp);
				$filtro="mandante_".$proyecto->id_man_emp;
				
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				$template=$this->create_row("Mandante", utf8_decode($value), "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", utf8_decode($proyecto->Nombre_pro), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

		
			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", $this->params->br2nl($proyecto->Desc_pro), "descripcion", $template, "si", 1);

			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", $proyecto->detalle_equipos, "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", $proyecto->detalle_suministros, "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

		
			$tmp_contact='';
			$item=0;
			
			

			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							
							
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Nombre_contact).'</td>
							
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Empresa_contact).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Cargo_contact).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Email_contact).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Telefono_contact).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Direccion_contact).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

							

			
					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", $tmp_contact , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}


			

			if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}

			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

		
		

			
			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
				$template=$this->create_row("", $etapa_act, "nombre_etapa", $template, "si");
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				
			$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", $proyecto->Nombre_pro, $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			
		    $template = utf8_encode($template);
         //   $template = str_replace("?", "-**0.*",$template);
            $template = str_replace("é", "-**1.*",$template);
			$template = str_replace("á", "-**2.*",$template);
			$template = str_replace("í", "-**3.*",$template);
			$template = str_replace("ó", "-**4.*",$template);
			$template = str_replace("ú", "-**5.*",$template);
			$template = str_replace("ñ", "-**6.*",$template);
			$template = str_replace("à", "-**7.*",$template);
			$template = str_replace("Á", "-**8.*",$template);

			$template = str_replace("É", "-**9.*",$template);
			$template = str_replace("Í", "-**10.*",$template);
			$template = str_replace("Ó", "-**11.*",$template);
			$template = str_replace("Ú", "-**12.*",$template);
			$template = str_replace("/", "-**13.*",$template);
			$template = str_replace("--", "-**14.*",$template);



			
				

            $template = preg_replace('/[\x80-\xFF]/', '', $template);

      //      $template = str_replace('-**0.*', '"', $template);
            $template = str_replace("-**1.*", "é", $template);
			$template = str_replace("-**2.*", "á", $template);
			$template = str_replace("-**3.*", "í", $template);
			$template = str_replace("-**4.*", "ó", $template);
			$template = str_replace("-**5.*", "ú", $template);
			$template = str_replace("-**6.*", "ñ", $template);
			$template = str_replace("-**7.*", "à", $template);
            $template = str_replace("-**8.*", "Á", $template);



			$template = str_replace("-**9.*", "É", $template);
			$template = str_replace("-**10.*", "Í", $template);
			$template = str_replace("-**11.*", "Ó", $template);
			$template = str_replace("-**12.*", "Ú", $template);
			$template = str_replace("-**13.*", "/", $template);
			$template = str_replace("-**14.*", "/",$template);
			$template = str_replace("-**13.*", "/", $template);
			$template = str_replace("/ /", "/",$template);
			$template = str_replace("Fecha Actualizacion", "Fecha Actualización", $template);



  //          $this->guardar_html_proyecto($proyecto->id_pro, $template);

			$template = utf8_encode(utf8_decode($template));
  			//$template = utf8_decode($template);
 //echo $template;
//exit; 
			return($template);

/* 			$template = utf8_decode($template);
			return($template); */
		}else{
			return(false);
		}
	}


	function generar_ficha_html_prueba($id, $titulo=""){


		$html_propietarios="";
			   $html_tipos="";
			   $html_obras="";
			   $html_equipos="";
			   $html_suministros="";
			   $html_servicios="";
			   $html_hitos="";
			   $html_etapas="";
			   $html_ubicacion="";
			   $nom_xml=$this->get_xml_file($id);
	   
			   $template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);
	   
			   $proyecto=$this->mostrar_proyecto_full($id);
		   
			   //$url=$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];
			   $url=$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];
	   
			   /*Mano de obra*/
			   $mostrar_mo=0;
			   $mano_obra='';
			   $item_mo='';
	   
			   
	   
	   
					/* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
			   if( $this->tiene_rca($id)  > 0) {
					$htm_timbre  = "
					<DIV class='iconos_licitaciones'>
					  <DIV class='imagen_lici'>
						<img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
					  </DIV>
					</DIV>";
					$template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);
	   
	   
			   }else{
	   
				   $template=str_replace("<!--@imgamb-->",  " " ,$template);
			   }
					/* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
	   
	   
	   
	   
	   
	   $template=str_replace("@ultima_informacion",  utf8_decode($proyecto->ultima_informacion_pro) ,$template);
	   $template=str_replace("@descripcion",  utf8_decode($proyecto->Desc_pro) ,$template);
	   
	   $template=str_replace("@titulo_inversion",    "Inversion" ,$template);
	   $template=str_replace("@titulo_descripcion",  "Descripcion", $template);
	   $template=str_replace("@titulo_descripcion",  "Descripcion", $template);
	   
	   
	   
	   if($proyecto->Etapa_actual_pro==1){ $laetapa="Exploracion";}
	   if($proyecto->Etapa_actual_pro==2){ $laetapa="Ingenieria Conceptual o Prefactibilidad ";}
	   if($proyecto->Etapa_actual_pro==3){ $laetapa="Ingenieria Basica o Factibilidad ";}
	   if($proyecto->Etapa_actual_pro==6){ $laetapa="Ingenieria de Detalle";}
	   if($proyecto->Etapa_actual_pro==7){ $laetapa="Construccion y Montaje";}
	   if($proyecto->Etapa_actual_pro==8){ $laetapa="Operacion";}
	   
		$template=str_replace("@etapa_actual",  $laetapa ,$template);
	   
	   
	   
	   
			   if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
				   $item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
				   $mostrar_mo=1;
			   }
			   if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
				   $item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
				   $mostrar_mo=1;
			   }
			   if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
				   $item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
				   $mostrar_mo=1;
			   }
	   
			   if($mostrar_mo==1){
				   $mano_obra='<tr style="">
							   <td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
							   <td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
						   </tr>';
			   }
			   $template=str_replace("@mano_obra", $mano_obra, $template);
	   
	   
			   $dire = utf8_decode($proyecto->Direccion_pro);
			   $template=str_replace("@direccion", $dire, $template);
	   
	   
			   
			   
			   $template=str_replace("@nombre_sector", utf8_decode($proyecto->Nombre_sector), $template);
			   $template=str_replace("@region", utf8_decode($proyecto->Nombre_region), $template);
			   $template=str_replace("@comuna", utf8_decode($proyecto->Nombre_comuna), $template);
			   $template=str_replace("@pais", utf8_decode($proyecto->Nombre_pais), $template);
			   $linea = $this->trae_tipo_proyecto($id);
			   $linea = "<a href='#'>$linea</a>";
			   $template=str_replace("@tipos", utf8_decode($linea), $template);
	   
			   /*Fin mano de obra*/
			   
			   
	   
			   $params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
			   //echo $params_confluence;
			   $url_js=$this->generar_grafico_js($proyecto->id_sector);
			   $template=str_replace("@url_js", $proyecto->id_sector, $template);
			   $template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
			   if($proyecto->Etapa_actual_pro!=0)
				   $url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);
	   
			   $tipos=$this->mostrar_tipos($id);
			   $obras=$this->mostrar_obras($id);
			   $equipos=$this->mostrar_equipos($id);
			   $suministros=$this->mostrar_suministros($id);
			   $servicios=$this->mostrar_servicios($id);
			   $cat=$this->mostrar_serv_cat($id);
			   $subcat=$this->mostrar_serv_subcat($id);
			   $licitaciones=$this->mostrar_licitaciones($id);
			   $adjudicaciones=$this->mostrar_adjudicaciones($id);
			   $hitos=$this->get_hitos($id);
			   $etapas=$this->get_etapas($id);
			   if(is_object($proyecto)){
				   $url_informa=$this->params->url_confluence_dns.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";
	   
				   $etapa=$this->cargar_datos_etapa_actual($id);
				   $space=$this->params->spaces_proy[$proyecto->id_sector];
				   $propietarios=$this->mostrar_propietarios($proyecto->id_pro);
	   
				   if(is_array($hitos) && sizeof($hitos)>0){
					   $html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
					   $html_hitos.='<tr>';
						   $html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
					   $html_hitos.='</tr>';
					   $html_hitos.='<tr>';
						   $html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
						   $html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
					   $html_hitos.='</tr>';
	   
					   foreach($hitos as $ht){
						   if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
							   $html_hitos.="<tr>";
							   $html_hitos.=str_replace("@text",  $ht->Nombre_hito, $this->params->formato_columna);
							   $html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
							   $html_hitos.="</tr>";
						   }
					   }
					   $html_hitos.="</table>";
					   if($html_hitos!=""){
						   $template=str_replace("@hitos", utf8_decode($html_hitos), $template);
						   $template=str_replace("@style_hitos", "", $template);
					   }else{
						   $template=$this->create_row("Hito", "", "hitos", $template, "no");
					   }
				   }else{
					   $template=$this->create_row("Hito", "", "hitos", $template, "no");
				   }
				   
			   
	   
				   if(is_array($etapas) && sizeof($etapas)>0){
					   $html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
					   $html_etapas.='<tr>';
						   $html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
					   $html_etapas.='</tr>';
					   $html_etapas.='<tr>';
						   $html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
						   $html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
						   $html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
					   $html_etapas.='</tr>';
					   foreach($etapas as $st){
						   if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
							   $html_etapas.="<tr>";
							   $html_etapas.=str_replace("@text",  $st->Nombre_etapa, $this->params->formato_columna);
							   $html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
							   $html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
							   $html_etapas.="</tr>";
						   }
					   }
					   $html_etapas.="</table>";
					   if($html_etapas!=""){
						   //$template=str_replace("@etapas", $html_etapas, $template);
						   $template=$this->create_row("", utf8_decode($html_etapas), "etapas", $template, "si",1);
					   }else{
						   $template=$this->create_row("Etapas", "", "etapas", $template, "no");
					   }
				   }else{
					   $template=$this->create_row("Etapas", "", "etapas", $template, "no");
				   }
	   
				   
				   if(is_array($licitaciones) && sizeof($licitaciones)>0){
					   $html_lic="";
					   $html_lic_est="";
					   $html_lic_def="";
					   $html_lic_adj="";
					   $html_lic_pro="";
					   foreach($licitaciones as $licitacion){
						   $value=$this->params->br2nl($licitacion->Nombre_lici_completo);
				   ;
						   if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
							   if($html_lic_est==""){
								   $html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
								   $html_lic_est.="<div style='clear:both;'>";
							   }
	   
							   if($licitacion->url_confluence_lici!="")
								   $value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
							   else
								   $value="<a class='label_content' href='#'>".$value."</a>";
							   $html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
	   
						   }elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
							   if($html_lic_def==""){
								   $html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
								   $html_lic_def.="<div style='clear:both;'>";
							   }
							   if($licitacion->url_confluence_lici!="")
								   $value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
							   else
								   $value="<a class='label_content' href='#'>".$value."</a>";
							   $html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
						   }elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
							   if($html_lic_adj==""){
								   $html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
								   $html_lic_adj.="<div style='clear:both;'>";
							   }
							   if($licitacion->url_confluence_lici!="")
								   $value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
							   else
								   $value="<a class='label_content' href='#'>".$value."</a>";
							   $html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
						   }else{
							   if($html_lic_pro==""){
								   $html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
								   $html_lic_pro.="<div style='clear:both;'>";
							   }
							   if($licitacion->url_confluence_lici!="")
								   $value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
							   else
								   $value="<a class='label_content' href='#'>".$value."</a>";
							   $html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
						   }
					   }
					   if($html_lic_def!=""){
						   $html_lic_def.="</div></div>";
					   }
					   if($html_lic_est!=""){
						   $html_lic_est.="</div></div>";
					   }
					   if($html_lic_adj!=""){
						   $html_lic_adj.="</div></div>";
	   
					   }
					   if($html_lic_pro!=""){
						   $html_lic_pro.="</div></div>";
					   }
					   $html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
					   $template=$this->create_row("Licitaciones Asociadas", utf8_decode($html_lic), "licitaciones", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
				   }
	   
				   
			   
				   if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
					   $html_adj="";
					   foreach($adjudicaciones as $adjudicacion){
						   $value=$this->params->br2nl($adjudicacion->nombre_adj);
						   if($adjudicacion->url_confluence_adj!="")
							   $value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='#'>".$value."</a>";
						   if($html_adj!=""){
							   $html_adj.="\n- ".$value;
						   }else{
							   $html_adj.="- ".$value;
						   }
					   }
					   $template=$this->create_row("Adjudicaciones Asociadas", utf8_decode($html_adj), "adjudicaciones", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
				   }
	   
				   if(is_array($propietarios) && sizeof($propietarios)>0){
					   $html_propietarios="";
					   foreach($propietarios as $prop){
						   $value=$this->params->br2nl($prop->Nombre_fantasia_emp);
						   if($html_propietarios!=""){
	   
							   $html_propietarios.="\n".$value;
						   }else{
							   $html_propietarios.=$value;
						   }
					   }
					   $template=$this->create_row("Propietarios", utf8_decode($html_propietarios), "propietarios", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
				   }
	   
				   
			   
				   if(is_array($tipos) && sizeof($tipos)>0){
					   $html_tipos="";
					   foreach($tipos as $tipo){
						   $value=$this->params->br2nl($tipo->Nombre_tipo);
						   $filtro="tipo_".$tipo->id_tipo;
						   if(strstr($url, "?"))
							   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   if($html_tipos!=""){
							   $html_tipos.="\n".$value;
						   }else{
							   $html_tipos.=$value;
						   }
					   }
					   $template=$this->create_row("Tipos", utf8_decode($html_tipos), "tipos", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
				   }
	   
		   
			   
				   if(is_array($obras) && sizeof($obras)>0){
					   $html_obras="";
					   foreach($obras as $obra){
						   $value=$this->params->br2nl($obra->Nombre_obra);
						   $filtro="obra_".$obra->id_obra;
						   if(strstr($url, "?"))
							   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   if($html_obras!=""){
							   $html_obras.="\n- ".$value;
						   }else{
							   $html_obras.="- ".$value;
						   }
					   }
					   $template=$this->create_row("Obras Principales", utf8_decode($html_obras), "obras", $template, "si", 1);
					   //$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
				   }
	   
			   
				   if(is_array($equipos) && sizeof($equipos)>0){
					   $html_equipos="";
					   foreach($equipos as $equipo){
						   $value=utf8_decode($this->params->br2nl($equipo->Nombre_equipo));
						   $filtro="equipo_".$equipo->id_equipo;
						   if(strstr($url, "?"))
							   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   if($html_equipos!=""){
							   $html_equipos.="\n- ".$value;
						   }else{
							   $html_equipos.="- ".$value;
						   }
					   }
					   $template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
				   }
	   
				   if(is_array($suministros) && sizeof($suministros)>0){
					   $html_suministros="";
					   foreach($suministros as $suministro){
						   $value=utf8_decode($this->params->br2nl($suministro->Nombre_sumin));
						   $filtro="suministro_".$suministro->id_sumin;
						   if(strstr($url, "?"))
							   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   if($html_suministros!=""){
							   $html_suministros.="\n- ".$value;
						   }else{
							   $html_suministros.="- ".$value;
						   }
					   }
					   $template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
				   }
	   
				   
				   
				   if(is_array($servicios) && sizeof($servicios)>0){
					   $html_servicios="";
					   $html_cat="";
					   foreach($servicios as $servicio){
						   $value=$this->params->br2nl($servicio->Nombre_serv);
						   $filtro="servicio_".$servicio->id_serv;
						   if(strstr($url, "?"))
							   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   $html_servicios.="<div style='clear:both;'>- ".$value."</div>";
	   
						   if(is_array($cat) && sizeof($cat)>0){
							   $html_cat="";
							   foreach($cat as $c){
								   if($c->id_serv==$servicio->id_serv){
									   $value2=$this->params->br2nl($c->Nombre_cat_serv);
									   $html_cat.="<div style='clear:both;'>- ".$value2."</div>";
								   }
								   if(is_array($subcat) && sizeof($subcat)>0){
									   $html_subcat="";
									   foreach($subcat as $sc){
										   if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
											   $value3=$this->params->br2nl($sc->Nombre_sub_serv);
											   if($value3!=""){
												   $html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
											   }
										   }
									   }
									   if($html_subcat!=""){
										   $html_cat.="<div style='padding-left:25px; clear:both;'>";
										   $html_cat.=$html_subcat."</div>";
									   }
								   }
							   }
							   if($html_cat!=""){
								   $html_servicios.="<div style='padding-left:15px; clear:both;'>";
								   $html_servicios.=$html_cat."</div>";
							   }
						   }
					   }
					   $template=$this->create_row("Servicios Principales", utf8_decode($html_servicios), "servicios", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
				   }
				   
	   
				   if(is_object($etapa)){
					   if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
						   $etapa_act=$etapa->Nombre_etapa;
						   $filtro="etapa_".$etapa->id_etapa;
						   $value=utf8_decode($etapa->Nombre_etapa);
						   if(strstr($url, "?"))
							   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   $template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
					   }else{
						   $template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
					   }
	   
					   if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
						   $template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
					   }else{
						   $template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
					   }
	   
					   if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
						   $value=utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp));
						   $emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
						   if(is_array($emp1)){
							   $value="";
							   foreach($emp1 as $e){
								   $valuet1=$e->Nombre_fantasia_emp;
								   $filtro="responsable_".$e->id_emp;
								   if($value==""){
									   if(strstr($url, "?"))
										   $value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
									   else
										   $value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								   }else{
									   if(strstr($url, "?"))
										   $value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
									   else
										   $value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								   }
							   }
						   }else if($emp1==true){
							   $filtro="responsable_".$etapa->id_emp;
							   if(strstr($url, "?"))
								   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
							   else
								   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
	   
						   }else{
							   $filtro="responsable_".$etapa->id_emp;
							   if(strstr($url, "?"))
								   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
							   else
								   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   }
						   $template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
					   }else{
						   $template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
					   }
				   }else{
					   $template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
					   $template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
					   $template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				   }
				   
							   
				   
			   
				   
	   //echo $proyecto->Historial_pro;
				   if($proyecto->Historial_pro!=""){
					   $proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
					   $proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
					   $proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
					   $proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
					   $proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
					   $template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
				   }else{
					   $template=$this->create_row("Historial", "", "historial", $template, "no");
				   }
	   
				   if($proyecto->Fecha_actualizacion_pro!=""){
					   $fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
					   
					   $template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
				   }else{
					   $template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
				   }
	   
				   if($proyecto->Oport_neg_pro!=""){
					   $template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
				   }else{
					   $template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
				   }
	   
				   if($proyecto->Nombre_generico_pro!=""){
					   $template=$this->create_row("Nombre Genérico", $proyecto->Nombre_generico_pro, "nombre_generico", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
				   }
	   
				   if($proyecto->Nombre_sector!=""){
					   $value1=$proyecto->Nombre_sector;
					   $value="<a href='".$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
					   $template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
					   $template=str_replace("@_nombre_sector2", $value1, $template);
				   }else{
					   $template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
					   $template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
				   }
	   
				   //modificado 20-05-2014
				   if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
					   $texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
					   $template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
				   }else{
					   $template=$this->create_row("Producción", "", "produccion", $template, "no");
				   }
	   
				   if($proyecto->Oport_neg_pro!=""){
					   $template=$this->create_row("Oportunidad de Negocio", utf8_decode($proyecto->Oport_neg_pro), "oportunidad_negocio", $template, "si");
				   }else{
					   $template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
				   }
	   
				   if($proyecto->Nombre_pais!=""){
					   $value=$proyecto->Nombre_pais;
					   $filtro="pais_".$proyecto->id_pais_p;
					   if(strstr($url, "?"))
						   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					   else
						   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					   $template=$this->create_row("País",  $value, "pais", $template, "si", 1);
				   }else{
					   $template=$this->create_row("País", "", "pais", $template, "no");
				   }
				   if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
					   $value=$proyecto->Nombre_region;
					   if($proyecto->id_pais_p==$this->params->id_pais_chile){
						   $filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
						   if(strstr($url, "?"))
							   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						   else
							   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					   }
					   $template=$this->create_row("Región", $value, "region", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Región", "", "region", $template, "no");
				   }
	   
				   if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
					   $value=$proyecto->Nombre_comuna;
					   $template=$this->create_row("Comuna",  $value, "comuna", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Comuna",  "", "comuna", $template, "no");
				   }
	   
				   if($proyecto->Direccion_pro!=""){
					   $template=$this->create_row("Dirección", $proyecto->Direccion_pro, "direccion", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Dirección", "", "direccion", $template, "no");
				   }
	   
				   if($proyecto->Latitud_pro!=""){
					   $template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
				   }else{
					   $template=$this->create_row("latitud", "", "latitud", $template, "no");
				   }
	   
				   if($proyecto->Longitud_pro!=""){
					   $template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
				   }else{
					   $template=$this->create_row("Longitud", "", "longitud", $template, "no");
				   }
	   // URL DE LINKS MANDANTE
				   if($proyecto->Nombre_fantasia_emp!=""){
					   $value=$this->params->br2nl($proyecto->Nombre_fantasia_emp);
					   $filtro="mandante_".$proyecto->id_man_emp;
					   
					   if(strstr($url, "?"))
	   
						   $value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					   else
						   $value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					   $template=$this->create_row("Mandante", utf8_decode($value), "mandante", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Mandante", "", "mandante", $template, "no");
				   }
	   
				   if($proyecto->Nombre_pro!=""){
					   $template=str_replace("@titulo_pag", utf8_decode($proyecto->Nombre_pro), $template);
				   }else{
					   $template=$this->create_row("", "", "titulo", $template, "no");
				   }
	   
			   
				   if($proyecto->Desc_pro!=""){
					   $proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
					   $proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
					   $proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
					   $proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
					   $template=$this->create_row("Descripción del Proyecto", $this->params->br2nl($proyecto->Desc_pro), "descripcion", $template, "si", 1);
	   
				   }else{
					   $template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
				   }
	   
				   if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
					   $template=$this->create_row("Detalle Equipos", $proyecto->detalle_equipos, "detalle_equipos", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
				   }
	   
				   if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
					   $template=$this->create_row("Detalle Suministros", $proyecto->detalle_suministros, "detalle_sumin", $template, "si", 1);
				   }else{
					   $template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
				   }
	   
				   if($proyecto->Inversion_pro!=""){
					   $template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
				   }else{
					   $template=$this->create_row("Inversión", "", "inversion", $template, "no");
				   }
	   
	   
				   $contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
				   $contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);
	   
			   
				   $tmp_contact='';
				   $item=0;
				   
				   
	   
				   if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
					   if((is_array($contactos_princ))&&($contactos_princ!='')){
						   foreach($contactos_princ as $ct){
								if($item==0){
								   $tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
									$tmp_contact.='Contacto Principal';
									$item=1;
								   $tmp_contact.='</span>';
								}
	   
							   $tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
							   if($ct->Nombre_contact!=''){
							   $tmp_contact.='<tr>
								   
								   
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Nombre_contact).'</td>
								   
							   </tr>';
							   }
	   
							   if($ct->Empresa_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Empresa_contact).'</td>
							   </tr>';
							   }
	   
							   if($ct->Cargo_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Cargo_contact).'</td>
							   </tr>';
							   }
	   
							   if($ct->Email_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Email_contact).'</td>
							   </tr>';
							   }
	   
							   if($ct->Telefono_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Telefono_contact).'</td>
							   </tr>';
							   }
	   
							   if($ct->Direccion_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Direccion_contact).'</td>
							   </tr>';
							   }
	   
						   $tmp_contact.='</table></div>';
						   }
					   }
	   
								   
	   
				   
						   $item=0;
	   
						   if((is_array($contactos_secun))&&($contactos_secun!='')){
						   foreach($contactos_secun as $ct){
	   
								if($item==0){
								   $tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
									$tmp_contact.='Otros Contactos';
									$item=1;
									$tmp_contact.='</span>';
								}
	   
							   $tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
							   if($ct->Nombre_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
							   </tr>';
							   }
	   
							   if($ct->Empresa_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
							   </tr>';
							   }
	   
							   if($ct->Cargo_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
							   </tr>';
							   }
	   
							   if($ct->Email_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
							   </tr>';
							   }
	   
							   if($ct->Telefono_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
							   </tr>';
							   }
	   
							   if($ct->Direccion_contact!=''){
							   $tmp_contact.='<tr>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
								   <td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
							   </tr>';
							   }
	   
						   $tmp_contact.='</table></div>';
						   }
					   }
						   $template=str_replace("@contactos", $tmp_contact , $template);
						   $template=str_replace("@style_contactos",'', $template);
				  }
				   else{
					   $template=str_replace("@contactos", $tmp_contact , $template);
					   $template=str_replace("@style_contactos",'display:none;', $template);
				   }
	   
	   
				   
	   
				   if($proyecto->Nombre_contacto_pro!=""){
					   $template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
				   }else{
					   $template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
				   }
	   
				   if($proyecto->Empresa_contacto_pro!=""){
					   $template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
				   }else{
					   $template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
				   }
	   
				   if($proyecto->Cargo_contacto_pro!=""){
					   $template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
				   }else{
					   $template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
				   }
	   
				   if($proyecto->Email_contacto_pro!=""){
					   $template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
				   }else{
					   $template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
				   }
	   
				   if($proyecto->Direccion_contacto_pro!=""){
					   $template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
				   }else{
					   $template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
				   }
	   
				   if($proyecto->Telefono_contacto_pro!=""){
					   $template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
				   }else{
					   $template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
				   }
	   
				   if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
					   $template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
				   }else{
					   $template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
				   }
	   
				   if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
					   $proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
					   $template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
				   }else{
					   $template=$this->create_row("", "", "ultima_informacion", $template, "no");
				   }
	   
				   if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
					   $template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
				   }else{
					   $template=$this->create_row("", "", "pais", $template, "no");
				   }
	   
				   if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
					   $template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
				   }else{
					   $template=$this->create_row("", "", "region", $template, "no");
				   }
	   
				   if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
					   $template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
				   }else{
					   $template=$this->create_row("", "", "comuna", $template, "no");
				   }
	   
				   if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
					   if(strstr($proyecto->url_confluence_pro, "?")){
						   $url=$proyecto->url_confluence_pro."&amp;print=1";
						   $template=str_replace("@paramsurl", $url, $template);
					   }else{
						   $url=$proyecto->url_confluence_pro."?print=1";
						   $template=str_replace("@paramsurl", $url, $template);
					   }
				   }
	   
			   
			   
	   
				   
				   if(isset($etapa_act)){
					   //cambio hecho 03-12-2014 Felipe
					   //$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
					   $template=$this->create_row("", $etapa_act, "nombre_etapa", $template, "si");
				   }
				   else
					   $template=$this->create_row("", "", "nombre_etapa", $template, "no");
				   if(!$fechas_tl=$this->get_fechas_tl($id)){
					   $template=str_replace("@fecha_inicio", "", $template);
					   $template=str_replace("@fecha_desde", "", $template);
					   $template=str_replace("@fecha_hasta", "", $template);
					   $template=str_replace("@_fecha_desde_f", "", $template);
					   $template=str_replace("@_fecha_hasta_f", "", $template);
				   }
				   $template=str_replace("@_urlinforma", $url_informa, $template);
	   
				   if($proyecto->id_pro==86){
					   $template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
				   }
				   else $template=str_replace("@_urlvervideo", '', $template);
	   
				   //$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
				   $template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
	   
				   if(strstr($proyecto->url_confluence_pro, "?"))
					   $template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
				   else
					   
				   $template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
				   $template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
				   $template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
				   $template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
				   $template=str_replace("@nombre_mina", $proyecto->Nombre_pro, $template);
				   $template=str_replace("@nom_xml", $nom_xml, $template);
				   $template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
				   $template=str_replace("@fecha_desde", $fechas_tl[1], $template);
				   $template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
				   $template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
				   $template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
				   
				   $template = utf8_encode($template);
				//   $template = str_replace("?", "-**0.*",$template);
				   $template = str_replace("é", "-**1.*",$template);
				   $template = str_replace("á", "-**2.*",$template);
				   $template = str_replace("í", "-**3.*",$template);
				   $template = str_replace("ó", "-**4.*",$template);
				   $template = str_replace("ú", "-**5.*",$template);
				   $template = str_replace("ñ", "-**6.*",$template);
				   $template = str_replace("à", "-**7.*",$template);
				   $template = str_replace("Á", "-**8.*",$template);
	   
				   $template = str_replace("É", "-**9.*",$template);
				   $template = str_replace("Í", "-**10.*",$template);
				   $template = str_replace("Ó", "-**11.*",$template);
				   $template = str_replace("Ú", "-**12.*",$template);
				   $template = str_replace("/", "-**13.*",$template);
				   $template = str_replace("--", "-**14.*",$template);
	   
	   
	   
				   
					   
	   
				   $template = preg_replace('/[\x80-\xFF]/', '', $template);
	   
			 //      $template = str_replace('-**0.*', '"', $template);
				   $template = str_replace("-**1.*", "é", $template);
				   $template = str_replace("-**2.*", "á", $template);
				   $template = str_replace("-**3.*", "í", $template);
				   $template = str_replace("-**4.*", "ó", $template);
				   $template = str_replace("-**5.*", "ú", $template);
				   $template = str_replace("-**6.*", "ñ", $template);
				   $template = str_replace("-**7.*", "à", $template);
				   $template = str_replace("-**8.*", "Á", $template);
	   
	   
	   
				   $template = str_replace("-**9.*", "É", $template);
				   $template = str_replace("-**10.*", "Í", $template);
				   $template = str_replace("-**11.*", "Ó", $template);
				   $template = str_replace("-**12.*", "Ú", $template);
				   $template = str_replace("-**13.*", "/", $template);
				   $template = str_replace("-**14.*", "/",$template);
				   $template = str_replace("-**13.*", "/", $template);
				   $template = str_replace("/ /", "/",$template);
				   $template = str_replace("Fecha Actualizacion", "Fecha Actualización", $template);
	   
	   
	   
		 //          $this->guardar_html_proyecto($proyecto->id_pro, $template);
	   
				   $template = utf8_encode(utf8_decode($template));
					 //$template = utf8_decode($template);
	   echo $template;
	   exit; 
				   return($template);
	   
	   /* 			$template = utf8_decode($template);
				   return($template); */
			   }else{
				   return(false);
			   }
		   }
	   




// ******************************************************************************************* */
// Nueva modificacion 07/06/2019  SUMA.CL
// Correccion de carateres UFT-8
// ******************************************************************************************* */
	function generar_ficha_html_suma($id, $titulo=""){


 $html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);

		$proyecto=$this->mostrar_proyecto_full($id);
	
		//$url=$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];
		$url=$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';

		


             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/





$template=str_replace("@ultima_informacion",  utf8_decode($proyecto->ultima_informacion_pro) ,$template);
$template=str_replace("@descripcion",  utf8_decode($proyecto->Desc_pro) ,$template);

$template=str_replace("@titulo_inversion",    "Inversion" ,$template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);



if($proyecto->Etapa_actual_pro==1){ $laetapa="Exploracion";}
if($proyecto->Etapa_actual_pro==2){ $laetapa="Ingenieria Conceptual o Prefactibilidad ";}
if($proyecto->Etapa_actual_pro==3){ $laetapa="Ingenieria Basica o Factibilidad ";}
if($proyecto->Etapa_actual_pro==6){ $laetapa="Ingenieria de Detalle";}
if($proyecto->Etapa_actual_pro==7){ $laetapa="Construccion y Montaje";}
if($proyecto->Etapa_actual_pro==8){ $laetapa="Operacion";}

 $template=str_replace("@etapa_actual",  $laetapa ,$template);




		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);


		$dire = utf8_decode($proyecto->Direccion_pro);
		$template=str_replace("@direccion", $dire, $template);


		
		
		$template=str_replace("@nombre_sector", utf8_decode($proyecto->Nombre_sector), $template);
        $template=str_replace("@region", utf8_decode($proyecto->Nombre_region), $template);
        $template=str_replace("@comuna", utf8_decode($proyecto->Nombre_comuna), $template);
        $template=str_replace("@pais", utf8_decode($proyecto->Nombre_pais), $template);
        $linea = $this->trae_tipo_proyecto($id);
        $linea = "<a href='#'>$linea</a>";
        $template=str_replace("@tipos", utf8_decode($linea), $template);

		/*Fin mano de obra*/
		
		

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		//echo $params_confluence;
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence_dns.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  $ht->Nombre_hito, $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", utf8_decode($html_hitos), $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}
			
		

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  $st->Nombre_etapa, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row("", utf8_decode($html_etapas), "etapas", $template, "si",1);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			
			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=$this->params->br2nl($licitacion->Nombre_lici_completo);
			;
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", utf8_decode($html_lic), "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			
		
			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=$this->params->br2nl($adjudicacion->nombre_adj);
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", utf8_decode($html_adj), "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=$this->params->br2nl($prop->Nombre_fantasia_emp);
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", utf8_decode($html_propietarios), "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			
		
			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					$value=$this->params->br2nl($tipo->Nombre_tipo);
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", utf8_decode($html_tipos), "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

	
		
			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=$this->params->br2nl($obra->Nombre_obra);
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", utf8_decode($html_obras), "obras", $template, "si", 1);
				//$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

		
			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=utf8_decode($this->params->br2nl($equipo->Nombre_equipo));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=utf8_decode($this->params->br2nl($suministro->Nombre_sumin));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			
			
			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=$this->params->br2nl($servicio->Nombre_serv);
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=$this->params->br2nl($c->Nombre_cat_serv);
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=$this->params->br2nl($sc->Nombre_sub_serv);
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", utf8_decode($html_servicios), "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}
			

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=utf8_decode($etapa->Nombre_etapa);
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=$e->Nombre_fantasia_emp;
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}
			
						
			
		
			
//echo $proyecto->Historial_pro;
			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", $proyecto->Nombre_generico_pro, "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				$value1=$proyecto->Nombre_sector;
				//$value="<a href='".$this->params->url_confluence_dns.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				//$value1=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_sector)));
				//echo($this->params->url_confluence_dns);
				
				$value="<a href='".$this->params->url_confluence_dns.'/'.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				//echo("<<<");
				//echo($value);
				//die();
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
				
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");

			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", utf8_decode($proyecto->Oport_neg_pro), "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=$proyecto->Nombre_pais;
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$this->params->url_confluence_dns."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$this->params->url_confluence_dns."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				//echo($value);
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=$proyecto->Nombre_region;
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=$proyecto->Nombre_comuna;
				$template=$this->create_row("Comuna",  $value, "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", $proyecto->Direccion_pro, "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}
// URL DE LINKS MANDANTE
			if($proyecto->Nombre_fantasia_emp!=""){
				$value=$this->params->br2nl($proyecto->Nombre_fantasia_emp);
				$filtro="mandante_".$proyecto->id_man_emp;
				
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", str_replace("/", "--", $params_confluence.$filtro))."'>".$value."</a>";
				$template=$this->create_row("Mandante", utf8_decode($value), "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", utf8_decode($proyecto->Nombre_pro), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

		
			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", $this->params->br2nl($proyecto->Desc_pro), "descripcion", $template, "si", 1);

			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", $proyecto->detalle_equipos, "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", $proyecto->detalle_suministros, "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

		
			$tmp_contact='';
			$item=0;
			
			

			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							
							
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Nombre_contact).'</td>
							
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Empresa_contact).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Cargo_contact).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Email_contact).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Telefono_contact).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.utf8_decode($ct->Direccion_contact).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

							

			
					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", $tmp_contact , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}


			

			if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}

			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

		
		

			
			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
				$template=$this->create_row("", $etapa_act, "nombre_etapa", $template, "si");
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				
			$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", $proyecto->Nombre_pro, $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			
		    $template = utf8_encode($template);
         //   $template = str_replace("?", "-**0.*",$template);
            $template = str_replace("é", "-**1.*",$template);
			$template = str_replace("á", "-**2.*",$template);
			$template = str_replace("í", "-**3.*",$template);
			$template = str_replace("ó", "-**4.*",$template);
			$template = str_replace("ú", "-**5.*",$template);
			$template = str_replace("ñ", "-**6.*",$template);
			$template = str_replace("à", "-**7.*",$template);
			$template = str_replace("Á", "-**8.*",$template);

			$template = str_replace("É", "-**9.*",$template);
			$template = str_replace("Í", "-**10.*",$template);
			$template = str_replace("Ó", "-**11.*",$template);
			$template = str_replace("Ú", "-**12.*",$template);
			$template = str_replace("/", "-**13.*",$template);
			$template = str_replace("--", "-**14.*",$template);



			
				

            $template = preg_replace('/[\x80-\xFF]/', '', $template);

      //      $template = str_replace('-**0.*', '"', $template);
            $template = str_replace("-**1.*", "é", $template);
			$template = str_replace("-**2.*", "á", $template);
			$template = str_replace("-**3.*", "í", $template);
			$template = str_replace("-**4.*", "ó", $template);
			$template = str_replace("-**5.*", "ú", $template);
			$template = str_replace("-**6.*", "ñ", $template);
			$template = str_replace("-**7.*", "à", $template);
            $template = str_replace("-**8.*", "Á", $template);



			$template = str_replace("-**9.*", "É", $template);
			$template = str_replace("-**10.*", "Í", $template);
			$template = str_replace("-**11.*", "Ó", $template);
			$template = str_replace("-**12.*", "Ú", $template);
			$template = str_replace("-**13.*", "/", $template);
			$template = str_replace("-**14.*", "/",$template);
			$template = str_replace("-**13.*", "/", $template);
			$template = str_replace("/ /", "/",$template);
			$template = str_replace("Fecha Actualizacion", "Fecha Actualización", $template);



  //          $this->guardar_html_proyecto($proyecto->id_pro, $template);

			//$template = utf8_encode(utf8_decode($template));
  			$template = utf8_decode($template);
 echo $template;
exit; 
			return($template);

/* 			$template = utf8_decode($template);
			return($template); */
		}else{
			return(false);
		}
	}
// ******************************************************************************************* */
// Nueva modificacion 07/06/2019  SUMA.CL
// Correccion de carateres UFT-8
// ******************************************************************************************* */



/* EPF */
	
		function generar_ficha_html_2jun($id, $titulo=""){
		$html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);

		$proyecto=$this->mostrar_proyecto_full($id);
	
		$url=$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';

		


             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/





$template=str_replace("@ultima_informacion",  utf8_decode($proyecto->ultima_informacion_pro) ,$template);
$template=str_replace("@descripcion",  utf8_decode($proyecto->Desc_pro) ,$template);

$template=str_replace("@titulo_inversion",  "Inversion" ,$template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);
$template=str_replace("@titulo_descripcion",  "Descripcion", $template);



if($proyecto->Etapa_actual_pro==1){ $laetapa="Exploracion";}
if($proyecto->Etapa_actual_pro==2){ $laetapa="Ingenieria Conceptual o Prefactibilidad ";}
if($proyecto->Etapa_actual_pro==3){ $laetapa="Ingenieria Basica o Factibilidad ";}
if($proyecto->Etapa_actual_pro==6){ $laetapa="Ingenieria de Detalle";}
if($proyecto->Etapa_actual_pro==7){ $laetapa="Construccion y Montaje";}
if($proyecto->Etapa_actual_pro==8){ $laetapa="Operacion";}

 $template=str_replace("@etapa_actual",  $laetapa ,$template);


		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);

		/*Fin mano de obra*/
		
		

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  $ht->Nombre_hito, $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", $html_hitos, $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}
			
		

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  $st->Nombre_etapa, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row("", $html_etapas, "etapas", $template, "si",1);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			
			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=$this->params->br2nl($licitacion->Nombre_lici_completo);
			;
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", $html_lic, "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			
		
			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=$this->params->br2nl($adjudicacion->nombre_adj);
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", $html_adj, "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=$this->params->br2nl($prop->Nombre_fantasia_emp);
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", $html_propietarios, "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			
		
			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					$value=$this->params->br2nl($tipo->Nombre_tipo);
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", $html_tipos, "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

	
		
			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=$this->params->br2nl($obra->Nombre_obra);
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

		
			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=utf8_decode($this->params->br2nl($equipo->Nombre_equipo));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=utf8_decode($this->params->br2nl($suministro->Nombre_sumin));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			
			
			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=utf8_decode($this->params->br2nl($servicio->Nombre_serv));
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=$this->params->br2nl($c->Nombre_cat_serv);
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=$this->params->br2nl($sc->Nombre_sub_serv);
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", $html_servicios, "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=utf8_decode($etapa->Nombre_etapa);
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=$e->Nombre_fantasia_emp;
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}
			
						
			
			
			
//echo $proyecto->Historial_pro;
			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", $proyecto->Nombre_generico_pro, "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				$value1=utf8_decode($proyecto->Nombre_sector);
				$value="<a href='".$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=utf8_decode($proyecto->Nombre_pais);
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=$proyecto->Nombre_region;
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=$proyecto->Nombre_comuna;
				$template=$this->create_row("Comuna",  $value, "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", $proyecto->Direccion_pro, "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}

			if($proyecto->Nombre_fantasia_emp!=""){
				$value=$this->params->br2nl($proyecto->Nombre_fantasia_emp);
				$filtro="mandante_".$proyecto->id_man_emp;
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("Mandante", $value, "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", $proyecto->Nombre_pro, $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

		
			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", $this->params->br2nl($proyecto->Desc_pro), "descripcion", $template, "si", 1);

			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", $proyecto->detalle_equipos, "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", $proyecto->detalle_suministros, "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

		
			$tmp_contact='';
			$item=0;
			
			
																		

			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							
							
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
							
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

							

			
					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", $tmp_contact , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}


			

			if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}

			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

		
		

			
			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
				$template=$this->create_row("", $etapa_act, "nombre_etapa", $template, "si");
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", $proyecto->Nombre_pro, $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			//$this->guardar_html_proyecto($proyecto->id_pro, $template);

			//$template = utf8_encode(utf8_decode($template));
			$template = utf8_encode(utf8_decode($template));
			
echo $template;
exit;			
		
			return($template);
		}else{
			return(false);
		}
	}
	
	
	function generar_ficha_html_old($id, $titulo=""){
		$html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);

		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template);

		$proyecto=$this->mostrar_proyecto_full($id);
		$url=$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';



             /* INICIO Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/
		if( $this->tiene_rca($id)  > 0) {
			 $htm_timbre  = "
			 <DIV class='iconos_licitaciones'>
		       <DIV class='imagen_lici'>
		         <img src='http://pm.portalminero.com/images/evahamb.png' pagespeed_url_hash='857716621' onload='pagespeed.CriticalImages.checkImageForCriticality(this);' width='120' />
		       </DIV>
		     </DIV>";
		     $template=str_replace("<!--@imgamb-->",  $htm_timbre ,$template);


		}else{

			$template=str_replace("<!--@imgamb-->",  " " ,$template);
		}
             /* FIN  Ve si el proyecto tiene el hito RCA  EPF y agrega imagen RCA*/



		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);

		/*Fin mano de obra*/

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  htmlentities(utf8_decode($ht->Nombre_hito)), $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", $html_hitos, $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  htmlentities(utf8_decode($st->Nombre_etapa)), $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row("", $html_etapas, "etapas", $template, "si",1);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=$this->params->br2nl($licitacion->Nombre_lici_completo);
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content' href='".$licitacion->url_confluence_lici."'>".$value."</a>";
						else
							$value="<a class='label_content' href='#'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";

				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", $html_lic, "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($adjudicacion->nombre_adj))));
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content' href='".$adjudicacion->url_confluence_adj."'>".$value."</a>";
					else
						$value="<a class='label_content' href='#'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", $html_adj, "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($prop->Nombre_fantasia_emp))));
					if($html_propietarios!=""){

						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}
				}
				$template=$this->create_row("Propietarios", $html_propietarios, "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($tipo->Nombre_tipo))));
					$filtro="tipo_".$tipo->id_tipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", $html_tipos, "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($obra->Nombre_obra))));
					$filtro="obra_".$obra->id_obra;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}
				}
				$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($equipo->Nombre_equipo))));
					$filtro="equipo_".$equipo->id_equipo;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($suministro->Nombre_sumin))));
					$filtro="suministro_".$suministro->id_sumin;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}

			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($servicio->Nombre_serv))));
					$filtro="servicio_".$servicio->id_serv;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";
						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($c->Nombre_cat_serv))));
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($sc->Nombre_sub_serv))));
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", $html_servicios, "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=htmlentities(html_entity_decode(utf8_decode($etapa->Nombre_etapa)));
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp))));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=htmlentities(html_entity_decode(utf8_decode($e->Nombre_fantasia_emp)));
							$filtro="responsable_".$e->id_emp;
							if($value==""){
								if(strstr($url, "?"))
									$value.="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}else{
								if(strstr($url, "?"))
									$value.=" - <a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
								else
									$value.=" - <a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
						if(strstr($url, "?"))
							$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
						else
							$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}

			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", $proyecto->Nombre_generico_pro, "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				$value1=$proyecto->Nombre_sector;
				$value="<a href='".$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."' class='label_content'>".$value1."</a>";
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=html_entity_decode(utf8_decode($proyecto->Nombre_pais));
				$filtro="pais_".$proyecto->id_pais_p;
				if(strstr($url, "?"))
					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				print_r($value);
				die();
				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_region)));
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
					$filtro="pais_".$proyecto->id_pais_p."-region_".$proyecto->id_region_p;
					if(strstr($url, "?"))
						$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
					else
						$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=html_entity_decode($proyecto->Nombre_comuna);
				$template=$this->create_row("Comuna",  utf8_decode($value), "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", utf8_decode($proyecto->Direccion_pro), "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}

			if($proyecto->Nombre_fantasia_emp!=""){
				$value=$this->params->br2nl($proyecto->Nombre_fantasia_emp);
				$filtro="mandante_".$proyecto->id_man_emp;
				if(strstr($url, "?"))

					$value="<a class='label_content' href='".$url."&amp;p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				else
					$value="<a class='label_content' href='".$url."?p=".str_replace("=", "-", base64_encode(str_replace("/", "--", $params_confluence.$filtro)))."'>".$value."</a>";
				$template=$this->create_row("Mandante", $value, "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", $proyecto->Nombre_pro, $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", $this->params->br2nl($proyecto->Desc_pro), "descripcion", $template, "si", 1);

			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", $proyecto->detalle_equipos, "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", $proyecto->detalle_suministros, "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

			$tmp_contact='';
			$item=0;
			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Nombre_contact)).'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Empresa_contact)).'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Cargo_contact)).'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Email_contact)).'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Telefono_contact)).'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.htmlentities(utf8_decode($ct->Direccion_contact)).'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					//$template=str_replace("@contactos", $tmp_contact , $template);
					//$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}


			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

			if(isset($etapa_act)){
								
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", $etapa_act, $template);
				$template=$this->create_row("", htmlentities(utf8_decode($etapa_act)), "nombre_etapa", $template, "si");
				
			}
			else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			if($proyecto->id_pro==86){
				$template=str_replace("@_urlvervideo", '<div style="float:right; padding-left:25px;"><a class="urlvervideo" href="/display/acce/Ver+Video?decorator=popup&amp;id='.$proyecto->id_pro.'"><img src="/sitio_portal/images/videos.png" /></a></div>', $template);
			}
			else $template=str_replace("@_urlvervideo", '', $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", $proyecto->Nombre_pro, $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			//$this->guardar_html_proyecto($proyecto->id_pro, $template);
			
			echo $template;
			exit;
			return($template);
		}else{
			return(false);
		}
	}

	function generar_ficha_intranet($id,$sistema=false){
		$html_propietarios	= "";
		$html_tipos			= "";
		$html_obras			= "";
		$html_equipos		= "";
		$html_suministros	= "";
		$html_servicios		= "";
		$html_hitos			= "";
		$html_etapas		= "";
		$html_ubicacion		= "";

		$datos = array();

		$datos["proyectos"]			= $this->mostrar_proyecto_full($id);
		$datos['propietarios']		= $this->mostrar_propietarios($id);
		$datos["tipos"]				= $this->mostrar_tipos($id);
		$datos["obras"]				= $this->mostrar_obras($id);
		$datos["equipos"]			= $this->mostrar_equipos($id);
		$datos["suministros"]		= $this->mostrar_suministros($id);
		$datos["servicios"]			= $this->mostrar_servicios($id);
		$datos["cat"]				= $this->mostrar_serv_cat($id);
		$datos["subcat"]			= $this->mostrar_serv_subcat($id);
		$datos["licitaciones"]		= $this->mostrar_licitaciones($id);
		$datos["adjudicaciones"]	= $this->mostrar_adjudicaciones($id);
		$datos["hitos"]				= $this->get_hitos($id);
		$datos["etapas"]			= $this->get_etapas($id);
		$datos["oportunidad"]		= $this->mostrar_oportunidad($id);


		//11-11-2013 obtener ultima modificacion de la adjudicacion
		/*$this->db->select('Nombre_completo_user');
		$this->db->from('m_accion_user');
		$this->db->join('m_user', 'm_accion_user.id_user = m_user.id_user', 'inner');
		$this->db->where('id_modulo', 1);
		$this->db->where('id_padre', $datos['proyectos']->id_pro);
		$this->db->order_by('Fecha_a_user', 'desc');
		$this->db->limit(1);*/

		//09-12-2013 Modificación
		$this->db->where('id_user',$datos['proyectos']->id_usuario_modifica);
		$rs=$this->db->get('m_user');

		if($sistema){
			$datos["proyectos"]->id_user_ultima_modif = 'Sistema';
		}
		else if($rs->num_rows() == 0){
			$datos["proyectos"]->id_user_ultima_modif = '';

		}else{
			$fila = $rs->first_row();
			$datos["proyectos"]->id_user_ultima_modif = $fila->Nombre_completo_user;
		}

			$contactos_princ=$this->buscar_contactos_pro($datos['proyectos']->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($datos['proyectos']->id_pro,0);

			$tmp_contact='';
			$item=0;
			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>

						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$datos['tmp_contact']=$tmp_contact;
		   }
			else{
				$datos['tmp_contact']='';
			}

		/*07-11-2014 formato historial y última información*/
		$historial=$datos["proyectos"]->Historial_pro;
		$historial=str_replace("“", '"', $historial);
		$historial=str_replace("”", '"', $historial);
		$historial=str_replace("•", ' - ', $historial);
		$historial=str_replace("–", ' - ', $historial);
		$historial=str_replace("<", '"Mayor que"', $historial);
		$historial=str_replace(">", '"Menor que"', $historial);
		$datos['historial']=$this->params->elimina_signos($historial);
		$datos['ultima_info']=$this->params->elimina_signos($datos["proyectos"]->ultima_informacion_pro);

		$html = $this->load->view('proyectos/verFicha_intranet.php',$datos, true);
		return($html);
	}

	function generar_ficha_html_ejemplo($id, $titulo=""){
		$html_propietarios="";
		$html_tipos="";
		$html_obras="";
		$html_equipos="";
		$html_suministros="";
		$html_servicios="";
		$html_hitos="";
		$html_etapas="";
		$html_ubicacion="";
		$nom_xml=$this->get_xml_file($id);
		$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->ficha_template_ejemplo);

		$proyecto=$this->mostrar_proyecto_full($id);
		$url=$this->params->url_confluence.$this->params->spaces_proy[$proyecto->id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$proyecto->id_sector]];

		/*Mano de obra*/
		$mostrar_mo=0;
		$mano_obra='';
		$item_mo='';
		if(($proyecto->mo_construccion_pro!='')&&($proyecto->mo_construccion_pro!=0)){
			$item_mo.='<div style="clear:both;">Construcci&oacute;n: '.$proyecto->mo_construccion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_operacion_pro!='')&&($proyecto->mo_operacion_pro!=0)){
			$item_mo.='<div style="clear:both;">Operaci&oacute;n: '.$proyecto->mo_operacion_pro.'</div>';
			$mostrar_mo=1;
		}
		if(($proyecto->mo_cierre_pro!='')&&($proyecto->mo_cierre_pro!=0)){
			$item_mo.='<div style="clear:both;">Cierre o Abandono: '.$proyecto->mo_cierre_pro.'</div>';
			$mostrar_mo=1;
		}

		if($mostrar_mo==1){
			$mano_obra='<tr style="">
						<td class="confluenceTd"  style="border-collapse:collapse" width="40%" valign="top">Mano de Obra:</td>
						<td class="confluenceTd"  style="border-collapse:collapse" width="60%" valign="top"><div style="float:left">'.$item_mo.'</div></td>
					</tr>';
		}
		$template=str_replace("@mano_obra", $mano_obra, $template);

		/*Fin mano de obra*/

		$params_confluence=$this->params->list_confluence["pro"]."/%20/".$proyecto->id_sector."/1/";
		$url_js=$this->generar_grafico_js($proyecto->id_sector);
		$template=str_replace("@url_js", $proyecto->id_sector, $template);
		$template=str_replace("@_url_js2", $proyecto->Etapa_actual_pro, $template);
		if($proyecto->Etapa_actual_pro!=0)
			$url_js2=$this->generar_grafico_js2($proyecto->Etapa_actual_pro);

		$tipos=$this->mostrar_tipos($id);
		$obras=$this->mostrar_obras($id);
		$equipos=$this->mostrar_equipos($id);
		$suministros=$this->mostrar_suministros($id);
		$servicios=$this->mostrar_servicios($id);
		$cat=$this->mostrar_serv_cat($id);
		$subcat=$this->mostrar_serv_subcat($id);
		$licitaciones=$this->mostrar_licitaciones($id);
		$adjudicaciones=$this->mostrar_adjudicaciones($id);
		$hitos=$this->get_hitos($id);
		$etapas=$this->get_etapas($id);
		if(is_object($proyecto)){
			$url_informa=$this->params->url_confluence.$this->params->confluence_informa_cambio."?idpagina=".$proyecto->id_pagina_pro."&amp;popup";

			$etapa=$this->cargar_datos_etapa_actual($id);
			$space=$this->params->spaces_proy[$proyecto->id_sector];
			$propietarios=$this->mostrar_propietarios($proyecto->id_pro);

			if(is_array($hitos) && sizeof($hitos)>0){
				$html_hitos.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="2" align="center"><strong>Hitos Importantes</strong></td>';
				$html_hitos.='</tr>';
				$html_hitos.='<tr>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Hito</td>';
					$html_hitos.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha</td>';
				$html_hitos.='</tr>';

				foreach($hitos as $ht){
					if($ht->trim_hito!=0 && $ht->ano_hito!=0 && $ht->trim_hito!="" && $ht->ano_hito!=""){
						$html_hitos.="<tr>";
						$html_hitos.=str_replace("@text",  htmlentities(utf8_decode($ht->Nombre_hito)), $this->params->formato_columna);
						$html_hitos.=str_replace("@text",  $this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito, $this->params->formato_columna);
						$html_hitos.="</tr>";
					}
				}
				$html_hitos.="</table>";
				if($html_hitos!=""){
					$template=str_replace("@hitos", $html_hitos, $template);
					$template=str_replace("@style_hitos", "", $template);
				}else{
					$template=$this->create_row("Hito", "", "hitos", $template, "no");
				}
			}else{
				$template=$this->create_row("Hito", "", "hitos", $template, "no");
			}

			if(is_array($etapas) && sizeof($etapas)>0){
				$html_etapas.='<table class="confluenceTable" cellspacing="0" cellpadding="0" border="1" align="center">';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap" colspan="3" align="center"><strong>Etapas</strong></td>';
				$html_etapas.='</tr>';
				$html_etapas.='<tr>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="" nowrap="nowrap">Etapa</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha Inicio</td>';
					$html_etapas.='<td class="confluenceTd"  style="border-collapse:collapse" width="">Fecha T&eacute;rmino</td>';
				$html_etapas.='</tr>';
				foreach($etapas as $st){
					if($st->trim_inicio!=0 && $st->ano_inicio!=0 && $st->trim_inicio!="" && $st->ano_inicio!="" && $st->trim_fin!=0 && $st->ano_fin!=0 && $st->trim_fin!="" && $st->ano_fin!=""){
						$html_etapas.="<tr>";
						$html_etapas.=str_replace("@text",  htmlentities(utf8_decode($st->Nombre_etapa)), $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_inicio]." de ".$st->ano_inicio, $this->params->formato_columna);
						$html_etapas.=str_replace("@text",  $this->params->trimestre[$st->trim_fin]." de ".$st->ano_fin, $this->params->formato_columna);
						$html_etapas.="</tr>";
					}
				}
				$html_etapas.="</table>";
				if($html_etapas!=""){
					//$template=str_replace("@etapas", $html_etapas, $template);
					$template=$this->create_row("", $html_etapas, "etapas", $template, "si",1);
				}else{
					$template=$this->create_row("Etapas", "", "etapas", $template, "no");
				}
			}else{
				$template=$this->create_row("Etapas", "", "etapas", $template, "no");
			}

			if(is_array($licitaciones) && sizeof($licitaciones)>0){
				$html_lic="";
				$html_lic_est="";
				$html_lic_def="";
				$html_lic_adj="";
				$html_lic_pro="";
				foreach($licitaciones as $licitacion){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($licitacion->Nombre_lici_completo))));
					if($licitacion->id_lici_tipo==$this->params->tipo_lici["estimada"]){
						if($html_lic_est==""){
							$html_lic_est.="<div><div style='clear:both; font-weight:bold;'>Estimada</div>";
							$html_lic_est.="<div style='clear:both;'>";
						}

						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content'>".$value."</a>";
						else
							$value="<a class='label_content'>".$value."</a>";
						$html_lic_est.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";

					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["definida"]){
						if($html_lic_def==""){
							$html_lic_def.="<div><div style='clear:both; font-weight:bold;'>Definida</div>";
							$html_lic_def.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content'>".$value."</a>";
						else
							$value="<a class='label_content'>".$value."</a>";
						$html_lic_def.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}elseif($licitacion->id_lici_tipo==$this->params->tipo_lici["adjudicada"]){
						if($html_lic_adj==""){
							$html_lic_adj.="<div><div style='clear:both; font-weight:bold;'>Adjudicada</div>";
							$html_lic_adj.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content'>".$value."</a>";
						else
							$value="<a class='label_content'>".$value."</a>";
						$html_lic_adj.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}else{
						if($html_lic_pro==""){
							$html_lic_pro.="<div><div style='clear:both; font-weight:bold;'>En Proceso de Adjudicaci&oacute;n</div>";
							$html_lic_pro.="<div style='clear:both;'>";
						}
						if($licitacion->url_confluence_lici!="")
							$value="<a class='label_content'>".$value."</a>";
						else
							$value="<a class='label_content'>".$value."</a>";
						$html_lic_pro.="<div style='padding-left:15px; float:left; clear:both;'>- ".$value."</div>";
					}
				}
				if($html_lic_def!=""){
					$html_lic_def.="</div></div>";
				}
				if($html_lic_est!=""){
					$html_lic_est.="</div></div>";
				}
				if($html_lic_adj!=""){
					$html_lic_adj.="</div></div>";
				}
				if($html_lic_pro!=""){
					$html_lic_pro.="</div></div>";
				}
				$html_lic=$html_lic_def.$html_lic_est.$html_lic_adj.$html_lic_pro;
				$template=$this->create_row("Licitaciones Asociadas", $html_lic, "licitaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Licitaciones Asociadas", "", "licitaciones", $template, "no", 1);
			}

			if(is_array($adjudicaciones) && sizeof($adjudicaciones)>0){
				$html_adj="";
				foreach($adjudicaciones as $adjudicacion){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($adjudicacion->nombre_adj))));
					if($adjudicacion->url_confluence_adj!="")
						$value="<a class='label_content'>".$value."</a>";
					else
						$value="<a class='label_content'>".$value."</a>";
					if($html_adj!=""){
						$html_adj.="\n- ".$value;
					}else{
						$html_adj.="- ".$value;
					}
				}
				$template=$this->create_row("Adjudicaciones Asociadas", $html_adj, "adjudicaciones", $template, "si", 1);
			}else{
				$template=$this->create_row("Adjudicaciones Asociadas", "", "adjudicaciones", $template, "no", 1);
			}

			if(is_array($propietarios) && sizeof($propietarios)>0){
				$html_propietarios="";
				foreach($propietarios as $prop){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($prop->Nombre_fantasia_emp))));
					if($html_propietarios!=""){
						$html_propietarios.="\n".$value;
					}else{
						$html_propietarios.=$value;
					}


				}
				$template=$this->create_row("Propietarios", $html_propietarios, "propietarios", $template, "si", 1);
			}else{
				$template=$this->create_row("Propietarios", "", "propietarios", $template, "no", 1);
			}

			if(is_array($tipos) && sizeof($tipos)>0){
				$html_tipos="";
				foreach($tipos as $tipo){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($tipo->Nombre_tipo))));
						$value="<a class='label_content'>".$value."</a>";
					if($html_tipos!=""){
						$html_tipos.="\n".$value;
					}else{
						$html_tipos.=$value;
					}
				}
				$template=$this->create_row("Tipos", $html_tipos, "tipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Tipos", "", "tipos", $template, "no", 1);
			}

			if(is_array($obras) && sizeof($obras)>0){
				$html_obras="";
				foreach($obras as $obra){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($obra->Nombre_obra))));
					$filtro="obra_".$obra->id_obra;
						$value="<a class='label_content'>".$value."</a>";

					if($html_obras!=""){
						$html_obras.="\n- ".$value;
					}else{
						$html_obras.="- ".$value;
					}

				}
				$template=$this->create_row("Obras Principales", $html_obras, "obras", $template, "si", 1);
			}else{
				$template=$this->create_row("Obras Principales", "", "obras", $template, "no", 1);
			}

			if(is_array($equipos) && sizeof($equipos)>0){
				$html_equipos="";
				foreach($equipos as $equipo){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($equipo->Nombre_equipo))));
					$filtro="equipo_".$equipo->id_equipo;
						$value="<a class='label_content'>".$value."</a>";
					if($html_equipos!=""){
						$html_equipos.="\n- ".$value;
					}else{
						$html_equipos.="- ".$value;
					}
				}
				$template=$this->create_row("Equipos Principales", $html_equipos, "equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Equipos Principales", "", "equipos", $template, "no", 1);
			}

			if(is_array($suministros) && sizeof($suministros)>0){
				$html_suministros="";
				foreach($suministros as $suministro){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($suministro->Nombre_sumin))));
					$filtro="suministro_".$suministro->id_sumin;
						$value="<a class='label_content'>".$value."</a>";
					if($html_suministros!=""){
						$html_suministros.="\n- ".$value;
					}else{
						$html_suministros.="- ".$value;
					}
				}
				$template=$this->create_row("Suministros Principales", $html_suministros, "suministros", $template, "si", 1);
			}else{
				$template=$this->create_row("Suministros Principales", "", "suministros", $template, "no", 1);
			}





			if(is_array($servicios) && sizeof($servicios)>0){
				$html_servicios="";
				$html_cat="";
				foreach($servicios as $servicio){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($servicio->Nombre_serv))));
					$filtro="servicio_".$servicio->id_serv;
						$value="<a class='label_content'>".$value."</a>";
					$html_servicios.="<div style='clear:both;'>- ".$value."</div>";

					if(is_array($cat) && sizeof($cat)>0){
						$html_cat="";

						foreach($cat as $c){
							if($c->id_serv==$servicio->id_serv){
								$value2=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($c->Nombre_cat_serv))));
								$html_cat.="<div style='clear:both;'>- ".$value2."</div>";
							}
							if(is_array($subcat) && sizeof($subcat)>0){
								$html_subcat="";
								foreach($subcat as $sc){
									if($sc->id_serv==$servicio->id_serv && $sc->id_cat_serv==$c->id_cat_serv){
										$value3=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($sc->Nombre_sub_serv))));
										if($value3!=""){
											$html_subcat.="<div style='clear:both;'>- ".$value3."</div>";
										}
									}
								}
								if($html_subcat!=""){
									$html_cat.="<div style='padding-left:25px; clear:both;'>";
									$html_cat.=$html_subcat."</div>";
								}
							}
						}
						if($html_cat!=""){
							$html_servicios.="<div style='padding-left:15px; clear:both;'>";
							$html_servicios.=$html_cat."</div>";
						}
					}
				}
				$template=$this->create_row("Servicios Principales", $html_servicios, "servicios", $template, "si", 1);
			}else{
				$template=$this->create_row("Servicios Principales", "", "servicios", $template, "no", 1);
			}

			if(is_object($etapa)){
				if($etapa->Nombre_etapa!="" && $etapa->Nombre_etapa!=NULL){
					$etapa_act=$etapa->Nombre_etapa;
					$filtro="etapa_".$etapa->id_etapa;
					$value=htmlentities(html_entity_decode(utf8_decode($etapa->Nombre_etapa)));
						$value="<a class='label_content'>".$value."</a>";
					$template=$this->create_row("Etapa Actual", $value, "etapa_actual", $template, "si", 1);
				}else{
					$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
				}

				if($etapa->Nombre_tipo_contrato!="" && $etapa->Nombre_tipo_contrato!=NULL && $etapa->id_etapa!=$this->params->id_etapa_operaciones){
					$template=$this->create_row("Tipo de Contrato", $etapa->Nombre_tipo_contrato." (".$etapa->Abreviacion_tipo_contrato.")", "tipo_contrato", $template, "si");
				}else{
					$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				}

				if($etapa->Nombre_fantasia_emp!="" && $etapa->Nombre_fantasia_emp!=NULL){
					$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($etapa->Nombre_fantasia_emp))));
					$emp1=$this->empresa->buscar_nombre_compuesto($etapa->Nombre_fantasia_emp);
					if(is_array($emp1)){
						$value="";
						foreach($emp1 as $e){
							$valuet1=htmlentities(html_entity_decode(utf8_decode($e->Nombre_fantasia_emp)));
							$filtro="responsable_".$e->id_emp;
							if($value==""){
									$value.="<a class='label_content'>".$valuet1."</a>";
							}else{
									$value.=" - <a class='label_content'>".$valuet1."</a>";
							}
						}
					}else if($emp1==true){
						$filtro="responsable_".$etapa->id_emp;
							$value="<a class='label_content'>".$value."</a>";

					}else{
						$filtro="responsable_".$etapa->id_emp;
							$value="<a class='label_content'>".$value."</a>";
					}
					$template=$this->create_row("Responsable", $value, "empresa_responsable", $template, "si", 1);
				}else{
					$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				}
			}else{
				$template=$this->create_row("Responsable", "", "empresa_responsable", $template, "no");
				$template=$this->create_row("Tipo de Contrato", "", "tipo_contrato", $template, "no");
				$template=$this->create_row("Etapa Actual", "", "etapa_actual", $template, "no");
			}

			if($proyecto->Historial_pro!=""){
				$proyecto->Historial_pro=str_replace("“", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("”", '"', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("•", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=str_replace("–", ' - ', $proyecto->Historial_pro);
				$proyecto->Historial_pro=$this->params->elimina_signos($proyecto->Historial_pro);
				$template=$this->create_row("Historial", $proyecto->Historial_pro, "historial", $template, "si");
			}else{
				$template=$this->create_row("Historial", "", "historial", $template, "no");
			}

			if($proyecto->Fecha_actualizacion_pro!=""){
				$fecha_actualiza=explode('-',$proyecto->Fecha_actualizacion_pro);
				$template=$this->create_row("Fecha Actualización", $fecha_actualiza[2].'-'.$fecha_actualiza[1].'-'.$fecha_actualiza[0], "fecha_actualizacion", $template, "si");
			}else{
				$template=$this->create_row("Fecha Actualización", "", "fecha_actualizacion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{

				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_generico_pro!=""){
				$template=$this->create_row("Nombre Genérico", utf8_decode($proyecto->Nombre_generico_pro), "nombre_generico", $template, "si", 1);
			}else{
				$template=$this->create_row("Nombre Genérico", "", "nombre_generico", $template, "no");
			}

			if($proyecto->Nombre_sector!=""){
				$value1=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_sector)));
				$value="<a class='label_content'>".$value1."</a>";
				$template=$this->create_row("Nombre Sector", $value, "nombre_sector", $template, "si", 1);
				$template=str_replace("@_nombre_sector2", $value1, $template);
			}else{
				$template=$this->create_row("Nombre Sector", "", "nombre_sector", $template, "no");
				$template=$this->create_row("Nombre Sector", "", "_nombre_sector2", $template, "no");
			}

			//modificado 20-05-2014
			if(($proyecto->Produccion_pro!="")&&($proyecto->Produccion_pro!=0)&&($proyecto->Medicion_produccion_pro!=0)){
				$texto_produccion=$proyecto->Produccion_pro.' '.$proyecto->Sigla_med;
				$template=$this->create_row("Producción", $texto_produccion, "produccion", $template, "si");
			}else{
				$template=$this->create_row("Producción", "", "produccion", $template, "no");
			}

			if($proyecto->Oport_neg_pro!=""){
				$template=$this->create_row("Oportunidad de Negocio", $proyecto->Oport_neg_pro, "oportunidad_negocio", $template, "si");
			}else{
				$template=$this->create_row("Oportunidad de Negocio", "", "oportunidad_negocio", $template, "no");
			}

			if($proyecto->Nombre_pais!=""){
				$value=html_entity_decode(utf8_decode($proyecto->Nombre_pais));
					$value="<a class='label_content'>".$value."</a>";

				$template=$this->create_row("País",  $value, "pais", $template, "si", 1);
			}else{
				$template=$this->create_row("País", "", "pais", $template, "no");
			}
			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$value=htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_region)));
				if($proyecto->id_pais_p==$this->params->id_pais_chile){
						$value="<a class='label_content'>".$value."</a>";
				}
				$template=$this->create_row("Región", $value, "region", $template, "si", 1);
			}else{
				$template=$this->create_row("Región", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$value=html_entity_decode($proyecto->Nombre_comuna);
				$template=$this->create_row("Comuna",  utf8_decode($value), "comuna", $template, "si", 1);
			}else{
				$template=$this->create_row("Comuna",  "", "comuna", $template, "no");
			}

			if($proyecto->Direccion_pro!=""){
				$template=$this->create_row("Dirección", utf8_decode($proyecto->Direccion_pro), "direccion", $template, "si", 1);
			}else{
				$template=$this->create_row("Dirección", "", "direccion", $template, "no");
			}

			if($proyecto->Latitud_pro!=""){
				$template=$this->create_row("latitud", $proyecto->Latitud_pro, "latitud", $template, "si");
			}else{
				$template=$this->create_row("latitud", "", "latitud", $template, "no");
			}

			if($proyecto->Longitud_pro!=""){
				$template=$this->create_row("Longitud", $proyecto->Longitud_pro, "longitud", $template, "si");
			}else{
				$template=$this->create_row("Longitud", "", "longitud", $template, "no");
			}

			if($proyecto->Nombre_fantasia_emp!=""){
				$value=htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($proyecto->Nombre_fantasia_emp))));
					$value="<a class='label_content'>".$value."</a>";
				$template=$this->create_row("Mandante", $value, "mandante", $template, "si", 1);
			}else{
				$template=$this->create_row("Mandante", "", "mandante", $template, "no");
			}

			if($proyecto->Nombre_pro!=""){
				$template=str_replace("@titulo_pag", htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_pro))), $template);
			}else{
				$template=$this->create_row("", "", "titulo", $template, "no");
			}

			if($proyecto->Desc_pro!=""){
				$proyecto->Desc_pro=str_replace("“", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("”", '"', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("•", ' - ', $proyecto->Desc_pro);
				$proyecto->Desc_pro=str_replace("–", ' - ', $proyecto->Desc_pro);
				$template=$this->create_row("Descripción del Proyecto", htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($proyecto->Desc_pro)))), "descripcion", $template, "si", 1);
			}else{
				$template=$this->create_row("Descripción del Proyecto", "", "descripcion", $template, "no");
			}

			if($proyecto->detalle_equipos!="" && $proyecto->detalle_equipos!=NULL){
				$template=$this->create_row("Detalle Equipos", utf8_decode($proyecto->detalle_equipos), "detalle_equipos", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Equipos", "", "detalle_equipos", $template, "no");
			}

			if($proyecto->detalle_suministros!="" && $proyecto->detalle_suministros!=NULL){
				$template=$this->create_row("Detalle Suministros", utf8_decode($proyecto->detalle_suministros), "detalle_sumin", $template, "si", 1);
			}else{
				$template=$this->create_row("Detalle Suministros", "", "detalle_sumin", $template, "no");
			}

			if($proyecto->Inversion_pro!=""){
				$template=$this->create_row("Inversión", "USD ".$proyecto->Inversion_pro." millones", "inversion", $template, "si");
			}else{
				$template=$this->create_row("Inversión", "", "inversion", $template, "no");
			}


			$contactos_princ=$this->buscar_contactos_pro($proyecto->id_pro,1);
			$contactos_secun=$this->buscar_contactos_pro($proyecto->id_pro,0);

			$tmp_contact='';
			$item=0;
			if(((is_array($contactos_princ))&&($contactos_princ!=''))&&((is_array($contactos_secun))&&($contactos_secun!=''))){
				if((is_array($contactos_princ))&&($contactos_princ!='')){
					foreach($contactos_princ as $ct){
						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Contacto Principal';
							 $item=1;
							$tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}

					$item=0;

					if((is_array($contactos_secun))&&($contactos_secun!='')){
					foreach($contactos_secun as $ct){

						 if($item==0){
							$tmp_contact.='<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;">';
							 $tmp_contact.='Otros Contactos';
							 $item=1;
							 $tmp_contact.='</span>';
						 }

						$tmp_contact.='<div style="padding:7px 15px;"><table class="confluenceTable" cellspacing="0" cellpadding="0" border="1">';
						if($ct->Nombre_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Nombre Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Nombre_contact.'</td>
						</tr>';
						}

						if($ct->Empresa_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Empresa Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Empresa_contact.'</td>
						</tr>';
						}

						if($ct->Cargo_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Cargo Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Cargo_contact.'</td>
						</tr>';
						}

						if($ct->Email_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Email Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Email_contact.'</td>
						</tr>';
						}

						if($ct->Telefono_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Teléfono Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Telefono_contact.'</td>
						</tr>';
						}

						if($ct->Direccion_contact!=''){
						$tmp_contact.='<tr>
							<td class="confluenceTd"  style="border-collapse:collapse" width="15%" nowrap="nowrap">Direcci&oacute;n Contacto:</td>
							<td class="confluenceTd"  style="border-collapse:collapse" width="85%">'.$ct->Direccion_contact.'</td>
						</tr>';
						}

					$tmp_contact.='</table></div>';
					}
				}
					$template=str_replace("@contactos", utf8_decode($tmp_contact) , $template);
					$template=str_replace("@style_contactos",'', $template);
		   }
			else{
				$template=str_replace("@contactos", $tmp_contact , $template);
				$template=str_replace("@style_contactos",'display:none;', $template);
			}


			/*if($proyecto->Nombre_contacto_pro!=""){
				$template=$this->create_row("Nombre Contacto", $proyecto->Nombre_contacto_pro, "nombre_contacto", $template, "si");
			}else{
				$template=$this->create_row("Nombre Contacto", "", "nombre_contacto", $template, "no");
			}

			if($proyecto->Empresa_contacto_pro!=""){
				$template=$this->create_row("Empresa Contacto", $proyecto->Empresa_contacto_pro, "empresa_contacto", $template, "si");
			}else{
				$template=$this->create_row("Empresa Contacto", "", "empresa_contacto", $template, "no");
			}

			if($proyecto->Cargo_contacto_pro!=""){
				$template=$this->create_row("Cargo Contacto", $proyecto->Cargo_contacto_pro, "cargo_contacto", $template, "si");
			}else{
				$template=$this->create_row("Cargo Contacto", "", "cargo_contacto", $template, "no");
			}

			if($proyecto->Email_contacto_pro!=""){
				$template=$this->create_row("Email Contacto", $proyecto->Email_contacto_pro, "email_contacto", $template, "si");
			}else{
				$template=$this->create_row("Email Contacto", "", "email_contacto", $template, "no");
			}

			if($proyecto->Direccion_contacto_pro!=""){
				$template=$this->create_row("Dirección Contacto", $proyecto->Direccion_contacto_pro, "_direccion_contacto", $template, "si");
			}else{
				$template=$this->create_row("Dirección Contacto", "", "_direccion_contacto", $template, "no");
			}

			if($proyecto->Telefono_contacto_pro!=""){
				$template=$this->create_row("Teléfono Contacto", $proyecto->Telefono_contacto_pro, "telefono_contacto", $template, "si");
			}else{
				$template=$this->create_row("Teléfono Contacto", "", "telefono_contacto", $template, "no");
			}

			if($proyecto->Otros_contacto_pro!="" && $proyecto->Otros_contacto_pro!=NULL){
				$template=$this->create_row("Otros Contactos", $proyecto->Otros_contacto_pro, "otros_contactos", $template, "si");
			}else{
				$template=$this->create_row("Otros Contactos", "", "otros_contactos", $template, "no");
			}*/

			if($proyecto->ultima_informacion_pro!="" && $proyecto->ultima_informacion_pro!=NULL){
				$proyecto->ultima_informacion_pro=$this->params->elimina_signos($proyecto->ultima_informacion_pro);
				$template=$this->create_row("", ($proyecto->ultima_informacion_pro) , "ultima_informacion", $template, "si");
			}else{
				$template=$this->create_row("", "", "ultima_informacion", $template, "no");
			}

			if($proyecto->Nombre_pais!="" && $proyecto->Nombre_pais!=NULL){
				$template=$this->create_row("país", substr($proyecto->Nombre_pais, 20) , "pais", $template, "si");
			}else{
				$template=$this->create_row("", "", "pais", $template, "no");
			}

			if($proyecto->Nombre_region!="" && $proyecto->Nombre_region!=NULL){
				$template=$this->create_row("Región", substr($proyecto->Nombre_region, 20) , "region", $template, "si");
			}else{
				$template=$this->create_row("", "", "region", $template, "no");
			}

			if($proyecto->Nombre_comuna!="" && $proyecto->Nombre_comuna!=NULL){
				$template=$this->create_row("Comuna", substr($proyecto->Nombre_comuna, 20) , "comuna", $template, "si");
			}else{
				$template=$this->create_row("", "", "comuna", $template, "no");
			}

			if($proyecto->url_confluence_pro!="" && $proyecto->url_confluence_pro!=NULL){
				if(strstr($proyecto->url_confluence_pro, "?")){
					$url=$proyecto->url_confluence_pro."&amp;print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}else{
					$url=$proyecto->url_confluence_pro."?print=1";
					$template=str_replace("@paramsurl", $url, $template);
				}
			}

			if(isset($etapa_act)){
				//cambio hecho 03-12-2014 Felipe
				//$template=str_replace("@nombre_etapa", htmlentities(utf8_decode($etapa_act)), $template);
				$template=$this->create_row("", htmlentities(utf8_decode($etapa_act)), "nombre_etapa", $template, "si");
			}else
				$template=$this->create_row("", "", "nombre_etapa", $template, "no");
			if(!$fechas_tl=$this->get_fechas_tl($id)){
				$template=str_replace("@fecha_inicio", "", $template);
				$template=str_replace("@fecha_desde", "", $template);
				$template=str_replace("@fecha_hasta", "", $template);
				$template=str_replace("@_fecha_desde_f", "", $template);
				$template=str_replace("@_fecha_hasta_f", "", $template);
			}
			$template=str_replace("@_urlinforma", $url_informa, $template);

			//$template=str_replace("@__urladjunta", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);
			$template=str_replace("@__urlveradjunto", $this->params->url_confluence_attach.$proyecto->id_pagina_pro, $template);

			if(strstr($proyecto->url_confluence_pro, "?"))
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."&amp;gallery", $template);
			else
				$template=str_replace("@____urlvergaleria", $proyecto->url_confluence_pro."?gallery", $template);
			$template=str_replace("@id_usuario_modif", $proyecto->id_usuario_modifica, $template);
			$template=str_replace("@latitud", $proyecto->Latitud_pro, $template);
			$template=str_replace("@longitud", $proyecto->Longitud_pro, $template);
			$template=str_replace("@nombre_mina", htmlentities(html_entity_decode(utf8_decode($proyecto->Nombre_pro))), $template);
			$template=str_replace("@nom_xml", $nom_xml, $template);
			$template=str_replace("@fecha_inicio", $fechas_tl[0], $template);
			$template=str_replace("@fecha_desde", $fechas_tl[1], $template);
			$template=str_replace("@fecha_hasta", $fechas_tl[2], $template);
			$template=str_replace("@_fecha_desde_f", $fechas_tl[3], $template);
			$template=str_replace("@_fecha_hasta_f", $fechas_tl[4], $template);
			//$this->guardar_html_proyecto($proyecto->id_pro, $template);
			
			return($template);
		}else{
			return(false);
		}
	}

	function guarda_pagina($id, $id_pag, $nro_version, $url_confluence_pro,$titulo_confluence_pro,$space_confluence_pro){
		$datos=array("id_pagina_pro"=>$id_pag, "nro_version"=>$nro_version, "url_confluence_pro"=>$url_confluence_pro, "titulo_confluence_pro"=>$titulo_confluence_pro, "space_confluence_pro"=>$space_confluence_pro);
		$this->db->where('id_pro', $id);
		return($this->db->update('proyectos', $datos));
	}

	function guarda_pagina_intranet($id, $id_pag, $nro_version, $url_confluence_pro,$titulo_confluence_pro){
		$datos=array("id_pagina_pro_intranet"=>$id_pag, "nro_version_intranet"=>$nro_version, "url_confluence_pro_intranet"=>$url_confluence_pro, "titulo_confluence_pro_intranet"=>$titulo_confluence_pro);
		$this->db->where('id_pro', $id);
		return($this->db->update('proyectos', $datos));
	}

	function guarda_pagina_example($id, $id_pag, $nro_version, $url_confluence_pro,$titulo_confluence_pro){
		$datos=array("id_pagina_pro"=>$id_pag, "nro_version"=>$nro_version, "url_confluence_pro"=>$url_confluence_pro, "titulo_confluence_pro"=>$titulo_confluence_pro);
		$this->db->where('id_pro', $id);
		return($this->db->update('proyectos2', $datos));
	}

	/*function create_row($titulo, $cont){
		$return="";
		if($cont!=""){
			$return="<tr><td>".htmlentities(((utf8_decode($titulo))))." :</td><td>".htmlentities(html_entity_decode(utf8_decode($this->params->br2nl($cont))))."</td></tr>";
		}
		return($return);
	}*/

	function create_row($titulo, $cont, $name, $content, $visible, $parsear=0){
		$return="";
		$titulo=$titulo;
		if($visible=="si"){
			if($parsear==0){
				$cont=utf8_decode($this->params->br2nl($cont));
			}
			$content=str_replace("@".$name, nl2br($cont), $content);
			$content=str_replace("@style_".$name, "", $content);
			$content=str_replace("@titulo_".$name, $titulo, $content);
		}else{
			$content=str_replace("@".$name, "", $content);
			$content=str_replace("@style_".$name, "display:none;", $content);
			$content=str_replace("@titulo_".$name, "", $content);
		}
		return($content);
	}

	function create_row1($titulo, $cont, $name, $content, $visible, $parsear=0){
		$return="";
		$titulo=$titulo;
		if($visible=="si"){
			if($parsear==0){
				$cont=$this->params->br2nl($cont);
			}
			$content=str_replace("@".$name, nl2br($cont), $content);
			$content=str_replace("@style_".$name, "", $content);
			$content=str_replace("@titulo_".$name, $titulo, $content);
		}else{
			$content=str_replace("@".$name, "", $content);
			$content=str_replace("@style_".$name, "display:none;", $content);
			$content=str_replace("@titulo_".$name, "", $content);
		}
		return($content);
	}

	function generar_labels($id){
		$this->db->where("id_pro",$id);
		$this->db->select("pro.id_sector, man.id_emp, sec.Nombre_sector, man.Nombre_fantasia_emp, p.Nombre_pais, r.Nombre_region, com.Nombre_comuna");
		$this->db->join("proyectos_sector sec", "sec.id_sector=pro.id_sector", "left");
		$this->db->join("empresas man", "man.id_emp=pro.id_man_emp", "left");
		$this->db->join("u_pais p", "p.id_pais=pro.id_pais", "left");
		$this->db->join("u_region r", "r.id_region=pro.id_region", "left");
		$this->db->join("u_comuna com", "com.id_comuna=pro.id_comuna", "left");
		$query=$this->db->get("proyectos pro");
		$rs1=$query->first_row();
		$str_label="";
		$labels=array();
		if(is_object($rs1)){
			$razon_s1=$rs1->id_emp;
			$space=$this->params->spaces_proy[$rs1->id_sector];
			unset($rs1->id_sector);
			unset($rs1->id_emp);

			$rs=get_object_vars($rs1);
			foreach($rs as $r){
				if($r!="" && $r!=NULL){
					$valor=(html_entity_decode(utf8_decode($r)));
					$arr1=explode(" ", $valor);
					foreach($arr1 as $varr){
						if(!in_array(strtolower($varr), ($labels))){
							$labels[]=strtolower($varr);
						}
					}
				}
			}

			if($result=$this->mostrar_tipos($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_tipo)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}

			if($result=$this->mostrar_obras($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_obra)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}

			/*if($result=$this->mostrar_equipos($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_equipo)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}

					}
				}
			}*/

			if($result=$this->mostrar_propietarios($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						if($razon_s1!=$rs->id_emp){
							$valor=(html_entity_decode(utf8_decode($rs->Nombre_fantasia_emp)));
							$arr1=explode(" ", $valor);
							foreach($arr1 as $varr){
								if(!in_array(strtolower($varr), ($labels))){
									$labels[]=strtolower($varr);
								}
							}
						}
					}
				}
			}

			/*if($result=$this->mostrar_servicios($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_serv)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}

			if($result=$this->mostrar_serv_cat($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=html_entity_decode(utf8_decode($rs->Nombre_cat_serv));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}

			if($result=$this->mostrar_serv_subcat($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_sub_serv)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}*/

			/*if($result=$this->mostrar_suministros($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_sumin)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}*/

			$labels=$this->params->ordenar($labels);
			if(is_array($labels) && sizeof($labels)>0){
				$y=0;
				$this->params->borra_label($id, $this->params->modulo["proyecto"]);
				foreach($labels as $lbl){
					$lbl=str_replace(",", "", $lbl);
					$lbl=str_replace("&", "and", $lbl);
					$lbl=str_replace(";", " ", $lbl);
					$lbl=str_replace(":", ",", $lbl);
					$lbl=str_replace("-", "", $lbl);
					$tofind = "ÒÓÔÕÖØ–:;";
					$replac = "óóóóóó-,,";
					$lbl=utf8_encode((strtr(($lbl),$tofind,$replac)));
					$lbl=utf8_decode(($lbl));
					$lbl=str_replace("-", "", $lbl);
					if(in_array(strtolower($lbl),$this->params->no_label)){
						unset($lbl);
					}

					if(isset($lbl) && strtolower($lbl) != "otros" && $lbl != "" && $lbl != " " && $lbl != "?"){
						$lbl=str_replace("?", "", $lbl);
						$lbl=str_replace("-", "", $lbl);
						$lbl=str_replace("(", "", $lbl);
						$lbl=str_replace(")", "", $lbl);
						$lbl=str_replace('"', "", $lbl);
						$lbl=str_replace(' ', "", $lbl);
						if(intval($lbl)!=1){
							$text_full=$lbl;
							$tl=ucfirst(strtolower($lbl));
							if($str_label!="")
								$str_label.=",".$tl;
							else
								$str_label.=$tl;

							$text_label=str_replace(",", "", $tl);
							$datos["id_parent"]=$id;
							$datos["id_modulo"]=$this->params->modulo["proyecto"];
							$datos["texto_full"]=$this->params->elimina_signos_label(utf8_encode($text_full));
							$datos["texto_label"]=$this->params->elimina_signos_label($this->params->elimina_acentos($text_label));

							if($datos["texto_full"]!="Otros" || $datos["texto_full"]!="otros"){
								$words=explode(" ", $datos["texto_full"]);
								if(sizeof($words)>1){
									$datos["url_label"]="<a class='label_content' href='".$this->params->url_search.str_replace(" ", "+", ($this->params->elimina_signos($datos["texto_full"])))."&where=".$space."'>@replace</a>";
								}else{
									$datos["url_label"]="<a class='label_content' href='".$this->params->url_tags.$space."/".$datos["texto_full"]."'>@replace</a>";
								}
								$this->params->guarda_label($datos);
							}
							++$y;
						}
					}
				}
			}

			$rt=utf8_encode(html_entity_decode(str_replace(" ", ",",str_replace(" ", ",", htmlentities($this->params->elimina_signos_label($str_label))))));
			//$rt=str_replace(",,", ",", $rt);
			return $rt;
		}else{
			return(false);
		}
	}

	function generar_labels_sin_sector($id){
		$this->db->where("id_pro",$id);
		$this->db->select("pro.id_sector, man.id_emp, man.Nombre_fantasia_emp, p.Nombre_pais, r.Nombre_region, com.Nombre_comuna");
		$this->db->join("proyectos_sector sec", "sec.id_sector=pro.id_sector", "left");
		$this->db->join("empresas man", "man.id_emp=pro.id_man_emp", "left");
		$this->db->join("u_pais p", "p.id_pais=pro.id_pais", "left");
		$this->db->join("u_region r", "r.id_region=pro.id_region", "left");
		$this->db->join("u_comuna com", "com.id_comuna=pro.id_comuna", "left");
		$query=$this->db->get("proyectos pro");
		$rs1=$query->first_row();
		$str_label="";
		$labels=array();
		if(is_object($rs1)){
			$razon_s1=$rs1->id_emp;
			$space=$this->params->spaces_proy[$rs1->id_sector];
			unset($rs1->id_sector);
			unset($rs1->id_emp);

			$rs=get_object_vars($rs1);
			foreach($rs as $r){
				if($r!="" && $r!=NULL){
					$valor=(html_entity_decode(utf8_decode($r)));
					$arr1=explode(" ", $valor);
					foreach($arr1 as $varr){
						if(!in_array(strtolower($varr), ($labels))){
							$labels[]=strtolower($varr);
						}
					}
				}
			}

			if($result=$this->mostrar_tipos($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_tipo)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}

			if($result=$this->mostrar_obras($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						$valor=(html_entity_decode(utf8_decode($rs->Nombre_obra)));
						$arr1=explode(" ", $valor);
						foreach($arr1 as $varr){
							if(!in_array(strtolower($varr), ($labels))){
								$labels[]=strtolower($varr);
							}
						}
					}
				}
			}

			if($result=$this->mostrar_propietarios($id)){
				if(is_array($result) && sizeof($result)>0){
					foreach($result as $rs){
						if($razon_s1!=$rs->id_emp){
							$valor=(html_entity_decode(utf8_decode($rs->Nombre_fantasia_emp)));
							$arr1=explode(" ", $valor);
							foreach($arr1 as $varr){
								if(!in_array(strtolower($varr), ($labels))){
									$labels[]=strtolower($varr);
								}
							}
						}
					}
				}
			}

			$labels=$this->params->ordenar($labels);

			if(is_array($labels) && sizeof($labels)>0){
				$y=0;
				$this->params->borra_label($id, $this->params->modulo["proyecto"]);
				foreach($labels as $lbl){
					$lbl=str_replace(",", "", $lbl);
					$lbl=str_replace("&", "and", $lbl);
					$lbl=str_replace(";", " ", $lbl);
					$lbl=str_replace(":", ",", $lbl);
					$lbl=str_replace("-", "", $lbl);
					$tofind = "ÒÓÔÕÖØ–:;";

					$replac = "óóóóóó-,,";
					$lbl=utf8_encode((strtr(($lbl),$tofind,$replac)));
					$lbl=utf8_decode(($lbl));
					$lbl=str_replace("-", "", $lbl);
					if(in_array(strtolower($lbl),$this->params->no_label)){
						unset($lbl);
					}
					//quitar etiqueta sector
					$q_sector=$this->db->get('proyectos_sector');
					foreach ($q_sector->result() as $s) {
						if((isset($lbl))&&(strtolower($s->Nombre_sector)==strtolower($lbl))){
						unset($lbl);
						}
					}

					if(isset($lbl) && strtolower($lbl) != "otros" && $lbl != "" && $lbl != " " && $lbl != "?"){
						$lbl=str_replace("?", "", $lbl);
						$lbl=str_replace("-", "", $lbl);
						$lbl=str_replace("(", "", $lbl);
						$lbl=str_replace(")", "", $lbl);
						$lbl=str_replace('"', "", $lbl);
						$lbl=str_replace(' ', "", $lbl);
						if(intval($lbl)!=1){
							$text_full=$lbl;
							$tl=ucfirst(strtolower($lbl));
							if($str_label!="")
								$str_label.=",".$tl;
							else
								$str_label.=$tl;

							$text_label=str_replace(",", "", $tl);
							$datos["id_parent"]=$id;
							$datos["id_modulo"]=$this->params->modulo["proyecto"];
							$datos["texto_full"]=$this->params->elimina_signos_label(utf8_encode($text_full));
							$datos["texto_label"]=$this->params->elimina_signos_label($this->params->elimina_acentos($text_label));

							if($datos["texto_full"]!="Otros" || $datos["texto_full"]!="otros"){
								$words=explode(" ", $datos["texto_full"]);
								if(sizeof($words)>1){
									$datos["url_label"]="<a class='label_content' href='".$this->params->url_search.str_replace(" ", "+", ($this->params->elimina_signos($datos["texto_full"])))."&where=".$space."'>@replace</a>";
								}else{
									$datos["url_label"]="<a class='label_content' href='".$this->params->url_tags.$space."/".$datos["texto_full"]."'>@replace</a>";
								}
								$this->params->guarda_label($datos);
							}
							++$y;
						}
					}
				}
			}

			$rt=utf8_encode(html_entity_decode(str_replace(" ", ",",str_replace(" ", ",", htmlentities($this->params->elimina_signos_label($str_label))))));
			//$rt=str_replace(",,", ",", $rt);
			return $rt;
		}else{
			return(false);
		}
	}

	function mostrar_labels($id){
		$return=array();
		$this->db->where("id_parent", $id);
		$this->db->where("id_modulo", $this->params->modulo["proyecto"]);
		$query=$this->db->get("labels");
		$result=$query->result();

		if(is_array($result) && sizeof($result)>0){
			foreach($result as $rs){
				$return[]=html_entity_decode(utf8_decode($rs->texto_full));
			}
		}
		return($return);
	}

	function generar_grafico_js($id_sector){
		if($id_sector!=""){
			$result="";
			$sector="";
			$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->grafico_template);
			$datos="";
			$nombre="";
			if($r=$this->cargar_regiones($this->params->id_pais_chile)){
				$x=0;
				foreach($r as $rs){

					if($rs1=$this->contar_x_region($rs->id_region, $id_sector)){
						if($rs1->total>0){
							if(intval($rs->orden==13))
								$rs->nombre_corto="RM - 1";
							else
								$rs->nombre_corto=explode(" - ", $rs->nombre_corto);
							if($x<=sizeof($this->params->colores)-1){
								$color=$this->params->colores[$x];
								++$x;
							}else{
								$x=0;
								$color=$this->params->colores[$x];
							}
							if($datos==""){
								$datos.="[{y:".round($rs1->total).", color:'".$color."'}";
								//$datos.="[".round($rs->total);
								$nombre.="['".$rs->nombre_corto[0]."'";
							}else{

								$datos.=",{y:".round($rs1->total).", color:'".$color."'}";
								//$datos.=",".round($rs->total);
								$nombre.=",'".$rs->nombre_corto[0]."'";
							}
						}
					}
				}

				if($datos!="" && $nombre!=""){
					$datos.="]";
					$nombre.="]";
				}
				$template=str_replace("@data", ((($datos))), $template);
				$template=str_replace("@nombre", ((($nombre))), $template);
				$template=str_replace("@url_home", ((($url=$this->params->url_confluence.$this->params->spaces_proy[$id_sector]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[$id_sector]]))), $template);
				$template=str_replace("@id_sector", ((($id_sector))), $template);
				$template=str_replace("@titulo_y", ((("Total Proyectos"))), $template);
				$url_js=CONFLUENCE_FILES;

				$url_js_http=base_url().$this->params->file_graficos_tipo[$id_sector];
				$js="var grafico".$id_sector."=\"".$template.'";';
				$file=explode("/", $this->params->file_graficos_tipo[$id_sector]);
				$a=file_put_contents($url_js.$file[1], $js);
				if($a){
					return($url_js_http);
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

	function generar_grafico_js2($id_etapa=1){
			$result="";
			$sector="";
			$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->grafico_template2);
			$datos="";
			$nombre="";
			if($id_etapa!="" && $id_etapa!=NULL){
				if($r=$this->contar_proy_etapa_act($id_etapa)){
					$x=0;
					$nombre.="['1. Minería', '2. Energía']";
					$datos.="[{y:".$r[0].", color:'".$this->params->colores[3]."'},{y:".$r[1].", color:'".$this->params->colores[4]."'}]";
					$template=str_replace("@data", ((($datos))), $template);
					$template=str_replace("@nombre", ((($nombre))), $template);
					$template=str_replace("@url_home", ((($url=$this->params->url_confluence.$this->params->spaces_proy[1]."/".$this->params->confluence_home_proyectos[$this->params->spaces_proy[1]]))), $template);
					$template=str_replace("@titulo_y", ((("Total Proyectos"))), $template);
					$template=str_replace("@id_etapa", ((($id_etapa))), $template);
					$url_js=CONFLUENCE_FILES;
					$url_js_http=base_url().$this->params->file_graficos_etp[$id_etapa];

					$js="grafico_etp[".$id_etapa."]=\"".$template.'";';
					$file=explode("/", $this->params->file_graficos_etp[$id_etapa]);
					$a=file_put_contents($url_js.$file[1], $js);
					if($a){
						return($url_js_http);
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



	function cargar_datos_etapa_actual($id){
		$this->db->select("eta.id_etapa, eta.Nombre_etapa, tc.Nombre_tipo_contrato, tc.Abreviacion_tipo_contrato , emp.Nombre_fantasia_emp, emp.id_emp");
		$this->db->where("pro.id_pro", $id);
		$this->db->join("proyectos_x_etapas peta", "peta.id_pro=pro.id_pro and peta.id_etapa=pro.etapa_actual_pro", "inner");
		$this->db->join("proyectos_etapas eta", "eta.id_etapa=pro.etapa_actual_pro", "inner");
		$this->db->join("empresas emp", "emp.id_emp=peta.id_emp", "inner");
		$this->db->join("tipos_contratos tc", "tc.id_tipo_contrato=peta.id_tipo_contrato", "inner");
		$query=$this->db->get("proyectos pro");
		$result=$query->first_row();
		if(is_object($result)){
			return($result);
		}else{
			return(false);
		}
	}

	function cargar_regiones($id_pais=""){
		$this->db->order_by("orden", "asc");

		if($id_pais!=""){
			$this->db->where("id_pais", $id_pais);
		}

		$query=$this->db->get("u_region");
		$r=$query->result();
		if(is_array($r) && sizeof($r)>0){
			return($r);
		}else{
			return(false);
		}
	}

	function contar_x_region($id_region, $id_sector){

		$this->db->select("count(*) total, ps.Nombre_sector sector, ps.id_sector");
		$this->db->where("r.id_region", $id_region);
		$this->db->where("pro.id_sector", $id_sector);
		$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'left');
		$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
		$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
		$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
		$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
		$query=$this->db->get("proyectos pro");
		$r=$query->first_row();
		if(is_object($query)){
			return($r);
		}else{
			return(false);
		}
	}

	function get_xml_file($id_pro){

		if($etapas=$this->get_etapas($id_pro)){
			$xml_template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->xml_template);
			$events="";
			if(is_array($etapas) && sizeof($etapas)>0){
				foreach($etapas as $etp){
					if($etp->trim_inicio != 0 && $etp->trim_fin != 0 && $etp->ano_inicio != 0 && $etp->ano_fin != 0){
						$descripcion=htmlentities("<div style='clear:both; padding-top:3px; padding-bottom:3px;'>")."Fecha de Inicio: ".utf8_encode(html_entity_decode($this->params->trimestre[$etp->trim_inicio]))." de ".$etp->ano_inicio."\n".htmlentities("</div>");
						$descripcion.=htmlentities("<div style='clear:both; padding-top:3px; padding-bottom:3px;'>")."Fecha de Término: ".utf8_encode(html_entity_decode($this->params->trimestre[$etp->trim_fin]))." de ".$etp->ano_fin."\n".htmlentities("</div>");
						if($etp->empresa!="" && $etp->empresa!=NULL)
							$descripcion.=htmlentities("<div style='clear:both; padding-top:3px; padding-bottom:3px;'>")."Empresa Responsable: ".$this->params->elimina_signos_nombre($etp->empresa)."\n".htmlentities("</div>");
						if($etp->tipo_con !="" && $etp->av_tipo_con !="" && $etp->tipo_con !=NULL && $etp->av_tipo_con !=NULL)
							$descripcion.=htmlentities("<div style='clear:both; padding-top:3px; padding-bottom:3px;'>")."Tipo de Contrato: ".$this->params->elimina_signos_nombre($etp->tipo_con)." (".$etp->av_tipo_con.")".htmlentities("</div>");
						$inicio=$this->parse_trim_date($etp->trim_inicio, $etp->ano_inicio, 0);
						$fin=$this->parse_trim_date($etp->trim_fin, $etp->ano_fin, 1);
						$etapa=$this->params->template_xml_barra;
						$etapa=str_replace("@titulo", $etp->Nombre_etapa, $etapa);
						$etapa=str_replace("@inicio", $inicio, $etapa);
						$etapa=str_replace("@fin", $fin, $etapa);
						$etapa=str_replace("@descripcion", ($descripcion), $etapa);

						$etapa=str_replace("@color", $etp->color, $etapa);
						$events.=$etapa;
					}
				}
			}

			if($hitos=$this->get_hitos($id_pro)){
				if(is_array($hitos) && sizeof($hitos)>0){
					foreach($hitos as $ht){
						$descripcion=htmlentities("<div style='clear:both; padding-top:3px; padding-bottom:3px;'>")."Fecha del Hito: ".$this->params->trimestre[$ht->trim_hito]." de ".$ht->ano_hito."\n".htmlentities("</div>");
						if($ht->descripcion!="" && $ht->descripcion!="")
							$descripcion.=htmlentities("<div style='clear:both; padding-top:3px; padding-bottom:3px;'>")."Descripción: ".$this->params->elimina_signos_nombre(($ht->descripcion))."\n".htmlentities("</div>");
						$hito=$this->params->template_xml_hito;
						$inicio=$this->parse_trim_date($ht->trim_hito, $ht->ano_hito, 2);
						$hito=str_replace("@inicio", $inicio, $hito);
						$hito=str_replace("@titulo", $ht->Nombre_hito, $hito);
						$hito=str_replace("@icon", $this->params->imagen_hito, $hito);
						$hito=str_replace("@descripcion", ($descripcion), $hito);
						$events.=$hito;
					}
				}else{
					return(false);
				}
			}

			if($events!=""){
				$xml_template=str_replace("@params", $events, $xml_template);
				$xml_template=str_replace("&",'&amp;',$xml_template);
				$ruta_xml=CONFLUENCE_FILES.$id_pro.".xml";
				
							
				if(file_put_contents($ruta_xml, $xml_template)){
					@chmod($ruta_xml, 0777);
					return($id_pro);
				}else{
					return(false);
				}
				

			}else{
				$ruta_xml=CONFLUENCE_FILES.$id_pro.".xml";
				$ruta_xml_url=base_url().$this->params->ruta_xml.$id_pro.".xml";
				
				
				
				
				if(file_put_contents($ruta_xml, "")){
					@chmod($ruta_xml, 0777);
					return($id_pro);
				}else{
					return(false);
				}
			}

		}else{
			return(false);
		}
	}

	function get_etapas($id_pro=""){
		if($id_pro!=""){
			$this->db->select("pro.Etapa_actual_pro etapa_actual, etp.*, eta.*, emp.Nombre_fantasia_emp empresa, tcon.Nombre_tipo_contrato tipo_con, tcon.Abreviacion_tipo_contrato av_tipo_con");
			$this->db->where("pro.id_pro", $id_pro);
			$this->db->join("proyectos_x_etapas etp", "pro.id_pro=etp.id_pro", "left");
			$this->db->join("proyectos_etapas eta", "etp.id_etapa=eta.id_etapa", "left");
			$this->db->join("empresas emp", "emp.id_emp=etp.id_emp", "left");
			$this->db->join("tipos_contratos tcon", "tcon.id_tipo_contrato=etp.id_tipo_contrato", "left");
			$this->db->order_by("eta.id_etapa", "asc");
			$query=$this->db->get("proyectos pro");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				return($rs);
			}else{
				$this->db->select("pro.Etapa_actual_pro etapa_actual, eta.*");
				$this->db->where("pro.id_pro", $id_pro);
				$this->db->join("proyectos_etapas eta", "pro.Etapa_actual_pro=eta.id_etapa", "inner");
				$query=$this->db->get("proyectos pro");
				$rs=$query->result();
				if(is_array($rs) && sizeof($rs)>0){
					return($rs);
				}else{
					return(false);
				}
			}
		}else{
			return(false);
		}
	}

	function get_ubicacion($id_pro=""){
		if($id_pro!=""){
			$this->db->select("p.Nombre_pais, r.Nombre_region, c.Nombre_comuna");
			$this->db->where("pro.id_pro", $id_pro);
			$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
			$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
			$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
			$query=$this->db->get("proyectos pro");
			$rs=$query->first_row();
			return($rs);
		}else{
			return(false);
		}
	}

	function get_hitos($id_pro=""){
		if($id_pro!=""){
			$this->db->select("pxh.*, ph.*");
			$this->db->where("pro.id_pro", $id_pro);
			$this->db->join("proyectos_x_hitos pxh", "pro.id_pro=pxh.id_pro", "inner");
			$this->db->join("proyectos_hitos ph", "ph.id_hito=pxh.id_hito", "inner");
			$this->db->order_by("pxh.ano_hito", "desc");
			$this->db->order_by("pxh.trim_hito", "desc");
			$query=$this->db->get("proyectos pro");
			$rs=$query->result();
			return($rs);
		}else{
			return(false);
		}
	}

	function get_fechas_tl($id_pro){
		$etp=$this->get_etapas($id_pro);
		if(is_array($etp) && sizeof($etp)>0){

			$min_ano=0;
			$min_trim=0;
			$max_ano=0;
			$max_trim=0;
			$x=0;
			foreach($etp as $index=>$stg){
				if($stg->trim_inicio > 0 && $stg->trim_fin > 0 && $stg->ano_inicio > 0 && $stg->ano_fin > 0){
					if($x==0){

						$min_ano=$stg->ano_inicio;
						$min_trim=$stg->trim_inicio;
						$max_ano=$stg->ano_fin;
						$max_trim=$stg->trim_fin;
					}else{
						if($stg->ano_inicio<$min_ano){

							$min_ano=$stg->ano_inicio;
							$min_trim=$stg->trim_inicio;
							$actual=$index;
						}

						if($stg->ano_fin>$max_ano){

								$max_ano=$stg->ano_fin;
								$max_trim=$stg->trim_fin;
								$actual=$index;
						}else if($stg->ano_fin==$max_ano){

							if($stg->trim_fin>$max_trim){
								$max_ano=$stg->ano_fin;
								$max_trim=$stg->trim_fin;
								$actual=$index;
							}
						}
					}
					if($stg->etapa_actual==$stg->id_etapa){
						$actual=$index;
					}
					++$x;
				}
			}
			if(isset($actual)){
				$et_act=$etp[$actual];




				if($et_act->trim_inicio==$et_act->trim_fin){
					$trim_act=$et_act->trim_inicio;
				}else{
					$trim_act=round((intval($et_act->trim_fin)+intval($et_act->trim_inicio))/2);
				}
				if($et_act->ano_inicio==$et_act->ano_fin){
					$ano_act=$et_act->ano_inicio;
				}else{
					$ano_act=round((intval($et_act->ano_fin)+intval($et_act->ano_inicio))/2);
				}
				$dia=15;
				if($trim_act==1){
					$mes=2;
				}elseif($trim_act==2){
					$mes=5;
				}elseif($trim_act==3){
					$mes=8;
				}elseif($trim_act==4){
					$mes=11;
				}
				$fecha_inicio=(date("D M d Y H:i:s", mktime(0, 0, 0, intval($mes)+9, $dia, $ano_act))." GMT-0600");
			}
			if(is_array($etp) && sizeof($etp)>0){
				if($min_trim==1){
					$mes=1;
				}elseif($min_trim==2){
					$mes=3;
				}elseif($min_trim==3){
					$mes=6;
				}elseif($min_trim==4){
					$mes=9;
				}
				if(isset($mes)){
					$fecha_desde=(date("D M d Y H:i:s", mktime(0, 0, 0, $mes, 1, $min_ano))." GMT-0600");
					$fecha_inicio=(date("D M d Y H:i:s", mktime(0, 0, 0, intval($mes)+7, 1, $min_ano))." GMT-0600");
					$fecha_desde_f=(date("Y-m-d", mktime(0, 0, 0, $mes, 1, $min_ano)));
					//$fecha_desde_f=date("Y-m-d",strtotime("-1 second",strtotime("01/01/".(intval($min_ano))." 00:00:00")));
					//$fecha_desde_f=date("Y-m-d", mktime(0, 0, 0, $mes, 1, intval($min_ano)-1));

					if($max_trim==1){

						$mes=3;
						$dia=date("d",strtotime("-1 second",strtotime("4/01/".$max_ano." 00:00:00")));
					}elseif($max_trim==2){

						$mes=6;
						$dia=date("d",strtotime("-1 second",strtotime("7/01/".$max_ano." 00:00:00")));
					}elseif($max_trim==3){

						$mes=9;
						$dia=date("d",strtotime("-1 second",strtotime("10/01/".$max_ano." 00:00:00")));
					}elseif($max_trim==4){

						$mes=12;
						$dia=date("d",strtotime("-1 second",strtotime("1/01/".(intval($max_ano)+1)." 00:00:00")));
					}

					$fecha_hasta=(date("D M d Y H:i:s", mktime(0, 0, 0, $mes, $dia, $max_ano))." GMT-0600");
					$fecha_hasta_f=date("Y-m-d", mktime(0, 0, 0, $mes, $dia, intval($max_ano)+1));
					return(array($fecha_inicio, $fecha_desde, $fecha_hasta, $fecha_desde_f, $fecha_hasta_f));
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

	function parse_trim_date($trim, $ano, $st=""){
		if($st==1){
			if($trim==1){
				$dia=date("d",strtotime("-1 second",strtotime("4/01/".$ano." 00:00:00")));
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 3, $dia, $ano))." GMT-0600");
			}elseif($trim==2){
				$dia=date("d",strtotime("-1 second",strtotime("7/01/".$ano." 00:00:00")));
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 6, $dia, $ano))." GMT-0600");
			}elseif($trim==3){
				$dia=date("d",strtotime("-1 second",strtotime("10/01/".$ano." 00:00:00")));
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 9, $dia, $ano))." GMT-0600");
			}elseif($trim==4){
				$dia=date("d",strtotime("-1 second",strtotime("1/01/".(intval($ano)+1)." 00:00:00")));
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 12, $dia, $ano))." GMT-0600");
			}else{
				return(false);
			}
		}else if($st==0){
			$dia=1;
			if($trim==1){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 1, $dia, $ano))." GMT-0600");
			}elseif($trim==2){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 4, $dia, $ano))." GMT-0600");
			}elseif($trim==3){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 7, $dia, $ano))." GMT-0600");
			}elseif($trim==4){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 10, $dia, $ano))." GMT-0600");
			}else{
				return(false);
			}
		}else{
			$dia=15;
			if($trim==1){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 2, $dia, $ano))." GMT-0600");
			}elseif($trim==2){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 5, $dia, $ano))." GMT-0600");
			}elseif($trim==3){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 8, $dia, $ano))." GMT-0600");
			}elseif($trim==4){
				return(date("D M d Y H:i:s", mktime(0, 0, 0, 11, $dia, $ano))." GMT-0600");
			}else{
				return(false);
			}
		}
	}

	function contar_proy($tipo_cli, $id_sector, $search1="", $username="%20"){
		if($tipo_cli==0){
			if($id_sector==0)
				$this->db->or_where("(ps.id_sector=".$this->params->id_sectores["mineria"]." or ps.id_sector=".$this->params->id_sectores["energia"].")");
			else
				$this->db->where("ps.id_sector", $id_sector);
		}else if($tipo_cli==1){
			if($id_sector!=0)
				$this->db->where("ps.id_sector", $id_sector);
		}else{
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
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
		}
		$this->db->select("count(DISTINCT pro.id_pro) total");
		$this->db->order_by("pro.id_sector", "asc");
		$this->db->order_by("pro.Nombre_pro", "asc");
		$this->db->join("proyectos_sector ps", "ps.id_sector = pro.id_sector", 'inner');
		$this->db->join("u_pais p", "p.id_pais = pro.id_pais", 'left');
		$this->db->join("u_region r", "r.id_region = pro.id_region", 'left');
		$this->db->join("u_comuna c", "c.id_comuna = pro.id_comuna", 'left');
		$this->db->join("empresas emp", "emp.id_emp = pro.id_man_emp", 'left');
		$this->db->where('Estado_pro !=', 'N');
		$this->db->where('Estado_pro !=', 'R');
		/*$this->db->where('Revision_pro', 'revisado');*/
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
						$this->db->like("pro.Nombre_pro", $v[1]);
					}

					if($v[0]=="estado"){
						if ($v[1] != 'O'){
							$this->db->where("pro.Estado_pro", $v[1]);
							$this->db->where("pro.Etapa_actual_pro <>", '8');
						}else{
							$this->db->where("pro.Etapa_actual_pro", '8');
						}
					}

				}
			}
		}
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->first_row();
		if(is_object($rs)){
			return($rs->total);
		}else{
			return(0);
		}
	}

	function servicios_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- Servicios -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){
			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(serv.id_serv), serv.Nombre_serv");
		$this->db->join("proyectos_x_servicios pxs", "pro.id_pro=pxs.id_pro", "inner");
		$this->db->join("servicios_principales serv","serv.id_serv=pxs.id_serv", "inner");
		$this->db->order_by("serv.Nombre_serv", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				$arr[$r->id_serv]=$r->Nombre_serv;
			}
		}
		return($arr);
	}

	function equipos_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- Equipos -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){
			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(eq.id_equipo), eq.Nombre_equipo");
		$this->db->join("proyectos_x_equipos pxeq","pro.id_pro=pxeq.id_pro", "inner");
		$this->db->join("equipos_principales eq","eq.id_equipo=pxeq.id_equipo", "inner");
		$this->db->order_by("eq.Nombre_equipo", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				$arr[$r->id_equipo]=$r->Nombre_equipo;
			}
		}
		return($arr);
	}

	function suministro_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- Suministro -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){
			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(sp.id_sumin), sp.Nombre_sumin");
		$this->db->join("proyectos_x_suministros pxs","pro.id_pro=pxs.id_pro", "inner");
		$this->db->join("suministros_principales sp","sp.id_sumin=pxs.id_sumin", "inner");
		$this->db->order_by("sp.Nombre_sumin", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				$arr[$r->id_sumin]=$r->Nombre_sumin;
			}
		}
		return($arr);
	}

	function obras_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- Obras -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){

			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(op.id_obra), op.Nombre_obra");
		$this->db->join("proyectos_x_obras pxo","pro.id_pro=pxo.id_pro", "inner");
		$this->db->join("obras_principales op","op.id_obra=pxo.id_obra", "inner");
		$this->db->order_by("op.Nombre_obra", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				$arr[$r->id_obra]=$r->Nombre_obra;
			}
		}
		return($arr);
	}

	function tipo_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- Tipos -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){
			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(pt.id_tipo), pt.Nombre_tipo");
		$this->db->join("proyectos_x_tipo pxt","pro.id_pro=pxt.id_pro", "inner");
		$this->db->join("proyectos_tipo pt","pt.id_tipo=pxt.id_tipo", "inner");
		$this->db->order_by("pt.Nombre_tipo", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				$arr[$r->id_tipo]=$r->Nombre_tipo;
			}
		}
		return($arr);
	}

	function pais_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- pa&iacute;s -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){
			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(p.id_pais), p.Nombre_pais");
		$this->db->join("u_pais p","p.id_pais=pro.id_pais", "inner");
		$this->db->order_by("p.Nombre_pais", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				$arr[$r->id_pais]=$r->Nombre_pais;
			}
		}
		return($arr);
	}

	function responsable_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- Responsable -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){
			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(emp.id_emp), emp.Nombre_fantasia_emp");
		$this->db->join("proyectos_x_etapas pxet","pro.id_pro=pxet.id_pro and pro.Etapa_actual_pro=pxet.id_etapa", "inner");
		$this->db->join("empresas emp","emp.id_emp=pxet.id_emp", "inner");
		$this->db->order_by("emp.Nombre_fantasia_emp", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				/*$emps=$this->empresa->buscar_nombre_compuesto($r->Nombre_fantasia_emp);
				if(is_array($emps)){
					foreach($emps as $e){
						if(!in_array($e->id_emp, array_keys($arr))){
							$arr[$e->id_emp]=$e->Nombre_fantasia_emp;
						}
					}
				}else*/
				// if($emps==true){
					if(!in_array($r->id_emp, array_keys($arr))){
						$arr[$r->id_emp]=$r->Nombre_fantasia_emp;
					}
				/*}else{
					if(!in_array($r->id_emp, array_keys($arr))){
						$arr[$r->id_emp]=$r->Nombre_fantasia_emp;
					}
				}*/
			}
		}
		return($arr);
	}

	function mandante_sel($tipo_socio=0, $id_sector=0, $username="%20"){
		$arr=array(""=>"- Mandante -");
		if($tipo_socio==$this->params->tipo_socio[0]){
			if($id_sector==$this->params->id_sectores["mineria"] || $id_sector==$this->params->id_sectores["energia"]){
				$this->db->where("pro.id_sector", $id_sector);
			}else{
				$this->db->or_where("(pro.id_sector=".$this->params->id_sectores["mineria"]." or pro.id_sector=".$this->params->id_sectores["energia"].")");
			}
		}else if($tipo_socio==$this->params->tipo_socio[1]){
			if($id_sector!=0){
				$this->db->where("pro.id_sector", $id_sector);
			}
		}else if($tipo_socio==$this->params->tipo_socio[2]){
			$this->db->where("us.username_socio", $username);
			$this->db->join("socio s", "sxs.id_socio=s.id_socio", "inner");
			$this->db->join("user_socio us", "us.id_socio=s.id_socio", "inner");
			$query=$this->db->get("socio_x_sector sxs");
			$rs=$query->result();
			if(is_array($rs) && sizeof($rs)>0){
				$cent=0;
				foreach($rs as $r){
					if($id_sector!=0){
						if($id_sector==$r->id_sector){
							$cent=1;
							$this->db->where("pro.id_sector", $r->id_sector);
						}
					}else{
						$cent=1;
						$this->db->or_where("pro.id_sector", $r->id_sector);
					}
				}
				if(isset($cent)){
					if($cent==0){
						$this->db->where("pro.id_sector", 0);
					}
				}
			}else{
				$this->db->where("pro.id_sector", 0);
			}
		}
		$this->db->select("distinct(emp.id_emp), emp.Nombre_fantasia_emp");
		$this->db->join("empresas emp","emp.id_emp=pro.id_man_emp", "inner");
		$this->db->order_by("emp.Nombre_fantasia_emp", "asc");
		$this->db->where("pro.Borrar", "0");
		$query=$this->db->get("proyectos pro");
		$rs=$query->result();
		if(is_array($rs) && sizeof($rs)>0){
			foreach($rs as $r){
				$arr[$r->id_emp]=$r->Nombre_fantasia_emp;
			}
		}
		return($arr);
	}

	function inversion_x_sector(){
		$this->db->where("pro.id_pais", $this->params->id_pais_chile);
		$this->db->where("pro.id_sector", $this->params->id_sectores["mineria"]);
		$this->db->select("sum(pro.Inversion_pro) inversion, sec.Nombre_sector");
		$this->db->join("proyectos_sector sec", "sec.id_sector=pro.id_sector", "inner");
		$query=$this->db->get("proyectos pro");
		$res=$query->first_row();
		$datos="";
		$sector="";
		$suma_inversion=0;
		$cont_vars=0;
		$valores=array();
		$descripcion="";
		if(is_object($res)){
			if($datos==""){
				$datos.="[{y:".round($res->inversion).", color:'".$this->params->colores[0]."'}";
				$sector.="['".$res->Nombre_sector."'";
			}else{
				$datos.=",{y:".round($res->inversion).", color:'".$this->params->colores[0]."'}";
				$sector.=",'".$res->Nombre_sector."'";
			}
			if(round($res->inversion)>0){
				$suma_inversion+=round($res->inversion);
				$valores[$this->params->id_sectores["mineria"]]=round($res->inversion);
				++$cont_vars;
			}
		}
		$this->db->where("pro.id_pais", $this->params->id_pais_chile);
		$this->db->where("pro.id_sector", $this->params->id_sectores["energia"]);
		$this->db->select("sum(pro.Inversion_pro) inversion, sec.Nombre_sector");
		$this->db->join("proyectos_sector sec", "sec.id_sector=pro.id_sector", "inner");
		$query=$this->db->get("proyectos pro");
		$res=$query->first_row();

		if(is_object($res)){
			if($datos==""){
				$datos.="[{y:".round($res->inversion).", color:'".$this->params->colores[1]."'}";
				$sector.="['".$res->Nombre_sector."'";
			}else{
				$datos.=",{y:".round($res->inversion).", color:'".$this->params->colores[1]."'}";
				$sector.=",'".$res->Nombre_sector."'";
			}
			if(round($res->inversion)>0){
				$suma_inversion+=round($res->inversion);
				$valores[$this->params->id_sectores["energia"]]=round($res->inversion);
				++$cont_vars;
			}
		}

		if($datos!="" && $sector!=""){
			$datos.="]";
			$sector.="]";
		}
		$porcent_min=round(($valores[$this->params->id_sectores["mineria"]]*100)/$suma_inversion);
		$porcent_nrg=round(($valores[$this->params->id_sectores["energia"]]*100)/$suma_inversion);
		$max_colors=sizeof($this->params->colores);
		if($porcent_min>$porcent_nrg){
			$diff=$porcent_min-$porcent_nrg;
			$mayor="Minería";
			$menor="Energía";
		}else{
			$diff=$porcent_nrg-$porcent_min;
			$mayor="Energía";
			$menor="Minería";
		}
		if($porcent_min==$porcent_nrg){
			$descripcion="De acuerdo a los datos del @portal, la inversión en Minería es igual a la inversión estimada en Energía a ".$this->params->mes[date("m")]." del ".date("Y").".";
		}else{

			$descripcion="De acuerdo a los datos del Portal Minero, la inversión en ".$mayor." es ".$diff."% superior a la inversión estimada en ".$menor." a ".$this->params->mes[date("m")]." del ".date("Y").".";
		}
		if($datos!="" && $sector!=""){
			$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->grafico_template_index["column"]);
			$template=str_replace("@descripcion", ((($descripcion))), $template);
			$template=str_replace("@data", ((($datos))), $template);
			$template=str_replace("@render", ((("grafico1"))), $template);
			$template=str_replace("@sector", ((($sector))), $template);
			$template=str_replace("@titulo_y", ((('Inversión (USD Millones)'))), $template);
			$template=str_replace("@_titulo", ((("Inversión en Chile de Minería y Energía"))), $template);

			$url_js=CONFLUENCE_FILES.$this->params->file_graficos_index[1];
			$url_js_http=base_url().$this->params->file_graficos_index[1];
			$js="var grafico1=\"".$template."\";";
			$a=file_put_contents($url_js, $js);
			if($a){
				@chmod($url_js, 0777);
				return($url_js);
			}else{
				return(false);
			}
		}
	}

	function proy_x_region(){
		$qr="select r.Nombre_region, r.nombre_corto, r.id_region, (select sum(pro1.Inversion_pro) from proyectos pro1 where pro1.id_sector=".$this->params->id_sectores["mineria"]." and pro1.id_region=r.id_region) inversion, (select count(*) from proyectos pro inner join proyectos_sector sec on sec.id_sector=pro.id_sector inner join u_region reg1 on pro.id_region=reg1.id_region where pro.id_sector=".$this->params->id_sectores["mineria"]." and pro.id_region=r.id_region) total from u_region r where r.id_pais=".$this->params->id_pais_chile." order by inversion desc limit 5";
		$query=$this->db->query($qr);
		$res=$query->result();
		$datos="";
		$sector="";
		$descripcion="";
		$suma=0;
		$regiones="";
		if(is_array($res)){
			$x=0;
			$y=0;
			foreach($res as $rs){
				if($y<3){
					$suma+=round($rs->inversion);
					$reg=explode(" - ", $rs->nombre_corto);
					if($regiones=="")
						$regiones.=$reg[0];
					else
						$regiones.=", ".$reg[0];
					++$y;
				}
				$var=explode(" - ", $rs->nombre_corto);

				$rs->nombre_corto=$var[0];
				if($x<=sizeof($this->params->colores)-1){
					$color=$this->params->colores[$x];
					++$x;
				}else{
					$x=0;
					$color=$this->params->colores[$x];
				}
				if($datos==""){
					$datos.="[{y:".round($rs->inversion).", color:'".$color."'}";
					//$datos.="[".round($rs->total);
					$sector.="['".$rs->nombre_corto."'";
				}else{
					$datos.=",{y:".round($rs->inversion).", color:'".$color."'}";
					//$datos.=",".round($rs->total);
					$sector.=",'".$rs->nombre_corto."'";
				}
			}
		}
		if($datos!="" && $sector!=""){
			$datos.="]";
			$sector.="]";
		}

		if($total_inv=$this->total_inversion_sector($this->params->id_sectores["energia"])){
			$std=$suma*100;
			$result=round($std)/$total_inv;
			$descripcion="Según la base de datos de Portal Minero, el ".round($result)."% del total de la inversión en Minería se concentra en las regiones ".$regiones.' respectivamente (US$ '.$suma." millones).";
		}

		if($datos!="" && $sector!=""){
			$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->grafico_template_index["bar"]);
			$template=str_replace("@descripcion", ((($descripcion))), $template);
			$template=str_replace("@data", ((($datos))), $template);
			$template=str_replace("@sector", ((($sector))), $template);
			$template=str_replace("@render", ((("grafico2"))), $template);
			$template=str_replace("@titulo_y", ((('Monto (USD Millones)'))), $template);
			$template=str_replace("@_titulo", ((("Concentración Geográfica Minería por Región"))), $template);

			$url_js=CONFLUENCE_FILES.$this->params->file_graficos_index[2];
			$url_js_http=base_url().$this->params->file_graficos_index[2];
			$js="var grafico2=\"".$template."\";";
			$a=file_put_contents($url_js, $js);
			if($a){
				@chmod($url_js, 0777);
				return($url_js);
			}else{
				return(false);
			}
		}
	}

	function grafico3(){
		$qr="select r.Nombre_region, r.nombre_corto, r.id_region, (select sum(pro1.Inversion_pro) from proyectos pro1 where pro1.id_sector=".$this->params->id_sectores["energia"]." and pro1.id_region=r.id_region) inversion, (select count(*) from proyectos pro inner join proyectos_sector sec on sec.id_sector=pro.id_sector inner join u_region reg1 on pro.id_region=reg1.id_region where pro.id_sector=".$this->params->id_sectores["energia"]." and pro.id_region=r.id_region) total from u_region r where r.id_pais=".$this->params->id_pais_chile." order by inversion desc limit 5";
		$query=$this->db->query($qr);
		$res=$query->result();
		$datos="";
		$sector="";
		$descripcion="";
		$suma=0;
		$regiones="";
		if(is_array($res)){
			$x=0;
			$y=0;
			foreach($res as $rs){
				if($y<3){
					$suma+=round($rs->inversion);
					$reg=explode(" - ", $rs->nombre_corto);
					if($regiones=="")
						$regiones.=$reg[0];
					else
						$regiones.=", ".$reg[0];
					++$y;
				}
				$var=explode(" - ", $rs->nombre_corto);
				$rs->nombre_corto=$var[0];
				if($x<=sizeof($this->params->colores)-1){
					$color=$this->params->colores[$x];
					++$x;
				}else{
					$x=0;
					$color=$this->params->colores[$x];
				}
				if($datos==""){
					$datos.="[{y:".round($rs->inversion).", color:'".$color."'}";
					//$datos.="[".round($rs->total);
					$sector.="['".$rs->nombre_corto."'";
				}else{
					$datos.=",{y:".round($rs->inversion).", color:'".$color."'}";
					//$datos.=",".round($rs->total);
					$sector.=",'".$rs->nombre_corto."'";
				}
			}
		}
		if($datos!="" && $sector!=""){
			$datos.="]";
			$sector.="]";
		}

		if($total_inv=$this->total_inversion_sector($this->params->id_sectores["energia"])){
			$std=$suma*100;
			$result=round($std)/$total_inv;
			$descripcion="Según la base de datos de Portal Minero, el ".round($result)."% del total de la inversión en Energía se concentra en las regiones ".$regiones.' respectivamente (US$ '.$suma." millones).";
		}

		if($datos!="" && $sector!=""){
			$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->grafico_template_index["bar"]);
			$template=str_replace("@descripcion", ((($descripcion))), $template);
			$template=str_replace("@data", ((($datos))), $template);
			$template=str_replace("@sector", ((($sector))), $template);
			$template=str_replace("@render", ((("grafico3"))), $template);
			$template=str_replace("@titulo_y", ((('Monto (USD Millones)'))), $template);
			$template=str_replace("@_titulo", ((("Concentración Geográfica Energía por Región"))), $template);

			$url_js=$_SERVER["DOCUMENT_ROOT"]."/".$this->params->dir_codeigniter.$this->params->file_graficos_index[3];
			$url_js_http=base_url().$this->params->file_graficos_index[3];
			$js="var grafico3=\"".$template."\";";
			$a=file_put_contents($url_js, $js);
			if($a){
				@chmod($url_js, 0777);
				return($url_js);
			}else{
				return(false);
			}
		}
	}

	function grafico4(){

		$query=$this->db->query("select ps.Nombre_sector nombre_corto, (select count(*) from proyectos pro where pro.id_sector=ps.id_sector and pro.Etapa_actual_pro=".$this->params->id_etapa_exploracion.") total from proyectos_sector ps where (ps.id_sector=".$this->params->id_sectores["mineria"]." or ps.id_sector=".$this->params->id_sectores["energia"].") order by total desc limit 5");
		$res=$query->result();
		$datos="";
		$sector="";
		$descripcion="";
		if(is_array($res)){
			$x=2;
			$suma=0;
			$valores=array();

			foreach($res as $rs){
				$var=explode(" - ", $rs->nombre_corto);
				$rs->nombre_corto=$var[0];
				if($x<=sizeof($this->params->colores)-1){
					$color=$this->params->colores[$x];
					++$x;
				}else{
					$x=0;
					$color=$this->params->colores[$x];
				}
				if($datos==""){
					$datos.="[{y:".round($rs->total).", color:'".$color."'}";
					//$datos.="[".round($rs->total);
					$sector.="['".$rs->nombre_corto."'";
				}else{

					$datos.=",{y:".round($rs->total).", color:'".$color."'}";
					//$datos.=",".round($rs->total);
					$sector.=",'".$rs->nombre_corto."'";
				}
				$sectores[]=$rs->nombre_corto;
				$valores[]=$rs->total;
				$suma+=$rs->total;
			}
		}

		if($datos!="" && $sector!=""){
			$datos.="]";
			$sector.="]";
		}

		if($datos!="" && $sector!=""){
			$template=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/".$this->params->grafico_template_index["column"]);
			$template=str_replace("@descripcion", ((("Según la base de Portal Minero, hay ".$suma." Proyectos en etapa de Exploración. De los cuales, ".$valores[0]." son de ".$sectores[0]." y ".$valores[1]." de ".$sectores[1]." a ".$this->params->mes[date("m")]." de ".date("Y")."."))), $template);
			$template=str_replace("@data", ((($datos))), $template);
			$template=str_replace("@sector", ((($sector))), $template);
			$template=str_replace("@render", ((("grafico4"))), $template);
			$template=str_replace("@titulo_y", ((('Total Proyectos'))), $template);
			$template=str_replace("@_titulo", ((("Cantidad de Proyectos en Fase Exploración"))), $template);

			$url_js=CONFLUENCE_FILES.$this->params->file_graficos_index[4];
			$url_js_http=base_url().$this->params->file_graficos_index[4];
			$js="var grafico4=\"".$template."\";";
			$a=file_put_contents($url_js, $js);
			if($a){
				@chmod($url_js, 0777);
				return($url_js);
			}else{
				return(false);
			}
		}
	}

	function norm_pro(){
		$query=$this->db->query("select * from proyectos where Nombre_pro like '%–%' or Telefono_contacto_pro like '%–%' or Empresa_contacto_pro like '%–%' or Nombre_generico_pro like '%–%' or Historial_pro like '%–%' or ultima_informacion_pro like '%–%' or Direccion_pro like '%–%'");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_pro=str_replace("–", "-", $r->Nombre_pro);
			$r->Empresa_contacto_pro=str_replace("–", "-", $r->Empresa_contacto_pro);
			$r->Telefono_contacto_pro=str_replace("–", "-", $r->Telefono_contacto_pro);
			$r->ultima_informacion_pro=str_replace("–", "-", $r->ultima_informacion_pro);
			$r->Historial_pro=str_replace("–", "-", $r->Historial_pro);
			$r->Direccion_pro=str_replace("–", "-", $r->Direccion_pro);
			$r->Nombre_generico_pro=str_replace("–", "-", $r->Nombre_generico_pro);
			$this->db->where("id_pro", $r->id_pro);
			$this->db->update("proyectos", array("Nombre_pro"=>$r->Nombre_pro, "Empresa_contacto_pro"=>$r->Empresa_contacto_pro, "Telefono_contacto_pro"=>$r->Telefono_contacto_pro, "ultima_informacion_pro"=>$r->ultima_informacion_pro, "Historial_pro"=>$r->Historial_pro, "Nombre_generico_pro"=>$r->Nombre_generico_pro, "Direccion_pro"=>$r->Direccion_pro));
		}

		$query=$this->db->query("select * from proyectos where Nombre_pro like '%”%' or Telefono_contacto_pro like '%”%' or Empresa_contacto_pro like '%”%' or Nombre_generico_pro like '%”%' or Historial_pro like '%”%' or ultima_informacion_pro like '%”%' or Direccion_pro like '%”%'");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_pro=str_replace("”", "-", $r->Nombre_pro);
			$r->Empresa_contacto_pro=str_replace("”", '"', $r->Empresa_contacto_pro);
			$r->Telefono_contacto_pro=str_replace("”", '"', $r->Telefono_contacto_pro);
			$r->ultima_informacion_pro=str_replace("”", '"', $r->ultima_informacion_pro);
			$r->Historial_pro=str_replace("”", '"', $r->Historial_pro);
			$r->Direccion_pro=str_replace("”", '"', $r->Direccion_pro);
			$r->Nombre_generico_pro=str_replace("”", '"', $r->Nombre_generico_pro);
			$this->db->where("id_pro", $r->id_pro);
			$this->db->update("proyectos", array("Nombre_pro"=>$r->Nombre_pro, "Empresa_contacto_pro"=>$r->Empresa_contacto_pro, "Telefono_contacto_pro"=>$r->Telefono_contacto_pro, "ultima_informacion_pro"=>$r->ultima_informacion_pro, "Historial_pro"=>$r->Historial_pro, "Nombre_generico_pro"=>$r->Nombre_generico_pro, "Direccion_pro"=>$r->Direccion_pro));
		}

		$query=$this->db->query("select * from proyectos where Nombre_pro like '%“%' or Telefono_contacto_pro like '%“%' or Empresa_contacto_pro like '%“%' or Nombre_generico_pro like '%“%' or Historial_pro like '%“%' or ultima_informacion_pro like '%“%' or Direccion_pro like '%“%'");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_pro=str_replace("“", '"', $r->Nombre_pro);
			$r->Empresa_contacto_pro=str_replace("“", '"', $r->Empresa_contacto_pro);
			$r->Telefono_contacto_pro=str_replace("“", '"', $r->Telefono_contacto_pro);
			$r->ultima_informacion_pro=str_replace("“", '"', $r->ultima_informacion_pro);
			$r->Historial_pro=str_replace("“", '"', $r->Historial_pro);
			$r->Direccion_pro=str_replace("“", '"', $r->Direccion_pro);
			$r->Nombre_generico_pro=str_replace("“", '"', $r->Nombre_generico_pro);
			$this->db->where("id_pro", $r->id_pro);
			$this->db->update("proyectos", array("Nombre_pro"=>$r->Nombre_pro, "Empresa_contacto_pro"=>$r->Empresa_contacto_pro, "Telefono_contacto_pro"=>$r->Telefono_contacto_pro, "ultima_informacion_pro"=>$r->ultima_informacion_pro, "Historial_pro"=>$r->Historial_pro, "Nombre_generico_pro"=>$r->Nombre_generico_pro, "Direccion_pro"=>$r->Direccion_pro));
		}
	}

	function norm_lici(){
		$query=$this->db->query("select * from licitaciones where Nombre_lici like '%–%' or Empresa_contacto_lici like '%–%' or Telefono_contacto_lici like '%–%' or Desc_lici like '%–%'");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_lici=str_replace("–", "-", $r->Nombre_lici);
			$r->Empresa_contacto_lici=str_replace("–", "-", $r->Empresa_contacto_lici);
			$r->Telefono_contacto_lici=str_replace("–", "-", $r->Telefono_contacto_lici);
			$r->Desc_lici=str_replace("–", "-", $r->Desc_lici);
			$this->db->where("id_lici", $r->id_lici);
			$this->db->update("licitaciones", array("Nombre_lici"=>$r->Nombre_lici, "Empresa_contacto_lici"=>$r->Empresa_contacto_lici, "Telefono_contacto_lici"=>$r->Telefono_contacto_lici, "Desc_lici"=>$r->Desc_lici));
		}

		$query=$this->db->query("select * from licitaciones where Nombre_lici like '%”%' or Empresa_contacto_lici like '%”%' or Telefono_contacto_lici like '%”%' or Desc_lici like '%”%'");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_lici=str_replace("”", '"', $r->Nombre_lici);
			$r->Empresa_contacto_lici=str_replace("”", '"', $r->Empresa_contacto_lici);
			$r->Telefono_contacto_lici=str_replace("”", '"', $r->Telefono_contacto_lici);
			$r->Desc_lici=str_replace("”", '"', $r->Desc_lici);
			$this->db->where("id_lici", $r->id_lici);
			$this->db->update("licitaciones", array("Nombre_lici"=>$r->Nombre_lici, "Empresa_contacto_lici"=>$r->Empresa_contacto_lici, "Telefono_contacto_lici"=>$r->Telefono_contacto_lici, "Desc_lici"=>$r->Desc_lici));
		}

		$query=$this->db->query("select * from licitaciones where Nombre_lici like '%“%' or Empresa_contacto_lici like '%“%' or Telefono_contacto_lici like '%“%' or Desc_lici like '%“%'");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_lici=str_replace("“", '"', $r->Nombre_lici);
			$r->Empresa_contacto_lici=str_replace("“", '"', $r->Empresa_contacto_lici);
			$r->Telefono_contacto_lici=str_replace("“", '"', $r->Telefono_contacto_lici);
			$r->Desc_lici=str_replace("“", '"', $r->Desc_lici);
			$this->db->where("id_lici", $r->id_lici);
			$this->db->update("licitaciones", array("Nombre_lici"=>$r->Nombre_lici, "Empresa_contacto_lici"=>$r->Empresa_contacto_lici, "Telefono_contacto_lici"=>$r->Telefono_contacto_lici, "Desc_lici"=>$r->Desc_lici));
		}
	}

	function norm_emp(){
		$query=$this->db->query("SELECT *
FROM  `empresas`
WHERE (
(  `id_emp`
 ) LIKE  '%–%'
OR (  `Razon_social_emp`
 ) LIKE  '%–%'
OR (  `Nombre_fantasia_emp`
 ) LIKE  '%–%'
OR (  `Rut_emp`
 ) LIKE  '%–%'
OR (  `Direccion_emp`
 ) LIKE  '%–%'
OR (  `Email_emp`
 ) LIKE  '%–%'
OR (  `id_pais`
 ) LIKE  '%–%'
OR (  `id_region`
 ) LIKE  '%–%'
OR (  `id_comuna`
 ) LIKE  '%–%'
OR (  `Telefono_emp`
 ) LIKE  '%–%'
)");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_fantasia_emp=str_replace("–", "-", $r->Nombre_fantasia_emp);
			$r->Razon_social_emp=str_replace("–", "-", $r->Razon_social_emp);
			$r->Direccion_emp=str_replace("–", "-", $r->Direccion_emp);
			$r->Rut_emp=str_replace("–", "-", $r->Rut_emp);
			$r->Telefono_emp=str_replace("–", "-", $r->Telefono_emp);
			$this->db->where("id_emp", $r->id_emp);
			$this->db->update("empresas", array("Nombre_fantasia_emp"=>$r->Nombre_fantasia_emp, "Razon_social_emp"=>$r->Razon_social_emp, "Direccion_emp"=>$r->Direccion_emp, "Rut_emp"=>$r->Rut_emp, "Telefono_emp"=>$r->Telefono_emp));
		}

		$query=$this->db->query("SELECT *
FROM  `empresas`
WHERE (
(  `id_emp`
 ) LIKE  '%”%'
OR (  `Razon_social_emp`
 ) LIKE  '%”%'
OR (  `Nombre_fantasia_emp`
 ) LIKE  '%”%'
OR (  `Rut_emp`
 ) LIKE  '%”%'
OR (  `Direccion_emp`
 ) LIKE  '%”%'
OR (  `Email_emp`
 ) LIKE  '%”%'
OR (  `id_pais`
 ) LIKE  '%”%'
OR (  `id_region`
 ) LIKE  '%”%'
OR (  `id_comuna`
 ) LIKE  '%”%'
OR (  `Telefono_emp`
 ) LIKE  '%”%'
)");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_fantasia_emp=str_replace("”", '"', $r->Nombre_fantasia_emp);
			$r->Razon_social_emp=str_replace("”", '"', $r->Razon_social_emp);
			$r->Direccion_emp=str_replace("”", '"', $r->Direccion_emp);
			$r->Rut_emp=str_replace("”", '"', $r->Rut_emp);
			$r->Telefono_emp=str_replace("”", '"', $r->Telefono_emp);
			$this->db->where("id_emp", $r->id_emp);
			$this->db->update("empresas", array("Nombre_fantasia_emp"=>$r->Nombre_fantasia_emp, "Razon_social_emp"=>$r->Razon_social_emp, "Direccion_emp"=>$r->Direccion_emp, "Rut_emp"=>$r->Rut_emp, "Telefono_emp"=>$r->Telefono_emp));
		}

		$query=$this->db->query("SELECT *
FROM  `empresas`
WHERE (
(  `id_emp`
 ) LIKE  '%“%'
OR (  `Razon_social_emp`
 ) LIKE  '%“%'
OR (  `Nombre_fantasia_emp`
 ) LIKE  '%“%'
OR (  `Rut_emp`
 ) LIKE  '%“%'
OR (  `Direccion_emp`
 ) LIKE  '%“%'
OR (  `Email_emp`
 ) LIKE  '%“%'
OR (  `id_pais`
 ) LIKE  '%“%'
OR (  `id_region`
 ) LIKE  '%“%'
OR (  `id_comuna`
 ) LIKE  '%“%'
OR (  `Telefono_emp`
 ) LIKE  '%“%'
)");
		$rs=$query->result();
		foreach($rs as $r){
			$r->Nombre_fantasia_emp=str_replace("“", '"', $r->Nombre_fantasia_emp);
			$r->Razon_social_emp=str_replace("“", '"', $r->Razon_social_emp);
			$r->Direccion_emp=str_replace("“", '"', $r->Direccion_emp);
			$r->Rut_emp=str_replace("“", "-", $r->Rut_emp);
			$r->Telefono_emp=str_replace("“", '"', $r->Telefono_emp);
			$this->db->where("id_emp", $r->id_emp);
			$this->db->update("empresas", array("Nombre_fantasia_emp"=>$r->Nombre_fantasia_emp, "Razon_social_emp"=>$r->Razon_social_emp, "Direccion_emp"=>$r->Direccion_emp, "Rut_emp"=>$r->Rut_emp, "Telefono_emp"=>$r->Telefono_emp));
		}
	}

	function total_inversion_sector($id_sector){
		if($id_sector!=""){
			$this->db->select("sum(pro.Inversion_pro) total");
			$this->db->where("pro.id_sector", $id_sector);
			$query=$this->db->get("proyectos pro");
			$rs=$query->first_row();
			if(is_object($rs)){
				return($rs->total);
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}

	function contar_proy_etapa_act($id_etapa){
		if($id_etapa!="" && $id_etapa!=NULL){
			$this->db->select("count(*) as total");
			$this->db->where("Borrar",0);
			$this->db->where("Etapa_actual_pro",$id_etapa);
			$this->db->where("id_pais",$this->params->id_pais_chile);
			$this->db->where("id_sector",$this->params->id_sectores["mineria"]);
			$query=$this->db->get("proyectos");
			$rs=$query->first_row();
			if(is_object($rs))
				$result[0]=$rs->total;
			else
				$result[0]=0;
			$this->db->select("count(*) as total");
			$this->db->where("Etapa_actual_pro",$id_etapa);
			$this->db->where("id_pais",$this->params->id_pais_chile);
			$this->db->where("id_sector",$this->params->id_sectores["energia"]);
			$query=$this->db->get("proyectos");
			$rs=$query->first_row();
			if(is_object($rs))
				$result[1]=$rs->total;
			else
				$result[1]=0;

			return($result);
		}else{
			return(false);
		}
	}

	function usuarios(){
		$this->db->where('socio.Estado_socio','A');
		$this->db->where('user_socio.email_socio !=','');
		$this->db->where('user_socio.email_socio !=','NULL');

		$this->db->join('socio','socio.id_socio=user_socio.id_socio');
		$where=$this->db->get("user_socio");
		$result=$where->result();
		return $result;
	}

	function listar_proy(){
		$this->db->order_by('id_pro','asc');
		$where=$this->db->get("proyectos");
		$result=$where->result();
		return $result;
	}

	function cambiar_url($id,$url_nueva){
		$datos['url_confluence_pro']=$url_nueva;
		$this->db->where("id_pro",$id);
		$this->db->update('proyectos', $datos);
		return true;
	}

	function sector_socio($id_socio,$id_sector){
		$this->db->where("id_socio",$id_socio);
		$where=$this->db->get("socio_x_sector");
		$result=$where->result();
		foreach($result as $socio){
			if($socio->id_sector==$id_sector){
				return true;
			}
			else{
				return false;
			}
		}
	}

	function mostrar_proyectos_excel($sector=0,$operacion=0){
		if($sector!=0)
			$query = $this->db->where('proyectos.id_sector',$sector);

		$this->db->where('Borrar',0);

		if($operacion==0)
			$this->db->where('Estado_pro !=','O');

	    $this->db->order_by('id_pro','asc');
		//$this->db->where('pc.Principal_contact','1');
	    $this->db->join("proyectos_medicion_produccion pmp", "pmp.id_med = proyectos.Medicion_produccion_pro","left");
	    $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector");
	   	$this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais");
		$this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
		$this->db->join("empresas", "empresas.id_emp = proyectos.id_man_emp", 'left');
	    $this->db->join("proyectos_etapas", "proyectos_etapas.id_etapa = proyectos.Etapa_actual_pro","left");
		$this->db->join("proyectos_x_etapas", "proyectos_x_etapas.id_pro = proyectos.id_pro and proyectos_x_etapas.id_etapa = proyectos.Etapa_actual_pro", "left");
		$this->db->join("tipos_contratos", "tipos_contratos.id_tipo_contrato = proyectos_x_etapas.id_tipo_contrato", "left");
		//$this->db->join("proyectos_contactos pc","pc.id_pro = proyectos.id_pro","left");
		$this->db->join("empresas emp_respons", "emp_respons.id_emp = proyectos_x_etapas.id_emp", "left");
		$this->db->select("Produccion_pro,Estado_pro,Sigla_med,proyectos.id_pro, Nombre_etapa, Nombre_pro, Nombre_generico_pro, Nombre_region,
									Nombre_pais, Inversion_pro, empresas.Razon_social_emp, Fecha_actualizacion_pro,
									Nombre_sector, ultima_informacion_pro, url_confluence_pro,
									Nombre_tipo_contrato, emp_respons.Razon_social_emp as resp_etapa_act,(SELECT group_concat(pt.Nombre_tipo separator '/') as tipo FROM proyectos_x_tipo pxt LEFT JOIN proyectos_tipo pt ON pt.id_tipo=pxt.id_tipo WHERE pxt.id_pro=proyectos.id_pro) as Tipos,(SELECT group_concat(em.Razon_social_emp separator '/') as tipo FROM proyectos_x_empresas pxe LEFT JOIN empresas em ON em.id_emp=pxe.id_emp WHERE pxe.id_pro=proyectos.id_pro) as Propietarios");
		$query = $this->db->get("proyectos");
		$result = $query->result();

		return $result;

   }

   function notificar_sector($id_user,$id_sector){
	   $this->db->where('id_user_socio',$id_user);
	   $query=$this->db->get('user_socio_sector');
	   $existe=$query->num_rows();
	   if($existe>0){
		   $this->db->where('id_user_socio',$id_user);
		   $this->db->where('id_sector',$id_sector);
		   $query2=$this->db->get('user_socio_sector');
		   $cant=$query2->num_rows();
	   }
	   else{
		   $cant=1;
	   }
		return $cant;
   }
   function buscar_permiso_membresia($membresia,$id_sector,$id_socio){
	   $this->db->where("mem.Nombre_mem",$membresia);
	   $this->db->join('membresias mem',"mem.id_mem=mxs.id_mem",'left');
	   $query=$this->db->get('membresia_x_sectores mxs');
	   $existe=$query->num_rows();
	   if($existe>0){
	   		$this->db->where("mem.Nombre_mem",$membresia);
	   		$this->db->where("mxs.id_sector",$id_sector);
			$this->db->join('membresias mem',"mem.id_mem=mxs.id_mem",'left');
			$query2=$this->db->get('membresia_x_sectores mxs');
			$cant=$query2->num_rows();
			if($cant>0)
				$aceptar=true;
			else
				$aceptar=false;
	   }
	   else{
		    $this->db->where("sxs.id_socio",$id_socio);
	   		$this->db->where("sxs.id_sector",$id_sector);
			$query2=$this->db->get('socio_x_sector sxs');
			$cant=$query2->num_rows();
			if($cant>0)
				$aceptar=true;
			else
				$aceptar=false;
	   }

	   return $aceptar;
   }
   function mostrar_oportunidades(){
   	//$this->db->join("","","left");
	$this->db->limit(50);
	$this->db->order_by('id_oport','desc');
	$query=$this->db->get('oportunidadesNegocios');
	$result=$query->result();
	return $result;
   }
   function buscar_oport($id){
     $this->db->where('id_oport',$id);
	 $query=$this->db->get('oportunidadesNegocios');
	$result = $query->first_row();
	 return $result;
   }
   function actualizar_oport($datos,$id){
   	$this->db->where('id_oport',$id);
	$this->db->update('oportunidadesNegocios',$datos);
   }
   function lista_proyectos_noti($id_pro=0){
	    $pro='';
		if($id_pro!=0)
		$pro='AND `id_pro` = '.$id_pro;

		$query = $this->db->query('SELECT * FROM (`proyectos` pro) LEFT JOIN `m_user` ON `m_user`.`id_user` = `pro`.`id_usuario_modifica` JOIN `proyectos_sector` ON `proyectos_sector`.`id_sector` = `pro`.`id_sector` WHERE `Borrar` = 0 AND `Etapa_actual_pro` != 8 AND ((Estado_pro="A") OR (Estado_pro="P")) '.$pro.' ORDER BY Fecha_actualizacion_pro asc');
		$result = $query->result();
		return $result;
	}
   function validar_fecha_actualizacion($fecha_actualizacion){
	   //$fecha= explode("-",$fecha_actualizacion);
	   //$fecha_actualizacion = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
	   $timestamp_fecha_actualizacion=strtotime($fecha_actualizacion);
	   $timestamp_fecha_actual=strtotime(date("Y-m-d"));
	   $diferencia=$timestamp_fecha_actual - $timestamp_fecha_actualizacion;
	   $timestamp_fecha_limite=80*86400;//80 dias después de última actualización

	   //echo 'time actual: '.$diferencia.' time limite: '.$timestamp_fecha_limite."<br />";
	   if($diferencia>=$timestamp_fecha_limite){
	  // echo $diferencia.'<br />';
		   return true;
	   }
	   /*$socios_restan_caducar= date("d-m-Y",$timestamp_fecha_actual - (80*86400));
	   echo $socios_restan_caducar;
		//$this->db->where('fecha_caduca >=',$actual);
		$this->db->where('Fecha_actualizacion_pro <=',$socios_restan_caducar);
		//$this->db->where('id_vendedora',25);
	   //$query = $this->db->join("m_user", "m_user.id_user = socio.id_vendedora", 'left');
	   //query = $this->db->join("user_socio", "user_socio.id_user_socio = socio.id_contacto_admin_socio", 'inner');
		$query=$this->db->get("proyectos");
		$result = $query->result();*/
		return false;
	}
	function validar_notificacion_actualizacion($id_pro){
	   $actual=date('Y-m-d H:m:s');
	   $timestamp_fecha_actual=strtotime($actual);
	   $tiempo_volver_notificar= date("Y-m-d H:m:s",$timestamp_fecha_actual - (80*86400));
		$this->db->where('id_pro',$id_pro);
		$this->db->where('Fecha_not >=',$tiempo_volver_notificar);
		$query=$this->db->get("proyectos_notificacion_actualizacion");
		$cant = $query;
		return $cant;
	}

	function guardar_notificacion_actualizacion($datos,$id_pro){
		$this->db->where('id_pro',$id_pro);
		$query=$this->db->get('proyectos_notificacion_actualizacion');
		if($query->num_rows()==0){
			$datos['id_pro']=$id_pro;
			$this->db->insert('proyectos_notificacion_actualizacion', $datos);
		}
		else{
			$this->db->where('id_pro',$id_pro);
			$this->db->update('proyectos_notificacion_actualizacion',$datos);
		}
	}
	function envio_soap_confluence_pro($id_proyecto,$subirIntra=0,$subirPM=0,$califica='no',$minor=0){
		$notif_correo=$minor;
		$result['msj']='OK';
		//$ressoap = $this->soap->storePageProy($id_proyecto, $minor, 1);
		if(($subirIntra==1)){
		    /*
			$ressoap = $this->soap->storePageProy_intranet($id_proyecto,$califica);
			if(!$ressoap){
				$result['msj']='ERROR_INTRANET';
				return($result);
			}
			$result['ver_intra']=$ressoap;
			
			*/
		} 

		$result['ver_intra']=1;
		
		if($subirPM==1){
			if($minor==0)$minor=true;
			else $minor=false;

			if(!$this->soap->storePageProy($id_proyecto, $minor, 1)){
				$result['msj']='ERROR_CONFLUENCE';
				return($result);
			}

/* comentado epf domingo
			$ejemplos=$this->params->ejemplos_pro;
			if(in_array($id_proyecto,$ejemplos)){
				if(!$this->soap->storePageProyExample($id_proyecto)){
					$result['msj']='ERROR_EJEMPLO_CONFLUENCE';
					return($result);
				}

			}*/

		}

		return($result);
	}

	
	
	function enviar_correo_proyecto_old_11_06_2019($id){
	  /*  error_reporting(E_ALL);
	    ini_set('display_errors', '1');
	    $this->load->library('My_PHPMailer');
	    $mail = new PHPMailer();
	    $mail->ClearAddresses();
	    $mail->MsgHTML("contenido prueba");
	    $mail->AddAddress("epinto@portalminero.com", "epinto@portalminero.com");
	    $mail->SetFrom('proyectos@portalminero.com', 'Proyectos Portal Minero');  //Quien envía el correo
	    $mail->Subject    = 'Proyecto Ingresado - Sector ';  //Asunto del mensaje
	    $mail->CharSet="utf-8";
	    $mail->Send();
	    echo "hola";
	    exit;*/
	    
	    $proyecto=$this->proyecto->editar_proyecto($id);
	    
	    $usuarios=$this->proyecto->usuarios();
	    $correo='';
	    
	    $this->load->library('My_PHPMailer');
	    
	    $mail = new PHPMailer();
	    $mail->SetFrom('proyectos@portalminero.com', 'Proyectos Portal Minero');  //Quien envía el correo
	    $mail->Subject    = 'Proyecto Ingresado - Sector '.$proyecto->Nombre_sector;  //Asunto del mensaje
	    $mail->CharSet="utf-8";
	    
	    
	    foreach($usuarios as $user){
	        $enviar=false;
	        if($user->email_socio!=NULL){
	            $membresia=$user->tipo_socio;
	            
	            if($this->proyecto->buscar_permiso_membresia($membresia,$proyecto->id_sector,$user->id_socio)){
	                $result=$this->proyecto->notificar_sector($user->id_user_socio,$proyecto->id_sector);
	                if($result>0)
	                    $enviar=true;
	            }
	            
	            if($enviar){
	                $datos=array();
	                $contenido='';
	                $datos['tipo_envio']='Proyecto de Sector '.$proyecto->Nombre_sector;
	                $datos['url_titulo']=$proyecto->id_pagina_pro;
	                $datos['titulo']=htmlentities(utf8_decode($proyecto->Nombre_pro));
	                $datos['url_confluence']=URL_PUBLICA_CONFLUENCE;
	                $datos['url_sector']='/display/'.$this->params->spaces_proy[$proyecto->id_sector];
	                $datos['imagen_envio']=URL_PUBLICA_CONFLUENCE.'/sitio_portal/images/portal-06.png';
	                $datos['url_add_rubro']=URL_PUBLICA_CONFLUENCE.'/pages/viewpage.action?pageId=29786340';
	                $datos['nombre_user']=htmlentities(utf8_decode($user->nombre_completo_socio));
	                
	                $contenido =$this->load->view('proyectos/formato_correo_proyecto', $datos, true);
	                
	                $mail->ClearAddresses();
	                $mail->MsgHTML($contenido);
	                $mail->AddAddress($user->email_socio, $datos['nombre_user']);
	                
	                if($mail->Send()){
	                    /*$directorio=BASE_DIRECTORIO."/correo/proyectos/";
	                     if (!file_exists($directorio)) {
	                     $directorio = mkdir($directorio,0777);
	                     }
	                     if (!file_exists($directorio.$id)) {
	                     $directorio2 = mkdir($directorio.$id,0777);
	                     }
	                     $nuevoarchivo = fopen($directorio.$id."/".$user->email_socio.".html", "w+") or die("Problemas en la creacion");
	                     fwrite($nuevoarchivo,$contenido);
	                     fclose($nuevoarchivo); */
	                }
	                /*else{
	                 $directorio=BASE_DIRECTORIO."/correo/error/";
	                 if (!file_exists($directorio)) {
	                 $directorio1 = mkdir($directorio,0777);
	                 }
	                 $directorio=BASE_DIRECTORIO."/correo/error/proyectos/";
	                 if (!file_exists($directorio)) {
	                 $directorio2 = mkdir($directorio,0777);
	                 }
	                 $directorio=BASE_DIRECTORIO."/correo/error/proyectos/".$id;
	                 if (!file_exists($directorio)) {
	                 $directorio3 = mkdir($directorio,0777);
	                 }
	                 $nuevoarchivo = fopen($directorio."/".$user->email_socio.".html", "w+") or die("Problemas en la creacion");
	                 fwrite($nuevoarchivo,$contenido);
	                 fclose($nuevoarchivo);
	                 }*/
	                
	            }
	        }
	    }
	}

	
	function enviar_correo_nuevo_server($id){	 
	
	
	    set_time_limit(300);
	    $proyecto=$this->proyecto->editar_proyecto($id);
		$usuarios=$this->proyecto->usuarios();
          $cuenta=0;
		
		 
		  foreach($usuarios as $user){	
			 $enviar=false;
			 // $enviar=true; //OJO: comentar cuando pase a productivo
			  if($user->email_socio!=NULL){
				  $membresia=$user->tipo_socio;
				  
				  if($this->proyecto->buscar_permiso_membresia($membresia,$proyecto->id_sector,$user->id_socio)){
					  $result=$this->proyecto->notificar_sector($user->id_user_socio,$proyecto->id_sector);
					  if($result>0)
						  $enviar=true;
				  }
			
				  if($enviar){
 					  $datos=array();
					  $contenido='';
					  $datos['tipo_envio']='Proyecto de Sector '.$proyecto->Nombre_sector;
					  $datos['url_titulo']=$proyecto->id_pagina_pro;
		 			  $datos['titulo']=htmlentities(utf8_decode($proyecto->Nombre_pro));
					  $datos['url_confluence']=URL_PUBLICA_CONFLUENCE;
					  $datos['url_sector']='/display/'.$this->params->spaces_proy[$proyecto->id_sector];
					  $datos['imagen_envio']=URL_PUBLICA_CONFLUENCE.'/sitio_portal/images/portal-06.png';
					  $datos['url_add_rubro']=URL_PUBLICA_CONFLUENCE.'/pages/viewpage.action?pageId=29786340';
					  $datos['nombre_user']=htmlentities(utf8_decode($user->nombre_completo_socio));
					  
					  $contenido =$this->load->view('proyectos/formato_correo_proyecto', $datos, true);
					 
					 
					 
					 $this->enviar_correo_mail($proyecto->Nombre_sector,$user->email_socio,$datos['nombre_user'],$contenido);
					 $cuenta=$cuenta+1;
					  
				  }
			  }
		  } 
	
	}
	
	
	
	function enviar_correo_mail($Nombre_sector,$email_socio,$nombre_user,$contenido){	 
	
	
	  
	    $this->load->library('email');
		$config['protocol'] = 'smtp';
		$config["smtp_host"] = 'smtp.mandrillapp.com';
		$config["smtp_user"] = 'Portal Minero';
		$config["smtp_pass"] = 'LzPt3PoN1LlJSoW6g0W8MA';   
		$config["smtp_port"] = '587';
		$config['charset'] = 'utf-8';
		$config['wordwrap'] = TRUE;
		$config['validate'] = true;
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from('proyectos@portalminero.com', 'Proyectos Portal Minero');
		$this->email->subject('Proyecto Ingresado - Sector '.$Nombre_sector);  
    	$this->email->message($contenido);
	    $this->email->to($email_socio, $nombre_user);
		// $this->email->to('epinto@portalminero.com', 'William');
		
		$this->email->send();

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function enviar_correo_proyecto($id){	  
		$proyecto=$this->proyecto->editar_proyecto($id);
		
		$usuarios=$this->proyecto->usuarios();

		$correo='';
		
		$this->load->library('email');
		//Indicamos el protocolo a utilizar
		$config['protocol'] = 'smtp';
			
		//El servidor de correo que utilizaremos
		$config["smtp_host"] = 'smtp.mandrillapp.com';

		//Nuestro usuario
		$config["smtp_user"] = 'Portal Minero';

		//Nuestra contraseña
		$config["smtp_pass"] = 'LzPt3PoN1LlJSoW6g0W8MA';   

		//El puerto que utilizará el servidor smtp
		$config["smtp_port"] = '587';

		//El juego de caracteres a utilizar
		$config['charset'] = 'utf-8';

		//Permitimos que se puedan cortar palabras
		$config['wordwrap'] = TRUE;

		//El email debe ser valido 
		$config['validate'] = true;

		//formato del email
		$config['mailtype'] = 'html';

		//Establecemos esta configuración
		$this->email->initialize($config);

		//Ponemos la dirección de correo que enviará el email y un nombre
		$this->email->from('proyectos@portalminero.com', 'Proyectos Portal Minero');
		$this->email->subject('Proyecto Ingresado - Sector '.$proyecto->Nombre_sector);  //Asunto del mensaje

			/*
		* Ponemos el o los destinatarios para los que va el email
		* en este caso al ser un formulario de contacto te lo enviarás a ti
		* mismo
		*/
		//$this->email->to('william@estudiosuma.cl', 'William');
		
	//Definimos el asunto del mensaje
		$this->email->subject("Asunto del mensaje");
		
	//Definimos el mensaje a enviar
 		 $this->email->message("Mensaje del correo para hacer la prueba");
		
/*		//Enviamos el email y si se produce bien o mal que avise con un echo
		if($this->email->send()){
			echo "Email enviado correctamente desde enviar_correo_proyecto_suma_new</br>";
		}else{
			echo "No se a enviado el email </br>";
		}
	
	echo $this->email->print_debugger();
	exit;
	die();  */
		  
		  
		  foreach($usuarios as $user){	
			  //$enviar=false;
			  $enviar=true; //OJO: comentar cuando pase a productivo
			  if($user->email_socio!=NULL){
				  $membresia=$user->tipo_socio;
				  
				  if($this->proyecto->buscar_permiso_membresia($membresia,$proyecto->id_sector,$user->id_socio)){
					  $result=$this->proyecto->notificar_sector($user->id_user_socio,$proyecto->id_sector);
					  if($result>0)
						  $enviar=true;
				  }
			
				  if($enviar){
 					  $datos=array();
					  $contenido='';
					  $datos['tipo_envio']='Proyecto de Sector '.$proyecto->Nombre_sector;
					  $datos['url_titulo']=$proyecto->id_pagina_pro;
					  $datos['titulo']=htmlentities(utf8_decode($proyecto->Nombre_pro));
					  $datos['url_confluence']=URL_PUBLICA_CONFLUENCE;
					  $datos['url_sector']='/display/'.$this->params->spaces_proy[$proyecto->id_sector];
					  $datos['imagen_envio']=URL_PUBLICA_CONFLUENCE.'/sitio_portal/images/portal-06.png';
					  $datos['url_add_rubro']=URL_PUBLICA_CONFLUENCE.'/pages/viewpage.action?pageId=29786340';
					  $datos['nombre_user']=htmlentities(utf8_decode($user->nombre_completo_socio));
					  
					  $contenido =$this->load->view('proyectos/formato_correo_proyecto', $datos, true);
					  
					  //echo "voy a convertit a html";
					  $this->email->message($contenido);
					  $this->email->to($user->email_socio, $datos['nombre_user']);
					  //$this->email->to('william@estudiosuma.cl', 'William');
					  
					  $this->email->send();
 /* 						if($this->email->send()){
							echo "Email enviado correctamente";
						}else{
							echo "No se a enviado el email";
						}

						echo $this->email->print_debugger();
						exit;
						die(); */
					   
					  
					 // if($mail->Send()){
						  /*$directorio=BASE_DIRECTORIO."/correo/proyectos/";
						   if (!file_exists($directorio)) {
						   $directorio = mkdir($directorio,0777);
						   }
						   if (!file_exists($directorio.$id)) {
						   $directorio2 = mkdir($directorio.$id,0777);
						   }
						   $nuevoarchivo = fopen($directorio.$id."/".$user->email_socio.".html", "w+") or die("Problemas en la creacion");
						   fwrite($nuevoarchivo,$contenido);
						   fclose($nuevoarchivo); */
					  //}
					  /*else{
					   $directorio=BASE_DIRECTORIO."/correo/error/";
					   if (!file_exists($directorio)) {
					   $directorio1 = mkdir($directorio,0777);
					   }
					   $directorio=BASE_DIRECTORIO."/correo/error/proyectos/";
					   if (!file_exists($directorio)) {
					   $directorio2 = mkdir($directorio,0777);
					   }
					   $directorio=BASE_DIRECTORIO."/correo/error/proyectos/".$id;
					   if (!file_exists($directorio)) {
					   $directorio3 = mkdir($directorio,0777);
					   }
					   $nuevoarchivo = fopen($directorio."/".$user->email_socio.".html", "w+") or die("Problemas en la creacion");
					   fwrite($nuevoarchivo,$contenido);
					   fclose($nuevoarchivo);
					   }*/
					  
				  }
			  }
		  } //end del foreach
	
	  }

	function enviar_correo_proyecto_SUMA(){
		
		$correo='';
		
		//$this->load->library('My_PHPMailer');
		//Cargamos la librería email
		$this->load->library('email');


		//Indicamos el protocolo a utilizar
		$config['protocol'] = 'smtp';
			
		//El servidor de correo que utilizaremos
		$config["smtp_host"] = 'smtp.mandrillapp.com';
		
		//Nuestro usuario
		$config["smtp_user"] = 'Portal Minero';
		
		//Nuestra contraseña
		$config["smtp_pass"] = 'LzPt3PoN1LlJSoW6g0W8MA';   
		
		//El puerto que utilizará el servidor smtp
		$config["smtp_port"] = '587';
		
		//El juego de caracteres a utilizar
		$config['charset'] = 'utf-8';

		//Permitimos que se puedan cortar palabras
		$config['wordwrap'] = TRUE;
		
		//El email debe ser valido 
		$config['validate'] = true;
		
		
	//Establecemos esta configuración
		$this->email->initialize($config);

	//Ponemos la dirección de correo que enviará el email y un nombre
		$this->email->from('proyectos@portalminero.com', 'Proyectos Portal Minero');
		
	/*
		* Ponemos el o los destinatarios para los que va el email
		* en este caso al ser un formulario de contacto te lo enviarás a ti
		* mismo
		*/
		$this->email->to('william@estudiosuma.cl', 'William');
		
	//Definimos el asunto del mensaje
		$this->email->subject("Asunto del mensaje");
		
	//Definimos el mensaje a enviar
		 $this->email->message("Mensaje del correo para hacer la prueba");
		
		//Enviamos el email y si se produce bien o mal que avise con un echo
		if($this->email->send()){
			echo "Email enviado correctamente </br>";
		}else{
			echo "No se a enviado el email </br>";
		}
	
	echo $this->email->print_debugger();
	     
/* 
		$mail->SetFrom('proyectos@portalminero.com', 'Proyectos Portal Minero');  //Quien envía el correo
		$mail->Subject    = 'correo Prueba ';  //Asunto del mensaje
		$mail->CharSet="utf-8";
		
		$mail->ClearAddresses();
		$mail->MsgHTML("contenido");
		$mail->AddAddress("william@estudiosuma.cl", "prueba asunto"); 

		if(!$mail->Send())
		{
		   echo "error al enviar el mensaje";
		   exit;
		}
		
		echo "mensaje fue enviado con exito";*/

	}
  
	function enviar_correo_proyecto_CRISTO($id){
		$proyecto=$this->proyecto->editar_proyecto($id);

			$usuarios=$this->proyecto->usuarios();
			$correo='';

			$remitente = array('proyectos@portalminero.com', 'Proyectos Portal Minero');  //Quien envía el correo
			$asunto= 'Proyecto Ingresado - Sector '.$proyecto->Nombre_sector;  //Asunto del mensaje

			foreach($usuarios as $user){
				$enviar=false;
				if($user->email_socio!=NULL){
					$membresia=$user->tipo_socio;

					if($this->proyecto->buscar_permiso_membresia($membresia,$proyecto->id_sector,$user->id_socio)){
						$result=$this->proyecto->notificar_sector($user->id_user_socio,$proyecto->id_sector);
						if($result>0)
							$enviar=true;
					}

					if($enviar){
							$datos=array();
							$contenido='';
							$datos['tipo_envio']='Proyecto de Sector '.$proyecto->Nombre_sector;
							$datos['url_titulo']=$proyecto->id_pagina_pro;
							$datos['titulo']=$proyecto->Nombre_pro;
							$datos['url_confluence']=URL_PUBLICA_CONFLUENCE;
                            $datos['url_sector']='/display/'.$this->params->spaces_proy[$proyecto->id_sector];
							$datos['imagen_envio']=URL_PUBLICA_CONFLUENCE.'/sitio_portal/images/portal-06.png';
							$datos['url_add_rubro']=URL_PUBLICA_CONFLUENCE.'/pages/viewpage.action?pageId=29786340';
							$datos['nombre_user']=$user->nombre_completo_socio;

							$contenido =$this->load->view('proyectos/formato_correo_proyecto', $datos, true);

							$clear_address = 1;
							$destino[$user->email_socio] = $datos['nombre_user'];

							if($this->params->enviar_correo($remitente,$clear_address,$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
								/*$directorio=BASE_DIRECTORIO."/correo/proyectos/";
								if (!file_exists($directorio)) {
									$directorio = mkdir($directorio,0777);
								}
								if (!file_exists($directorio.$id)) {
									$directorio2 = mkdir($directorio.$id,0777);
								}
								$nuevoarchivo = fopen($directorio.$id."/".$user->email_socio.".html", "w+") or die("Problemas en la creacion");
								fwrite($nuevoarchivo,$contenido);
								fclose($nuevoarchivo); */
							}
							
							
							
							/*else{
								$directorio=BASE_DIRECTORIO."/correo/error/";
								if (!file_exists($directorio)) {
									$directorio1 = mkdir($directorio,0777);
								}
								$directorio=BASE_DIRECTORIO."/correo/error/proyectos/";
								if (!file_exists($directorio)) {
									$directorio2 = mkdir($directorio,0777);
								}
								$directorio=BASE_DIRECTORIO."/correo/error/proyectos/".$id;
								if (!file_exists($directorio)) {
									$directorio3 = mkdir($directorio,0777);
								}
								$nuevoarchivo = fopen($directorio."/".$user->email_socio.".html", "w+") or die("Problemas en la creacion");
								fwrite($nuevoarchivo,$contenido);
								fclose($nuevoarchivo);
							}*/

					}
				}
			}
	}
	function add_form_contacto($id_contact,$item=0){
		$nombre_contacto=array(
			'id'=>'nombre_contacto',
			'name'=>'nombre_contacto[]',
			'style'=>'width:50%',
			'value'=>''
		);
		$empresa_contacto=array(
			'id'=>'empresa_contacto',
			'name'=>'empresa_contacto[]',
			'style'=>'width:50%',
			'value'=>''
		);
		$cargo_contacto=array(
			'id'=>'cargo_contacto',
			'name'=>'cargo_contacto[]',
			'style'=>'width:30%',
			'value'=>''

		);
		$email_contacto=array(
			'id'=>'email_contacto',
			'name'=>'email_contacto[]',
			'style'=>'width:40%',
			'value'=>''
		);
		$direccion_contacto=array(
			'id'=>'direccion_contacto',
			'name'=>'direccion_contacto[]',
			'style'=>'width:70%',
			'value'=>''
		);
		$telefono_contacto=array(
			'id'=>'telefono_contacto',
			'name'=>'telefono_contacto[]',
			'style'=>'width:15%',
			'value'=>''
		);

		if($id_contact!=0){
			$ct=$this->buscar_contacto($id_contact);
			$nombre_contacto['value']=$ct->Nombre_contact;
			$empresa_contacto['value']=$ct->Empresa_contact;
			$cargo_contacto['value']=$ct->Cargo_contact;
			$email_contacto['value']=$ct->Email_contact;
			$direccion_contacto['value']=$ct->Direccion_contact;
			$telefono_contacto['value']=$ct->Telefono_contact;
			$datos['tipo_contacto_sel']=$ct->id_tipo;
			$datos['id_contacto']=$id_contact;
			$datos['principal']=$ct->Principal_contact;
		}
		else{
			$datos['tipo_contacto_sel']=0;
			$datos['id_contacto']=0;
			$datos['principal']=1;
		}

		if($item==0)$datos['item']=1;
		else $datos['item']=$item+1;

		$datos['nombre_contacto']=$nombre_contacto;
		$datos['empresa_contacto']=$empresa_contacto;
		$datos['cargo_contacto']=$cargo_contacto;
		$datos['email_contacto']=$email_contacto;
		$datos['direccion_contacto']=$direccion_contacto;
		$datos['telefono_contacto']=$telefono_contacto;

		$datos['tipo_contacto']= $this->llenar_combo_tipo_contact();
		$contact='';

		$contact=$this->load->view('proyectos/form_contact.php',$datos,true);

		return $contact;
	}

	function llenar_combo_tipo_contact(){
		$query=$this->db->order_by('id_tipo','asc');
		$query = $this->db->get('proyectos_contactos_tipos');
		$lista_sector['']='Sin Clasificación'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado_sector){
				$lista_sector[$resultado_sector['id_tipo']]= $resultado_sector['Nombre_tipo'];

			}

			return $lista_sector;
		}
	}

	function guardar_contactos($datos,$id=0){
		if($id==0){
			$datos['id_user']=$this->session->userdata('id_login');
			$this->db->insert('proyectos_contactos',$datos);
		}
		else{
			$this->db->where('id_contact',$id);
			$this->db->update('proyectos_contactos',$datos);
		}
	}
	function buscar_contacto($id){
		$this->db->where('id_contact',$id);
		$resp=$this->db->get('proyectos_contactos');
		return $resp->first_row();
	}
	function buscar_contactos_pro($id,$princ=2){
		if($princ==1){
			$this->db->where('Principal_contact',1);
		}
		else if($princ==0){
			$this->db->where('Principal_contact',0);
		}

		$this->db->where('id_pro',$id);
		$resp=$this->db->get('proyectos_contactos');
		return $resp->result();
	}
	function procesa_contactos($id,$a_contact){
		if(is_array($a_contact['nombre_contacto'])){
				if(isset($a_contact['arr_id_contacto'])){
					$suma_ct=0;
					$contactos_ant = array();
					$contactos_proyecto_ant=$this->buscar_contactos_pro($id);
					foreach($contactos_proyecto_ant as $cpant){
						$contactos_ant[$suma_ct]=$cpant->id_contact;
						$suma_ct ++;
					}
				}

				foreach($a_contact['nombre_contacto'] as $key=>$nombre){
					if($nombre!=''){
						$datos_contact['id_pro']=$id;
						$datos_contact['id_tipo']=$a_contact['tipo_contacto'][$key];
						$datos_contact['Nombre_contact']=$nombre;
						$datos_contact['Empresa_contact']=$a_contact['empresa_contacto'][$key];
						$datos_contact['Cargo_contact']=$a_contact['cargo_contacto'][$key];
						$datos_contact['Email_contact']=$a_contact['email_contacto'][$key];
						$datos_contact['Telefono_contact']=$a_contact['telefono_contacto'][$key];
						$datos_contact['Direccion_contact']=$a_contact['direccion_contacto'][$key];
						$datos_contact['Principal_contact']=$a_contact['principal'][$key];
						//var_dump($datos_contact);
						if(isset($a_contact['arr_id_contacto']))
						$this->guardar_contactos($datos_contact,$a_contact['arr_id_contacto'][$key]);
						else
						$this->guardar_contactos($datos_contact);
					}
				}

				if(isset($a_contact['arr_id_contacto'])){
					/*Eliminar contactos antiguos*/
					$contactos_proyecto=$this->buscar_contactos_pro($id);

					//var_dump($contactos_proyecto_ant);die();
					foreach($contactos_proyecto as $cp){
						if(in_array($cp->id_contact,$contactos_ant,true)){
							if(!in_array($cp->id_contact,$a_contact['arr_id_contacto'])){
								$this->borrar_contacto($cp->id_contact);
							}
						}
					}
				}

			}
	}
	function borrar_contacto($id){
		$this->db->where('id_contact',$id);
		$this->db->delete('proyectos_contactos');
	}

	function arma_contactos($id,$principal,$sum_contact=0){

		$b_contactos_princ=$this->proyecto->buscar_contactos_pro($id,$principal);
		$texto=($principal==1)?'Principal':'Secundario';
		$divid=($principal==1)?'caja_contact_princ':'caja_contact_secun';
			$contactos='';
			if((is_array($b_contactos_princ))&&($b_contactos_princ!='')){
				$contactos.='<div id="'.$divid.'" class="connectedSortable" style="min-height:320px;position:relative;margin:20px;border:2px solid #666;background-color:#fff">
	<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;-moz-border-radius:10px 10px 10px 0px;border-radius:10px 10px 10px 0px;-webkit-border-radius:10px 10px 10px 0px;">Contacto '.$texto.'</span>';

				foreach($b_contactos_princ as $cp){
					$contactos.=$this->add_form_contacto($cp->id_contact,$sum_contact);
					$sum_contact++;
				}
				$contactos.='</div>';
			}
			else{
				$contactos.='<div id="'.$divid.'" class="connectedSortable" style="min-height:320px;position:relative;margin:20px;border:2px solid #666;background-color:#fff">
	<span style="position:absolute;padding:5px;color:#fff;background-color:#666;top:-15px;left:-2px;-moz-border-radius:10px 10px 10px 0px;border-radius:10px 10px 10px 0px;-webkit-border-radius:10px 10px 10px 0px;">Contacto '.$texto.'</span>';
				$contactos.=$this->add_form_contacto(0);
				$contactos.='</div>';
			}
			$ct['contactos']=$contactos;
			$ct['item']=$sum_contact;
		return $ct;
	}
	function mostrar_calif_pendientes($param_orden,$tipo_orden){
		$this->db->order_by($param_orden,$tipo_orden);
		$this->db->where('Nota_cal',NULL);
		$this->db->join('proyectos pro','pro.id_pro=pc.id_pro');
		$this->db->join('m_user','m_user.id_user=pc.User_cal');
		$this->db->join('u_pais','u_pais.id_pais=pro.id_pais');
		$this->db->join('proyectos_sector ps','ps.id_sector=pro.id_sector');
		$query=$this->db->get('proyectos_calificaciones pc');
		return $query->result();
	}

	function mostrar_calif_realizadas($param_orden,$tipo_orden){
		$this->db->order_by($param_orden,$tipo_orden);
		$this->db->where('Nota_cal !=', '');
		$this->db->join('proyectos pro','pro.id_pro=pc.id_pro');
		$this->db->join('m_user','m_user.id_user=pc.User_cal');
		$this->db->join('u_pais','u_pais.id_pais=pro.id_pais');
		$this->db->join('proyectos_sector ps','ps.id_sector=pro.id_sector');
		$query=$this->db->get('proyectos_calificaciones pc');
		return $query->result();
	}
	function editar_calificacion($id){
		$this->db->where('id_cal', $id);
		$resp=$this->db->get('proyectos_calificaciones');
		return $resp->first_row();
	}
	function editar_calificacion_pro($id_pro,$version){
		$this->db->where('id_pro', $id_pro);
		$this->db->where('Version_confluence_cal', $version);
		$this->db->where('Nota_cal', NULL);
		$resp=$this->db->get('proyectos_calificaciones');
		return $resp->first_row();
	}
	function guardar_calificacion($datos,$id=0){
		if($id==0){
			$resp=$this->db->insert('proyectos_calificaciones', $datos);
		}
		else{
			$this->db->where('id_cal',$id);
			$resp=$this->db->update('proyectos_calificaciones',$datos);
		}
		return $resp;
	}
	function guardar_imagen($datos){
		$this->db->insert('proyectos_imagenes',$datos);
	}
	function borrar_imagen($id,$fileName){
		$this->db->where('id_pro',$id);
		$this->db->where('Nombre_img',$fileName);
		$this->db->delete('proyectos_imagenes');
	}
	function listado_coordinadores(){
	   $listado=array();
	   $this->db->order_by('Nombre_completo_user','asc');
	   $this->db->where('id_area','1');
	   $this->db->join('m_user_x_area','m_user_x_area.id_user=m_user.id_user');
	   $query = $this->db->get('m_user');
	   foreach ($query->result() as $cor) {
	   		$listado[$cor->id_user]=$cor->Nombre_completo_user;
	   }
	   return $listado;
	}
	function validar_version_anterior($id_pro,$ver){
		if(($ver!=1)&&($ver!='')){
			$this->db->where('id_pro',$id_pro);
			$this->db->where('Version_confluence_cal',$ver-1);
			$query=$this->db->get('proyectos_calificaciones');
			if($query->num_rows()>0){
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
	}
	function guardar_actualizacion($tipo,$id,$notif,$calif,$registros,$ver_intra,$fecha_pro=NULL,$fecha_ant_pro=NULL){
		$pro=$this->editar_proyecto($id);
		$datos_act['Version_confluence_act']=$ver_intra;
		$datos_act['id_pro']=$id;
		$datos_act['Tipo_act']=$tipo;
		$datos_act['Notificar_act']=$notif;
		$datos_act['Calificar_act']=$calif;
		$datos_act['Cambios_act']=$registros;
		$datos_act['id_user']=$this->session->userdata('id_login');
		$datos_act['Fecha_act_pro']=$fecha_pro;
		$datos_act['Fecha_anterior_pro']=$fecha_ant_pro;
		$datos_act['Fecha_act']=date('Y-m-d');
		$datos_act['Revision_act']='pendiente';
		$this->db->insert('proyectos_actualizaciones', $datos_act);
		$data_pro['Revision_pro']='pendiente';
		$resp=$this->guardar_edicion($data_pro,$id);
	}
	function guardar_edicion_actualizacion($datos,$id){
		$this->db->where('id_pro', $id);
		$resp=$this->db->update('proyectos_actualizaciones', $datos);
		return $resp;
	}
	function registro_previo_cambios($id_pro){
		$pro=$this->editar_proyecto($id_pro);
		$registros['nombre']=$pro->Nombre_pro;
		$registros['estado']=$pro->Estado_pro;
		$registros['cant_contact']=$this->total_contact($id_pro);
		$registros['desc']=$pro->Desc_pro;
		$registros['etapa_actual']=$pro->Etapa_actual_pro;
		$registros['produccion']=$pro->Produccion_pro;
		$registros['medida_produccion']=$pro->Medicion_produccion_pro;
		$registros['cant_hitos']=$this->total_hitos($id_pro);
		$registros['sector']=$pro->id_sector;
		$registros['ultima_informacion']=str_ireplace(' ', '',preg_replace('/[\n|\r|\n\r]/i','',$pro->ultima_informacion_pro));
		$registros['historial']=str_ireplace(' ', '',preg_replace('/[\n|\r|\n\r]/i','',$pro->Historial_pro));
		$registros['cant_lici']=$this->total_lici($id_pro);
		$registros['cant_adju']=$this->total_adju($id_pro);
		$registros['inversion']=$pro->Inversion_pro;
		$registros['detalle_equipos']=$pro->detalle_equipos;
		$registros['mo_construccion']=$pro->mo_construccion_pro;
		$registros['mo_operacion']=$pro->mo_operacion_pro;
		$registros['mo_cierre']=$pro->mo_cierre_pro;
		$registros['detalle_suministros']=$pro->detalle_suministros;
		return(base64_encode(serialize($registros)));
	}

	function identificar_cambios($datos,$id_pro){
		$pro=$this->editar_proyecto($id_pro);
		$datos=unserialize(base64_decode($datos));
		$registro_cambios=[];
		//Nombre de Proyecto
		if(isset($datos['nombre']))if($datos['nombre']!=$pro->Nombre_pro)$registro_cambios[]='Nombre de Proyecto';
		//Estado de Proyecto
		if(isset($datos['estado']))if($datos['estado']!=$pro->Estado_pro)$registro_cambios[]='Estado de Proyecto';
		//Sector de Proyecto
		if(isset($datos['sector']))if($datos['sector']!=$pro->id_sector)$registro_cambios[]='Sector de Proyecto';
		//Cantidad de Contactos
		if(isset($datos['cant_contact']))if($datos['cant_contact']!=$this->total_contact($id_pro))$registro_cambios[]='Total Contactos';
		//Descripción
		if(isset($datos['desc']))if($datos['desc']!=$pro->Desc_pro)$registro_cambios[]='Descripción';
		//etapa actual
		if(isset($datos['etapa_actual']))if($datos['etapa_actual']!=$pro->Etapa_actual_pro)$registro_cambios[]='Etapa Actual';
		//Valor producción
		if(isset($datos['produccion']))if($datos['produccion']!=$pro->Produccion_pro)$registro_cambios[]='Producción';
		//Medida producción
		if(isset($datos['medida_produccion']))if($datos['medida_produccion']!=$pro->Medicion_produccion_pro)$registro_cambios[]='Medida Producción';
		//Cantidad de Hitos
		if(isset($datos['cant_hitos']))if($datos['cant_hitos']!=$this->total_hitos($id_pro))$registro_cambios[]='Total Hitos';
		//Cantidad de Licitaciones
		if(isset($datos['cant_lici']))if($datos['cant_lici']!=$this->total_lici($id_pro))$registro_cambios[]='Total Licitaciones';
		//Cantidad de Adjudicaciones
		if(isset($datos['cant_adju']))if($datos['cant_adju']!=$this->total_adju($id_pro))$registro_cambios[]='Total Adjudicaciones';
		//Ultima Información
		if(isset($datos['ultima_informacion']))if(strcmp($datos['ultima_informacion'],str_ireplace(' ', '',preg_replace('/[\n|\r|\n\r]/i','',$pro->ultima_informacion_pro))) != 0)$registro_cambios[]='Última Información';
		//Historial
		if(isset($datos['historial']))if(strcmp($datos['historial'],str_ireplace(' ', '',preg_replace('/[\n|\r|\n\r]/i','',$pro->Historial_pro))) != 0)$registro_cambios[]='Historial';
		//Fecha de Actualización
		if(isset($datos['fecha_actualiza']))if($datos['fecha_actualiza'] != $pro->Fecha_actualizacion_pro)$registro_cambios[]='Fecha de Actualización';
		//Inversión
		if(isset($datos['inversion']))if($datos['inversion'] != $pro->Inversion_pro)$registro_cambios[]='Inversión';
		//Detalle Equipos
		if(isset($datos['detalle_equipos']))if($datos['detalle_equipos'] != $pro->detalle_equipos)$registro_cambios[]='Detalle Equipos';
		//Detalle Suministros
		if(isset($datos['detalle_suministros']))if($datos['detalle_suministros'] != $pro->detalle_suministros)$registro_cambios[]='Detalle Suministros';
		//Mano de Obra Construcción
		if(isset($datos['mo_construccion']))if($datos['mo_construccion'] != $pro->mo_construccion_pro)$registro_cambios[]='Mano de Obra Construcción';
		//Mano de Obra Operación
		if(isset($datos['mo_operacion']))if($datos['mo_operacion'] != $pro->mo_operacion_pro)$registro_cambios[]='Mano de Obra Operación';
		//Mano de Obra Cierre
		if(isset($datos['mo_cierre']))if($datos['mo_cierre'] != $pro->mo_cierre_pro)$registro_cambios[]='Mano de Obra Cierre';

		if(is_array($registro_cambios))
			$reg_cambios=implode(',',$registro_cambios);
			else
			$reg_cambios='';
		return $reg_cambios;
	}
	function llenar_combo_medida($id=0){
		$id_select=0;
		$this->db->order_by("Sigla_med", "asc");
		$query = $this->db->get('proyectos_medicion_produccion');
		$arreglo= array();
		$lista = array();
		$lista['']='- Medición -'; // Opción sin valor, servirá de selección por defecto.
		if($query->num_rows()>0){
			foreach($query->result_array() as $resultado){
				$lista[$resultado['id_med']]= $resultado['Sigla_med'];
				if($id==$resultado['id_med']){
					$id_select=$id;
				}

			}

				return $lista;

		}
	}
	function editar_medida($id){
		$this->db->where('id_med',$id);
		$query=$this->db->get('proyectos_medicion_produccion');
		return $query->first_row();
	}


    /*20141008 - Jose Meneses*/
    function mostrar_coordinador_proyecto(){

        $this->db->select('DISTINCT(p.id_pro), p.Nombre_pro, p.Nombre_generico_pro, p.Fecha_actualizacion_pro, mu.id_user, mu.Nombre_completo_user');
        $this->db->from('proyectos p');
        $this->db->join('m_user mu', 'p.id_usuario_modifica = mu.id_user', 'left');
        $this->db->join('proyectos_x_hitos pxh', 'p.id_pro = pxh.id_pro', 'left');
        $this->db->where('p.borrar', 0);
        $this->db->where('p.Etapa_actual_pro != ', 8);
        //$this->db->where_not_in('pxh.id_hito', array($this->params->hito_desistido, $this->params->hito_desistido2));
        $this->db->where('( ( SELECT count(id_hito) FROM proyectos_x_hitos WHERE proyectos_x_hitos.id_pro = p.id_pro ) = 0 OR ( ( ( SELECT id_hito FROM proyectos_x_hitos WHERE id_pro = p.id_pro ORDER BY ano_hito DESC, trim_hito DESC, id_proyxhito DESC LIMIT 1) != 14 ) AND ( ( SELECT id_hito FROM proyectos_x_hitos WHERE proyectos_x_hitos.id_pro = p.id_pro ORDER BY ano_hito DESC, trim_hito DESC, id_proyxhito DESC LIMIT 1 ) != 17 ) ) )');
        $this->db->order_by('mu.id_user', 'ASC');

        $query = $this->db->get();
        //$this->output->enable_profiler(TRUE);
		$result = $query->result();

		return $result;

    }

    /*20141008 - Jose Meneses*/
    function mostrar_proyectos_coordinador($ids_pro, $id_user){

        $this->db->select('DISTINCT(p.id_pro), p.Nombre_pro, p.Nombre_generico_pro, p.Fecha_actualizacion_pro, ps.Nombre_sector, up.Nombre_pais, ur.Nombre_region, uc.Nombre_comuna, mu.Nombre_completo_user');
        $this->db->from('proyectos p');
        $this->db->join('proyectos_sector ps', 'p.id_sector = ps.id_sector', 'left');
        $this->db->join('u_pais up', 'p.id_pais = up.id_pais', 'left');
        $this->db->join('u_region ur', 'p.id_region = ur.id_region', 'left');
        $this->db->join('u_comuna uc', 'p.id_comuna = uc.id_comuna', 'left');
        $this->db->join('m_user mu', 'p.id_usuario_modifica = mu.id_user', 'left');
        $this->db->join('proyectos_x_hitos pxh', 'p.id_pro = pxh.id_pro', 'left');
        $this->db->where('p.borrar', 0);
        $this->db->where('p.Etapa_actual_pro != ', 8);
        //$this->db->where_not_in('pxh.id_hito', array($this->params->hito_desistido, $this->params->hito_desistido2));
        $this->db->where('( ( SELECT count(id_hito) FROM proyectos_x_hitos WHERE proyectos_x_hitos.id_pro = p.id_pro ) = 0 OR ( ( ( SELECT id_hito FROM proyectos_x_hitos WHERE id_pro = p.id_pro ORDER BY ano_hito DESC, trim_hito DESC, id_proyxhito DESC LIMIT 1) != 14 ) AND ( ( SELECT id_hito FROM proyectos_x_hitos WHERE proyectos_x_hitos.id_pro = p.id_pro ORDER BY ano_hito DESC, trim_hito DESC, id_proyxhito DESC LIMIT 1 ) != 17 ) ) )');
        if (!is_null($id_user) && trim($id_user) != ""){
            $this->db->where('p.id_usuario_modifica', $id_user);
        }
        $this->db->where_in('p.id_pro', $ids_pro);
        $this->db->order_by('p.id_pro', 'ASC');

        $query = $this->db->get();
        //$this->output->enable_profiler(TRUE);
		$result = $query->result();

		return $result;

    }

    function actualiza_estado_proyecto(){
    	//$this->db->where('(pxh.id_hito = 13)'); //PARALIZADO
    	$this->db->where('(pxh.id_hito = 14) OR (pxh.id_hito = 17) OR (pxh.id_hito = 18) OR (pxh.id_hito = 19)'); //DESISTIDO
    	//$this->db->join('proyectos_x_hitos pxh','pxh.id_pro=pro.id_pro','left');
    	$query=$this->db->get('proyectos_x_hitos pxh');
    	$this->output->enable_profiler(true);
    	foreach ($query->result() as $pro) {
    		$data['Estado_pro']='D';
    		$this->db->where('id_pro',$pro->id_pro);
    		$this->db->update('proyectos',$data);
    	}
    }

    function ultimo_hito($id_pro){
      	$query=$this->db->query('SELECT id_hito FROM proyectos_x_hitos WHERE id_pro='.$id_pro.' ORDER BY ano_hito DESC, trim_hito DESC, id_hito DESC');
    	//$this->output->enable_profiler(true);
    	return $query->first_row();
    }
    function total_hitos($id_pro){
    	$this->db->where('id_pro',$id_pro);
    	$query=$this->db->get('proyectos_x_hitos');
    	return $query->num_rows();
    }
    function total_lici($id_pro){
    	$this->db->where('id_pro',$id_pro);
    	$query=$this->db->get('licitaciones');
    	return $query->num_rows();
    }
    function total_adju($id_pro){
    	$this->db->where('id_proy_adj',$id_pro);
    	$query=$this->db->get('adjudicaciones');
    	return $query->num_rows();
    }
    function total_contact($id_pro){
    	$this->db->where('id_pro',$id_pro);
    	$query=$this->db->get('proyectos_contactos');
    	return $query->num_rows();
    }
    /*jomp Proceso de subida de proyectos a www.portalminero.com masivo*/
    function mostrar_revision_masivo($param_orden,$tipo_orden){
    	$this->db->where('Borrar',0);
		$this->db->where('Estado_pro', 'A');
		$this->db->where('id_pagina_pro !=', 'NULL');
		$this->db->where('Fecha_actualizacion_pro !=','0000-00-00');
		$this->db->where('Revision_pro','pendiente');
		$this->db->order_by($param_orden,$tipo_orden);
		$this->db->select("proyectos.*,m_user.Nombre_completo_user, proyectos_sector.*, u_pais.*, u_region.*, u_comuna.*, (SELECT id_hito FROM `proyectos_x_hitos` where id_pro=proyectos.id_pro order by ano_hito desc, trim_hito desc, id_proyxhito desc limit 1) ultimo_hito");
		$this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector");
		$this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais");
		$this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
		$this->db->join("m_user", "m_user.id_user = proyectos.id_usuario_modifica", 'left');
		$this->db->join("u_comuna", "u_comuna.id_comuna = proyectos.id_comuna", 'left');
		
		$query = $this->db->get("proyectos");
		$result = $query->result();
		return $result;
    }
    //fin jomp
    //19-11-2014 Listado de proyectos para revisar
    function mostrar_revision($param_orden,$tipo_orden){
        
        // EPF
        $this->db->where('Borrar',0);
        $this->db->where('Revision_pro','pendiente');
        $this->db->order_by($param_orden,$tipo_orden);
        
        $result = array();
        
       // $this->db->select("proyectos.*,m_user.Nombre_completo_user, proyectos_sector.*, u_pais.*, u_region.*, u_comuna.*, (SELECT id_hito FROM `proyectos_x_hitos` where id_pro=proyectos.id_pro order by ano_hito desc, trim_hito desc, id_proyxhito desc limit 1) ultimo_hito");
        $this->db->select("proyectos.*,m_user.Nombre_completo_user, proyectos_sector.*, u_pais.*, u_region.*, u_comuna.*, (SELECT id_hito FROM `proyectos_x_hitos` WHERE id_pro= proyectos.id_pro ORDER BY ano_hito  ,`trim_hito` , id_proyxhito DESC LIMIT 1) ultimo_hito");
        $this->db->join("proyectos_sector", "proyectos_sector.id_sector = proyectos.id_sector");
        $this->db->join("u_pais", "u_pais.id_pais = proyectos.id_pais");
        $this->db->join("u_region", "u_region.id_region = proyectos.id_region", 'left');
        $this->db->join("m_user", "m_user.id_user = proyectos.id_usuario_modifica", 'left');
        $this->db->join("u_comuna", "u_comuna.id_comuna = proyectos.id_comuna", 'left');
        $query = $this->db->get("proyectos");
        $result = $query->result();
        return $result;
	}
	function versiones_revision($id_pro){
		$this->db->where('id_pro',$id_pro);
		$this->db->where('Revision_act','pendiente');
		$this->db->join('m_user','m_user.id_user=act.id_user');
		$resp=$this->db->get('proyectos_actualizaciones act');
		return($resp->result());
	}
	function validar_notificar_proyecto($id_pro){
		$this->db->where('id_pro',$id_pro);
		$this->db->where('Revision_act','pendiente');
		$this->db->where('Notificar_act','1');
		$this->db->select_sum('Notificar_act');
		$resp=$this->db->get('proyectos_actualizaciones act');
		if($resp->num_rows()>0){
			return($resp->first_row()->Notificar_act);
		}
		else{
			return(0);
		}
	}

}
?>
