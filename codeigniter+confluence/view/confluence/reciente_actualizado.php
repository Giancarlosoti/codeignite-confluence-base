<style>

/*mantiene los bordes de la tabla html para el contenido es la caja principal de recientemente agregados*/

/*define cómo se deben visualizar y comportar los elementos tbody en el HTML, 
incluyendo la alineación vertical del contenido, la dirección del texto y el color del borde.*/
tbody {
    display: table-row-group;
    vertical-align: middle;
    unicode-bidi: isolate;
    border-color: inherit;
}

/*mantiene el comportamiento de los elementos de celda (td y th) de la  tabla HTML -caja recientemente agregados, incluyendo la eliminación de márgenes y rellenos, 
y la alineación del texto tanto horizontal como verticalmente.*/

td, th {
    margin: 0;
    padding: 0;
    text-align: left;
    vertical-align: top;
}

/*establece cómo se deben mostrar y comportar las celdas de datos (<td>) dentro de una tabla HTML, 
incluyendo la forma en que se alinean verticalmente y cómo se maneja el texto bidireccional dentro de ellas.*/
td {
    display: table-cell;
    vertical-align: inherit;
    unicode-bidi: isolate;
}

/*incio de clases body definen color , fuentes,propiedades para la impresion , y define el tamañao para
distintos elementos*/

body {
    font-family: Arial, Helvetica, sans-serif;
    text-decoration: none;
    background-color: #ffffff;
    margin-top: 0;
    margin-bottom: 0;
    margin-left: 0;
    margin-right: 0;
    font-size: 11px;}



body {
    -webkit-print-color-adjust: exact;
    font-size: 10px;
}
body {
    font-family: Arial, Helvetica, FreeSans, sans-serif;
}

html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
    font-family: "Open Sans", Arial, sans-serif;
}

body, p, td, table, tr, .bodytext, .stepfield {
    font-size: 10pt;
    line-height: 1.3;
    color: #ffffff;
    font-weight: normal;
}

/******************Fin clases body*******************************/

/*define cómo se deben mostrar y comportar las filas de una tabla HTML-caja recientemente agregados,
 incluyendo su alineación vertical, el manejo del texto bidireccional y el color del borde.*/
tr {
    display: table-row;
    vertical-align: inherit;
    unicode-bidi: isolate;
    border-color: inherit;
}
/*configura la apariencia y el diseño de las tablas HTML-caja recienteme agregados al especificar cómo se deben manejar los bordes,
 la indentación del texto y el espaciado entre las celdas de la tabla.*/
table {
    border-collapse: separate;
    text-indent: initial;
    border-spacing: 2px;
}

/*efine la alineación del texto a la izquierda y establece el color de fondo en blanco 
para el elemento con el ID "main" en HTML-caja recientemente agregados*/
#main {
    text-align: left;
    background-color: #fff;
}

#main {
    padding: 1.25em;
    background-color: #fff;
}


/*establece que los elementos con la clase "sectionMacro", así como los párrafos (<p>), listas no ordenadas (<ul>) y listas ordenadas (<ol>) 
dentro de esos elementos, tendrán un tamaño de fuente de 10 puntos (font-size: 10pt;).*/
.sectionMacro, .sectionMacro p, .sectionMacro ul, .sectionMacro ol {
    font-size: 10pt;
}

/*stablece que no tengan ningún borde (border: none;). Esto significa que las celdas de esa tabla no tendrán bordes visibles cuando se aplique este estilo.*/
table.sectionMacro>tbody>tr>td {
    border: none;
}

/*establece que todos los elementos <div> se mostrarán como bloques de nivel de bloque y 
asegura que el texto dentro de ellos se maneje de manera independiente del texto fuera del <div>.*/
div {
    display: block;
    unicode-bidi: isolate;
}

