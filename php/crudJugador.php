<?php
require_once "config.php";
header('Content-Type: text/html; charset=utf-8');

if($_POST){
    $action=$_REQUEST['action'];
    switch($action){

        case "selectEquipos":
            $sql = "SELECT idequipo, nombree FROM equipo"; // Solo recupera idequipo y nombree
            $registros = array('data' => array());
            $res = $cx->query($sql);
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_array()) {
                    $registros['data'][] = array($row['idequipo'], $row['nombree']); // Ajustado para usar nombres de columnas
                }
            }
            echo json_encode($registros);
            break;
        
            case "registrarJ":
                $valido['success'] = array('success' => false, 'mensaje' => "");            
                $a = $_POST['nombre'];
                $b = $_POST['edad'];
                $c = $_POST['genero'];
                $d = $_POST['numero'];
                $e = $_POST['posicion'];
                $f = $_POST['pais'];
                $g = $_POST['equipo']; // Asegúrate de que esto sea un ID válido de equipo
            
                // Verifica si el equipo existe
                $checkEquipo = "SELECT * FROM equipo WHERE idequipo = $g";
                $resEquipo = $cx->query($checkEquipo);
                if ($resEquipo->num_rows > 0) {
                    $tipo = $_FILES['foto']['type'];
                    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $fileName = "img_" . time() . "." . $extension;
                    $fileTmpName = $_FILES['foto']['tmp_name'];
                    $uploadDirectory = '../img_jugador/';
                    
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0755, true);
                    }
            
                    $filePath = $uploadDirectory . basename($fileName);
                    if (move_uploaded_file($fileTmpName, $filePath)) {
                        $check = "SELECT * FROM jugador WHERE numero = $d AND idequipo='$g'";
                        $res = $cx->query($check);
                        if ($res->num_rows == 0) {
                            $sql = "INSERT INTO jugador (nombre, edad, genero, numero, posicion, pais, idequipo, foto) VALUES ('$a', $b, '$c', $d, '$e', '$f', $g, '$filePath')";
                            if ($cx->query($sql)) {
                                $valido['success'] = true;
                                $valido['mensaje'] = "EL JUGADOR SE REGISTRÓ CORRECTAMENTE";
                            } else {
                                $valido['success'] = false;
                                $valido['mensaje'] = "ERROR AL REGISTRAR JUGADOR EN BD: " . $cx->error; // Incluye el error
                            }
                        } else {
                            $valido['success'] = false;
                            $valido['mensaje'] = "NÚMERO DE JUGADOR NO DISPONIBLE";
                        }
                    } else {
                        $valido['success'] = false;
                        $valido['mensaje'] = "ERROR AL SUBIR LA FOTO";
                    }
                } else {
                    $valido['success'] = false;
                    $valido['mensaje'] = "EL ID DE EQUIPO NO EXISTE"; // Mensaje si el equipo no existe
                }
                
                echo json_encode($valido);
                break;
            


                case "cargarJ":
                    $result = $cx->query("
                        SELECT j.*, e.nombree AS equipo
                        FROM jugador j
                        JOIN equipo e ON j.idequipo = e.idequipo
                        ORDER BY j.idjugador DESC
                    ");
                    $rows = array();
                
                    while ($row = $result->fetch_assoc()) {
                        $valido = array();
                        $valido['foto'] = $row['foto'];
                        $valido['nombre'] = $row['nombre'];
                        $valido['edad'] = $row['edad'];
                        $valido['genero'] = $row['genero'];
                        $valido['numero'] = $row['numero'];
                        $valido['posicion'] = $row['posicion'];
                        $valido['pais'] = $row['pais'];
                        $valido['idjugador'] = $row['idjugador'];
                        $valido['equipo'] = $row['equipo']; // Agrega el nombre del equipo
                        $rows[] = $valido;
                    }
                
                    echo json_encode($rows);
                    break;
                
                    case "selectJ":
                        $valido = [
                            'success' => false,
                            'mensaje' => "",
                            'idjugador' => "",
                            'nombre' => "",
                            'edad' => "",
                            'genero' => "",
                            'numero' => "",
                            'posicion' => "",
                            'pais' => "",
                            'foto' => "",
                            'idequipo' => ""
                        ];
                    
                        $idjugador = (int)$_POST['idjugador']; // Asegúrate de que el ID es un entero
                    
                        // Usa una consulta preparada para evitar inyecciones SQL
                        $sql = "SELECT idjugador, nombre, edad, genero, numero, posicion, pais, foto, idequipo FROM jugador WHERE idjugador = ?";
                        $stmt = $cx->prepare($sql);
                        $stmt->bind_param('i', $idjugador);
                        $stmt->execute();
                        $res = $stmt->get_result();
                    
                        if ($row = $res->fetch_assoc()) {
                            $valido['success'] = true;
                            $valido['mensaje'] = "SE ENCONTRÓ JUGADOR";
                            $valido['idjugador'] = $row['idjugador'];
                            $valido['nombre'] = $row['nombre'];
                            $valido['edad'] = $row['edad'];
                            $valido['genero'] = $row['genero'];
                            $valido['numero'] = $row['numero'];
                            $valido['posicion'] = $row['posicion'];
                            $valido['pais'] = $row['pais'];
                            $valido['foto'] = $row['foto'];
                            $valido['idequipo'] = $row['idequipo'];
                        } else {
                            $valido['mensaje'] = "Jugador no encontrado";
                        }
                    
                        echo json_encode($valido);
                        break;
                    
    
                        case "updateJ":
                            $idjugador = $_POST['idjugador'] ?? '';
                            $nombre = $_POST['nombre'] ?? '';
                            $edad = $_POST['edad'] ?? 0;
                            $genero = $_POST['genero'] ?? '';
                            $numero = $_POST['numero'] ?? 0;
                            $posicion = $_POST['posicion'] ?? '';
                            $pais = $_POST['pais'] ?? '';
                            $equipo = $_POST['equipo'] ?? '';
                            
                            // Manejo de la imagen
                            $fileName = $_FILES['foto']['name'];
                            $fileTmpName = $_FILES['foto']['tmp_name'];
                            $uploadDirectory = '../img_jugador/';
                            
                            // Verificar y crear directorio si no existe
                            if (!is_dir($uploadDirectory)) {
                                mkdir($uploadDirectory, 0755, true);
                            }
                        
                            // Inicializar la ruta del archivo
                            $filePath = '';
                        
                            // Mover la imagen subida al directorio deseado si se proporciona una nueva imagen
                            if (!empty($fileName)) {
                                $filePath = $uploadDirectory . basename($fileName);
                        
                                if (!move_uploaded_file($fileTmpName, $filePath)) {
                                    $valido['mensaje'] = "ERROR AL SUBIR LA IMAGEN DEL JUGADOR";
                                    echo json_encode($valido);
                                    exit;
                                }
                            } else {
                                // Si no se proporciona una nueva imagen, buscar la actual
                                $currentImageQuery = "SELECT foto FROM jugador WHERE idjugador = $idjugador";
                                $currentImageResult = $cx->query($currentImageQuery);
                                $currentImage = ($currentImageResult && $row = $currentImageResult->fetch_array()) ? $row['foto'] : '';
                                $filePath = $currentImage; // Mantener la imagen actual
                            }
                        
                            // Actualizar los datos del jugador en la base de datos
                            $sqlUpdateJugador = "UPDATE jugador SET 
                                                    nombre = '$nombre',
                                                    edad = $edad,
                                                    genero = '$genero',
                                                    numero = $numero,
                                                    posicion = '$posicion',
                                                    pais = '$pais',
                                                    idequipo = $equipo,
                                                    foto = '$filePath'
                                                  WHERE idjugador = $idjugador";
                        
                            if ($cx->query($sqlUpdateJugador)) {
                                $valido['success'] = true;
                                $valido['mensaje'] = "JUGADOR ACTUALIZADO CORRECTAMENTE";
                            } else {
                                $valido['mensaje'] = "ERROR AL ACTUALIZAR JUGADOR EN BD: " . $cx->error;
                            }
                        
                            echo json_encode($valido);
                            break;
                        
                        case "deleteJ":
                            $idjugador = $_POST['idjugador'];
                        
                            $sql = "DELETE FROM jugador WHERE idjugador = $idjugador";
                            if ($cx->query($sql)) {
                                $valido['success'] = true;
                                $valido['mensaje'] = "EL JUGADOR SE ELIMINÓ CORRECTAMENTE";
                            } else {
                                $valido['success'] = false;
                                $valido['mensaje'] = "ERROR AL ELIMINAR JUGADOR EN BD: " . $cx->error; 
                            }
                        
                            echo json_encode($valido);
                            break;
                        
                        
    }
    
}else{
    $valido['success']=false;
    $valido['mensaje']="ERROR NO SE RECIBIO NADA";
}
?>