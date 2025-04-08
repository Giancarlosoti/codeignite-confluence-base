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
				Listado de Licitaciones Sugeridas
			</div>
			<div style="float:right; width:40%;" id="div_busqueda_nombre">
				Buscar Por Nombre de Licitaci&oacute;n:
				<?php
					if($busqueda_default == 'null'){
						$texto_busqueda = '';
					}else{
						$texto_busqueda = $busqueda_default;

					}

					$data = array(
									'name' 			=> 'txt_busca_lic',
									'id' 			=> 'txt_busca_lic',
									'onkeypress' 	=> '',
									'value' 		=> $texto_busqueda
								);
					echo form_input($data);
				?>
				<input type="button" class="btn_buscar_licitacion btn_verde" id="btn_buscar_nombre" value="Buscar" onClick="" />
			</div>
		</div>

		<div style="clear:both;padding-bottom:10px;">
			<div style="clear:both;padding-bottom:10px;">
				<a href="/pages/viewpage.action?pageId=54001750" style="color:#109aa5;">Seleccione sus rubros para recibir en su correo</a>
			</div>
		</div>
		<div class="columna_listado_pro">
			<? if(isset($licitaciones) && is_array($licitaciones) && sizeof($licitaciones)>0){?>
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
						echo '<a class="siguiente" style="padding:4px; cursor: hand;cursor:pointer;" data="'.($paginador['pagina']+1).'">Siguiente</a>';


					echo '</div>';
			 		//fin arma paginador
				?>
				</div>

				<div style="width:100%; clear:both; float:left; color:#254E6D" align="center">
					<input type="hidden" id="hd_pagina" value="<?php echo $pagina_def; ?>" />
					<input type="hidden" id="hd_campo_orden" value="<?php echo $campo_orden_def; ?>" />
					<input type="hidden" id="hd_sent_orden" value="<?php echo $sentido_default; ?>" />
				</div>

				<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse">
					<thead>
						<tr>
							<?php
								if($campo_orden_def == 'Nombre_lici_completo'){
									$sent_nom = $sent_campo_def;
									$ruta_img_nom = $ruta_img_flecha[$sent_nom];
								}else{
									//por default
									$sent_nom = 'asc';
									$ruta_img_nom = $ruta_img_flecha['asc'];
								}
							?>
							<td class="titulo label_orden" data-campo="Nombre_lici_completo" data-sentido="<?php echo $sent_nom; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="50%">
								<span style="color:#066293;" class="">
									Nombre
									<br/>
									<img id="Nombre_lici_completo_img" data-seleccion="0" src="<?php echo $ruta_img_nom ?>" />
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
							<td class="titulo label_orden" data-campo="Nombre_sector" data-sentido="<?php echo $sent_sec; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="50%">
								<span style="color:#066293;" class="">
									Sector
									<br />
									<img id="Nombre_sector_completo_img" data-seleccion="0" src="<?php echo $ruta_img_sec ?>" />
								</span>
							</td>
							<?php
								if($campo_orden_def == 'Nombre_lici_tipo'){
									$sent_lt = $sent_campo_def;
									$ruta_img_lt = $ruta_img_flecha[$sent_lt];
								}else{
									//por default
									$sent_lt = 'desc';
									$ruta_img_lt = $ruta_img_flecha['desc'];
								}
							?>
							<td class="titulo label_orden" data-campo="Nombre_lici_tipo" data-sentido="<?php echo $sent_lt; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="15%">

								<span style="color:#066293;" class="" >
									Estado Licitaci&oacute;n
									<br/>
									<img id="Nombre_lici_tipo_img" data-seleccion="1" src="<?php echo $ruta_img_lt; ?>" />
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
							<td class="titulo label_orden" data-campo="Nombre_pais" data-sentido="<?php echo $sent_pais; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="15%">
								<span style="color:#066293;" class="" >
									Pa&iacute;s
									<br/>
									<img id="Nombre_pais_img" data-seleccion="1" src="<?php echo $ruta_img_pais; ?>" />
								</span>
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#066293;" align="center" width="20%">
								<?php
									if($tipo_lici_default == 2){
										echo "Fecha L&iacute;mite Compra Bases";
									}else{
										echo "Regi&oacute;n";
									}

								?>

							</td>

						</tr>
					</thead>
					<tbody>
						<?php
						foreach($licitaciones as $fila){
							if(strstr($fila['url'], "pageId")){
								$t1 = "&test=1";
							}else{
								$t1 = "?test=1";
							}
							?>
							<tr class="tr_hover" id="<?php echo $fila['id_licitacion']; ?>">
								<td class="confluenceTd" width="40%">
									<a target="_top" href="<? echo $fila['url'];?>" >
										<?=$fila['nombre_completo'];?>
									</a>
									<a rel="shadowbox;width=1100;" href="<? echo $fila['url'].$t1;?>" style="font-size:10px;color:#999;display:inline-block">
										(Ver En Pop-Up)
									</a>
								</td>

								<td class="confluenceTd" width="10%">
									<?=$fila['nombre_sector'];?>
								</td>
								<td class="confluenceTd" width="10%">
									<?=$fila['estado_licitacion'];?>
								</td>
								<td class="confluenceTd" width="20%">
									<?=$fila['pais'];?>
								</td>
								<td class="confluenceTd" width="10%" >
									<?php
										if($tipo_lici_default == 2){
											if($fila['fecha_limite'] != "0000-00-00"){
												$fecha = explode('-', $fila['fecha_limite']);
												echo $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
											}else{
												echo "";
											}
										}else{
											echo $fila['region'];
										}

									?>
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
						echo '<a class="siguiente" style="padding:4px; cursor: hand;cursor:pointer;" data="'.($paginador['pagina']+1).'">Siguiente</a>';


					echo '</div>';
			 		//fin arma paginador
				?>
				</div>
			<?}else{?>

				<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse">
					<thead>
						<tr>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="50%">
								Nombre
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="15%">
								Sector
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="15%">
								Estado Licitaci&oacute;n
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="15%">
								Pa&iacute;s
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="20%">
								Regi&oacute;n
							</td>

						</tr>
					</thead>
					<tbody>
						<tr class="tr_hover" >
							<td colspan="5" style="text-align:center;"  class="confluenceTd" >
								Para poder ver su listado de sugeridos debe ingresar su información al directorio de proveedores hágalo <a href="http://www.portalminero.com/pages/viewpage.action?pageId=60458967">aqu&iacute;</a>
							</td>
						</tr>
					</tbody>
				</table>
			<? }?>
		</div>

		<!-- inicio de filtros -->
		<div class="columna_filtros_pro">
			<div style="clear:both;">
				<div style="clear:both; font-weight:bold; text-decoration:underline; color:#044073;">Filtros</div>
				<div style="clear:both;">&nbsp;</div>

				<div style="width:100%;" class="select-box">
					<div style="clear:both;">Sector:</div>
					<select style="width:123px;" id="filtro_sector" class="sector input_radius">
						<option value="">Todos</option>
						<?php
							foreach($sector as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
								if($clave == $sector_default){
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
					<div style="clear:both;">Empresa que Licita:</div>
					<select style="width:123px;" id="filtro_mandante" class="mandante input_radius">
						<option value="">Todos</option>
						<?php
							foreach($mandante as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
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
					<select style="width:123px;" id="filtro_pais" class="pais input_radius">
						<option value="">Todos</option>
						<?php
							foreach($pais as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
								if($clave == $pais_default){
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
					<select style="width:123px;" id="filtro_region" class="region input_radius">
						<option value="">Todos</option>
						<?php
							foreach($region as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
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
					<div style="clear:both;">Registros Requeridos:</div>
					<select style="width:123px;" id="filtro_rev_prov" class="reg_prov input_radius">
						<option value="">Todos</option>
						<?php
							foreach($reg_prov as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
								if($clave == $reg_prov_default){
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
					<div style="clear:both;">Estado Licitaci&oacute;n:</div>
					<select style="width:123px;" id="filtro_tipo_lici" class="tipo_lici input_radius">
						<option value="">Todos</option>
						<?php
							foreach($tipo_lici as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
								if($clave == $tipo_lici_default){
									echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
								}else{
									echo '<option value="'.$clave.'">'.$valor.'</option>';
								}
							}
						?>
					</select>
				</div>
				<div style="clear:both;">&nbsp;</div>
				<div style="width:100%;" class="">
					<input type="button" class="btn_buscar_licitacion btn_azul" value="Buscar" id="btn_busca_lic_1" onClick="" />
				</div>

			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="clear:both;">&nbsp;</div>

			<div style="clear:both; background-color:#F0F0F0; padding:10px;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
				<div style="clear:both; font-weight:bold; text-decoration:underline;color:#107883;">Buscar Licitaciones Relacionadas con:</div>
				<div style="clear:both;">&nbsp;</div>
				<div style="width:100%;" class="select-box">
					<div style="clear:both;">Obras Principales:</div>
					<select style="width:110px;" id="filtro_obra" class="obra input_radius">
						<option value="">Todos</option>
						<?php
							foreach($obra as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
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
					<select style="width:110px;" id="filtro_equipo" class="equipo input_radius">
						<option value="">Todos</option>
						<?php
							foreach($equipo as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
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
					<select style="width:110px;" id="filtro_suministro" class="suministro input_radius">
						<option value="">Todos</option>
						<?php
							foreach($suministro as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
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
					<select style="width:110px;" id="filtro_servicio" class="servicio input_radius">
						<option value="">Todos</option>
						<?php
							foreach($servicio as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
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
				<div style="width:100%;" class="select-box">
					<div style="clear:both;">Tipo de Proyectos:</div>
					<select style="width:110px;" id="filtro_tipo" class="tipo input_radius">
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
					<div style="clear:both;">Rubros Principales:</div>
					<select style="width:110px;" id="filtro_rubro" class="rubro input_radius">
						<option value="">Todos</option>
						<?php
							foreach($rubro as $clave => $valor){
								//echo '<option value="'.$clave.'">'.$valor.'</option>';
								if($clave == $rubro_default){
									echo '<option value="'.$clave.'" selected >'.$valor.'</option>';
								}else{
									echo '<option value="'.$clave.'">'.$valor.'</option>';
								}
							}
						?>
					</select>
				</div>
				<div style="clear:both;">&nbsp;</div>
				<div style="width:100%;" class="">
					<input type="button" class="btn_buscar_licitacion btn_verde" id="btn_busca_lic_2" value="Buscar" onClick="" />
				</div>
			</div>
		</div>
<?php
	}
?>