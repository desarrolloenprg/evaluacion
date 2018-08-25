<?php

require_once 'conexion.php';
require_once 'google.php';

class Code
{
    private $id_seccion;
    private $hoja_seccion;
    private $google;
    private $cone;

    public function Code ()
    {
        $this->cone = new Conexion('localhost', 'prg_test', '123456', 'kenshin'); //local
        // $this->cone = new Conexion('localhost', 'prg_test', 'aurora_091', 'kenshin');
    }

    public function cargar ($id_seccion, $hoja_seccion, $rango)
    {
        // $this->id_seccion = $id_seccion;
        // $this->hoja_seccion = $hoja_seccion;
        $this->google = new Google();
        $id_sesion = -1;
        try 
        {
            $matriz = $this->google->matriz($id_seccion, $hoja_seccion.'!'.$rango);
            // echo "prin_objetivos: ".$matriz[2][1]."</br>";
            // echo "objetivos: ".$matriz[3][1]."</br>";
            // exit;
            $id_curso = $this->agregar_curso($matriz);
            // echo "id_curso: ".$id_curso."</br>"; 
            // exit;
            if ($id_curso > 0)
            {
                $id_seccion = $this->agregar_seccion($id_curso, $matriz);
                
                $id_sesion = $this->agregar_sesion($id_curso, $matriz);
                $vector = $this->ver_etapas($matriz);
                $this->agregar_etapa($id_curso, $vector);
                $matriz_aux = $this->matriz_etapa_reto($matriz);
                $this->agregar_reto($id_curso, $matriz_aux);
                //--------------------------------------------------
                $principal_objetivos = $matriz[2][1];
                $principal_total = $matriz[3][1];

                $matriz_aux_1 = $this->matriz_avance_alumno($matriz);
                // $this->agregar_avance_alumno($id_seccion, $id_sesion, $matriz_aux_1);
                $this->cone->agregar_avance_alumno($principal_objetivos, $principal_total, $matriz_aux_1, $id_seccion, $id_sesion);
            }
        }
        catch(Exception $e) { return $e; }
        return $id_sesion;
    }

    public function cargar_config($id_seccion, $hoja_seccion, $rango)
    {
        $this->google = new Google();
        $matriz = $this->google->matriz($id_seccion, $hoja_seccion.'!'.$rango);
        
        foreach ($matriz as $vector)
        {
            $nombre_pais = $vector[0];
            $nombre_escuela = $vector[1];
            
            $id_pais = $this->cone->agregar_pais($nombre_pais);
            $id_escuela = $this->cone->agregar_escuela($id_pais, $nombre_escuela);
            // echo "pais: ".$nombre_pais.", escuela: ".$nombre_escuela."</br>";
        }
        
        return $matriz;
    }

    public function ver_paises()
    {
        $matriz = $this->cone->ver_paises();
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }

