

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<script>
function FiltraSelect(nameobj){
	//alert(nameobj);
	obj=document.getElementById(nameobj); 
    id=obj.value; 
	//alert(id)


	if(! document.getElementById('fil_ajax').checked){
		return;
    }
	if(id=""){ return; }


	var filtro_tipo           = document.getElementById("filtro_tipo").value;
	var filtro_estado         = document.getElementById("filtro_estado").value;
	var filtro_mandante       = document.getElementById("filtro_mandante").value;
	var filtro_pais           = document.getElementById("filtro_pais").value;
	var filtro_region         = document.getElementById("filtro_region").value;
	var filtro_obra           = document.getElementById("filtro_obra").value;
	var filtro_equipo         = document.getElementById("filtro_equipo").value;
	var filtro_suministro     = document.getElementById("filtro_suministro").value;
	var filtro_servicio       = document.getElementById("filtro_servicio").value;
	var filtro_etapa          = document.getElementById("filtro_etapa").value;
	var filtro_responsable    = document.getElementById("filtro_responsable").value;
        var vid_sector            = <? echo $id_sector;?>;
	
	if(filtro_estado==""){
		 filtro_estado="A"
	};	
	   
		   var parametros = {
						"filtro_tipo"        : filtro_tipo,
						"filtro_estado"      : filtro_estado,
						"filtro_mandante"    : filtro_mandante,
						"filtro_pais"        : filtro_pais,
						"filtro_region"      : filtro_region,
						"filtro_obra"        : filtro_obra,
						"filtro_equipo"      : filtro_equipo,
						"filtro_suministro"  : filtro_suministro,
						"filtro_servicio"    : filtro_servicio,
						"filtro_etapa"       : filtro_etapa,
						"filtro_responsable" : filtro_responsable,
                                                "vid_sector"         : vid_sector
						
				};

				enb_dis(false);

				$.ajax({
						data:  parametros,
						url:   '<?= base_url();?>filtros/llena_select/',
						type:  'post',

						beforeSend: function () {
								$("#resultado").html("Espere por favor...");
								$("#loader").css("display", "inline-block");
						},
						success:  function (response) {
								$("#div_filtros_select").html(response);
								$("#resultado").html("");
								$("#loader").css("display", "none");
														
								
								
						}
						, error: function(error){
						    $("#resultado").html("Sin respuesta, restablesca los filtros.");
                                                     enb_dis(true);	
						 }
				});
enb_dis(true);		
}

function enb_dis(dispinible){
  document.getElementById("filtro_tipo").disabled         = dispinible;
  document.getElementById("filtro_estado").disabled       = dispinible;
  document.getElementById("filtro_mandante").disabled     = dispinible;
  document.getElementById("filtro_pais").disabled         = dispinible;
  document.getElementById("filtro_region").disabled       = dispinible;
  document.getElementById("filtro_obra").disabled         = dispinible;
  document.getElementById("filtro_equipo").disabled       = dispinible;
  document.getElementById("filtro_suministro").disabled   = dispinible;
  document.getElementById("filtro_servicio").disabled     = dispinible;
  document.getElementById("filtro_etapa").disabled        = dispinible;
  document.getElementById("filtro_responsable").disabled  = dispinible;
}
</script>



<div style="clear:both">&nbsp;</div>
<div>
<div style="color:#24587F; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">
	Proyectos de <? echo $nombre_sector;?>