/*estilo para los h1,h2,h3,h4,h5,h6*/
h4 {
    font-size: 12px;
}
h1, h2, h3, h4, h5, h6 {
    line-height: normal;
    font-weight: bold;
    padding: 2px 2px 2px 0;
}
h4 {
    display: block;
    margin-block-start: 1.33em;
    margin-block-end: 1.33em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    font-weight: bold;
    unicode-bidi: isolate;
}

/********************fin stilos H*********************/

/*dentro de caja recientemente agregados visualización como bloques, el tipo de marcador, los márgenes y el relleno, 
y el manejo del flujo de texto bidireccional dentro de ellas.*/
ul {
    display: block;
    list-style-type: disc;
    margin-block-start: 1em;
    margin-block-end: 1em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    padding-inline-start: 40px;
    unicode-bidi: isolate;
}

/*mantiene la configuracion de las imagenes */

a img {
    border: none;
}
img {
    border: 0;
}
hoja de estilo de user-agent
img {
    overflow-clip-margin: content-box;
    overflow: clip;
}

/* clases wiki-content  establecen los formatos de los elementos que estan dentro de esta clase*/

.wiki-content, .wiki-content p, .wiki-content table, .wiki-content tr, .wiki-content td, .wiki-content th, .wiki-content ol, .wiki-content ul, .wiki-content li {
    font-size: 10pt;
    line-height: 13pt;
}
.wiki-content p, .wiki-content pre, .wiki-content ul, .wiki-content ol, .wiki-content dl, table.confluenceTable {
    margin-bottom: 10px;
}

.wiki-content p, .wiki-content table {
    padding: 0;
}

h1, h2, h3, h4, h5, h6, .wiki-content h1, .wiki-content h2, .wiki-content h3, .wiki-content h4, .wiki-content h5, .wiki-content h6, .pagetitle, .steptitle, .substeptitle, .formtitle, table.confluenceTable td.confluenceTd.highlight, table.confluenceTable td.confluenceTd.highlight > p, table.confluenceTable th.confluenceTh, table.confluenceTable th.confluenceTh > p, table.admin th, .form-element-large, .form-element-small, #toolbar #format-dropdown .format-h1 a, #toolbar #format-dropdown .format-h2 a, #toolbar #format-dropdown .format-h3 a, #toolbar #format-dropdown .format-h4 a, #toolbar #format-dropdown .format-h5 a, #toolbar #format-dropdown .format-h6 a {
    color: #6f2601;
}
.wiki-content h4 {
    font-size: 12pt;
}
.wiki-content h1, .wiki-content h2, .wiki-content h3, .wiki-content h4, .wiki-content h5, .wiki-content h6 {
    font-weight: bold;
    line-height: normal;
    margin-top: 21px;
    padding: 0;
}
.wiki-content ul, .wiki-content ol {
    margin-left: 0;
    padding-left: 3em;
    list-style-position: outside;
}
.wiki-content>*:first-child, .wiki-content td.confluenceTd>*:first-child, .wiki-content th.confluenceTh>*:first-child, .wiki-content li>*:first-child {
    margin-top: 0;
}
.wiki-content>*:last-child, .wiki-content li>*:last-child {
    margin-bottom: 0;
}
.wiki-content li>ul, .wiki-content li>ol {
    margin-bottom: 0;
}
 /***************************fin clas wiki-content*****************************/


 /*clases recently-update-social - todal el area de la caja recientemente , mantiene las configuraciones
 de los titulos - subtitulos colores fuentes -margenes  y bordes*/
 .recently-updated-social h4.sub-heading {
    border-bottom: 1px solid #066293;
    color: #fff;
    font-size: 1em;
    margin-top: 0;
    margin-bottom: 0;
    padding-bottom: 2px;
    background-color: #066293;
    border-radius: 5px 5px 0 0;
    padding: 5px;
}

.recently-updated ul {
    padding: 0;
    margin: 0;
    list-style-type: none;
}


