<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


</head>
<body>


<div class="column_1">
    	<div class="espacio_personal">
        	<div class="titulo"><div class="menu_socio-mi_comunidad titulo_icono"></div><div class="titulo_texto">Mi Comunidad</div></div>
            <div class="contenido">
            	<ul>
                	<li><div class="icono"><svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                          >
                            <path d="M10 18l6 -6l-6 -6v12" />
                          </svg></div><div class="link"><a href="<?=$url?>display/acce/">Muro Actividades</a></div></li>
                    <hr/>
                	<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>users/viewmyprofile.action">Espacio Personal</a></div></li>
                	<?
					if(($menu_premium!='Mandante')&&($menu_premium!='Directorio')){
					?>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>display/SPM">Foro Socios</a></div></li>
                	<?
					}
					if(($menu_premium=='Premium')||($menu_premium=='Mandante')){
						?>
                    <hr/>
                        <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>display/SPJP">Foro Premium y Mandantes</a></div></li>
                    <? }?>
                </ul>
            </div> 
        </div>
        <div class="perfil">
        	<div class="titulo" style="position:relative"><div class="menu_socio-perfil_empresa titulo_icono"></div><div class="titulo_texto">Perfil Empresa</div>
               
               <!-- Impresiòn de la cinta Nuevo
                <div style="position:absolute;z-index:99;top:-3px;right:-3px;left:auto;bottom:auto"><img src="<?=base_url()?>images/nuevo2.png" width="50" /></div>-->
            </div>
            <div class="contenido">
            	<ul>
                	<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>pages/viewpage.action?pageId=23889110">Información Membresía</a></div></li>
                    <? if($menu_premium!='Mandante'){
					?>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>pages/viewpage.action?pageId=60458967">Información Directorio</a></div></li>
                    <? /*} else{*/?>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?= $link_ver_adju?>">Mis Adjudicaciones</a></div></li>
                    <hr/>
					<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$this->params->url_historial_cambios?>">Historial de Mis Cambios&nbsp;</a></div></li>
                    <hr/>
					<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$link_add_adju?>">Agregar Adjudicaciones</a></div></li>
                    <hr/>
					<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/acce/Buscar+Proveedor">Comparador B&aacute;sico</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/pages/viewpage.action?pageId=75863033">Curriculum Técnico</a></div></li>
                    <? }else{?>
                    <hr/>
					<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$link_ver_pro?>">Mis Proyectos</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$link_ver_lici?>">Mis Licitaciones</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$link_ver_adju?>">Mis Adjudicaciones</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$this->params->url_historial_cambios?>">Historial de Mis Cambios</a></div></li>
                    <hr/>
					<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=FORMATO_URL_CONFLUENCE_ID?>75884659">Agregar Información</a></div></li>
					<? }?>
                </ul>
            </div>
        </div>
		<? if($menu_premium=='Mandante'){
			?>
        <div class="proveedor">
        	<div class="titulo" style="position:relative">Precalificaci&oacute;n de Proveedores 
                <!-- <div style="position:absolute;z-index:99999;top:-3px;right:-3px;left:auto;bottom:auto"><img src="<?=base_url()?>/images/nuevo2.png" width="50" /></div> -->
            </div>
            <div class="contenido">
            	<ul>
                	<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/acce/Mis+Proveedores">Mis Proveedores</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/acce/Buscar+Proveedor">Comparar Proveedores</a></div></li>
                </ul>
            </div>
        </div>
		<?

		}?>
        <?
        if($menu_premium!='Directorio'){
		?>
        <div class="proyectos">
        	<div class="titulo"><div class="menu_socio-proyectos titulo_icono"></div><div class="titulo_texto">Proyectos</div></div>
            <div class="contenido">
            	<ul>
                    <?=$menu_proyectos?>
					<? if(1==1){?>
					<hr>
					<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='javascript:informes_bi();'>Estadistcas</a></div></li>
					<? } ?>
                </ul>
            </div>
        </div>

        <div class="licitaciones">
        	<div class="titulo"><div class="menu_socio-licitaciones titulo_icono"></div><div class="titulo_texto">Licitaciones</div></div>
            <div class="contenido">
            	<ul>
                    <?php
                        if($menu_premium <> 'Mandante'){
                            echo '<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="'.$this->params->url_lista_lici_sugeridas.'"><b>Sugeridas para usted</b></a></div></li><hr/>';
                        }
                    ?>

                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>display/lici/Licitaciones">Ver Todas</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>display/lici/Licitaciones?p=<? echo str_replace("=", "_", base64_encode("listar_lic--".$username."--0--1--licitipo_1"));?>">Licitaciones Estimadas</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>display/lici/Licitaciones?p=<? echo str_replace("=", "_", base64_encode("listar_lic--".$username."--0--1--licitipo_2"));?>">Licitaciones Definidas</a></div></li>
                    <hr/>
					<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>display/lici/Licitaciones?p=<? echo str_replace("=", "_", base64_encode("listar_lic--".$username."--0--1--licitipo_4"));?>">Licitaciones En Proceso de Adjudicaci&oacute;n</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="<?=$url?>display/lici/Licitaciones?p=<? echo str_replace("=", "_", base64_encode("listar_lic--".$username."--0--1--licitipo_3"));?>">Licitaciones Adjudicadas</a></div></li>

                </ul>
            </div>
        </div>

        <div class="adjudicaciones">
        	<div class="titulo"><div class="menu_socio-adjudicaciones titulo_icono"></div><div class="titulo_texto">Adjudicaciones</div></div>
            <div class="contenido">
            	<ul>
                    <?=$menu_adjudicaciones?>
                </ul>
            </div>
        </div>

        <?
		}
		?>

        <div class="datos">
        	<div class="titulo"><div class="menu_socio-min_datos titulo_icono"></div><div class="titulo_texto">Mineria de Datos</div></div>
            <div class="contenido">
            	<ul>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='<?=$url?>pages/viewpage.action?pageId=27525652'>¿Qué es?</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='<?=$url?>display/acce/Informes+a+Pedido'>Informes a pedido</a></div></li>
                </ul>
            </div>
        </div>

        <div class="articulos">
        	<div class="titulo"><div class="titulo_texto" style="padding:5px 3px;">Articulación de Negocios</div></div>
            <div class="contenido">
            	<ul>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='<?=$url?>pages/viewpage.action?pageId=27525650'>¿Cómo funciona?</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='<?=$url?>pages/viewpage.action?pageId=27525359'>Solicitar</a></div></li>

                    <!--<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href=''>Buzón</a></div></li>-->
                </ul>
            </div>
        </div>
        <?
			if(($menu_premium!='Mandante')&&($menu_premium!='Directorio')){
		?>
        <div class="equipos">
        	<div class="titulo"><div class="menu_socio-equipos titulo_icono"></div><div class="titulo_texto">Equipos Gran Minería</div></div>
            <div class="contenido">
            	<ul>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='<?=$url?>pages/viewpage.action?pageId=10912092'>Ver equipos</a></div></li>

                    
                </ul>
            </div>
        </div>

        <div class="mineras">
        	<div class="titulo"><div class="menu_socio-comp_min titulo_icono"></div><div class="titulo_texto">Compañias Mineras</div></div>
            <div class="contenido">
            	<ul>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='<?=$url?>pages/viewpage.action?pageId=10912090'>Chile</a></div></li>
                    <hr/>
                    <li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href='<?=$url?>pages/viewpage.action?pageId=15992037'>Perú</a></div></li>
                </ul>
            </div>
        </div>

        <? } ?>

	</div>
	
	
<form id="frm" name="frm" method="post" action="https://www.portalminero.com/wp/estadisticas/">
		<input type="hidden" name="username" id="username" value="<?=$username;?>"/>
		  
		<script>
				function informes_bi(){ 
				
					     document.frm.submit(); 
				  
				}
		</script>
</form>


</body>



</html>