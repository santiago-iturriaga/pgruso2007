<?php
include_once("Conexion.php");
include_once("Constantes.php");
include_once("Sesion.php");
include_once("Usuarios.php");
include_once("Alertas.php");
$s = new Sesion();
// $df contiene el número total de bytes disponible en "/"
//$df = disk_total_space("/home/paulo/Proyecto/trunk");
//echo $df;

$dquota = null;
$tquota = null;

//echo "<pre>";print_r($s->sesion);echo '</pre>';exit;

$conex = new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
$users = new Usuarios($conex);
$alertas = new Alertas($conex);


	$consulta= "select id, cliente, quota from trabajo";
	if($conex->EjecutarConsulta($consulta,null,true)){
			$trabajos=array();
			while(($row=$conex->Next()) != null){
				$trabajos[$row["id"]]=array("id"=>$row["id"],"trabajo"=>$row["id"],"cliente"=>$row["cliente"], "quota"=>$row["quota"]);
				}
				//echo "<pre>";print_r($trabajos);echo '</pre>';
			//obtnego grupos de usuarios y asigno mail
			$resUsersAlertas = $users->getUsuariosConAlertas();
			echo "<pre>";print_r($resUsersAlertas);echo '</pre>';
			if ($resUsersAlertas["error"] == 0){
				$usersAlertas = $resUsersAlertas["usuarios"];
				foreach ($trabajos as $id=>$trabajo){
					$tquota = $trabajo["quota"];
					$dir = RAIZ."/".$trabajo["cliente"]."/".$trabajo["id"];
					$dquota = dir_size($dir);
					//$dquota = $dquota/(1024*1024);
					if($dquota > $tquota){
						//echo "ERROR: el trabajo ".$trabajo["id"]." se paso de cuota";
						//echo ">>>>";
						//echo $dquota.' MBytes';
						//echo "<br>";
						//Asigno una la alerta al usuario con alertas
						foreach   ($usersAlertas as $ucaid=>$usuarioCA){
							$params = array("TRABAJO"=>$trabajo["id"],"CLIENTE"=>$trabajo["cliente"]);
							$alertas->asignarAlerta($ucaid,$trabajo["id"],1,$params);
						}

						//Asigno alerta al usuario
						$resUsuarios = $users->getUsuariosTrabajo($trabajo["id"]);
						//echo "<pre>";print_r($resUsuarios);echo '</pre>';
						if ($resUsuarios["error"] == 0){
							$usuariosT = 	$resUsuarios["usuarios"];
							foreach   ($usuariosT as $idu=>$usuarioT){
								$params2 = array("TRABAJO"=>$trabajo["id"]);
								$alertas->asignarAlerta($usuarioT["usuario"],$trabajo["id"],2,$params2);
							}
						}else{
							//echo "error al obtener usuarios del trabajo";
						}

					}else{
						//echo "El trabajo ".$trabajo["id"]." esta bien de cuota";
						//echo ">>>>";
						//echo $dquota.' MBytes';
						//echo "<br>";
					}
				}
			}else{
				//echo "error al obtener usuarios con alertas";
			}
		}
		else{

			echo "error al obtener trabajos";
		}

function dir_size($dir)
{
    $handle = opendir($dir);

    while ($file = readdir($handle)) {
        if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) {
            $mas += filesize($dir.'/'.$file);
            } else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') {
            $mas += dir_size($dir.'/'.$file);
        }
    }
    return $mas;
}

?>