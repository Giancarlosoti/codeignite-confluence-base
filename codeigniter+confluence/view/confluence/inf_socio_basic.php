<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Información Socio</title>
</head>
<body>
<div style="width:100%;">   
    <div style="display:inline-block;width:100%;border:#109aa5 2px solid;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;">
        <div style=" float:left; width:245px; padding:0px 5px">
            <div>
                <div style="width:100%; float:left; padding:1px 0; font-weight:bold; font-size:0.8rem; color:#109aa5">Datos Usuario:</div>
            </div>
            <div>
                <div style="width:30%; float:left; padding:1px 0; font-size:0.7rem"><b>Nombre :</b></div>
                <div style="width:70%; float:left; padding:1px 0; font-size:0.7rem"><?=$nombre_usuario?>&nbsp;</div>
            </div>
            
            <div>
                <div style="width:30%; float:left; padding:1px 0; font-size:0.7rem"><b>RUT o ID:</b></div>
                <div style="width:70%; float:left; padding:1px 0; font-size:0.7rem"><?=$rut_id_socio?>&nbsp;</div>
            </div>
            <div>
                <div style="width:30%; float:left; padding:1px 0; font-size:0.7rem"><b>Empresa :</b></div>
                <div style="width:70%; float:left; padding:1px 0; font-size:0.7rem"><?=$nombre_socio?>&nbsp;</div>
            </div>
            
        </div>
        <div style="float:left; width:245px; padding:0px 5px">
            <div>
            <div style="width:100%; float:left; padding:1px 0; font-weight:bold; font-size:0.8rem; color:#109aa5">Datos de Ejecutivo:</div>
            </div>
            <div>
                <div style="width:30%; float:left; padding:1px 0; font-size:0.7rem"><b>Nombre :</b></div>
                <div style="width:70%; float:left; padding:1px 0; font-size:0.7rem"><?=$nombre_vendedor?>&nbsp;</div>
            </div>
            <div>
                <div style="width:30%; float:left; padding:1px 0; font-size:0.7rem"><b>Email :</b></div>
                <div style="width:70%; float:left; padding:1px 0; font-size:0.7rem"><?=$email_vendedor?>&nbsp;</div>
            </div>
            <div>
                <div style="width:30%; float:left; padding:1px 0; font-size:0.7rem"><b>Telefono :</b></div>
                <div style="width:70%; float:left; padding:1px 0; font-size:0.7rem"><?=$fono_vendedor?>&nbsp;</div>
            </div>
        </div>
        <div style="float:left; width:240px; padding:0px 0px">
        	<div>
            <div style="width:100%; float:left; padding:1px 0; font-weight:bold; font-size:0.8rem; color:#109aa5">Membresía:</div>
            </div>
            <div>
                <div style="width:35%; float:left; padding:1px 0; font-size:0.7rem"><b>Tipo :</b></div>
                <div style="width:65%; float:left; padding:1px 0; font-size:0.7rem"><?=$membresia?>
                <?
					if(($membresia!='Premium')&&($membresia!='Mandante')){
						?>
                        <div style=" float:right; padding:0px 5px; font-size:0.75rem;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;" class="btn_naranjo"><a href="/pages/viewpage.action?pageId=7014135" style="text-decoration:none; color:#FFF">Pasar a Premium</a></div></div>
                        <?
                	}
                ?>
			</div>
            <div>
                <div style="width:35%; float:left; padding:1px 0; font-size:0.7rem"><b>Inicio:</b></div>
                <div style="width:65%; float:left; padding:1px 0; font-size:0.7rem"><?=$fecha_inicio?></div>
            </div>
            <div>
                <div style="width:35%; padding:1px 0; font-size:0.7rem"><b>Renovación :</b></div>
                <div style="width:65%; float:left; padding:1px 0; font-size:0.7rem"><?=$fecha_renovacion?></div>
            </div>
             
        </div>
    </div>
     
</div>
</body>
</html>