<?php
    set_time_limit(300);
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    // parametos en una peticion get
    //$allGetVars = $request->getQueryParams();
    //$getParam = $allGetVars['title'];

    require 'vendor/autoload.php';
    require_once 'google.php';
    require_once 'src/code.php';
    require_once 'src/PHPExcel.php';
    require_once 'src/PHPExcel/Writer/Excel2007.php';

    $configuration = [
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ];

    $config = new \Slim\Container($configuration);
    $app = new \Slim\App($config);

    $app->add(new \Slim\Middleware\Session([
        'name' => 'dummy_session',
        'autorefresh' => true,
        'lifetime' => '1 hour'
      ]));

    $container = $app->getContainer($configuration);

    $container['view'] = function ($container) {
        $view = new \Slim\Views\Twig('templates', [
            'cache' => false
        ]);

        $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
        $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

        return $view;
    };

    $container['session'] = function ($container) {
        return new \SlimSession\Helper;
      };


    $app->get('/', function($request, $response, $args)
    {

        return $this->view->render($response, 'home.html', []);
    })->setName('home');

    $app->get('/carga_code', function($request, $response, $args)
    {
        $error = -1;
        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }

        return $this->view->render($response, 'carga_code.html', ['error'=> $error]);
    })->setName('carga_code');

    $app->post('/data_code', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('carga_code'));
            }
        }

        $id_seccion = $request->getParsedBody()['id-seccion'];
        $hoja_seccion = $request->getParsedBody()['fichero-seccion-hoja'];

        // se abre la conexion con Drive
        $google = new Google();

        $rango = $google->cargar_code($id_seccion, $hoja_seccion)[0];

        if($rango == NULL || $rango == -1)
        {
            $this->session->error = 1;
            return $response->withRedirect($this->router->pathFor('carga_code'));
        }
        $id_sesion = -1;
        // se almacena la data en la bd
        $code = new Code();
        try
        {
            $id_sesion = $code->cargar($id_seccion, $hoja_seccion, $rango);
        }
        catch(Exception $e)
        {
            $this->session->error = 2;
            return $response->withRedirect($this->router->pathFor('carga_code'));
        }

        if ($id_sesion == -1) 
        {
            $this->session->error = 2;
            return $response->withRedirect($this->router->pathFor('carga_code'));
        }

        // print('id_sesion f: '.$id_sesion);
        return $response->withRedirect('tabla_code/'.$id_sesion);

    })->setName('data_code');

    $app->get('/tabla_code/{id}', function($request, $response, $args)
    {
        // $code = new Code();
        // $matriz = $code->tabla_code($args['id']);

        // return $this->view->render($response, 'tabla_code.html', ['matriz'=>$matriz]);
        $this->session->error = 0;
        return $response->withRedirect($this->router->pathFor('reporte_code'));
    })->setName('tabla_code');

    $app->get('/reporte_code', function($request, $response, $args)
    {
        $descarga = 0;
        $code = new Code();
        // $sesiones = $code->fecha_sesiones();
        $sesiones = null;
        $secciones = null;
        $matriz = array();
        $index = -1;
        $cantidad = 0;
        $inicio = 0;
        $fin = 10;
        $pagina = 1;
        $error = -1;
        $pais_default = null;
        $escuela_default = null;
        $index_curso = -1;
        $index_seccion = -1;
        $cursos = null;

        if ($this->session->exists('id_pais_default') && $this->session->exists('id_escuela_default'))
        {
            $lista_paises = $code->ver_paises();
            $pais_default = $lista_paises[$this->session->id_pais_default];

            $lista_escuelas = $code->ver_escuelas($this->session->id_pais_default);
            $escuela_default = $lista_escuelas[$this->session->id_escuela_default];
            $id_escuela = $this->session->id_escuela_default;
            $cursos = $code->ver_cursos_escuela($id_escuela);


            //************************************************ */
            if ($this->session->exists('id_curso'))
            {
                $index_curso = $this->session->id_curso;
                $secciones = $code->ver_secciones_escuela($this->session->id_curso, $id_escuela);

                if ($this->session->exists('id_seccion')) 
                {
                    $index_seccion = $this->session->id_seccion;
                   
                    $sesiones = $code->fecha_sesiones_escuela_seccion($index_seccion, $index_curso);
                    // echo "datos</br></br>";
                    // var_dump($sesiones);

                    // exit;
                    foreach ($sesiones as $id=>$fecha) {

                        $matriz_aux = $code->tabla_code($id);
                        $matriz = $code->tabla_code_ini($id, $inicio, $fin);
                        if (count($matriz) == 0) {
                            unset($sesiones[$id]);
                        }
                    }
                    $matriz = array();
                    // var_dump($sesiones);
                    // exit;

                    $this->session->delete('id_seccion');
                }
                $this->session->delete('id_curso');
            }

            //---------------------------------------------------
            
            //---------------------------------------------------
            // var_dump($sesiones);
        }

        if($this->session->exists('id_sesion'))
        {
            if($this->session->exists('pagina'))
            {
                $pagina = $this->session->pagina;
                $inicio = 10 * ($this->session->pagina - 1);
                // $fin = $inicio +10;
                $fin = 10;
                $this->session->delete('pagina');
            }

            $index = $this->session->id_sesion;
            $matriz_aux = $code->tabla_code($this->session->id_sesion);
            $matriz = $code->tabla_code_ini($this->session->id_sesion, $inicio, $fin);
            $cantidad = ceil(count($matriz_aux)/10);
            $this->session->delete('id_sesion');
            // exit;
            if(count($matriz) == 0)
            {
                $error = 1;
            }
        }

        if($this->session->exists('descarga'))
        {
            $descarga = $this->sesion->descarga;
            $this->session->delete('descarga');
        }

        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }
        return $this->view->render($response, 'reporte_code.html', ['index_seccion' => $index_seccion, 'index_curso' => $index_curso, 'secciones' => $secciones, 'cursos' => $cursos, 'pais_default' => $pais_default, 'escuela_default' => $escuela_default, 'error'=>$error, 'pagina'=>$pagina, 'cantidad'=>$cantidad ,'descarga' => $descarga, 'index'=>$index, 'sesiones'=>$sesiones, 'matriz'=>$matriz]);
    })->setName('reporte_code');

    $app->get('/paginacion_code/{id}/{pagina}', function($request, $response, $args)
    {
        if($args['id'] != NULL && $args['pagina'] != NULL)
        {
            $this->session->id_sesion = (int)$args['id'];
            $this->session->pagina = (int)$args['pagina'];
        }
        return $response->withRedirect($this->router->pathFor('reporte_code'));
    })->setName('paginacion_code');

    $app->get('/paginacion_video/{id}/{pagina}', function($request, $response, $args)
    {
        if($args['id'] != NULL && $args['pagina'] != NULL)
        {
            $this->session->id_sesion = (int)$args['id'];
            $this->session->pagina = (int)$args['pagina'];
        }
        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
    })->setName('paginacion_video');

    $app->get('/paginacion_pregunta/{id}/{pagina}', function($request, $response, $args)
    {
        if($args['id'] != NULL && $args['pagina'] != NULL)
        {
            $this->session->id_sesion = (int)$args['id'];
            $this->session->pagina = (int)$args['pagina'];
        }
        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
    })->setName('paginacion_pregunta');

    $app->get('/paginacion_proyectos/{id}/{pagina}/{id_proyecto}', function($request, $response, $args)
    {
        if($args['id'] != NULL && $args['pagina'] != NULL && $args['id_proyecto'] != NULL)
        {
            $this->session->id_sesion = (int)$args['id'];
            $this->session->pagina = (int)$args['pagina'];
            $this->session->id_proyecto = (int)$args['id_proyecto'];
        }
        return $response->withRedirect($this->router->pathFor('reporte_proyectos'));
    })->setName('paginacion_proyectos');

    $app->post('/busqueda_sesiones', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_code'));
            }
        }

        $id_sesion = $request->getParsedBody()['id_sesion'];
        $this->session->id_sesion = explode ('-', $id_sesion)[1];
        return $response->withRedirect($this->router->pathFor('reporte_code'));

    })->setName('busqueda_sesiones');

    $app->post('/excel_code', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_code'));
            }
        }
        $id_sesion = $request->getParsedBody()['id_sesion_1'];
        $code = new Code();

        // $matriz = $code->tabla_code($id_sesion);
        $matriz = $code->tabla_code_excel($id_sesion);

        // $titulo = array('Sección', 'Nombre', 'Fecha', 'Sesión', 'Obligatorio Acum' , 'General Acum', 'Sesión Obligatorio', 'Sesión General', 'Acum Target', 'Sesión Target', '% Oblig Cump Acum', '% Oblig Cump Sesión', '% General Cump Acum', '% General Cump Sesión',	'Color Oblig Acum', 'Color Oblig Sesión', 'Nivel cumplimiento');
        $titulo = array('SECCION','#',  'Nombre', 'Fecha', 'Sesion', 'Obligatorio Acum' , 'General Acum', 'Sesión Obligatorio', 'Sesión General', 'Acum  Target', 'Sesión Target', '% Oblig Cump Acum', '% Oblig Cump Sesión', '% General Cump Acum', '% General Cump Sesión',	'Color Oblig Acum', 'Color Oblig Sesión', 'Nivel cumplimiento');
        $columnas = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'i', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z');

        $fichero = new PhpExcel();
        $fichero->setActiveSheetIndex(0);
        $fichero->getActiveSheet()->setTitle("Code");

        // $fichero->getActiveSheet()->setCellValue('A1', 'Hola');

        $cont_titulo = 0;
        $cont_columnas = 0;
        foreach($titulo as $elemento)
        {
            $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].'1', $titulo[$cont_titulo++]);
        }

        $fila = 2;
        $cont = 1;
        for($i=0; $i < count($matriz); $i++)
        {
            $cont_columnas = 0;
            $j = 0;
            foreach($matriz[$i] as $elemento)
            {
                if($j == 1)
                {
                    $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $cont);

                }
                $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $elemento);
                $j++;
            }
            $fila++;
            $cont++;
        }
        // $fichero->setActiveSheetIndex(0);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. 'Code_'.date('dmY').'.xlsx"');
        header('Cache-Control: max-age=0');
        $fichero_almacenado =PHPExcel_IOFactory::createWriter($fichero, 'Excel2007');

        ob_end_clean();
        $fichero_almacenado->save('php://output');

    })->setName('excel_code');

    $app->post('/excel_video', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
            }
        }
        $id_sesion = $request->getParsedBody()['id_sesion_1'];
        $code = new Code();

        // $matriz = $code->tabla_code($id_sesion);
        $matriz = $code->tabla_video_excel($id_sesion);

        // $titulo = array('Sección', 'Nombre', 'Fecha', 'Sesión', 'Obligatorio Acum' , 'General Acum', 'Sesión Obligatorio', 'Sesión General', 'Acum Target', 'Sesión Target', '% Oblig Cump Acum', '% Oblig Cump Sesión', '% General Cump Acum', '% General Cump Sesión',	'Color Oblig Acum', 'Color Oblig Sesión', 'Nivel cumplimiento');
        $titulo = array('SECCION','#',  'Nombre', 'Fecha', 'Sesion', 'Obligatorio Acum' , 'General Acum', 'Sesión Obligatorio', 'Sesión General', 'Acum  Target', 'Sesión Target', '% Oblig Cump Acum', '% Oblig Cump Sesión', '% General Cump Acum', '% General Cump Sesión',	'Color Oblig Acum', 'Color Oblig Sesión', 'Nivel cumplimiento');
        $columnas = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'i', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z');

        $fichero = new PhpExcel();
        $fichero->setActiveSheetIndex(0);
        $fichero->getActiveSheet()->setTitle("Videos");

        // $fichero->getActiveSheet()->setCellValue('A1', 'Hola');

        $cont_titulo = 0;
        $cont_columnas = 0;
        foreach($titulo as $elemento)
        {
            $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].'1', $titulo[$cont_titulo++]);
        }

        $fila = 2;
        $cont = 1;
        for($i=0; $i < count($matriz); $i++)
        {
            $cont_columnas = 0;
            $j = 0;
            foreach($matriz[$i] as $elemento)
            {
                if($j == 1)
                {
                    $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $cont);

                }
                $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $elemento);
                $j++;
            }
            $fila++;
            $cont++;
        }
        $fichero->setActiveSheetIndex(0);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. 'Video_'.date('dmY').'.xlsx"');
        header('Cache-Control: max-age=0');
        $fichero_almacenado = PHPExcel_IOFactory::createWriter($fichero, 'Excel2007');

        ob_end_clean();
        $fichero_almacenado->save('php://output');

    })->setName('excel_video');

    $app->post('/excel_pregunta', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
            }
        }
        $id_sesion = $request->getParsedBody()['id_sesion_1'];
        $code = new Code();

        // $matriz = $code->tabla_code($id_sesion);
        $matriz = $code->tabla_pregunta_excel($id_sesion);

        // $titulo = array('Sección', 'Nombre', 'Fecha', 'Sesión', 'Obligatorio Acum' , 'General Acum', 'Sesión Obligatorio', 'Sesión General', 'Acum Target', 'Sesión Target', '% Oblig Cump Acum', '% Oblig Cump Sesión', '% General Cump Acum', '% General Cump Sesión',	'Color Oblig Acum', 'Color Oblig Sesión', 'Nivel cumplimiento');
        $titulo = array('SECCION','#',  'Nombre', 'Fecha', 'Sesion', 'Obligatorio Acum' , 'General Acum', 'Sesión Obligatorio', 'Sesión General', 'Acum  Target', 'Sesión Target', '% Oblig Cump Acum', '% Oblig Cump Sesión', '% General Cump Acum', '% General Cump Sesión',	'Color Oblig Acum', 'Color Oblig Sesión', 'Nivel cumplimiento');
        $columnas = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'i', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z');

        $fichero = new PhpExcel();
        $fichero->setActiveSheetIndex(0);
        $fichero->getActiveSheet()->setTitle("Preguntas");

        // $fichero->getActiveSheet()->setCellValue('A1', 'Hola');

        $cont_titulo = 0;
        $cont_columnas = 0;
        foreach($titulo as $elemento)
        {
            $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].'1', $titulo[$cont_titulo++]);
        }

        $fila = 2;
        $cont = 1;
        for($i=0; $i < count($matriz); $i++)
        {
            $cont_columnas = 0;
            $j = 0;
            foreach($matriz[$i] as $elemento)
            {
                if($j == 1)
                {
                    $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $cont);

                }
                $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $elemento);
                $j++;
            }
            $fila++;
            $cont++;
        }
        $fichero->setActiveSheetIndex(0);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. 'Preguntas_'.date('dmY').'.xlsx"');
        header('Cache-Control: max-age=0');
        $fichero_almacenado = PHPExcel_IOFactory::createWriter($fichero, 'Excel2007');

        ob_end_clean();
        $fichero_almacenado->save('php://output');
        exit;

    })->setName('excel_pregunta');

    $app->post('/excel_proyecto', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_proyectos'));
            }
        }
        $id_sesion = $request->getParsedBody()['id_sesion_1'];
        $id_proyecto = $request->getParsedBody()['id_proyecto_1'];
        $code = new Code();

        $matriz = $code->tabla_proyectos($id_sesion, $id_proyecto);

        $titulo = array('SECCION','#',  'Nombre', 'Fecha', 'Sesion');
        $columnas = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'i', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z');
        $matriz_aux = $code->tabla_proyectos_ini($id_sesion, $id_proyecto , 0, 1);
        $num_rubricas = count($matriz_aux[0])-8;

        for($i=1; $i <= $num_rubricas; $i++)
        {
            $titulo[] = 'Rubrica '.$i;
        }

        $titulo[] = 'Sesión Target';
        $titulo[] = '% Cump Sesión';
        $titulo[] = 'Color Sesión';
        $titulo[] = 'Nivel cumplimiento';
        $fichero = new PhpExcel();
        $fichero->setActiveSheetIndex(0);
        $fichero->getActiveSheet()->setTitle("Proyectos");

        // $fichero->getActiveSheet()->setCellValue('A1', 'Hola');

        $cont_titulo = 0;
        $cont_columnas = 0;
        foreach($titulo as $elemento)
        {
            $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].'1', $titulo[$cont_titulo++]);
        }

        $fila = 2;
        $cont = 0;
        $seccion = $matriz[0]["SECCION"];

        for($i=0; $i < count($matriz); $i++)
        {
            if($matriz[$i]["SECCION"] != $seccion) {
                $cont = 1;
                $seccion = $matriz[$i]["SECCION"];
            }
            else {$cont++;}
            $cont_columnas = 0;
            $j = 0;
            foreach($matriz[$i] as $elemento)
            {
                if($j == 1)
                {
                    $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $cont);

                }
                $fichero->getActiveSheet()->setCellValue($columnas[$cont_columnas++].''.$fila, $elemento);
                $j++;
            }
            $fila++;
        }
        $fichero->setActiveSheetIndex(0);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. 'Proyectos_'.date('dmY').'.xlsx"');
        header('Cache-Control: max-age=0');
        $fichero_almacenado = PHPExcel_IOFactory::createWriter($fichero, 'Excel2007');

        ob_end_clean();
        $fichero_almacenado->save('php://output');

    })->setName('excel_proyecto');

    $app->get('/carga_edpuzzle_videos', function($request, $response, $args)
    {
        $error = -1;

        return $this->view->render($response, 'carga_edpuzzle_videos.html', ['error'=> $error]);
    })->setName('carga_edpuzzle_videos');

    $app->get('/carga_edpuzzle_pregunta', function($request, $response, $args)
    {
        $error = -1;

        return $this->view->render($response, 'carga_edpuzzle_preguntas.html', ['error'=> $error]);
    })->setName('carga_edpuzzle_pregunta');

    $app->get('/carga_proyectos', function($request, $response, $args)
    {
        $error = -1;

        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }

        return $this->view->render($response, 'carga_proyectos.html', ['error'=> $error]);
    })->setName('carga_proyectos');

    $app->post('/busqueda_sesiones_video', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
            }
        }

        $id_sesion = $request->getParsedBody()['id_sesion'];
        $this->session->id_sesion = explode ('-', $id_sesion)[1];
        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));

    })->setName('busqueda_sesiones_video');

    $app->post('/busqueda_sesiones_pregunta', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
            }
        }

        $id_sesion = $request->getParsedBody()['id_sesion'];
        $this->session->id_sesion = explode ('-', $id_sesion)[1];
        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));

    })->setName('busqueda_sesiones_pregunta');

    $app->post('/busqueda_sesiones_proyecto', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_proyectos'));
            }
        }

        $id_sesion = $request->getParsedBody()['id_sesion'];
        $this->session->id_sesion = explode ('-', $id_sesion)[1];
        return $response->withRedirect($this->router->pathFor('reporte_proyectos'));

    })->setName('busqueda_sesiones_proyecto');

    $app->get('/reporte_edpuzzle_videos', function($request, $response, $args)
    {
        $descarga = 0;
        $code = new Code();
        $sesiones =null;
        $matriz = array();
        $index = -1;
        $cantidad = 0;
        $inicio = 0;
        $fin = 10;
        $pagina = 1;
        $error = -1;
        $pais_default = null;
        $escuela_default = null;
        $index_curso = -1;
        $index_seccion = -1;
        $secciones = null;
        $cursos = null;

        if ($this->session->exists('id_pais_default') && $this->session->exists('id_escuela_default'))
        {
            $lista_paises = $code->ver_paises();
            $pais_default = $lista_paises[$this->session->id_pais_default];

            $lista_escuelas = $code->ver_escuelas($this->session->id_pais_default);
            $escuela_default = $lista_escuelas[$this->session->id_escuela_default];
            $id_escuela = $this->session->id_escuela_default;
            // $sesiones = $code->fecha_sesiones_escuela($id_escuela);
            $cursos = $code->ver_cursos_escuela($id_escuela);

            if ($this->session->exists('id_curso'))
            {
                $index_curso = $this->session->id_curso;
                $secciones = $code->ver_secciones_escuela($this->session->id_curso, $id_escuela);

                if ($this->session->exists('id_seccion')) 
                {
                    $index_seccion = $this->session->id_seccion;
                   
                    $sesiones = $code->fecha_sesiones_escuela_video($index_seccion, $index_curso);
                    // echo "datos</br></br>";
                    // var_dump($sesiones);

                    // exit;
                    foreach ($sesiones as $id=>$fecha) {

                        $matriz_aux = $code->tabla_video($id);
                        $matriz = $code->tabla_video_ini($id, $inicio, $fin);
                        if (count($matriz) == 0) {
                            unset($sesiones[$id]);
                        }
                    }
                    $matriz = array();
                    // var_dump($sesiones);
                    // exit;

                    $this->session->delete('id_seccion');
                }
                $this->session->delete('id_curso');
            }
        }

        if($this->session->exists('id_sesion'))
        {
            if($this->session->exists('pagina'))
            {
                $pagina = $this->session->pagina;
                $inicio = 10 * ($this->session->pagina - 1);
                // $fin = $inicio +10;
                $fin = 10;
                $this->session->delete('pagina');
            }

            $index = $this->session->id_sesion;
            $matriz_aux = $code->tabla_video($this->session->id_sesion);
            $matriz = $code->tabla_video_ini($this->session->id_sesion, $inicio, $fin);
            $cantidad = ceil(count($matriz_aux)/10);
            $this->session->delete('id_sesion');
            if(count($matriz) == 0)
            {
                $error = 1;
            }
        }

        if($this->session->exists('descarga'))
        {
            $descarga = $this->sesion->descarga;
            $this->session->delete('descarga');
        }

        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }

        // return $this->view->render($response, 'reporte_edpuzzle_videos.html', ['pagina'=>$pagina, 'cantidad'=>$cantidad ,'descarga' => $descarga, 'index'=>$index, 'sesiones'=>$sesiones, 'matriz'=>$matriz]);
        return $this->view->render($response, 'reporte_edpuzzle_videos.html', ['secciones' => $secciones, 'cursos' => $cursos, 'index_seccion' => $index_seccion, 'index_curso' => $index_curso, 'pais_default' => $pais_default, 'escuela_default' => $escuela_default,'error'=>$error, 'pagina'=>$pagina, 'cantidad'=>$cantidad ,'descarga' => $descarga, 'index'=>$index, 'sesiones'=>$sesiones, 'matriz'=>$matriz]);
    })->setName('reporte_edpuzzle_videos');

    $app->get('/reporte_edpuzzle_pregunta', function($request, $response, $args)
    {
        $descarga = 0;
        $code = new Code();
        $sesiones = null;
        $matriz = array();
        $index = -1;
        $cantidad = 0;
        $inicio = 0;
        $fin = 10;
        $pagina = 1;
        $error = -1;
        $pais_default = null;
        $escuela_default = null;
        $index_curso = -1;
        $index_seccion = -1;
        $secciones = null;
        $cursos = null;

        if ($this->session->exists('id_pais_default') && $this->session->exists('id_escuela_default'))
        {
            $lista_paises = $code->ver_paises();
            $pais_default = $lista_paises[$this->session->id_pais_default];

            $lista_escuelas = $code->ver_escuelas($this->session->id_pais_default);
            $escuela_default = $lista_escuelas[$this->session->id_escuela_default];
            $id_escuela = $this->session->id_escuela_default;
            // $sesiones = $code->fecha_sesiones_escuela($id_escuela);

            $cursos = $code->ver_cursos_escuela($id_escuela);

            if ($this->session->exists('id_curso'))
            {
                $index_curso = $this->session->id_curso;
                $secciones = $code->ver_secciones_escuela($this->session->id_curso, $id_escuela);

                if ($this->session->exists('id_seccion')) 
                {
                    $index_seccion = $this->session->id_seccion;
                   
                    $sesiones = $code->fecha_sesiones_escuela_pregunta($index_seccion, $index_curso);
                   
                    //---------------------------------------------------
                    foreach ($sesiones as $id=>$fecha) {

                        $matriz_aux = $code->tabla_pregunta($id);
                        $matriz = $code->tabla_pregunta_ini($id, $inicio, $fin);
                        if (count($matriz) == 0) {
                            unset($sesiones[$id]);
                        }
                    }
                    $matriz = array();
                    //---------------------------------------------------
                    // var_dump($sesiones);
                    // exit;

                    $this->session->delete('id_seccion');
                }
                $this->session->delete('id_curso');
            }
        }

        if($this->session->exists('id_sesion'))
        {
            if($this->session->exists('pagina'))
            {
                $pagina = $this->session->pagina;
                $inicio = 10 * ($this->session->pagina - 1);
                // $fin = $inicio +10;
                $fin = 10;
                $this->session->delete('pagina');
            }

            $index = $this->session->id_sesion;
            $matriz_aux = $code->tabla_pregunta($this->session->id_sesion);
            $matriz = $code->tabla_pregunta_ini($this->session->id_sesion, $inicio, $fin);
            $cantidad = ceil(count($matriz_aux)/10);
            $this->session->delete('id_sesion');
            // exit;
            if(count($matriz) == 0)
            {
                $error = 1;
            }
        }

        if($this->session->exists('descarga'))
        {
            $descarga = $this->sesion->descarga;
            $this->session->delete('descarga');
        }

        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }

        return $this->view->render($response, 'reporte_edpuzzle_preguntas.html', ['secciones' => $secciones, 'cursos' => $cursos, 'index_seccion' => $index_seccion, 'index_curso' => $index_curso, 'pais_default' => $pais_default, 'escuela_default' => $escuela_default, 'error'=>$error, 'pagina'=>$pagina, 'cantidad'=>$cantidad ,'descarga' => $descarga, 'index'=>$index, 'sesiones'=>$sesiones, 'matriz'=>$matriz]);
    })->setName('reporte_edpuzzle_pregunta');

    $app->get('/reporte_proyectos', function($request, $response, $args)
    {
        $descarga = 0;
        $code = new Code();
        $sesiones = $code->fecha_sesiones();
        $matriz = array();
        $index = -1;
        $cantidad = 0;
        $inicio = 0;
        $fin = 10;
        $pagina = 1;
        $error = -1;
        $proyectos = null;
        $index_proyecto = -1;
        $num_rubricas= -1;
        $pais_default = null;
        $escuela_default = null;


        if ($this->session->exists('id_pais_default') && $this->session->exists('id_escuela_default'))
        {
            $lista_paises = $code->ver_paises();
            $pais_default = $lista_paises[$this->session->id_pais_default];

            $lista_escuelas = $code->ver_escuelas($this->session->id_pais_default);
            $escuela_default = $lista_escuelas[$this->session->id_escuela_default];
            $id_escuela = $this->session->id_escuela_default;
            $sesiones = $code->fecha_sesiones_escuela($id_escuela);
        }

        if($this->session->exists('id_sesion'))
        {
            if($this->session->exists('pagina'))
            {
                $pagina = $this->session->pagina;
                $inicio = 10 * ($this->session->pagina - 1);
                // $fin = $inicio +10;
                $fin = 10;

                $this->session->delete('pagina');
            }

            $index = $this->session->id_sesion;
            if(!$this->session->exists('id_proyecto'))
            {
                $proyectos = $code->ver_proyectos($index);
                if(count($proyectos) == 0) { $proyectos=null; }
                $this->session->delete('id_sesion');
            }
            else
            {
                $proyectos = $code->ver_proyectos($index);
                $id_sesion = $this->session->id_sesion;
                $id_proyecto = $this->session->id_proyecto;

                $matriz_aux = $code->tabla_proyectos($id_sesion, $id_proyecto);
                $matriz = $code->tabla_proyectos_ini($this->session->id_sesion, $id_proyecto , $inicio, $fin);
                $cantidad = ceil(count($matriz_aux)/10);
                $index_proyecto = $this->session->id_proyecto;
                $num_rubricas = count($matriz[0])-8;
                $this->session->delete('id_sesion');
                $this->session->delete('id_proyecto');

                // print("</br>tam matriz: ".count($matriz));
                // print("</br>tam matriz_aux: ".count($matriz_aux));
                // exit;
            }

            if(count($matriz) == 0 && $proyectos == null)
            {
                $error = 1;
            }
        }

        if($this->session->exists('descarga'))
        {
            $descarga = $this->sesion->descarga;
            $this->session->delete('descarga');
        }

        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }


        return $this->view->render($response, 'reporte_proyectos.html', ['pais_default' => $pais_default, 'escuela_default' => $escuela_default,'num_rubricas'=>$num_rubricas, 'index_proyecto'=>$index_proyecto, 'proyectos'=>$proyectos, 'error'=>$error, 'pagina'=>$pagina, 'cantidad'=>$cantidad ,'descarga' => $descarga, 'index'=>$index, 'sesiones'=>$sesiones, 'matriz'=>$matriz]);
    })->setName('reporte_proyectos');

    $app->post('/busqueda_info_proyectos', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_proyectos'));
            }
        }

        $dato = $request->getParsedBody()['id_proyectos'];
        $valores = explode("-", $dato);
        // print_r($valores);

        $this->session->id_proyecto = $valores[1];
        $this->session->id_sesion = $valores[2];

        return $response->withRedirect($this->router->pathFor('reporte_proyectos'));
    })->setName('busqueda_info_proyectos');

    $app->post('/data_carga_videos', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('carga_edpuzzle_videos'));
            }
        }

        $id_seccion = $request->getParsedBody()['id-seccion'];
        $hoja_seccion = $request->getParsedBody()['fichero-seccion-hoja'];

        // se abre la conexion con Drive
        $google = new Google();

        $rango = $google->ver_rango($id_seccion, $hoja_seccion);

        if($rango == NULL || $rango == -1)
        {
            $this->session->error = 1;
            return $response->withRedirect($this->router->pathFor('carga_edpuzzle_videos'));
        }
        ;
        $id_sesion = -1;
        // se almacena la data en la bd
        $code = new Code();
        try
        {
            $id_sesion = $code->cargar_video($id_seccion, $hoja_seccion, $rango);
        }
        catch(Exception $e)
        {
            $this->session->error = 2;
            return $response->withRedirect($this->router->pathFor('carga_edpuzzle_videos'));
        }

        return $response->withRedirect('tabla_video/'.$id_sesion);
    })->setName('data_carga_videos');

    $app->get('/tabla_video/{id}', function($request, $response, $args)
    {
        echo "tabla video!!! ".$args['id'];

        // $code = new Code();
        // $matriz = $code->tabla_code($args['id']);

        // return $this->view->render($response, 'tabla_code.html', ['matriz'=>$matriz]);
        $this->session->error = 0;
        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
    })->setName('tabla_video');

    $app->post('/data_carga_pregunta', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('carga_edpuzzle_pregunta'));
            }
        }

        $id_seccion = $request->getParsedBody()['id-seccion'];
        $hoja_seccion = $request->getParsedBody()['fichero-seccion-hoja'];

        // se abre la conexion con Drive
        $google = new Google();

        $rango = $google->ver_rango($id_seccion, $hoja_seccion);


        if($rango == NULL || $rango == -1)
        {
            $this->session->error = 1;
            return $response->withRedirect($this->router->pathFor('carga_edpuzzle_pregunta'));
        }
        ;
        $id_sesion = -1;
        // se almacena la data en la bd
        $code = new Code();
        try
        {
            $id_sesion = $code->cargar_pregunta($id_seccion, $hoja_seccion, $rango);
        }
        catch(Exception $e)
        {
            $this->session->error = 2;
            return $response->withRedirect($this->router->pathFor('carga_edpuzzle_pregunta'));
        }
        return $response->withRedirect('tabla_pregunta/'.$id_sesion);
    })->setName('data_carga_pregunta');

    $app->post('/data_carga_proyecto', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('carga_proyectos'));
            }
        }

        $id = $request->getParsedBody()['id-seccion'];
        $hoja = $request->getParsedBody()['fichero-seccion-hoja'];

        // se abre la conexion con Drive
        $google = new Google();

        $rango = $google->ver_rango_proyecto($id, $hoja);
        
        $valores = explode(";", $rango);
        $rango_target = $google->ver_sesion_target($id, $hoja);
        $targets = explode(";", $rango_target);
        $sesiones_target = [];
        foreach($targets as $target)
        {
            $sesiones_target[] = $google->suma_sesion_target($id, $hoja, $target);
        }

        if($rango == NULL || $rango == -1)
        {
            $this->session->error = 1;
            return $response->withRedirect($this->router->pathFor('carga_proyectos'));
        }
        ;
        $id_sesion = -1;
        // se almacena la data en la bd
        $code = new Code();
        $respuesta = 0;
        try
        {
            for($i=0; $i < count($valores); $i++)
            {
                $respuesta = $code->carga_proyecto($id, $hoja, $valores[$i], $sesiones_target[$i]);
                if($respuesta  == -10) { break; }
            }

            if($respuesta  == -10)
            {
                $this->session->error = 10;
                return $response->withRedirect($this->router->pathFor('carga_proyectos'));
            }

            // $id_sesion = $code->cargar_proyecto($id, $hoja, $rango);
        }
        catch(Exception $e)
        {
            $this->session->error = 2;
            return $response->withRedirect($this->router->pathFor('carga_proyectos'));
        }
        return $response->withRedirect('tabla_proyecto/'.$id_sesion);
    })->setName('data_carga_proyecto');

    $app->get('/tabla_pregunta/{id}', function($request, $response, $args)
    {
        // echo "tabla pregunta!!! ".$args['id'];

        // $code = new Code();
        // $matriz = $code->tabla_code($args['id']);

        // return $this->view->render($response, 'tabla_code.html', ['matriz'=>$matriz]);
        $this->session->error = 0;
        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
    })->setName('tabla_pregunta');

    $app->get('/tabla_proyecto/{id}', function($request, $response, $args)
    {
        // echo "tabla pregunta!!! ".$args['id'];

        // $code = new Code();
        // $matriz = $code->tabla_code($args['id']);

        // return $this->view->render($response, 'tabla_code.html', ['matriz'=>$matriz]);
        $this->session->error = 0;
        return $response->withRedirect($this->router->pathFor('reporte_proyectos'));
    })->setName('tabla_proyecto');

    $app->get('/informe_avance', function($request, $response, $args)
    {
        $index_seccion = -1;
        $index_alumno = -1;
        $index_curso = -1;
        $index_proyecto = -1;
        $code = new Code();
        // $cursos = $code->ver_cursos();
        $cursos = null;
        $alumnos = null;
        $secciones = null;

        $boleta_code = null;
        $info_boleta = null;
        $info_pregunta = null;
        $info_rubrica = null;
        $info_video = null;
        $proyectos = null;
        $pais_default = null;
        $escuela_default = null;
        

        if ($this->session->exists('id_pais_default') && $this->session->exists('id_escuela_default'))
        {
            $lista_paises = $code->ver_paises();
            $pais_default = $lista_paises[$this->session->id_pais_default];

            $lista_escuelas = $code->ver_escuelas($this->session->id_pais_default);
            $escuela_default = $lista_escuelas[$this->session->id_escuela_default];
            
            $id_pais = $this->session->id_pais_default;
            $id_escuela = $this->session->id_escuela_default;
            $cursos = $code->ver_cursos_escuela($id_escuela);
            // var_dump($cursos);
            // $cursos = $code->ver_cursos();

            if($this->session->exists("id_curso"))
            {
                $index_curso = $this->session->id_curso;
                $secciones = $code->ver_secciones_escuela_code($this->session->id_curso, $id_escuela);
                // $alumnos = $code->ver_alumnos($this->session->id_seccion);

                if($this->session->exists('id_seccion'))
                {
                    $index_seccion = $this->session->id_seccion;
                    $alumnos = $code->ver_alumnos($this->session->id_curso, $this->session->id_seccion);

                    if($this->session->exists('id_alumno'))
                    {
                        $index_alumno = $this->session->id_alumno;
                        $boleta_code = $code->boleta_code($this->session->id_curso, $this->session->id_seccion, $this->session->id_alumno);
                        // $info_boleta = asignar_valores($boleta_code);

                        $boleta_video = $code->boleta_video($this->session->id_curso, $this->session->id_seccion, $this->session->id_alumno);
                        // $info_video = asignar_valores($boleta_video);

                        $boleta_pregunta = $code->boleta_pregunta($this->session->id_curso, $this->session->id_seccion, $this->session->id_alumno);
                        // $info_pregunta = asignar_valores($boleta_pregunta);

                        $proyectos = $code->ver_proyectos_alumno($this->session->id_alumno, $this->session->id_seccion, $this->session->id_curso);

                        if($this->session->exists('id_proyecto'))
                        {
                            $index_proyecto = $this->session->id_proyecto;
                            // $info_rubrica = $code->boleta_rubrica($index_curso, $index_seccion, $index_alumno, $index_proyecto);
                            $this->session->delete('id_proyecto');
                        }


                        $this->session->delete('id_alumno');
                    }

                    $this->session->delete('id_seccion');
                }
                $this->session->delete('id_curso');
            }
        }

        // return $this->view->render($response, 'generar_boletas.html', ['pais_default' => $pais_default, 'escuela_default' => $escuela_default, 'index_proyecto'=>$index_proyecto, 'proyectos'=>$proyectos, 'info_rubrica'=>$info_rubrica, 'info_pregunta'=>$info_pregunta, 'info_video'=>$info_video, 'info_boleta'=>$info_boleta, 'index_curso'=>$index_curso, 'cursos'=>$cursos, 'index_alumno'=>$index_alumno, 'alumnos'=>$alumnos, 'index_seccion'=>$index_seccion, 'secciones'=>$secciones]);
        return $this->view->render($response, 'generar_boletas.html', ['pais_default' => $pais_default, 'escuela_default' => $escuela_default, 'index_proyecto'=>$index_proyecto, 'proyectos'=>$proyectos, 'index_curso'=>$index_curso, 'cursos'=>$cursos, 'index_alumno'=>$index_alumno, 'alumnos'=>$alumnos, 'index_seccion'=>$index_seccion, 'secciones'=>$secciones]);
    })->setName('generar_boletas');

    $app->post('/siguiente_avance', function($request, $response, $args) 
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('generar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['input_siguiente'];
        $valores = explode("-", $dato);
        //{{index_curso}}-{{index_seccion}}-{{index_alumno}}-{{ rango_fin }}
        $id_curso = $valores[0];
        $id_seccion = $valores[1];
        $id_alumno = $valores[2];
        $rango_inicio = (int) $valores[3];
        $rango_fin = (int) $valores[4];

        $rango_fin += 1;
        $this->session->rango_fin = $rango_fin;
        return $response->withRedirect('avance_alumno/'.$id_curso.'/'.$id_seccion.'/'.$id_alumno.'/0');
    })->setName('siguiente_avance');

    $app->post('/anterior_avance', function($request, $response, $args) 
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('generar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['input_anterior'];
        $valores = explode("-", $dato);
        //{{index_curso}}-{{index_seccion}}-{{index_alumno}}-{{ rango_fin }}
        $id_curso = $valores[0];
        $id_seccion = $valores[1];
        $id_alumno = $valores[2];
        $rango_inicio = (int) $valores[3];
        $rango_fin = (int) $valores[4];

        if ($rango_fin > 0) 
            $rango_fin -= 1;
        else
            $rango_fin = 0;

        $this->session->rango_fin = $rango_fin;
        
        return $response->withRedirect('avance_alumno/'.$id_curso.'/'.$id_seccion.'/'.$id_alumno.'/0');
    })->setName('anterior_avance');

    $app->get('/avance_alumno/{id_curso}/{id_seccion}/{id_alumno}/{id_proyecto}', function($request, $response, $args)
    {
        $error = -1;
        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }
        
        $id_curso = $args["id_curso"];
        $id_seccion =  $args["id_seccion"];
        $id_alumno =  $args["id_alumno"];
        $id_proyecto =  $args["id_proyecto"];
        
        //--------------------------

        $index_proyecto = $id_proyecto;
        $index_seccion = $id_seccion;
        $index_curso = $id_curso;
        $index_alumno = $id_alumno;
        //--------------------------

        $code = new Code();
        $proyectos = $code->ver_proyectos_alumno($id_alumno, $id_seccion, $id_curso);
        $alumno = $code->ver_alumno($id_curso, $id_seccion, $id_alumno)[0];
        $seccion = $code->ver_seccion($id_curso, $id_seccion)[0];

        $rango_inicio = 5;
        $rango_fin = 0;

        if ($this->session->exists('rango_fin')) 
        {
            $rango_fin = (int) $this->session->rango_fin;
            
            $this->session->delete('rango_fin');
        }

        // $boleta_code = $code->boleta_code($id_curso, $id_seccion, $id_alumno);
        $boleta_code = $code->boleta_code_rango($id_curso, $id_seccion, $id_alumno, $rango_fin, $rango_inicio);
        $info_boleta = asignar_valores_1($boleta_code);
        // var_dump($info_boleta);
        // exit;

        // $boleta_video = $code->boleta_video($id_curso, $id_seccion, $id_alumno);
        $boleta_video = $code->boleta_video_rango($id_curso, $id_seccion, $id_alumno, $rango_fin, $rango_inicio);
        $info_video = asignar_valores_1($boleta_video);
        //var_dump($info_video);
        
        // $boleta_pregunta = $code->boleta_pregunta($id_curso, $id_seccion, $id_alumno);
        $boleta_pregunta = $code->boleta_pregunta_rango($id_curso, $id_seccion, $id_alumno, $rango_fin, $rango_inicio);
        // $info_pregunta = asignar_valores($boleta_pregunta);
        $info_pregunta = asignar_valores_1($boleta_pregunta);
        // var_dump($info_pregunta);
        // exit;
        

        $info_rubrica = NULL;
        if ($this->session->exists('nombre_dni') && $this->session->nombre_dni == $alumno["DNI"]) 
        {
            $info_rubrica = [];
            $info_rubrica["SESION"] = $this->session->datos_rubricas_sesion;
            $info_rubrica["CALIFICACION"] = $this->session->datos_rubricas_calificacion;
            $info_rubrica["OBJETIVO"] = $this->session->datos_rubricas_objetivo;
            // $this->session->delete('nombre_dni');
            // exit;
        } else 
        {
            $info_rubrica = $code->boleta_rubrica_nuevo($id_curso, $id_seccion, $id_alumno);
            $this->session->nombre_dni = $alumno["DNI"];
            $this->session->datos_rubricas_sesion = $info_rubrica["SESION"];
            $this->session->datos_rubricas_calificacion = $info_rubrica["CALIFICACION"];
            $this->session->datos_rubricas_objetivo = $info_rubrica["OBJETIVO"];
        }
        
        $google = new Google();
        $rango = $google->cargar_code('1MBTFsJFbBhorkUGGPNE4Bsze4YdvzB0rFMprb1mhXLk', $seccion["NOMBRE"])[0];
        $code = new Code();
        $info = $code->ver_comentario_individual($seccion, $alumno['DNI'], '1MBTFsJFbBhorkUGGPNE4Bsze4YdvzB0rFMprb1mhXLk', $seccion["NOMBRE"], $rango);

        $comentario = $info['COMENTARIO'];
        $apoyo = $info['APOYO'];

        //----------------------------------------------
        $rango = $google->cargar_code('15tqrhRwaNOtfrpOh3oTf12o8cvnm-o0rWVcqkYsr5ug', 'noticias')[0];
        $noticias = $code->ver_noticias('15tqrhRwaNOtfrpOh3oTf12o8cvnm-o0rWVcqkYsr5ug', 'noticias', $rango);
        $noti = array();
        for ($i=0; $i < count($noticias); $i++)
        {
            $noti[$i] = array("IMG"=>$noticias[$i][1], "DIR"=>$noticias[$i][2]);
        }
        //----------------------------------------------
        $nombre_seccion = $code->ver_seccion($id_curso, $id_seccion)[0]["NOMBRE"];

        $rango = $google->cargar_code('1BNBdMHe6r78-3oIR7wqzrF4st5nXPTZD8Xbfo8XUZ3Q', $nombre_seccion)[0];
        $info_proyectos = $code->ver_info_proyectos('1BNBdMHe6r78-3oIR7wqzrF4st5nXPTZD8Xbfo8XUZ3Q', $nombre_seccion, $rango);
        $cont = 0;

        for ($i = 0; $i < count($info_proyectos[3]); $i++)
        {   
            $valores = explode("-", $info_proyectos[3][$i]);
            if ($valores[0] == $alumno['DNI'])
            {
                $cont = $i;
                break;
            }
        }

        $info_proy = array();
        $row = 0;
        for ($i = 4; $i < 16; $i += 2)
        {
            $info_proy[$row] = array("IMG"=>$info_proyectos[$i][$cont], "DIR"=>$info_proyectos[$i+1][$cont]);
            $row += 1;
        }

        return $this->view->render($response, 'avance_alumno.html', ['rango_inicio' => $rango_inicio, 'rango_fin' => $rango_fin, 'info_proy' =>  $info_proy, 'index_curso' => $index_curso ,'index_seccion' => $index_seccion, 'index_alumno' => $index_alumno,'index_proyecto' => $index_proyecto, 'proyectos' => $proyectos, 'noticias'=> $noti,'apoyo'=> $apoyo, 'comentario'=> $comentario, 'info_rubrica'=>$info_rubrica, 'info_pregunta'=>$info_pregunta, 'info_video'=>$info_video, 'info_boleta'=>$info_boleta,'seccion'=> $seccion,'alumno' => $alumno, 'error'=> $error]);
    })->setName('avance_alumno');

    $app->post('/seleccion_de_proyecto', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('generar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['id_proyectos'];
        $valores = explode("-", $dato);
        $id_proyecto = $valores[1];
        $id_curso = $valores[2];
        $id_seccion = $valores[3];
        $id_alumno = $valores[4];

        return $response->withRedirect('avance_alumno/'.$id_curso.'/'.$id_seccion.'/'.$id_alumno.'/'.$id_proyecto);
    })->setName('seleccion_de_proyecto');

    $app->post('/busqueda_boleta_proyectos', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('generar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['id_proyectos'];
        $valores = explode("-", $dato);
        // $('#id_proyectos').val($(this).attr('id')+'-{{index_curso}}-{{index_seccion}}-{{index_alumno}}');
        $this->session->id_proyecto = $valores[1];
        $this->session->id_curso = $valores[2];
        $this->session->id_seccion = $valores[3];
        $this->session->id_alumno = $valores[4];

        return $response->withRedirect($this->router->pathFor('generar_boletas'));
    })->setName('busqueda_boleta_proyectos');

    function asignar_valores_1(&$info_valores)
    {
        $data["tam"] = count($info_valores);
        $data["OBLIGATORIO_ACUM"] = null;
        $data["GENERAL_ACUMULADO"] = null;
        $data["SESION"] = null;

        foreach($info_valores as $fila)
        {
            if($data["OBLIGATORIO_ACUM"] == null) { $data["OBLIGATORIO_ACUM"] = $fila[0]["OBLIGATORIO_ACUM"];}
            else{ $data["OBLIGATORIO_ACUM"] = $data["OBLIGATORIO_ACUM"].';'.$fila[0]["OBLIGATORIO_ACUM"]; }

            if($data["GENERAL_ACUMULADO"] == null) { $data["GENERAL_ACUMULADO"] = $fila[0]["GENERAL_ACUMULADO"];}
            else{ $data["GENERAL_ACUMULADO"] = $data["GENERAL_ACUMULADO"].';'.$fila[0]["GENERAL_ACUMULADO"]; }
        
            if($data["SESION"] == null) { $data["SESION"] = $fila[0]["SESION"];}
            else{ $data["SESION"] = $data["SESION"].';'.$fila[0]["SESION"]; }
        }

        return $data;
    }

    function asignar_valores(&$info_valores)
    {
        $data["tam"] = count($info_valores);
        $data["OBLIGATORIO_ACUM"] = null;
        $data["GENERAL_ACUMULADO"] = null;
        $data["ACUM_TARGET"] = null;
        $data["SESION_OBLIGATORIO"] = null;
        $data["SESION_GENERAL"] = null;
        $data["SESION_TARGET"] = null;

        foreach($info_valores as $fila)
        {
            if($data["OBLIGATORIO_ACUM"] == null) { $data["OBLIGATORIO_ACUM"] = $fila[0]["OBLIGATORIO_ACUM"];}
            else{ $data["OBLIGATORIO_ACUM"] = $data["OBLIGATORIO_ACUM"].';'.$fila[0]["OBLIGATORIO_ACUM"]; }

            if($data["GENERAL_ACUMULADO"] == null) { $data["GENERAL_ACUMULADO"] = $fila[0]["GENERAL_ACUMULADO"];}
            else{ $data["GENERAL_ACUMULADO"] = $data["GENERAL_ACUMULADO"].';'.$fila[0]["GENERAL_ACUMULADO"]; }

            if($data["ACUM_TARGET"] == null) { $data["ACUM_TARGET"] = $fila[0]["ACUM_TARGET"];}
            else{ $data["ACUM_TARGET"] = $data["ACUM_TARGET"].';'.$fila[0]["ACUM_TARGET"]; }

            if($data["SESION_OBLIGATORIO"] == null) { $data["SESION_OBLIGATORIO"] = $fila[0]["SESION_OBLIGATORIO"];}
            else{ $data["SESION_OBLIGATORIO"] = $data["SESION_OBLIGATORIO"].';'.$fila[0]["SESION_OBLIGATORIO"]; }

            if($data["SESION_GENERAL"] == null) { $data["SESION_GENERAL"] = $fila[0]["SESION_GENERAL"];}
            else{ $data["SESION_GENERAL"] = $data["SESION_GENERAL"].';'.$fila[0]["SESION_GENERAL"]; }

            if($data["SESION_TARGET"] == null) { $data["SESION_TARGET"] = $fila[0]["SESION_TARGET"];}
            else{ $data["SESION_TARGET"] = $data["SESION_TARGET"].';'.$fila[0]["SESION_TARGET"]; }
        }

        return $data;
    }

    $app->post('/busqueda_curso_envio', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('enviar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['id_curso'];
        $valores = explode("-", $dato);
        $this->session->id_curso = $valores[1];


        return $response->withRedirect($this->router->pathFor('enviar_boletas'));
    })->setName('busqueda_curso_envio');

    $app->post('/busqueda_seccion_envio', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('enviar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['id_seccion'];
        $valores = explode("-", $dato);

        $this->session->id_seccion = $valores[1];
        $this->session->id_curso = $valores[2];


        return $response->withRedirect($this->router->pathFor('enviar_boletas'));
    })->setName('busqueda_seccion_envio');
    // busqueda_tipo_envio
    $app->post('/busqueda_tipo_envio', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('enviar_boletas'));
            }
        }
        
        $dato = $request->getParsedBody()['id_info_envio'];
        $valores = explode("-", $dato);

        $this->session->tipo_envio = $valores[0];
        $this->session->id_curso = $valores[1];
        $this->session->id_seccion = $valores[2];
        
        return $response->withRedirect($this->router->pathFor('enviar_boletas'));
    })->setName('busqueda_tipo_envio');

    //enviar boletas a los representantes y profesores
    $app->get('/enviar_avance', function($request, $response, $args)
    {
        $index_curso = -1;
        $index_seccion = -1;
        $code = new Code();
        $cursos = $code->ver_cursos();
        $secciones = null;
        $tipos = null;
        $msg_enviado = null;

        if($this->session->exists('id_curso'))
        {
            $index_curso = $this->session->id_curso;
            $secciones = $code->ver_secciones($this->session->id_curso);

            if($this->session->exists('id_seccion'))
            {
                $index_seccion = $this->session->id_seccion;
                $tipos = 1;

                if ($this->session->exists('tipo_envio'))
                {
                    $google = new Google();
                    $id_curso = $this->session->id_curso;
                    $id_seccion = $this->session->id_seccion;
                    // Envio del correo a los representates 
                    if ($this->session->tipo_envio == 2)
                    {
						
                        $nombre_seccion = $code->ver_seccion($this->session->id_curso, $this->session->id_seccion)[0]["NOMBRE"]; 
                        $rango = $google->ver_rango('189Cbwb4NpEJu-HMqwKGbpVnTgWuDaT_94adM6kr46VU', $nombre_seccion);
                        $representates = $code->ver_representantes('189Cbwb4NpEJu-HMqwKGbpVnTgWuDaT_94adM6kr46VU', $nombre_seccion, $rango);
                        
						foreach ($representates as $info)
                        {
							$id_alumno = $code->ver_alumno_dni($id_curso, $id_seccion, $info[2])[0];
                            // $mail = "http://www.progracademy.org/prg-test/index.php/enviar_avance/".$id_curso."/".$id_seccion."/".$id_alumno["ID"]."/0";
                            $mail = ' <p>Estimados representantes en este enlace <a  href="http://www.progracademy.org/prg-test/index.php/avance_alumno/'.$id_curso.'/'.$id_seccion.'/'.$id_alumno["ID"].'/0"> http://www.progracademy.org/prg-test/index.php/avance_alumno/'.$id_curso.'/'.$id_seccion.'/'.$id_alumno["ID"].'/0 </a> encontrarán el informe de progreso de su representado '.$id_alumno["NOMBRE"].'. </p> ';
                            
                            //Titulo
                            $titulo = "Informe de Evaluación a la fecha";

                            //cabecera
                            $headers = "MIME-Version: 1.0\r\n"; 
                            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
                            //dirección del remitente 
                            $headers .= "From: progracademy.org\r\n";
                            //Enviamos el mensaje a tu_dirección_email 
                            //$msg_enviado =  'info[1] '.$info[1].' titulo '.$titulo.' mail '.$mail;	
							$bool = mail($info[1], $titulo, $mail, $headers);
                            if($bool){
                                $msg_enviado = "Mensajes enviados";
                            }else{
                                $msg_enviado = "Problemas al enviar los mensajes";
                                break;
                            }
                        }
                    }
                     // Envio del correo a la los profesores 
                    else if ($this->session->tipo_envio == 3)
                    {
                        $rango = $google->ver_rango('1hrI7CnX9smCN_NbNjMuXQm36FMawq3D1c6TqAzXQ11c', 'profesores');
                        $profesores = $code->ver_representantes('1hrI7CnX9smCN_NbNjMuXQm36FMawq3D1c6TqAzXQ11c', 'profesores', $rango);
                        $alumnos = $code->ver_alumnos($id_curso, $id_seccion);
                        
                        foreach ($profesores as $profesor) 
                        {
                            foreach ($alumnos as $id=>$nombre) 
                            {
                                // echo "id: ".$id.", nombre: ".$nombre."</br>";
                                // echo $profesor[1]."</br>";
                                // exit;
                                 //Titulo
                                $mail = ' <p>Estimados profesores en esta dirección <a  href="http://www.progracademy.org/prg-test/index.php/avance_alumno/'.$id_curso.'/'.$id_seccion.'/'.$id.'/0"> link </a> encontrarán el informe de progreso de su representado '.$nombre.'.</p> ';
                                
                                $titulo = "Informe de Evaluación a la fecha";
                                //cabecera
                                $headers = "MIME-Version: 1.0\r\n"; 
                                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
                                //dirección del remitente 
                                $headers .= "From: progracademy.org\r\n";
                                //Enviamos el mensaje a tu_dirección_email 
                                $bool = mail($profesor[1], $titulo, $mail, $headers);
                                if($bool){
                                    $msg_enviado = "Mensajes enviados";
                                }else{
                                    $msg_enviado = "Problemas al enviar los mensajes";
                                    break;
                                }
                            }
                        }

                        // exit;
                    }
                   
                    // exit;
                    $this->session->delete('tipo_envio');
                }
                $this->session->delete('id_seccion');
            }
            $this->session->delete('id_curso');
        }


        return $this->view->render($response, 'enviar_boletas.html', ['msg_enviado' => $msg_enviado, 'tipos'=>$tipos, 'secciones'=>$secciones, 'cursos'=>$cursos, 'index_curso'=>$index_curso, 'index_seccion'=>$index_seccion]);
    })->setName('enviar_boletas');

    function enviar_correo()
    {
        $para = 'ingblend3d@gmail.com';
        $titulo = 'Enviando email desde PHP';



        $mensaje = '';
        $file = fopen('boletas/boleta.html', "r");

        while(!feof($file)) { $mensaje = $mensaje.fgets($file);}
        fclose($file);

        print("-----------------</br>");
        print($mensaje);
        print("-----------------</br>");
        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $cabeceras .= 'From: Mi Nombre<yo@correo.com>';

        $enviado = mail($para, $titulo, $mensaje, $cabeceras);

       if ($enviado)
         echo 'Email enviado correctamente';
       else
         echo 'Error en el envío del email';

    }

    $app->post('/busqueda_curso_boleta', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('generar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['id_curso'];
        $valores = explode("-", $dato);
        $this->session->id_curso = $valores[1];


        return $response->withRedirect($this->router->pathFor('generar_boletas'));
    })->setName('busqueda_curso_boleta');

    $app->post('/busqueda_curso_code', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_code'));
            }
        }

        $dato = $request->getParsedBody()['id_curso'];
        $valores = explode("-", $dato);
        $this->session->id_curso = $valores[1];


        return $response->withRedirect($this->router->pathFor('reporte_code'));
    })->setName('busqueda_curso_code');

    $app->post('/busqueda_curso_video', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
            }
        }

        $dato = $request->getParsedBody()['id_curso'];
        $valores = explode("-", $dato);
        $this->session->id_curso = $valores[1];


        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
    })->setName('busqueda_curso_video');

    $app->post('/busqueda_curso_pregunta', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
            }
        }

        $dato = $request->getParsedBody()['id_curso'];
        $valores = explode("-", $dato);
        $this->session->id_curso = $valores[1];


        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
    })->setName('busqueda_curso_pregunta');

    $app->post('/busqueda_seccion_boleta', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('generar_boletas'));
            }
        }

        $dato = $request->getParsedBody()['id_seccion'];
        $valores = explode("-", $dato);

        $this->session->id_seccion = $valores[1];
        $this->session->id_curso = $valores[2];


        return $response->withRedirect($this->router->pathFor('generar_boletas'));
    })->setName('busqueda_seccion_boleta');

    $app->post('/busqueda_seccion_code', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_code'));
            }
        }

        $dato = $request->getParsedBody()['id_seccion'];
        $valores = explode("-", $dato);

        $this->session->id_seccion = $valores[1];
        $this->session->id_curso = $valores[2];


        return $response->withRedirect($this->router->pathFor('reporte_code'));
    })->setName('busqueda_seccion_code');

    $app->post('/busqueda_seccion_video', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
            }
        }

        $dato = $request->getParsedBody()['id_seccion'];
        $valores = explode("-", $dato);

        $this->session->id_seccion = $valores[1];
        $this->session->id_curso = $valores[2];


        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_videos'));
    })->setName('busqueda_seccion_video');

    $app->post('/busqueda_seccion_pregunta', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
            }
        }

        $dato = $request->getParsedBody()['id_seccion'];
        $valores = explode("-", $dato);

        $this->session->id_seccion = $valores[1];
        $this->session->id_curso = $valores[2];


        return $response->withRedirect($this->router->pathFor('reporte_edpuzzle_pregunta'));
    })->setName('busqueda_seccion_pregunta');

    $app->post('/busqueda_alumno_boleta', function($request, $response, $args)
    {

        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('generar_boletas'));
            }
        }
        $dato = $request->getParsedBody()['id_alumno'];
        $valores = explode("-", $dato);

        $this->session->id_alumno = $valores[1];
        $this->session->id_curso = $valores[2];
        $this->session->id_seccion = $valores[3];

        return $response->withRedirect($this->router->pathFor('generar_boletas'));
    })->setName('busqueda_alumno_boleta');

    $app->post('/carga_datos_config', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('conf_sistema'));
            }
        }

        $id_seccion = $request->getParsedBody()['id-seccion'];
        $hoja_seccion = $request->getParsedBody()['fichero-seccion-hoja'];

        // se abre la conexion con Drive
        $google = new Google();

        $rango = $google->cargar_code($id_seccion, $hoja_seccion)[0];
        var_dump($rango);

        if($rango == NULL || $rango == -1)
        {
            $this->session->error = 1;
            return $response->withRedirect($this->router->pathFor('conf_sistema'));
        }

        // se almacena la data en la bd
        $code = new Code();
        try
        {
            $matriz = $code->cargar_config($id_seccion, $hoja_seccion, $rango);
        }
        catch(Exception $e)
        {
            $this->session->error = 2;
            return $response->withRedirect($this->router->pathFor('conf_sistema'));
        }


        //-------------------------------------------------------------------------
        $this->session->error = 10; //msg que se dispara para notificar que no hubo problema!
        return $response->withRedirect($this->router->pathFor('conf_sistema'));
    })->setName('carga_datos_config');

    $app->get('/carga_config', function($request, $response, $args)
    {
        $error = -1;
        $index_pais = -1;
        $index_escuela = -1;
        $lista_escuelas = null;

        if($this->session->exists('error'))
        {
            $error = $this->session->error;
            $this->session->delete('error');
        }

        $code = new Code();
        $lista_paises = $code->ver_paises();

        if ($this->session->exists('id_pais_default') && $this->session->exists('id_escuela_default'))
        {
            if (!$this->session->exists('id_pais'))
            {
                $this->session->id_pais = $this->session->id_pais_default; 
            }
            if (!$this->session->exists('id_escuela'))
            {
                $this->session->id_escuela = $this->session->id_escuela_default; 
            }

            // $this->session->delete("id_pais_default");
            // $this->session->delete("id_escuela_default");
        }


        if($this->session->exists('id_pais'))
        {
            $id_pais = $this->session->id_pais;
            $index_pais = $id_pais;
            $lista_escuelas = $code->ver_escuelas($id_pais);

            if ($this->session->exists('id_escuela'))
            {
                $index_escuela = $this->session->id_escuela;
                $this->session->delete('id_escuela');
            }

            $this->session->delete('id_pais');
        }

        // var_dump($lista_paises);

        return $this->view->render($response, 'conf_sistema.html', ['index_escuela' => $index_escuela, 'lista_escuelas' => $lista_escuelas, 'index_pais' => $index_pais, 'lista_paises' => $lista_paises, 'error'=> $error]);
    })->setName('conf_sistema');

    $app->post('/busqueda_paises', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('conf_sistema'));
            }
        }

        $dato = $request->getParsedBody()['id_pais'];
        $valores = explode("-", $dato);
        $this->session->id_pais = $valores[1];

        return $response->withRedirect($this->router->pathFor('conf_sistema'));
    })->setName('busqueda_paises');

    $app->post('/guardar_pais_escuela', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('conf_sistema'));
            }
        }

        $dato = $request->getParsedBody()['id_escuela'];
        $valores = explode("-", $dato);
        $this->session->id_escuela = $valores[1];
        $this->session->id_pais = $valores[2];

        return $response->withRedirect($this->router->pathFor('conf_sistema'));
    })->setName('guardar_pais_escuela');

    $app->post('/guardar_info_config', function($request, $response, $args)
    {
        foreach($request->getParsedBody() as $key => $value)
        {
            if(empty($value))
            {
                $this->session->error = 0;
                return $response->withRedirect($this->router->pathFor('conf_sistema'));
            }
        }

        $dato = $request->getParsedBody()['id_info'];
        $valores = explode("-", $dato);
        $this->session->id_pais_default = $valores[1]; //aqui es al reves
        $this->session->id_escuela_default = $valores[2];
        
        // echo "id_pais: ".$this->session->id_pais_default.", id_escuela: ".$this->session->id_escuela_default;
        $this->session->error = 10; //msg que se dispara para notificar que no hubo problema!
        return $response->withRedirect($this->router->pathFor('conf_sistema'));
    })->setName('guardar_info_config');

    $app->run();
    ?>