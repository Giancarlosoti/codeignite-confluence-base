<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<div style="clear:both">&nbsp;</div>
<div>
<div style="color:#24587F; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">
	Adjudicaciones <? echo $nombre_sector;?>
</div>
<div style="float:right; width:40%;" id="div_busqueda_nombre">
	Buscar Por Nombre de Adjudicaci&oacute;n: <? echo form_input($nombre);?>
	<input type="button" style="margin-top:0.4rem;" class="btn_buscar_nombre btn_verde" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" />
</div>
</div>
<div style="clear:both">&nbsp;</div>
<div class="columna_listado_pro">
<? if(isset($adjudicaciones) && is_array($adjudicaciones) && sizeof($adjudicaciones)>0){?>
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
					Nombre <br />
					<img src="/sitio_portal/images/<? if ($ordernombre == "ASC"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; } ?>" />
				</a>
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
				<a href="javascript:pagina(<?echo $actual;?>, 0, 'orderempresa_<? echo $orderempresa?>')" class="order" style="color:#066293">
					Empresa Adjudicada <br />
					<img src="/sitio_portal/images/<? if ($orderempresa == "ASC"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; } ?>" />
				</a>
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
				<a href="javascript:pagina(<?echo $actual;?>, 0, 'ordercomprador_<? echo $ordercomprador?>')" class="order" style="color:#066293">
					Comprador <br />
					<img src="/sitio_portal/images/<? if ($ordercomprador == "ASC"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; } ?>" />
				</a>
			</td>
			<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293" align="center" width="100%">
				<a href="javascript:pagina(<?echo $actual;?>, 0, 'orderfecha_<? echo $orderfecha?>')" class="order" style="color:#066293">
					Fecha Adjudicaci&oacute;n <br />
					<img src="/sitio_portal/images/<? if ($orderfecha == "ASC"){ echo "arrowup.png"; }else{ echo "arrowdown.png"; } ?>" />
				</a>
			</td>
			<!--<td nowrap class="titulo">Fecha Actualizaci&oacute;n</td>-->
		</tr>
	</thead>
	<tbody>
		<? foreach($adjudicaciones as $adj){
			if(strstr($adj->url_confluence_adj, "pageId")){
				$t1="&test=1";
			}else{
				$t1="?test=1";
			}
			
			if($adj->Nombre_pro!="" && $adj->Nombre_pro!=NULL)
				$nombre=$adj->nombre_adj." (".$adj->Nombre_pro.")";
			else
				$nombre=$adj->nombre_adj;
		?>
		<tr class="tr_hover">
			<td class="confluenceTd" width="100%"><a target="_top" href="<? echo $adj->url_confluence_adj;?>"><?=$nombre;?></a> <a rel="shadowbox;width=1100;" href="<? echo $adj->url_confluence_adj.$t1;?>" style="font-size:10px;color:#999;">(Ver En Pop-Up)</a></td>
			<td class="confluenceTd" width="100%"><? echo str_replace("/", " / ",$adj->emp_adj);?></td>
			<td class="confluenceTd" width="100%"><? echo str_replace("/", " / ",$adj->emp_comp);?></td>
			<td class="confluenceTd" width="100%"><? echo $adj->trim_fecha_adj;?>&deg; Trimestre <? echo $adj->ano_fecha_adj;?></td>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Empresa Adjudicada:</div><select style="width:123px;" id="filtro_empadj" class="empadj input_radius"><option value="">Todos</option>
<? if(is_array($empadj["arr"]) && sizeof($empadj["arr"])>0){
	foreach($empadj["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$empadj["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">V&iacute;a:</div><select style="width:123px;" id="filtro_via" class="via input_radius"><option value="">Todos</option><option value="0" <? if(strval($via["var"])=="0"){?> selected<?}?>>V&iacute;a No Informada</option>
<?
if(is_array($via["arr"]) && sizeof($via["arr"])>0){
	$keys=array_keys($via["arr"]);
	$x=0;
	foreach($via["arr"] as $value){
		?>
		<option value="<? echo $keys[$x];?>" <? if(strval($keys[$x])==strval($via["var"])){?> selected="selected" <?}?>><? echo $value;?></option>
		<?
		++$x;
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Comprador:</div>
	<select style="width:123px;" id="filtro_comprador" class="comprador input_radius"><option value="">Todos</option>
	<? if(is_array($comprador["arr"]) && sizeof($comprador["arr"])>0){
		foreach($comprador["arr"] as $key=>$value){
			if($key!="" && $key!="0"){
				?>
				<option value="<? echo $key;?>" <? if($key==$comprador["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
				<?
			}
		}
	}?>
	</select>
</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box">
  <div style="clear:both;">Equipos:</div><select style="width:123px;" id="filtro_equipo" class="equipo input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class="select-box">
  <div style="clear:both;">Suministros:</div><select style="width:123px;" id="filtro_suministro" class="suministro input_radius"><option value="">Todos</option>
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
<div style="clear:both; background-color:#F0F0F0; padding:5px; display:inline-block;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
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
</div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both; background-color:#F0F0F0; padding:5px; display:inline-block;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
<div style="width:100%;" class="select-box"><div style="clear:both;">Tipo Servicio:</div><select style="width:123px;" id="filtro_catservicio" class="catservicio input_radius"><option value="">Todos</option>
<? if(is_array($catservicio["arr"]) && sizeof($catservicio["arr"])>0){
	foreach($catservicio["arr"] as $key=>$value){
		if($key!="" && $key!="0"){
			?>
			<option value="<? echo $key;?>" <? if($key==$catservicio["var"]){?> selected="selected" <?}?>><? echo $value;?></option>
			<?
		}
	}
}?>
</select></div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class="select-box"><div style="clear:both;">Servicios:</div><select style="width:123px;" id="filtro_subcatservicio" class="subcatservicio input_radius"><option value="">Todos</option>
<? if(is_array($subcatservicio["arr"]) && sizeof($subcatservicio["arr"])>0){
	$keys=array_keys($subcatservicio["arr"]);
	$x=0;
	foreach($subcatservicio["arr"] as $value){
		?>
		<option value="<? echo $keys[$x];?>" <? if(strval($keys[$x])==strval($subcatservicio["var"])){?> selected="selected" <?}?>><? echo $value;?></option>
		<?
		++$x;
	}
}?>
</select></div>
</div>
<div style="clear:both;">&nbsp;</div>
<div style="width:100%;" class=""><input type="button" class="btn_buscar_proy btn_azul" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" /></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec"><? echo $id_sector;?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec2"><? echo base64_encode("listar_adj");?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="actual"><? echo $actual;?></div>
</div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both; background-color:#F0F0F0; padding:10px;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
<div style="clear:both; font-weight:bold; text-decoration:underline;color:#107883;">Buscar Adjudicaciones Relacionadas con:</div>
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
<div style="width:100%;" class="select-box"><div style="clear:both;">Tipos de Proyecto:</div><select style="width:110px;" id="filtro_tipo" class="tipo input_radius"><option value="">Todos</option>
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
<div style="width:100%;" class=""><input type="button" class="btn_buscar_proy btn_verde" value="Buscar" onClick="javascript:pagina(<?echo $actual;?>, 0)" /></div>
</div>
</div>
