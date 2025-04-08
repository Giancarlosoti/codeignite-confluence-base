<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Información Socio</title>
</head>
<body>
<div style="width:100%;">   
    <div style="display:inline-block;width:100%">
        <div style="border:#066293 1px solid; float:left; width:49%;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;">
            <div style="background-color:#066293; color:#fff">
                <div style="padding:5px; font-weight:bold; font-size:16px" align="center">Datos de Usuario</div>
            </div>
            <div>
                <div style="width:23%; float:left; padding:2px 5px; font-weight:bold; font-size:0.8rem">Nombre :</div>
                <div style="width:68%; float:left; padding:2px 5px; font-size:0.75rem"><?=$nombre_usuario?>&nbsp;</div>
            </div>
            <div>
                <div style="width:23%; float:left; padding:2px 5px; font-weight:bold; font-size:0.8rem">RUT o ID :</div>
                <div style="width:68%; float:left; padding:2px 5px; font-size:0.75rem"><?=$rut_id_socio?>&nbsp;</div>
            </div>
            <div>
                <div style="width:23%; float:left; padding:2px 5px; font-weight:bold; font-size:0.8rem">Email :</div>
                <div style="width:68%; float:left; padding:2px 5px; font-size:0.75rem"><?=$email_usuario?>&nbsp;</div>
            </div>
            <div>
                <div style="width:23%; float:left; padding:2px 5px; font-weight:bold; font-size:0.8rem">Telefono :</div>
                <div style="width:68%; float:left; padding:2px 5px; font-size:0.75rem"><?=$fono_usuario?>&nbsp;</div>
            </div>
        </div>
        <div style="border:#f67e62 1px solid; float:right; width:49%;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;">
            <div style="background-color:#f67e62; color:#fff">
                <div style="padding:5px; font-weight:bold; font-size:16px" align="center">Datos de Ejecutivo</div>
            </div>
            <div>
                <div style="width:23%; float:left; padding:6px 5px 5px 5px; font-weight:bold; font-size:0.8rem">Nombre :</div>
                <div style="width:68%; float:left; padding:6px 5px 5px 5px; font-size:0.75rem"><?=$nombre_vendedor?></div>
            </div>
            <div>
                <div style="width:23%; float:left; padding:6px 5px; font-weight:bold; font-size:0.8rem">Email :</div>
                <div style="width:68%; float:left; padding:6px 5px; font-size:0.75rem"><?=$email_vendedor?></div>
            </div>
            <div>
                <div style="width:23%; float:left; padding:5px 5px; font-weight:bold; font-size:0.8rem">Telefono :</div>
                <div style="width:68%; float:left; padding:5px 5px; font-size:0.75rem"><?=$fono_vendedor?></div>
            </div>
        </div>
    </div>
    <div style="margin:10px 0px; border:#066293 1px solid;-moz-border-radius:5px;-webkit-border-radius:5px;	border-radius:5px;">
    	<div>
    		<div align="center" style="font-size:16px; font-weight:bold;background-color:#066293; padding:4px 0px; color:#fff; width:100%">Datos Empresa..</div>
        </div>
    	<div style="display:inline-block;">
        	<div style="width:50%; float:left">
            	<div style="width:30%; float:left; padding:5px; font-weight:bold; font-size:0.8rem">Nombre :</div>
            	<div style="width:60%; float:left; padding:5px; font-size:0.75rem"><?=$nombre_socio?></div>
            </div>
            
            
            <div style="width:50%; float:left">
            	<div style="width:40%; float:left; padding:5px; font-weight:bold; font-size:0.8rem">P. de Contacto :</div>
            	<div style="width:51%; float:left; padding:5px; font-size:0.75rem"><?=$nombre_contacto_socio?></div>
            </div>
            <div style="width:50%; float:left">
            	<div style="width:30%; float:left; padding:5px; font-weight:bold; font-size:0.8rem">Membresía :</div>
            	<div style="width:60%; float:left; padding:5px; font-size:0.75rem"><?=$membresia?></div>
            </div>
            <div style="width:50%; float:left">
            	<div style="width:40%; float:left; padding:5px; font-weight:bold; font-size:0.8rem">Teléfono :</div>
            	<div style="width:51%; float:left; padding:5px; font-size:0.75rem"><?=$fono_contacto_socio?></div>
            </div>
            
        </div>
        
      </div> 
      <div align="center" style="margin:10px 0px">   
          <div style="width:99%; border:#066293 1px solid;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;">
               <div style=" background-color:#066293;color:#fff">
                    <div style="padding:5px; font-weight:bold; font-size:16px;" align="center">Otros Usuarios de su Empresa</div>
               </div>
               <div align="left" style="height:100px; overflow:auto;">
                   <?=$usuarios?>
               </div>
          </div>
        </div>
        <div align="center" style="margin:20px 0px">
             <div style="width:90%;background-color:#f67e62;color:#FFF; display:inline-block;-moz-border-radius:5px;-webkit-border-radius:5px;	border-radius:5px;">
                 <div style="width:50%; float:left">
                       <div style="width:53%; float:left; padding:5px; font-weight:bold; font-size:0.8rem">Fecha de Ingreso :</div>
                       <div style="width:30%; float:left; padding:5px; font-size:0.75rem"><?=$fecha_inicio?></div>
                 </div>
                 <div style="width:50%; float:left">
                       <div style="width:60%; float:left; padding:5px; font-weight:bold; font-size:0.8rem">Fecha de Renovación:</div>
                       <div style="width:30%; float:left; padding:5px; font-size:0.75rem"><?=$fecha_renovacion?></div>
                 </div>
             </div>
        </div>
     
</div>
</body>
</html>