<!--Generar Tabla de Estatus de los proyectos desde aqui JOMP-->
	<br />
    <br />
	<table width="90%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse">
		<thead>
    		<tr>
    			<td class="titulo" style="display:table-cell;padding:5px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%">Activos
                </td>
				<!--Estilos del mensaje de alerta del signo de interrogacion -->       
				<style type="text/css">
					.tooltip {
				    	display: inline;
					    position: relative;
					    opacity: .95;
						-moz-opacity: .95;
					   filter:alpha(opacity=95);
					}
  				.tooltip:hover:after {
					/*top: 20px; cuadrado debajo*/
				    bottom:16px;/* cuadrado Encima*/    
				    content: attr(original-title); /* este es el texto que será mostrado */
				    left: 20%;
				    position: absolute;
				    z-index: 500;
				    /* el formato gráfico */
				   /*background: rgba(255,255,255, 0.2) ;*/
				    /*JOMP ORIGINALbackground: orange ;*/
					background: #ffec4f ;
				    border: 1px solid #000;
					border-radius: 10px;
					
				    color: #000; /* el color del texto */
				    font-family: Arial;
				    font-size: 11px;
					font-weight:normal;
				    /*padding: 1px 2px; ORIGINAL*/
					padding-left:1px;
					padding-right:1px;
				    text-align: center;
				  /*JOMP  text-shadow: 1px 1px 1px #000;*/
				    width: 130px;
  				}

			</style>
			<td class="titulo" style="display:table-cell;padding:5px;font-size:13px;text-align: right;vertical-align: central;background-color:#ecf4f7;color:#066293" width="10%">Activos<a href="#" original-title="Proyectos activos oficialmente, pero sin movimiento, o muy poco" class="tooltip"><img align=right src ="http://pm.portalminero.com/sitio_portal/images/interrogacion.png"></a><center>Diferidos</center></td>
               </td>
                <!---******************************************************************************-->
                
				<td class="titulo" style="display:table-cell;padding:5px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%">Paralizados
                </td>
	    		<td class="titulo" style="display:table-cell;padding:5px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%">En Operación
                </td>
				<td class="titulo" style="display:table-cell;padding:5px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%">Desistidos
                </td>
			</tr>
		</thead>
        	<tr>
            	<?php
				
					$estatus = array("A","P","O","D","F");		
					foreach ($estatus as $valor){
						$whereEstatus= "";
						$consulta = "";
						if ($id_sector == "0"){
							if ($valor =='O'){
								//$consulta = $this->db->where("Estado_pro",$valor)->count_all_results("proyectos");
								$ope=8;
								$whereEstatus= array('id_pagina_pro <>' =>"", 'Estado_pro' => $valor, 'Etapa_actual_pro' => $ope);
							}
							else{
								$ope=8;
								$whereEstatus= array('id_pagina_pro <>' =>"", 'Estado_pro' => $valor, 'Etapa_actual_pro <>' => $ope);
							}
						}		
						else{
							if ($valor == 'O'){
									$ope=8;
									$whereEstatus= array('id_pagina_pro <>' =>"", 'id_sector' => $id_sector, 'Estado_pro' => $valor, 'Etapa_actual_pro' => $ope);
								}
							else{
								$ope=8;
								$whereEstatus= array('id_pagina_pro <>' =>"", 'Estado_pro' => $valor, 'id_sector' => $id_sector, 'Etapa_actual_pro <>' => $ope);
							}
						}
						$consulta = $this->db->where($whereEstatus)->count_all_results("proyectos");
						if ($valor == "A"){
							$activos = $consulta;
						}
						elseif ($valor == "P"){
							$paralizado = $consulta;
						}
						elseif ($valor == "O"){
							$operacion = $consulta;
						}
						elseif ($valor =="D"){
							$desistido = $consulta;
						}
						elseif ($valor =="F"){
							$diferido = $consulta;
						}
					}
				?>
    	    			<td style="text-align:center;padding:5px;border-color:#a4c7c0">
                			<a href="javascript:pagina(<?echo $actual;?>, 0, 'tabla_A')" class="enlace" style="color:#254E6D;font-weight:bold"><?= $activos?></a>                      
		                </td>
                        <td style="text-align:center;padding:5px;border-color:#a4c7c0;color:#254E6D;font-weight:bold"><a href="javascript:pagina(<?echo $actual;?>, 0, 'tabla_F')" class="enlace" style="color:#254E6D;font-weight:bold"><?= $diferido?></a>
                                    
		                </td>
						<td style="text-align:center;padding:5px;border-color:#a4c7c0">
	            			<a href="javascript:pagina(<?echo $actual;?>, 0, 'tabla_P')" class="enlace" style="color:#254E6D;font-weight:bold"><?= $paralizado?></a>
                		</td>
				    	<td style="text-align:center;padding:5px;border-color:#a4c7c0">
    			        	<a href="javascript:pagina(<?echo $actual;?>, 0, 'tabla_O')" class="enlace" style="color:#254E6D;font-weight:bold"><?= $operacion?></a> 
        		        </td>
						<td style="text-align:center;padding:5px;border-color:#a4c7c0">
							<a href="javascript:pagina(<?echo $actual;?>, 0, 'tabla_D')" class="enlace" style="color:#254E6D;font-weight:bold"><?= $desistido?></a>
        		        </td>
			</tr>
    </table>

