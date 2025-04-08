<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<form action="" method="post" id="enviar1">
<div style="clear:both;">
  <div style="color:#066293; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both;  width:90%;">Notificaci&oacute;n de Proyectos Nuevos</div></div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both;">
  <div style="color:#066293; font-size:12px; float:left; line-height:20px; clear:both;  width:90%;">Inscriba los Sectores de su Inter&eacute;s</div></div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both;">&nbsp;</div>
<div style="margin:0px auto;width:100%; display:inline-block;">
<div style="float:left;width:48%;border:1px solid #066293; border-radius: 5px; background-color:#fff" class="column_1 sel_left">
	<div align="center" style="background-color:#066293; color:#fff;padding: 5px; width: 40%;border-bottom-right-radius: 5px;">Sectores Disponibles</div>
	<div style="height:140px;padding:20px;background-color:#fff"><?=$lista_sectores?></div>
</div>
<div style="float:right;width:48%;border:1px solid #00bfa5; border-radius: 5px; background-color:#fff" class="column_1 sel_right">
	<div align="center" style="background-color: #00bfa5; color: #fff;padding: 5px; width: 40%;border-bottom-right-radius: 5px;">Sectores Seleccionados</div>
	<div style="height:140px;padding:20px;background-color:#fff"><?=$sectores_select?></div>
</div>
</div>
<div align="center" style="padding-top:15px"><input name="Guardar" type="button" value="Inscribir" class="guardar_sector btn_verde" /></div>
<div style="clear:both;">&nbsp;</div>
<div style="clear:both;">
  <div style="color:#000; font-size:12px; float:left; line-height:20px; clear:both;  width:90%;"><span style="font-weight:bold; color:#f67e62;">Nota:</span><span> Para eliminar un sector ya inscrito solo elimine la selecci&oacute;n en sectores disponibles y presione el bot&oacute;n "<b>Inscribir</b>".</span></div></div>
</form>