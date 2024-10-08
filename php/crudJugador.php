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
            $valido['success']=array('success'=>false,'mensaje'=>"");            
            $a=$_POST['nombre'];
            $b=$_POST['edad'];
            $c=$_POST['genero'];
            $d=$_POST['numero'];
            $e=$_POST['posicion'];
            $f=$_POST['pais'];
            $g=$_POST['equipo'];



            $tipo=$_FILES['foto']['type'];

            $extension= pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

            $fileName="img_".time().".". $extension;
            $fileTmpName=$_FILES['foto']['tmp_name'];
            $uploadDirectory='../img_jugador/';
            if(!is_dir($uploadDirectory)){
                mkdir($uploadDirectory, 0755, true);
            }

            $filePath=$uploadDirectory . basename($fileName);
           

            if(move_uploaded_file($fileTmpName,$filePath)){

            $check="SELECT * FROM jugador WHERE numero=$d";
            $res=$cx->query($check);
            if($res->num_rows==0){
                $sql="INSERT INTO jugador VALUES(null,'$a',$b,'$c',$d,'$e','$f','$g', '$filePath')";
                if($cx->query($sql)){
                    $valido['success']=true;
                    $valido['mensaje']="EL JUGADOR SE REGISTRÓ CORRECTAMENTE";
                }else {
                    $valido['success']=false;
                    $valido['mensaje']="ERROR AL REGISTRAR JUGADOR EN BD";
                }
            }else{
                $valido['success']=false;
                $valido['mensaje']="NÚMERO DE JUGADOR NO DISPONIBLE";
            }
        }
           
            echo json_encode($valido);
            break;


            case "cargarJ":
                $result = $cx->query("SELECT * FROM jugador ORDER BY idjugador DESC");
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
                    $rows[] = $valido;
                }
    
                echo json_encode($rows);
                break;
    
            case "selectJ":
                $valido['success'] = array('success' => false, 'mensaje' => "", 'idequipo' => "", 'nombree' => "", 'cantidad' => "", 'logotipo' => "");
                $idequipo = (int)$_POST['idequipo'];
    
                $sql = "SELECT idequipo, nombree, cantidad, logotipo FROM equipo WHERE idequipo=$idequipo";
                $res = $cx->query($sql);
    
                if ($row = $res->fetch_array()) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "SE ENCONTRÓ EL JUGADOR";
                    $valido['idequipo'] = $row[0];
                    $valido['nombree'] = $row[1];
                    $valido['cantidad'] = $row[2];
                    $valido['logotipo'] = $row[3];
                }
    
                echo json_encode($valido);
                break;

                
                case "updateJ":
                    
                    $idequipo = $_POST['idequipo'] ?? '';
                    $a = $_POST['nombree'] ?? '';
                    $b = $_POST['cantidad'] ?? 0;
                
                    // Manejo de la imagen
                    $fileName = $_FILES['logotipo']['name'];
                    $fileTmpName = $_FILES['logotipo']['tmp_name'];
                    $uploadDirectory = '../img_equipo/';
                
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
                            $valido['mensaje'] = "ERROR AL SUBIR IMÁGEN DE JUGADOR";
                            echo json_encode($valido);
                            exit;
                        }
                    } else {
                        // Si no se proporciona una nueva imagen, buscar la actual
                        $currentImageQuery = "SELECT logotipo FROM equipo WHERE idequipo = $idequipo";
                        $currentImageResult = $cx->query($currentImageQuery);
                        $currentImage = ($currentImageResult && $row = $currentImageResult->fetch_array()) ? $row['logotipo'] : '';
                        $filePath = $currentImage; // Mantener la imagen actual
                    }
                
                    // Actualizar los datos del álbum en la base de datos
                    $sqlUpdateAlbum = "UPDATE equipo SET 
                                        nombree = '$a',
                                        cantidad = $b,
                                        logotipo = '$filePath'
                                        WHERE idequipo = $idequipo";
                
                    if ($cx->query($sqlUpdateAlbum)) {
                        $valido['success'] = true;
                        $valido['mensaje'] = "JUGADOR ACTUALIZADO CORRECTAMENTE";
                    } else {
                        $valido['mensaje'] = "ERROR AL ACTUALIZAR JUGADOR EN BD " . $cx->error;
                    }
                
                    echo json_encode($valido);
                    break;
                
                    case "deleteJ":
                        $idequipo=$_POST['idequipo'];

                        $sql="DELETE FROM equipo WHERE idequipo=$idequipo";
                        if($cx->query($sql)){
                           $valido['success']=true;
                           $valido['mensaje']="EL JUGADOR SE ELIMINÓ CORRECTAMENTE";
                        }else{
                            $valido['success']=false;
                           $valido['mensaje']="ERROR AL ELIMINAR JUGADOR EN BD"; 
                        }
                    
                 
                    echo json_encode($valido);
                    
                        break;
                        
    }
    
}else{
    $valido['success']=false;
    $valido['mensaje']="ERROR NO SE RECIBIO NADA";
}
?>