<?php
// ini_set('memory_limit', '512M');
class Conexion
{
    private $conexion;
    private $host;
    private $dbnombre;
    private $clave;
    private $usuario;

    public function Conexion($host, $dbnombre, $clave, $usuario)
    {
        $this->host = $host;
        $this->dbnombre = $dbnombre;
        $this->clave = $clave;
        $this->usuario = $usuario;
    }

    public function conectar()
    {
        try
        {
            $this->conexion = mysqli_connect($this->host, $this->usuario, $this->clave, $this->dbnombre);
        }
        catch(Exception $e)
        {
            echo "Error con la bd.</br>";
            throw new Exception("Error al conectarse con la BD");
        }
    }

    public function desconectar()
    {
        mysqli_close($this->conexion);
    }

////////////////////////////////////////////////////////////////////////////////////
//								FUNCIONES EXISTE						
////////////////////////////////////////////////////////////////////////////////////

    public function existe_curso ($numero)
    {
        $respuesta = -1;

        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM CURSO WHERE NUMERO=".$numero." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_seccion ($nombre)
    {
        $respuesta = -1;

        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM SECCION WHERE NOMBRE='".$nombre."' ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_sesion ($numero, $fecha)
    {
        $respuesta = -1;

        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM SESION WHERE NUMERO=".$numero." AND FECHA='".$fecha."' ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_etapa ($numero, $id_curso)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM ETAPA WHERE NUMERO=".$numero." AND FK_CURSO_ID=".$id_curso." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_reto ($nombre, $id_etapa)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM RETO WHERE NOMBRE='".$nombre."' AND FK_ETAPA_ID=".$id_etapa." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_alumno ($nombre, $id_seccion)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM ALUMNO WHERE NOMBRE='".$nombre."' AND FK_SECCION_ID=".$id_seccion." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_avance_code ($id_sesion, $id_alumno)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM AVANCE_CODE WHERE FK_SESION_ID=".$id_sesion." AND FK_ALUMNO_ID=".$id_alumno." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_video($nombre, $objetivo, $total) 
    {
        
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM VIDEO WHERE NOMBRE='".$nombre."' AND OBJETIVOS=".$objetivo." AND TOTAL=".$total." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_objetivo_video($id_video, $id_sesion) 
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM OBJETIVO_VIDEO WHERE FK_VIDEO_ID=".$id_video." AND FK_SESION_ID=".$id_sesion." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_avance_video($id_video, $id_alumno)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM AVANCE_VIDEO WHERE FK_VIDEO_ID=".$id_video." AND FK_ALUMNO_ID=".$id_alumno." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;   
    }

    public function existe_avance_pregunta($id_video, $id_alumno)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM AVANCE_PREGUNTA WHERE FK_VIDEO_ID=".$id_video." AND FK_ALUMNO_ID=".$id_alumno." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;   
    }

    public function existe_pregunta($objetivo, $total, $id_sesion)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM PREGUNTA WHERE FK_SESION_ID=".$id_sesion." AND OBJETIVOS=".$objetivo." AND TOTAL=".$total." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;   
    }

    public function existe_proyecto($nombre)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM PROYECTO WHERE NOMBRE='".$nombre."'";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta; 
    }

    public function existe_rubrica($nombre, $id_proyecto)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM RUBRICA WHERE NOMBRE='".$nombre."' AND FK_PROYECTO_ID=".$id_proyecto." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta; 
    }

    public function existe_objetivo_rubrica($valor, $id_sesion, $id_rubrica, $id_alumno)
    {
        $respuesta = -1;
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM OBJETIVO_RUBRICA WHERE VALOR=".$valor." AND FK_SESION_ID=".$id_sesion." AND FK_RUBRICA_ID=".$id_rubrica." AND FK_ALUMNO_ID=".$id_alumno." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta; 
    }

    public function existe_pais($nombre)
    {
        $respuesta = -1;

        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM PAIS WHERE NOMBRE='".$nombre."' ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta;
    }

    public function existe_escuela($id_pais, $nombre_escuela)
    {
        $respuesta = -1;
        $nombre_escuela = strtoupper($nombre_escuela);
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID FROM ESCUELA WHERE NOMBRE='".$nombre_escuela."' AND FK_PAIS_ID=".$id_pais." ";
            $resultado = mysqli_query($this->conexion, $query);
            if($resultado) { 
                $filas = mysqli_fetch_assoc($resultado);
                if(count($filas) > 0) { $respuesta = (int) $filas['ID']; }
                mysqli_free_result($resultado);
            }
            $this->desconectar();
        }

        return $respuesta; 
    }

