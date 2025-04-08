<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<? if(isset($send)){
if(isset($socio)){
echo "
	<div id='form4' class='contcontacto' style='padding-top:20px; padding-bottom:20px;'>
	<div style='padding:30px; width:85%; margin:0 auto;'>
			<div style='float:left;'><img src='/sitio_portal/images/formularios/correo_enviado.png'/></div><div style='text-align:center; float:none; color:#326CA6; font-weight:bold; font-size:20px; line-height:24px;'>Estimado ".$socio->nombre_completo_socio.", agradecemos su aporte, la informaci&oacute;n ha sido enviada exitosamente. </div>
		</div>
	</div>";

}else{
echo "
	<div id='form4' class='contcontacto' style='padding-top:20px; padding-bottom:20px;'>
	<div style='padding:30px; width:85%; margin:0 auto;'>
			<div style='float:left;'><img src='/sitio_portal/images/formularios/correo_enviado.png'/></div><div style='text-align:center; float:none; color:#326CA6; font-weight:bold; font-size:20px; line-height:24px;'>Estimado, agradecemos su aporte, la informaci&oacute;n ha sido enviada exitosamente.</div>
		</div>
	</div>";
}
die();
}?>
<div style="margin:0 auto; clear:both; width:95%; padding:1%; border-radius:8px; border: 1px solid #9cbebd; -moz-border-radius:8px; -webkit-border-radius:8px; background-color:#efefef;" id="form">
	<form id="form4">
		<div style="clear:both;">&nbsp;</div>
		<table border="0" cellpadding="3" cellspacing="5" class="" width="98%" align="center">
			<tbody>
            	<tr style="">
					<td colspan="2" align="center" class="" style="border-collapse: collapse;"><div style="font-size:16px; padding-bottom:10px; font-weight:bold; margin:0 auto;float:left;" align="center">
						Agradecemos su colaboraci&oacute;n.</div>
					</td>
		    </tr>
				<?
				if(isset($socio)){
				?>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap colspan="2"><div style="font-size:16px; font-weight:bold; padding-bottom:10px;">Datos del Usuario</div></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap>Remitente:</td>
					<td class="" style="border-collapse: collapse;"><input type="text"  class="input_verde" style="width:100%;" name="remitente" id="remitente" value="<? echo $socio->nombre_completo_socio;?>" readonly="readonly" /></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap>Email:</td>
					<td class="" style="border-collapse: collapse;"><input type="text"  class="input_verde" style="width:100%;" name="email" id="email" value="<? echo $socio->email_socio;?>" readonly="readonly" /></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap>Tel&eacute;fono:</td>
					<td class="" style="border-collapse: collapse;"><input type="text"  class="input_verde" style="width:100%;" name="fono" id="fono" value="<? echo $socio->fono_user_socio;?>" readonly="readonly" /></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap colspan="2">&nbsp;</td>
				</tr>
				<?
				}
				?>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap colspan="2"><div style="font-size:16px; font-weight:bold; padding-bottom:10px;">Sugerencias</div></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" colspan="2"><textarea cols="100" rows="10" name="sugerencias" id="sugerencias" class="input_verde"  style="width:100%;"></textarea></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" colspan="2">
						<input type="reset" id="reset" value="Reiniciar" class="btn_naranjo" style="margin-left:5px;float:right;" />
						<input type="button" id="enviar" value="Enviar" class="btn_verde" style="float:right;" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>