<!--Generar Tabla de Estatus de los proyectos hasta aqui JOMP-->
</div>
<div style="float:right; width:36%;" id="div_busqueda_nombre">
	Buscar Por Nombre de Proyecto: <? echo form_input($nombre);?>
	<input type="button" class="btn_buscar_nombre btn_verde" value="Buscar" style="margin-top: 0.4rem;" onClick="javascript:pagina(<?echo $actual;?>, 0)" />
</div>
</div>
<div style="clear:both;padding-bottom:10px;"><a href="/pages/viewpage.action?pageId=72096318" style="color:#109aa5;"><b>Suscribirse a proyectos nuevos: </b>Seleccione sectores de su interés</a></div>
<div class="columna_listado_pro" style="width:80%;float:left;padding-right:1%;">
<? if(isset($proyectos) && is_array($proyectos) && sizeof($proyectos)>0){?>
<div style="width:100%; clear:both; margin:0 auto; float:left; padding-bottom:5px; color:#254E6D" align="center" class="paginador"><? echo $paginador;?></div>
<?
if($selected!=""){
?>
<div style="width:100%; clear:both; float:left; color:#d68277" align="center"><b><? echo $selected;?></b></div>
<?
}
?>
<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse">
	<thead>
		<tr>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="40%"><a href="javascript:pagina(<?echo $actual;?>, 0, 'ordernombre_<? echo $ordernombre?>')" class="order" style="color:#066293">Nombre<br/><img src="/sitio_portal/images/<? if($ordernombre=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png";}?>" /></a>
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%">Pa&iacute;s
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="20%">Regi&oacute;n
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%"><a href="javascript:pagina(<?echo $actual;?>, 0, 'orderinversion_<? echo $orderinversion?>')" class="order" style="color:#066293">Inversi&oacute;n (US$MM)<br/><img style="clear:both;" src="/sitio_portal/images/<? if($orderinversion=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png";}?>" /></a>
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%"><a href="javascript:pagina(<?echo $actual;?>, 0, 'orderfecha_<? echo $orderfecha?>')"class="order" style="color:#066293">Fecha Actualizaci&oacute;n<br/><img style="clear:both;" src="/sitio_portal/images/<? if($orderfecha=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png";}?>" /></a>
			</td>
			<!--<td class="titulo">
				<div align="center">Favorito</div>-->
			</td><td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" width="10%">Seguir
			</td>
		</tr>
	</thead>
	<tbody>
		<? foreach($proyectos as $proy){
			if(strstr($proy->url_confluence_pro, "pageId")){
				$t1="&test=1";
			}else{
				$t1="?test=1";
			}
		?>
		<tr class="tr_hover">
			<td class="confluenceTd" width="40%"><a target="_top" href="<? echo $proy->url_confluence_pro;?>" onClick="_gaq.push(['_trackEvent', 'proyectos', '<?=$sectores[$proy->pid_sector]?>', '<?=$proy->id_pagina_pro?>']);"><?=$proy->Nombre_pro;?></a> <a rel="shadowbox;width=1100;" href="<? echo $proy->url_confluence_pro.$t1;?>" onClick="_gaq.push(['_trackEvent', 'proyectos', '<?=$sectores[$proy->pid_sector]?>', '<?=$proy->id_pagina_pro?>']);" style="font-size:10px;color:#999;display:inline-block">(Ver En Pop-Up)</a></td>
			<td class="confluenceTd" width="10%"><?=$proy->Nombre_pais;?></td>
			<td class="confluenceTd" width="20%"><?=$proy->Nombre_region;?></td>
			<td class="confluenceTd" width="10%" ><div align="center"><?=(($proy->Inversion_pro != "" && $proy->Inversion_pro!=NULL) ? number_format(round(floatval($proy->Inversion_pro)), 0, ",", ".") : "");?></div></td>
			<!-- <td class="confluenceTd" width="10%"<? if(intval($proy->ultimo_hito)==$this->params->hito_desistido || intval($proy->ultimo_hito)==$this->params->hito_desistido2) { ?>style="color:#4B7B86; font-weight:bold;"<? }else{ if(intval($proy->Etapa_actual_pro)==8){ ?>style="color:#9D2F14; font-weight:bold;"<?}}?>><? if(intval($proy->ultimo_hito)==$this->params->hito_desistido || intval($proy->ultimo_hito)==$this->params->hito_desistido2) { echo "Proyecto Desistido"; }else{ if(intval($proy->Etapa_actual_pro)!=8){ if($proy->Fecha_actualizacion_pro != "" && $proy->Fecha_actualizacion_pro!=NULL){$fecha=explode('-',$proy->Fecha_actualizacion_pro);echo $fecha[2].'-'.$fecha[1].'-'.$fecha[0];}else{echo'';}}else{ echo "Proyecto en Operaci&oacute;n";}}?></td> -->
			<? if ($proy->Revision_pro == 'revisado'){ ?>
				<? if (intval($proy->ultimo_hito) == $this->params->hito_desistido || intval($proy->ultimo_hito) == $this->params->hito_desistido2){ ?>
					<td class="confluenceTd" width="10%" style="color:#1a237e; font-weight:bold;">Proyecto Desistido</td>
				<? }else{ ?>
					<? if (intval($proy->Etapa_actual_pro) == 8){ ?>
						<td class="confluenceTd" width="10%" style="color:#e53935; font-weight:bold;">Proyecto en Operaci&oacute;n</td>
					<? }else{ ?>
						<td class="confluenceTd" width="10%" >
							<? $fecha = explode('-',$proy->Fecha_actualizacion_pro); echo $fecha[2].'-'.$fecha[1].'-'.$fecha[0]; ?>
						</td>
					<? } ?>
				<? } ?>
			<? }else{ ?>
				<td class="confluenceTd" width="10%" style="color:#464646; font-weight:bold;">Proyecto en Actualizaci&oacute;n</td>
			<? } ?>
			<!--<td class="confluenceTd" nowrap><span class="favp1" name="<? echo $proy->id_pagina_pro;?>"></span></td>-->
			<td class="confluenceTd" nowrap width="10%"><span class="watp1" name="<? echo $proy->id_pagina_pro;?>"></span></td>
		</tr>
		<?}?>
	</tbody>
