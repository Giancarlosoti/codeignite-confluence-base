<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<div style="clear:both">&nbsp;</div>
<div>
	<div style="color:#24587F; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">
		Licitaciones <? echo $nombre_sector;?>
	</div>
	<div style="float:right; width:40%;" id="div_busqueda_nombre">
		Buscar Por Nombre de Licitaci&oacute;n: <? echo form_input($nombre);?>
		<input type="button" style="margin-top:0.4rem;" class="btn_buscar_nombre btn_verde" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" />
	</div>
</div>
<div style="clear:both;padding-bottom:10px;"><a href="/pages/viewpage.action?pageId=54001750" style="color:#109aa5;">Seleccione sus rubros para recibir en su correo</a></div>
<div class="columna_listado_pro">
<? if(isset($licitaciones) && is_array($licitaciones) && sizeof($licitaciones)>0){?>
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
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
				<a href="javascript:pagina(<?echo $actual;?>, 0, 'ordernombre_<? echo $ordernombre?>')" class="order" style="color:#066293">
					Nombre<br/>
					<img src="/sitio_portal/images/<? if($ordernombre=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; }?>" />
				</a>
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
				<a href="javascript:pagina(<?echo $actual;?>, 0, 'ordersector_<? echo $ordersector?>')" class="order" style="color:#066293">
					Sector<br/>
					<img src="/sitio_portal/images/<? if($ordersector=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; }?>" />
				</a>
			</td>
			<? if ($tipo_lici["var"] != 1 || $tipo_lici["var"] != 2){ ?>
				<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
					<a href="javascript:pagina(<?echo $actual;?>, 0, 'orderestado_<? echo $orderestado?>')" class="order" style="color:#066293">
						Estado Licitaci&oacute;n<br/>
						<img src="/sitio_portal/images/<? if($orderestado=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; }?>" />
					</a>
				</td>
			<? } ?>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
				<a href="javascript:pagina(<?echo $actual;?>, 0, 'orderpais_<? echo $orderpais?>')" class="order" style="color:#066293">
					Pa&iacute;s<br/>
					<img src="/sitio_portal/images/<? if($orderpais=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; }?>" />
				</a>
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
				<a href="javascript:pagina(<?echo $actual;?>, 0, 'orderregion_<? echo $orderregion?>')" class="order" style="color:#066293">
					Regi&oacute;n<br/>
					<img src="/sitio_portal/images/<? if($orderregion=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; }?>" />
				</a>
			</td>
			<? if ($tipo_lici["var"] == 1){ ?>
				<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="100%">
					<a href="javascript:pagina(<?echo $actual;?>, 0, 'orderfet_<? echo $orderfet?>')" class="order" style="color:#4b7b85">
						Fecha Estimada Trimestre<br/>
						<img src="/sitio_portal/images/<? if($orderfet=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; }?>" />
					</a>
				</td>
			<? }elseif ($tipo_lici["var"] == 2){ ?>
				<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="100%">
					<a href="javascript:pagina(<?echo $actual;?>, 0, 'orderflcb_<? echo $orderflcb?>')" class="order" style="color:#4b7b85">
						Fecha L&iacute;mite Compra Bases<br/>
						<img src="/sitio_portal/images/<? if($orderflcb=="asc"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; }?>" />
					</a>
				</td>
			<? } ?>
			<!--<td nowrap class="titulo">Fecha Actualizaci&oacute;n</td>-->
		</tr>
	</thead>
	<tbody>
		<? foreach($licitaciones as $lici){
			if(strstr($lici->url_confluence_lici, "pageId")){
				$t1="&test=1";
			}else{
				$t1="?test=1";
			}
		?>
		<tr class="tr_hover">
			<td class="confluenceTd" width="100%"><a target="_top" href="<? echo $lici->url_confluence_lici;?>"><?=$lici->Nombre_lici_completo;?></a> <a rel="shadowbox;width=1100;" href="<? echo $lici->url_confluence_lici.$t1;?>" style="font-size:10px;color:#999;display:inline-block">(Ver En Pop-Up)</a></td>
			<td class="confluenceTd" width="100%"><?=$lici->Nombre_sector;?></td>
			<? if ($tipo_lici["var"] != 1 || $tipo_lici["var"] != 2){ ?>
				<td class="confluenceTd" width="100%"><?=$lici->Nombre_lici_tipo;?></td>
			<? } ?>
			<td class="confluenceTd" width="100%"><?=$lici->Nombre_pais;?></td>
			<td class="confluenceTd" width="100%"><?=$lici->Nombre_region;?></td>
			<? if ($tipo_lici["var"] == 1){ ?>
				<td class="confluenceTd" width="100%"><? echo $lici->Compra_base_lici_estimada_trim;?>&deg; Trimestre <? echo $lici->Compra_base_lici_estimada_anno;?></td>
			<? }elseif ($tipo_lici["var"] == 2){ ?>
				<td class="confluenceTd" width="100%"><? if ($lici->Compra_base_lici_fin != "0000-00-00"){ echo implode('-', array_reverse( explode('-', $lici->Compra_base_lici_fin))); } ?></td>
			<? } ?>
			<!--<td class="confluenceTd"><?=(($proy->Fecha_actualizacion_pro != "" && $proy->Fecha_actualizacion_pro!=NULL) ? $proy->Fecha_actualizacion_pro : "");?></td>-->
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
<div class="columna_filtros_pro">
<div style="clear:both;">
<div style="clear:both; font-weight:bold; text-decoration:underline;color:#044073;">Filtros</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Sector:</div><select style="width:123px;" id="filtro_sector" class="sector input_radius"><option value="">Todos</option>
<? if(is_array($sector["arr"]) && sizeof($sector["arr"])>0){
	foreach($sector["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$sector["var"]){ ?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Empresa que Licita:</div><select style="width:123px;" id="filtro_mandante" class="mandante input_radius"><option value="">Todos</option>
<? if(is_array($mandante["arr"]) && sizeof($mandante["arr"])>0){
	echo $mandante["var"];
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Pa&iacute;s:</div><select style="width:123px;" id="filtro_pais" class="pais input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Regi&oacute;n:</div><select style="width:123px;" id="filtro_region" class="region input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Registros Requeridos:</div>
	<select style="width:123px;" id="filtro_rev_prov" class="reg_prov input_radius"><option value="">Todos</option>
	<? if(is_array($reg_prov["arr"]) && sizeof($reg_prov["arr"])>0){
		foreach($reg_prov["arr"] as $key=>$value){
			if($key!="" && $key!="0"){
				?>
				<option value="<? echo $key;?>" <? if($key==$reg_prov["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
				<?
			}
		}
	}?>
	</select>
</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Estado Licitaci&oacute;n:</div>
	<select style="width:123px;" id="filtro_tipo_lici" class="tipo_lici input_radius"><option value="">Todos</option>
	<? if(is_array($tipo_lici["arr"]) && sizeof($tipo_lici["arr"])>0){
		foreach($tipo_lici["arr"] as $key=>$value){
			if($key!="" && $key!="0"){
				?>
				<option value="<? echo $key;?>" <? if($key==$tipo_lici["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
				<?
			}
		}
	}?>
	</select>
</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><input type="button" class="btn_buscar_proy btn_azul" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" /></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec"><? echo $id_sector;?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec2"><? echo base64_encode("listar_lic");?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="actual"><? echo $actual;?></div>
</div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both; background-color:#F0F0F0; padding:10px;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
<div style="clear:both; font-weight:bold; text-decoration:underline;color:#107883;">Buscar Licitaciones Relacionadas con:</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Obras Principales:</div><select style="width:110px;" id="filtro_obra" class="obra input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Equipos Principales:</div><select style="width:110px;" id="filtro_equipo" class="equipo input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Suministros Principales:</div><select style="width:110px;" id="filtro_suministro" class="suministro input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Servicios Principales:</div><select style="width:110px;" id="filtro_servicio" class="servicio input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Tipo de Proyectos:</div><select style="width:110px;" id="filtro_tipo" class="tipo input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Rubros Principales:</div><select style="width:110px;" id="filtro_rubro" class="rubro input_radius"><option value="">Todos</option>
<? if(is_array($rubro["arr"]) && sizeof($rubro["arr"])>0){
	foreach($rubro["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$rubro["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><input type="button" class="btn_buscar_proy btn_verde" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" /></div>
</div>
</div>
