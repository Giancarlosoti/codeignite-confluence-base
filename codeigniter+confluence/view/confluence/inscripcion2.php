<? 
if(isset($send) && $send==1){
	echo "
	<div class='contcontacto' style='padding-top:20px; padding-bottom:20px;'>
	<div style='padding:30px; width:85%; margin:0 auto;'>
			<div style='float:left;'><img src='/sitio_portal/images/formularios/correo_enviado.png'/></div><div style='text-align:center; float:none; color:#326CA6; font-weight:bold; font-size:20px; line-height:24px;'>Inscripci&oacute;n Enviada Exitosamente!<br>Pronto Lo Contactaremos, Gracias.</div>
		</div>
	</div>";
	die();
}else if(isset($send) && $send==0){
	echo "<div style='margin:0 auto; clear:both; top:150px; font-size:24px; font-weight:bold;' align='center'>Por Favor Reingrese su Correo.</div><div style='clear:both;'>&nbsp;</div><div style='clear:both;' align='center'><a href='javascript:history.go(-1);'>Volver</a></div>"; die();
}?>

<style>
.cajacontacto{
	border:1px solid #D18067;
	width:100%;
}

.contcontacto{
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	background-color:#FFF;
	margin:0 auto;
	width:100%;
}

.titlecontacto{
	background-color:#F4F4F4;
	border-width:1px;
	border-style:solid;
	border-color:#ddd;
	padding:5px 7px;vertical-align:top;
	min-width:.6em;
	text-align:left;
}

.tdcontacto{
	border-width:1px;
	border-style:solid;
	border-color:#ddd;
	padding:5px 7px;vertical-align:top;
	min-width:.6em;
	text-align:left;
}

td{
	font-size:14px;	
}

