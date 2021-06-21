<?php

namespace App\Services;

use DateTime;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Email {
    
    private string $email;
    private string $subject;
    private string $body;

    public function set_body($body){
        $this->body = $body;
    }
    public function set_subject($subject){
        $this->subject = $subject;
    }


    public function __construct($email)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $this->email = $email;
           
    }
    public static function filter($string)
    {
        $arr = explode (" ", $string);
        
        $arrNew = [];
        foreach($arr as $key => $value){
            
            $tmp_arr = explode(";", $value);
            
            for($i = 0 ; $i < count($tmp_arr) ; $i++){
                $arrNew[] = $tmp_arr[$i];
                
            }
            
        }
        
        return $arrNew;
    }

    public static function sort($arr)
    {
        
        sort($arr);
        return $arr;
    }

    public static function array_unique($arr){
        $new_arr = array_unique($arr);
        return $new_arr;
    }

    public function send(){
        $hora = date('H:i');
        $endereco = $this->email;
        $assunto = $this->subject;
        $log = "hora: $hora, endereço: $endereco, assunto: $assunto ";
        $faker = rand(0,1);;
        if($faker){
            $this->log(storage_path().'/logs', 'sent.log', 'enviado' , $log);
        }else{
            $this->log(storage_path().'/logs', 'fail.log', 'error' , $log);
        }
        
        
            
        
    }

    
    

    public function log( $path, $nameFile ,$name, $message){
        
        if(!is_dir($path)){
            mkdir($path, 0776 , true);
            
        }
        
        $log = new Logger($name);
        $log->pushHandler(new StreamHandler($path.'/'.$nameFile));
        $log->info($message);
    }

    public static function sav($email){
        try
        {
            $date = new DateTime();
            
            $path = storage_path().'/app/emails'."/";
            $name = "emails_{$date->getTimestamp()}.txt";
            $file = $path.$name;

            if(!is_dir($path)){
                mkdir($path, 0776 , true);
                
            }
            if(!file_exists($file)){
                
                $arquivo = fopen($file,'w');
             
                if( $arquivo == false){
                    return response()->json(['error' => 'houve um erro por favor contate o administrados']);
                }
                fwrite($arquivo, $email);
                fclose($arquivo);
            }else{
                $arquivo = fopen($file,'a');
                $nl=chr(10);
                fwrite($arquivo, $email. $nl);
                fclose($arquivo);
            }
        }catch(Exception $e){

        }
    

    }

    public static function openEmails($path){
        $file = $path;
        
        if(!file_exists($file)){
            return false;
        }
        $arr_arquivo = file($file);
        
        
        return $arr_arquivo;
    }



}
