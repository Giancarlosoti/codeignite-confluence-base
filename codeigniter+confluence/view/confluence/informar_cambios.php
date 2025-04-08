<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<? if(isset($send)){
if(isset($socio)){
echo "
	<div id='form3' class='contcontacto' style='padding-top:20px; padding-bottom:20px;'>
	<div style='padding:30px; width:85%; margin:0 auto;'>
			<div style='float:left;'><img src='/sitio_portal/images/formularios/correo_enviado.png'/></div><div style='text-align:center; float:none; color:#326CA6; font-weight:bold; font-size:20px; line-height:24px;'>Estimado ".$socio->nombre_completo_socio.", agradecemos su aporte, la informaci&oacute;n ha sido enviada exitosamente. </div>
		</div>
	</div>";

}else{
echo "
	<div id='form3' class='contcontacto' style='padding-top:20px; padding-bottom:20px;'>
	<div style='padding:30px; width:85%; margin:0 auto;'>
			<div style='float:left;'><img src='/sitio_portal/images/formularios/correo_enviado.png'/></div><div style='text-align:center; float:none; color:#326CA6; font-weight:bold; font-size:20px; line-height:24px;'>Estimado, agradecemos su aporte, la informaci&oacute;n ha sido enviada exitosamente.</div>
		</div>
	</div>";
}
echo "<div style='clear:both;'>&nbsp;</div>";
echo "<div style='clear:both;' class=''><a href='#' class='cerrarpopup'>CERRAR</a></div>";
die();
}?>
<div style="margin:0 auto; clear:both; width:95%; padding:1%; border-radius:8px; border: 1px solid #C0C0C0; -moz-border-radius:8px; -webkit-border-radius:8px; background-color:#F4F4F4" id="form">
	<form id="form3">
		<div style="clear:both;">&nbsp;</div>
		<table border="0" cellpadding="3" cellspacing="5" class="" width="100%" align="center">
			<tbody>
            	<tr style="">
					<td colspan="2" align="center" class="" style="border-collapse: collapse;"><div style="font-size:16px; padding-bottom:10px; font-weight:bold; margin:0 auto;float:left;" align="center">Si usted tiene otros antecedentes que no estén en esta ficha de proyecto, agradeceríamos los incluya en este formulario.<br /><br />
					  Gracias por su colaboración.</div></td>
		    </tr>
				<?
				if(isset($socio)){
				?>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap colspan="2"><div style="font-size:16px; font-weight:bold; padding-bottom:10px;">Datos del Usuario</div></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap>Remitente:</td>
					<td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="remitente" id="remitente" value="<? echo $socio->nombre_completo_socio;?>" readonly="readonly" /></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap>Email:</td>
					<td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="email" id="email" value="<? echo $socio->email_socio;?>" readonly="readonly" /></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap>Tel&eacute;fono:</td>
					<td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="fono" id="fono" value="<? echo $socio->fono_user_socio;?>" readonly="readonly" /></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap colspan="2">&nbsp;</td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap>Proyecto:</td>
					<td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="proyecto" id="proyecto" value="<? echo $proyecto->Nombre_pro;?>" readonly="readonly" /></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap colspan="2">&nbsp;</td>
				</tr>
				<?
				}
				?>
				<tr style="">
					<td class="" style="border-collapse: collapse;" nowrap colspan="2"><div style="font-size:16px; font-weight:bold; padding-bottom:10px;">Informar Cambios</div></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" colspan="2"><textarea cols="100" rows="10" name="cambios" id="cambios" style="width:100%;"></textarea></td>
				</tr>
				<tr style="">
					<td class="" style="border-collapse: collapse;" colspan="2">
						<input type="reset" id="reset" value="Reiniciar" style="float:right;" />
						<input type="button" id="enviar" value="Enviar" style="float:right;" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>