</table>
<div style="width:100%; clear:both; margin:0 auto; float:left; padding-bottom:5px; color:#254E6D" align="center" class="paginador"><? echo $paginador;?></div>

<?}else{?>

<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable" style="border-collapse:collapse">
	<thead>
		<tr>
			<td>
				No se encontraron resultados para su busqueda.
			</td>
		</tr>
	</thead>
</table>
<? }?>
</div>
<div   class="columna_filtros_pro" style="float:right;width:16%;padding-top:15px;padding-left:1%;">

<!--div filtros ajax-->
<div id ="div_filtros_select" >
<!--div filtros ajax-->

<div style="clear:both; font-weight:bold; text-decoration:underline; color:#044073;">Filtros Proyectos</div>
<hr>



<div style="clear:both;"><input type="checkbox" name="fil_ajax" id="fil_ajax"  value="0">Habilitar Filtros Relacionados <a href="#" original-title="Con esta nueva opción de filtros relacionados, al realizar UD una selección en un filtro los demás se llenaran con los datos coincidentes y relacionados a la búsqueda, por consecuencia su búsqueda sera mas exacta y exitosa. ¡¡ATENCIÓN!! En algunos casos puede ser mas lento el llenado de los filtros dependiendo de la complejidad de la búsqueda, de todas formas siempre está la opción de la búsqueda convencional desmarcando la opción." class="tooltip"><img align=right src ="http://pm.portalminero.com/sitio_portal/images/interrogacion.png"></a>
</div><hr>
    


