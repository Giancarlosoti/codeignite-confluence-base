<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
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
				<div align="center">&nbsp;</div>
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
			<td class="confluenceTd"><?=$proy->Nombre_generico_pro;?></td>
			<td class="confluenceTd"><?=$proy->Nombre_pais;?></td>
			<td class="confluenceTd"><?=$proy->Nombre_region;?></td>
			<td class="confluenceTd"><a target="_top" href="<? echo $proy->url_confluence_pro;?>">Ver Detalles</a></td>
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
<div style="clear:both;">&nbsp;</div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec"><? echo $id_sector;?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec2"><? echo base64_encode("listar_pro_pub");?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="actual"><? echo $actual;?></div>
