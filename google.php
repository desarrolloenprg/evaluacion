<?php
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/client_s.json');
    require 'vendor/autoload.php';
    require 'src/conexion.php';

    class Google
    {
        private $cliente;
        private $servicio;

        public function Google ()
        {
            $this->cliente = new Google_Client();
            
            $this->cliente->useApplicationDefaultCredentials();
            
            $this->cliente->setApplicationName("programacedemy-client-1");
            $this->cliente->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);
            
            if ($this->cliente->isAccessTokenExpired()) { $this->cliente->refreshTokenWithAssertion(); }
            $this->servicio = new Google_Service_Sheets($this->cliente);
        }
        
        private function comprobar_hoja ($id_documento, $hoja)
        {
            $bandera = false;
            $resultado = $this->servicio->spreadsheets->get($id_documento);
            
            $titulos = $resultado['sheets'];
            
            $array_titulo = array_column($titulos, 'properties');
            print_r($array_titulo);
            exit;
            
            foreach ($array_titulo as $element) 
            {
                if ($element->title == $hoja) 
                {
                    $bandera = true;
                    break;
                }
            }

            
            if(!$bandera) {throw new Exception('Nombre de la hoja invalido.');}
        }

        public function ver_rango ($id_documento, $hoja)
        {
            $rango_final = -1;
            try
            {
                // $this->comprobar_hoja($id_documento, $hoja); no funciona metodo revisar
                $rango = $hoja.'!A1:B1';
                $resultado = $this->servicio->spreadsheets_values->get($id_documento, $rango);
                $matriz =  $resultado->getValues();  
                $rango_final = $matriz[0][1];
                
            }
            catch(Exception $e)
            {
                return -1;
            }
            return $rango_final;
        }

        public function ver_rango_proyecto($id, $hoja)
        {
            $rango = -1;
            try
            {
                // $this->comprobar_hoja($id_documento, $hoja); no funciona metodo revisar
                $busqueda = $hoja."!A1:B1";
                $resultado = $this->servicio->spreadsheets_values->get($id, $busqueda);
                $matriz =  $resultado->getValues();  
                $rango = $matriz[0][1];
            }
            catch(Exception $e)
            {
                print($e->getMessage());
                return -1;
            }
            return $rango;
        }

        public function ver_sesion_target($id, $hoja)
        {
            $rango = -1;
            try
            {
                // $this->comprobar_hoja($id_documento, $hoja); no funciona metodo revisar
                $busqueda = $hoja."!F1:G1";
                $resultado = $this->servicio->spreadsheets_values->get($id, $busqueda);
                $matriz =  $resultado->getValues();  
                $rango = $matriz[0][1];
            }
            catch(Exception $e)
            {
                print($e->getMessage());
                return -1;
            }
            return $rango;
        }

        public function suma_sesion_target($id, $hoja, $target)
        {
            $rango = 0;
            try
            {
                // $this->comprobar_hoja($id_documento, $hoja); no funciona metodo revisar
                $busqueda = $hoja."!".$target;
                $resultado = $this->servicio->spreadsheets_values->get($id, $busqueda);
                $matriz =  $resultado->getValues();  
                for($i=0; $i < count($matriz[0]); $i++)
                {
                    $rango += $matriz[0][$i];
                }
                
            }
            catch(Exception $e)
            {
                print($e->getMessage());
                return -1;
            }
            return $rango;
        }

        public function cargar_code ($id_seccion, $hoja_seccion)
        {
            $rango_seccion = $this->ver_rango($id_seccion, $hoja_seccion);
            if($rango_seccion == -1){ return -1; }

            return array($rango_seccion);
        }

        public function matriz ($id, $rango)
        {
            try
            {
                $resultado = $this->servicio->spreadsheets_values->get($id, $rango);
                $matriz =  $resultado->getValues();  
            }
            catch(Exception $e)
            {
                return -1;
            }
            
            return $matriz;
        }

        public function valor ($id, $hoja, $rango)
        {
            try
            {
                $resultado = $this->servicio->spreadsheets_values->get($id, $hoja."!".$rango);
                $vector =  $resultado->getValues();
                return $vector[0][0];  
            }
            catch(Exception $e)
            {
                return -1;
            }
        }

    };
?>