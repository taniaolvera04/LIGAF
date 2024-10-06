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
                    
                    $rows[] = $valido; // Agrega el array $valido al array $rows
                }
            
                echo json_encode($rows); // Devuelve el array en formato JSON
                break;
            

            
                
                case "saveperfil":
                    header('Content-Type: application/json; charset=utf-8');
                    $valido = ['success' => false, 'mensaje' => '', 'foto' => ''];
                    $a = $_POST['nombre'];
                    $c = $_POST['email'];
                    $fileName = $_FILES['foto']['name'];
                    $fileTmpName = $_FILES['foto']['tmp_name'];
                    $uploadDirectory = '../img_profile/';
                
                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0755, true);
                    }
                
                    $filePath = $uploadDirectory . basename($fileName);
                
                    if (move_uploaded_file($fileTmpName, $filePath)) {
                        $check = "UPDATE usuario SET nombre='$a', foto='$filePath' WHERE email='$c'";
                        if ($cx->query($check) === TRUE) {
                            $valido['success'] = true;
                            $valido['mensaje'] = "SE GUARDÓ CORRECTAMENTE";
                            $valido['foto'] = $filePath;
                        } else {
                            $valido['success'] = false;
                            $valido['mensaje'] = "ALGO SALIÓ MAL EN LA ACTUALIZACIÓN";
                        }
                    } else {
                        $valido['success'] = false;
                        $valido['mensaje'] = "ALGO SALIÓ MAL AL SUBIR LA IMAGEN";
                    }
                
                    echo json_encode($valido);
                    break;
                
                        
    }
    
}else{
    $valido['success']=false;
    $valido['mensaje']="ERROR NO SE RECIBIO NADA";
}
?>