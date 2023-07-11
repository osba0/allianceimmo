<?php

namespace App\Helpers;
use Carbon\Carbon;
use File;

class Helper
{
    public static function IDGenerator($model, $trow, $length, $prefix)
    {
        $data = $model::orderBy('id', 'desc')->first();
        if(!$data){
            $og_length = $length;
            $last_number = '';
        }else{
            $code = substr($data->$trow, strlen($prefix)+1);
            $actial_last_number = ($code/1) * 1;
            $increment_last_number = $actial_last_number+1;
            $last_number_length = strlen($increment_last_number);
            $og_length = $length - $last_number_length;
            $last_number = $increment_last_number;
        }
        $zero = "";
        for($i=0;$i<$og_length;$i++){
            $zero.="0";
        }
        return $prefix.'-'.$zero.$last_number; 
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

    function changeDateFormate($date,$date_format){

        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);

    }
}
?>
