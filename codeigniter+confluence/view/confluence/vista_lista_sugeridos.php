<html>
	<head>
		<title>Sistema de proyectos sugeridos</title>
	</head>
	<body>
	
		<?php
			if(!isset($no_entra)){
		?>	
				<div id="contenedor_sugeridos">
					<div class="tabletitle">
						<h2>Principales Proyectos Sugeridos para Usted</h2>
					</div>
					<!--<div style="height:300;overflow:scroll">-->
						<table class="tableview">
							<?php
								if($estado == true){  
									$limite = 10;
									$i = 1;
									if(isset($result)){
										if(sizeof($result['contenedor']) > 0){
											foreach($result['contenedor'] as $fila){
												if($i <= $limite){
													echo '<tr><td>
														<span class="icono">
															<img src="/sitio_portal/images/socios/flecha.png">
														</span><a href="'.$fila['url'].'">'.$fila['nombre_proyecto'].'</a>
													</td></tr>';
													$i++;
												}
												
											}
										}else{
											echo 'Para poder sugerir proyectos debe ingresar su informaci&oacute;n al directorio de proveedores hágalo <a href="'.URL_PUBLICA_CONFLUENCE.'/pages/viewpage.action?pageId=60458967">aqu&iacute;</a>';
										}
									
									}else{
										echo 'Para poder sugerir proyectos debe ingresar su informaci&oacute;n al directorio de proveedores hágalo <a href="'.URL_PUBLICA_CONFLUENCE.'/pages/viewpage.action?pageId=60458967">aqu&iacute;</a>';

									}
									
								}else{
									echo $estado;
								
								}
							
								
								
							?>
						</table>
						<?php
							//echo $estado;
						?>
					<!--</div>-->
				</div>
		<?php			
			}
		?>
	</body>
</html>