.recently-updated-social .update-item-changes {
    font-size: .9em;
    color: #666;
    margin-left: .5em;
}

.recently-updated-social .update-item-content a {
    text-decoration: none; /* Elimina el subrayado */
}

.recently-updated-social {
    margin: 0 auto; /* Esto centra horizontalmente el contenido */
    max-width: 600px; /* Define el ancho máximo del contenedor */
    background-color: #ffffff;
}

.recently-updated-social img {
    max-width: 100%; /* Ajusta el ancho máximo de las imágenes para que no excedan el contenedor */
    height: auto; /* Mantiene la proporción de aspecto de las imágenes */
    display: block; /* Asegura que las imágenes no afecten el diseño del texto */
    margin: 0 auto 10px; /* Añade un pequeño margen inferior para separar las imágenes */
}
/*************************fin clases recently update************/

/*inicio clase hidden se utilizan para ocultar elementos con la clase "hidden" en HTML, 
asegurándose de que no sean visibles y no ocupen espacio en el diseño de la página.*/
.hidden {
    display: none;
}
.hidden, li.hidden {
    display: none !important;
    visibility: hidden;
}
.hidden, form.aui .hidden, form.aui .field-group.hidden, form.aui fieldset.hidden {
    display: none;
}

/***************fin clase hidden************/

/*aumenta la saturacion de color */
.logo.space.custom, .userLogo.logo {
    filter: saturate(2.7);
}

/*no permite que ningún otro elemento flote a su izquierda*/
#content {
    clear: left;
}

/*hacen que los elementos con la clase "update-item-profile" floten hacia la izquierda dentro de su contenedor, se muestren en línea con otros elementos en línea, 
tengan un margen izquierdo de -68 píxeles y tengan un relleno de 5 píxeles en el lado izquierdo*/
div.update-item-profile {
    float: left;
    display: inline;
    margin-left: -68px;
    padding-left: 5px;
}
/*establecen que los enlaces en varios estados, así como los enlaces con la clase "blogHeading", 
tendrán un color de texto azul #326ca6.*/
a:link, a:visited, a:focus, a:hover, a:active, a.blogHeading {
    color: #326ca6;
}

/*definen la apariencia del elemento "main" dentro de un contenedor con la clase "theme-default". 
Esto incluye la altura mínima, el margen, las esquinas redondeadas y el borde.*/
.theme-default #main {
    min-height: 85%;
    margin: .75em;
    -moz-border-radius: .3125em;
    -webkit-border-radius: .3125em;
    border-radius: .3125em;
    border: 1px solid #BBB;
}


.theme-default #main {
    border-radius: 0;
    /* border: 1px solid #066293; */
    border: 0;
}

/*clases input definen la apariencia de los elementos <input> y <textarea>, incluidos estilos generales de fuente y color,
 así como estilos específicos para ciertos tipos de inputs, como los ocultos.*/
input, textarea, textarea.editor {
    font-family: Arial, Helvetica, FreeSans, sans-serif;
    font-size: 10pt;
    color: #000;
}
input[type="hidden" i] {
    appearance: none;
    background-color: initial;
    cursor: default;
    display: none !important;
    padding: initial;
    border: initial;
}
input {
    text-rendering: auto;
    color: fieldtext;
    letter-spacing: normal;
    word-spacing: normal;
    line-height: normal;
    text-transform: none;
    text-indent: 0px;
    text-shadow: none;
    display: inline-block;
    text-align: start;
    appearance: auto;
    -webkit-rtl-ordering: logical;
    cursor: text;
    background-color: field;
    margin: 0em;
    padding: 1px 0px;
    border-width: 2px;
    border-style: inset;
    border-color: light-dark(rgb(118, 118, 118), rgb(133, 133, 133));
    border-image: initial;
    padding-block: 1px;
    padding-inline: 2px;
}

/***********************fin clase input ***************/

