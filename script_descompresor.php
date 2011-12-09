
<?php

function descomprimir_zip( $nombre_archivo, $directorio_salida='.' ) {
    // Un día de estos implanto en el formulario lo de la selección del directorio de salida :)

    $zip = new ZipArchive();

    if( $zip->open( $nombre_archivo ) ) {
        $zip->extractTo( $directorio_salida ) 
            or die( "Fallo al extraer el archivo $nombre_archivo al directorio $directorio_salida.<br/>" );
    } else {
        echo 'No se puede abrir el archivo' . $nombre_archivo . '.<br/>';
    }

    $zip->close();

}

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">

<head>
  <title>Descompresor</title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>

<body>



<?php

// Utilizo esta variable superglobal para el "action" del formulario.
// De esta manera, el script puede ser renombrado.

$nombre_script = $_SERVER['PHP_SELF'];

if( isset( $_GET['info'] ) ) {
    // Ejecutamos phpinfo()
    phpinfo();
} elseif( isset( $_GET['enviar'] ) ) {

    if( isset( $_GET['a_descomprimir'] ) ) {

        // El usuario ha pulsado "descomprimir"

        if( isset( $_GET['timeout'] ) ) {

            // Si se ha seleccionado un valor distinto para m.e.t., lo aplicamos

            ini_set( 'max_execution_time', $_GET['timeout'] );
        }

        foreach( $_GET['a_descomprimir'] as $archivo_zip ) {

            // Vamos descomprimiendo, archivo a archivo

            descomprimir_zip( $archivo_zip );
            echo "Archivo $archivo_zip descomprimido correctamente.<br/>";
        }

        if( isset( $_GET['borrar_a'] ) ) {

            // Cuando se ha descomprimido todo, vamos borrando los originales

            foreach( $_GET['a_descomprimir'] as $archivo_borrar ) {
                if( file_exists( $archivo_borrar ) ) {
                    unlink( $archivo_borrar );
                }
            }
        }

        if( isset( $_GET['borrar_s'] ) ) {

            // Borramos el script al acabar

            $nombre_base_script = basename( $nombre_script );
            if( file_exists( $nombre_base_script ) ) {
                unlink( $nombre_base_script );
            } else {
                echo "No se pudo borrar el script.";
            }
        } 

    } else {
        echo 'No se ha seleccionado ningún archivo para descomprimir.';
    } 


} else {

    echo '<h1>Script para descomprimir archivos zip</h1>';

    echo '<form action="' . $nombre_script . '" method="GET">';

    echo '<fieldset>';
    echo '<p>Selecciona los archivos a descomprimir:<br/>(Puedes seleccionar varios a la vez)</p>';
    echo '<select name="a_descomprimir[]" size="5" multiple="multiple">';

    // Preparamos un "handle" con los archivos del directorio actual.

    $handle = opendir( '.' );

    // Vamos cogiendo archivos, uno a uno.

    while( $archivo = readdir( $handle ) ) {

        // Convierto el nombre del archivo a minúsculas, ya que voy a buscar la cadena ".zip".
        //   $archivo_min = strtolower( $archivo );

        // En lugar de "strstr()", voy a usar "stristr()", que no distingue mayúsculas de minúsculas.

        if ( stristr( $archivo, ".zip" ) ) {
            echo '<option value="' . $archivo . '"\>' . $archivo . '</option>';
        }
    }

    echo "</select><br/>";
    echo '</fieldset><br/>';
    closedir( $handle );

    $met = ini_get( 'max_execution_time' );
    echo '<fieldset>';
    echo '<p>Este servidor no permite la ejecución de un script durante más de ' . $met . ' segundos.<br/>
        Este límite se debe aumentar si se encuentra el error "Fatal error: Maximum execution time of x seconds exceeded"
        <br/> al descomprimir archivos muy grandes, o en servidores muy lentos.</p>';
    echo '<label>Valor para max_execution_time, en segundos: <input type="text" name="timeout" value="' . $met . '"/></label><br/>';
    echo '</fieldset>';
?>

        <input type="checkbox" name="borrar_a">Borrar archivo comprimido al acabar</input><br/>
        <input type="checkbox" name="borrar_s">Borrar el script al acabar</input><br/>
        <input type="submit" name="enviar" value="descomprimir"/>
        <input type="submit" name="info" value="Ejecutar phpinfo()"/>
    </form>

<?php
}
?>

</body>
</html>
