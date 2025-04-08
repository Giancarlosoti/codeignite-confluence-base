<?php
	if(isset($no_entra)){
?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<div style="clear:both">&nbsp;</div>
		<div>
			<div style="color:#066293; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">
				Usted no puede acceder desde esta cuenta
			</div>
		</div>
<?php
	}else{
?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<div style="clear:both">&nbsp;</div>
		<div>
			<div style="color:#066293; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">
				Listado de Proyectos Sugeridos
			</div>
			<div style="float:right; width:40%;" id="div_busqueda_nombre">
				<span style="font-size:0.73rem;">Buscar Por Nombre de Proyecto:</span>
				<?php
					if($busqueda_default == 'null'){
						$texto_busqueda = '';
					}else{
						$texto_busqueda = $busqueda_default;

					}

					$data = array(
									'name' 			=> 'txt_busca_proy',
									'id' 			=> 'txt_busca_proy',
									'onkeypress' 	=> '',
									'placeholder'   => 'Buscar en Proyectos Sugeridos',
									'value' 		=> $texto_busqueda
								);
					echo form_input($data);
				?>
				<input type="button" class="btn_buscar_proy_rel btn_buscaradju" id ="btn_buscar_nombre" value="Buscar" onClick="" />
			</div>
		</div>

		<div style="clear:both;padding-bottom:10px;">
			<a class="billo" style="color:#109aa5;" href="/pages/viewpage.action?pageId=72096318">
			<b>Suscribirse a proyectos nuevos: </b>Seleccione sectores de su interés</a>
		</div>
		<div class="columna_listado_pro" style="width:80%;float:left;padding-right:1%;">
		<? if(isset($proyectos) && is_array($proyectos) && sizeof($proyectos)>0){?>
			<div style="width:100%; clear:both; margin:0 auto; float:left; padding-bottom:5px; color:#254E6D" align="center" class="paginador">
			<?
				//arma paginador de resultados
				if($paginador['offset'] + $paginador['cantidad_resultados'] < $paginador['total_registros_query']){
					$cant_resultado_calc = $paginador['offset'] + $paginador['cantidad_resultados'];
				}else{
					$cant_resultado_calc = $paginador['total_registros_query'];
				}

				echo '<div align="center" style="clear:both; width:100%; font-size:14px;">
						Mostrando <span id="inicio_res">'.($paginador['offset'] + 1).'</span> - <span id="fin_res">'.$cant_resultado_calc.'</span> de un total de <span id="total_reg_query">'.$paginador['total_registros_query'].'</span> resultados.
					 </div>';
				echo '<div style="clear:both;"><br /> </div>';
				echo '<div class="paginate" id="contenedor_paginador" align="center" style="clear:both;">';

				if ($paginador['pagina'] != 1)
					echo '<a class="anterior" style="cursor: pointer;" data="'.($paginador['pagina'] - 1).'">Anterior</a>';

					for ($i=1;$i<=$paginador['total_nro_paginas'];$i++) {
						if ($paginador['pagina'] == $i)
							//si muestro el índice de la página actual, no coloco enlace
							echo '<span class="p_actual" style="padding:4px;">'.$i.'</span>';
						else
							//si el índice no corresponde con la página mostrada actualmente,
							//coloco el enlace para ir a esa página
							echo '<a class="p_others" style="padding:4px; cursor: hand;cursor:pointer;" data="'.$i.'">'.$i.'</a>';
						if ($i%25==0) {
									echo "<br><br>";
						}
					 }
				if ($paginador['pagina'] != $paginador['total_nro_paginas'])
					echo '<a class="siguiente" style="padding:4px; cursor: pointer;" data="'.($paginador['pagina']+1).'">Siguiente</a>';


				echo '</div>';
		 		//fin arma paginador
			?>
			</div>

			<div style="width:100%; clear:both; float:left; color:#254E6D" align="center" id="depurador">
				<input type="hidden" id="hd_pagina" value="<?php echo $pagina_def; ?>" />
				<input type="hidden" id="hd_campo_orden" value="<?php echo $campo_orden_def; ?>" />
				<input type="hidden" id="hd_sent_orden" value="<?php echo $sentido_default; ?>" />
			</div>

			<div style="clear:both;"></div>
			<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse">
				<thead>
					<tr>
						<?php
							if($campo_orden_def == 'Nombre_pro'){
								$sent_nom = $sent_campo_def;
								$ruta_img_nom = $ruta_img_flecha[$sent_nom];
							}else{
								//por default
								$sent_nom = 'asc';
								$ruta_img_nom = $ruta_img_flecha['asc'];
							}
						?>
						<td class="titulo label_orden" data-campo="Nombre_pro" data-sentido="<?php echo $sent_nom; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="40%">

							<span style="color:#066293;" class="">
								Nombre
								<br/>
								<img id="Nombre_pro_img" data-seleccion="0" src="<?php echo $ruta_img_nom ?>" />
							</span>
						</td>
						<?php
							if($campo_orden_def == 'Nombre_sector'){
								$sent_sec = $sent_campo_def;
								$ruta_img_sec = $ruta_img_flecha[$sent_sec];
							}else{
								//por default
								$sent_sec = 'desc';
								$ruta_img_sec = $ruta_img_flecha['desc'];
							}
						?>
						<td class="titulo label_orden" data-campo="Nombre_sector" data-sentido="<?php echo $sent_sec; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="40%">
							<span style="color:#066293;" class="">
								Sector
								<br />
								<img id="Nombre_sec_img" data-seleccion="0" src="<?php echo $ruta_img_sec ?>" />
							</span>
						</td>
						<?php
							if($campo_orden_def == 'Nombre_pais'){
								$sent_pais = $sent_campo_def;
								$ruta_img_pais = $ruta_img_flecha[$sent_pais];
							}else{
								//por default
								$sent_pais = 'desc';
								$ruta_img_pais = $ruta_img_flecha['desc'];
							}
						?>
						<td class="titulo label_orden" data-campo="Nombre_pais" data-sentido="<?php echo $sent_pais; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">

							<span style="color:#066293;" class="" >
								Pa&iacute;s
								<br/>
								<img id="Nombre_pais_img" data-seleccion="1" src="<?php echo $ruta_img_pais; ?>" />
							</span>
						</td>

						<td class="titulo" style="color:#066293;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="20%">
							<span style="color:#066293;">
							Regi&oacute;n
							</span>
						</td>
						<?php
							if($campo_orden_def == 'Inversion_pro'){
								$sent_inv = $sent_campo_def;
								$ruta_img_inv = $ruta_img_flecha[$sent_inv];
							}else{
								//por default
								$sent_inv = 'desc';
								$ruta_img_inv = $ruta_img_flecha['desc'];
							}
						?>
						<td class="titulo label_orden" data-campo="Inversion_pro" data-sentido="<?php echo $sent_inv; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">

							<span style="color:#066293;" class="">
								Inversi&oacute;n (US$MM)
								<br/>
								<img id="Inversion_pro_img" data-seleccion="1" src="<?php echo $ruta_img_inv; ?>" />
							</span>
						</td>
						<?php
								if($campo_orden_def == 'fecha'){
									$sent_fecha = $sent_campo_def;
									$ruta_img_fecha = $ruta_img_flecha[$sent_fecha];
								}else{
									//por default
									$sent_fecha = 'desc';
									$ruta_img_fecha = $ruta_img_flecha['desc'];
								}
							?>
						<td class="titulo label_orden" data-campo="fecha" data-sentido="<?php echo $sent_fecha; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">

							<br/>
							<span style="color:#066293;" class="" >
								Fecha Actualizaci&oacute;n
								<br/>
								<img id="fecha_actualizacion_pro_img" data-seleccion="1" src="<?php echo $ruta_img_fecha; ?>" />
							</span>
						</td>
						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">
							<span style="color:#066293;">
							Seguir
							</span>
						</td>
					</tr>
				</thead>
				<tbody id="contenedor_tabla">
					<?php
					foreach($proyectos as $fila){
						if(strstr($fila['url'], "pageId")){
							$t1 = "&test=1";
						}else{
							$t1 = "?test=1";
						}
						?>
						<tr class="tr_hover" id="<?php echo $fila['id_proyecto']; ?>">
							<td class="confluenceTd" width="40%">
								<a target="_top" href="<? echo $fila['url'];?>" >
									<?=$fila['nombre_proyecto'];?>
								</a>
								<a rel="shadowbox;width=1100;" href="<? echo $fila['url'].$t1;?>" style="font-size:10px;color:#999;display:inline-block">
									(Ver En Pop-Up)
								</a>
							</td>
							<td class="confluenceTd" width="10%">
								<?php
									echo $fila['nombre_sector'];
								?>
							</td>
							<td class="confluenceTd" width="10%">
								<?=$fila['pais'];?>
							</td>
							<td class="confluenceTd" width="20%">
								<?=$fila['region'];?>
							</td>
							<td class="confluenceTd" width="10%" >
								<div align="center">
									<?=(($fila['inversion'] != "" && $fila['inversion']!=NULL) ? number_format(round(floatval($fila['inversion'])), 0, ",", ".") : "");?>
								</div>
							</td>
							<?php
								$estilo = "";
								if($fila['ultimo_hito'] == $this->params->hito_desistido || intval($fila['ultimo_hito']) == $this->params->hito_desistido2){
									$estilo = "color:#1a237e; font-weight:bold;";
								}else{
									if(intval($fila['etapa_actual']) == 8){
										$estilo = "color:#e53935; font-weight:bold;";

									}
								}
							?>
							<td class="confluenceTd" width="10%" style="<?php echo $estilo;?>">
								<?php
									//=$fila['fecha'];
									if($fila['ultimo_hito'] == $this->params->hito_desistido || intval($fila['ultimo_hito']) == $this->params->hito_desistido2){
										echo "Proyecto Desistido";
									}else{
										if(intval($fila['etapa_actual']) != 8){
											echo $fila['fecha'];
											//$fecha = explode('-', $fila['fecha']);
											//echo $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
										}else{
											echo "Proyecto en Operaci&oacute;n";
										}
									}
								?>
							</td>

							<td class="confluenceTd" nowrap width="10%">
								<span class="watp1" name="<? echo $fila['id_pagina_pro'];?>"></span>
							</td>
						</tr>
					<?}?>
				</tbody>
			</table>

			<div style="width:100%; clear:both; margin:0 auto; float:left; padding-bottom:5px; color:#254E6D" align="center" class="paginador">
			<?
				//arma paginador de resultados
				if($paginador['offset'] + $paginador['cantidad_resultados'] < $paginador['total_registros_query']){
					$cant_resultado_calc = $paginador['offset'] + $paginador['cantidad_resultados'];
				}else{
					$cant_resultado_calc = $paginador['total_registros_query'];
				}

				echo '<div align="center" style="clear:both; width:100%; font-size:14px;">
						Mostrando <span id="inicio_res">'.($paginador['offset'] + 1).'</span> - <span id="fin_res">'.$cant_resultado_calc.'</span> de un total de <span id="total_reg_query">'.$paginador['total_registros_query'].'</span> resultados.
					 </div>';
				echo '<div style="clear:both;"><br /> </div>';
				echo '<div class="paginate" id="contenedor_paginador" align="center" style="clear:both;">';

				if ($paginador['pagina'] != 1)
					echo '<a class="anterior" style="cursor: pointer;" data="'.($paginador['pagina'] - 1).'">Anterior</a>';

					for ($i=1;$i<=$paginador['total_nro_paginas'];$i++) {
						if ($paginador['pagina'] == $i)
							//si muestro el índice de la página actual, no coloco enlace
							echo '<span class="p_actual" style="padding:4px;">'.$i.'</span>';
						else
							//si el índice no corresponde con la página mostrada actualmente,
							//coloco el enlace para ir a esa página
							echo '<a class="p_others" style="padding:4px; cursor: hand;cursor:pointer;" data="'.$i.'">'.$i.'</a>';
						if ($i%25==0) {
							echo "<br><br>";
						}
					 }
				if ($paginador['pagina'] != $paginador['total_nro_paginas'])
					echo '<a class="siguiente" style="padding:4px; cursor: pointer;" data="'.($paginador['pagina']+1).'">Siguiente</a>';


				echo '</div>';
		 		//fin arma paginador
			?>
			</div>
		<?}else{?>

			<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse">
				<thead>
					<tr class="tr_hover">
						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="40%">
								Nombre
								<br/>
						</td>
						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">
							Sector
						</td>
						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">
							Pa&iacute;s
						</td>

						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="20%">
							Regi&oacute;n
						</td>
						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">
							Inversi&oacute;n (US$MM)
							<br/>
						</td>
						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">
							Fecha Actualizaci&oacute;n
							<br/>
						</td>
						<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" width="10%">
							Seguir
						</td>
					</tr>
				</thead>
				<tbody id="contenedor_tabla">
					<tr  class="tr_hover" >
						<td colspan="7" style="text-align:center;"  class="confluenceTd" >
							Para poder ver su listado de sugeridos debe ingresar su información al directorio de proveedores hágalo <a href="http://www.portalminero.com/pages/viewpage.action?pageId=60458967">aqu&iacute;</a>
						</td>
					</tr>
				</tbody>
			</table>
		<? }?>
		</div>

		<!-- Paneles de filtros -->

		<div class="columna_filtros_pro" style="float:right;width:16%;padding-top:15px;padding-left:1%;">
			<div style="clear:both; font-weight:bold; text-decoration:underline; color:#044073;">Filtros</div>
			<div style="clear:both;">&nbsp;</div>

			<div style="width:100%" class="select-box">
			<div style="clear:both;">Tipo de Proyecto:</div>
			<select style="width:123px;" class="tipo input_radius" id="filtro_tipo">
				<option value="">Todos</option>
				<?php
					foreach($tipo as $clave => $valor){
						//echo '<option value="'.$clave.'">'.$valor.'</option>';
						if($clave == $tipo_default){
							echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
						}else{
							echo '<option value="'.$clave.'">'.$valor.'</option>';
						}
					}
				?>
			</select>
			</div>

			<div style="clear:both;">&nbsp;</div>

			<div style="width:100%;" class="select-box">
			<div style="clear:both;">Mandante:</div>
				<select style="width:123px;" id="filtro_mandante" class="input_radius" class="mandante">
					<option value="">Todos</option>
					<?php
						foreach($mandante as $clave => $valor){
							if($clave == $mandante_default){
								echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
							}else{
								echo '<option value="'.$clave.'">'.$valor.'</option>';
							}
						}
					?>
				</select>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="width:100%;" class="select-box">
				<div style="clear:both;">Pa&iacute;s:</div>
				<select style="width:123px;" id="filtro_pais" class="input_radius" class="pais">
					<option value="">Todos</option>
					<?php
						foreach($pais as $clave => $valor){
							if($clave == $pais_default){
								//country by default
								echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
							}else{
								echo '<option value="'.$clave.'">'.$valor.'</option>';
							}

						}
					?>
				</select>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="width:100%;" class="select-box">
				<div style="clear:both;">Regi&oacute;n:</div>
				<select style="width:123px;" id="filtro_region" class="input_radius" class="region">
					<option value="">Todos</option>
					<?php
						foreach($region as $clave => $valor){
							if($clave == $region_default){
								echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
							}else{
								echo '<option value="'.$clave.'">'.$valor.'</option>';
							}
						}
					?>
				</select>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="width:100%;" class="select-box">
				<div style="clear:both;">Obras Principales:</div>
				<select style="width:123px;" id="filtro_obra" class="input_radius" class="obra">
					<option value="">Todos</option>
					<?php
						foreach($obra as $clave => $valor){
							if($clave == $obra_default){
								echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
							}else{
								echo '<option value="'.$clave.'">'.$valor.'</option>';
							}
						}
					?>
				</select>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="width:100%;" class="select-box">
				<div style="clear:both;">Equipos Principales:</div>
				<select style="width:123px;" id="filtro_equipo" class="input_radius" class="equipo">
					<option value="">Todos</option>
					<?php
						foreach($equipo as $clave => $valor){
							if($clave == $equipo_default){
								echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
							}else{
								echo '<option value="'.$clave.'">'.$valor.'</option>';
							}
						}
					?>
				</select>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="width:100%;" class="select-box">
				<div style="clear:both;">Suministros Principales:</div>
				<select style="width:123px;" id="filtro_suministro" class="suministro input_radius">
					<option value="">Todos</option>
					<?php
						foreach($suministro as $clave => $valor){
							if($clave == $suministro_default){
								echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
							}else{
								echo '<option value="'.$clave.'">'.$valor.'</option>';
							}
						}
					?>
				</select>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="width:100%;" class="select-box">
				<div style="clear:both;">Servicios Principales:</div>
				<select style="width:123px;" id="filtro_servicio" class="servicio input_radius">
					<option value="">Todos</option>
					<?php
						foreach($servicio as $clave => $valor){
							if($clave == $servicio_default){
								echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
							}else{
								echo '<option value="'.$clave.'">'.$valor.'</option>';
							}
						}
					?>
				</select>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="float:left; border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px; padding-top:10px; padding-bottom:10px; padding-left:2%; padding-right:2%; background-color:#F4F4F4">
				<div style="width:95%;" class="select-box">
					<div style="clear:both;">Etapa Actual:</div>
					<select style="width:110px;" id="filtro_etapa" class="etapa input_radius">
						<option value="">Todos</option>
						<?php
							foreach($etapa as $clave => $valor){
								if($clave == $etapa_default){
									echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
								}else{
									echo '<option value="'.$clave.'">'.$valor.'</option>';
								}
							}
						?>
					</select>
				</div>

				<div style="clear:both;">&nbsp;</div>

				<div style="width:95%;" class="select-box">
				<div style="clear:both;">Responsable Etapa Actual:</div>
					<select style="width:110px;" id="filtro_responsable" class="responsable input_radius">
						<option value="">Todos</option>
						<?php
							foreach ($empresa as $clave => $valor) {
								if($clave == $empresa_default){
									echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
								}else{
									echo '<option value="'.$clave.'">'.$valor.'</option>';
								}
							}
						?>
					</select>
				</div>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="width:100%;" class="">
				<input type="button" class="btn_buscar_proy_rel btn_azul" id="btn_busca_relacionados" value="Buscar" onClick="" />
			</div>

		</div>

<?php
	}
?>