/*clase ul -------efinen la apariencia de los elementos de lista <li> dentro de una lista desordenada <ul> 
con la clase "update-groupings", estableciendo diferentes estilos de borde, relleno*/

ul.update-groupings li.first {
    border-top: 0;
    border-bottom: 0;
}
ul.update-groupings li.grouping {
    border: 1px solid #DDD;
}
ul.update-groupings li.grouping {
    border-top: 1px solid #eee;
    padding: 10px 10px 10px 68px;
    overflow: hidden;
    _height: 1%;
}
ul.update-items h3 {
    margin: 0;
    padding: 0;
    font-weight: normal;
    font-size: 1em;
    color: #666;}
    h1, h2, h3, h4, h5, h6 {
        line-height: normal;
        font-weight: bold;
        padding: 2px 2px 2px 0;
    }
    ul.update-items li.update-item {
        padding: 5px 0;
        margin: 2px 0 0 13px;
    }
    ul.update-groupings li .icon-container {
        padding-left: 1.75em;
        display: block;
        color: #666;
        overflow: hidden;
    }

/****************** fin clase ul**************************/

/*aplican un color de texto y un cursor específicos a todos los enlaces en el documento,
independientemente de su estado o estilo predeterminado.*/
a:-webkit-any-link {
    color: -webkit-link;
    cursor: pointer;
}

/*clases update ajustan la apariencia y el diseño de varios elementos dentro de una estructura de lista desordenada, incluidos enlaces, fechas, cambios y perfiles de actualización.
 */
 .update-item-content, .update-item-date, .update-item-permlink, .update-item-changes {
        float: left;
        line-height: 16px;
    }
    ul.update-items li.update-item a {
        font-weight: normal;
    }
    span.update-item-date {
        color: #666;
        font-size: .9em;
        margin-left: .5em;
    }
 
    .update-item-changes {
        display: none;
    }
    ul.update-items {
    float: left;
}
div.update-item-profile {
    float: left;
    display: inline;
    margin-left: -68px;
    padding-left: 5px;
    background-image: url(img/negoci.png);
}

/**********************fin clase update***************/

/*clase content afectan a los enlaces y otros elementos dentro de un contenedor específico, 
estableciendo estilos de texto y fondo para los enlaces, así como imágenes de fondo para elementos específicos con ciertas clases.
*/
#content-editable-container a {
    text-decoration: none;
    color: #066293;
    transition-duration: 0.2s;
}
#content-editable-container a {
    text-decoration: none;
    color: #066293;
    transition-duration: 0.2s;
}

    
    a.content-type-page span, div.content-type-page, span.content-type-page {
        /*background-image: url('/sitio_portal/images/page_16.png');*/
        background-repeat: no-repeat;
        background-image: url('/sitio_portal/images/file.png');
     


    }
    
    ul.update-groupings li .icon-container {
        padding-left: 1.75em;
        display: block;
        color: #666;
        overflow: hidden;
    }
    
/******************fin clase content************************/

/* seccin para imagenes */
.update-item-profile {
    position: relative; /* Asegura que la imagen y el texto se posicionen correctamente */
    padding-left: 30px; /* Espacio a la izquierda del texto para la imagen */
}

.update-item-profile:before {
    content: ""; /* Agrega un pseudo-elemento antes del contenido existente */
    position: absolute; /* Posición absoluta relativa al contenedor padre */
    left: 10px; /* Coloca la imagen en el borde izquierdo del contenedor */
    top: 0; /* Alinea la imagen en la parte superior del contenedor */
    width: 45px; /* Ancho deseado para la imagen */
    height: 45px; /* Altura deseada para la imagen */
    background-image: url('200.6.115.193/sitio_portal/file.png'); /* Ruta de la imagen */
    background-size: cover; /* Ajusta el tamaño de la imagen para cubrir todo el espacio */
    background-repeat: no-repeat; /* Evita que la imagen se repita */
}