<div style="clear:both;">&nbsp;<label style="font-size: medium; color:#F00" id="resultado"></div>
<div style="width:100%" class="select-box"><div style="clear:both;">Tipo de Proyecto:</div><select style="width:123px;" class="tipo input_radius"  onChange="FiltraSelect(this.id);"  id="filtro_tipo"><option value="">Todos</option>
<? if(is_array($tipo["arr"]) && sizeof($tipo["arr"])>0){
	foreach($tipo["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$tipo["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%" class="select-box">
<div style="clear:both;">Estado del Proyecto:</div>
    <select style="width:123px;" class="estado input_radius" id="filtro_estado" onChange="FiltraSelect(this.id);"  >
        <option value=""  <? if ($estado_pro == ""){ ?> selected="selected/" <? } ?> >Todos</option>
        <option value="A" <? if ($estado_pro == "A"){ ?> selected="selected/" <? } ?> >Activo</option>
        <option value="F" <? if ($estado_pro == "F"){ ?> selected="selected/" <? } ?> >Activo Diferido</option>
        <option value="P" <? if ($estado_pro == "P"){ ?> selected="selected/" <? } ?> >Paralizado</option>
        <option value="D" <? if ($estado_pro == "D"){ ?> selected="selected/" <? } ?> >Desistido</option>
        <option value="O" <? if ($estado_pro == "O"){ ?> selected="selected/" <? } ?> >En Operaci&oacute;n</option>
    </select>
     <!--Se comenta el condicional del select para embasurar la busqueda en la tabla general JOMP -->
</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Mandante:</div><select  onChange="FiltraSelect(this.id);"  style="width:123px;" id="filtro_mandante" class="mandante input_radius"><option value="">Todos</option>
<? if(is_array($mandante["arr"]) && sizeof($mandante["arr"])>0){
	foreach($mandante["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$mandante["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Pa&iacute;s:</div><select style="width:123px;" id="filtro_pais" onChange="FiltraSelect(this.id);"  class="pais input_radius"><option value="">Todos</option>
<? if(is_array($pais["arr"]) && sizeof($pais["arr"])>0){
	foreach($pais["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$pais["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Regi&oacute;n:</div><select style="width:123px;" id="filtro_region"  onChange="FiltraSelect(this.id);"  class="region input_radius"><option value="">Todos</option>
<? if(is_array($region["arr"]) && sizeof($region["arr"])>0){
	foreach($region["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$region["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Obras Principales:</div><select style="width:123px;" id="filtro_obra"  onChange="FiltraSelect(this.id);"  class="obra input_radius"><option value="">Todos</option>
<? if(is_array($obra["arr"]) && sizeof($obra["arr"])>0){
	foreach($obra["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$obra["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Equipos Principales:</div><select style="width:123px;" id="filtro_equipo"  onChange="FiltraSelect(this.id);"  class="equipo input_radius"><option value="">Todos</option>
<? if(is_array($equipo["arr"]) && sizeof($equipo["arr"])>0){
	foreach($equipo["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$equipo["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Suministros Principales:</div><select style="width:123px;" id="filtro_suministro"  onChange="FiltraSelect(this.id);"  class="suministro input_radius"><option value="">Todos</option>
<? if(is_array($suministro["arr"]) && sizeof($suministro["arr"])>0){
	foreach($suministro["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$suministro["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Servicios Principales:</div><select style="width:123px;" id="filtro_servicio"  onChange="FiltraSelect(this.id);"  class="servicio input_radius"><option value="">Todos</option>
<? if(is_array($servicio["arr"]) && sizeof($servicio["arr"])>0){
	foreach($servicio["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$servicio["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="float:left; border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px; padding-top:10px; padding-bottom:10px; padding-left:2%; padding-right:2%; background-color:#F4F4F4">
<div style="width:95%;" class="select-box"><div style="clear:both;">Etapa Actual:</div><select style="width:110px;" id="filtro_etapa"   onChange="FiltraSelect(this.id);" class="etapa input_radius"><option value="">Todos</option>
<? if(is_array($etapa["arr"]) && sizeof($etapa["arr"])>0){
	foreach($etapa["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$etapa["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:95%;" class="select-box"><div style="clear:both;">Responsable Etapa Actual:</div><select style="width:110px;" id="filtro_responsable" class="responsable input_radius"><option value="">Todos</option>
<? if(is_array($empresa["arr"]) && sizeof($empresa["arr"])>0){
	foreach($empresa["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$empresa["var"]){?> selected="selected/" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
</div>
<!--div fin filtros ajax-->
</div>
<!--div fin filtros ajax-->

<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><input type="button" class="btn_buscar_proy btn_azul" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" /></div>
<div style="width:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec"><? echo $id_sector;?></div>
<div style="width:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec2"><? echo base64_encode("listar_pro");?></div>
<div style="width:100%; clear:both; margin:0 auto; display:none;" align="center" class="actual"><? echo $actual;?></div>
<?
if(isset($order)){
	?>
	<div id="isorder" style="display:none;"><? echo $order;?></div>
	<?
}
?>
</div>
