<?php
/****************** CONFIGURAR AQUI *******************/
$url = "http://www.portalminero.com/display/libr/";
/****************** FIN CONFIGURACION *****************/
$TBK_ORDEN_COMPRA = $_POST["TBK_ORDEN_COMPRA"];
?>
<body style="">
	<table align="center">
		<tr>
			<td>
				<div style="width:100%; font-weight:bold; text-align:left;" align="center">
					<div style="float:left; margin:0 auto;">
						Transacci&oacute;n Fracasada
						<br/>
						<br/>
						Las posibles causas de este rechazo son: 
						<br/>
						- Error en el ingreso de los datos de su tarjeta de crédito (fecha y/o código de seguridad). 
						<br/>
						- Su tarjeta de crédito no cuenta con el cupo necesario para cancelar la compra. 
						<br/>
						- Tarjeta aún no habilitada en el sistema financiero.  
						<br/>
						- Si el problema persiste favor comunicarse con su Banco emisor.” 
						<br/>
						N&uacute;mero de Orden de Compra = <?PHP ECHO $TBK_ORDEN_COMPRA; ?>
						<br/>
						<br/>
						<a href="javascript:history.go(-1);">VOLVER</a>
					</div>
				</div>
			</td>
		</tr>
	</table>
</body>