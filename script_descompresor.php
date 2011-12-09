
<?php

function descomprimir_zip( $nombre_archivo, $directorio_salida='.' ) {



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

$nombre_script = $_SERVER['PHP_SELF'];

if( isset( $_GET['enviar'] ) ) {

//    echo '<h3>Descomprimiendo. Espera un momento...</h3>';
    if( isset( $_GET['a_descomprimir'] ) ) {
        foreach( $_GET['a_descomprimir'] as $archivo_zip ) {
            descomprimir_zip( $archivo_zip );
            echo "Archivo $archivo_zip descomprimido correctamente.<br/>";
        }

        if( isset( $_GET['borrar_a'] ) ) {
            foreach( $_GET['a_descomprimir'] as $archivo_borrar ) {
                if( file_exists( $archivo_borrar ) ) {
                    unlink( $archivo_borrar );
                }
            }
        }

        if( isset( $_GET['borrar_s'] ) ) {
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


    echo '<form action="' . $nombre_script . '" method="GET">';
    echo '<h3>Selecciona los archivos a descomprimir:</h3><p>(Puedes seleccionar varios a la vez)</p>';
    echo '<select name="a_descomprimir[]" size="5" multiple="multiple">';

    $handle = opendir( '.' );

    while( $archivo = readdir( $handle ) ) {

        $archivo_min = strtolower( $archivo );

        if ( strstr( $archivo_min, ".zip" ) ) {
            echo '<option value="' . $archivo . '"\>' . $archivo . '</option>';
        }
    }

    closedir( $handle );

?>
        </select><br/>
        <input type="checkbox" name="borrar_a">Borrar archivo comprimido al acabar</input><br/>
        <input type="checkbox" name="borrar_s">Borrar el script al acabar</input><br/>
        <input type="submit" name="enviar" value="descomprimir"/>
    </form>

<?php
}
?>



</body>

</html>