        return $vector;
    }

    public function ver_escuelas($id_pais)
    {
        $matriz = $this->cone->ver_escuelas($id_pais);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }

        return $vector;
    }

    public function ver_comentario_individual ($seccion, $nombre_alumno, $id_seccion, $hoja_seccion, $rango)
    {
        $this->google = new Google();
        $matriz = $this->google->matriz($id_seccion, $hoja_seccion.'!'.$rango);
        $index = -1;

        if (strcmp($seccion['NOMBRE'], $matriz[1][1]) == 0)
        {
            for ($i = 0; $i < count($matriz[3]); $i++)
            {
                if (strcmp($matriz[3][$i], $nombre_alumno) == 0)
                {
                        $index = $i;
                }
            }
        }

        $comentario = "";
        if ($index > -1) { $comentario = $matriz[4][$index]; }
        $apoyo = $matriz[2][1];

        $info['COMENTARIO'] = $comentario;
        $info['APOYO'] = $apoyo;

        return $info;
    }

    public function ver_noticias($id_seccion, $hoja_seccion, $rango)
    {
        $matriz = $this->google->matriz($id_seccion, $hoja_seccion.'!'.$rango);
        return $matriz;
    }

    public function ver_representantes($id_seccion, $hoja_seccion, $rango)
    {
        if ($this->google == null) {$this->google = new Google();}
        $matriz = $this->google->matriz($id_seccion, $hoja_seccion.'!'.$rango);
        return $matriz;
        
    }

    public function ver_info_proyectos($id_seccion, $hoja_seccion, $rango)
    {
        $matriz = $this->google->matriz($id_seccion, $hoja_seccion.'!'.$rango);
        return $matriz;
    }

    public function cargar_video($id, $hoja, $rango)
    {
        // $this->id_seccion = $id;
        // $this->hoja_seccion = $hoja;
        $this->google = new Google();
        $id_sesion = -1;
        $id_seccion = -1;
        $id_curso = -1;
        try
        {
            $matriz = $this->google->matriz($id, $hoja.'!'.$rango);
            $principal_objetivos = $matriz[2][1];
            $principal_total = $matriz[3][1];
            // echo "prin_objetivos: ".$principal_objetivos."</br>";
            // echo "principal_total: ".$principal_total."</br>";
            // exit;

            $id_seccion = $this->cone->existe_seccion($matriz[1][1]);
            if($id_seccion == -1)
            {
                $id_curso = $this->agregar_curso($matriz);
                $id_seccion = $this->agregar_seccion($id_curso, $matriz);
            }
            
            $id_sesion = $this->existe_sesion($matriz[0][5], $matriz[0][3]);
            if($id_sesion == -1)
            {
                if ($id_curso == -1) { $id_curso = $this->agregar_curso($matriz); }
                $id_sesion = $this->agregar_sesion($id_curso, $matriz);
            }
            
            // nombre, objetivo, totales
            $id_video = $this->cone->agregar_video($matriz[1][5], round($matriz[2][1]), round($matriz[3][1]));
            // echo "video: ".$id_video."";
            // exit;
            // echo "id_video: ".$id_video."</br>";

            $this->cone->agregar_objetivo_video($id_video, $id_sesion);
            $this->cone->agregar_avance_video($principal_objetivos, $principal_total, $id_video, $id_seccion, $matriz);

        }
        catch(Exeption $e)
        {
            return $e;
        }

        // exit;
        return $id_sesion;
    }

    public function cargar_pregunta($id, $hoja, $rango)
    {
        // $this->id_seccion = $id;
        // $this->hoja_seccion = $hoja;
        $this->google = new Google();
        $id_sesion = -1;
        $id_curso = -1;
        try
        {
            $matriz = $this->google->matriz($id, $hoja.'!'.$rango);
            $principal_objetivos = $matriz[2][1];
            $principal_total = $matriz[3][1];

            // echo "principal_objetivos: ".$principal_objetivos."</br>";
            // echo "principal_total: ".$principal_total."</br>";
            // exit;
            $id_seccion = $this->cone->existe_seccion($matriz[1][1]);
            if($id_seccion == -1)
            {
                $id_curso = $this->agregar_curso($matriz);
                $id_seccion = $this->agregar_seccion($id_curso, $matriz);

            }

            $id_sesion = $this->existe_sesion($matriz[0][5], $matriz[0][3]);
            if($id_sesion == -1)
            {
                if ($id_curso == -1) { $id_curso = $this->agregar_curso($matriz); }
                $id_sesion = $this->agregar_sesion($id_curso, $matriz);
            }

            //objetivos, totales, sesion
            $id_pregunta = $this->cone->agregar_pregunta(round($matriz[2][1]), round($matriz[3][1]), $id_sesion);
            $this->cone->agregar_avance_pregunta($principal_objetivos, $principal_total, $id_pregunta, $id_seccion, $matriz);

        }
        catch(Exeption $e)
        {
            return $e;
        }
        return $id_sesion;
    }

    public function carga_proyecto($id, $hoja, $rango, $sesion_target)
    {
        $this->google = new Google();
        $id_sesion = -1;
        $id_curso = -1;
        try
        {
            $valores = explode("-", $rango);
            $matriz = $this->google->matriz($id, $hoja.'!'.$valores[0]);


            $matriz_info = $this->google->matriz($id, $hoja.'!A1:H2');
            $lista = explode('-', $matriz_info[0][1]);
            $fecha = $lista[2].'-'.$lista[1].'-'.$lista[0];
            if($this->cone->existe_sesion($matriz_info[0][3], $fecha) > 0)
            {
                $id_proyecto = $this->cone->agregar_proyecto($matriz[0][count($matriz[0])-1]);
                print("id_proyecto: ".$id_proyecto."</br>");
                $this->cone->agregar_rubricas_alumnos($id_proyecto, $matriz, $valores[1], $matriz_info, $sesion_target);
            }
            else
            {
                return -10;
            }
        }
        catch(Exeption $e)
        {
            return $e;
        }
        return 1;
    }

    public function carga_proyecto_nuevo($id, $hoja, $rango)
    {
        $this->google = new Google();
        $id_sesion = -1;
        $id_curso = -1;
        try
        {
            $matriz = $this->google->matriz($id, $hoja.'!'.$rango);
            var_dump($matriz);
            exit;
        }
        catch(Exeption $e)
        {
            return $e;
        }
        return 1;
    }

    public function tabla_proyectos($id_sesion, $id_proyecto)
    {
        $matriz = array();
        $matriz_aux = array();
        $secciones = $this->cone->ver_secciones_proyectos($id_sesion, $id_proyecto);

        $matriz_aux = $this->cone->ver_subtabla_0($id_sesion, $id_proyecto);
        foreach($matriz_aux as $fila)
        {
            $id_seccion = $this->cone->existe_seccion($fila["SECCION"]);
            $id_alumno = $this->cone->existe_alumno($fila["NOMBRE"], $id_seccion);
            $notas = $this->cone->ver_nota_rubrica_alumno($id_alumno, $id_sesion, $id_proyecto, $id_seccion);
            $cont = 1;
            foreach($notas as $info)
            {
                $fila["RUBRICA ".$cont] = $info["VALOR"];
                $cont++;
            }
            $valores = $this->cone->ver_valores_rubricas($id_alumno, $id_sesion, $id_proyecto, $id_seccion);
            $fila["SESION_TARGET"] = $valores[0]["SESION_TARGET"];
            $fila["CUMP_SESION"] = $valores[0]["CUMP_SESION"];
            $fila["COLOR_SESION"] = $valores[0]["COLOR_SESION"];
            $fila["NIVEL_CUMPLIMIENTO"] = $valores[0]["NIVEL_CUMPLIMIENTO"];

            $matriz[] = $fila;
        }
        return $matriz;
    }

    public function tabla_proyectos_ini($id_sesion ,$id_proyecto ,$inicio, $fin)
    {
        $matriz = array();
        $matriz_aux = array();
        // $secciones = $this->cone->ver_secciones_proyectos($id_sesion, $id_proyecto);

        $cont = $inicio;
        $i = 1;
        $matriz_aux = $this->cone->ver_subtabla_0_ini($id_sesion, $id_proyecto, $inicio, $fin);

        foreach($matriz_aux as $fila)
        {
            $id_seccion = $this->cone->existe_seccion($fila["SECCION"]);
            $id_alumno = $this->cone->existe_alumno($fila["NOMBRE"], $id_seccion);
            $notas = $this->cone->ver_nota_rubrica_alumno($id_alumno, $id_sesion, $id_proyecto, $id_seccion);
            $cont = 1;
            foreach($notas as $info)
            {
                $fila["RUBRICA ".$cont] = $info["VALOR"];
                $cont++;
            }
            $valores = $this->cone->ver_valores_rubricas($id_alumno, $id_sesion, $id_proyecto, $id_seccion);
            $fila["SESION_TARGET"] = $valores[0]["SESION_TARGET"];
            $fila["CUMP_SESION"] = $valores[0]["CUMP_SESION"];
            $fila["COLOR_SESION"] = $valores[0]["COLOR_SESION"];
            $fila["NIVEL_CUMPLIMIENTO"] = $valores[0]["NIVEL_CUMPLIMIENTO"];

            $matriz[] = $fila;
        }

        return $matriz;
    }

    private function existe_sesion($numero, $fecha)
    {
        $f = explode('-', $fecha);
        $fecha = $f[2].'-'.$f[1].'-'.$f[0];
        // print("numero: ".$numero.", fecha: ".$fecha);
        $id_sesion = $this->cone->existe_sesion($numero, $fecha);
        // print('id_sesion: '.$id_sesion);
        return $id_sesion;
    }

    private function matriz_avance_alumno(&$matriz)
    {
        $matriz_aux = array();
        for($j=2; $j < count($matriz[3]); $j++)
        {
            $matriz_aux[] = array($matriz[2][$j], $matriz[3][$j], $matriz[4][$j]);
        }

        return $matriz_aux;
    }

    private function matriz_etapa_reto(&$matriz)
    {
        $matriz_aux = array();
        for($i=5; $i < count($matriz); $i++)
        {
            $matriz_aux[] = array($matriz[$i][0], $matriz[$i][1]);
        }
        return $matriz_aux;
    }

    private function agregar_curso(&$matriz)
    {
        $id_curso = -1;
        $num_curso = $matriz[1][3]; // posicion de la variable curso
        $nombre_pais = $matriz[1][9]; 
        $nombre_escuela = $matriz[0][11];
        
        $id_pais = $this->cone->existe_pais($nombre_pais);
        $id_escuela = $this->cone->existe_escuela($id_pais, $nombre_escuela);

        if ($id_pais != -1 && $id_escuela != -1)
        {
            try
            {
                $id_curso = $this->cone->agregar_curso_escuela($num_curso, $id_escuela);
            }
            catch(Exception $e)
            {
                throw new Exception("Error al conectarse con la BD.");
            }
        }

        return $id_curso;
    }

    private function agregar_seccion (&$id_curso, &$matriz)
    {
        $nombre = $matriz[1][1]; //posicion de la variable nombre seccion
        $id_seccion = $this->cone->agregar_seccion($nombre, $id_curso);
        return $id_seccion;
    }

    private function agregar_sesion (&$id_curso, &$matriz)
    {
        $numero = $matriz[0][5];
        $fecha = $matriz[0][3];
        $objetivos = $matriz[2][1];
        $totales = $matriz[3][1];

        $id_sesion = $this->cone->agregar_sesion ($numero, $fecha, $totales, $objetivos, $id_curso);
        return $id_sesion;
    }

    // index para un array no asociativo
    private function index ($vector, $elemento)
    {
        for ($i=0; $i < count($vector); $i++)
        {
            if($vector[$i] == $elemento)
            {
                return $i;
            }
        }
        return -1;
    }

    private function ver_etapas(&$matriz)
    {
        $vector = array();
        for($i=5; $i < count($matriz); $i++)
        {
            if($this->index($vector, $matriz[$i][0]) == -1)
            {
                $vector[] = $matriz[$i][0];
            }
        }
        return $vector;
    }

    private function agregar_etapa ($id_curso, &$vector)
    {
        $this->cone->agregar_etapas($vector, $id_curso);
    }

    private function agregar_reto ($id_curso, &$matriz_aux)
    {
        $this->cone->agregar_retos($matriz_aux, $id_curso);
    }

    private function trasponer_matriz($matriz)
    {
        $nueva_matriz = array();
        for($j=0; $j < count($matriz[0]); $j++)
        {
            $nueva_matriz[] = array($matriz[0][$j], $matriz[1][$j], $matriz[2][$j]);
        }

        return $nueva_matriz;
    }

    private function agregar_avance_alumno ($id_seccion, $id_sesion, &$matriz)
    {
        $this->cone->agregar_avance_alumno($matriz, $id_seccion, $id_sesion);
    }

    // revisar
    public function tabla_code($id_sesion)
    {
        $matriz = $this->cone->tabla_code($id_sesion);
        if(count($matriz) == 0)
        {
            $matriz = $this->cone->tabla_code_1($id_sesion);
        }
        return $matriz;
    }

    public function boleta_code($id_curso, $id_seccion, $id_alumno)
    {
        $matriz_aux = [];
        $matriz = [];
        $sesiones = $this->cone->ver_sesiones_alumno_code($id_curso, $id_alumno);
        // for($i=0; $i < count($sesiones); $i++)
        // {
        //     if($i < count($sesiones)-1)
        //     {
        //         $matriz_aux = $this->cone->boleta_code($sesiones[$i]["ID"], $sesiones[$i+1]["ID"], $id_curso, $id_seccion, $id_alumno);
        //     }
        //     else
        //     {
        //         $matriz_aux = $this->cone->boleta_code_1($sesiones[$i]["ID"], $id_curso, $id_seccion, $id_alumno);
        //     }
        //     $matriz[] = $matriz_aux;
        // }
        for($i=0; $i < count($sesiones); $i++)
        {
            $matriz_aux = $this->cone->boleta_code_1($sesiones[$i]["ID"], $id_curso, $id_seccion, $id_alumno);
            $matriz[] = $matriz_aux;
        }

        return $matriz;
    }

    public function boleta_code_rango($id_curso, $id_seccion, $id_alumno, $rango_inicio, $rango_fin)
    {
        $matriz_aux = [];
        $matriz = [];
        $sesiones = $this->cone->ver_sesiones_alumno_code_rango($id_curso, $id_alumno, $rango_inicio, $rango_fin);
        
        for($i=0; $i < count($sesiones); $i++)
        {
            $matriz_aux = $this->cone->boleta_code_1($sesiones[$i]["ID"], $id_curso, $id_seccion, $id_alumno);
            $matriz[] = $matriz_aux;
        }
        
        return $matriz;
    }

    public function boleta_video($id_curso, $id_seccion, $id_alumno)
    {
        $matriz_aux = [];
        $matriz = [];
        $sesiones = $this->cone->ver_sesiones_alumno_video($id_curso, $id_alumno);
        for($i=0; $i < count($sesiones); $i++)
        {
            if($i < count($sesiones)-1)
            {
                $matriz_aux = $this->cone->boleta_video($sesiones[$i]["ID"], $sesiones[$i+1]["ID"], $id_curso, $id_seccion, $id_alumno);
            }
            else
            {
                $matriz_aux = $this->cone->boleta_video_1($sesiones[$i]["ID"], $id_curso, $id_seccion, $id_alumno);
            }
            $matriz[] = $matriz_aux;
        }

        return $matriz;
    }

    public function boleta_video_rango($id_curso, $id_seccion, $id_alumno, $rango_inicio, $rango_fin)
    {
        $matriz_aux = [];
        $matriz = [];
        
        $sesiones = $this->cone->ver_sesiones_alumno_video_rango($id_curso, $id_alumno, $rango_inicio, $rango_fin);

        for($i=0; $i < count($sesiones); $i++)
        {
            $matriz_aux = $this->cone->boleta_video_1($sesiones[$i]["ID"], $id_curso, $id_seccion, $id_alumno);
            $matriz[] = $matriz_aux;
        }

        // foreach ($matriz as $fila) {
        //     var_dump($fila);
        //     echo "</br> </br>";
        // }

        return $matriz;
    }

    public function boleta_pregunta($id_curso, $id_seccion, $id_alumno)
    {
        $matriz_aux = [];
        $matriz = [];
        $sesiones = $this->cone->ver_sesiones_alumno_pregunta($id_curso, $id_alumno);
        
        for($i=0; $i < count($sesiones); $i++)
        {
            if($i < count($sesiones)-1)
            {
                $matriz_aux = $this->cone->boleta_pregunta($sesiones[$i]["ID"], $sesiones[$i+1]["ID"], $id_curso, $id_seccion, $id_alumno);
            }
            else
            {
                $matriz_aux = $this->cone->boleta_pregunta_1($sesiones[$i]["ID"], $id_curso, $id_seccion, $id_alumno);
            }
            $matriz[] = $matriz_aux;
        }
        
        // var_dump($matriz);

        // foreach ($matriz as $fila) {
        //     var_dump($fila);
        //     echo "</br> </br>";
        // }

        // exit;
        return $matriz;
    }

    public function boleta_pregunta_rango($id_curso, $id_seccion, $id_alumno, $rango_inicio, $rango_fin)
    {
        $matriz_aux = [];
        $matriz = [];
        $sesiones = $this->cone->ver_sesiones_alumno_pregunta_rango($id_curso, $id_alumno, $rango_inicio, $rango_fin);
        for($i=0; $i < count($sesiones); $i++)
        {
            $matriz_aux = $this->cone->boleta_pregunta_1($sesiones[$i]["ID"], $id_curso, $id_seccion, $id_alumno);
            $matriz[] = $matriz_aux;
        }

        return $matriz;
    }

    public function boleta_rubrica($id_curso, $id_seccion, $id_alumno, $id_proyecto)
    {
        $matriz = [];
        $matriz["TOTAL_RUBRICA"] = null;
        $matriz["SESION_TARGET"] = null;
        $matriz["LABEL"] = null;
        $sesiones = $this->cone->ver_sesiones_proyectos($id_curso, $id_seccion, $id_alumno, $id_proyecto);
        $matriz["tam"] = count($sesiones);

        foreach($sesiones as $fila)
        {
            $notas = $this->cone->ver_nota_rubrica_alumno($id_alumno, $fila["ID"], $id_proyecto, $id_seccion);
            $cont = 0;
            foreach($notas as $info)
            {
                $cont++;
            }
            if($matriz["TOTAL_RUBRICA"] == null) { $matriz["TOTAL_RUBRICA"] = $cont;}
            else{ $matriz["TOTAL_RUBRICA"] = $matriz["TOTAL_RUBRICA"].';'.$cont; }

            $valores = $this->cone->ver_valores_rubricas($id_alumno, $fila["ID"], $id_proyecto, $id_seccion);
            if($matriz["SESION_TARGET"] == null) { $matriz["SESION_TARGET"] = $valores[0]["SESION_TARGET"];}
            else{ $matriz["SESION_TARGET"] = $matriz["SESION_TARGET"].';'.$valores[0]["SESION_TARGET"]; }

            if($matriz["LABEL"] == null) { $matriz["LABEL"] = $fila["ID"];}
            else{ $matriz["LABEL"] = $matriz["LABEL"].';'.$fila["ID"]; }
        }
        return $matriz;
    }


    public function boleta_rubrica_nuevo($id_curso, $id_seccion, $id_alumno) 
    {
        $google = new Google();
        $rango_hojas = $google->ver_rango_hojas_rubrica('1n8WU8uHO2aoS5gux1z32rq7Ryw3KoHUbWGlg2cb_jKE', 'config');
        $lista_hojas = $google->matriz('1n8WU8uHO2aoS5gux1z32rq7Ryw3KoHUbWGlg2cb_jKE', 'config!'.$rango_hojas);
        $resultado = [];
        $resultado["SESION"] = null;
        $resultado["CALIFICACION"] = null;
        $resultado["OBJETIVO"] = null;

        $conversion = array(1=>0, 2=>0.5, 3=>1);
        foreach ($lista_hojas as $hoja) 
        {
            $rango = $google->ver_rango('1n8WU8uHO2aoS5gux1z32rq7Ryw3KoHUbWGlg2cb_jKE', $hoja[0]);
            $matriz = $google->matriz('1n8WU8uHO2aoS5gux1z32rq7Ryw3KoHUbWGlg2cb_jKE', $hoja[0]."!".$rango);
            // echo $hoja[0]."!".$rango."</br>";
            $id_curso_1 = $this->cone->existe_curso($matriz[1][1]);
            if ( $id_curso == $id_curso_1) 
            {
                foreach ($matriz as $fila)
                {
                    $nombre_seccion = $fila[0];
                    $id_seccion_1 = $this->cone->existe_seccion($nombre_seccion);
                    
                    if ($id_seccion == $id_seccion_1) 
                    {
                        // $fila[3];
                        $lista_datos_persona = explode("-", $fila[3]);
                        $id_alumno_1 = $this->cone->existe_alumno($lista_datos_persona[1], $id_seccion);
                        
                        if ($id_alumno == $id_alumno_1)
                        {   
                            // echo $conversion[2]."-".$lista_datos_persona[1].", existe bitches</br>";
                            $calificacion = $conversion[$fila[5]] + $conversion[$fila[8]] + $conversion[$fila[11]] + $conversion[$fila[14]];
                            $objetivo = $conversion[$fila[6]] + $conversion[$fila[9]] + $conversion[$fila[12]] + $conversion[$fila[15]];
                            // echo "calificacion: ".$calificacion."</br>";
                            // echo "objetivo: ".$objetivo."</br>";

                            if ($resultado["SESION"] == null) { $resultado["SESION"] = (string) $matriz[1][3]; }
                            else { $resultado["SESION"] =  $resultado["SESION"].";".$matriz[1][3];}

                            if ($resultado["CALIFICACION"] == null) { $resultado["CALIFICACION"] = (string) $calificacion; }
                            else { $resultado["CALIFICACION"] =  $resultado["CALIFICACION"].";".$calificacion;}

                            if ($resultado["OBJETIVO"] == null) { $resultado["OBJETIVO"] = (string) $objetivo; }
                            else { $resultado["OBJETIVO"] =  $resultado["OBJETIVO"].";".$objetivo;}
                            break;
                        }
                    }
                }
            }
        }
        // var_dump($resultado);
        // echo "</br>";
        // exit;
        return $resultado;
    }

    public function tabla_code_ini($id_sesion, $inicio, $fin)
    {
        $matriz = $this->cone->tabla_code_ini($id_sesion, $inicio, $fin);
        if(count($matriz) == 0)
        {
            $matriz = $this->cone->tabla_code_ini_1($id_sesion, $inicio, $fin);
        }
        return $matriz;
    }

    public function tabla_video($id_sesion)
    {
        $matriz = $this->cone->tabla_video($id_sesion);
        if(count($matriz) == 0)
        {
            $matriz = $this->cone->tabla_video_1($id_sesion);
        }
        return $matriz;
    }

    public function tabla_video_ini($id_sesion, $inicio, $fin)
    {
        $matriz = $this->cone->tabla_video_ini($id_sesion, $inicio, $fin);
        if(count($matriz) == 0)
        {
            $matriz = $this->cone->tabla_video_ini_1($id_sesion, $inicio, $fin);
        }
        return $matriz;
    }

    public function tabla_pregunta($id_sesion)
    {
        $matriz = $this->cone->tabla_pregunta($id_sesion);
        if(count($matriz) == 0)
        {
            $matriz = $this->cone->tabla_pregunta_1($id_sesion);
        }
        return $matriz;
    }

    public function tabla_pregunta_ini($id_sesion, $inicio, $fin)
    {
        $matriz = $this->cone->tabla_pregunta_ini($id_sesion, $inicio, $fin);
        if(count($matriz) == 0)
        {
            $matriz = $this->cone->tabla_pregunta_ini_1($id_sesion, $inicio, $fin);
        }
        return $matriz;
    }

    public function fecha_sesiones()
    {
        $matriz = $this->cone->fecha_sesiones();
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['FECHA'];
        }
        return $vector;
    }

    public function fecha_sesiones_escuela($id_escuela)
    {
        $matriz = $this->cone->fecha_sesiones_escuela($id_escuela);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['FECHA'];
        }
        return $vector;
    }

    public function fecha_sesiones_escuela_seccion($id_seccion, $id_curso)
    {
        $matriz = $this->cone->fecha_sesiones_escuela_seccion($id_seccion, $id_curso);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['FECHA'];
        }
        return $vector;
    }

    public function fecha_sesiones_escuela_video($id_seccion, $id_curso)
    {
        $matriz = $this->cone->fecha_sesiones_escuela_video($id_seccion, $id_curso);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['FECHA'];
        }
        return $vector;
    }

    public function fecha_sesiones_escuela_pregunta($id_seccion, $id_curso)
    {
        $matriz = $this->cone->fecha_sesiones_escuela_pregunta($id_seccion, $id_curso);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['FECHA'];
        }
        return $vector;
    }

    public function tabla_code_excel($id_sesion)
    {
        $fechas = $this->cone->fechas_avance_code($id_sesion);
        $matriz = [];
        $matriz_aux = [];
        for($i=0; $i < count($fechas); $i++)
        {
            $matriz_aux = $this->tabla_code($fechas[$i]["ID"]);
            foreach($matriz_aux as $fila) { $matriz[] = $fila; }
        }
        return $matriz;
    }

    public function tabla_video_excel($id_sesion)
    {

        $fechas = $this->cone->fechas_avance_video($id_sesion);
        $matriz = [];
        $matriz_aux = [];
        for($i=0; $i < count($fechas); $i++)
        {
            $matriz_aux = $this->tabla_video($fechas[$i]["ID"]);
            foreach($matriz_aux as $fila) { $matriz[] = $fila; }
        }
        return $matriz;
    }

    public function tabla_pregunta_excel($id_sesion)
    {
        $fechas = $this->cone->fechas_avance_pregunta($id_sesion);
        $matriz = [];
        $matriz_aux = [];
        for($i=0; $i < count($fechas); $i++)
        {
            $matriz_aux = $this->tabla_pregunta($fechas[$i]["ID"]);
            foreach($matriz_aux as $fila) { $matriz[] = $fila; }
        }
        return $matriz;
    }


    public function ver_proyectos($id_sesion)
    {
        $matriz = $this->cone->ver_proyectos($id_sesion);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }
        return $vector;
    }

    public function ver_proyectos_alumno($id_alumno, $id_seccion, $id_curso)
    {
        $matriz = $this->cone->ver_proyectos_alumno($id_alumno, $id_seccion, $id_curso);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }
        return $vector;
    }

    public function ver_secciones($id_curso)
    {
        $matriz = $this->cone->ver_secciones($id_curso);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }
        return $vector;
    }

    public function ver_secciones_escuela($id_curso, $id_escuela)
    {
        $matriz = $this->cone->ver_secciones_escuela($id_curso, $id_escuela);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }
        return $vector;
    }

    public function ver_secciones_escuela_code($id_curso, $id_escuela)
    {
        $matriz = $this->cone->ver_secciones_escuela($id_curso, $id_escuela);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }
        return $vector;
    }

    public function ver_seccion($id_curso, $id_seccion)
    {
        $matriz = $this->cone->ver_seccion($id_curso, $id_seccion);
        return $matriz;
    }

    public function ver_alumnos($id_curso, $id_seccion)
    {
        $matriz = $this->cone->ver_alumnos($id_curso, $id_seccion);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NOMBRE'];
        }
        return $vector;
    }

    public function ver_alumno($id_curso, $id_seccion, $id_alumno)
    {
        $matriz = $this->cone->ver_alumno($id_curso, $id_seccion, $id_alumno);
        return $matriz;
    }

    public function ver_alumno_dni($id_curso, $id_seccion, $dni) 
    {
        $matriz = $this->cone->ver_alumno_dni($id_curso, $id_seccion, $dni);
        return $matriz;
    }

    public function ver_cursos()
    {
        $matriz = $this->cone->ver_cursos();
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NUMERO'];
        }
        return $vector;
    }

    public function ver_cursos_escuela($id_escuela)
    {
        $matriz = $this->cone->ver_cursos_escuela($id_escuela);
        $vector = array();
        for($i=0; $i < count($matriz); $i++)
        {
           $vector[$matriz[$i]['ID']] = $matriz[$i]['NUMERO'];
        }
        return $vector;
    }
}
?>