.update-item-content {
    position: relative;
}

.update-item-content::before {
    content: '';
    position: absolute;
    left: -40px; /* Ajusta la posición horizontal de la imagen */
    top: 50%; /* Centra la imagen verticalmente */
    transform: translateY(-50%);
    width: 16px; /* Ancho deseado para la imagen */
    height: 16px; /* Altura deseada para la imagen */
    background-image: url('/sitio_portal/images/file.png') ; /* Ruta de la imagen */
    background-size: cover; /* Ajusta el tamaño de la imagen */
    background-repeat: no-repeat; /* Evita que la imagen se repita */
    margin-right: 10px; /* Espacio entre la imagen y el texto */
}

</style>



<div class="recently-updated recently-updated-social" >
<h4 class="sub-heading">Recientemente Actualizado</h4>
<div class="hidden parameters">
   
</div>





    <div class="results-container">
            <!-- 
            <ul class="update-items">
                <h3><a class="confluence-userlink url fn" data-username="alicitaciones" href="/display/~alicitaciones">Área de Licitaciones</a></h3>
                                <li class="update-item">
                    <span class="icon-container content-type-page">
                        <span class="update-item-content"><a href="/pages/viewpage.action?pageId=202934003" title="Licitaciones Sector Minería">Servicio confección enlaces inalámbricos</a></span>
                        <span class="update-item-date">actualizado ayer a las 11:59 PM</span>
                        <span class="update-item-changes">(<a href="/pages/diffpagesbyversion.action?pageId=202934003&selectedPageVersions=4&selectedPageVersions=3">ver cambios</a>)</span>                                            </span>
                                    </li>

      -->                       
<?php 
 
//var_dump($sectores_recientes);

foreach ($sectores_recientes as $row) {

  $idCategoria = $row["idCategoria"];
  $imagen = "portal-06.png";
  if ($idCategoria == 1) {
      $imagen = "portal-08.png";
  }
  
   echo '
   <ul class="update-groupings">
      <li class=" grouping">
         <div class="update-item-profile">
          <a class="confluence-userlink url fn" data-username="alicitaciones" href="">
          	<img class="userLogo logo" src="http://200.6.115.193/sitio_portal/images/'.$imagen.'" alt="" title="alicitaciones">
          </a>
         </div>
         <ul class="update-items">
       <h3><a class="confluence-userlink url fn" data-username="alicitaciones" href="/display/~alicitaciones">' .$row["NomCategoria"] .'</a></h3>';

    $idCategoria = $row["idCategoria"];
     //  echo $this->muro->prueba("hola");  
	
    $proy = $this->confluence->recientemente_actualizado_proyectos($username);
    foreach ($proy as $p) {
	
        
       echo '
		      <li class="update-item">
		       <span class="icon-container content-type-page">
		       <span class="update-item-content">
		       	<a href="#"
			       	onClick="MyWindow=window.open(\'http://200.6.115.193/ficha/verFicha/'. $p["id_pro"] .'\'
					 			,\'MyWindow\'
					 			,\'width=820,height=600\'); 
					 			return false;"
						title="'.$p["Nombre_pro"].'" >'.$p["Nombre_pro"].'</a>
		       </span>
		       <span class="update-item-date">actualizado ayer a las 11:59 PM</span>
				
		       </span>
		        </li>';
        
          
        }
		
		

 ?>
      </div>
<?php
  }

/*
            foreach ($proyectos_recientes as $row)
              {
                 $date = date_create($row['fecha']);
                  echo "<li><a href='".URL_PM_APP_NEG."Fichaproyecto/ficha_proyectos/".$row['ID']."/1/0/' target='_parent'>".$row['Nombre']."&nbsp;&nbsp;actualizado el&nbsp;&nbsp;".date_format( $date,"d/m/Y" )."</a></li>"; 
              }

          */

        

?>

        </ul>
</div>