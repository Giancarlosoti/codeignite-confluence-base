<?php
class Confluence extends CI_Controller {
	private $key='c29jaW9zX3J5czIwMTU=';
	function test_pag(){
		echo base64_decode("Z3VhcmRhcl9ydWJyb3Mvd2VibWFzdGVyX2Z2L2xpc3RhXzIwLWxpc3RhXzIxLWxpc3RhXzIyLWxpc3RhXzIzLWxpc3RhXzI0LWxpc3RhXzI1LWxpc3RhXzI2LWxpc3RhXzI3LWxpc3RhXzI4LWxpc3RhXzI5LWxpc3RhXzMwLWxpc3RhXzMxLWxpc3RhXzMyLWxpc3RhXzMzLWxpc3RhXzM0LWxpc3RhXzM1LWxpc3RhXzM2LWxpc3RhXzM3LWxpc3RhXzM4");
	}

	function __construct(){
		parent::__construct();
		$this->load->model('params','params');
		$this->load->model('confluence/m_confluence','confluence');
		$this->load->model('soap','soap');
		$this->load->model('proyecto_tipo/m_tipo','tipo');
		$this->load->model('obra/m_obra','obra');
		$this->load->model('equipo/m_equipo','equipo');
		$this->load->model('etapa/m_etapa','etapas');
		$this->load->model('adjudicacion/m_adjudicacion','adjudicacion');
		$this->load->model('licitacion/m_licitacion','licitacion');
		$this->load->model('suministro/m_suministro','suministro');
		$this->load->model('empresa/m_empresa','empresa');
		$this->load->model('dir_prov/m_dirprov','dir_prov');
		$this->load->model('etapa/m_etapa','etapa');
		$this->load->model('m_pais','pais');
		$this->load->model('m_region','region');
		$this->load->model('m_comuna','comuna');
		$this->load->model('ingreso/m_ingreso','ingreso');
		$this->load->model('servicio/m_servicio','servicio');
		$this->load->model('proyecto_sector/m_sector','sector');
		$this->load->model('proyectos/m_ficha','ficha');

		//require("simplepie.inc");
		include_once('simplepie/autoloader.php');
		include_once('simplepie/idn/idna_convert.class.php');
		$this->output->enable_profiler(false);
	}

	public function listado_mensual_de_proyectos(){
		$this->load->view('confluence/listado_mensual_de_proyectos');
	}
	
	public function generar_js_proyectos_todos(){
		var_dump($this->confluence->generar_js_proyectos_todos());
	}

	public function datos_bolsa($metal){
		redirect('bolsa/ultimos/'.$metal);
	}

	public function listar_bolsa(){
		redirect('bolsa/listar_bolsa/');
	}

	public function index(){
		//redirect('ficha/mostrar');
	}

	public function ajax(){
		echo json_encode(array("estado"=>"OK"));
	}

	public function contacto($origen){
		$dato=array();
                $dato["origen"]= $origen;
		$dato["pais"]=array("name"=>"pais", "params"=>$this->pais->get_all(), "select"=>"", "js"=>"class='cajacontacto' id='pais' style='width:100%;'");

		$this->load->view('confluence/contacto',$dato);
	}

	public function contacto_membresia(){
		$dato=array();
		$dato["pais"]=array("name"=>"pais", "params"=>$this->pais->get_all(), "select"=>"", "js"=>"class='cajacontacto' id='pais' style='width:100%;'");
		$this->load->view('confluence/contacto_membresia',$dato);
	}

	public function informes($username){
		$dato=array();
		if($soc=$this->confluence->cargar_socio($username)){
			$dato["socio"]=$soc;
		}
		$this->load->view('confluence/informes',$dato);
	}

	public function informar_cambios($username, $id_pag="", $id_usuario=""){
		$dato=array();
		if($soc=$this->confluence->cargar_socio($username)){
			$dato["socio"]=$soc;
			$dato["proyecto"]=$this->ficha->buscar_por_pag($id_pag);
		}
		$this->load->view('confluence/informar_cambios', $dato);
	}

	public function sugerencias($username){
		$dato=array();
		if($soc=$this->confluence->cargar_socio($username)){
			$dato["socio"]=$soc;
		}
		$this->load->view('confluence/sugerencias', $dato);
	}

	public function articulacion($username){
		$dato=array();

		if($soc=$this->confluence->cargar_socio($username)){
			$dato["socio"]=$soc;
		}
		$this->load->view('confluence/articulacion',$dato);
	}

	public function inscripcion(){
		$this->load->view("confluence/inscripcion", array());
	}

	public function inscripcion2(){
		$this->load->view("confluence/inscripcion2", array());
	}

