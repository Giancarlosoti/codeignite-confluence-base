<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<div style="clear:both">&nbsp;</div>
<div>
<div style="color:#24587F; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">Proyectos de <? echo $nombre_sector;?></div><div style="float:right; width:40%;">Buscar Por Nombre de Proyecto: <? echo form_input($nombre);?><input type="button" class="btn_buscar_nombre" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" /></div>
</div>
<div style="clear:both">&nbsp;</div>
<div style="width:80%; float:left; padding-right:1%;">
<? if(isset($proyectos) && is_array($proyectos) && sizeof($proyectos)>0){?>
<div style="width:100%; clear:both; margin:0 auto; float:left; padding-bottom:5px; color:#254E6D" align="center" class="paginador"><? echo $paginador;?></div>
<?
if($selected!=""){
?> 
<div style="width:100%; clear:both; float:left; color:#254E6D" align="center"><? echo $selected;?></div>
<?
}
?>
<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse">
	<thead>
		<tr>
			<td class="titulo">
				Nombre
			</td>
			<td class="titulo">
				Pa&iacute;s
			</td>
			<td class="titulo">
				Regi&oacute;n
			</td>
			<td class="titulo">
				<div align="center">Inversi&oacute;n (US$MM)</div>
			</td>
			<td class="titulo">
				<div align="center">Fecha Actualizaci&oacute;n</div>
			</td>
			<td class="titulo">
				<div align="center">Favorito</div>
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
			<td class="confluenceTd"><a target="_top" href="<? echo $proy->url_confluence_pro;?>"><?=$proy->Nombre_pro;?></a> <a rel="shadowbox;width=1100;" href="<? echo $proy->url_confluence_pro.$t1;?>" style="font-size:10px;color:#999;">(Ver En Pop-Up)</a></td>
			<td class="confluenceTd"><?=$proy->Nombre_pais;?></td>
			<td class="confluenceTd"><?=$proy->Nombre_region;?></td>
			<td class="confluenceTd" ><div align="center"><?=(($proy->Inversion_pro != "" && $proy->Inversion_pro!=NULL) ? number_format(intval($proy->Inversion_pro), 0, ",", ".") : "");?></div></td> 
			<td class="confluenceTd"><?=(($proy->Fecha_actualizacion_pro != "" && $proy->Fecha_actualizacion_pro!=NULL) ? $proy->Fecha_actualizacion_pro : "");?></td>
			<td class="confluenceTd"><span class="favp1" name="<? echo $proy->id_pagina_pro;?>"></span></td>
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
<div style="float:right; width:18%; padding-top:15px; padding-left:1%;" class="panel_derecho">
<div style="clear:both; font-weight:bold; text-decoration:underline;">Filtros</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Tipo de Proyecto:</div><select style="width:100%;"class="tipo"><option value="">Todos</option>
<? if(is_array($tipo["arr"]) && sizeof($tipo["arr"])>0){
	foreach($tipo["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$tipo["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Mandante:</div><select style="width:100%;" class="mandante"><option value="">Todos</option>
<? if(is_array($mandante["arr"]) && sizeof($mandante["arr"])>0){
	foreach($mandante["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$mandante["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Pa&iacute;s:</div><select style="width:100%;" class="pais"><option value="">Todos</option>
<? if(is_array($pais["arr"]) && sizeof($pais["arr"])>0){
	foreach($pais["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$pais["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Regi&oacute;n:</div><select style="width:100%;" class="region"><option value="">Todos</option>
<? if(is_array($region["arr"]) && sizeof($region["arr"])>0){
	foreach($region["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$region["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Obras Principales:</div><select style="width:100%;" class="obra"><option value="">Todos</option>
<? if(is_array($obra["arr"]) && sizeof($obra["arr"])>0){
	foreach($obra["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$obra["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Equipos Principales:</div><select style="width:100%;" class="equipo"><option value="">Todos</option>
<? if(is_array($equipo["arr"]) && sizeof($equipo["arr"])>0){
	foreach($equipo["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$equipo["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Suministros Principales:</div><select style="width:100%;" class="suministro"><option value="">Todos</option>
<? if(is_array($suministro["arr"]) && sizeof($suministro["arr"])>0){
	foreach($suministro["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$suministro["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><div style="clear:both;">Servicios Principales:</div><select style="width:100%;" class="servicio"><option value="">Todos</option>
<? if(is_array($servicio["arr"]) && sizeof($servicio["arr"])>0){
	foreach($servicio["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$servicio["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="float:left; border: 1px solid #C0C0C0; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px; padding-top:10px; padding-bottom:10px; padding-left:2%; padding-right:2%; background-color:#F4F4F4">
<div style="width:95%;" class=""><div style="clear:both;">Etapa Actual:</div><select style="width:95%;" class="etapa"><option value="">Todos</option>
<? if(is_array($etapa["arr"]) && sizeof($etapa["arr"])>0){
	foreach($etapa["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$etapa["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:95%;" class=""><div style="clear:both;">Responsable Etapa Actual:</div><select style="width:95%;" class="responsable"><option value="">Todos</option>
<? if(is_array($empresa["arr"]) && sizeof($empresa["arr"])>0){
	foreach($empresa["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$empresa["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><input type="button" class="btn_buscar_proy" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" /></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec"><? echo $id_sector;?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec2"><? echo base64_encode("listar_pro");?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="actual"><? echo $actual;?></div>
</div>
