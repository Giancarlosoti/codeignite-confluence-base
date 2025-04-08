<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<div style="clear:both">&nbsp;</div>
<div style="width:98%; float:left; padding-right:1%; padding-left:1%;">
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
			<td class=" titulo">
				Nombre
			</td>
			<td class="titulo">
				Pa&iacute;s
			</td>
			<td class="titulo">
				&nbsp;
			</td>
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
			<td class="confluenceTd"><?=$lici->Nombre_lici;?></td>
			<td class="confluenceTd"><?=$lici->Nombre_pais;?></td>
			<td class="confluenceTd"><a href="<? echo $lici->url_confluence_lici;?>">Ver Detalles</a></td>
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
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec">0</div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="sec2"><? echo base64_encode("listar_lic_pub");?></div>
<div style="weidth:100%; clear:both; margin:0 auto; display:none;" align="center" class="actual"><? echo $actual;?></div>