<?php

namespace App\Helpers;
use Carbon\Carbon;
use File;

class Helper
{
    public static function IDGenerator($model, $trow, $length, $prefix)
    {
         $data = $model::orderBy('id', 'desc')->first();

            if (!$data) {
                $last_number = 1;
            } else {
                $code = $data->$trow;

                // Extraire la partie numérique après le préfixe
                $numberPart = preg_replace('/[^0-9]/', '', substr($code, strlen($prefix)));

                if (is_numeric($numberPart)) {
                    $last_number = intval($numberPart) + 1;
                } else {
                    $last_number = 1; // fallback si la partie numérique est absente
                }
            }

            // Générer les zéros devant pour respecter la longueur totale
            $zero = str_pad('', $length - strlen($last_number), '0');
            return $prefix . '-' . $zero . $last_number;
    }

    public static function getFiles($totalFiles, $request, $path, $name, $prefix, $additional=null, $req='files'){

        $filename = '';
        $allFileName = ($additional!=null? json_decode($additional) : []);

        for ($x = 0; $x < $totalFiles; $x++) 
        {

           if ($request->hasFile($req.$x)) 
            {
                $current_date_time = Carbon::now()->toDateTimeString();
                $paseDate = explode(' ', $current_date_time);
                $file     = $request->file($req.$x); 
                $filename = $prefix.'_'.$name."_".($x+1).'_'.$paseDate[0].'_'.str_replace(":","-",$paseDate[1]).'.'.$file->getClientOriginalExtension();

                $file->move($path, $filename);
                array_push($allFileName, $filename);
              
            }
        } 

        return $allFileName; 
    }

    
    public static function deleteFiles($path, $files){
        $convert = json_decode($files);

        for ($x = 0; $x < sizeof($convert); $x++) 
        {

           File::delete($path.$convert[$x]);
        } 

    }

    public static function deleteFile($path, $file){
        File::delete($path.$file);
    }

    function changeDateFormate($date,$date_format){

        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);

    }
}
?>