	public function procesa_inscripcion($params){
		$body="";
		/*$this->load->library('email');
		$config['protocol'] = 'smtp';
		$config['wordwrap'] = TRUE;
		$config['smtp_host'] = 'smtp.portalminero.com';
		$config['smtp_user'] = 'info.portalminero';
		$config['smtp_pass'] = $this->params->pass_contacto;
		$config['mailtype'] = 'html';
		$config['charset']  = 'utf-8';
		$config['newline']  = "\r\n";
		$this->email->initialize($config);
		$this->email->subject(utf8_encode("Inscripci�n Nueva"));
		*/

		$asunto = "Inscripción Nueva";  //Asunto del mensaje
		$params=base64_decode(str_replace("_", "=", $params));
		$params=json_decode($params);

		$templatemail=$_SERVER["DOCUMENT_ROOT"]."/template/inscripcion_template.php";
		$templatemail=file_get_contents($templatemail);
		foreach($params as $p){
			if($p->name=="email")
				$email=$p->value;
			$templatemail=str_replace("@".$p->name, htmlentities(utf8_decode($p->value)), $templatemail);
		}

		$remitente = array($email, $email);  //Quien env�a el correo
		$destino = array("inscripciones@portalminero.com"=>"Inscripciones", $email => $email);

		$contenido = nl2br($templatemail);
		/*if(isset($id_) && $id_==17){
			$this->email->to("cmorgado@portalminero.com", "Carolina Morgado");
		}else{
			$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
		}*/

		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
			$dato=array("send"=>1, "url"=>$this->params->url_contacto);
			$this->load->view('confluence/inscripcion',$dato);
		}else{
			$dato=array("send"=>0, "url"=>$this->params->url_contacto);
			$this->load->view("confluence/inscripcion", $dato);
		}
	}

	public function view($oc, $monto){
		if($oc!="" && $oc!=NULL && $monto!="" && $monto!=NULL){
			$d_pago["oc"]=$oc;
			$d_pago["monto"]=$monto;
			$this->load->view("confluence/view", $d_pago);
		}else{
			echo "error";
		}
	}

	public function procesa_inscripcion2($params){
		$body="";

		#PARAMETRIZACION DE LIBRERIA EMAIL
		$asunto    = "Inscripción Nueva";  //Asunto del mensaje
		#FIN PARAMETRIZACION
		$params=base64_decode(str_replace("_", "=", $params));
		$params=json_decode($params);

		$templatemail=$_SERVER["DOCUMENT_ROOT"]."/".$this->params->dir_codeigniter."template/inscripcion2_template.php";
		$templatemail=file_get_contents($templatemail);
		$datos=array();
		foreach($params as $p){
			if($p->name=="email"){
				$email=$p->value;
				$datos["email"]=$p->value;
			}
			if($p->name=="nombre_completo"){
				$datos["nombre"]=$p->value;
			}
			if($p->name=="rut"){
				$datos["rut"]=$p->value;
			}
			if($p->name=="direccion"){
				$datos["direccion"]=$p->value;
			}
			if($p->name=="telefono"){
				$datos["telefono"]=$p->value;
			}
			if($p->name=="sem1")
				$sem1="";
			if($p->name=="sem2")
				$sem2="";
			if($p->name=="sem3")
				$sem3="";
			if($p->name=="webpay")
				$webpay=$p->value;
			$templatemail=str_replace("@".$p->name, htmlentities(utf8_decode($p->value)), $templatemail);
		}
		$monto=0;
		$selected="";
		if(!isset($sem1)){
			$sem1="display:none;";
		}else{
			if($selected!="")
				$selected.=",sem1";
			else
				$selected.="sem1";
			$monto+=235000;
		}
		if(!isset($sem2)){
			$sem2="display:none;";
		}else{
			if($selected!="")
				$selected.=",sem2";
			else
				$selected.="sem2";
			$monto+=215000;
		}
		if(!isset($sem3)){
			$sem3="display:none;";
		}else{
			if($selected!="")
				$selected.=",sem3";
			else
				$selected.="sem3";
			$monto+=215000;
		}
		$datos["selected"]=$selected;
		$templatemail=str_replace("@style_sem1", $sem1, $templatemail);
		$templatemail=str_replace("@style_sem2", $sem2, $templatemail);
		$templatemail=str_replace("@style_sem3", $sem3, $templatemail);

		/*if(isset($id_) && $id_==17){
			$this->email->to("cmorgado@portalminero.com", "Carolina Morgado");
		}else{
			$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
		}*/

		#agregar emisores, y receptores
		$remitente = array($email, $email);  //Quien env�a el correo
		$destino_cc = array("inscripciones@portalminero.com"=>"Inscripciones");
		$destino = array($email => $email);

		#agregar contenido BODY
		$contenido = $templatemail;
		#enviar
		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc,$destino_bcc="",$contenido,$asunto)){
			if(isset($webpay)){
				if($this->confluence->guardar_inscripcion($datos)){
					if($d_pago=$this->confluence->crear_pago($datos["rut"], $monto)){
						$this->load->view("confluence/iframe", $d_pago);
					}else{
						echo "Error de Ingreso";
					}
				}else{
					echo "Error de Ingreso";
				}
			}else{
				$dato=array("send"=>1, "url"=>$this->params->url_contacto);
				$this->load->view('confluence/inscripcion2',$dato);
			}
		}else{
			$dato=array("send"=>0, "url"=>$this->params->url_contacto);
			$this->load->view("confluence/inscripcion2", $dato);
		}
	}

	function resultado(){
		#valida recepcion por post
		$error="entra a resultado\n\r";
		if(isset($_POST)){
			$error.="recibe post\n\r";
			foreach($this->params->tbk_params as $p){
				if(isset($_POST[$p])){
					$$p=$_POST[$p];
				}
			}
			$error.=implode(",", $_POST)."\n\r";
			if(isset($TBK_RESPUESTA) && $TBK_RESPUESTA=="0"){ $acepta=true; } else { $acepta=false; }
			#comprueba que se recibió el TBK_ORDEN_COMPRA
			if(isset($TBK_ORDEN_COMPRA) && $TBK_ORDEN_COMPRA!="" && $TBK_ORDEN_COMPRA!=NULL){
				$error.="Se crea orden compra\n\r";
				if($acepta){
					$data="";
					$x=0;
					#genera texto para validar MAC
					$arr=array();
					foreach($this->params->tbk_params as $p){
						if(isset($_POST[$p])){
							$data.=$p."=".$_POST[$p];
							$arr[$p]=$_POST[$p];
							if($x!=(sizeof($_POST)-1)){
								$data.="&";
							}
						}
					}
					#crea archivo de validacion de MAC
					$folder_save=$_SERVER["DOCUMENT_ROOT"]."/".$this->params->dir_codeigniter.str_replace("@oc", $TBK_ORDEN_COMPRA, $this->params->log_folder_webpay);
					if(file_put_contents($folder_save, trim($data, "&"))){
						$error.="Se crea checkmac->".trim($data, "&")."\n\r";
						@chmod($folder_save, 0777);
						$cmdline = "/usr/lib/cgi-bin/inscripcion/tbk_check_mac.cgi ".$folder_save;
						#valida MAC
						exec($cmdline, $result, $retint);
						if(is_array($result) && sizeof($result)>0){
							if ($result[0]=="CORRECTO"){
								$error.="checkmac validado\n\r";
								if(sizeof($arr)>0){
									$this->db->insert("webpay_data",$arr);
								}
								#busca TBK_ORDEN_COMPRA en base de datos
								$rs=$this->confluence->buscar_usuario_oc($TBK_ORDEN_COMPRA);
								if(is_object($rs)){
									$error.="existe OC en BD\n\r";
									#echo "EXISTE OC<br>";
									#TBK_ORDEN_COMPRA encontrado, valida si el monto devuelto por la transacción es igual al de la base de datos.
									//if(ceil(str_replace(".", "", str_replace(",", "", number_format(floatval(str_replace(".", "", $TBK_MONTO)), 0, "", ""))))==ceil(str_replace(".", "", str_replace(",", "", $rs->total_webpay)))){
									$error.="MONTOS INCORRECTOS->".floatval($TBK_MONTO)."==".floatval(strval($rs->total_webpay)."00")."\n\r";
									if(floatval($TBK_MONTO)==floatval(strval($rs->total_webpay)."00")){
										$datos["resultado"]="ACEPTADO";
									}else{
										$error.="MONTOS INCORRECTOS\n\r";
										$datos["resultado"]="RECHAZADO";
									}
								}else{
									$error.="MONTOS INCORRECTOS\n\r";
									$datos["resultado"]="RECHAZADO";
								}
							}else{
								$error.="NO CHECKMAC\n\r";
								$datos["resultado"]="RECHAZADO";
							}
						}else{
							$error.="MONTOS INCORRECTOS\n\r";
							$datos["resultado"]="RECHAZADO";
						}
					}else{
						$error.="CHECkMAC INVALIDO\n\r";
						$datos["resultado"]="RECHAZADO";
					}
				}else{
					$error.="RECHAZADA\n\r";
					#PENDIENTE, CUANDO TBK RECHAZA LA TRANSACCION
					$datos["resultado"]="ACEPTADO";
				}
			}else{
				$error.="NO HAY OC DESDE POST\n\r";
				$datos["resultado"]="RECHAZADO";
			}
		}else{
			$error.="NO HAY POST\n\r";
			$datos["resultado"]="RECHAZADO";
		}
		file_put_contents("/tmp/TFL", $error);
		chmod("/tmp/TFL", 0777);
		$this->load->view('confluence/resultado',$datos);
	}

	function exitosa($carro=0){
		/*$_POST["TBK_ORDEN_COMPRA"]=20120705101401;
		$_POST["TBK_CODIGO_AUTORIZACION"]=1;
		$_POST["TBK_ID_TRANSACCION"]=1;
		$_POST["TBK_TIPO_TRANSACCION"]=1;
		$_POST["TBK_FINAL_NUMERO_TARJETA"]=1;
		$_POST["TBK_TIPO_PAGO"]="1";
		$_POST["TBK_FECHA_CONTABLE"]=1;
		$_POST["TBK_ID_SESION"]=1;
		$_POST["TBK_MAC"]=1;*/
		/*echo "<pre>";
		var_dump($this->ajax->get_comprados($_POST["TBK_ORDEN_COMPRA"]));
		echo "</pre>";#die();*/
		if(isset($_POST["TBK_ORDEN_COMPRA"])){
			$this->db->where("orden_compra_webpay", $_POST["TBK_ORDEN_COMPRA"]);
			$data=array("estado"=>"Aprobado", "fecha_pago"=>date("Y-m-d H:i:s"));
			$detalle="";
			if($this->db->update("webpay_inscripcion", $data)){
				if($rs=$this->confluence->buscar_usuario_oc($_POST["TBK_ORDEN_COMPRA"])){
					if(is_object($rs)){
						$contenido="<strong>La transaccion fue Exitosa, los datos de esta son:</strong>\n\n@detalle\n\n@info";
						$info_trans="";
						$iva=0;
						$total=$rs->total_webpay;
						$prod=explode(",",$rs->selected);
						foreach($prod as $p){
							if($p=="sem1"){
								$detalle.="<div style='clear:both; padding-bottom:20px;'>Gesti&oacute;n de M&uacute;ltiples Proyectos <strong>$235.000 IVA Incluido</strong></div>";
							}
							if($p=="sem2"){
								$detalle.="<div style='clear:both; padding-bottom:20px;'>Gesti&oacute;n de Riesgos y Desarrollo de la Empresa <strong>$215.000 IVA Incluido</strong></div>";
							}
							if($p=="sem3"){
								$detalle.="<div style='clear:both; padding-bottom:20px;'>Contratos para Empresas Proveedoras y Contratistas <strong>$215.000 IVA Incluido</strong></div>";
							}
						}

						$detalle.="\n\nTotal : $  ".number_format(floatval($total), 2, ",", ".")." (pesos chilenos)";
					}
					$web=$this->confluence->get_wpdata($_POST["TBK_ORDEN_COMPRA"]);

					$tt=((isset($web->TBK_TIPO_PAGO)) ? $web->TBK_TIPO_PAGO : "");
					if($tt!=""){
						if($tt=="VN")
							$tt="Sin Cuotas";
						if($tt=="VC")
							$tt="Cuotas Normales";
						if($tt=="SI")
							$tt="Sin Inter&eacute;s";
						if($tt=="VD")
							$tt="Venta D&eacute;bito";
					}
					$info_trans.="\nDETALLES DE LA COMPRA:";
					$info_trans.="\n\nCONTACTO 	:	<strong>".(($rs->nombre))."</strong>";
					$info_trans.="\nNRO. DE COMPRA WEBPAY	:	".((isset($_POST["TBK_ORDEN_COMPRA"])) ? $_POST["TBK_ORDEN_COMPRA"] : "");
					$info_trans.="\nCOD. AUTORIZACION	:	".((isset($web->TBK_CODIGO_AUTORIZACION)) ? $web->TBK_CODIGO_AUTORIZACION : "");
					$info_trans.="\nNUMERO TRANSACCION	:	".((isset($web->TBK_ID_TRANSACCION)) ? $web->TBK_ID_TRANSACCION : "");
					$info_trans.="\nTIPO DE TRANSACCION	:	Venta";
					$info_trans.="\n4 ULTIMOS DIGITOS	:	".((isset($web->TBK_FINAL_NUMERO_TARJETA)) ? $web->TBK_FINAL_NUMERO_TARJETA : "");
					$info_trans.="\nNUMERO DE CUOTAS	:	".((isset($web->TBK_NUMERO_CUOTAS)) ? $web->TBK_NUMERO_CUOTAS : "");
					$info_trans.="\nTIPO DE CUOTAS		:	".$tt;
					$info_trans.="\nFECHA TRANSACCION	:	".date("d-m-Y", strtotime(substr($rs->fecha_pago, 0, 10)));
					$info_trans.="\n\nURL DEL COMERCIO: <a href='http://www.portalminero.com'>www.portalminero.com</a>\n";
					$info_trans.="\n\n<strong>EN CASO DE TENER ALGUNA DUDA CONTACTESE CON <a href='mailto:info@portalminero.com'>info@portalminero.com</a></strong>";
					$contenido=str_replace("@detalle", $detalle, $contenido);
					$contenido=str_replace("@info", $info_trans, $contenido);

					#PARAMETRIZACION DE LIBRERIA EMAIL
					#FIN PARAMETRIZACION
					$remitente = array($this->params->email_contacto, $this->params->nombre_contacto);

					$destino = array($rs->email => $rs->email);

					$destino_cc = array("info@portalminero.com"=>"info@portalminero.com");
					//$mail->AddCC("clizama@portalminero.com", "clizama@portalminero.com");

					$asunto = "Transacción Exitosa";

					$contenido = nl2br($contenido);
					if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc,$destino_bcc="",$contenido,$asunto)){
						$this->load->view('confluence/exitosa',array("text"=>nl2br($contenido)));
					}else{
						return(false);
					}
					/*
					if($id_sesion=$this->busca()){
						$sess=$id_sesion;
						if($id_sesion=$this->ajax->validar_sesion($id_sesion)){
							//$this->load->view('ajax/exitosa',array());
						}else{
							$this->falla();
						}
					}else{
						$this->falla();
					}
					*/
				}else{
					echo "Error en Detalle de Compra";
				}
			}
		}else{
			echo "Error de Datos";
		}
	}

	function fallida(){
		/*if($id_sesion=$this->busca()){
			$sess=$id_sesion;
			if($id_sesion=$this->ajax->validar_sesion($id_sesion)){*/
				if(isset($_POST["TBK_ORDEN_COMPRA"])){
					$this->db->where("orden_compra_webpay", $_POST["TBK_ORDEN_COMPRA"]);
					$data=array("estado"=>"Rechazado");
					if($this->db->update("webpay_inscripcion", $data)){
						$this->load->view('confluence/fallida',array());
					}
				}else{
					echo "Transaccion Mala";
				}
			/*}else{
				$this->falla();
			}
		}else{
			$this->falla();
		}*/
	}


	function inscripcion_template(){
		$this->load->view("confluence/inscripcion_template", array());
	}


	function get_subcatserv($p=""){

		$method=$_GET["method"];
		unset($_GET["method"]);
		unset($_GET["_"]);
		$arr=$_GET;
		$estado="ok";
		$mensaje="";
		$html="";
		$res="";
		if(isset($_GET["id_cat_serv"]))
			$id_cat_serv=$_GET["id_cat_serv"];
		if(isset($_GET["id_sector"]))
			$id_sector=$_GET["id_sector"];
		if(isset($_GET["username"]))
			$username=base64_decode(str_replace("_", "=", $_GET["username"]));
		if(isset($id_cat_serv) && isset($id_sector) && isset($username)){
			$subcatservicio=$this->adjudicacion->subcatservicios_sel("listar_adj", $id_sector, $username, $id_cat_serv);
			if(is_array($subcatservicio) && sizeof($subcatservicio)>0){
				unset($subcatservicio[""]);
				foreach($subcatservicio as $ind=>$scs){
					$html.='<option value="'.$ind.'">'.$scs.'</option>';
				}
			}else{
				$estado="error";
				$mensaje="No hay Parametros";
			}
		}else{
			$estado="error";
			$mensaje="No hay Parametros";
		}
		echo $method."(".json_encode(array("estado"=>$estado, "mensaje"=>$mensaje, "html"=>$html)).")";
	}

	
	function procesa_contacto2_old($p=""){
	    $body="";
	    /*$this->load->library('email');
	     $config['protocol'] = 'smtp';
	     $config['wordwrap'] = TRUE;
	     $config['smtp_host'] = 'smtp.portalminero.com';
	     $config['smtp_user'] = 'info.portalminero';
	     $config['smtp_pass'] = $this->params->pass_contacto;
	     $config['mailtype'] = 'html';
	     $config['charset']  = 'utf-8';
	     $config['newline']  = "\r\n";
	     $config['validate']  = FALSE;
	     $this->email->initialize($config);*/

	    /*$mail->WordWrap = 50;*/
	    if(isset($_GET["membresia"])){
	        unset($_GET["membresia"]);
	    }
	    
	    $method=$_GET["method"];
	    unset($_GET["method"]);
	    unset($_GET["_"]);
	    $arr=$_GET;
	    $estado="ok";
	    $mensaje="";
	    $res="";
	    /*$body='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	     $body.='<html xmlns="http://www.w3.org/1999/xhtml">';
	     $body.='<head>';
	     $body.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	     $body.='<title>Contacto desde EL Portal</title>';
	     $body.='</head>';
	     $body.='<body>';
	     */
	    if(is_array($arr) && sizeof($arr)>0){
	        
	        $valor=array();
	        foreach($arr as $key=>$val1){
	            if($key=="id"){
	                $id_=intval($val1);
	                $id=intval($val1);
	                if($id=="0")
	                    $asunto="P&aacute;gina Principal";
	                    else if($id=="1")
	                        $asunto="Informaci&oacute;n de Proyectos";
	                        else if($id=="2")
	                            $asunto="Informaci&oacute;n de Licitaciones";
	                            else if($id=="3")
	                                $asunto="Empresas Mineras";
	                                else if($id=="4")
	                                    $asunto="Estudios y Miner&iacute;a de Datos";
	                                    else if($id=="5")
	                                        $asunto="Articulaci&oacute;n de Negocios";
	                                        else if($id=="6")
	                                            $asunto="Reclutamiento y Selecci&oacute;n de Personal";
	                                            else if($id=="7")
	                                                $asunto="Capacitaci&oacute;n";
	                                                else if($id=="8")
	                                                    $asunto="&aacute;reas de Capacitaci&oacute;n";
	                                                    else if($id=="9")
	                                                        $asunto="Certificaciones";
	                                                        else if($id=="10")
	                                                            $asunto="Banners";
	                                                            else if($id=="11")
	                                                                $asunto="Publicidad en el Bolet&iacute;n Semanal";
	                                                                else if($id=="12")
	                                                                    $asunto="Publi-Reportajes";
	                                                                    else if($id=="13")
	                                                                        $asunto="Publicaci&oacute;n en Directorio";
	                                                                        else if($id=="14")
	                                                                            $asunto="Publi-Videos";
	                                                                            else if($id=="15")
	                                                                                $asunto="Publicidad en Agenda Minera";
	                                                                                else if($id=="16")
	                                                                                    $asunto="Ver Beneficio";
	                                                                                    else if($id=="17")
	                                                                                        $asunto="Interesado desde Noticia";
	                                                                                        else if($id=="18")
	                                                                                            $asunto="Socio Preferencial";
	                                                                                            else if($id=="19")
	                                                                                                $asunto="Socio Premium";
	                                                                                                else if($id=="20")
	                                                                                                    $asunto="Solicite una Ejecutiva";
	                                                                                                    else if($id=="21")
	                                                                                                        $asunto="Equipos en la Gran Miner&iacute;a";
	                                                                                                        else if($id=="22")
	                                                                                                            $asunto="Encabezado";
	                                                                                                            else if($id=="23")
	                                                                                                                $asunto="Pie de P&aacute;gina";
	                                                                                                                else if($id=="24")
	                                                                                                                    $asunto="La Comunidad";
	                                                                                                                    else if($id=="25")
	                                                                                                                        $asunto="Licitaciones y Adjudicaciones";
	                                                                                                                        else if($id=="26")
	                                                                                                                            $asunto="Licitaciones, Adjudicaciones o Proyectos (Fichas de Ejemplo)";
	                                                                                                                            else if($id=="27")
	                                                                                                                                $asunto="&iquest;Qu&eacute; es Miner&iacute;a de Datos?";
	                                                                                                                                else if($id=="28")
	                                                                                                                                    $asunto="Informe Cochilco";
	                                                                                                                                    else if($id=="29")
	                                                                                                                                        $asunto="SEMINARIOS ANTOFAGASTA INSCRIPCIONES";
	                                                                                                                                        else if($id=="30")
	                                                                                                                                            $asunto="Oportunidades de Negocios";
	                                                                                                                                            else if($id=="31")
	                                                                                                                                                $asunto="Hagase Socio";
	                                                                                                                                                else if($id=="32")
	                                                                                                                                                    $asunto="Librer&iacute;a";
	                                                                                                                                                    else if($id=="33")
	                                                                                                                                                        $asunto="Interesado en Estudio Empleador Favorito";
	                                                                                                                                                        else if($id=="34")
	                                                                                                                                                            $asunto="Nueva Herramienta para la Precalificaci&oacute;n de Proveedores";
	                                                                                                                                                            else if($id=="35")
	                                                                                                                                                                $asunto="Lo Nuevo En Portal Minero";
	                                                                                                                                                                else if($id=="36")
	                                                                                                                                                                    $asunto="Employer Branding";
	                                                                                                                                                                    else if($id=="37")
	                                                                                                                                                                        $asunto="Contacto Folleto";
	                                                                                                                                                                        else if($id=="38")
	                                                                                                                                                                            $asunto="Membres&iacute;a R&S";
	                                                                                                                                                                            else if($id=="39")
	                                                                                                                                                                                $asunto="CyberDay";
	                                                                                                                                                                                else if($id=="40")
	                                                                                                                                                                                    $asunto="Membres&iacute;a Negocios y R&S";
	                                                                                                                                                                                    else if($id=="41")
	                                                                                                                                                                                        $asunto="Membres&iacute;a Negocios";
	                                                                                                                                                                                        else if($id=="42")
	                                                                                                                                                                                            $asunto="Interesado Curso Norma NFPA 70-E";
	                                                                                                                                                                                            else
	                                                                                                                                                                                                $asunto="Contacto";
	                                                                                                                                                                                                //$this->email->subject("Interesado desde:  '".utf8_encode(html_entity_decode($asunto))."'");
	                                                                                                                                                                                                
	                                                                                                                                                                                                //$mail->Subject    = "Interesado desde:  '".utf8_encode(html_entity_decode($asunto))."'";  //Asunto del mensaje
	                                                                                                                                                                                                
	                                                                                                                                                                                                $asunto="'".$asunto."'";
	                                                                                                                                                                                                $body=str_replace("@tipo", $asunto, $body);
	                                                                                                                                                                                                
	            }else{
	                
	                
	                if($key=="boletin"){
	                    $key="Inscribir en Bolet&iacute;n";
	                    if(strval($val1)=="1"){
	                        $val1="SI";
	                    }else{
	                        $val1="NO";
	                    }
	                }
	                
	                if($key=="empresa_cli"){
	                    $key="Empresa a Contactar";
	                    if($val1=="" || $val1==NULL){
	                        $val1="Desconocido";
	                    }else{
	                        $asunto.=" - ".utf8_decode($val1);
	                    }
	                }
	                
					if($key=="nombres_cli"){
	                    $key="Nombre Cliente";
	                }
					
					
					if($key=="persona_contacto"){
							$key="Persona Contacto";
						}
						
					/*
	                if($key=="nombre"){
	                    $key="Nombre";
	                }
	                
	                if($key=="apellido"){
	                    $key="Apellido";
	                }
	                */
	                if($key=="pais"){
	                    $key="Pa&iacute;s";
	                    if($val1!=""){
	                        $pais=$this->pais->get_pais($val1);
	                        $val1=($pais->Nombre_pais);
	                    }
	                }
	                
	               
	                
	                if($key=="direccion"){
	                    $key="Direcci&oacute;n";
	                }
	                
	                if($key=="telefono"){
	                    $key="Tel&eacute;fono";
	                }
	                
	               /* if($key=="fax"){
	                    $key="Fax";
	                }*/
	                
	              /*  if($key=="persona_contacto"){
	                    $key="Persona Contacto";
	                }*/
	                
	                if($key=="email"){
	                    $key="Email";
	                    $email=$this->params->elimina_acentos(utf8_decode($val1));
	                    $val1=$this->params->elimina_acentos(utf8_decode($val1));
	                }
	                
	                if($key=="comentarios"){
	                    $key="Comentarios";
	                }
	                
	                if($key=="ciudad"){
	                    $key="Ciudad";
	                }
	                
	                $body.="<strong>".$key. "</strong>: ".htmlentities(utf8_decode($val1))."\n\n";
	            }
	        }
	           
	        
	        $this->db->insert("email_contacto", $arr);
	        $body.="\n\n<div style='float:right; clear:both;'><strong>Atte. portal Minero</strong></div>";
	    }
	    //$this->email->message(nl2br($body));
	    /*$body.='</body>';
	     $body.='</html>';*/
	    $asunto = "Interesado desde:  ".html_entity_decode($asunto);
	    $contenido = nl2br($body);
	    $destino = array();
	    if(isset($id_) && $id_==17){
	        //$this->email->to("cmorgado@portalminero.com", "Carolina Morgado");
	       // $mail->AddAddress("cmorgado@portalminero.com", "Carolina Morgado");
	    	$remitente = array($this->params->email_contacto, $this->params->nombre_contacto);
	        $destino ["inscripciones@portalminero.com"] = "Inscripciones";
			//$mail->AddAddress("epinto@portalminero.com", "Enrique");
	        $destino["info@portalminero.com"] = "Informaciones";
			
	    }else if(isset($id_) && $id_==29){
	        //$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
			//$this->email->to("bechegoyen@portalminero.com", "Bryan Echegoyen");
			$destino["inscripciones@portalminero.com"] = "Inscripciones";
	       // $mail->AddAddress("epinto@portalminero.com", "Enrique");
			$destino["info@portalminero.com"] = "Informaciones";
	       // $mail->AddAddress("epinto@portalminero.com", "Enrique Pinto");
	    }else{
	        //$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
			//$this->email->to("bechegoyen@portalminero.com", "Bryan Echegoyen");
			$destino = [$this->params->email_contacto] = $this->params->nombre_contacto;
		//	$mail->AddAddress("epinto@portalminero.com", "Enrique");
			//$mail->AddAddress("bechegoyen@portalminero.com", "bechegoyen@portalminero.com");
	    }
	    
	    //$this->email->from($email, $email);
	    /*$mail->SetFrom($email, $email);  //Quien envï¿½a el correo*/
	    if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
	        $dato=array("send"=>1, "url"=>$this->params->url_contacto);
	        $res=$this->load->view('confluence/contacto', $dato, true);
	    }else{
	        $estado="error";
	        $mensaje="No se pudo enviar el correo.";
	        $dato=array("send"=>0, "url"=>$this->params->url_contacto);
	        $res=$this->load->view('confluence/contacto', $dato, true);
	    }
	    echo $method."(".json_encode(array("estado"=>$estado, "mensaje"=>$mensaje, "result"=>$res)).")";
	}
	
	
	
	

	function procesa_contacto2($p=""){
		$body="";
		/*$this->load->library('email');
		$config['protocol'] = 'smtp';
		$config['wordwrap'] = TRUE;
		$config['smtp_host'] = 'smtp.portalminero.com';
		$config['smtp_user'] = 'info.portalminero';
		$config['smtp_pass'] = $this->params->pass_contacto;
		$config['mailtype'] = 'html';
		$config['charset']  = 'utf-8';
		$config['newline']  = "\r\n";
		$config['validate']  = FALSE;
		$this->email->initialize($config);*/

		if(isset($_GET["membresia"])){
			unset($_GET["membresia"]);
		}

		$method=$_GET["method"];
		unset($_GET["method"]);
		unset($_GET["_"]);
		$arr=$_GET;
		$estado="ok";
		$mensaje="";
		$res="";
		/*$body='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$body.='<html xmlns="http://www.w3.org/1999/xhtml">';
		$body.='<head>';
		$body.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$body.='<title>Contacto desde EL Portal</title>';
		$body.='</head>';
		$body.='<body>';
		*/
		if(is_array($arr) && sizeof($arr)>0){

			$valor=array();
			foreach($arr as $key=>$val1){
				if($key=="id"){
					$id_=intval($val1);
					$id=intval($val1);
					if($id=="0")
						$asunto="P&aacute;gina Principal";
					else if($id=="1")
						$asunto="Informaci&oacute;n de Proyectos";
					else if($id=="2")
						$asunto="Informaci&oacute;n de Licitaciones";
					else if($id=="3")
						$asunto="Empresas Mineras";
					else if($id=="4")
						$asunto="Estudios y Miner&iacute;a de Datos";
					else if($id=="5")
						$asunto="Articulaci&oacute;n de Negocios";
					else if($id=="6")
						$asunto="Reclutamiento y Selecci&oacute;n de Personal";
					else if($id=="7")
						$asunto="Capacitaci&oacute;n";
					else if($id=="8")
						$asunto="&aacute;reas de Capacitaci&oacute;n";
					else if($id=="9")
						$asunto="Certificaciones";
					else if($id=="10")
						$asunto="Banners";
					else if($id=="11")
						$asunto="Publicidad en el Bolet&iacute;n Semanal";
					else if($id=="12")
						$asunto="Publi-Reportajes";
					else if($id=="13")
						$asunto="Publicaci&oacute;n en Directorio";
					else if($id=="14")
						$asunto="Publi-Videos";
					else if($id=="15")
						$asunto="Publicidad en Agenda Minera";
					else if($id=="16")
						$asunto="Ver Beneficio";
					else if($id=="17")
						$asunto="Interesado desde Noticia";
					else if($id=="18")
						$asunto="Socio Preferencial";
					else if($id=="19")
						$asunto="Socio Premium";
					else if($id=="20")
						$asunto="Solicite una Ejecutiva";
					else if($id=="21")
						$asunto="Equipos en la Gran Miner&iacute;a";
					else if($id=="22")
						$asunto="Encabezado";
					else if($id=="23")
						$asunto="Pie de P&aacute;gina";
					else if($id=="24")
						$asunto="La Comunidad";
					else if($id=="25")
						$asunto="Licitaciones y Adjudicaciones";
					else if($id=="26")
						$asunto="Licitaciones, Adjudicaciones o Proyectos (Fichas de Ejemplo)";
					else if($id=="27")
						$asunto="&iquest;Qu&eacute; es Miner&iacute;a de Datos?";
					else if($id=="28")
						$asunto="Informe Cochilco";
					else if($id=="29")
						$asunto="SEMINARIOS ANTOFAGASTA INSCRIPCIONES";
					else if($id=="30")
						$asunto="Oportunidades de Negocios";
					else if($id=="31")
						$asunto="Hagase Socio";
					else if($id=="32")
						$asunto="Librer&iacute;a";
					else if($id=="33")
						$asunto="Interesado en Estudio Empleador Favorito";
					else if($id=="34")
						$asunto="Nueva Herramienta para la Precalificaci&oacute;n de Proveedores";
					else if($id=="35")
						$asunto="Lo Nuevo En Portal Minero";
					else if($id=="36")
						$asunto="Employer Branding";
					else if($id=="37")
						$asunto="Contacto Folleto";
					else if($id=="38")
						$asunto="Membres&iacute;a R&S";
					else if($id=="39")
						$asunto="CyberDay";
					else if($id=="40")
						$asunto="Membres&iacute;a Negocios y R&S";
					else if($id=="41")
						$asunto="Membres&iacute;a Negocios";
					else if($id=="42")
						$asunto="Interesado Curso Norma NFPA 70-E";
					else
						 $asunto="Contacto";
					//$this->email->subject("Interesado desde:  '".utf8_encode(html_entity_decode($asunto))."'");

					//$mail->Subject    = "Interesado desde:  '".utf8_encode(html_entity_decode($asunto))."'";  //Asunto del mensaje

					$asunto="'".$asunto."'";
					$body=str_replace("@tipo", $asunto, $body);

				}else{
					if($key=="boletin"){
						$key="Inscribir en Bolet&iacute;n";
						if(strval($val1)=="1"){
							$val1="SI";
						}else{
							$val1="NO";
						}
					}

					if($key=="empresa"){
						$key="Empresa a Contactar";
						if($val1=="" || $val1==NULL){
							$val1="Desconocido";
						}else{
							$asunto.=" - ".utf8_decode($val1);
						}
					}

					if($key=="nombre"){
						$key="Nombre";
					}

					if($key=="pais"){
						$key="Pa&iacute;s";
						if($val1!=""){
							$pais=$this->pais->get_pais($val1);
							$val1=($pais->Nombre_pais);
						}
					}

					if($key=="direccion"){
						$key="Direcci&oacute;n";
					}

					if($key=="telefono"){
						$key="Tel&eacute;fono";
					}

					if($key=="fax"){
						$key="Fax";
					}

					if($key=="personacontacto"){
						$key="Persona Contacto";
					}

					if($key=="persona_contacto"){
						$key="Persona Contacto";
					}
					
					
					
					if($key=="email"){
						$key="Email";
						$email=$this->params->elimina_acentos(utf8_decode($val1));
						$val1=$this->params->elimina_acentos(utf8_decode($val1));
					}

					if($key=="comentarios"){
						$key="Comentarios";
					}

					if($key=="ciudad"){
						$key="Ciudad";
					}

					$body.="<strong>".$key. "</strong>: ".htmlentities(utf8_decode($val1))."\n\n";
				}
			}
			$this->db->insert("email_contacto", $arr);
			$body.="\n\n<div style='float:right; clear:both;'><strong>Atte. Portal Minero</strong></div>";
		}
		//$this->email->message(nl2br($body));
		/*$body.='</body>';
		$body.='</html>';*/
		$asunto = "Interesado desde:  ".html_entity_decode($asunto);
		$contenido = nl2br($body);
		$destino = array();
		if(isset($id_) && $id_==17){
			//$this->email->to("cmorgado@portalminero.com", "Carolina Morgado");
			$destino["cmorgado@portalminero.com"] = "Carolina Morgado";
		}else if(isset($id_) && $id_==29){
			//$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
			//$this->email->to("bechegoyen@portalminero.com", "Bryan Echegoyen");
			$destino["inscripciones@portalminero.com"] = "Inscripciones";
		}else{
			//$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
			//$this->email->to("bechegoyen@portalminero.com", "Bryan Echegoyen");
			$destino[$this->params->email_contacto] = $this->params->nombre_contacto;
			//$mail->AddAddress("bechegoyen@portalminero.com", "bechegoyen@portalminero.com");
			$destino["epinto@portalminero.com"] = "EPF";
			$destino["cgarciam@portalminero.com"] = "CGM";
		}

		//$this->email->from($email, $email);
		$remitente = array($email, $email);  //Quien env�a el correo
		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
			$dato=array("send"=>1, "url"=>$this->params->url_contacto);
			$res=$this->load->view('confluence/contacto', $dato, true);
		}else{
			$estado="error";
			$mensaje="No se pudo enviar el correo.";
			$dato=array("send"=>0, "url"=>$this->params->url_contacto);
			$res=$this->load->view('confluence/contacto', $dato, true);
		}
		echo $method."(".json_encode(array("estado"=>$estado, "mensaje"=>$mensaje, "result"=>$res)).")";
	}

	function procesa_contacto($params){
		$body="";
		/*$this->load->library('email');
		$config['protocol'] = 'smtp';
		$config['wordwrap'] = TRUE;
		$config['smtp_host'] = 'smtp.portalminero.com';
		$config['smtp_user'] = 'info.portalminero';
		$config['smtp_pass'] = $this->params->pass_contacto;
		$config['mailtype'] = 'html';
		$config['charset']  = 'utf-8';
		$config['newline']  = "\r\n";
		$config['validate']  = FALSE;
		$this->email->initialize($config);*/

		$params=base64_decode(str_replace("_", "=", $params));
		$params=explode("-_-", $params);
		$arr=array();
		foreach($params as $p){
			$cent=explode("_",$p);
			$arr[]=array($cent[0]=>str_replace("%20", " ",$cent[1]));
		}
		/*$body='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$body.='<html xmlns="http://www.w3.org/1999/xhtml">';
		$body.='<head>';
		$body.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$body.='<title>Contacto desde EL Portal</title>';
		$body.='</head>';
		$body.='<body>';
		*/
		if(is_array($arr) && sizeof($arr)>0){

			$valor=array();
			foreach($arr as $val){
				foreach($val as $key=>$val1){

					if($key=="id"){
						$id_=intval($val1);
						$id=intval($val1);
						if($id=="0")
							$asunto="P&aacute;gina Principal";
						if($id=="1")
							$asunto="Informaci&oacute;n de Proyectos";
						if($id=="2")
							$asunto="Informaci&oacute;n de Licitaciones";
						if($id=="3")
							$asunto="Empresas Mineras";
						if($id=="4")
							$asunto="Estudios y Miner&iacute;a de Datos";
						if($id=="5")
							$asunto="Articulaci&oacute;n de negocios";
						if($id=="6")
							$asunto="Reclutamiento y Selecci&oacute;n de Personal";
						if($id=="7")
							$asunto="Capacitaci&oacute;n";
						if($id=="8")
							$asunto="&aacute;reas de Capacitaci&oacute;n";
						if($id=="9")
							$asunto="Certificaciones";
						if($id=="10")
							$asunto="Banners";
						if($id=="11")
							$asunto="Publicidad en el Bolet&iacute;n Semanal";
						if($id=="12")
							$asunto="Publi-Reportajes";
						if($id=="13")
							$asunto="Publicaci&oacute;n en Directorio";
						if($id=="14")
							$asunto="Publi-Videos";
						if($id=="15")
							$asunto="Publicidad en Agenda Minera";
						if($id=="16")
							$asunto="Ver Beneficio";
						if($id=="17")
							$asunto="Interesado desde Noticia";
						if($id=="18")
							$asunto="Socio Preferencial";
						if($id=="19")
							$asunto="Socio Premium";
						if($id=="20")
							$asunto="Solicite una Ejecutiva";
						if($id=="21")
							$asunto="Equipos en la Gran Miner&iacute;a";
						if($id=="22")
							$asunto="Encabezado";
						if($id=="23")
							$asunto="Pie de P&aacute;gina";
						if($id=="24")
							$asunto="La Comunidad";
						if($id=="25")
							$asunto="Licitaciones y Adjudicaciones";
						if($id=="26")
							$asunto="Licitaciones, Adjudicaciones o Proyectos (Fichas de Ejemplo)";
						if($id=="27")
                            $asunto="&iquest;Qu&eacute; es Miner&iacute;a de Datos?";
						if($id=="28")
                            $asunto="Informe Cochilco";
						if($id=="29")
                            $asunto="SEMINARIOS ANTOFAGASTA INSCRIPCIONES";
						if($id=="30")
                            $asunto="Oportunidades de Negocios";
						if($id=="31")
                            $asunto="Hagase Socio";

						//$this->email->subject("Interesado desde:  '".utf8_encode(html_entity_decode($asunto))."'");

						$asunto    = "Interesado desde:  '".html_entity_decode($asunto)."'";  //Asunto del mensaje
						$body=str_replace("@tipo", $asunto, $body);
					}else{
						if($key=="boletin"){
							$key="Inscribir en Bolet&iacute;n";
							if(strval($val1)=="1"){
								$val1="SI";
							}else{
								$val1="NO";
							}
						}

						if($key=="nombre"){
							$key="Nombre";
						}

						if($key=="pais"){
							$key="Pa&iacute;s";
							if($val1!=""){
								$pais=$this->pais->get_pais($val1);
								$val1=($pais->Nombre_pais);
							}
						}

						if($key=="direccion"){
							$key="Direcci&oacute;n";
						}

						if($key=="telefono"){
							$key="Tel&eacute;fono";
						}

						if($key=="fax"){
							$key="Fax";
						}

						if($key=="personacontacto"){
							$key="Persona Contacto";
						}

						if($key=="email"){
							$key="Email";
							$email=$this->params->elimina_acentos(utf8_decode(base64_decode(str_replace("-", "=", $val1))));
							$val1=$email;
						}

						if($key=="comentarios"){
							$key="Comentarios";
						}

						$body.="<strong>".$key. "</strong>: ".htmlentities(utf8_decode($val1))."\n\n";
					}
					$valor[$key]=$val1;
				}
			}
			$this->db->insert("email_contacto", $valor);
			$body.="\n\n<div style='float:right; clear:both;'><strong>Atte. portal Minero</strong></div>";
		}
		//$this->email->message(nl2br($body));
		/*$body.='</body>';
		$body.='</html>';*/
		$contenido = nl2br($body);
		$destino = array();
		if(isset($id_) && $id_==17){
			//$this->email->to("cmorgado@portalminero.com", "Carolina Morgado");
			$destino["cmorgado@portalminero.com"] = "Carolina Morgado";
		}else if(isset($id_) && $id_==29){
			//$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
			//$this->email->to("bechegoyen@portalminero.com", "Bryan Echegoyen");
			$destino["inscripciones@portalminero.com"] = "Inscripciones";
		}else{
			//$this->email->to($this->params->email_contacto, $this->params->nombre_contacto);
			//$this->email->to("bechegoyen@portalminero.com", "Bryan Echegoyen");
			$destino[$this->params->email_contacto] = $this->params->nombre_contacto;
			//$mail->AddAddress("bechegoyen@portalminero.com", "bechegoyen@portalminero.com");
		}

		//$this->email->from($email, $email);
		$remitente = array($email, $email);  //Quien env�a el correo
		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
			$dato=array("send"=>1, "url"=>$this->params->url_contacto);
			$this->load->view('confluence/contacto',$dato);
		}else{
			$dato=array("send"=>0, "url"=>$this->params->url_contacto);
			$this->load->view('confluence/contacto',$dato);
		}
	}

	public function procesa_informes($params){
		$body="";
		#PARAMETRIZACION DE LIBRERIA EMAIL
		
		$asunto    = "Inscripción Nueva";  //Asunto del mensaje
		#FIN PARAMETRIZACION
		$remitente = array($this->params->email_contacto, $this->params->nombre_contacto);
		$destino = array("inscripciones@portalminero.com"=>"Inscripciones","bvidal@portalminero.com"=>"Barinia Vidal");

		$params=base64_decode(str_replace("_", "=", $params));
		$params=explode("-_-", $params);
		$arr=array();

		foreach($params as $p){
			$cent=explode("_",$p);
			$arr[]=array($cent[0]=>str_replace("%20", " ",$cent[1]));
		}

		if(is_array($arr) && sizeof($arr)>0){
			$body="Se ha Creado una solicitud de \"Informes a Pedido\" con la siguiente Informaci&oacute;n:\n\n";
			foreach($arr as $val){
				foreach($val as $key=>$val){

					if($key=="username"){
						if($soc=$this->confluence->cargar_socio(base64_decode($val))){
							$dato["socio"]=$soc;
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n de Usuario:</strong></div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Remitente:</strong> ".htmlentities(utf8_decode($soc->nombre_completo_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Email:</strong> ".htmlentities(utf8_decode($soc->email_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Fono:</strong> ".htmlentities(utf8_decode($soc->fono_user_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n Ingresada:</strong></div>";
						}
					}else{

						if($key=="requerimiento"){
							$key="Requerimiento";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}
						}

						if($key=="objetivo"){
							$key="Objetivo de la Investigaci&oacute;n";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}
						}

						if($key=="alcance"){
							$key="Alcances del Estudio";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}
						}

						if($key=="plazos"){
							$key="Plazos";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}
						}

						if($key=="comentarios"){
							$key="Comentarios";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}
						}

						$body.="<div style='clear:both; float:left; width:100%;'><div style='float:left;'><strong>".$key. "</strong>:</div><div style='float:left; text-align:justify;'> ".htmlentities(utf8_decode($val))."</div></div>\n";
					}
				}
			}
			$body.="\n\n\n<div style='float:right; clear:both;'><strong>Atte. Portal Minero</strong></div>";
		}

		$contenido= nl2br($body);

		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
			$dato["send"]="1";
			$this->load->view('confluence/informes',$dato);
		}
	}

	public function procesa_informar_cambios($params){
		$body="";

		#PARAMETRIZACION DE LIBRERIA EMAIL
		$asunto = "Inscripción Nueva";  //Asunto del mensaje
		#FIN PARAMETRIZACION

		$remitente = array($this->params->email_contacto, $this->params->nombre_contacto);
		$destino = array("bvidal@portalminero.com" => "Barinia Vidal");

		//$this->email->cc("bechegoyen@portalminero.com", "beche");
		$params=base64_decode(str_replace("_", "=", $params));
		$params=explode("-_-", $params);
		$arr=array();

		foreach($params as $p){
			$cent=explode("_",$p);
			$arr[]=array($cent[0]=>str_replace("%20", " ",$cent[1]));
		}

		if(is_array($arr) && sizeof($arr)>0){
			$body="Se ingreso Informaci&oacute;n Nueva Sobre Cambios en el Proyecto <strong>\"@nombrepro\"</strong>:\n\n";
			foreach($arr as $val){
				foreach($val as $key=>$val){
					if($key=="idpagina"){
						$pro=$this->ficha->buscar_por_pag($val);
						$body=str_replace("@nombrepro", htmlentities(utf8_decode($pro->Nombre_pro)), $body);
					}
					if($key=="idedita"){
						$user=$this->user->busca_user_full($val);
						if(is_object($user)){
							$this->email->to($user->Nombre_user."@portalminero.com", $user->Nombre_completo_user);
						}
					}


					if($key=="username"){
						if($soc=$this->confluence->cargar_socio(base64_decode($val))){
							$dato["socio"]=$soc;
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n de Usuario:</strong></div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Remitente:</strong> ".htmlentities(utf8_decode($soc->nombre_completo_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Email:</strong> ".htmlentities(utf8_decode($soc->email_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Fono:</strong> ".htmlentities(utf8_decode($soc->fono_user_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n Ingresada:</strong></div>";
						}
					}

					if($key=="cambios"){
						$key="Cambios";
						$body.="<div style='clear:both; float:left; width:100%;'><div style='float:left;'><strong>".$key. "</strong>:</div><div style='float:left; text-align:justify;'> ".htmlentities(utf8_decode($val))."</div></div>\n";
					}
				}
			}

			//$body.="\n\n\n<div style='float:right; clear:both;'><strong>Atte. Portal Minero</strong></div>";
		}

		$contenido = nl2br($body);
		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
			$dato["send"]="1";
			$this->load->view('confluence/informar_cambios',$dato);
		}
	}

	public function procesa_sugerencias($params){
		$body="";

		#PARAMETRIZACION DE LIBRERIA EMAIL

		#FIN PARAMETRIZACION

		$asunto = "Sugerencia Socio";
		$remitente = array($this->params->email_contacto, $this->params->nombre_contacto); 
		$destino = array($this->params->email_contacto => $this->params->nombre_contacto);
		//$this->email->cc("bechegoyen@portalminero.com", "beche");
		$params=base64_decode(str_replace("_", "=", $params));
		$params=explode("-_-", $params);
		$arr=array();

		foreach($params as $p){
			$cent=explode("_",$p);
			$arr[]=array($cent[0]=>str_replace("%20", " ",$cent[1]));
		}

		if(is_array($arr) && sizeof($arr)>0){
			$body="Se Ingreso una Nueva Sugerencia:\n\n";
			foreach($arr as $val){
				foreach($val as $key=>$val){
					if($key=="username"){
						if($soc=$this->confluence->cargar_socio(base64_decode($val))){
							$dato["socio"]=$soc;
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n de Usuario:</strong></div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Remitente:</strong> ".htmlentities(utf8_decode($soc->nombre_completo_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Email:</strong> ".htmlentities(utf8_decode($soc->email_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Fono:</strong> ".htmlentities(utf8_decode($soc->fono_user_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n Ingresada:</strong></div>";
						}
					}
					if($key=="sugerencias"){
						$key="Sugerencias";
						$body.="<div style='clear:both; float:left; width:100%;'><div style='float:left;'><strong>".$key. "</strong>:</div><div style='float:left; text-align:justify;'> ".htmlentities(utf8_decode($val))."</div></div>\n";
					}
				}
			}

			//$body.="\n\n\n<div style='float:right; clear:both;'><strong>Atte. Portal Minero</strong></div>";
		}

		$contenido = nl2br($body);

		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
			$dato["send"]="1";
			$this->load->view('confluence/sugerencias',$dato);
		}
	}

	public function procesa_articulacion($params){
		$body="";

		#PARAMETRIZACION DE LIBRERIA EMAIL
		
		#FIN PARAMETRIZACION

		$asunto = "Articulación de Negocios";

		$remitente = array($this->params->email_contacto, $this->params->nombre_contacto);
		$destino = array($this->params->email_contacto => $this->params->nombre_contacto,"bvidal@portalminero.com" => "Barinia Vidal");

		//$this->email->to("bechegoyen@portalminero.com", "beche");
		$params=base64_decode(str_replace("_", "=", $params));
		$params=explode("-_-", $params);
		$arr=array();
		foreach($params as $p){
			$cent=explode("_",$p);
			$arr[]=array($cent[0]=>str_replace("%20", " ",$cent[1]));
		}
		if(is_array($arr) && sizeof($arr)>0){
			$body="Se ha Creado una solicitud de \"Articulaci&oacute;n de Negocios\" con la siguiente Informaci&oacute;n:\n\n";
			foreach($arr as $val){
				foreach($val as $key=>$val){

					if($key=="username"){
						if($soc=$this->confluence->cargar_socio(base64_decode($val))){
							$dato["socio"]=$soc;
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n de Usuario:</strong></div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Remitente:</strong> ".htmlentities(utf8_decode($soc->nombre_completo_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Email:</strong> ".htmlentities(utf8_decode($soc->email_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Fono:</strong> ".htmlentities(utf8_decode($soc->fono_user_socio))."</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'>&nbsp;</div>";
							$body.="<div style='clear:both; float:left; width:100%;'><strong>Informaci&oacute;n Ingresada:</strong></div>";
						}
					}else{
						if($key=="empresa"){
							$key="Empresa a Contactar";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}
						}

						if($key=="persona"){
							$key="Persona";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}
						}

						if($key=="cargo"){
							$key="Cargo";
							if($val=="" || $val==NULL){
								$val="Desconocido";
							}

						}

						if($key=="comentario"){
							$key="Comentario";
						}

						$body.="<div style='clear:both; float:left; width:100%;'><div style='float:left;'><strong>".$key. "</strong>:</div><div style='float:left; text-align:justify;'> ".htmlentities(utf8_decode($val))."</div></div>\n";
					}
				}
			}
			$body.="\n\n\n<div style='float:right; clear:both;'><strong>Atte. Portal Minero</strong></div>";
		}

		$contenido = nl2br($body);

		if($this->params->enviar_correo($remitente,$clear_address="",$clear_cc="",$destino,$destino_cc="",$destino_bcc="",$contenido,$asunto)){
			$dato["send"]="1";
			$this->load->view('confluence/articulacion',$dato);
		}
	}

	public function procesa($param, $username="%20"){
		
		$param=base64_decode(str_replace("-", "=", $param));
		$p1=str_replace("--", "/", str_replace("-----", "-_-",$param));
		$p1=explode("/", $p1);
		
		
		
		
		if($p1[0]=="listar_pro"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->listar_pro($username, $p1[1], $p1[2], $p1[3]);
		}else if($p1[0]=="listar_adj"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->listar_adj($username, $p1[1], $p1[2], $p1[3]);
		}else if($p1[0]=="listar_lic"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->listar_lic($username, $p1[1], $p1[2], $p1[3]);
		}else if($p1[0]=="guardar_rubros"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->guardar_rubros($p1[0], $p1[1]);
		}else if($p1[0]=="guardar_sectores"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->guardar_sectores($p1[0], $p1[1]);
		}else if($p1[0]=="listar_pro_pub"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->listar_pro_pub($p1[0]);
		}else if($p1[0]=="listar_lic_pub"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->listar_lic_pub($p1[0]);
		}else if($p1[0]=="listar_adj_pub"){
			unset($p1[0]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			if(!isset($p1[3])){
				$p1[3]="";
			}
			$this->listar_adj_pub($p1[0]);
		}else if($p1[1]=="listar_pro"){
			unset($p1[0]);
			unset($p1[1]);
			$p1=implode("/", $p1);
			$p1=explode("/", $p1);
			$this->listar_pro($p1[0], $p1[1], $p1[2], $p1[3]);
		}
	}

	public function test(){
		$arr=array("url"=>"confluence##listar_pro##".$username."##0", "search"=>"");
		echo(str_replace("=", "-", base64_encode(json_encode($arr))));
	}

	public function seleccionar_rubros($username, $id_sector="", $where2=""){
		$datos_user=$this->confluence->datos_user($username);
		$id_user=$datos_user->id_user_socio;
		$lista_rubros=$this->confluence->lista_rubros($id_user);
		$rubros_select=$this->confluence->rubros_select($id_user);
		$dato['lista_rubros']=$lista_rubros;
		$dato['rubros_select']=$rubros_select;
		$dato['username']=$username;
		$this->load->view('confluence/seleccionar_rubros',$dato);
	}

	public function seleccionar_sectores($username, $id_sector="", $where2=""){
		$datos_user=$this->confluence->datos_user($username);
		$id_user=$datos_user->id_user_socio;
		$datos_socio=$this->confluence->tipo_socio($username);
		$lista_sectores=$this->confluence->lista_sectores($id_user, $datos_socio);
		$sectores_select=$this->confluence->sectores_select($id_user,$datos_user->id_socio);
		$dato['lista_sectores']=$lista_sectores;
		$dato['sectores_select']=$sectores_select;
		$dato['username']=$username;
		$this->load->view('confluence/seleccionar_sectores',$dato);
	}

	public function guardar_rubros($username,$array_rubros=''){
		if($array_rubros!="" && $array_rubros!=NULL){
			$arr1=explode("-", $array_rubros);
			$datos=array();
			if(is_array($arr1) && sizeof($arr1)>0){
				foreach($arr1 as $ar){
					$dt=explode("_", $ar);
					$datos[]=$dt[1];
				}
			}
			$datos_user=$this->confluence->datos_user($username);
			$id_user=$datos_user->id_user_socio;
			$array_rubros=$datos;
			if($array_rubros){
				foreach($array_rubros as $rubros){
					if(!$this->confluence->verificar_rubro($id_user,$rubros)){
						$id_user_rubro=$this->confluence->ingresar_user_rubro($id_user,$rubros);
					}
				}
				$lista_rubros_guardados=$this->confluence->rubros_user($id_user);

				foreach($lista_rubros_guardados as $rubros_ant){
					if((!in_array($rubros_ant,$array_rubros))&&(is_array($array_rubros))){
						$this->confluence->borrar_user_rubro($rubros_ant,$id_user);
					}
				}
				$this->seleccionar_rubros($username);
			}
		}else{
			$datos_user=$this->confluence->datos_user($username);
			$id_user=$datos_user->id_user_socio;
			$lista_rubros_guardados=$this->confluence->rubros_user($id_user);

			foreach($lista_rubros_guardados as $rubros_ant){
				$this->confluence->borrar_user_rubro($rubros_ant,$id_user);
			}
			$this->seleccionar_rubros($username);
		}
	}

	public function guardar_sectores($username,$array_sectores=''){
		if($array_sectores!="" && $array_sectores!=NULL){
			$arr1=explode("-", $array_sectores);
			$datos=array();
			if(is_array($arr1) && sizeof($arr1)>0){
				foreach($arr1 as $ar){
					$dt=explode("_", $ar);
					$datos[]=$dt[1];
				}
			}
			$datos_user=$this->confluence->datos_user($username);
			$id_user=$datos_user->id_user_socio;
			$array_sectores=$datos;
			if($array_sectores){
				foreach($array_sectores as $sector){
					if(!$this->confluence->verificar_sector($id_user,$sector)){
						$id_user_sector=$this->confluence->ingresar_user_sector($id_user,$sector);
					}
				}
				$lista_sectores_guardados=$this->confluence->sectores_user($id_user);

				foreach($lista_sectores_guardados as $sector_ant){
					if((!in_array($sector_ant,$array_sectores))&&(is_array($array_sectores))){
						$this->confluence->borrar_user_sector($sector_ant,$id_user);
					}
				}
				$this->seleccionar_sectores($username);
			}
		}else{
			$datos_user=$this->confluence->datos_user($username);
			$id_user=$datos_user->id_user_socio;
			$lista_sectores_guardados=$this->confluence->sectores_user($id_user);

			foreach($lista_sectores_guardados as $sector_ant){
				$this->confluence->borrar_user_sector($sector_ant,$id_user);
			}
			$this->seleccionar_sectores($username);
		}

	}

	public function listar_pro_ajx($username=""){
		//$this->output->enable_profiler(true);
		$estado="ok";
		$mensaje="";
		$method=(isset($_GET["method"]) ? $_GET["method"] : "");
		$id_sector=(isset($_GET["id_sector"]) ? $_GET["id_sector"] : "");
		$nro_pag=(isset($_GET["nro_pag"]) ? $_GET["nro_pag"] : "");
		unset($_GET["_"]);
		unset($_GET["method"]);
		$arr=$_GET;
		
		$username=base64_decode(str_replace("_", "=", $username));
		if($id_sector!=0)
			$sector=$this->sector->mostrar($id_sector);
		else
			$sector[0]->Nombre_sector="Todos los Sectores";
		if($username!=""){

			$dt=$this->confluence->listar_proyectos_ajx($username, $id_sector, $nro_pag, $arr);
			$jsparam=$this->confluence->generar_js_proyectos($dt[0], $username);
			if(is_array($dt[0]) && sizeof($dt[0])>0){
				$x=0;
				$paginador=$this->params->paginador($nro_pag, $dt[1]);
				$datos["paginador"]=$paginador;
				$datos["proyectos"]=$dt[0];
			}else{
				$datos["proyectos"]=array();
			}
			$sel_equipo=(isset($arr["id_equipo"]) ? $arr["id_equipo"] : "" );
			$sel_servicio=(isset($arr["id_serv"]) ? $arr["id_serv"] : "" );
			$sel_suministro=(isset($arr["id_sumin"]) ? $arr["id_sumin"] : "" );
			$sel_obra=(isset($arr["id_obra"]) ? $arr["id_obra"] : "" );
			$sel_tipo=(isset($arr["id_tipo"]) ? $arr["id_tipo"] : "" );
			$sel_pais=(isset($arr["id_pais"]) ? $arr["id_pais"] : "" );
			$sel_region=(isset($arr["id_region"]) ? $arr["id_region"] : "" );
			$sel_etapa=(isset($arr["id_etapa"]) ? $arr["id_etapa"] : "" );
			$sel_empresa=(isset($arr["id_responsable"]) ? $arr["id_responsable"] : "" );
			$sel_mandante=(isset($arr["id_man_emp"]) ? $arr["id_man_emp"] : "" );

			$servicio=$this->ficha->servicios_sel($dt[2], $id_sector, $username);
			unset($servicio[""]);
			$equipo=$this->ficha->equipos_sel($dt[2], $id_sector, $username);
			unset($equipo[""]);
			$suministro=$this->ficha->suministro_sel($dt[2], $id_sector, $username);
			unset($suministro[""]);
			$obra=$this->ficha->obras_sel($dt[2], $id_sector, $username);
			unset($obra[""]);
			$tipo=$this->ficha->tipo_sel($dt[2], $id_sector, $username);
			unset($tipo[""]);
			$etapa=$this->etapa->listar_etapas_all_cbo();
			unset($etapa[""]);
			$pais= $this->ficha->pais_sel($dt[2], $id_sector, $username);
			unset($pais[""]);
			$empresa= $this->ficha->responsable_sel($dt[2], $id_sector, $username);
			unset($empresa[""]);
			$region= $this->region->llenar_combo_region_chile();
			unset($region[""]);
			$mandante= $this->ficha->mandante_sel($dt[2], $id_sector, $username);
			unset($mandante[""]);
			$servicio=array("arr"=>$servicio, "var"=>$sel_servicio);
			$datos["servicio"]=$servicio;
			$equipo=array("arr"=>$equipo, "var"=>$sel_equipo);
			$datos["equipo"]=$equipo;
			$suministro=array("arr"=>$suministro, "var"=>$sel_suministro);
			$datos["suministro"]=$suministro;
			$obra=array("arr"=>$obra, "var"=>$sel_obra);
			$datos["obra"]=$obra;
			$tipo=array("arr"=>$tipo, "var"=>$sel_tipo);
			$datos["tipo"]=$tipo;
			$pais=array("arr"=>$pais, "var"=>$sel_pais);
			$datos["pais"]=$pais;
			$region=array("arr"=>$region, "var"=>$sel_region);
			$datos["region"]=$region;
			$etapa=array("arr"=>$etapa, "var"=>$sel_etapa);
			$datos["etapa"]=$etapa;
			$empresa=array("arr"=>$empresa, "var"=>$sel_empresa);
			$datos["empresa"]=$empresa;
			$mandante=array("arr"=>$mandante, "var"=>$sel_mandante);
			$datos["mandante"]=$mandante;
			$datos["actual"]=$nro_pag;
			$datos["id_sector"]=$id_sector;
			$datos["estado_pro"]=$sel_estado;
			if($id_sector!=0)
				$datos["nombre_sector"]=" Sector ".$sector[0]->Nombre_sector;
			else
				$datos["nombre_sector"]=$sector[0]->Nombre_sector;
			$selected="";
			if($mandante["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$mandante["arr"][$mandante["var"]];
				}else{
					$selected.=", ".$mandante["arr"][$mandante["var"]];
				}
			}
			if($pais["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$pais["arr"][$pais["var"]];
				}else{
					$selected.=", ".$pais["arr"][$pais["var"]];
				}
			}
			if($region["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$region["arr"][$region["var"]];
				}else{
					$selected.=", ".$region["arr"][$region["var"]];
				}
			}
			if($obra["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$obra["arr"][$obra["var"]];
				}else{
					$selected.=", ".$obra["arr"][$obra["var"]];
				}
			}
			if($equipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$equipo["arr"][$equipo["var"]];
				}else{
					$selected.=", ".$equipo["arr"][$equipo["var"]];
				}
			}
			if($suministro["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$suministro["arr"][$suministro["var"]];
				}else{
					$selected.=", ".$suministro["arr"][$suministro["var"]];
				}
			}
			if($servicio["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$servicio["arr"][$servicio["var"]];
				}else{
					$selected.=", ".$servicio["arr"][$servicio["var"]];
				}
			}
			if($etapa["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$etapa["arr"][$etapa["var"]];
				}else{
					$selected.=", ".$etapa["arr"][$etapa["var"]];
				}
			}
			if($empresa["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$empresa["arr"][$empresa["var"]];
				}else{
					$selected.=", ".$empresa["arr"][$empresa["var"]];
				}
			}
			if($tipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$tipo["arr"][$tipo["var"]];
				}else{
					$selected.=", ".$tipo["arr"][$tipo["var"]];
				}
			}
			$datos["selected"]=$selected;
			$datos["sectores"]=$this->params->name_sectores;
			$html=$this->load->view('confluence/listar_pro_ajx',$datos, true);
			echo $method."(".json_encode(array("estado"=>$estado, "mensaje"=>$mensaje, "html"=>$html)).")";
		}
	}

	public function listar_pro($username="", $id_sector="", $nro_pag, $where2=""){
		//$this->output->enable_profiler(true);
		if($id_sector!=0)
			$sector=$this->sector->mostrar($id_sector);
		else{
			$sector=array();
			$sector[0]=new stdClass();
			$sector[0]->Nombre_sector="Todos los Sectores";
		}
		
		
		
		if($username!=""){
			$sel_servicio="";
			$sel_equipo="";
			$sel_suministro="";
			$sel_obra="";
			$sel_tipo="";
			$sel_pais="";
			$sel_region="";
			$sel_etapa="";
			$sel_empresa="";
			$sel_mandante="";
			$txt_nombre="";
			$sel_estado="";
			$ordernombre="asc";
			$orderfecha="desc";
			$orderinversion="desc";
			if($where2!=""){
				if($where2!="*"){
					$where2=str_replace("-_-", " ", $where2);
					$search=explode("-",$where2);
					$where="";
					foreach($search as $x=>$f){

						$v=explode("_", $f);
						/*Condicional que gestiona los enlaces de los totales de la tabla de proyecto JOMP*/
						if($v[0]=="tabla"){
							$v[0]="estado";
							$sel_estado=$v[1];
							$where2="estado_".$v[1];
							break;
						}
						/***********JOMP*******************/
						if($v[0]=="pais"){
							$sel_pais=$v[1];
						}

						if($v[0]=="region"){
							$sel_region=$v[1];
						}

						if($v[0]=="obra"){
							$sel_obra=$v[1];
						}

						if($v[0]=="equipo"){
							$sel_equipo=$v[1];
						}

						if($v[0]=="suministro"){
							$sel_suministro=$v[1];
						}

						if($v[0]=="servicio"){
							$sel_servicio=$v[1];
						}

						if($v[0]=="etapa"){
							$sel_etapa=$v[1];
						}

						if($v[0]=="responsable"){
							$sel_empresa=$v[1];
						}

						if($v[0]=="tipo"){
							$sel_tipo=$v[1];
						}

						if($v[0]=="mandante"){
							$sel_mandante=$v[1];
						}

						if($v[0]=="nombre"){
							$txt_nombre=str_replace("-_-", " ", $v[1]);
						}

						if($v[0]=="estado"){
							$sel_estado=$v[1];
						}

						if($v[0]=="ordernombre"){
							if($v[1]=="asc")
								$ordernombre="desc";
							else
								$ordernombre="asc";
							$order="ordernombre_".$v[1];
						}

						if($v[0]=="orderfecha"){
							if($v[1]=="asc")
								$orderfecha="desc";
							else
								$orderfecha="asc";
							$order="orderfecha_".$v[1];
						}

						if($v[0]=="orderinversion"){
							if($v[1]=="asc")
								$orderinversion="desc";
							else
								$orderinversion="asc";
							$order="orderinversion_".$v[1];
						}
					}
				}
			}

			$dt=$this->confluence->listar_proyectos($username, $id_sector, $nro_pag, $where2);

			$jsparam=$this->confluence->generar_js_proyectos($dt[0], $username);
			if(is_array($dt[0]) && sizeof($dt[0])>0){
				$x=0;
				$paginador=$this->params->paginador($nro_pag, $dt[1]);
				$datos["paginador"]=$paginador;
				$datos["proyectos"]=$dt[0];
			}else{
				$datos["proyectos"]=array();
			}
	
		
			$servicio=$this->ficha->servicios_sel($dt[2], $id_sector, $username);
			unset($servicio[""]);
			$equipo=$this->ficha->equipos_sel($dt[2], $id_sector, $username);
			unset($equipo[""]);
			$suministro=$this->ficha->suministro_sel($dt[2], $id_sector, $username);
			unset($suministro[""]);
			$obra=$this->ficha->obras_sel($dt[2], $id_sector, $username);
			unset($obra[""]);
			$tipo=$this->ficha->tipo_sel($dt[2], $id_sector, $username);
			unset($tipo[""]);
			$etapa=$this->etapa->listar_etapas_all_cbo();
			unset($etapa[""]);
			$pais= $this->ficha->pais_sel($dt[2], $id_sector, $username);
			unset($pais[""]);
			$empresa= $this->ficha->responsable_sel($dt[2], $id_sector, $username);
			unset($empresa[""]);
			$region= $this->region->llenar_combo_region_chile();
			unset($region[""]);
			$mandante= $this->ficha->mandante_sel($dt[2], $id_sector, $username);
			unset($mandante[""]);
			$servicio=array("arr"=>$servicio, "var"=>$sel_servicio);
			$datos["nombre"]=array("value"=>$txt_nombre, "name"=>"nombre", "class"=>"nombre", "style"=>"width:100%;","placeholder"=>"Buscar en Proyectos de ".$sector[0]->Nombre_sector);
			$datos["servicio"]=$servicio;
			$equipo=array("arr"=>$equipo, "var"=>$sel_equipo);
			$datos["equipo"]=$equipo;
			$suministro=array("arr"=>$suministro, "var"=>$sel_suministro);
			$datos["suministro"]=$suministro;
			$obra=array("arr"=>$obra, "var"=>$sel_obra);
			$datos["obra"]=$obra;
			$tipo=array("arr"=>$tipo, "var"=>$sel_tipo);
			$datos["tipo"]=$tipo;
			$pais=array("arr"=>$pais, "var"=>$sel_pais);
			$datos["pais"]=$pais;
			$region=array("arr"=>$region, "var"=>$sel_region);
			$datos["region"]=$region;
			$etapa=array("arr"=>$etapa, "var"=>$sel_etapa);
			$datos["etapa"]=$etapa;
			$empresa=array("arr"=>$empresa, "var"=>$sel_empresa);
			$datos["empresa"]=$empresa;
			$mandante=array("arr"=>$mandante, "var"=>$sel_mandante);
			$datos["mandante"]=$mandante;
			$datos["actual"]=$nro_pag;
			$datos["id_sector"]=$id_sector;
			$datos["estado_pro"]=$sel_estado;
			if(isset($order))
				$datos["order"]=$order;

			$datos["ordernombre"]=$ordernombre;
			$datos["orderfecha"]=$orderfecha;
			$datos["orderinversion"]=$orderinversion;

			if($id_sector!=0)
				$datos["nombre_sector"]=" Sector ".$sector[0]->Nombre_sector;
			else
				$datos["nombre_sector"]=$sector[0]->Nombre_sector;
			$selected="";
			if($mandante["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$mandante["arr"][$mandante["var"]];
				}else{
					$selected.=", ".$mandante["arr"][$mandante["var"]];
				}
			}
			if($pais["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$pais["arr"][$pais["var"]];
				}else{
					$selected.=", ".$pais["arr"][$pais["var"]];
				}
			}
			if($region["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$region["arr"][$region["var"]];
				}else{
					$selected.=", ".$region["arr"][$region["var"]];
				}
			}
			if($obra["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$obra["arr"][$obra["var"]];
				}else{
					$selected.=", ".$obra["arr"][$obra["var"]];
				}
			}
			if($equipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$equipo["arr"][$equipo["var"]];
				}else{
					$selected.=", ".$equipo["arr"][$equipo["var"]];
				}
			}
			if($suministro["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$suministro["arr"][$suministro["var"]];
				}else{
					$selected.=", ".$suministro["arr"][$suministro["var"]];
				}
			}
			if($servicio["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$servicio["arr"][$servicio["var"]];
				}else{
					$selected.=", ".$servicio["arr"][$servicio["var"]];
				}
			}
			if($etapa["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$etapa["arr"][$etapa["var"]];
				}else{
					$selected.=", ".$etapa["arr"][$etapa["var"]];
				}
			}
			if($empresa["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$empresa["arr"][$empresa["var"]];
				}else{
					$selected.=", ".$empresa["arr"][$empresa["var"]];
				}
			}
			if($tipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$tipo["arr"][$tipo["var"]];
				}else{
					$selected.=", ".$tipo["arr"][$tipo["var"]];
				}
			}

			if($txt_nombre!=""){
				if($selected==""){
					$selected.="Mostrando licitaciones que contienen: ".urldecode($txt_nombre);
				}else{
					$selected.=", ".urldecode($txt_nombre);
				}
			}
			if($sel_estado!=""){/*JOMP imprimir el estado de los proyectos desde*/
				if ($sel_estado=="A"){
					$estadoProyecto="Proyectos Activos";
				}
				elseif ($sel_estado=="F"){
					$estadoProyecto="Proyectos Activos Diferidos";
				}
				elseif ($sel_estado=="P"){
					$estadoProyecto="Proyectos Paralizados";	
				}
				elseif ($sel_estado=="O"){
					$estadoProyecto="Proyectos En Operación";
				}
				elseif ($sel_estado=="D"){
					$estadoProyecto="Proyectos Desistidos";
				}
				if($selected==""){
					$selected.="Mostrando resultados según los criterios: ".$estadoProyecto;
				}else{
					$selected.=", ".$estadoProyecto;
				}

			}/*JOMP fin de impresiòn de estado de los proyectos:-)*/



			$datos["selected"]=$selected;
			$datos["sectores"]=$this->params->name_sectores;
			
		
		
			$this->load->view('confluence/listar_pro',$datos);
		}
	}

	public function listar_pro2($username="", $id_sector="", $nro_pag, $where2=""){
		//$ec=str_replace("-", "=", $ec);
		//echo base64_decode($ec)."<br>"; die();
		if($id_sector!=0)
			$sector=$this->sector->mostrar($id_sector);
		else
			$sector[0]->Nombre_sector="Todos los Sectores";
		if($username!=""){
			$sel_servicio="";
			$sel_equipo="";
			$sel_suministro="";
			$sel_obra="";
			$sel_tipo="";
			$sel_pais="";
			$sel_region="";
			$sel_etapa="";
			$sel_empresa="";
			$sel_mandante="";
			$txt_nombre="";
			if($where2!=""){
				if($where2!="*"){
					$where2=str_replace("-_-", " ", $where2);
					$search=explode("-",$where2);
					$where="";
					foreach($search as $x=>$f){

						$v=explode("_", $f);
						if($v[0]=="pais"){
							$sel_pais=$v[1];
						}
						if($v[0]=="region"){
							$sel_region=$v[1];
						}
						if($v[0]=="obra"){
							$sel_obra=$v[1];
						}
						if($v[0]=="equipo"){
							$sel_equipo=$v[1];
						}
						if($v[0]=="suministro"){
							$sel_suministro=$v[1];
						}
						if($v[0]=="servicio"){
							$sel_servicio=$v[1];
						}
						if($v[0]=="etapa"){
							$sel_etapa=$v[1];
						}
						if($v[0]=="responsable"){
							$sel_empresa=$v[1];
						}
						if($v[0]=="tipo"){
							$sel_tipo=$v[1];
						}
						if($v[0]=="mandante"){
							$sel_mandante=$v[1];
						}

						if($v[0]=="mandante"){
							$sel_mandante=$v[1];
						}

						if($v[0]=="nombre"){
							$txt_nombre=str_replace("-_-", " ", $v[1]);
						}
					}
				}
			}

			$dt=$this->confluence->listar_proyectos($username, $id_sector, $nro_pag, $where2);
			if(is_array($dt[0]) && sizeof($dt[0])>0){
				$x=0;

				$paginador=$this->params->paginador($nro_pag, $dt[1]);
				$datos["paginador"]=$paginador;
				$datos["proyectos"]=$dt[0];
				$datos["jsfile"]=$jsparam;
			}else{
				$datos["proyectos"]=array();
			}

			$servicio=$this->ficha->servicios_sel($dt[2], $id_sector, $username);
			unset($servicio[""]);
			$equipo=$this->ficha->equipos_sel($dt[2], $id_sector, $username);
			unset($equipo[""]);
			$suministro=$this->ficha->suministro_sel($dt[2], $id_sector, $username);
			unset($suministro[""]);
			$obra=$this->ficha->obras_sel($dt[2], $id_sector, $username);
			unset($obra[""]);
			$tipo=$this->ficha->tipo_sel($dt[2], $id_sector, $username);
			unset($tipo[""]);
			$etapa=$this->etapa->listar_etapas_all_cbo();
			unset($etapa[""]);
			$pais= $this->ficha->pais_sel($dt[2], $id_sector, $username);
			unset($pais[""]);
			$empresa= $this->ficha->responsable_sel($dt[2], $id_sector, $username);
			unset($empresa[""]);
			$region= $this->region->llenar_combo_region_chile();
			unset($region[""]);
			$mandante= $this->ficha->mandante_sel($dt[2], $id_sector, $username);
			unset($mandante[""]);
			$servicio=array("arr"=>$servicio, "var"=>$sel_servicio);
			$datos["nombre"]=array("value"=>$txt_nombre, "name"=>"nombre", "class"=>"nombre", "style"=>"width:100%;");
			$datos["servicio"]=$servicio;
			$equipo=array("arr"=>$equipo, "var"=>$sel_equipo);
			$datos["equipo"]=$equipo;
			$suministro=array("arr"=>$suministro, "var"=>$sel_suministro);
			$datos["suministro"]=$suministro;
			$obra=array("arr"=>$obra, "var"=>$sel_obra);
			$datos["obra"]=$obra;
			$tipo=array("arr"=>$tipo, "var"=>$sel_tipo);
			$datos["tipo"]=$tipo;
			$pais=array("arr"=>$pais, "var"=>$sel_pais);
			$datos["pais"]=$pais;
			$region=array("arr"=>$region, "var"=>$sel_region);
			$datos["region"]=$region;
			$etapa=array("arr"=>$etapa, "var"=>$sel_etapa);
			$datos["etapa"]=$etapa;
			$empresa=array("arr"=>$empresa, "var"=>$sel_empresa);
			$datos["empresa"]=$empresa;
			$mandante=array("arr"=>$mandante, "var"=>$sel_mandante);
			$datos["mandante"]=$mandante;
			$datos["actual"]=$nro_pag;
			$datos["id_sector"]=$id_sector;

			if($id_sector!=0)
				$datos["nombre_sector"]=" Sector ".$sector[0]->Nombre_sector;
			else
				$datos["nombre_sector"]=$sector[0]->Nombre_sector;
			$selected="";
			if($mandante["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$mandante["arr"][$mandante["var"]];
				}else{
					$selected.=", ".$mandante["arr"][$mandante["var"]];
				}
			}
			if($pais["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$pais["arr"][$pais["var"]];
				}else{
					$selected.=", ".$pais["arr"][$pais["var"]];
				}
			}
			if($region["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$region["arr"][$region["var"]];
				}else{
					$selected.=", ".$region["arr"][$region["var"]];
				}
			}
			if($obra["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$obra["arr"][$obra["var"]];
				}else{
					$selected.=", ".$obra["arr"][$obra["var"]];
				}
			}
			if($equipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$equipo["arr"][$equipo["var"]];
				}else{
					$selected.=", ".$equipo["arr"][$equipo["var"]];
				}
			}
			if($suministro["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$suministro["arr"][$suministro["var"]];
				}else{
					$selected.=", ".$suministro["arr"][$suministro["var"]];
				}
			}
			if($servicio["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$servicio["arr"][$servicio["var"]];
				}else{
					$selected.=", ".$servicio["arr"][$servicio["var"]];
				}
			}
			if($etapa["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$etapa["arr"][$etapa["var"]];
				}else{
					$selected.=", ".$etapa["arr"][$etapa["var"]];
				}
			}
			if($empresa["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$empresa["arr"][$empresa["var"]];
				}else{
					$selected.=", ".$empresa["arr"][$empresa["var"]];
				}
			}
			if($tipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando proyectos que contienen: ".$tipo["arr"][$tipo["var"]];
				}else{
					$selected.=", ".$tipo["arr"][$tipo["var"]];
				}
			}

			if($txt_nombre!=""){
				if($selected==""){
					$selected.="Mostrando licitaciones que contienen: ".urldecode($txt_nombre);
				}else{
					$selected.=", ".urldecode($txt_nombre);
				}
			}

			$datos["selected"]=$selected;
			$this->load->view('confluence/listar_pro2',$datos);
		}
	}


	public function cargar_menu($username){
		
		echo "<span style='color:#fff'>".$username."</span>";
		$socio_validar=$this->confluence->tipo_socio($username);
		$url=URL_PUBLICA_CONFLUENCE."/";

		$datos['menu_premium']=$socio_validar->tipo_socio;
		$datos['url_perfil']=$socio_validar->url_espacio_socio;
		$datos['url']=$url;

		$menu_proyectos = '';
		$menu_adjudicaciones = '';

		if($socio_validar->tipo_socio <> 'Mandante'){
			//si es asi, muestra los sugeridos
			$menu_proyectos .= '<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="'.$this->params->url_lista_pro_sugeridos.'"><b>Sugeridos para usted</b></a></div></li><hr>';

			$menu_adjudicaciones .= '<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="'.$this->params->url_lista_adju_sugeridas.'"><b>Sugeridas para usted</b></a></div></li><hr>';
		}

		$menu_proyectos .= '<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/proy/Proyectos">Ver Todos</a></div></li>';
		$menu_adjudicaciones .= '<li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/adju/Adjudicaciones">Ver Todas</a></div></li>';

        if($socio_validar->tipo_socio=='Premium'){
			$sectores=$this->confluence->mostrar_sectores($socio_validar->tipo_socio);
			foreach($sectores as $sector){
                $menu_proyectos.='<hr><li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/'.$this->params->spaces_proy[$sector->id_sector].'/">'.$sector->Nombre_sector.'</a></div></li>';
                $menu_adjudicaciones.='<hr><li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/'.$this->params->spaces_adj[$sector->id_sector].'/">'.$sector->Nombre_sector.'</a></div></li>';
			}

			$menu_proyectos.='<hr><li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/acce/Informes+Mensuales+De+Proyectos/">Informes</a></div></li>';
			
			

		}else if($socio_validar->tipo_socio=='Preferencial'){
			$sectores=$this->confluence->mostrar_sectores($socio_validar->tipo_socio);
			foreach($sectores as $sector){
                $menu_proyectos.='<hr><li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/'.$this->params->spaces_proy[$sector->id_sector].'/">'.$sector->Nombre_sector.'</a></div></li>';
                $menu_adjudicaciones.='<hr><li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/'.$this->params->spaces_adj[$sector->id_sector].'/">'.$sector->Nombre_sector.'</a></div></li>';
			}

		}else if(($socio_validar->tipo_socio=='Mandante')||($socio_validar->tipo_socio=='Especial')){
			$sectores=$this->confluence->mostrar_sectores($socio_validar->tipo_socio,$socio_validar->id_socio);
			foreach($sectores as $sector){
                $menu_proyectos.='<hr><li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/'.$this->params->spaces_proy[$sector->id_sector].'/">'.$sector->Nombre_sector.'</a></div></li>';
                $menu_adjudicaciones.='<hr><li><div class="icono"><img src="/sitio_portal/images/socios/flecha.png" /></div><div class="link"><a href="/display/'.$this->params->spaces_adj[$sector->id_sector].'/">'.$sector->Nombre_sector.'</a></div></li>';
			}
		}

		$datos['menu_proyectos']		= $menu_proyectos;
		$datos['menu_adjudicaciones']	= $menu_adjudicaciones;
		$datos['username']				= $username;
		$datos['link_ver_pro']			= $this->params->url_listar_proyectos;
		$datos['link_ver_lici']			= $this->params->url_listar_licitaciones;
		//$datos['link_add_lici']		= $this->params->url_agregar_licitacion;
		$datos['link_ver_adju']			= $this->params->url_listar_adjudicaciones;
		$datos['link_add_adju']			= $this->params->url_agregar_adjudicacion;
		//$datos['link_add_adju_man']	= $this->params->url_agregar_adjudicacion_man;

		//valida si hay resultados para las licitaciones sugeridas
		$total_registros_query = $this->confluence->valida_resultado_licitaciones_sugeridas($username);

		$datos['show_link_ls'] = true;
		if($total_registros_query == 0 || $total_registros_query == false){
			$datos['show_link_ls'] = false;
		}

		$this->load->view('confluence/menu_user',$datos);
	}


	public function mostrar_pro($username, $id_pro){
		/*if($username!=""){
			$dt=$this->confluence->listar_proyectos($username, $id_sector);
			if(is_array($dt) && sizeof($dt)>0){
				$x=0;
				foreach($dt as $d){
					$urlcode="confluence/mostrar_pro/".$username."/".$d->id_pro;
					$dt[$x]->code_url=str_replace("=", "", base64_encode( json_encode(array("url"=>$urlcode))));
					++$x;
				}
				$datos["proyectos"]=$dt;
			}
		}*/
		if($html=$this->confluence->generar_ficha_html($username, $id_pro, 1)){
			$datos=array("html"=>$html);
			$this->load->view('confluence/mostrar_pro',$datos);
		}else{
			echo "NO PUEDE VISUALIZAR";
		}
	}

	public function mostrar_home($username){
		if($username!=""){
			$user_datos=$this->confluence->listar_proyectos($username, $id_sector);
			$u_dashboard=$this->soap->parser("{recently-updated-dashboard:types=mail}");
			$dato['u_dashboard']=$u_dashboard;
			$this->load->view('confluence/home_personal',$dato);
		}
	}

	public function listar_videos($ancho_columna=''){
		$this->output->cache(1440);
		$videos_lista = URL_INTERNA_CONFLUENCE."/createrssfeed.action?types=page&spaces=vide&title=Videos&labelString%3D&excludedSpaceKeys%3D&sort=created&maxResults=20&timeSpan=200&showContent=true&confirm=Crear+Feed+RSS";

		$ent_video = new SimplePie();
		$ent_video->set_feed_url($videos_lista);
		$ent_video->init();
		$ent_video->handle_content_type();
		$ent_video->strip_htmltags(false);
		$ent_video_max = $ent_video->get_item_quantity();

		$entrevista_otras="";

		if($ancho_columna==''){
			$ancho_columna="width:230px;";
		}
		else{
			$ancho_columna="width:".$ancho_columna."px;";
		}
		//var_dump($ent_video->get_item());
		for($x = 0; $x < $ent_video_max; $x++) {
if($x!=0){
			$ent_video_items = $ent_video->get_item($x);
			if($ent_video_items->get_title()!='Videos'){

				$iframe_all=explode('<p><iframe',$ent_video_items->get_description());
				$iframe_all=explode('>',$iframe_all[1]);
				$iframe_url=explode('" frameborder',$iframe_all[0]);
				$iframe_url=explode('src="',$iframe_url[0]);
				$url_video=$iframe_url[1];

				$url_completa=explode('embed/',$url_video);
				$id_video=$url_completa[1];

					$imagen_inicial='http://img.youtube.com/vi/'.$id_video.'/1.jpg';
					$imagen_final=uniqid().'.jpg';
					$dir_cache= base_url().'cache/';
					//copy(base_url().'timthumb.php?src='.$imagen_inicial.'&w=64&q=100',BASE_DIRECTORIO.'/cache/'.$imagen_final);
					$url_imagen_final=base_url().'timthumb.php?src='.$imagen_inicial.'&w=64&q=100';

			if($ent_video_items->get_title()!='Borrar'){
					
					$entrevista_otras.='<div class="caja_video_testi" style="'.$ancho_columna.'"><a href="'.$ent_video_items->get_link().'" style="text-decoration:none;"><img src="'.$url_imagen_final.'" width="64" style="float:left;padding:5px;" /><span style="padding:2px;vertical-align:middle;font-size:11px">'.$ent_video_items->get_title().'</span></a></div>';
			}
					}
			else{
				//$entrevista_otras="Error";
		
			}

			}
		}
		$datos['entrevista_otras']=$entrevista_otras;
		$datos['ancho_columna']=$ancho_columna;

		$this->load->view('confluence/listar_videos',$datos);

	}

	
	public function listar_videos_all($ancho_columna=''){
	    $this->output->delete_cache();
	    
		$this->output->cache(1440);
	    
		$videos_lista = URL_INTERNA_CONFLUENCE."/createrssfeed.action?types=page&spaces=vide&title=Videos&labelString%3D&excludedSpaceKeys%3D&sort=created&maxResults=80&timeSpan=500&showContent=true&confirm=Crear+Feed+RSS";

		$ent_video = new SimplePie();
		$ent_video->set_feed_url($videos_lista);
		$ent_video->init();
		$ent_video->handle_content_type();
		$ent_video->strip_htmltags(false);
		$ent_video_max = $ent_video->get_item_quantity();
		echo $videos_lista;
		$entrevista_otras = "";

		if($ancho_columna==''){
			$ancho_columna="width:230px;";
		}
		else{
			$ancho_columna="width:".$ancho_columna."px;";
		}

		$formato_video='<div class="caja_video_testi" style="'.$ancho_columna.';float:left;margin:2px"><a href="@link_video" style="text-decoration:none;"><img src="@imagen_video" width="64" style="float:left;padding:5px;" /><span style="padding:2px;vertical-align:middle;font-size:11px">@titulo_video</span></a></div>';
		//var_dump($ent_video->get_item());
		for($x = 0; $x < $ent_video_max; $x++) {
          if($x!=0){
			$ent_video_items = $ent_video->get_item($x);
			if($ent_video_items->get_title()!='Videos'){

				$iframe_all=explode('<p><iframe',$ent_video_items->get_description());
				$iframe_all=explode('>',$iframe_all[1]);
				$iframe_url=explode('" frameborder',$iframe_all[0]);
				$iframe_url=explode('src="',$iframe_url[0]);
				$url_video=$iframe_url[1];

				$url_completa=explode('embed/',$url_video);
				$id_video=$url_completa[1];

					$imagen_inicial='http://img.youtube.com/vi/'.$id_video.'/1.jpg';
					$imagen_final=uniqid().'.jpg';
					$dir_cache= base_url().'cache/';
					//copy(base_url().'timthumb.php?src='.$imagen_inicial.'&w=64&q=100',BASE_DIRECTORIO.'/cache/'.$imagen_final);
					$imagen_video=base_url().'timthumb.php?src='.$imagen_inicial.'&w=64&q=100';

					$video_completo=str_replace('@link_video',$ent_video_items->get_link(),$formato_video);
					$video_completo=str_replace('@imagen_video',$imagen_video,$video_completo);
					$video_completo=str_replace('@titulo_video',$x,$video_completo);
					$entrevista_otras.=$video_completo;

					}
			else{
				//$entrevista_otras="Error";
			}
		}}
		$datos['entrevista_otras']=$entrevista_otras;

		$this->load->view('confluence/listar_videos_all',$datos);

	}

	public function listar_testimoniales($labels=''){
		$this->output->cache(100);

		$label='';

		if($labels!=''){
			$labels=str_replace('=','-',$labels);
			$labels= base64_decode($labels);
			$label="&labelString=".$labels;
		}

		$videos_ent = URL_INTERNA_CONFLUENCE."/createrssfeed.action?types=page&spaces=testi&title=Testimoniales&labelString%3D&excludedSpaceKeys%3D".$label."&sort=created&maxResults=10&timeSpan=1000&showContent=true&confirm=Crear+Feed+RSS";

		$ent_video = new SimplePie();
		$ent_video->set_feed_url($videos_ent);
		$ent_video->init();
		$ent_video->handle_content_type();
		$ent_video->strip_htmltags(false);
		$ent_video_max = $ent_video->get_item_quantity();

		$entrevista_otras="";


		for($x = 0; $x < $ent_video_max; $x++) {

			$ent_video_items = $ent_video->get_item($x);
			if(($ent_video_items->get_title()!='Testimoniales')&&($ent_video_items->get_title()!='Listado')){

				$iframe_all=explode('<p><iframe',$ent_video_items->get_description());
				$iframe_all=explode('>',$iframe_all[1]);
				$iframe_url=explode('" frameborder',$iframe_all[0]);
				$iframe_url=explode('src="',$iframe_url[0]);
				$url_video=$iframe_url[1];

				$url_completa=explode('embed/',$url_video);
				$id_video=$url_completa[1];
					$imagen_inicial='http://img.youtube.com/vi/'.$id_video.'/2.jpg';
					//$imagen_final=uniqid().'.jpg';
					//$dir_cache= base_url().'cache/';
					//copy(base_url().'timthumb.php?src='.$imagen_inicial.'&w=64&q=100',BASE_DIRECTORIO.'/cache/'.$imagen_final);
					$imagen_video=base_url().'timthumb.php?src='.$imagen_inicial.'&w=64&q=100';


					$entrevista_otras.='<div class="caja_video_testi" style="width:230px;"><a href="'.$ent_video_items->get_link().'" style="text-decoration:none;"><img src="'.$imagen_video.'" width="64" style="float:left;padding:5px;" /><span style="padding:2px;vertical-align:middle;font-size:11px">'.$ent_video_items->get_title().'</span></a></div>';

					}
		}
		$datos['entrevista_otras']=$entrevista_otras;

		$this->load->view('confluence/listar_testimoniales',$datos);

	}

	public function home_testimoniales($tipo=''){
		$this->output->cache(1440);
		$label='';
		$titulo='Testimonios 2012';

			$video_principal='';
			$listado_videos='';


		if($tipo=='CAPA'){
			$label="&labelString=capacitación";
			$titulo='Testimoniales de Capacitación';
		}
		else if($tipo=='RECLU'){
			$label="&labelString=reclutamiento";
			$titulo='Testimoniales de Reclutamiento';
		}
		else if($tipo=='DEMO'){
			$label="&labelString=demostración";
			$titulo='Videos de Demostración Portal Minero';
		}
		else if($tipo=='SOCIO'){
			$label="&labelString=socio";
			$titulo='Testimoniales de Socios Portal Minero';
		}$label="";
		$videos_lista = URL_INTERNA_CONFLUENCE."/createrssfeed.action?types=page&spaces=testi&title=Videos+Testimoniales".$label."&excludedSpaceKeys%3D&sort=modified&maxResults=20&timeSpan=1000&showContent=true&confirm=Crear+Feed+RSS";

		$ent_video = new SimplePie();
		$ent_video->set_feed_url($videos_lista);
		$ent_video->init();
		$ent_video->handle_content_type();
		$ent_video->strip_htmltags(false);
		$ent_video_max = $ent_video->get_item_quantity();

		$vide_princ=0;
		for($x = 0; $x < $ent_video_max; $x++) {

			$ent_video_items = $ent_video->get_item($x);
			if(($ent_video_items->get_title()!='Testimoniales')&&($ent_video_items->get_title()!='Listado')){

				$iframe_all=explode('<p><iframe',$ent_video_items->get_description());
				$iframe_all=explode('>',$iframe_all[1]);
				$iframe_url=explode('" frameborder',$iframe_all[0]);
				$iframe_url=explode('src="',$iframe_url[0]);
				$url_video=$iframe_url[1];
				$url_completa=explode('embed/',$url_video);
				$id_video=$url_completa[1];
				//var_dump($ent_video_items);
					$imagen_inicial='http://img.youtube.com/vi/'.$id_video.'/1.jpg';
					$imagen_video=base_url().'timthumb.php?src='.$imagen_inicial.'&w=64&q=100';

					if($vide_princ==0){
						$video_principal='<p><iframe class="youtube-player" type="text/html" style="width: 480px; height: 320px" src="'.$url_video.'" frameborder="0"></iframe></p><h3 style="color:#b96212">'.$ent_video_items->get_title().'</h3>';
						$vide_princ=1;
					}
					else{
						$listado_videos.='<div class="caja_video_testi"><a href="'.$ent_video_items->get_link().'" style="text-decoration:none;"><img src="'.$imagen_video.'" width="64" style="float:left;padding:5px;" /><span style="padding:2px;vertical-align:middle;font-size:11px">'.$ent_video_items->get_title().'</span></a></div>';
					}


					}
			else{
				//$entrevista_otras="Error";
			}
		}
		//$datos['entrevista_otras']=$entrevista_otras;
		$datos['video_principal']=$video_principal;
		$datos['listado_videos']=$listado_videos;
		$datos['titulo']=$titulo;


		$this->load->view('confluence/home_testimoniales',$datos);

	}

	public function listar_lic($username="", $id_sector, $nro_pag, $where2=""){
		//$ec=str_replace("-", "=", $ec);
		//echo base64_decode($ec)."<br>"; die();
		$sel_sector="";

		if($id_sector!=0){
			$sector=$this->sector->mostrar($id_sector);
			$sel_sector=$id_sector;
		}else{
			$sector[0]=new stdClass();
			$sector[0]->Nombre_sector="Todos los Sectores";
		}

		if($username!=""){
			$sel_servicio="";
			$sel_equipo="";
			$sel_suministro="";
			$sel_obra="";
			$sel_tipo="";
			$sel_pais="";
			$sel_region="";
			$sel_etapa="";
			$sel_empresa="";
			$sel_mandante="";
			$sel_rubro="";
			$sel_reg_prov="";
			$sel_lici_tipo="";
			$txt_nombre="";
			$ordernombre="asc";
			$ordersector="asc";
			$orderestado="asc";
			$orderpais="asc";
			$orderregion="asc";
			$orderfet="asc";
			$orderflcb="asc";
			$order=null;
			if($where2!=""){
				if($where2!="*"){
					$where2=str_replace("-_-", " ", $where2);
					$search=explode("-",$where2);
					$where="";
					foreach($search as $x=>$f){
						$v=explode("_", $f);

						if($v[0]=="pais"){
							$sel_pais=$v[1];
						}

						if($v[0]=="obra"){
							$sel_obra=$v[1];
						}

						if($v[0]=="equipo"){
							$sel_equipo=$v[1];
						}

						if($v[0]=="suministro"){
							$sel_suministro=$v[1];
						}

						if($v[0]=="servicio"){
							$sel_servicio=$v[1];
						}

						if($v[0]=="region"){
							$sel_region=$v[1];
						}

						if($v[0]=="tipo"){
							$sel_tipo=$v[1];
						}

						if($v[0]=="mandante"){
							$sel_mandante=$v[1];
						}

						if($v[0]=="nombre"){
							$txt_nombre=str_replace("-_-", " ", $v[1]);
						}

						if($v[0]=="rubro"){
							$sel_rubro=$v[1];
						}

						if($v[0]=="licitipo"){
							$sel_lici_tipo=$v[1];
						}

						if($v[0]=="regprov"){
							$sel_reg_prov=$v[1];
						}

						if($v[0]=="ordernombre"){
							if($v[1]=="asc")
								$ordernombre="desc";
							else
								$ordernombre="asc";
							$order="ordernombre_".$v[1];
						}

						if($v[0]=="ordersector"){
							if($v[1]=="asc")
								$ordersector="desc";
							else
								$ordersector="asc";
							$order="ordersector_".$v[1];
						}

						if($v[0]=="orderestado"){
							if($v[1]=="asc")
								$orderestado="desc";
							else
								$orderestado="asc";
							$order="orderestado_".$v[1];
						}

						if($v[0]=="orderpais"){
							if($v[1]=="asc")
								$orderpais="desc";
							else
								$orderpais="asc";
							$order="orderpais_".$v[1];
						}

						if($v[0]=="orderregion"){
							if($v[1]=="asc")
								$orderregion="desc";
							else
								$orderregion="asc";
							$order="orderregion_".$v[1];
						}

						if($v[0]=="orderfet"){
							if($v[1]=="asc")
								$orderfet="desc";
							else
								$orderfet="asc";
							$order="orderfet_".$v[1];
						}

						if($v[0]=="orderflcb"){
							if($v[1]=="asc")
								$orderflcb="desc";
							else
								$orderflcb="asc";
							$order="orderflcb_".$v[1];
						}

					}
				}
			}

			$dt=$this->confluence->listar_licitaciones($username, $id_sector, $nro_pag, $where2);
			if(is_array($dt[0]) && sizeof($dt[0])>0){
				$x=0;
				$paginador=$this->params->paginador($nro_pag, $dt[1], null, $order);
				$datos["paginador"]=$paginador;
				$datos["licitaciones"]=$dt[0];
			}else{
				$datos["licitaciones"]=array();
			}

			$servicio=$this->licitacion->servicios_sel($dt[2], $id_sector, $username);
			unset($servicio[""]);
			$equipo=$this->licitacion->equipos_sel($dt[2], $id_sector, $username);
			unset($equipo[""]);
			$suministro=$this->licitacion->suministro_sel($dt[2], $id_sector, $username);
			unset($suministro[""]);
			$obra=$this->licitacion->obras_sel($dt[2], $id_sector, $username);
			unset($obra[""]);
			$tipo=$this->licitacion->tipo_sel($dt[2], $id_sector, $username);
			unset($tipo[""]);
			$pais= $this->licitacion->pais_sel($dt[2], $id_sector, $username);
			unset($pais[""]);
			$region= $this->region->llenar_combo_region_chile();
			unset($region[""]);
			$mandante= $this->licitacion->mandante_sel($dt[2], $id_sector, $username);
			unset($mandante[""]);
			$tipo_lici= $this->licitacion->lici_tipo_sel($dt[2], $id_sector, $username);
			unset($tipo_lici[""]);
			$rubro= $this->licitacion->rubro_sel($dt[2], $id_sector, $username);
			unset($rubro[""]);
			$reg_prov= $this->licitacion->reg_prov_sel($dt[2], $id_sector, $username);
			unset($reg_prov[""]);
			$sector1= $this->licitacion->get_sector();
			unset($sector1[""]);

			$datos["nombre"]=array("value"=>$txt_nombre, "name"=>"nombre", "class"=>"nombre", "style"=>"width:100%;");
			$rubro=array("arr"=>$rubro, "var"=>$sel_rubro);
			$datos["rubro"]=$rubro;
			$reg_prov=array("arr"=>$reg_prov, "var"=>$sel_reg_prov);
			$datos["reg_prov"]=$reg_prov;
			$servicio=array("arr"=>$servicio, "var"=>$sel_servicio);
			$datos["servicio"]=$servicio;
			$equipo=array("arr"=>$equipo, "var"=>$sel_equipo);
			$datos["equipo"]=$equipo;
			$suministro=array("arr"=>$suministro, "var"=>$sel_suministro);
			$datos["suministro"]=$suministro;
			$obra=array("arr"=>$obra, "var"=>$sel_obra);
			$datos["obra"]=$obra;
			$tipo=array("arr"=>$tipo, "var"=>$sel_tipo);
			$datos["tipo"]=$tipo;
			$pais=array("arr"=>$pais, "var"=>$sel_pais);
			$datos["pais"]=$pais;
			$region=array("arr"=>$region, "var"=>$sel_region);
			$datos["region"]=$region;
			$mandante=array("arr"=>$mandante, "var"=>$sel_mandante);
			$datos["mandante"]=$mandante;
			$tipo_lici=array("arr"=>$tipo_lici, "var"=>$sel_lici_tipo);
			$datos["tipo_lici"]=$tipo_lici;
			$sector1=array("arr"=>$sector1, "var"=>intval($id_sector));
			$datos["sector"]=$sector1;
			$datos["actual"]=$nro_pag;
			$datos["id_sector"]=$id_sector;
			$datos["nombre_sector"]=$sector[0]->Nombre_sector;
			if(isset($order))
				$datos["order"]=$order;

			$datos["ordernombre"]=$ordernombre;
			$datos["ordersector"]=$ordersector;
			$datos["orderestado"]=$orderestado;
			$datos["orderpais"]=$orderpais;
			$datos["orderregion"]=$orderregion;
			$datos["orderfet"]=$orderfet;
			$datos["orderflcb"]=$orderflcb;

			$selected="";
			if($mandante["var"]!=""){
				if($selected==""){
					if(is_array($mandante["arr"]) && in_array($mandante["var"], array_keys($mandante["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$mandante["arr"][$mandante["var"]];
				}else{
					if(is_array($mandante["arr"]) && in_array($mandante["var"], array_keys($mandante["arr"])))
						$selected.=", ".$mandante["arr"][$mandante["var"]];
				}
			}
			if($pais["var"]!=""){
				if($selected==""){
					if(is_array($pais["arr"]) && in_array($pais["var"], array_keys($pais["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$pais["arr"][$pais["var"]];
				}else{
					if(is_array($pais["arr"]) && in_array($pais["var"], array_keys($pais["arr"])))
						$selected.=", ".$pais["arr"][$pais["var"]];
				}
			}
			if($region["var"]!=""){
				if($selected==""){
					if(is_array($region["arr"]) && in_array($region["var"], array_keys($region["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$region["arr"][$region["var"]];
				}else{
					if(is_array($region["arr"]) && in_array($region["var"], array_keys($region["arr"])))
						$selected.=", ".$region["arr"][$region["var"]];
				}
			}
			if($obra["var"]!=""){
				if($selected==""){
					if(is_array($obra["arr"]) && in_array($obra["var"], array_keys($obra["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$obra["arr"][$obra["var"]];
				}else{
					if(is_array($obra["arr"]) && in_array($obra["var"], array_keys($obra["arr"])))
						$selected.=", ".$obra["arr"][$obra["var"]];
				}
			}
			if($equipo["var"]!=""){
				if($selected==""){
					if(is_array($equipo["arr"]) && in_array($equipo["var"], array_keys($equipo["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$equipo["arr"][$equipo["var"]];
				}else{
					if(is_array($equipo["arr"]) && in_array($equipo["var"], array_keys($equipo["arr"])))
						$selected.=", ".$equipo["arr"][$equipo["var"]];
				}
			}
			if($suministro["var"]!=""){
				if($selected==""){
					if(is_array($suministro["arr"]) && in_array($suministro["var"], array_keys($suministro["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$suministro["arr"][$suministro["var"]];
				}else{
					if(is_array($suministro["arr"]) && in_array($suministro["var"], array_keys($suministro["arr"])))
						$selected.=", ".$suministro["arr"][$suministro["var"]];
				}
			}
			if($servicio["var"]!=""){
				if($selected==""){
					if(is_array($servicio["arr"]) && in_array($servicio["var"], array_keys($servicio["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$servicio["arr"][$servicio["var"]];
				}else{
					if(is_array($servicio["arr"]) && in_array($servicio["var"], array_keys($servicio["arr"])))
						$selected.=", ".$servicio["arr"][$servicio["var"]];
				}
			}

			if($tipo["var"]!=""){
				if($selected==""){
					if(is_array($tipo["arr"]) && in_array($tipo["var"], array_keys($tipo["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$tipo["arr"][$tipo["var"]];
				}else{
					if(is_array($tipo["arr"]) && in_array($tipo["var"], array_keys($tipo["arr"])))
						$selected.=", ".$tipo["arr"][$tipo["var"]];
				}
			}

			if($rubro["var"]!=""){
				if($selected==""){
					if(is_array($rubro["arr"]) && in_array($rubro["var"], array_keys($rubro["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$rubro["arr"][$rubro["var"]];
				}else{
					if(is_array($rubro["arr"]) && in_array($rubro["var"], array_keys($rubro["arr"])))
						$selected.=", ".$rubro["arr"][$rubro["var"]];
				}
			}

			if($reg_prov["var"]!=""){
				if($selected==""){
					if(is_array($reg_prov["arr"]) && in_array($reg_prov["var"], array_keys($reg_prov["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$reg_prov["arr"][$reg_prov["var"]];
				}else{
					if(is_array($reg_prov["arr"]) && in_array($reg_prov["var"], array_keys($reg_prov["arr"])))
						$selected.=", ".$reg_prov["arr"][$reg_prov["var"]];
				}
			}

			if(isset($tipo_lici["arr"][$tipo_lici["var"]]) && $tipo_lici["var"]!=""){
				if($selected==""){
					if(is_array($tipo_lici["arr"]) && in_array($tipo_lici["var"], array_keys($tipo_lici["arr"])))
						$selected.="Mostrando licitaciones que contienen: ".$tipo_lici["arr"][$tipo_lici["var"]];
				}else{
					if(is_array($tipo_lici["arr"]) && in_array($tipo_lici["var"], array_keys($tipo_lici["arr"])))
						$selected.=", ".$tipo_lici["arr"][$tipo_lici["var"]];
				}
			}

			if($txt_nombre!=""){
				if($selected==""){
					$selected.="Mostrando licitaciones que contienen: ".urldecode($txt_nombre);
				}else{
					$selected.=", ".urldecode($txt_nombre);
				}
			}

			$datos["selected"]=$selected;
			$this->load->view('confluence/listar_lic',$datos);
		}
	}

	//public function listar_adj($username="", $id_sector, $nro_pag, $where2=""){
	public function listar_adj($username="", $id_sector="", $nro_pag, $where2=""){
		//$ec=str_replace("-", "=", $ec);
		//echo base64_decode($ec)."<br>"; die();
		//$sector=$this->sector->mostrar($id_sector);
		//$this->output->enable_profiler(true);
		$sel_sector="";
		if($id_sector!=0){
			$sector=$this->sector->mostrar($id_sector);
			$sel_sector=$id_sector;
		}/*else{
			$sector[0]->Nombre_sector="Todos los Sectores";
		}*/
		if($username!=""){
			$sel_servicio="";
			$sel_catservicio="";
			$sel_subcatservicio="";
			$sel_equipo="";
			$sel_suministro="";
			$sel_obra="";
			$sel_tipo="";
			$sel_pais="";
			$sel_region="";
			$sel_empadj="";
			$sel_comprador="";
			$sel_via="";
			$txt_nombre="";
			$ordernombre="DESC";
			$orderempresa="DESC";
			$ordercomprador="DESC";
			$orderfecha="DESC";
			$order=null;
			if($where2!=""){
				if($where2!="*"){
					$where2=str_replace("-_-", " ", $where2);
					$search=explode("-",$where2);
					$where="";
					foreach($search as $x=>$f){
						$v=explode("_", $f);

						if($v[0]=="pais"){
							$sel_pais=$v[1];
						}

						if($v[0]=="obra"){
							$sel_obra=$v[1];
						}

						if($v[0]=="equipo"){
							$sel_equipo=$v[1];
						}

						if($v[0]=="suministro"){
							$sel_suministro=$v[1];
						}

						if($v[0]=="catservicio"){
							$sel_catservicio=$v[1];
						}

						if($v[0]=="subcatservicio"){
							$sel_subcatservicio=$v[1];
						}

						if($v[0]=="region"){
							$sel_region=$v[1];
						}

						if($v[0]=="tipo"){
							$sel_tipo=$v[1];
						}

						if($v[0]=="empadj"){
							$sel_empadj=$v[1];
						}

						if($v[0]=="comprador"){
							$sel_comprador=$v[1];
						}

						if($v[0]=="via"){
							$sel_via=$v[1];
						}

						if($v[0]=="nombre"){
							$txt_nombre=str_replace("-_-", " ", $v[1]);
						}

						if($v[0]=="ordernombre"){
							if($v[1]=="ASC")
								$ordernombre="DESC";
							else
								$ordernombre="ASC";
							$order="ordernombre_".$v[1];
						}

						if($v[0]=="orderempresa"){
							if($v[1]=="ASC")
								$orderempresa="DESC";
							else
								$orderempresa="ASC";
							$order="orderempresa_".$v[1];
						}

						if($v[0]=="ordercomprador"){
							if($v[1]=="ASC")
								$ordercomprador="DESC";
							else
								$ordercomprador="ASC";
							$order="ordercomprador_".$v[1];
						}

						if($v[0]=="orderfecha"){
							if($v[1]=="ASC")
								$orderfecha="DESC";
							else
								$orderfecha="ASC";
							$order="orderfecha_".$v[1];
						}

					}
				}
			}
			$dt=$this->confluence->listar_adjudicaciones($username, $id_sector, $nro_pag, $where2);
			if(is_array($dt[0]) && sizeof($dt[0])>0){
				$x=0;
				$paginador=$this->params->paginador($nro_pag, $dt[1], null, $order);
				$datos["paginador"]=$paginador;
				$datos["adjudicaciones"]=$dt[0];
			}else{
				$datos["adjudicaciones"]=array();
			}

			$catservicio=$this->adjudicacion->catservicios_sel($dt[2], $id_sector, $username);
			unset($catservicio[""]);
			if($sel_catservicio!="" && $sel_catservicio!=NULL && $sel_catservicio!=0){
				$subcatservicio=$this->adjudicacion->subcatservicios_sel($dt[2], $id_sector, $username, $sel_catservicio);

				unset($subcatservicio[""]);
			}

			$equipo=$this->adjudicacion->equipos_sel($dt[2], $id_sector, $username);
			unset($equipo[""]);
			$suministro=$this->adjudicacion->suministro_sel($dt[2], $id_sector, $username);
			unset($suministro[""]);
			$obra=$this->adjudicacion->obras_sel($dt[2], $id_sector, $username);
			unset($obra[""]);
			$tipo=$this->adjudicacion->tipo_sel($dt[2], $id_sector, $username);
			unset($tipo[""]);
			$pais= $this->adjudicacion->pais_sel($dt[2], $id_sector, $username);
			unset($pais[""]);

			if($sel_pais!="" && $sel_pais!=NULL && $sel_pais!=0){
				$re=$this->region->get_regiones($sel_pais);
				foreach($re as $r){
					$region[$r->id_region]=$r->Nombre_region;
				}
			}else{
				$region=array();
			}

			$empadj= $this->adjudicacion->empadj_sel($dt[2], $id_sector, $username);
			unset($empadj[""]);
			$comprador=$this->adjudicacion->comprador_sel($dt[2], $id_sector, $username);
			unset($comprador[""]);
			$via=$this->confluence->get_via();
			unset($via[""]);
			$datos["nombre"]=array("value"=>$txt_nombre, "name"=>"nombre", "class"=>"nombre", "style"=>"width:100%;");

			$catservicio=array("arr"=>$catservicio, "var"=>$sel_catservicio);
			$datos["catservicio"]=$catservicio;
			$subcatservicio=array("arr"=>isset($subcatservicio)? $subcatservicio : "", "var"=>$sel_subcatservicio);
			$datos["subcatservicio"]=$subcatservicio;
			$equipo=array("arr"=>$equipo, "var"=>$sel_equipo);
			$datos["equipo"]=$equipo;
			$suministro=array("arr"=>$suministro, "var"=>$sel_suministro);
			$datos["suministro"]=$suministro;
			$obra=array("arr"=>$obra, "var"=>$sel_obra);
			$datos["obra"]=$obra;
			$tipo=array("arr"=>$tipo, "var"=>$sel_tipo);
			$datos["tipo"]=$tipo;
			$pais=array("arr"=>$pais, "var"=>$sel_pais);
			$datos["pais"]=$pais;
			$region=array("arr"=>$region, "var"=>$sel_region);
			$datos["region"]=$region;
			$via=array("arr"=>$via, "var"=>$sel_via);
			$datos["via"]=$via;

			$empadj=array("arr"=>$empadj, "var"=>$sel_empadj);
			$datos["empadj"]=$empadj;
			$comprador=array("arr"=>$comprador, "var"=>$sel_comprador);
			$datos["comprador"]=$comprador;
			$datos["actual"]=$nro_pag;

			$datos["ordernombre"]=$ordernombre;
			$datos["orderempresa"]=$orderempresa;
			$datos["ordercomprador"]=$ordercomprador;
			$datos["orderfecha"]=$orderfecha;

			$selected="";
			if($pais["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$pais["arr"][$pais["var"]];
				}else{
					$selected.=", ".$pais["arr"][$pais["var"]];
				}
			}
			if($region["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$region["arr"][$region["var"]];
				}else{
					$selected.=", ".$region["arr"][$region["var"]];
				}
			}
			if($obra["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$obra["arr"][$obra["var"]];
				}else{
					$selected.=", ".$obra["arr"][$obra["var"]];
				}
			}
			if($equipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$equipo["arr"][$equipo["var"]];
				}else{
					$selected.=", ".$equipo["arr"][$equipo["var"]];
				}
			}
			if($suministro["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$suministro["arr"][$suministro["var"]];
				}else{
					$selected.=", ".$suministro["arr"][$suministro["var"]];
				}
			}
			if($catservicio["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$catservicio["arr"][$catservicio["var"]];
				}else{
					$selected.=", ".$catservicio["arr"][$catservicio["var"]];
				}
			}
			if($catservicio["var"]!="" && $subcatservicio["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$subcatservicio["arr"][$subcatservicio["var"]];
				}else{
					$selected.=", ".$subcatservicio["arr"][$subcatservicio["var"]];
				}
			}

			if($tipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$tipo["arr"][$tipo["var"]];
				}else{
					$selected.=", ".$tipo["arr"][$tipo["var"]];
				}
			}

			if($comprador["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$comprador["arr"][$comprador["var"]];
				}else{
					$selected.=", ".$comprador["arr"][$comprador["var"]];
				}
			}
			if($empadj["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$empadj["arr"][$empadj["var"]];
				}else{
					$selected.=", ".$empadj["arr"][$empadj["var"]];
				}
			}
			if($via["var"]!="" && intval($via["var"])>0){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$via["arr"][$via["var"]];
				}else{
					$selected.=", ".$via["arr"][$via["var"]];
				}
			}

			if($txt_nombre!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".urldecode($txt_nombre);
				}else{
					$selected.=", ".urldecode($txt_nombre);
				}
			}

			$datos["selected"]=$selected;
			$datos["id_sector"]=$id_sector;
			if($id_sector!=0)
				$datos["nombre_sector"]=" Sector ".$sector[0]->Nombre_sector;
			else
				$datos["nombre_sector"]="Todos los Sectores";
			$this->load->view('confluence/listar_adj',$datos);
		}
	}

	public function listar_adj2($username="", $id_sector="", $nro_pag, $where2=""){
		//$ec=str_replace("-", "=", $ec);
		//echo base64_decode($ec)."<br>"; die();
		//$sector=$this->sector->mostrar($id_sector);
		$sel_sector="";

		if($id_sector!=0){
			$sector=$this->sector->mostrar($id_sector);
			$sel_sector=$id_sector;
		}else{
			$sector[0]->Nombre_sector="Todos los Sectores";
		}
		if($username!=""){
			$sel_servicio="";
			$sel_catservicio="";
			$sel_subcatservicio="";
			$sel_equipo="";
			$sel_suministro="";
			$sel_obra="";
			$sel_tipo="";
			$sel_pais="";
			$sel_region="";
			$sel_empadj="";
			$sel_comprador="";
			$txt_nombre="";
			if($where2!=""){
				if($where2!="*"){
					$where2=str_replace("-_-", " ", $where2);
					$search=explode("-",$where2);
					$where="";
					foreach($search as $x=>$f){
						$v=explode("_", $f);

						if($v[0]=="pais"){
							$sel_pais=$v[1];
						}
						if($v[0]=="obra"){
							$sel_obra=$v[1];
						}
						if($v[0]=="equipo"){
							$sel_equipo=$v[1];
						}
						if($v[0]=="suministro"){
							$sel_suministro=$v[1];
						}

						if($v[0]=="servicio"){
							$sel_servicio=$v[1];
						}

						if($v[0]=="catservicio"){
							$sel_catservicio=$v[1];
						}

						if($v[0]=="subcatservicio"){
							$sel_subcatservicio=$v[1];
						}

						if($v[0]=="region"){
							$sel_region=$v[1];
						}

						if($v[0]=="tipo"){
							$sel_tipo=$v[1];
						}

						if($v[0]=="empadj"){
							$sel_empadj=$v[1];
						}

						if($v[0]=="comprador"){
							$sel_comprador=$v[1];
						}

						if($v[0]=="nombre"){
							$txt_nombre=str_replace("-_-", " ", $v[1]);
						}
					}
				}
			}
			$dt=$this->confluence->listar_adjudicaciones($username, $id_sector, $nro_pag, $where2);
			if(is_array($dt[0]) && sizeof($dt[0])>0){

				$x=0;
				$paginador=$this->params->paginador($nro_pag, $dt[1]);
				$datos["paginador"]=$paginador;
				$datos["adjudicaciones"]=$dt[0];
			}else{

				$datos["adjudicaciones"]=array();
			}

			$servicio=$this->adjudicacion->servicios_sel($dt[2], $id_sector, $username);
			unset($servicio[""]);
			$equipo=$this->adjudicacion->equipos_sel($dt[2], $id_sector, $username);
			unset($equipo[""]);
			$suministro=$this->adjudicacion->suministro_sel($dt[2], $id_sector, $username);
			unset($suministro[""]);
			$obra=$this->adjudicacion->obras_sel($dt[2], $id_sector, $username);
			unset($obra[""]);
			$tipo=$this->adjudicacion->tipo_sel($dt[2], $id_sector, $username);
			unset($tipo[""]);
			$pais= $this->adjudicacion->pais_sel($dt[2], $id_sector, $username);
			unset($pais[""]);
			$region= $this->region->llenar_combo_region_chile();
			unset($region[""]);
			$empadj= $this->adjudicacion->empadj_sel($dt[2], $id_sector, $username);
			unset($empadj[""]);
			$comprador= $this->adjudicacion->comprador_sel($dt[2], $id_sector, $username);
			unset($comprador[""]);

			$datos["nombre"]=array("value"=>"", "name"=>"nombre", "class"=>"nombre", "style"=>"width:100%;");
			$servicio=array("arr"=>$servicio, "var"=>$sel_servicio);
			$datos["servicio"]=$servicio;
			$equipo=array("arr"=>$equipo, "var"=>$sel_equipo);
			$datos["equipo"]=$equipo;
			$suministro=array("arr"=>$suministro, "var"=>$sel_suministro);
			$datos["suministro"]=$suministro;
			$obra=array("arr"=>$obra, "var"=>$sel_obra);
			$datos["obra"]=$obra;
			$tipo=array("arr"=>$tipo, "var"=>$sel_tipo);
			$datos["tipo"]=$tipo;
			$pais=array("arr"=>$pais, "var"=>$sel_pais);
			$datos["pais"]=$pais;
			$region=array("arr"=>$region, "var"=>$sel_region);
			$datos["region"]=$region;

			$empadj=array("arr"=>$empadj, "var"=>$sel_empadj);
			$datos["empadj"]=$empadj;
			$comprador=array("arr"=>$comprador, "var"=>$sel_comprador);
			$datos["comprador"]=$comprador;
			$datos["actual"]=$nro_pag;
			$selected="";
			if($pais["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$pais["arr"][$pais["var"]];
				}else{
					$selected.=", ".$pais["arr"][$pais["var"]];
				}
			}
			if($region["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$region["arr"][$region["var"]];
				}else{
					$selected.=", ".$region["arr"][$region["var"]];
				}
			}
			if($obra["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$obra["arr"][$obra["var"]];
				}else{
					$selected.=", ".$obra["arr"][$obra["var"]];
				}
			}
			if($equipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$equipo["arr"][$equipo["var"]];
				}else{
					$selected.=", ".$equipo["arr"][$equipo["var"]];
				}
			}
			if($suministro["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$suministro["arr"][$suministro["var"]];
				}else{
					$selected.=", ".$suministro["arr"][$suministro["var"]];
				}
			}
			if($servicio["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$servicio["arr"][$servicio["var"]];
				}else{
					$selected.=", ".$servicio["arr"][$servicio["var"]];
				}
			}

			if($tipo["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$tipo["arr"][$tipo["var"]];
				}else{
					$selected.=", ".$tipo["arr"][$tipo["var"]];
				}
			}

			if($comprador["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$comprador["arr"][$comprador["var"]];
				}else{
					$selected.=", ".$comprador["arr"][$comprador["var"]];
				}
			}
			if($empadj["var"]!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".$empadj["arr"][$empadj["var"]];
				}else{
					$selected.=", ".$empadj["arr"][$empadj["var"]];
				}
			}

			if($txt_nombre!=""){
				if($selected==""){
					$selected.="Mostrando adjudicaciones que contienen: ".urldecode($txt_nombre);
				}else{
					$selected.=", ".urldecode($txt_nombre);
				}
			}

			$datos["selected"]=$selected;
			$datos["id_sector"]=$id_sector;
			if($id_sector!=0)
				$datos["nombre_sector"]=" Sector ".$sector[0]->Nombre_sector;
			else
				$datos["nombre_sector"]=$sector[0]->Nombre_sector;
			$this->load->view('confluence/listar_adj',$datos);
		}
	}

	public function listar_lic_pub($nro_pag=1){

		$id_sector=0;
		$username="%20";
		$dt=$this->confluence->listar_licitaciones($username, $id_sector, $nro_pag, "");
		if(is_array($dt[0]) && sizeof($dt[0])>0){
			$x=0;
			$paginador=$this->params->paginador($nro_pag, $dt[1]);
			$datos["paginador"]=$paginador;
			$datos["licitaciones"]=$dt[0];
			$datos["actual"]=$nro_pag;
		}else{
			$datos["licitaciones"]=array();
		}
		$selected="";
		$datos["selected"]=$selected;
		$this->load->view('confluence/listar_lic_pub',$datos);
	}

	public function listar_adj_pub($nro_pag=1){
		$id_sector=0;
		$username="%20";
		$dt=$this->confluence->listar_adjudicaciones($username, $id_sector, $nro_pag, "");
		if(is_array($dt[0]) && sizeof($dt[0])>0){
			$x=0;
			$paginador=$this->params->paginador($nro_pag, $dt[1]);
			$datos["selected"]="";
			$datos["paginador"]=$paginador;
			$datos["adjudicaciones"]=$dt[0];
			$datos["actual"]=$nro_pag;
		}else{
			$datos["adjudicaciones"]=array();
		}
		$this->load->view('confluence/listar_adj_pub',$datos);
	}

	public function listar_pro_pub($nro_pag=1){
		$id_sector=0;
		$username="%20";
		$dt=$this->confluence->listar_proyectos($username, $id_sector, $nro_pag, "");
		if(is_array($dt[0]) && sizeof($dt[0])>0){
			$x=0;
			$paginador=$this->params->paginador($nro_pag, $dt[1]);
			$datos["selected"]="";
			$datos["paginador"]=$paginador;
			$datos["proyectos"]=$dt[0];
			$datos["actual"]=$nro_pag;
		}else{
			$datos["proyectos"]=array();
		}
		$this->load->view('confluence/listar_pro_pub',$datos);
	}

	function listar_oportunidades(){
		$datos['listado_oport']=$this->confluence->oportunidades();

		$this->load->view('confluence/listar_oportunidades',$datos);
	}

	public function inf_socio($username){
		if($username!=""){
			$socio_validar=$this->confluence->tipo_socio($username);
		    $datos['nombre_usuario']=$socio_validar->nombre_completo_socio;
		    $datos['email_usuario']=$socio_validar->email_socio;
		    $datos['fono_usuario']=$socio_validar->fono_user_socio;

		    $datos['nombre_socio']=$socio_validar->Razon_social_socio;
		    $datos['membresia']=$socio_validar->tipo_socio;
		    $datos['fecha_inicio']=$socio_validar->fecha_inscripcion;
		    $datos['fecha_renovacion']=$socio_validar->fecha_caduca;

			$contacto_socio=$this->confluence->usuario_contacto($socio_validar->id_socio,$socio_validar->id_contacto_admin_socio);

			$datos['nombre_contacto_socio']=$contacto_socio->nombre_completo_socio;
			$datos['fono_contacto_socio']=$contacto_socio->fono_user_socio;
			$datos['rut_id_socio']=$socio_validar->rut_id_socio;
		    $id_vendedor=$socio_validar->id_vendedora;
			$datos_vendedor=$this->confluence->datos_vendedor($id_vendedor);

			/*$datos['nombre_vendedor']=$datos_vendedor->Nombre_completo_vendedor;
			$datos['fono_vendedor']=$datos_vendedor->Fono_vendedor;
			$datos['email_vendedor']=$datos_vendedor->Email_vendedor;*/

			$datos['nombre_vendedor']=$datos_vendedor->Nombre_completo_user;
			$telefono=$datos_vendedor->Fono_user;
			if($datos_vendedor->Anexo_user!=0){
				$telefono.=" Anexo: ".$datos_vendedor->Anexo_user;
			}
			$datos['fono_vendedor']=$telefono;
			$datos['email_vendedor']=$datos_vendedor->Email_user;

			$total_usuarios='<ul>';
			$usuarios_socio=$this->confluence->usuarios_socio($socio_validar->id_socio);
			$url=$this->params->url_confluence_dns."/display/~";
			foreach($usuarios_socio as $usocio){
				$total_usuarios.='<li style="padding:5px 2px; margin:0px; float:left; width:49%"><a style="text-decoration:none" href="'.$url.$usocio->username_socio.'" target="_blank">'.$usocio->nombre_completo_socio.'</a></li>';
			}
			$total_usuarios.='</ul>';
			$datos['usuarios']=$total_usuarios;
			//$dato['nombre']='';
			$this->load->view('confluence/inf_socio',$datos);
		}
	}

	public function inf_socio_basica($username){
		if($username!=""){
			$socio_validar=$this->confluence->tipo_socio($username);
		    $datos['nombre_usuario']=$socio_validar->nombre_completo_socio;
		    $datos['email_usuario']=$socio_validar->email_socio;
		    $datos['fono_usuario']=$socio_validar->fono_user_socio;

		    $datos['nombre_socio']=$socio_validar->Razon_social_socio;
		    $datos['membresia']=$socio_validar->tipo_socio;
		    $datos['fecha_inicio']=$socio_validar->fecha_inscripcion;
		    $datos['fecha_renovacion']=$socio_validar->fecha_caduca;
			$datos['nombre_contacto_socio']=$socio_validar->Nombre_contacto_socio;
			$datos['fono_contacto_socio']=$socio_validar->Telefono_contacto_socio;
			$datos['rut_id_socio']=$socio_validar->rut_id_socio;
		    $id_vendedor=$socio_validar->id_vendedora;
			$datos_vendedor=$this->confluence->datos_vendedor($id_vendedor);

			/*$datos['nombre_vendedor']=$datos_vendedor->Nombre_completo_vendedor;
			$datos['fono_vendedor']=$datos_vendedor->Fono_vendedor;
			$datos['email_vendedor']=$datos_vendedor->Email_vendedor;*/

			$datos['nombre_vendedor']=$datos_vendedor->Nombre_completo_user;
			$telefono=$datos_vendedor->Fono_user;
			if($datos_vendedor->Anexo_user!=0){
				$telefono.=" Anexo: ".$datos_vendedor->Anexo_user;
			}
			$datos['fono_vendedor']=$telefono;
			$datos['email_vendedor']=$datos_vendedor->Email_user;

			$total_usuarios='<ul>';
			$usuarios_socio=$this->confluence->usuarios_socio($socio_validar->id_socio);
			$url="https://".$this->params->url_confluence_dns."/display/~";
			foreach($usuarios_socio as $usocio){
				$total_usuarios.='<li style="padding:5px 2px; margin:0px; float:left; width:49%"><a style="text-decoration:none" href="'.$url.$usocio->username_socio.'" target="_blank">'.$usocio->nombre_completo_socio.'</a></li>';
			}
			$total_usuarios.='</ul>';
			$datos['usuarios']=$total_usuarios;
			//$dato['nombre']='';
			$this->load->view('confluence/inf_socio_basic',$datos);
		}
	}

 	public function iconos_muro($username){
		//$this->output->enable_profiler(true);
		$socio_validar=$this->confluence->tipo_socio($username);
	    $datos['menu_premium']=$socio_validar->tipo_socio;
		$datos['url']="https://".$this->params->url_confluence_dns."/";
		$this->load->view('confluence/iconos_muro',$datos);
	}

	public function ver_video_pro($id){
		if($id==86){
			$datos['link_video']=URL_PUBLICA_CONFLUENCE.'/sitio_portal/videos/proyectos/NNM_Agosto2011.mp4';
			$this->load->view('confluence/ver_video_pro',$datos);
		}
		else{
			echo "Este proyecto no tiene video";
		}
	}

 
	public function registra_visita(){
		$method=(isset($_GET["method"]) ? $_GET["method"] : "");
		$data['User_visit']=$_GET['user'];
		$data['PageId_visit']=$_GET['pageId'];
		$data['Space_visit']=$_GET['space'];
		$data['Titulo_visit']=$_GET['title'];
		$this->confluence->ingresa_visita($data);
		echo $method."(".json_encode(array("estado"=>'bien')).")";
	}


	//04-10-2013
	//vista pequeña de proyectos sugeridos
	public function carga_vista_socio_proyecto($username){
		


		if($username != "" && $username != NULL){
		    

			$username = base64_decode(str_replace("_", "=", $username));
			$datos_user = $this->confluence->busca_info_userlog($username);

			if($datos_user['tipo_socio'] <> 'Mandante'){
			   
				//valida que exista el usuario y que tenga su registro en la tabla emp_prov
			    
				$valida = $this->confluence->valida_usuario_dir_proveedores($username);
			
				if($valida === true){
					//caso verdadero: retorna id socio

					$limite_resultados = 10;
					//busca los proyectos que tengan equipos, suministros y servicios en comun con el usuario logueado
					$datos_user = $this->confluence->busca_info_userlog($username);
					
					$array_filtros = array(
												'pais' 			=> $datos_user['id_pais']
										   );
					
				$datos['result'] = $this->confluence->procesa_listado_sugeridos_completo($username, 0, $array_filtros, 1);
					
					
					
					$datos['estado'] = true;
					$this->load->view('confluence/vista_lista_sugeridos', $datos);

				}else if($valida === false){
					//no se encuentra username

					$datos['estado'] = 'No se encuentran datos';
					$this->load->view('confluence/vista_lista_sugeridos', $datos);

				}else if($valida === 'sin_portafolio'){
					//no posee registro en dir_prov

					$datos['estado'] = 'No posee perfil en portafolios';
					$this->load->view('confluence/vista_lista_sugeridos', $datos);

				}
			}else{
				$datos['no_entra'] = true;
				$this->load->view('confluence/vista_lista_sugeridos', $datos);

			}


		}else{
			echo "Debe iniciar sesi&oacute;n para ver contenido";
		}
	}

	public function listar_proyectos_relacionados($username, $origen = 0, $argumentos = ''){
		//$method = isset($_GET["method"]) ? $_GET["method"] : "";
		//origen: 0 = listar sin filtro - 1 = listado llamado de ajax (con filtro)
		//$this->output->enable_profiler(true);
		$pagina = 1;
		if($username != "" && $username != NULL){
			//origen determina el origen del llamado (0 = normal - 1 = ajax)

			$array_filtros = array();
			if($origen == 0){
				$username = base64_decode(str_replace("_", "=", $username));

				//detectar pais del usuario loggeado
				$datos_user = $this->confluence->busca_info_userlog($username);
				$array_filtros = array(
											'pais' 			=> 0,
											'pais_ordenar' 	=> $datos_user['id_pais']
									   );

				//establecer orden y sentido de la consulta por default
				$order_by = array(
									'campo' => 'default'
								);

			}else if($origen == 1){
				//muestra data en formato json para procesar con ajax (ya no se usa ajax)
				$username = base64_decode(str_replace("_", "=", $username));
				$argumentos = base64_decode(str_replace("_", "=", $argumentos));
				$argumentos = explode('&', $argumentos);

				$datos_user = $this->confluence->busca_info_userlog($username);

				$array_filtros = array(
											'tipo_proyecto' => $argumentos[0],
											'mandante' 		=> $argumentos[1],
											'pais' 			=> $argumentos[2],
											'region' 		=> $argumentos[3],
											'obras' 		=> $argumentos[4],
											'equipos' 		=> $argumentos[5],
											'suministros' 	=> $argumentos[6],
											'servicios' 	=> $argumentos[7],
											'etapa' 		=> $argumentos[8],
											'responsable' 	=> $argumentos[9],
											'busqueda'		=> $argumentos[10]
									   );

				$pagina = $argumentos[11];

				$order_by = array(
									'campo' 	=> $argumentos[12],
									'sentido' 	=> $argumentos[13]
								 );

				$resultado = 0;
				for($i=0;$i<=9;$i++){

					if($argumentos[$i] <> 0){
						$resultado++;
					}
				}

				if($resultado == 0){
					//viene por defecto
					$array_filtros['pais_ordenar'] = $datos_user['id_pais'];
				}else{
					//se selecciono un filtro
					$array_filtros['pais_ordenar'] = $array_filtros['pais'];
				}



				/*echo $method."(".json_encode($envio_datos).")";*/

			}

			//valida si no es mandante
			if($datos_user['tipo_socio'] <> 'Mandante'){
				//valida que exista el usuario y que tenga su registro en la tabla emp_prov
				$valida = $this->confluence->valida_usuario_dir_proveedores($username);

				if($valida === true){
					//caso verdadero
					//busca los proyectos que tengan equipos, suministros y servicios en comun con el usuario logueado
					$data_recibe = $this->confluence->procesa_listado_sugeridos_completo($username, $origen, $array_filtros, $pagina, $order_by);

					//genera archivo js para seguimiento de proyectos
					if(!is_null($data_recibe['contenedor'])){
						$this->confluence->generar_js_proyectos_version_array($data_recibe['contenedor'], $username, $origen);
					}

					$datos['proyectos'] = $data_recibe['contenedor'];
					$datos['paginador'] = $data_recibe['paginador'];


					//carga datos de select de filtros
					$servicio = $this->ficha->servicios_sel($datos_user['tipo_socio'], 0, $username);
					unset($servicio[""]);
					$datos["servicio"] = $servicio;
					$datos["servicio_default"] = (isset($array_filtros['servicios'])) ? $array_filtros['servicios'] : null;

					$equipo = $this->ficha->equipos_sel($datos_user['tipo_socio'], 0, $username);
					unset($equipo[""]);
					$datos["equipo"] = $equipo;
					$datos["equipo_default"] = (isset($array_filtros['equipos'])) ? $array_filtros['equipos'] : null;

					$suministro = $this->ficha->suministro_sel($datos_user['tipo_socio'], 0, $username);
					unset($suministro[""]);
					$datos["suministro"] = $suministro;
					$datos["suministro_default"] = (isset($array_filtros['suministros'])) ? $array_filtros['suministros'] : null;

					$obra = $this->ficha->obras_sel($datos_user['tipo_socio'], 0, $username);
					unset($obra[""]);
					$datos["obra"] = $obra;
					$datos["obra_default"] = (isset($array_filtros['obras'])) ? $array_filtros['obras'] : null;

					$tipo = $this->ficha->tipo_sel($datos_user['tipo_socio'], 0, $username);
					unset($tipo[""]);
					$datos["tipo"] = $tipo;
					$datos["tipo_default"] = (isset($array_filtros['tipo_proyecto'])) ? $array_filtros['tipo_proyecto'] : null;

					$etapa = $this->etapa->listar_etapas_all_cbo();
					unset($etapa[""]);
					$datos["etapa"] = $etapa;
					$datos["etapa_default"] = (isset($array_filtros['etapa'])) ? $array_filtros['etapa'] : null;

					$pais = $this->ficha->pais_sel($datos_user['tipo_socio'], 0, $username);
					unset($pais[""]);
					$datos["pais"] = $pais;
					if(isset($array_filtros['pais'])){
						if($origen <> 0){
							if($argumentos[2] == 0){
								//si no se habia seleccionado ningun pais
								$datos["pais_default"] = null;

							}else{
								$datos["pais_default"] = $array_filtros['pais'];
							}

						}else{
							$datos["pais_default"] = 0;
						}
					}else{
						$datos["pais_default"] = null;
					}
					//$datos["pais_default"] = (isset($array_filtros['pais'])) ? $array_filtros['pais'] : null;

					$empresa = $this->ficha->responsable_sel($datos_user['tipo_socio'], 0, $username);
					unset($empresa[""]);
					$datos["empresa"] = $empresa;
					$datos["empresa_default"] = (isset($array_filtros['responsable'])) ? $array_filtros['responsable'] : null;

					$region = $this->region->llenar_combo_region_chile();
					unset($region[""]);
					$datos["region"] = $region;
					$datos["region_default"] = (isset($array_filtros['region'])) ? $array_filtros['region'] : null;

					$mandante = $this->ficha->mandante_sel($datos_user['tipo_socio'], 0, $username);
					unset($mandante[""]);
					$datos["mandante"] = $mandante;
					$datos["mandante_default"] = (isset($array_filtros['mandante'])) ? $array_filtros['mandante'] : null;

					$datos["busqueda_default"] = (isset($array_filtros['busqueda'])) ? $array_filtros['busqueda'] : null;


					$datos['campo_orden_def'] = $order_by['campo'];

					if($order_by['campo'] == 'default'){
						//cuando es el orden por defecto, no se necesita rellenar este campo
						$datos['sent_campo_def'] = null;
						$datos['sentido_default'] = null;

					}else{
						if($order_by['sentido'] == 'asc'){
							$datos['sent_campo_def'] = 'desc';

						}else if($order_by['sentido'] == 'desc'){
							$datos['sent_campo_def'] = 'asc';

						}
						$datos['sentido_default'] = $order_by['sentido'];

					}

					$datos['ruta_img_flecha']['asc'] = "/sitio_portal/images/arrowup.png";
					$datos['ruta_img_flecha']['desc'] = "/sitio_portal/images/arrowdown.png";

					$datos['pagina_def'] = $pagina;


					$datos['nombre'] = '';
					$datos['estado'] = true;
					$this->load->view('confluence/vista_proyectos_sugeridos', $datos);

				}else if($valida === false){
					//no se encuentra username
					$datos["busqueda_default"] = null;
					$datos['estado'] = 'No se encuentran datos';
					$this->load->view('confluence/vista_proyectos_sugeridos', $datos);

				}else if($valida === 'sin_portafolio'){
					//no posee registro en dir_prov
					$datos["busqueda_default"] = null;
					$datos['estado'] = 'No posee perfil en portafolios';
					$this->load->view('confluence/vista_proyectos_sugeridos', $datos);

				}
			}else{
				$datos['no_entra'] = true;
				$datos['estado'] = 'Usted no puede acceder desde esta cuenta.';
				$this->load->view('confluence/vista_proyectos_sugeridos', $datos);
			}


		}else{
			echo "Debe iniciar sesi&oacute;n para ver contenido";
		}
	}


	//16-10-2013
	public function listar_licitaciones_relacionadas($username, $origen = 0, $argumentos = ''){
		$pagina = 1;
		if($username != "" && $username != NULL){
			//origen determina el origen del llamado (0 = normal - 1 = ajax)

			$array_filtros = array();
			if($origen == 0){
				$username = base64_decode(str_replace("_", "=", $username));

				//detectar pais del usuario loggeado
				$datos_user = $this->confluence->busca_info_userlog($username);
				/*$array_filtros = array(
											'pais' 	=> $datos_user['id_pais']
									   );*/
				$array_filtros = array(
											'pais' 			=> 0,
											'pais_ordenar' 	=> $datos_user['id_pais']
									   );
				//establecer orden y sentido de la consulta por default
				$order_by = array(
									'campo' => 'default'
								);

			}else if($origen == 1){
				//muestra data en formato json para procesar con ajax (ya no se usa ajax)
				$username = base64_decode(str_replace("_", "=", $username));
				$argumentos = base64_decode(str_replace("_", "=", $argumentos));
				$argumentos = explode('&', $argumentos);

				$datos_user = $this->confluence->busca_info_userlog($username);

				$array_filtros = array(
											'sector' 		=> $argumentos[0],
											'mandante' 		=> $argumentos[1],
											'pais' 			=> $argumentos[2],
											'region' 		=> $argumentos[3],
											'reg_prov' 		=> $argumentos[4],
											'tipo_lici'		=> $argumentos[5],
											'obra' 			=> $argumentos[6],
											'equipo' 		=> $argumentos[7],
											'suministro' 	=> $argumentos[8],
											'servicio' 		=> $argumentos[9],
											'tipo'			=> $argumentos[10],
											'rubro' 		=> $argumentos[11],
											'busqueda' 		=> $argumentos[12]
									   );

				$pagina = $argumentos[13];

				$order_by = array(
									'campo' 	=> $argumentos[14],
									'sentido' 	=> $argumentos[15]
								 );

				$resultado = 0;
				for($i=0;$i<=11;$i++){

					if($argumentos[$i] <> 0){
						$resultado++;
					}
				}

				if($resultado == 0){
					//viene por defecto
					$array_filtros['pais_ordenar'] = $datos_user['id_pais'];
				}else{
					//se selecciono un filtro
					$array_filtros['pais_ordenar'] = $array_filtros['pais'];
				}

				/*echo $method."(".json_encode($envio_datos).")";*/

			}

			//valida si no es mandante
			if($datos_user['tipo_socio'] <> 'Mandante'){

				//valida que exista el usuario y que tenga su registro en la tabla emp_prov
				$valida = $this->confluence->valida_usuario_dir_proveedores($username);

				if($valida === true){
					//caso verdadero
					//busca los licitaciones que tengan equipos, suministros y servicios en comun con el usuario logueado
					//$data_recibe = $this->confluence->procesa_listado_licitaciones_sugeridas($username, $origen, $array_filtros, $pagina);

					$data_recibe = $this->confluence->procesa_listado_licitaciones_sugeridas($username, $origen, $array_filtros, $pagina, $order_by);

					$datos['licitaciones'] 	= $data_recibe['contenedor'];
					$datos['paginador'] 	= $data_recibe['paginador'];
					//die(var_dump($username));
					$servicio = $this->licitacion->servicios_sel($datos_user['tipo_socio'], 0, $username);
					unset($servicio[""]);
					$datos['servicio'] 			= $servicio;
					$datos["servicio_default"] 	= (isset($array_filtros['servicio'])) ? $array_filtros['servicio'] : null;

					$equipo = $this->licitacion->equipos_sel($datos_user['tipo_socio'], 0, $username);
					unset($equipo[""]);

					$datos['equipo'] 			= $equipo;
					$datos["equipo_default"] 	= (isset($array_filtros['equipo'])) ? $array_filtros['equipo'] : null;

					$suministro = $this->licitacion->suministro_sel($datos_user['tipo_socio'], 0, $username);
					unset($suministro[""]);
					$datos['suministro'] 			= $suministro;
					$datos["suministro_default"] 	= (isset($array_filtros['suministro'])) ? $array_filtros['suministro'] : null;

					$obra = $this->licitacion->obras_sel($datos_user['tipo_socio'], 0, $username);
					unset($obra[""]);
					$datos['obra'] 			= $obra;
					$datos["obra_default"] 	= (isset($array_filtros['obra'])) ? $array_filtros['obra'] : null;

					$tipo = $this->licitacion->tipo_sel($datos_user['tipo_socio'], 0, $username);
					unset($tipo[""]);
					$datos['tipo'] 			= $tipo;
					$datos["tipo_default"] 	= (isset($array_filtros['tipo'])) ? $array_filtros['tipo'] : null;

					$pais = $this->licitacion->pais_sel($datos_user['tipo_socio'], 0, $username);
					unset($pais[""]);
					$datos['pais'] 			= $pais;
					//$datos["pais_default"] 	= (isset($array_filtros['pais'])) ? $array_filtros['pais'] : null;

					if(isset($array_filtros['pais'])){
						if($origen <> 0){
							if($argumentos[2] == 0){
								//si no se habia seleccionado ningun pais
								$datos["pais_default"] = null;

							}else{
								$datos["pais_default"] = $array_filtros['pais'];
							}

						}else{
							$datos["pais_default"] = 0;
						}
					}else{
						$datos["pais_default"] = null;
					}

					$region = $this->region->llenar_combo_region_chile();
					unset($region[""]);
					$datos['region'] 			= $region;
					$datos["region_default"] 	= (isset($array_filtros['region'])) ? $array_filtros['region'] : null;

					$mandante = $this->licitacion->mandante_sel($datos_user['tipo_socio'], 0, $username);
					unset($mandante[""]);
					$datos['mandante'] 			= $mandante;
					$datos["mandante_default"]  = (isset($array_filtros['mandante'])) ? $array_filtros['mandante'] : null;

					$tipo_lici = $this->licitacion->lici_tipo_sel($datos_user['tipo_socio'], 0, $username);
					unset($tipo_lici[""]);
					$datos['tipo_lici'] 		= $tipo_lici;
					$datos["tipo_lici_default"] = (isset($array_filtros['tipo_lici'])) ? $array_filtros['tipo_lici'] : null;

					$rubro = $this->licitacion->rubro_sel($datos_user['tipo_socio'], 0, $username);
					unset($rubro[""]);
					$datos['rubro'] 		= $rubro;
					$datos["rubro_default"] = (isset($array_filtros['rubro'])) ? $array_filtros['rubro'] : null;

					$reg_prov = $this->licitacion->reg_prov_sel($datos_user['tipo_socio'], 0, $username);
					unset($reg_prov[""]);
					$datos['reg_prov'] 			= $reg_prov;
					$datos["reg_prov_default"]  = (isset($array_filtros['reg_prov'])) ? $array_filtros['reg_prov'] : null;

					$sector1 = $this->licitacion->get_sector();
					unset($sector1[""]);
					$datos['sector'] 		 = $sector1;
					$datos["sector_default"] = (isset($array_filtros['sector'])) ? $array_filtros['sector'] : null;

					$datos["busqueda_default"] = (isset($array_filtros['busqueda'])) ? $array_filtros['busqueda'] : null;

					$datos['campo_orden_def'] = $order_by['campo'];

					if($order_by['campo'] == 'default'){
						//cuando es el orden por defecto, no se necesita rellenar este campo
						$datos['sent_campo_def'] = null;
						$datos['sentido_default'] = null;

					}else{
						if($order_by['sentido'] == 'asc'){
							$datos['sent_campo_def'] = 'desc';

						}else if($order_by['sentido'] == 'desc'){
							$datos['sent_campo_def'] = 'asc';

						}

						$datos['sentido_default'] = $order_by['sentido'];
					}

					$datos['ruta_img_flecha']['asc'] = "/sitio_portal/images/arrowup.png";
					$datos['ruta_img_flecha']['desc'] = "/sitio_portal/images/arrowdown.png";

					$datos['pagina_def'] = $pagina;


					$datos['nombre'] = '';
					$datos['estado'] = true;

					$this->load->view('confluence/vista_licitaciones_sugeridas', $datos);
				}else if($valida === false){
					//no se encuentra username
					$datos["busqueda_default"] = null;
					$datos['estado'] = 'No se encuentran datos';
					$this->load->view('confluence/vista_licitaciones_sugeridas', $datos);

				}else if($valida === 'sin_portafolio'){
					//no posee registro en dir_prov
					$datos["busqueda_default"] = null;
					$datos['estado'] = 'No posee perfil en portafolios';
					$this->load->view('confluence/vista_licitaciones_sugeridas', $datos);

				}
			}else{
				$datos['no_entra'] = true;
				$datos['estado'] = 'Usted no puede acceder desde esta cuenta.';
				$this->load->view('confluence/vista_licitaciones_sugeridas', $datos);
			}
		}
	}

	//21-10-2013
	public function listar_adjudicaciones_relacionadas($username, $origen = 0, $argumentos = ''){
		$pagina = 1;
		if($username != "" && $username != NULL){
			//origen determina el origen del llamado (0 = normal - 1 = ajax)
			$array_filtros = array();
			if($origen == 0){
				$username = base64_decode(str_replace("_", "=", $username));

				//detectar pais del usuario loggeado
				$datos_user = $this->confluence->busca_info_userlog($username);

				$array_filtros = array(
											'pais' 			=> 0,
											'pais_ordenar' 	=> $datos_user['id_pais']
									   );
				//establecer orden y sentido de la consulta por default
				$order_by = array(
									'campo' => 'default'
								);

			}else if($origen == 1){
				//muestra data en formato json para procesar con ajax (ya no se usa ajax)
				$username = base64_decode(str_replace("_", "=", $username));
				$argumentos = base64_decode(str_replace("_", "=", $argumentos));
				$argumentos = explode('&', $argumentos);


				$datos_user = $this->confluence->busca_info_userlog($username);

				$array_filtros = array(
											'empadj'		=> $argumentos[0],
											'via' 			=> $argumentos[1],
											'comprador' 	=> $argumentos[2],
											'equipo' 		=> $argumentos[3],
											'suministro' 	=> $argumentos[4],
											'pais'			=> $argumentos[5],
											'region' 		=> $argumentos[6],
											'catservicio' 	=> $argumentos[7],
											'subcatservicio'=> $argumentos[8],
											'obra' 			=> $argumentos[9],
											'tipo'			=> $argumentos[10],
											'busqueda'		=> $argumentos[11]
									   );

				$pagina = $argumentos[12];

				$order_by = array(
									'campo' 	=> $argumentos[13],
									'sentido' 	=> $argumentos[14]
								 );

				$resultado = 0;
				for($i=0;$i<=11;$i++){

					if($argumentos[$i] <> 0){
						$resultado++;
					}
				}

				if($resultado == 0){
					//viene por defecto
					$array_filtros['pais_ordenar'] = $datos_user['id_pais'];
				}else{
					//se selecciono un filtro
					$array_filtros['pais_ordenar'] = $array_filtros['pais'];
				}

				/*echo $method."(".json_encode($envio_datos).")";*/

			}

			//valida si no es mandante
			if($datos_user['tipo_socio'] <> 'Mandante'){
				//valida que exista el usuario y que tenga su registro en la tabla emp_prov
				$valida = $this->confluence->valida_usuario_dir_proveedores($username);

				if($valida === true){
					//caso verdadero
					//busca los licitaciones que tengan equipos, suministros y servicios en comun con el usuario logueado
					$data_recibe = $this->confluence->procesa_listado_adjudicaciones_sugeridas($username, $origen, $array_filtros, $pagina, $order_by);

					$datos['adjudicaciones'] 	= $data_recibe['contenedor'];
					$datos['paginador'] 		= $data_recibe['paginador'];

					$empadj = $this->adjudicacion->empadj_sel($datos_user['tipo_socio'], 0, $username);
					unset($empadj[""]);
					$datos["empadj"] = $empadj;
					$datos["empadj_default"] = (isset($array_filtros['empadj'])) ? $array_filtros['empadj'] : null;

					$via = $this->confluence->get_via();
					unset($via[""]);
					$datos["via"] = $via;
					$datos["via_default"] = (isset($array_filtros['via'])) ? $array_filtros['via'] : null;

					$comprador = $this->adjudicacion->comprador_sel($datos_user['tipo_socio'], 0, $username);
					unset($comprador[""]);
					$datos["comprador"] = $comprador;
					$datos["comprador_default"] = (isset($array_filtros['comprador'])) ? $array_filtros['comprador'] : null;

					$equipo = $this->adjudicacion->equipos_sel($datos_user['tipo_socio'], 0, $username);
					unset($equipo[""]);
					$datos["equipo"] = $equipo;
					$datos["equipo_default"] = (isset($array_filtros['equipo'])) ? $array_filtros['equipo'] : null;

					$suministro = $this->adjudicacion->suministro_sel($datos_user['tipo_socio'], 0, $username);
					unset($suministro[""]);
					$datos["suministro"] = $suministro;
					$datos["suministro_default"] = (isset($array_filtros['suministro'])) ? $array_filtros['suministro'] : null;

					$pais = $this->adjudicacion->pais_sel($datos_user['tipo_socio'], 0, $username);
					unset($pais[""]);
					$datos["pais"] = $pais;
					$datos["pais_default"] = (isset($array_filtros['pais'])) ? $array_filtros['pais'] : null;

					//consultar por el nuevo campo de seleccion de pais
					if($datos["pais_default"] != "" && $datos["pais_default"] != NULL && $datos["pais_default"] != 0){
						$re = $this->region->get_regiones($datos["pais_default"]);
						foreach($re as $r){
							$region[$r->id_region] = $r->Nombre_region;
						}

					}else{
						$region = array();
					}

					$datos["region"] = $region;
					$datos["region_default"] = (isset($array_filtros['region'])) ? $array_filtros['region'] : null;

					$catservicio = $this->adjudicacion->catservicios_sel($datos_user['tipo_socio'], 0, $username);
					unset($catservicio[""]);
					$datos["catservicio"] = $catservicio;
					$datos["catservicio_default"] = (isset($array_filtros['catservicio'])) ? $array_filtros['catservicio'] : null;

					$subcatservicio = array();
					if($datos["catservicio_default"] != "" && $datos["catservicio_default"] != NULL && $datos["catservicio_default"] != 0){
						$subcatservicio = $this->adjudicacion->subcatservicios_sel($datos_user['tipo_socio'], 0, $username, $datos["catservicio_default"]);

						unset($subcatservicio[""]);
					}

					$datos["subcatservicio"] = $subcatservicio;
					$datos["subcatservicio_default"] = (isset($array_filtros['subcatservicio'])) ? $array_filtros['subcatservicio'] : null;

					$obra = $this->adjudicacion->obras_sel($datos_user['tipo_socio'], 0, $username);
					unset($obra[""]);
					$datos["obra"] = $obra;
					$datos["obra_default"] = (isset($array_filtros['obra'])) ? $array_filtros['obra'] : null;

					$tipo=$this->adjudicacion->tipo_sel($datos_user['tipo_socio'], 0, $username);
					unset($tipo[""]);
					$datos["tipo"] = $tipo;
					$datos["tipo_default"] = (isset($array_filtros['tipo'])) ? $array_filtros['tipo'] : null;

					$datos["busqueda_default"] = (isset($array_filtros['busqueda'])) ? $array_filtros['busqueda'] : null;

					$datos['campo_orden_def'] = $order_by['campo'];

					if($order_by['campo'] == 'default'){
						//cuando es el orden por defecto, no se necesita rellenar este campo
						$datos['sent_campo_def'] = null;
						$datos['sentido_default'] = null;

					}else{
						if($order_by['sentido'] == 'asc'){
							$datos['sent_campo_def'] = 'desc';

						}else if($order_by['sentido'] == 'desc'){
							$datos['sent_campo_def'] = 'asc';

						}

						$datos['sentido_default'] = $order_by['sentido'];
					}

					$datos['ruta_img_flecha']['asc'] = "/sitio_portal/images/arrowup.png";
					$datos['ruta_img_flecha']['desc'] = "/sitio_portal/images/arrowdown.png";

					$datos['pagina_def'] = $pagina;


					$datos['nombre'] = '';
					$datos['estado'] = true;

					$this->load->view('confluence/vista_adjudicaciones_sugeridas', $datos);
				}else if($valida === false){
					//no se encuentra username
					$datos["busqueda_default"] = null;
					$datos['estado'] = 'No se encuentran datos';
					$this->load->view('confluence/vista_adjudicaciones_sugeridas', $datos);

				}else if($valida === 'sin_portafolio'){
					//no posee registro en dir_prov
					$datos["busqueda_default"] = null;
					$datos['estado'] = 'No posee perfil en portafolios';
					$this->load->view('confluence/vista_adjudicaciones_sugeridas', $datos);

				}
			}else{
				$datos['no_entra'] = true;
				$datos['estado'] = 'Usted no puede acceder desde esta cuenta.';
				$this->load->view('confluence/vista_adjudicaciones_sugeridas', $datos);
			}
		}

	}
	function listado_boletines_socio($username){
		if($username != "" && $username != NULL){
			$username = base64_decode(str_replace("_", "=", $username));
			$datos_user = $this->confluence->busca_info_userlog($username);
			if($datos_user['tipo_socio']=="Premium"){
				$listado=$this->confluence->listado_boletines_premium();
			}
			else if(($datos_user['tipo_socio']=='Preferencial')||($datos_user['tipo_socio']=='Especial')){
				$listado=$this->confluence->listado_boletines_preferencial();
			}
			else{
				echo "Usted no tiene permiso para ver esto.";
				exit;
			}
			$this->load->view('confluence/listado_boletin',array('listado'=>$listado));
		}
		else{
				echo "Usted no tiene permiso para ver esto.";
			}
	}
}
?>
