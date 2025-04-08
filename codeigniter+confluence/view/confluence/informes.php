<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<? if(isset($send)){
if(isset($socio)){
echo "
    <div class='contcontacto' style='padding-top:20px; padding-bottom:20px;'>
    <div style='padding:30px; width:85%; margin:0 auto;'>
            <div style='float:left;'><img src='/sitio_portal/images/formularios/correo_enviado.png'/></div><div style='text-align:center; float:none; color:#326CA6; font-weight:bold; font-size:20px; line-height:24px;'>Estimado ".$socio->nombre_completo_socio.", su solicitud ha sido enviada exitosamente.</div>
        </div>
    </div>";
die();
}else{
echo "
    <div class='contcontacto' style='padding-top:20px; padding-bottom:20px;'>
    <div style='padding:30px; width:85%; margin:0 auto;'>
            <div style='float:left;'><img src='/sitio_portal/images/formularios/correo_enviado.png'/></div><div style='text-align:center; float:none; color:#326CA6; font-weight:bold; font-size:20px; line-height:24px;'>Estimado, su solicitud ha sido enviada exitosamente.</div>
        </div>
    </div>";
die();
}
}?>
<div style="margin:0 auto; clear:both; width:95%; padding:1%; border-radius:8px; border: 1px solid #066293; -moz-border-radius:8px; -webkit-border-radius:8px; background-color:#F4F4F4" id="form">
<form id="form1">
<div style="float:left; clear:both;">
El primer paso para solicitar un informe a pedido es completar el siguiente formulario. A partir de esta informaci&oacute;n preliminar, una ejecutiva se contactar&aacute; con usted para seguir el procedimiento correspondiente.
</div>
<div style="clear:both;">&nbsp;</div>
<table align="center" border="0" cellpadding="3" cellspacing="5" class="" width="80%">
    <tbody>
        <?
        if(isset($socio)){
        ?>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap colspan="2"><div style="font-size:16px; font-weight:bold; padding-bottom:10px; color:#109aa5;">Informaci&oacute;n de Socio</div></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>Remitente:</td>
            <td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="remitente" id="remitente" value="<? echo $socio->nombre_completo_socio;?>" readonly="readonly" class="input_verde" /></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>Email:</td>
            <td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="email" id="email" value="<? echo $socio->email_socio;?>" readonly="readonly" class="input_verde" /></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>Tel&eacute;fono:</td>
            <td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="fono" id="fono" value="<? echo $socio->fono_user_socio;?>" readonly="readonly" class="input_verde"/></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap colspan="2">&nbsp;</td>
        </tr>
        <?
        }
        ?>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap colspan="2"><div style="font-size:16px; font-weight:bold; padding-bottom:10px; color:#066293">Requerimiento</div></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>(*) Nombre Investigaci&oacute;n:</td>
            <td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="requerimiento" id="requerimiento" class="input_azul"/></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>Objetivo de la Investigaci&oacute;n:</td>
            <td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="objetivo" id="objetivo"  class="input_azul"/></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>Alcance del Estudio:</td>
            <td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="alcance" id="alcance"  class="input_azul"/></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>Plazos:</td>
            <td class="" style="border-collapse: collapse;"><input type="text" style="width:100%;" name="plazos" id="plazos"  class="input_azul"/></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" nowrap>Comentarios:</td>
            <td class="" style="border-collapse: collapse;"><textarea cols="50" rows="10" name="comentarios" id="comentarios" class="input_azul"></textarea></td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" colspan="2">
                <input type="reset" id="reset" value="Reiniciar" style="float:right; margin-left:5px;" class="btn_naranjo" />
                <input type="button" id="enviar" value="Enviar" style="float:right;" class="btn_verde" />
            </td>
        </tr>
        <tr style="">
            <td class="" style="border-collapse: collapse;" colspan="2" style="font-size:10px;">
                <strong style="color:#f67e62;">NOTA: (*)</strong> Campos Obligatorios
            </td>
        </tr>
    </tbody>
</table>
</form>
</div>