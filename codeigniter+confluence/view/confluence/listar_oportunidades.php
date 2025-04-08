<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<div align="center"><h2>Listado de las Últimas 50 Oportunidades de Negocio Publicadas en Portal Minero</h2></div>
<div style="margin:20px">
    <div id='listado_oportunidades' style="margin:0 auto;">
    <?
    foreach($listado_oport as $oport){
        ?>
        <div class='titulo' align="center">
            <span>Publicado en Portal Minero el: </span><span style='color:#CE0000;font-weight:bold'><?=$oport->fecha?></span>
        </div>
        
        <div class='contenido'>
            <a href='<?=URL_PUBLICA_CONFLUENCE?>/pages/viewpage.action?pageId=<?=$oport->id_confluence?>' style='color: #254E6D;text-decoration: none;'><?=$oport->Nombre_oport?></a>
        </div>
        <? 
        }		
    ?>
    </div>
</div>