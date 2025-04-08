<?php
	if(isset($no_entra)){
?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<div style="clear:both">&nbsp;</div>
		<div>
			<div style="color:#24587F; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">
				Usted no puede acceder desde esta cuenta
			</div>
		</div>
<?php
	}else{
?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<div style="clear:both">&nbsp;</div>
		<div>
			<div style="color:#24587F; font-size:18px; font-weight:bold; float:left; line-height:20px; clear:both; width:60%;">
				Listado de Adjudicaciones Sugeridas
			</div>
			<div style="float:right; width:40%;" id="div_busqueda_nombre">
				Buscar Por Nombre de Adjudicaci&oacute;n:
				<?php
					if($busqueda_default == 'null'){
						$texto_busqueda = '';
					}else{
						$texto_busqueda = $busqueda_default;

					}

					$data = array(
									'name' 			=> 'txt_busca_adj',
									'id' 			=> 'txt_busca_adj',
									'onkeypress' 	=> '',
									'value' 		=> $texto_busqueda
								);
					echo form_input($data);
				?>
				<input type="button" class="btn_buscar_adjudicaciones btn_verde" id="btn_busca_nombre" value="Buscar" />
			</div>
		</div>
		<div style="clear:both">&nbsp;</div>
		<div class="columna_listado_pro">
			<? if(isset($adjudicaciones) && is_array($adjudicaciones) && sizeof($adjudicaciones)>0){?>
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
							echo '<a class="anterior" style="cursor: hand;" data="'.($paginador['pagina'] - 1).'">Anterior</a>';

							for ($i=1;$i<=$paginador['total_nro_paginas'];$i++) {
								if ($paginador['pagina'] == $i)
									//si muestro el índice de la página actual, no coloco enlace
									echo '<span class="p_actual" style="padding:4px;">'.$i.'</span>';
								else
									//si el índice no corresponde con la página mostrada actualmente,
									//coloco el enlace para ir a esa página
									echo '<a class="p_others" style="padding:4px; cursor: hand;cursor:pointer;" data="'.$i.'">'.$i.'</a>';
									if ($i % 25 == 0) {
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

				<table width="100%" align="center" cellspacing="0" cellpadding="0" border="1"  class="confluenceTable panel_principal column_1" style="border-collapse:collapse;">
					<thead>
						<tr>
							<?php
								if($campo_orden_def == 'nombre_adj'){
									$sent_nom = $sent_campo_def;
									$ruta_img_nom = $ruta_img_flecha[$sent_nom];
								}else{
									//por default
									$sent_nom = 'asc';
									$ruta_img_nom = $ruta_img_flecha['asc'];
								}
							?>
							<td class="titulo label_orden" data-campo="nombre_adj" data-sentido="<?php echo $sent_nom; ?>" style="cursor: hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="34%">

								<span style="color:#066293;" class="">
									Nombre
									<br/>
									<img id="nombre_adj_img" data-seleccion="0" src="<?php echo $ruta_img_nom ?>" />
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
							<td class="titulo label_orden" data-campo="Nombre_sector" data-sentido="<?php echo $sent_sec; ?>" style="cursor: hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="34%">
								<span style="color:#066293;" class="">
									Sector
									<br />
									<img id="sec_adj_img" data-seleccion="0" src="<?php echo $ruta_img_sec ?>" />
								</span>
							</td>
							<?php
								if($campo_orden_def == 'eadj.Nombre_fantasia_emp'){
									$sent_ea = $sent_campo_def;
									$ruta_img_ea = $ruta_img_flecha[$sent_ea];
								}else{
									//por default
									$sent_ea = 'desc';
									$ruta_img_ea = $ruta_img_flecha['desc'];
								}
							?>
							<td class="titulo label_orden" data-campo="eadj.Nombre_fantasia_emp" data-sentido="<?php echo $sent_ea; ?>" style="cursor: hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="23%">

								<span style="color:#066293;" class="">
									Empresa Adjudicada
									<br/>
									<img id="emp_adj_img" data-seleccion="0" src="<?php echo $ruta_img_ea ?>" />
								</span>
							</td>
							<?php
								if($campo_orden_def == 'ecomp.Nombre_fantasia_emp'){
									$sent_ec = $sent_campo_def;
									$ruta_img_ec = $ruta_img_flecha[$sent_ec];
								}else{
									//por default
									$sent_ec = 'desc';
									$ruta_img_ec = $ruta_img_flecha['desc'];
								}
							?>
							<td class="titulo label_orden" data-campo="ecomp.Nombre_fantasia_emp" data-sentido="<?php echo $sent_ec; ?>" style="cursor:hand;cursor:pointer;display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="23%">

								<span style="color:#066293;" class="">
									Comprador
									<br/>
									<img id="emp_compra_adj_img" data-seleccion="0" src="<?php echo $ruta_img_ec; ?>" />
								</span>
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="20%">
								<span style="color:#066293;">Fecha Adjudicaci&oacute;n</span>
							</td>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($adjudicaciones as $fila){
							if(strstr($fila['url'], "pageId")){
								$t1 = "&test=1";
							}else{
								$t1 = "?test=1";
							}

							if($fila['nombre_proyecto'] != "" && $fila['nombre_proyecto'] != NULL){
								$nombre = $fila['nombre_adjudicacion']." (".$fila['nombre_proyecto'].")";

							}else{
								$nombre = $fila['nombre_adjudicacion'];
							}

							?>
							<tr class="tr_hover" id="<?php echo $fila['id_adjudicacion']; ?>">
								<td class="confluenceTd" width="40%">
									<a target="_top" href="<? echo $fila['url'];?>" >
										<?=$nombre;?>
									</a>
									<a rel="shadowbox;width=1100;" href="<? echo $fila['url'].$t1;?>" style="font-size:10px;color:#999;display:inline-block">
										(Ver En Pop-Up)
									</a>
								</td>

								<td class="confluenceTd" width="10%">
									<?php echo str_replace("/", " / ",$fila['nombre_sector']); ?>
								</td>
								<td class="confluenceTd" width="10%">
									<?php echo str_replace("/", " / ",$fila['nombre_adjudicado']); ?>
								</td>
								<td class="confluenceTd" width="20%">
									<?php echo str_replace("/", " / ",$fila['nombre_comprador']);?>
								</td>
								<td class="confluenceTd" width="10%" nowrap>
									<? echo $fila['fecha_adj'];?>&deg; Trimestre <? echo $fila['anio_adj'];?>
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
							echo '<a class="anterior" style="cursor: hand;" data="'.($paginador['pagina'] - 1).'">Anterior</a>';

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
								Empresa Adjudicada
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="20%">
								Comprador
							</td>
							<td class="titulo" style="display:table-cell;padding:10px;font-size:13px;text-align:center;vertical-align:middle;background-color:#ecf4f7;color:#4b7b85" align="center" width="20%">
								Fecha Adjudicación
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

		<!-- comienzo de filtros -->
		<div class="columna_filtros_pro">
			<div style="clear:both;">
				<div style="clear:both; font-weight:bold; text-decoration:underline;color:#044073;">Filtros</div>
				<div style="clear:both;">&nbsp;</div>

				<div style="width:100%;" class="select-box">
					<div style="clear:both;">Empresa Adjudicada:</div>
					<select style="width:123px;" id="filtro_empadj" class="empadj input_radius">
						<option value="">Todos</option>
						<?php
							foreach($empadj as $clave => $valor){
								if($clave == $empadj_default){
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
					<div style="clear:both;">V&iacute;a:</div>
					<select style="width:123px;" id="filtro_via" class="via input_radius">
						<option value="">Todos</option>
						<?php
							foreach($via as $clave => $valor){
								if($clave == $via_default){
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
					<div style="clear:both;">Comprador:</div>
					<select style="width:123px;" id="filtro_comprador" class="comprador input_radius">
						<option value="">Todos</option>
						<?php
							foreach($comprador as $clave => $valor){
								if($clave == $comprador_default){
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
					<div style="clear:both;">Equipos:</div>
					<select style="width:123px;" id="filtro_equipo" class="equipo input_radius">
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
					<div style="clear:both;">Suministros:</div>
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
				<div style="clear:both; background-color:#F0F0F0; padding:5px; display:inline-block;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
					<div style="width:100%;" class="select-box">
						<div style="clear:both;">Pa&iacute;s:</div>
						<select style="width:123px;" id="filtro_pais" class="pais input_radius">
							<option value="">Todos</option>
							<?php
								foreach($pais as $clave => $valor){
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
									if($clave == $region_default){
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
				<div style="clear:both; background-color:#F0F0F0; padding:5px; display:inline-block;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
					<div style="width:100%;" class="select-box">
						<div style="clear:both;">Tipo Servicio:</div>
						<select style="width:123px;" id="filtro_catservicio" class="catservicio input_radius">
							<option value="">Todos</option>
							<?php
								foreach($catservicio as $clave => $valor){
									if($clave == $catservicio_default){
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
						<div style="clear:both;">Servicios:</div>
						<select style="width:123px;" id="filtro_subcatservicio" class="subcatservicio input_radius">
							<option value="">Todos</option>
							<?php
								foreach($subcatservicio as $clave => $valor){
									if($clave == $subcatservicio_default){
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
					<input type="button" class="btn_buscar_adjudicaciones btn_azul" id="btn_busca_adj"  value="Buscar" />
				</div>
			</div>

			<div style="clear:both;">&nbsp;</div>
			<div style="clear:both;">&nbsp;</div>
			<div style="clear:both; background-color:#F0F0F0; padding:10px;border: 1px solid #9cbebd; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;">
				<div style="clear:both; font-weight:bold; text-decoration:underline;color:#107883;">
					Buscar Adjudicaciones Relacionadas con:
				</div>
				<div style="clear:both;">&nbsp;</div>
				<div style="width:100%;" class="select-box">
					<div style="clear:both;">Obras:</div>
					<select style="width:110px;" id="filtro_obra" class="obra input_radius">
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
					<div style="clear:both;">Tipos de Proyecto:</div>
					<select style="width:110px;" id="filtro_tipo" class="tipo input_radius">
						<option value="">Todos</option>
						<?php
							foreach($tipo as $clave => $valor){
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
				<div style="width:100%;" class="">
					<input type="button" class="btn_buscar_adjudicaciones btn_verde" id="btn_busca_adj_2"  value="Buscar" />
				</div>
			</div>
		</div>
<?php
	}
?>