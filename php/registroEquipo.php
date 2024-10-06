<?php
require_once "config.php";
header('Content-Type: text/html; charset=utf-8');

if($_POST){
    $action=$_REQUEST['action'];
    switch($action){

        case "registrarE":
            $valido['success']=array('success'=>false,'mensaje'=>"");            
            $a=$_POST['nombree'];
            $b=$_POST['cantidad'];

            $tipo=$_FILES['logotipo']['type'];

            $extension= pathinfo($_FILES['logotipo']['name'], PATHINFO_EXTENSION);

            $fileName="img_".time().".". $extension;
            $fileTmpName=$_FILES['logotipo']['tmp_name'];
            $uploadDirectory='../img_equipo/';
            if(!is_dir($uploadDirectory)){
                mkdir($uploadDirectory, 0755, true);
            }

            $filePath=$uploadDirectory . basename($fileName);
           

            if(move_uploaded_file($fileTmpName,$filePath)){

            $check="SELECT * FROM equipo WHERE nombree='$a'";
            $res=$cx->query($check);
            if($res->num_rows==0){
                $sql="INSERT INTO equipo VALUES(null,'$a','$b', '$filePath')";
                if($cx->query($sql)){
                    $valido['success']=true;
                    $valido['mensaje']="EL EQUIPO SE REGISTRÓ CORRECTAMENTE";
                }else {
                    $valido['success']=false;
                    $valido['mensaje']="ERROR AL REGISTRAR EQUIPO";
                }
            }else{
                $valido['success']=false;
                $valido['mensaje']="NOMBRE NO DISPONIBLE";
            }
        }
           
            echo json_encode($valido);
            break;


            case "cargarE":
                $result = $cx->query("SELECT * FROM equipo ORDER BY idequipo DESC");
                $rows = array();
    
                while ($row = $result->fetch_assoc()) {
                    $valido = array();
                    $valido['logotipo'] = $row['logotipo'];
                    $valido['nombree'] = $row['nombree'];
                    $valido['cantidad'] = $row['cantidad'];
                    $valido['idequipo'] = $row['idequipo'];
                    $rows[] = $valido;
                }
    
                echo json_encode($rows);
                break;
    
            case "selectE":
                $valido['success'] = array('success' => false, 'mensaje' => "", 'idequipo' => "", 'nombree' => "", 'cantidad' => "", 'logotipo' => "");
                $idequipo = (int)$_POST['idequipo'];
    
                $sql = "SELECT idequipo, nombree, cantidad, logotipo FROM equipo WHERE idequipo=$idequipo";
                $res = $cx->query($sql);
    
                if ($row = $res->fetch_array()) {
                    $valido['success'] = true;
                    $valido['mensaje'] = "SE ENCONTRÓ EQUIPO";
                    $valido['idequipo'] = $row[0];
                    $valido['nombree'] = $row[1];
                    $valido['cantidad'] = $row[2];
                    $valido['logotipo'] = $row[3];
                }
    
                echo json_encode($valido);
                break;

                
                case "updateE":
                    
                    $idequipo = $_POST['idequipo'] ?? '';
                    $a = $_POST['nombree'] ?? '';
                    $b = $_POST['cantidad'] ?? 0;
                
                    // Manejo de la imagen
                    $fileName = $_FILES['logotipo']['name'];
                    $fileTmpName = $_FILES['logotipo']['tmp_name'];
                    $uploadDirectory = '../assets/img_equipo/';
                
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
                            $valido['mensaje'] = "Error al subir la imagen del equipo";
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
                        $valido['mensaje'] = "EQUIPO ACTUALIZADO CORRECTAMENTE";
                    } else {
                        $valido['mensaje'] = "Error al actualizar el equipo en la base de datos: " . $cx->error;
                    }
                
                    echo json_encode($valido);
                    break;
                
                        
    }
    
}else{
    $valido['success']=false;
    $valido['mensaje']="ERROR NO SE RECIBIO NADA";
}
?>