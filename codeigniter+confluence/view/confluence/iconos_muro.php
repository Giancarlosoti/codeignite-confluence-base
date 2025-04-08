<style type="text/css">
a:hover{text-decoration:underline;}
</style>

<script>
    window.onload = function() {
        let images = document.querySelectorAll('img');
        images.forEach(function(img) {
            img.style.removeProperty('filter');
        });
    };
</script>
<div style="display: flex;justify-content: space-between;width: 100%;max-width: 800px;">
<?
/*if($menu_premium=='Mandante')
$link_video='/pages/viewpage.action?pageId=75864805';
else
$link_video='/pages/viewpage.action?pageId=75863048';
?>
<div style="float:left;width:205px;height:60px;margin:5px 3px"><a href="<?=$link_video?>" style="font-size:11px; font-family:Verdana, Geneva, sans-serif;color:#236daa; text-decoration:none">

	<div style="position:relative; top:18px; left:0px; right:auto; bottom:auto;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;background-color:#fad4c9;height:40px; margin-bottom:2px;border:1px solid #236daa">
	  <div style="position:relative;width:150px;float:left;padding:3px 9px;line-height:15px" align="center">Presentación herramienta de Proveedores.</div>
	  <div style="position:absolute; float:right; top:-14px; right:0px; left:auto; bottom:auto; width:40px;"><img src="<?=base_url()?>images/HDPT.gif" width="39" height="51" /></div></div>
</a>
</div>
<?*/
if($menu_premium=='Mandante'){
?>
<div style="float:left;width:130px;height:60px;margin:5px 3px">
	<a href="/display/acce/Mis+Proveedores" style="font-size:11px; font-family:Verdana, Geneva, sans-serif;color:#fff; text-decoration:none">
	<div style="position:relative; top:18px; left:0px; right:auto; bottom:auto;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;background-color:#00bfa5;height:40px; margin-bottom:2px;border:1px solid #00bfa5;" class="brillo">
	  <div style="position:absolute;width:75px;float:left;padding:3px 8px;line-height:15px" align="center">Eval&uacute;e a sus proveedores</div>
	  <div style="position:absolute; float:right; top:-14px; right:0px; left:auto; bottom:auto; width:40px;"><img class="saturar_img" src="<?=base_url()?>images/evalue.gif" width="38" height="50" /></div></div>
      </a>
</div>
<div style="float:left;width:127px;height:60px;margin:5px 0px 5px 3px">
	<a href="/display/acce/Buscar+Proveedor" style="font-size:11px; font-family:Verdana, Geneva, sans-serif;color:#fff; text-decoration:none">
	<div style="position:relative; top:18px; left:0px; right:auto; bottom:auto;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;background-color:#00bfa5;height:40px; margin-bottom:2px;border:1px solid #00bfa5;" class="brillo">
	  <div style="position:absolute;width:65px;float:left;padding:3px 8px;line-height:15px" align="center">Comparar proveedores</div>
	  <div style="position:absolute; float:right; top:-14px; right:0px; left:auto; bottom:auto; width:40px;"><img class="saturar_img" src="<?=base_url()?>images/comparar3.gif" width="38" height="50" /></div></div>
      </a>

</div>
<?
}
else if($menu_premium=='Premium'){
/*?>
<div style="float:left;width:160px;height:60px;margin:5px 0px 5px 5px">
	<a href="/display/acce/Agregar+Adjudicaciones" style="font-size:11px; font-family:Verdana, Geneva, sans-serif;color:#236daa; text-decoration:none">
	<div style="position:relative; top:18px; left:0px; right:auto; bottom:auto;-moz-border-radius: 5px;-webkit-border-radius: 5px;border-radius: 5px;background-color:#fad4c9;height:40px; margin-bottom:2px;border:1px solid #236daa"">
	  <div style="position:absolute;width:100px;float:left;padding:3px 8px;line-height:15px" align="center">Ingrese aqu&iacute; sus experiencias </div>
	  <div style="position:absolute; float:right; top:-14px; right:0px; left:auto; bottom:auto; width:40px;"><img src="<?=base_url()?>images/ingrese.gif" width="38" height="50" /></div></div>
	</a>
</div>
<?*/
?>
	<div style="float:left;width:507px;height:93px;filter: saturate(90%) !important;">
		<a href="/display/acce/Listado+de+Boletines+Socios" style="font-size:11px; font-family:Verdana, Geneva, sans-serif;color:#236daa; text-decoration:none;filter: saturate(90%);">
		<img src="<?=base_url()?>images/boletin.png" width="350" height="93" style="filter: saturate(90%) !important;" />
		</a>
	</div>
<?
}
else if(($menu_premium=='Preferencial')||($menu_premium=='Especial')){
?>
	<div style="float:left;width:507px;height:93px;">
		<a href="/display/acce/Listado+de+Boletines+Socios" style="font-size:11px; font-family:Verdana, Geneva, sans-serif;color:#236daa; text-decoration:none;filter: saturate(90%);">
		<img src="<?=base_url()?>images/boletin.png" wfilter: saturate(90%) !important;idth="350" height="93" style="filter: saturate(90%) !important;" />
		</a>
	</div>
<?
}
?>
	<div style="float:right;width:214px;height:93px;filter: saturate(90%) !important;">
	<a href="https://www.portalminero.com/wp/podcast-audio/podcast/index.php">
        <img class="imagen-boton"  src="<?=base_url()?>images/boton-podcast-muro.png" width="350px" height="95px" style="float: right;
        margin-right: 65px;filter: saturate(90%) !important;" />
	    </a>
	</div>
</div>