////////////////////////////////////////////////////////////////////////////////////
//								FUNCIONES INSERCION						
////////////////////////////////////////////////////////////////////////////////////

    public function agregar_pais($nombre)
    {
        $respuesta = $this->existe_pais($nombre);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO PAIS(NOMBRE) VALUES('".$nombre."')";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_pais($nombre); 
        }
        return $respuesta;
    }

    public function agregar_escuela ($id_pais, $nombre_escuela) 
    {
        $respuesta = $this->existe_escuela($id_pais, $nombre_escuela);
        $nombre_escuela = strtoupper($nombre_escuela);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO ESCUELA(NOMBRE, FK_PAIS_ID) VALUES('".$nombre_escuela."', ".$id_pais.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_escuela($id_pais, $nombre_escuela);       
        }
        return $respuesta;
    }

    public function agregar_avance_video_alumno($principal_objetivos, $principal_total, $id_video, $id_alumno, $objetivo, $total)
    {
        $respuesta = $this->existe_avance_video($id_video, $id_alumno);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO AVANCE_VIDEO(PRINCIPAL_OBJETIVOS, PRINCIPAL_TOTAL, FK_VIDEO_ID, FK_ALUMNO_ID, OBJETIVOS, TOTAL) VALUES(".$principal_objetivos.",".$principal_total.",".$id_video.",".$id_alumno.", ".$objetivo.", ".$total." )";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_avance_video($id_video, $id_alumno); 
        }
        return $respuesta;
    }
    
    public function agregar_avance_video($principal_objetivos, $principal_total, $id_video, $id_seccion, &$matriz)
    {
        
        for($j=0; $j < count($matriz[2]); $j++)
        {   
            if($j > 1)
            {
                $id_alumno = $this->existe_alumno($matriz[4][$j], $id_seccion);
                if($id_alumno == -1) { $id_alumno = $this->agregar_alumno($matriz[4][$j], $id_seccion);}
                $this->agregar_avance_video_alumno($principal_objetivos, $principal_total, $id_video, $id_alumno, $matriz[2][$j], $matriz[3][$j]);
            }
        }
    }

    public function agregar_avance_pregunta_alumno($principal_objetivos, $principal_total, $id_pregunta, $id_alumno, $objetivo, $total)
    {
        $respuesta = $this->existe_avance_pregunta($id_pregunta, $id_alumno);
        
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO AVANCE_PREGUNTA(PRINCIPAL_OBJETIVOS, PRINCIPAL_TOTAL, FK_PREGUNTA_ID, FK_ALUMNO_ID, OBJETIVOS, TOTAL) VALUES(".$principal_objetivos.",".$principal_total.",".$id_pregunta.",".$id_alumno.", ".$objetivo.", ".$total." )";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_avance_pregunta($id_pregunta, $id_alumno); 
        }
        return $respuesta;
    }
    //$id_pregunta, $id_sesion, $matriz
    public function agregar_avance_pregunta($principal_objetivos, $principal_total, $id_pregunta, $id_seccion, &$matriz)
    {

        for($j=0; $j < count($matriz[2]); $j++)
        {   
            if($j > 1)
            {
                $id_alumno = $this->existe_alumno($matriz[4][$j], $id_seccion);
                if($id_alumno == -1) { $id_alumno = $this->agregar_alumno($matriz[4][$j], $id_seccion);}
                
                
                $this->agregar_avance_pregunta_alumno($principal_objetivos, $principal_total, $id_pregunta, $id_alumno, $matriz[2][$j], $matriz[3][$j]);
            }
        }
    }

    public function agregar_objetivo_video($id_video, $id_sesion)
    {
        $respuesta = $this->existe_objetivo_video($id_video, $id_sesion);

        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO OBJETIVO_VIDEO(FK_VIDEO_ID, FK_SESION_ID) VALUES(".$id_video.",".$id_sesion.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_objetivo_video($id_video, $id_sesion); 
        }
        return $respuesta;
    }

    public function agregar_curso ($numero)
    {
        $respuesta = $this->existe_curso($numero);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO CURSO(NUMERO) VALUES(".$numero.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_curso($numero); 
        }
        return $respuesta;
    }

    public function agregar_curso_escuela ($numero, $id_escuela)
    {
        $respuesta = $this->existe_curso($numero);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO CURSO(NUMERO, FK_ESCUELA_ID) VALUES(".$numero.", ".$id_escuela.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_curso($numero); 
        }
        return $respuesta;
    }

    public function agregar_seccion ($nombre, $fk_curso) 
    {
        $respuesta = $this->existe_seccion($nombre);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO SECCION(NOMBRE, FK_CURSO_ID, FK_DOCENTE_ID) VALUES('".$nombre."', ".$fk_curso.", NULL)";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_seccion($nombre);       
            
        }
        return $respuesta;
    }

    public function agregar_sesion ($numero, $fecha, $total, $objetivos, $id_curso) 
    {
        $lista = explode('-', $fecha);
        $fecha = $lista[2].'-'.$lista[1].'-'.$lista[0];
        $respuesta = $this->existe_sesion($numero, $fecha);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO SESION(NUMERO, FECHA, TOTAL, OBJETIVOS, FK_CURSO_ID) VALUES(".$numero.", '".$fecha."', ".$total.", ".$objetivos.", ".$id_curso.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_sesion($numero, $fecha);       
            
        }
        return $respuesta;
    }

    public function agregar_etapa ($numero, $id_curso) 
    {
        $respuesta = $this->existe_etapa($numero, $id_curso);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO ETAPA(NUMERO, FK_CURSO_ID) VALUES(".$numero.", ".$id_curso.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_etapa($numero, $id_curso);       
            
        }
        return $respuesta;
    }

    public function agregar_etapas ($vector, $id_curso)
    {
        foreach ($vector as $numero)
        {
            $this->agregar_etapa($numero, $id_curso);
        }
    }

    public function agregar_reto ($nombre, $id_etapa)
    {
        $respuesta = $this->existe_reto($nombre, $id_etapa);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO RETO(NOMBRE, FK_ETAPA_ID) VALUES('".$nombre."', ".$id_etapa.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_reto($nombre, $id_etapa);       
            
        }
        return $respuesta;
    }

    public function agregar_retos ($matriz, $id_curso)
    {
        for ($i=0; $i < count($matriz); $i++)
        {
            $id_etapa = $this->existe_etapa($matriz[$i][0], $id_curso);
            if($id_etapa > 0)
            {
                $this->agregar_reto($matriz[$i][1], $id_etapa);
            }
        }
    }

    public function agregar_alumno ($nombre, $id_seccion)
    {
        $_array = explode("-", $nombre);
        $dni = $_array[0];
        $nombre = $_array[1];
        $respuesta = $this->existe_alumno($nombre, $id_seccion);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO ALUMNO(DNI, NOMBRE, FK_SECCION_ID) VALUES('".$dni."' ,'".$nombre."', ".$id_seccion.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_alumno($nombre, $id_seccion);       
            
        }
        return $respuesta;
    }

    public function agregar_avance_code ($principal_objetivos, $principal_total, $objetivos, $total, $id_sesion, $id_alumno)
    {
        $respuesta = $this->existe_avance_code($id_sesion, $id_alumno);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO AVANCE_CODE(PRINCIPAL_OBJETIVOS, PRINCIPAL_TOTAL, OBJETIVOS, TOTAL, FK_SESION_ID, FK_ALUMNO_ID) VALUES(".$principal_objetivos.",".$principal_total.",".$objetivos.", ".$total.", ".$id_sesion.", ".$id_alumno.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_avance_code($id_sesion, $id_alumno);          
        }
        return $respuesta;
    }

    public function agregar_avance_alumno ($principal_objetivos, $principal_total, $matriz, $id_seccion, $id_sesion)
    {
        for ($i=0; $i < count($matriz); $i++)
        {
            $id_alumno = $this->agregar_alumno($matriz[$i][2], $id_seccion);
            if($id_alumno > 0)
            {
                $this->agregar_avance_code($principal_objetivos, $principal_total, $matriz[$i][0], $matriz[$i][1], $id_sesion, $id_alumno);
            }
        }
    }

    public function agregar_video($nombre, $objetivo, $total)
    {
        $respuesta = $this->existe_video($nombre, $objetivo, $total);

        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO VIDEO(NOMBRE, OBJETIVOS, TOTAL) VALUES('".$nombre."', ".$objetivo.", ".$total.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_video($nombre, $objetivo, $total);       
        }
        return $respuesta;
    }

    public function agregar_pregunta($objetivo, $total, $id_sesion)
    {
        $respuesta = $this->existe_pregunta($objetivo, $total, $id_sesion);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO PREGUNTA(OBJETIVOS, TOTAL, FK_SESION_ID) VALUES(".$objetivo.", ".$total.", ".$id_sesion." )";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_pregunta($objetivo, $total, $id_sesion);       
        }
        return $respuesta;
    }

    public function agregar_proyecto($nombre)
    {
        $respuesta = $this->existe_proyecto($nombre);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO PROYECTO(NOMBRE) VALUES('".$nombre."')";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_proyecto($nombre);     
        }
        return $respuesta;
    }

    public function agregar_rubrica($nombre, $id_proyecto)
    {
        $respuesta = $this->existe_rubrica($nombre, $id_proyecto);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO RUBRICA(NOMBRE, FK_PROYECTO_ID) VALUES('".$nombre."', ".$id_proyecto.")";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_rubrica($nombre, $id_proyecto);
        }
        return $respuesta;
    }

    public function agregar_objetivo_rubrica($valor, $id_sesion, $id_rubrica, $id_alumno, $sesion_target)
    {
        $respuesta = $this->existe_objetivo_rubrica($valor, $id_sesion, $id_rubrica, $id_alumno);
        $this->conectar();
        if($this->conexion && $respuesta < 0)
        {
            $query = "INSERT INTO OBJETIVO_RUBRICA(VALOR, SESION_TARGET, FK_SESION_ID, FK_RUBRICA_ID, FK_ALUMNO_ID) VALUES(".$valor.",".$sesion_target." ,".$id_sesion.", ".$id_rubrica.", ".$id_alumno." )";
            mysqli_query($this->conexion, $query);
            $this->desconectar();
            $respuesta = $this->existe_objetivo_rubrica($valor, $id_sesion, $id_rubrica, $id_alumno);
        }
        return $respuesta;
    }

    public function agregar_rubricas_alumnos($id_proyecto, &$matriz, $cantidad, &$matriz_info, $sesion_target)
    {
        $inicio = count($matriz[2])-1;
        $lista = explode('-', $matriz_info[0][1]);
        $fecha = $lista[2].'-'.$lista[1].'-'.$lista[0];
        $id_sesion = $this->existe_sesion($matriz_info[0][3], $fecha);
        $id_curso = $this->existe_curso($matriz_info[1][1]);
        if($id_curso == -1) {
            $nombre_pais = $matriz_info[0][8];
            $id_pais = $this->cone->existe_pais($nombre_pais);
            $nombre_escuela = $matriz_info[0][10];
            $id_escuela = $this->cone->existe_escuela($id_pais, $nombre_escuela);
            $id_curso = $this->agregar_curso_escuela($matriz_info[1][1], $id_escuela);
        }

        for($i=$inicio; $i > $inicio-$cantidad ; $i--)
        {
            $id_rubrica = $this->agregar_rubrica($matriz[2][$i], $id_proyecto);
            // print("n: ".$matriz[2][$i]." id: ".$id_rubrica."</br>");
            for($j=3; $j < count($matriz); $j++)
            {
                $id_seccion = $this->existe_seccion($matriz[$j][0]);
                if($id_seccion == -1) {$id_seccion = $this->agregar_seccion($matriz[$j][0], $id_curso);}
                $nombre_alumno = $matriz[$j][3]." ".$matriz[$j][4];
                $id_alumno = $this->existe_alumno($nombre_alumno, $id_seccion);
                if($id_alumno == -1){$id_alumno = $this->agregar_alumno($nombre_alumno, $id_seccion);}
                
                $this->agregar_objetivo_rubrica($matriz[$j][$i], $id_sesion, $id_rubrica, $id_alumno, $sesion_target);   
            }
        }
    }

