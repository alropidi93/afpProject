<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
class DateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function differenceDate($date1,$date2){

        $dateFinal=$date1;
        $dateBegin=$date2;
        if ($date1>$date2){
          $dateFinal=$date2;
          $dateBegin=$date1;
        }


        $final = DateTime::createFromFormat('Y-m-d', $dateFinal);
        $begin = DateTime::createFromFormat('Y-m-d', $dateBegin);
        $diff=$final->diff($begin);
        $period =$diff->y." años, ".$diff->m." meses, ".$diff->d." días.";


        return $period;
    }

    public function currentDay($myDate){
      //si la fecha es el dia de hoy la transforma a un texto que dice "A la actualidad"
      $today= date('Y-m-d');
      if ($today==$myDate || is_null($myDate)){
        return "A la actualidad";
      }
      return $myDate;
    }

    public function intToMonth($num){
    
      try {
        switch ($num) {
          case 1:
              return "Enero";

          case 2:
              return "Febrero";
          case 3:
            return "Marzo";
          case 4:
            return "Abril";
          case 5:
            return "Mayo";
          case 6:
            return "Junio";
          case 7:
            return "Julio";
          case 8:
            return "Agosto";
          case 9:
            return "Setiembre";
          case 10:
            return "Octubre";
          case 11:
            return "Noviembre";
          case 12:
            return "Diciembre";
          default:
            return null;
          }
      }
      catch(Exception $e){
        echo $e->getMessage()."\n";
      }

    }
    public function nullToDate($date){
      try{
        if (is_null($date)){
          $date=date('Y-m-d');
        }
        return $date;
      }
      catch(Exception $e){
        echo $e->getMessage()."\n";
      }
    }

    public function greatest($date1,$date2){
      $date1=$this->nullToDate($date1);
      $date2=$this->nullToDate($date1);

      if ($date1>=$date2){
        return $date1;
      }
      return $date2;
    }

    public function least($date1,$date2){
      $date1=$this->nullToDate($date1);
      $date2=$this->nullToDate($date1);

      if ($date1>=$date2){
        return $date2;
      }
      return $date1;
    }

    public function toYearMonth($date){
      $filter =explode("-",$date);
      $filter=$filter[0]*100+$filter[1];
      return $filter;

    }

    public function toRange($date1,$date2){
      $coincidence= "[".$date1." / ".$date2."]";
      return $coincidence;
    }


    public function intersectionDate($dates){


      $today= date('Y-m-d');

      $date1=$dates['date1'];  //emisorBegin
      $date2=$dates['date2'];//podria ser null //emisorEnd
      $date3=$dates['date3'];//afpBegin
      $date4=$dates['date4'];//podria ser null //afpEnd


      //$date2= DateTime::createFromFormat('y-m-d', $today);
      //echo $date2."<br>";
///*

      if (is_null($date2)){
          $date2=$today;
      }
      if (is_null($date4)){
          $date4=$today;
      }

      $finded=TRUE;

      if ($date2 >=$date3 && $date2<=$date4 ){
        if ( $date1>=$date3)
          $dateOne=$date1;
        else $dateOne=$date3;
        $dateTwo= $date2;
      }
      else if ($date2 >=$date4 && $date1<=$date4 ){
        if($date3<=$date1)
          $dateOne=$date1;
        else $dateOne=$date3;
          $dateTwo=$date4;

      }
      else $finded=FALSE;

      if($finded) {
          return array($dateOne,$dateTwo);


      }
      return null;



  //  */
    }


}
