<?php

namespace App\Http\Controllers;

use App\Services\Email;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function store(Request $request){
        $data = $request->only(['subject','body']);
        foreach($data as $key => $value){
            if(empty($value)){
                return response()->json(['error' => "o campo {$value} está em branco"]);
            }
        }

        $path = storage_path().'/app/emails';
        $diretorio = dir($path);
        $emails = [];
        while($arquivo = $diretorio -> read()){
            $extensao = pathinfo($arquivo);
            if($extensao['extension'] == "txt"){
                foreach(Email::openEmails($path.'/'.$arquivo) as $key => $value){
                    $emails [] = $value;
                }
                
            }
        }
        $body = $data['body'];
        $subject = $data['subject'];
        
        $arr_emails_sem_duplicados = Email::array_unique($emails);
        
        foreach($arr_emails_sem_duplicados as $key=> $value){
            $email = new Email($value);
            $email->set_body($body);
            $email->set_subject($subject);

            $email->send();
        }
    }

    public function new(Request $request){
        
        $emails = $request->only('emails')['emails'];
        if(empty($emails)){
            return response()->json(['error' => 'Emails não encontrados']);
        }
        $arr_email = Email::filter($emails);
        $arr_emails_ordernados =   Email::sort($arr_email);
        $arr_sem_duplicados = Email::array_unique($arr_emails_ordernados);
        //$arr_existentes_email = Email::openEmails();
        
        
       
        foreach($arr_sem_duplicados as $key => $value){
          
            Email::sav($value);
            
        }
        
        return response()->json(["success" => "Emails salvo com sucesso!"]);
        

    }
}