////////////////////////////////////////////////////////////////////////////////////
//								FUNCIONES CONSULTA						
////////////////////////////////////////////////////////////////////////////////////

    public function ver_paises()
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID, NOMBRE FROM PAIS";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_escuelas($id_pais)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT es.ID, es.NOMBRE FROM ESCUELA as es, PAIS as p WHERE p.ID='".$id_pais."' AND p.ID = es.FK_PAIS_ID";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_code ($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, se.OBJETIVOS as ACUM_TARGET, (se.OBJETIVOS-se1.OBJETIVOS) as SESION_TARGET, IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(se.OBJETIVOS-se1.OBJETIVOS))*100) as OBLI_CUMP_SESION, IF(se.OBJETIVOS=0, 0, (av.OBJETIVOS/se.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, (av.OBJETIVOS-av1.OBJETIVOS)/(se.OBJETIVOS-se1.OBJETIVOS)) as GENERAL_CUMP_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 80, 3, IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM, IF(IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(se.OBJETIVOS-se1.OBJETIVOS))*100)< 80, 3, IF(IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(se.OBJETIVOS-se1.OBJETIVOS))*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) >= 80 AND IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av, SESION as se1, AVANCE_CODE as av1 , ALUMNO as al1 WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se.ID=".$id_sesion." AND se1.ID=(SELECT ID FROM SESION as se2 WHERE se2.FECHA < se.FECHA AND se1.FK_CURSO_ID=cu.ID LIMIT 1) AND se1.ID=av1.FK_SESION_ID AND av1.FK_ALUMNO_ID=al1.ID AND al1.NOMBRE=al.NOMBRE";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function boleta_code($id_sesion, $id_sesion1, $id_curso, $id_seccion, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, se.OBJETIVOS as ACUM_TARGET, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, (se.OBJETIVOS-se1.OBJETIVOS) as SESION_TARGET FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av, SESION as se1, AVANCE_CODE as av1 WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se1.ID=av1.FK_SESION_ID AND av1.FK_ALUMNO_ID=al.ID AND se.ID=".$id_sesion." AND se1.ID=".$id_sesion1." AND al.ID=".$id_alumno." AND sec.ID=".$id_seccion."  AND cu.ID=".$id_curso." ";
            $query = "SELECT av.OBJETIVOS as OBLIGATORIO_ACUM, av.TOTAL as GENERAL_ACUMULADO, se.OBJETIVOS as ACUM_TARGET FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se.ID=".$id_sesion." AND al.ID=".$id_alumno." AND sec.ID=".$id_seccion."  AND cu.ID=".$id_curso." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function boleta_code_1($id_sesion, $id_curso, $id_seccion, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO,se.OBJETIVOS as ACUM_TARGET, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, se.OBJETIVOS as ACUM_TARGET, se.OBJETIVOS as SESION_TARGET FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se.ID=".$id_sesion." AND al.ID=".$id_alumno." AND cu.ID=".$id_curso." AND sec.ID=".$id_seccion." ";
            $query = "SELECT av.OBJETIVOS as OBLIGATORIO_ACUM, av.PRINCIPAL_OBJETIVOS as GENERAL_ACUMULADO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se.ID=".$id_sesion." AND al.ID=".$id_alumno." AND sec.ID=".$id_seccion."  AND cu.ID=".$id_curso." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function boleta_video($id_sesion, $id_sesion1, $id_curso, $id_seccion, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, se.OBJETIVOS as ACUM_TARGET, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, (vi.OBJETIVOS-vi1.OBJETIVOS) as SESION_TARGET FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_VIDEO as av, SESION as se1, AVANCE_VIDEO as av1 , ALUMNO as al1, VIDEO as vi, VIDEO as vi1, OBJETIVO_VIDEO as obj, OBJETIVO_VIDEO as obj1 WHERE obj.FK_SESION_ID=se.ID AND obj.FK_VIDEO_ID=vi.ID AND av.FK_VIDEO_ID=vi.ID AND se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND al.FK_SECCION_ID=sec.ID AND obj1.FK_SESION_ID=se1.ID AND obj1.FK_VIDEO_ID=vi1.ID AND se1.FK_CURSO_ID=cu.ID AND av1.FK_ALUMNO_ID=al.ID AND se.ID=".$id_sesion." AND se1.ID=".$id_sesion1." AND cu.ID=1 AND sec.ID=".$id_seccion." AND al.ID=".$id_alumno."  ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function boleta_video_1($id_sesion, $id_curso, $id_seccion, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            //$query = "SELECT av.OBJETIVOS as OBLIGATORIO_ACUM, av.PRINCIPAL_OBJETIVOS as GENERAL_ACUMULADO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND obj.FK_VIDEO_ID=vi.ID AND se.ID=".$id_sesion." AND cu.ID=".$id_curso." AND sec.ID=".$id_seccion." AND al.ID=".$id_alumno." ";
            $query = "SELECT av.OBJETIVOS as OBLIGATORIO_ACUM, av.PRINCIPAL_OBJETIVOS as GENERAL_ACUMULADO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND obj.FK_VIDEO_ID=vi.ID AND se.ID=".$id_sesion." AND cu.ID=".$id_curso." AND sec.ID=".$id_seccion." AND al.ID=".$id_alumno." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function boleta_pregunta($id_sesion, $id_sesion1, $id_curso, $id_seccion, $id_alumno)
    {
        // echo "ses: ".$id_sesion."</br>";
        // echo "ses1: ".$id_sesion1."</br>";
        // exit;
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, pre.OBJETIVOS as ACUM_TARGET, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, (pre.OBJETIVOS-pre1.OBJETIVOS) as SESION_TARGET FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, SESION as se1, AVANCE_PREGUNTA as av1 , ALUMNO as al1, PREGUNTA as pre, PREGUNTA as pre1 WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av1.FK_ALUMNO_ID=al1.ID AND al1.DNI=al.DNI AND av1.FK_PREGUNTA_ID=pre1.ID AND av.FK_PREGUNTA_ID=pre.ID AND se.ID=".$id_sesion." AND se1.ID=".$id_sesion1." AND cu.ID=".$id_curso." AND sec.ID=".$id_seccion." AND al.ID=".$id_alumno." ";
            $query = "SELECT av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, pre.OBJETIVOS as ACUM_TARGET, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, (pre.OBJETIVOS-pre1.OBJETIVOS) as SESION_TARGET FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, SESION as se1, AVANCE_PREGUNTA as av1 , ALUMNO as al1, PREGUNTA as pre, PREGUNTA as pre1 WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av1.FK_ALUMNO_ID=al1.ID AND al1.DNI=al.DNI AND av1.FK_PREGUNTA_ID=pre1.ID AND av.FK_PREGUNTA_ID=pre.ID AND se.ID=".$id_sesion." AND se1.ID=".$id_sesion1." AND cu.ID=".$id_curso." AND sec.ID=".$id_seccion." AND al.ID=".$id_alumno." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function boleta_pregunta_1($id_sesion, $id_curso, $id_seccion, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, pre.OBJETIVOS as ACUM_TARGET, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, pre.OBJETIVOS as SESION_TARGET FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, PREGUNTA as pre WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND pre.FK_SESION_ID=se.ID AND av.FK_PREGUNTA_ID=pre.ID AND av.FK_ALUMNO_ID=al.ID AND se.ID=".$id_sesion." AND sec.ID=".$id_seccion." AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ";
            $query = "SELECT av.OBJETIVOS as OBLIGATORIO_ACUM, av.PRINCIPAL_OBJETIVOS as GENERAL_ACUMULADO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, PREGUNTA as pre WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND pre.FK_SESION_ID=se.ID AND av.FK_PREGUNTA_ID=pre.ID AND av.FK_ALUMNO_ID=al.ID AND se.ID=".$id_sesion." AND sec.ID=".$id_seccion." AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_code_1 ($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, se.OBJETIVOS as ACUM_TARGET, se.OBJETIVOS as SESION_TARGET, IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100) as OBLI_CUMP_SESION, IF(se.OBJETIVOS=0, 0, (av.OBJETIVOS/se.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF(se.OBJETIVOS=0, 0, av.OBJETIVOS/se.OBJETIVOS) as GENERAL_CUMP_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 80, 3, IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM,IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100)< 80, 3, IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) >= 80 AND IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se.ID=".$id_sesion."";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_video ($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, se.OBJETIVOS as ACUM_TARGET, (vi.OBJETIVOS-vi1.OBJETIVOS) as SESION_TARGET, IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(vi.OBJETIVOS-vi1.OBJETIVOS))*100) as OBLI_CUMP_SESION, IF(vi.OBJETIVOS=0, 0, (av.OBJETIVOS/vi.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, (av.OBJETIVOS-av1.OBJETIVOS)/(vi.OBJETIVOS-vi1.OBJETIVOS)) as GENERAL_CUMP_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 80, 3, IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM, IF(IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(vi.OBJETIVOS-vi1.OBJETIVOS))*100)< 80, 3, IF(IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(vi.OBJETIVOS-vi1.OBJETIVOS))*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) >= 80 AND IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO 
            FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_VIDEO as av, SESION as se1, AVANCE_VIDEO as av1 , ALUMNO as al1, VIDEO as vi, VIDEO as vi1, OBJETIVO_VIDEO as obj, OBJETIVO_VIDEO as obj1 
            WHERE obj.FK_SESION_ID=se.ID AND obj.FK_VIDEO_ID=vi.ID AND av.FK_VIDEO_ID=vi.ID AND se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND al.FK_SECCION_ID=sec.ID AND se1.ID=(SELECT ID FROM SESION as se2 WHERE se2.FECHA < se.FECHA AND se2.FK_CURSO_ID=cu.ID ORDER BY se2.FECHA LIMIT 1) AND obj1.FK_SESION_ID=se1.ID AND obj1.FK_VIDEO_ID=vi1.ID AND se1.FK_CURSO_ID=cu.ID AND av1.FK_ALUMNO_ID=al.ID
            AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_video_1 ($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, vi.OBJETIVOS as ACUM_TARGET, vi.OBJETIVOS as SESION_TARGET, IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100) as OBLI_CUMP_SESION, IF(vi.OBJETIVOS=0, 0, (av.OBJETIVOS/vi.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF(vi.OBJETIVOS=0, 0, av.OBJETIVOS/vi.OBJETIVOS) as GENERAL_CUMP_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 80, 3, IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM,IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100)< 80, 3, IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) >= 80 AND IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND obj.FK_VIDEO_ID=vi.ID AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_video_ini ($id_sesion, $inicio, $fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, se.OBJETIVOS as ACUM_TARGET, (vi.OBJETIVOS-vi1.OBJETIVOS) as SESION_TARGET, IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(vi.OBJETIVOS-vi1.OBJETIVOS))*100) as OBLI_CUMP_SESION, IF(vi.OBJETIVOS=0, 0, (av.OBJETIVOS/vi.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, (av.OBJETIVOS-av1.OBJETIVOS)/(vi.OBJETIVOS-vi1.OBJETIVOS)) as GENERAL_CUMP_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 80, 3, IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM, IF(IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(vi.OBJETIVOS-vi1.OBJETIVOS))*100)< 80, 3, IF(IF((vi.OBJETIVOS-vi1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(vi.OBJETIVOS-vi1.OBJETIVOS))*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) >= 80 AND IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_VIDEO as av, SESION as se1, AVANCE_VIDEO as av1 , ALUMNO as al1, VIDEO as vi, VIDEO as vi1, OBJETIVO_VIDEO as obj, OBJETIVO_VIDEO as obj1 WHERE se.ID=obj.FK_SESION_ID AND vi.ID=obj.FK_VIDEO_ID AND av.FK_VIDEO_ID=vi.ID AND al.ID=av.FK_ALUMNO_ID AND al.FK_SECCION_ID=sec.ID AND sec.FK_CURSO_ID=cu.ID AND cu.ID=se.FK_CURSO_ID AND se1.ID=(SELECT ID FROM SESION as se2 WHERE se2.FECHA < se.FECHA AND se2.FK_CURSO_ID=cu.ID LIMIT 1) AND vi1.ID AND se.ID=".$id_sesion." LIMIT ".$inicio.", ".$fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_video_ini_1 ($id_sesion, $inicio, $fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, vi.OBJETIVOS as ACUM_TARGET, vi.OBJETIVOS as SESION_TARGET, IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100) as OBLI_CUMP_SESION, IF(vi.OBJETIVOS=0, 0, (av.OBJETIVOS/vi.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF(vi.OBJETIVOS=0, 0, av.OBJETIVOS/vi.OBJETIVOS) as GENERAL_CUMP_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 80, 3, IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM,IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100)< 80, 3, IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(vi.OBJETIVOS = 0, 0, (av.TOTAL/vi.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) >= 80 AND IF(vi.OBJETIVOS=0, 0, (av.TOTAL/vi.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND obj.FK_VIDEO_ID=vi.ID AND se.ID=".$id_sesion." LIMIT ".$inicio.", ".$fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_code_ini ($id_sesion, $inicio, $fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, se.OBJETIVOS as ACUM_TARGET, (se.OBJETIVOS-se1.OBJETIVOS) as SESION_TARGET, IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(se.OBJETIVOS-se1.OBJETIVOS))*100) as OBLI_CUMP_SESION, IF(se.OBJETIVOS=0, 0, (av.OBJETIVOS/se.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, (av.OBJETIVOS-av1.OBJETIVOS)/(se.OBJETIVOS-se1.OBJETIVOS)) as GENERAL_CUMP_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 80, 3, IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM, IF(IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(se.OBJETIVOS-se1.OBJETIVOS))*100)< 80, 3, IF(IF((se.OBJETIVOS-se1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(se.OBJETIVOS-se1.OBJETIVOS))*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) >= 80 AND IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av, SESION as se1, AVANCE_CODE as av1 , ALUMNO as al1 WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se.ID=".$id_sesion." AND se1.ID=(SELECT ID FROM SESION as se2 WHERE se2.FECHA < se.FECHA AND se1.FK_CURSO_ID=cu.ID LIMIT 1) AND se1.ID=av1.FK_SESION_ID AND av1.FK_ALUMNO_ID=al1.ID AND al1.NOMBRE=al.NOMBRE LIMIT ".$inicio.", ".$fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_code_ini_1 ($id_sesion, $inicio, $fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, se.OBJETIVOS as ACUM_TARGET, se.OBJETIVOS as SESION_TARGET, IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100) as OBLI_CUMP_SESION, IF(se.OBJETIVOS=0, 0, (av.OBJETIVOS/se.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF(se.OBJETIVOS=0, 0, av.OBJETIVOS/se.OBJETIVOS) as GENERAL_CUMP_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 80, 3, IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM,IF(IF((se.OBJETIVOS-se.OBJETIVOS)=0, 0, (av.TOTAL/(se.OBJETIVOS-se.OBJETIVOS))*100)< 80, 3, IF(IF((se.OBJETIVOS-se.OBJETIVOS)=0, 0, (av.TOTAL/(se.OBJETIVOS-se.OBJETIVOS))*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(se.OBJETIVOS = 0, 0, (av.TOTAL/se.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) >= 80 AND IF(se.OBJETIVOS=0, 0, (av.TOTAL/se.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_CODE as av WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND av.FK_SESION_ID=se.ID AND se.ID=".$id_sesion." LIMIT ".$inicio.", ".$fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_pregunta ($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, pre.OBJETIVOS as ACUM_TARGET, (pre.OBJETIVOS-pre1.OBJETIVOS) as SESION_TARGET, IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(pre.OBJETIVOS-pre1.OBJETIVOS))*100) as OBLI_CUMP_SESION, IF(pre.OBJETIVOS=0, 0, (av.OBJETIVOS/pre.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, (av.OBJETIVOS-av1.OBJETIVOS)/(pre.OBJETIVOS-pre1.OBJETIVOS)) as GENERAL_CUMP_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 80, 3, IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM, IF(IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(pre.OBJETIVOS-pre1.OBJETIVOS))*100)< 80, 3, IF(IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(pre.OBJETIVOS-pre1.OBJETIVOS))*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) >= 80 AND IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, SESION as se1, AVANCE_PREGUNTA as av1 , ALUMNO as al1, PREGUNTA as pre, PREGUNTA as pre1 WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND se1.ID=(SELECT ID FROM SESION as se2 WHERE se2.FECHA < se.FECHA AND pre.FK_SESION_ID=se.ID AND pre1.FK_SESION_ID=se.ID AND se1.FK_CURSO_ID=cu.ID ORDER BY se1.FECHA LIMIT 1) AND av1.FK_ALUMNO_ID=al1.ID AND al1.NOMBRE=al.NOMBRE AND av1.FK_PREGUNTA_ID=pre1.ID AND av.FK_PREGUNTA_ID=pre.ID AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_pregunta_1 ($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, pre.OBJETIVOS as ACUM_TARGET, pre.OBJETIVOS as SESION_TARGET, IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100) as OBLI_CUMP_SESION, IF(pre.OBJETIVOS=0, 0, (av.OBJETIVOS/pre.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF(pre.OBJETIVOS=0, 0, av.OBJETIVOS/pre.OBJETIVOS) as GENERAL_CUMP_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 80, 3, IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM,IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100)< 80, 3, IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) >= 80 AND IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, PREGUNTA as pre WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND pre.FK_SESION_ID=se.ID AND av.FK_PREGUNTA_ID=pre.ID AND av.FK_ALUMNO_ID=al.ID AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_pregunta_ini ($id_sesion, $inicio, $fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL-av1.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS-av1.OBJETIVOS) as SESION_GENERAL, pre.OBJETIVOS as ACUM_TARGET, (pre.OBJETIVOS-pre1.OBJETIVOS) as SESION_TARGET, IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(pre.OBJETIVOS-pre1.OBJETIVOS))*100) as OBLI_CUMP_SESION, IF(pre.OBJETIVOS=0, 0, (av.OBJETIVOS/pre.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, (av.OBJETIVOS-av1.OBJETIVOS)/(pre.OBJETIVOS-pre1.OBJETIVOS)) as GENERAL_CUMP_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 80, 3, IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM, IF(IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(pre.OBJETIVOS-pre1.OBJETIVOS))*100)< 80, 3, IF(IF((pre.OBJETIVOS-pre1.OBJETIVOS)=0, 0, ((av.TOTAL-av1.TOTAL)/(pre.OBJETIVOS-pre1.OBJETIVOS))*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) >= 80 AND IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, SESION as se1, AVANCE_PREGUNTA as av1 , ALUMNO as al1, PREGUNTA as pre, PREGUNTA as pre1 WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND av.FK_ALUMNO_ID=al.ID AND se1.ID=(SELECT ID FROM SESION as se2 WHERE se2.FECHA < se.FECHA AND pre.FK_SESION_ID=se.ID AND pre1.FK_SESION_ID=se.ID AND se1.FK_CURSO_ID=cu.ID ORDER BY se1.FECHA LIMIT 1) AND av1.FK_ALUMNO_ID=al1.ID AND al1.NOMBRE=al.NOMBRE AND av1.FK_PREGUNTA_ID=pre1.ID AND av.FK_PREGUNTA_ID=pre.ID AND se.ID=".$id_sesion." LIMIT ".$inicio.", ".$fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function tabla_pregunta_ini_1 ($id_sesion, $inicio, $fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.NOMBRE as SECCION, al.NOMBRE, DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO, av.TOTAL as OBLIGATORIO_ACUM, av.OBJETIVOS as GENERAL_ACUMULADO, (av.TOTAL) as SESION_OBLIGATORIO, (av.OBJETIVOS) as SESION_GENERAL, pre.OBJETIVOS as ACUM_TARGET, pre.OBJETIVOS as SESION_TARGET, IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) as OBLI_CUMP_ACUM, IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100) as OBLI_CUMP_SESION, IF(pre.OBJETIVOS=0, 0, (av.OBJETIVOS/pre.OBJETIVOS)*100) as GENERAL_CUMP_ACUM, IF(pre.OBJETIVOS=0, 0, av.OBJETIVOS/pre.OBJETIVOS) as GENERAL_CUMP_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 80, 3, IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 2, 1)) as COLOR_OBLIG_ACUM,IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100)< 80, 3, IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100) < 100, 2, 1)) as COLOR_OBLIG_SESION, IF(IF(pre.OBJETIVOS = 0, 0, (av.TOTAL/pre.OBJETIVOS)*100 )< 80, 'Por debajo de la meta', IF(IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) >= 80 AND IF(pre.OBJETIVOS=0, 0, (av.TOTAL/pre.OBJETIVOS)*100 ) < 100, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM SECCION as sec, CURSO as cu, ALUMNO as al, SESION as se, AVANCE_PREGUNTA as av, PREGUNTA as pre WHERE cu.ID=sec.FK_CURSO_ID AND al.FK_SECCION_ID=sec.ID AND se.FK_CURSO_ID=cu.ID AND pre.FK_SESION_ID=se.ID AND av.FK_PREGUNTA_ID=pre.ID AND av.FK_ALUMNO_ID=al.ID AND se.ID=".$id_sesion." LIMIT ".$inicio.", ".$fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            
            $this->desconectar();
        }
        return $matriz;
    }

    public function fecha_sesiones()
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT ID, DATE_FORMAT(FECHA, '%d-%m-%Y') as FECHA FROM SESION";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function fecha_sesiones_escuela($id_escuela)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT se.ID, DATE_FORMAT(se.FECHA, '%d-%m-%Y') as FECHA FROM SESION as se, ESCUELA as es, CURSO as cu WHERE se.FK_CURSO_ID=cu.ID AND cu.FK_ESCUELA_ID=es.ID AND es.ID=".$id_escuela."  ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function fechas_avance_code($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT se1.ID, DATE_FORMAT(se1.FECHA, '%d-%m-%Y') FROM SESION as se, SESION as se1, AVANCE_CODE as av WHERE av.FK_SESION_ID=se.ID AND se1.FECHA <= se.FECHA AND se1.FK_CURSO_ID=se.FK_CURSO_ID AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function fechas_avance_video($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT se1.ID, DATE_FORMAT(se1.FECHA, '%d-%m-%Y') FROM SESION as se, SESION as se1, OBJETIVO_VIDEO as obj WHERE obj.FK_SESION_ID=se.ID AND se1.FECHA <= se.FECHA AND se1.FK_CURSO_ID=se.FK_CURSO_ID AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function fechas_avance_pregunta($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT se1.ID, DATE_FORMAT(se1.FECHA, '%d-%m-%Y') FROM SESION as se, SESION as se1, PREGUNTA as pre WHERE pre.FK_SESION_ID=se.ID AND se1.FECHA <= se.FECHA AND se1.FK_CURSO_ID=se.FK_CURSO_ID AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }
   
    public function ver_proyectos($id_sesion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT pro.ID, pro.NOMBRE FROM SESION as se, PROYECTO as pro, RUBRICA as ru, OBJETIVO_RUBRICA as obj WHERE obj.FK_SESION_ID=se.ID AND obj.FK_RUBRICA_ID=ru.ID AND ru.FK_PROYECTO_ID=pro.ID AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_proyectos_alumno($id_alumno, $id_seccion, $id_curso)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT pro.ID, pro.NOMBRE FROM SESION as se, PROYECTO as pro, RUBRICA as ru, OBJETIVO_RUBRICA as obj, SECCION as sec, ALUMNO as al, CURSO as cu WHERE obj.FK_SESION_ID=se.ID AND obj.FK_RUBRICA_ID=ru.ID AND ru.FK_PROYECTO_ID=pro.ID AND se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND obj.FK_ALUMNO_ID=al.ID AND al.ID=".$id_alumno." AND sec.ID=".$id_seccion." AND cu.ID=".$id_curso." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_secciones_proyectos($id_sesion, $id_proyecto)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT sec.NOMBRE FROM OBJETIVO_RUBRICA as obj, SESION as se, SECCION as sec, CURSO as cu , PROYECTO as pro, RUBRICA as ru WHERE obj.FK_SESION_ID=se.ID AND obj.FK_RUBRICA_ID=ru.ID AND ru.FK_PROYECTO_ID=pro.ID AND se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND pro.ID=".$id_proyecto." AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_subtabla_0($id_sesion, $id_proyecto)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT sec.NOMBRE as SECCION, al.NOMBRE , DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO as SESION FROM PROYECTO as pro, RUBRICA as ru, OBJETIVO_RUBRICA as obj, SESION as se, CURSO as cu, SECCION as sec, ALUMNO as al WHERE se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND obj.FK_ALUMNO_ID=al.ID AND obj.FK_SESION_ID=se.ID AND obj.FK_RUBRICA_ID=ru.ID AND ru.FK_PROYECTO_ID=pro.ID AND pro.ID=".$id_proyecto." AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();

                //----------------------
                //----------------------
                
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_subtabla_0_ini($id_sesion, $id_proyecto, $inicio, $fin)
    {
        
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT sec.NOMBRE as SECCION, al.NOMBRE , DATE_FORMAT(se.FECHA, '%d-%m-%Y'), se.NUMERO as SESION FROM PROYECTO as pro, RUBRICA as ru, OBJETIVO_RUBRICA as obj, SESION as se, CURSO as cu, SECCION as sec, ALUMNO as al WHERE se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND obj.FK_ALUMNO_ID=al.ID AND obj.FK_SESION_ID=se.ID AND obj.FK_RUBRICA_ID=ru.ID AND ru.FK_PROYECTO_ID=pro.ID AND pro.ID=".$id_proyecto." AND se.ID=".$id_sesion." LIMIT ".$inicio.", ".$fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();

                //----------------------
                //----------------------
                
            }
            $this->desconectar();
           
        }
        return $matriz;
    }

    public function ver_nota_rubrica_alumno($id_alumno, $id_sesion, $id_proyecto, $id_seccion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT obj.ID, obj.VALOR, obj.SESION_TARGET FROM PROYECTO as pro, RUBRICA as ru, OBJETIVO_RUBRICA as obj, SESION as se, SECCION as sec, ALUMNO as al WHERE obj.FK_SESION_ID=se.ID AND obj.FK_RUBRICA_ID=ru.ID AND obj.FK_ALUMNO_ID=al.ID AND ru.FK_PROYECTO_ID=pro.ID AND al.FK_SECCION_ID=sec.ID AND sec.ID=".$id_seccion." AND pro.ID=".$id_proyecto." AND al.ID=".$id_alumno." AND se.ID=".$id_sesion." ORDER BY obj.ID DESC";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_valores_rubricas($id_alumno, $id_sesion, $id_proyecto, $id_seccion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT obj.SESION_TARGET, COUNT(obj.ID)/obj.SESION_TARGET as CUMP_SESION,IF(COUNT(obj.ID)/obj.SESION_TARGET < 0.8, 3,IF(COUNT(obj.ID)/obj.SESION_TARGET < 1,2,1)) as COLOR_SESION, IF(COUNT(obj.ID)/obj.SESION_TARGET < 0.8, 'Por debajo de meta', IF(COUNT(obj.ID)/obj.SESION_TARGET >= 0.8 AND COUNT(obj.ID)/obj.SESION_TARGET < 1, 'En entorno de meta', 'En meta')) as NIVEL_CUMPLIMIENTO FROM PROYECTO as pro, RUBRICA as ru, OBJETIVO_RUBRICA as obj, SESION as se, SECCION as sec, ALUMNO as al  WHERE obj.FK_SESION_ID=se.ID AND obj.FK_RUBRICA_ID=ru.ID AND obj.FK_ALUMNO_ID=al.ID AND ru.FK_PROYECTO_ID=pro.ID AND al.FK_SECCION_ID=sec.ID AND sec.ID=".$id_seccion." AND pro.ID=".$id_proyecto." AND al.ID=".$id_alumno." AND se.ID=".$id_sesion." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_secciones($id_curso)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.ID, sec.NOMBRE FROM SECCION as sec, CURSO as cu WHERE sec.FK_CURSO_ID=cu.ID AND cu.ID=".$id_curso." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_secciones_escuela($id_curso, $id_escuela)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.ID, sec.NOMBRE FROM SECCION as sec, CURSO as cu, ESCUELA as es WHERE sec.FK_CURSO_ID=cu.ID AND cu.ID=".$id_curso." AND cu.FK_ESCUELA_ID=es.ID AND es.ID=".$id_escuela." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_seccion($id_curso, $id_seccion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT sec.ID, sec.NOMBRE FROM SECCION as sec, CURSO as cu WHERE sec.FK_CURSO_ID=cu.ID AND cu.ID=".$id_curso." AND sec.ID=".$id_seccion."  ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_alumnos($id_curso, $id_seccion)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT al.ID, al.NOMBRE FROM SECCION as sec, CURSO as cu, ALUMNO as al WHERE sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND sec.ID=".$id_seccion." AND cu.ID=".$id_curso." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_alumno($id_curso, $id_seccion, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT al.ID, al.NOMBRE, al.DNI FROM SECCION as sec, CURSO as cu, ALUMNO as al WHERE sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND sec.ID=".$id_seccion." AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_alumno_dni($id_curso, $id_seccion, $dni) 
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT al.ID, al.NOMBRE, al.DNI FROM SECCION as sec, CURSO as cu, ALUMNO as al WHERE sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND sec.ID=".$id_seccion." AND cu.ID=".$id_curso." AND al.DNI=".$dni." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_cursos()
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT cu.ID, cu.NUMERO FROM CURSO as cu";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_cursos_escuela($id_escuela)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT cu.ID, cu.NUMERO FROM CURSO as cu, ESCUELA as es WHERE cu.FK_ESCUELA_ID=es.ID AND es.ID=".$id_escuela." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_sesiones_alumno_code($id_curso, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_CODE as av WHERE se.FK_CURSO_ID=cu.ID AND av.FK_SESION_ID=se.ID AND av.FK_ALUMNO_ID=al.ID AND al.ID=".$id_alumno." AND cu.ID=".$id_curso." ORDER BY se.FECHA DESC";
            $query = "SELECT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_CODE as av WHERE se.FK_CURSO_ID=cu.ID AND av.FK_SESION_ID=se.ID AND av.FK_ALUMNO_ID=al.ID AND al.ID=".$id_alumno." AND cu.ID=".$id_curso." ORDER BY se.FECHA";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_sesiones_alumno_code_rango($id_curso, $id_alumno, $rango_inicio, $rango_fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_CODE as av WHERE se.FK_CURSO_ID=cu.ID AND av.FK_SESION_ID=se.ID AND av.FK_ALUMNO_ID=al.ID AND al.ID=".$id_alumno." AND cu.ID=".$id_curso." ORDER BY se.FECHA DESC LIMIT ".$rango_inicio.", ".$rango_fin." ";
            $query = "SELECT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_CODE as av WHERE se.FK_CURSO_ID=cu.ID AND av.FK_SESION_ID=se.ID AND av.FK_ALUMNO_ID=al.ID AND al.ID=".$id_alumno." AND cu.ID=".$id_curso." ORDER BY se.FECHA LIMIT ".$rango_inicio.", ".$rango_fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_sesiones_alumno_video($id_curso, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj, SECCION as sec WHERE se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND av.FK_ALUMNO_ID=al.ID AND vi.ID=av.FK_VIDEO_ID AND obj.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ORDER BY se.FECHA DESC";
            $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj, SECCION as sec WHERE se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND av.FK_ALUMNO_ID=al.ID AND vi.ID=av.FK_VIDEO_ID AND obj.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ORDER BY se.FECHA";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_sesiones_alumno_video_rango($id_curso, $id_alumno, $rango_inicio, $rango_fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            // $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj, SECCION as sec WHERE se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND av.FK_ALUMNO_ID=al.ID AND vi.ID=av.FK_VIDEO_ID AND obj.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ORDER BY se.FECHA DESC LIMIT ".$rango_inicio.", ".$rango_fin." ";
            $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_VIDEO as av, VIDEO as vi, OBJETIVO_VIDEO as obj, SECCION as sec WHERE se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND av.FK_ALUMNO_ID=al.ID AND vi.ID=av.FK_VIDEO_ID AND obj.FK_VIDEO_ID=vi.ID AND obj.FK_SESION_ID=se.ID AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ORDER BY se.FECHA LIMIT ".$rango_inicio.", ".$rango_fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_sesiones_alumno_pregunta($id_curso, $id_alumno)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_PREGUNTA as av, PREGUNTA as pre, SECCION as sec WHERE pre.FK_SESION_ID=se.ID AND av.FK_PREGUNTA_ID=pre.ID AND av.FK_ALUMNO_ID=al.ID AND se.FK_CURSO_ID=cu.ID AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ORDER BY se.FECHA";
            // $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_PREGUNTA as av, PREGUNTA as pre, SECCION as sec WHERE pre.FK_SESION_ID=se.ID AND av.FK_PREGUNTA_ID=pre.ID AND av.FK_ALUMNO_ID=al.ID AND se.FK_CURSO_ID=cu.ID AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ORDER BY se.FECHA DESC";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_sesiones_alumno_pregunta_rango($id_curso, $id_alumno, $rango_inicio, $rango_fin)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, AVANCE_PREGUNTA as av, PREGUNTA as pre, SECCION as sec WHERE pre.FK_SESION_ID=se.ID AND av.FK_PREGUNTA_ID=pre.ID AND av.FK_ALUMNO_ID=al.ID AND se.FK_CURSO_ID=cu.ID AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." ORDER BY se.FECHA LIMIT ".$rango_inicio.", ".$rango_fin." ";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }

    public function ver_sesiones_proyectos($id_curso, $id_seccion, $id_alumno, $id_proyecto)
    {
        $matriz = [];
        $this->conectar();
        if($this->conexion)
        {
            $query = "SELECT DISTINCT se.ID, se.NUMERO, se.FECHA FROM SESION as se, CURSO as cu, ALUMNO as al, SECCION as sec, OBJETIVO_RUBRICA as obj, RUBRICA as ru, PROYECTO as pro WHERE obj.FK_SESION_ID=se.ID AND obj.FK_ALUMNO_ID=al.ID AND se.FK_CURSO_ID=cu.ID AND sec.FK_CURSO_ID=cu.ID AND al.FK_SECCION_ID=sec.ID AND obj.FK_RUBRICA_ID=ru.ID AND ru.FK_PROYECTO_ID=pro.ID AND sec.ID=".$id_seccion." AND cu.ID=".$id_curso." AND al.ID=".$id_alumno." AND pro.ID=".$id_proyecto." ORDER BY se.FECHA DESC";
            if($resultado = mysqli_query($this->conexion, $query))
            {
                $i = 0;
                while ($filas = $resultado->fetch_assoc())
                {
                    $matriz[$i++] = $filas;
                }
                $resultado->free();
            }
            $this->desconectar();
        }
        return $matriz;
    }
}
?>