</style>
<div style="margin:0 auto; clear:both;" id="form">
	<form id="form_inscripcion2">
		<div class="contcontacto" style="padding-top:20px; padding-bottom:20px; font-size:12px;">
			<div style="clear:both; margin:0 auto;" align="center">
				<table align="center" cellpadding="3" cellspacing="0" class="" width="90%">
					<tbody>
						<tr style="">
						  <td class="titlecontacto" style="border-collapse: collapse;" align="left" colspan="4"><div style="padding-top:10px; padding-bottom:10px; font-weight:bold;">Seminario al que se Inscribir&aacute;:</div></td>
					  </tr>
						<tr style="">
							<td class="tdcontacto" style="border-collapse: collapse;" colspan="4">
                                <table align="center" cellpadding="3" cellspacing="0" class="" width="99%">
                                <tr style="">
                                    <td class="titlecontacto" style="border-collapse: collapse;" width="90%"><strong><label for="sem1"><div style="width;:100%;">Gesti&oacute;n de M&uacute;ltiples Proyectos: Implantando la Capacidad en la Empresa u Organismo</div></label></strong></td>
                                    <td colspan="3" class="tdcontacto" style="border-collapse: collapse;"><input type="checkbox" name="sem1" id="sem1" value="sem1" /></td>
                                </tr>
                                <tr style="">
                                    <td class="titlecontacto" style="border-collapse: collapse;"><strong><label for="sem2"><div style="width;:100%;">Gesti&oacute;n de Riesgos y Desarrollo de la Empresa</div></label></strong></td>
                                    <td colspan="3" class="tdcontacto" style="border-collapse: collapse;"><input type="checkbox" name="sem2" id="sem2" value="sem2" /></td>
                                </tr>
                                <tr style="">
                                    <td class="titlecontacto" style="border-collapse: collapse;"><strong><label for="sem3"><div style="width;:100%;">Contratos para Empresas Proveedoras y Contratistas</div></label></strong></td>
                                    <td colspan="3" class="tdcontacto" style="border-collapse: collapse;"><input type="checkbox" name="sem3" id="sem3" value="sem3" /></td>
                                </tr>
                                </table>
                            </td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Nombre Completo</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="nombre_completo" type="text" class="cajacontacto required" id="nombre_completo" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Rut</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="rut" type="text" class="cajacontacto required" id="rut" /></td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">E-Mail</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="email" type="text" class="cajacontacto required email" id="email" style="margin:0px;" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Profesi&oacute;n</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="profesion" type="text" class="cajacontacto required" id="profesion" /></td>
						</tr>
						<tr style="">
							<td width="15%" height="30" class="titlecontacto" style="border-collapse: collapse;"><p>Tel&eacute;fono/Celular</p></td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="telefono" type="text" class="cajacontacto required" id="telefono" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Empresa</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="empresa" type="text" class="cajacontacto required" id="empresa" /></td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Direcci&oacute;n</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="direccion" type="text" class="cajacontacto required" id="direccion" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Cargo</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="cargo" type="text" class="cajacontacto required" id="cargo" /></td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Comuna</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="comuna" type="text" class="cajacontacto required" id="comuna" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Ciudad</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="ciudad" type="text" class="cajacontacto required" id="ciudad" /></td>
						</tr>
						<tr style="">
						  <td colspan="4" class="titlecontacto" style="border-collapse: collapse;"><div style="padding-top:10px; padding-bottom:10px; font-weight:bold;">Datos Para la Facturaci&oacute;n</div></td>
					  </tr>
					  <tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Raz&oacute;n Social</td>
							<td colspan="3" class="tdcontacto" style="border-collapse: collapse;"><input name="razon_social" type="text" class="cajacontacto required" id="razon_social" /></td>
					  </tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Dirección Facturación</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="_direccion_fact" type="text" class="cajacontacto required" id="_direccion_fact" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Rut</td>
							<td width="35%" class="tdcontacto" id="rut_emp" style="border-collapse: collapse;"><input name="_rut_emp" type="text" class="cajacontacto required" id="_rut_emp" /></td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Giro</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="giro" type="text" class="cajacontacto required" id="giro" /></td>
						  <td class="titlecontacto" style="border-collapse: collapse;" width="15%">Comuna</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="_comuna_fact" type="text" class="cajacontacto required" id="_comuna_fact" /></td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Teléfonos</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="_telefonos" type="text" class="cajacontacto required" id="_telefonos" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Ciudad</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="_ciudad_fact" type="text" class="cajacontacto required" id="_ciudad_fact" /></td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Dirección Envío de Factura</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="__direccion_envio" type="text" class="cajacontacto required" id="__direccion_envio" /></td>
							<td class="titlecontacto" style="border-collapse: collapse;" width="15%">Comuna de Env&iacute;o</td>
							<td class="tdcontacto" style="border-collapse: collapse;" width="35%"><input name="__comuna_envio" type="text" class="cajacontacto required" id="__comuna_envio" /></td>
						</tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;">Encargado de Capacitación</td>
							<td colspan="3" class="tdcontacto" style="border-collapse: collapse;">
							  <input name="encargado_capacit" type="text" class="cajacontacto required" id="encargado_capacit" /></td>
						</tr>
						<tr style="">
						  <td colspan="4" class="tdcontacto" style="border-collapse: collapse;"><table width="100%" cellpadding="0">
						    <tr>
						      <td><strong>Datos de Dep&oacute;sito:</strong><br>
						        Banco de Chile<br>
						        Cuenta Corriente: 159-30855-00<br>
						        Nombre: Portal Minero Capacitaci&oacute;n y Desarrollo Ltda.<br>
						        Rut Empresa: 76.151.493-8<br>
						        <br></td>
					        </tr>
					      </table></td>
					  </tr>
						<tr style="">
							<td colspan="4" class="tdcontacto" style="border-collapse: collapse;"><strong>Nota</strong>: Enviar comprobante de Pago a inscripciones@portalminero.com, indicando el nombre, rut y el teléfono del participante.</td>
						</tr>
						<tr style="">
							<td colspan="4" class="tdcontacto" style="border-collapse: collapse;">En caso de dudas o consultas:<br>
								<a href="mailto:inscripciones@portalminero.com">inscripciones@portalminero.com</a><br>
								(56-2) 2250164 / 6-9184753 / 9-9985523
							</td>
					  </tr>
						<tr style="">
							<td class="titlecontacto" style="border-collapse: collapse;" colspan="4">
								<div align="center" style="font-weight:bold; font-size:16px">PLAZO M&Aacute;XIMO PARA LAS INSCRIPCIONES: 13 DE AGOSTO</div></td>
						</tr>
						<tr style="">
						  <td class="tdcontacto" style="border-collapse: collapse;" colspan="4"><div style="float:left;"><input type="checkbox" value="1" name="webpay" id="webpay" /><label for="webpay">Pago Seguro con WebPay</label></div><div style="float:right;"><input type="button" id="enviar_inscripcion2" value="Enviar" style="float:right;" />
								<input type="reset" id="reset" value="Reiniciar" style="float:right;" /></div></td>
					  </tr>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>