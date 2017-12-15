<?php

namespace App\Http\Controllers;
use App\Afp;
use App\EconomicGroup;
use App\AfpXEconomicGroup;
use App\InvestedCompany;
use App\Functionary;
use App\FunctionaryXAfp;
use App\FunctionaryXCompany;
use App\Shareholder;
use App\Found;
use App\InvestmentRound;
use App\FinancialInstrument;
use App\Currency;
use App\Http\Controllers\DateController;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use DateTime;

class ReportController extends Controller
{
  public function hard1Instr(){

    $csv = fopen("hard1Instr.csv", "w");
    $date=new DateController();
    $matches = Afp::select('Functionary.name as functionary',
                'Afp.id as afpId',
                'Afp.name as afp',
                'InvestedCompany.id as emisorId',
                'InvestedCompany.name as emisor',
                'FunctionaryXAfp.beginDate as afpBegin', 'FunctionaryXAfp.endDate as afpEnd',
                'FunctionaryXCompany.beginDate as emisorBegin',
                'FunctionaryXCompany.endDate as emisorEnd',
                'FunctionaryXAfp.position as AfpPosition')
                ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                ->join('InvestedCompany', function($join) {
                  $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id');
                })->whereRaw('((not ("FunctionaryXAfp"."endDate" is not null and
                                "FunctionaryXCompany"."beginDate"> "FunctionaryXAfp"."endDate")
                                and not ("FunctionaryXCompany"."endDate" is not null and "FunctionaryXAfp"."beginDate">"FunctionaryXCompany"."endDate") )
                                or ("FunctionaryXAfp"."endDate" is  null and "FunctionaryXCompany"."endDate" is null))
                                and "InvestedCompany"."scope"=\'Nacional\' '
              )->orderBy('Functionary.name')->get();

    $c=0;
    foreach ($matches as $m) {

        $fechaInicio=max($m['afpBegin'],$m['emisorBegin']);
        $fechaFinal=min($date->nullToDate($m['afpEnd']),$date->nullToDate($m['emisorEnd']));


        $functionary=$m['functionary'];
        $afpId=$m['afpId'];
        $companyId=$m['emisorId'];
        $afp=$m['afp'];
        $company=$m['emisor'];
        $afpPosition=$m['afpPosition'];
        $coincidence=$date->toRange($fechaInicio,$fechaFinal);

        $fechaInicio=$date->toYearMonth($fechaInicio);
        $fechaFinal=$date->toYearMonth($fechaFinal);

        //echo $afp." ".$company." ".$coincidence."\n";
        $query=InvestmentRound::select('InvestmentRound.financialinstrumentId',
                              'InvestmentRound.year as year',
                              'InvestmentRound.month as month')
        ->join('InvestedCompany','InvestedCompany.id','=','InvestmentRound.companyId')
        ->join('Found','Found.id','=','InvestmentRound.foundId')
        ->join('Afp','Afp.id','=','Found.afpId')
        ->join('FinancialInstrument','FinancialInstrument.id','=','InvestmentRound.financialinstrumentId')
        ->whereRaw('"Afp"."name"= ? and
          "InvestedCompany"."name"= ?
        ',array($afp,$company) )->get();
        $now=$query->where('year_month','>=',$fechaInicio)
            ->where('year_month','<=',$fechaFinal);

        $before=$query->where('year_month','<',$fechaInicio);

        $after=$query->where('year_month','>',$fechaFinal);



        $arrNow=Array();//contiene los valores durante la interseccion, podria incluir repedidos con los de before
        $arrBefore=Array();//contendra todos los valores antes de la interseccion
        $arrAfter=Array();


        foreach ($before as $b) {
          array_push($arrBefore,$b['financialinstrumentId']);

        }
        foreach ($now as $n) {
          array_push($arrNow,$n['financialinstrumentId']);

        }
        foreach ($after as $a) {
          array_push($arrAfter,$a['financialinstrumentId']);

        }

        $arrBefore= array_unique($arrBefore);
        $arrNow= array_unique($arrNow);
        $arrAfter=array_unique($arrAfter);


        $news=array_diff($arrNow, $arrBefore);

        $arrNowBefore=array_merge($arrNow, $arrBefore);

        $afters=array_diff($arrAfter,$arrNowBefore );



        foreach ($arrBefore as $id) {


          $i=FinancialInstrument::find($id);

          $row = array (utf8_decode($functionary),
                        utf8_decode($coincidence),
                        utf8_decode($company) ,
                        utf8_decode($afp),
                        utf8_decode($i['name']),
                        'Antes'
                      );
          fputcsv($csv, $row);

        }


        foreach ($news as $id) {

          $i=FinancialInstrument::find($id);
          $row = array (utf8_decode($functionary),
                        utf8_decode($coincidence),
                        utf8_decode($company) ,
                        utf8_decode($afp),
                        utf8_decode($i['name']),
                        'Durante'
                      );
          fputcsv($csv, $row);
        }

        foreach ($afters as $id) {


          $i=FinancialInstrument::find($id);
          $row = array (utf8_decode($functionary),
                        utf8_decode($coincidence),
                        utf8_decode($company) ,
                        utf8_decode($afp),
                        utf8_decode($i['name']),
                          utf8_decode('Después')
                      );
          fputcsv($csv, $row);
        }



        $c++;
        echo $c."\n";


    }

    fclose($csv);

  }

  public function hard5(){
    $csv = fopen("hard5durante.csv", "w");
    $date=new DateController();
    $matches = Afp::select('Functionary.name as functionary',
                'Afp.id as afpId',
                'Afp.name as afp',
                'InvestedCompany.id as emisorId',
                'InvestedCompany.name as emisor',
                'FunctionaryXAfp.beginDate as afpBegin', 'FunctionaryXAfp.endDate as afpEnd',
                'FunctionaryXCompany.beginDate as emisorBegin',
                'FunctionaryXCompany.endDate as emisorEnd',
                'FunctionaryXAfp.position as AfpPosition',
                'FunctionaryXCompany.position as EmisorPosition')
                ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                ->join('InvestedCompany', function($join) {
                  $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id');
                })->whereRaw('((not ("FunctionaryXAfp"."endDate" is not null and
                                "FunctionaryXCompany"."beginDate"> "FunctionaryXAfp"."endDate")
                                and not ("FunctionaryXCompany"."endDate" is not null and "FunctionaryXAfp"."beginDate">"FunctionaryXCompany"."endDate") )
                                or ("FunctionaryXAfp"."endDate" is  null and "FunctionaryXCompany"."endDate" is null))
                                and "InvestedCompany"."scope"=\'Nacional\' and
                                "FunctionaryXAfp"."position"=\'GERENTE LEGAL Y DE COMPLIANCE\''
              )->orderBy('Functionary.name')->get(); //->count()
              $c=0;
              foreach ($matches as $m) {
                # code...
                $functionary=$m['functionary'];
                $afpId=$m['afpId']; $companyId=$m['emisorId'];
                $afp=$m['afp']; $company=$m['emisor'];
                $afpPosition=$m['AfpPosition'];
                $companyPosition=$m['EmisorPosition'];

                $fechaInicio=max($m['afpBegin'],$m['emisorBegin']);
                $fechaFinal=min($date->nullToDate($m['afpEnd']),$date->nullToDate($m['emisorEnd']));

                $coincidence=$date->toRange($fechaInicio,$fechaFinal);
                $fechaInicio=$date->toYearMonth($fechaInicio);
                $fechaFinal=$date->toYearMonth($fechaFinal);


                $results=InvestmentRound::select('InvestmentRound.id as investmendId',
                'Found.name as found',
                'FinancialInstrument.name as instrument',
                'InvestmentRound.year as year',
                'InvestmentRound.month as month',
                'InvestmentRound.mount as mount',
                'InvestmentRound.mountPercent as mountPercent'
                )
                ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
                ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
                ->join ('Afp','Afp.id', '=', 'Found.afpId')
                ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
                ->where('Afp.id','=',$afpId)
                ->where('InvestedCompany.id','=',$companyId)
                ->where('InvestedCompany.scope','=','Nacional')
                ->orderBy('InvestedCompany.name')->orderBy('Afp.name')
                ->orderBy('Found.name')->orderBy('FinancialInstrument.name')
                ->orderBy('InvestmentRound.year')->orderBy('InvestmentRound.month')
                ->get();


                $results= $results->where('year_month','>=',$fechaInicio)
                  ->where('year_month','<=',$fechaFinal);
                /*->where('year_month','>=',$fechaInicio)
                  ->where('year_month','<=',$fechaFinal);*/
                // ->where('year_month','<',$fechaInicio);
                //->where('year_month','>',$fechaFinal);

                foreach ($results as $r) {
                  # code...
                  $row = array (utf8_decode($functionary),
                      utf8_decode($afpPosition),
                      utf8_decode($companyPosition),
                      utf8_decode($coincidence),
                      utf8_decode($company) ,
                      utf8_decode($afp),
                      utf8_decode($r['found']),
                      utf8_decode($r['instrument']),
                      $r['year'],$r['month'],
                      utf8_decode($date->intToMonth($r['month'])),
                      $r['mount'],$r['mountPercent']);

                  fputcsv($csv, $row);

                }
                $c++;
                echo $c."\n";
              }

    fclose($csv);
  }

  public function hard5Instr(){

    $csv = fopen("hard5Instr.csv", "w");
    $date=new DateController();
    $matches = Afp::select('Functionary.name as functionary',
                'Afp.id as afpId',
                'Afp.name as afp',
                'InvestedCompany.id as emisorId',
                'InvestedCompany.name as emisor',
                'FunctionaryXAfp.beginDate as afpBegin', 'FunctionaryXAfp.endDate as afpEnd',
                'FunctionaryXCompany.beginDate as emisorBegin',
                'FunctionaryXCompany.endDate as emisorEnd',
                'FunctionaryXAfp.position as AfpPosition',
                'FunctionaryXCompany.position as EmisorPosition')
                ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                ->join('InvestedCompany', function($join) {
                  $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id');
                })->whereRaw('((not ("FunctionaryXAfp"."endDate" is not null and
                                "FunctionaryXCompany"."beginDate"> "FunctionaryXAfp"."endDate")
                                and not ("FunctionaryXCompany"."endDate" is not null and "FunctionaryXAfp"."beginDate">"FunctionaryXCompany"."endDate") )
                                or ("FunctionaryXAfp"."endDate" is  null and "FunctionaryXCompany"."endDate" is null))
                                and "InvestedCompany"."scope"=\'Nacional\' and
                                "FunctionaryXAfp"."position"=\'PRESIDENTE DEL DIRECTORIO\''
              )->orderBy('Functionary.name')->get();

              $c=0;
              foreach ($matches as $m) {

                  $fechaInicio=max($m['afpBegin'],$m['emisorBegin']);
                  $fechaFinal=min($date->nullToDate($m['afpEnd']),$date->nullToDate($m['emisorEnd']));

                  $functionary=$m['functionary'];
                  $afpId=$m['afpId'];
                  $companyId=$m['emisorId'];
                  $afp=$m['afp'];
                  $company=$m['emisor'];
                  $afpPosition=$m['AfpPosition'];
                  $companyPosition=$m['EmisorPosition'];
                  $coincidence=$date->toRange($fechaInicio,$fechaFinal);

                  $fechaInicio=$date->toYearMonth($fechaInicio);
                  $fechaFinal=$date->toYearMonth($fechaFinal);

                  //echo $afp." ".$company." ".$coincidence."\n";
                  $query=InvestmentRound::select('InvestmentRound.financialinstrumentId',
                                        'InvestmentRound.year as year',
                                        'InvestmentRound.month as month')
                  ->join('InvestedCompany','InvestedCompany.id','=','InvestmentRound.companyId')
                  ->join('Found','Found.id','=','InvestmentRound.foundId')
                  ->join('Afp','Afp.id','=','Found.afpId')
                  ->join('FinancialInstrument','FinancialInstrument.id','=','InvestmentRound.financialinstrumentId')
                  ->whereRaw('"Afp"."name"= ? and
                    "InvestedCompany"."name"= ? and "InvestedCompany"."scope"=?
                  ',array($afp,$company,'Nacional') )->get();
                  $now=$query->where('year_month','>=',$fechaInicio)
                      ->where('year_month','<=',$fechaFinal);

                  $before=$query->where('year_month','<',$fechaInicio);

                  $after=$query->where('year_month','>',$fechaFinal);



                  $arrNow=Array();//contiene los valores durante la interseccion, podria incluir repedidos con los de before
                  $arrBefore=Array();//contendra todos los valores antes de la interseccion
                  $arrAfter=Array();


                  foreach ($before as $b) {
                    array_push($arrBefore,$b['financialinstrumentId']);

                  }
                  foreach ($now as $n) {
                    array_push($arrNow,$n['financialinstrumentId']);

                  }
                  foreach ($after as $a) {
                    array_push($arrAfter,$a['financialinstrumentId']);

                  }

                  $arrBefore= array_unique($arrBefore);
                  $arrNow= array_unique($arrNow);
                  $arrAfter=array_unique($arrAfter);


                  $news=array_diff($arrNow, $arrBefore);

                  $arrNowBefore=array_merge($arrNow, $arrBefore);

                  $afters=array_diff($arrAfter,$arrNowBefore );



                  foreach ($arrBefore as $id) {


                    $i=FinancialInstrument::find($id);

                    $row = array (utf8_decode($functionary),
                                  utf8_decode($coincidence),
                                  utf8_decode($afpPosition),
                                  utf8_decode($companyPosition),
                                  utf8_decode($company) ,
                                  utf8_decode($afp),
                                  utf8_decode($i['name']),
                                  'Antes'
                                );
                    fputcsv($csv, $row);

                  }


                  foreach ($news as $id) {

                    $i=FinancialInstrument::find($id);
                    $row = array (utf8_decode($functionary),
                                  utf8_decode($coincidence),
                                  utf8_decode($afpPosition),
                                  utf8_decode($companyPosition),
                                  utf8_decode($company) ,
                                  utf8_decode($afp),
                                  utf8_decode($i['name']),
                                  'Nuevo'
                                );
                    fputcsv($csv, $row);
                  }

                  foreach ($afters as $id) {


                    $i=FinancialInstrument::find($id);
                    $row = array (utf8_decode($functionary),
                                  utf8_decode($coincidence),
                                  utf8_decode($afpPosition),
                                  utf8_decode($companyPosition),
                                  utf8_decode($company) ,
                                  utf8_decode($afp),
                                  utf8_decode($i['name']),
                                  utf8_decode('Después')
                                );
                    fputcsv($csv, $row);
                  }


        $c++;
        echo $c."\n";


    }

    fclose($csv);




  }

  public function obtenerInversionesEmisorAFP(){
    /*
    esta funcion te devuelve una instancia de la clase  Illuminate\Database\Eloquent\Collection
    ,pues si no fuese asi al aplicarle un count no saldria el valor correcto
    */

/*
      $matches=InvestmentRound::join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
                              ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
                              ->join ('Afp','Afp.id', '=', 'Found.afpId')
                              ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
                              ->select('InvestedCompany.id as companyId',
                                        'Afp.id as afpId'
                                        )
                              ->selectRaw('"InvestmentRound"."year"*100+"InvestmentRound"."month" as periodo,
                                            max("InvestmentRound"."mount") as monto,
                                            "InvestmentRound"."orden_periodo" - (ROW_NUMBER() OVER(
                                            order by "InvestedCompany"."name", "Afp"."name",
                                            "InvestmentRound"."year","InvestmentRound"."month" )) as grupo'
                                          )
                              ->groupBy('InvestedCompany.id')
                              ->groupBy('Afp.id')
                              ->groupBy('InvestmentRound.year')
                              ->groupBy('InvestmentRound.month')
                              ->groupBy('InvestmentRound.orden_periodo')
                              ->havingRaw(' max("InvestmentRound"."mount")>0')
                              ->orderBy('InvestedCompany.id')
                              ->orderBy('Afp.id')
                              ->orderBy('InvestmentRound.year')
                              ->orderBy('InvestmentRound.month');


                              //var_dump($matches->getQuery());
          $matches=$matches->getQuery();

          $c=$matches->select('companyId',
                          'afpId')
                  ->selectRaw('MIN(periodo) as Inicio,
                               MAX(periodo) as Fin'
                             )
                   ->groupBy('InvestedCompany.id')
                   ->groupBy('Afp.id')
                   ->orderBy('InvestedCompany.id')
                   ->orderBy('Afp.id')->get()->count();*/




      $query='WITH C1 AS (

          	SELECT emisor.id as emisorid,
                  	afp.id as afpid,
            			inv."year"*100+inv."month" as periodo,
           		 	max(inv."mount") as total,
              		inv.orden_periodo - (ROW_NUMBER() OVER(order by
          emisor.name, afp.name,inv."year",inv."month" )) AS grupo
            FROM public."InvestmentRound" inv
              inner join "InvestedCompany" emisor on emisor.id =inv."companyId"
              inner join ("Found" fondo inner join "Afp" afp on
          fondo."afpId"=afp.id)
              on fondo.id= inv."foundId"
              inner join "FinancialInstrument" instru on
          instru.id=inv."financialinstrumentId"
              where emisor.scope=\'Nacional\'
              group by emisor.id,emisor.name,
          afp.id,afp.name,inv.year,inv.month,inv.orden_periodo
            	having  max(inv."mount")>0
              order by emisor.name, afp.name,inv."year",inv."month"
          )
          SELECT
          	C1.emisorid,C1.afpid,
          	MIN(C1.periodo) AS Inicio,
          	MAX(C1.periodo) AS Fin


          FROM
          	C1
              group by C1.emisorid, C1.afpid,C1.grupo
              order by C1.emisorid, C1.afpid';

      $matches=new Collection(DB::select($query));

      return $matches;
  }
  public function obtenerInversiones(){

          /*
          esta funcion te devuelve una instancia de la clase  Illuminate\Database\Eloquent\Builder
          que posteriormente tendra que ser aplicado con get o all para obtener una instancia de
          Illuminate\Database\Eloquent\Collection
          */

          $funtionary_afp_Company_list = InvestmentRound::select('InvestmentRound.id as investmendId',
                'Found.name as found',
                'FinancialInstrument.name as instrument',
                'InvestmentRound.year as year',
                'InvestmentRound.month as month',
                'InvestmentRound.mount as mount',
                'InvestmentRound.mountPercent as mountPercent'
                )
                ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
                ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
                ->join ('Afp','Afp.id', '=', 'Found.afpId')
                ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
                ->orderBy('InvestedCompany.name')->orderBy('Afp.name')
                ->orderBy('Found.name')->orderBy('FinancialInstrument.name')
                ->orderBy('InvestmentRound.year')->orderBy('InvestmentRound.month')
                ;

          return $funtionary_afp_Company_list;


  }
  public function obtenerInversionesPorAlcance($scope){
    // $scope es una variable que puede ser 'I' de internacional o 'N de nacional'
    if ($scope=='I'){
      $scope='Internacional';
    }
    else if ($scope=='N' ){
      $scope='Nacional';
    }


    $listaInversiones=$this->obtenerInversiones();
    return $listaInversiones->where('InvestedCompany.scope',$scope);
  }


  public function obtenerInversionesEmisorAFPporAlcance($scope){
    // $scope es una variable que puede ser 'I' de internacional o 'N de nacional'
    if ($scope=='I'){
      $scope='Internacional';
    }
    else if ($scope=='N' ){
      $scope='Nacional';
    }


    $listaInversiones=$this->obtenerInversionesEmisorAFP();
    return $listaInversiones->where('scope',$scope);
  }

  public function extra1(){
    $csv = fopen("extra1.csv", "w");
    $dateLogNeg= new DateController();
    ini_set('memory_limit','2048M');
    $reporteLogNeg= new ReportController();
    $inversionesNacionales= $reporteLogNeg->obtenerInversionesEmisorAFP();
    //var_dump($inversionesNacionales);
    //print_r($inversionesNacionales[0]->emisorname);

    //return $inversionesNacionales->count();
    $n=0;
    $contador=0;
    foreach ($inversionesNacionales as $i) {
        $contador++;
        echo "Vuelta ".$contador."\n";
        $afpId=$i->afpid;
        $companyId=$i->emisorid;


        //obtengo los funcionarios que tienen la misma afp y emisor
        $functionary_afp_Company_match = Afp::select('Functionary.name as functionary',
                                                      'Afp.id as afpId',
                                                      'Afp.name as afp',
                                                      'InvestedCompany.id as emisorId',
                                                      'InvestedCompany.name as emisor',
                                                      'FunctionaryXAfp.beginDate as afpBegin',
                                                      'FunctionaryXAfp.endDate as afpEnd',
                                                      'FunctionaryXCompany.beginDate as emisorBegin',
                                                      'FunctionaryXCompany.endDate as emisorEnd',
                                                      'FunctionaryXAfp.position as afpPosition',
                                                      'FunctionaryXCompany.position as companyPosition')
                                                      ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                                                      ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                                                      ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                                                      ->join('InvestedCompany', function($join) {
                                                        $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id');
                                                      })->whereRaw('((not ("FunctionaryXAfp"."endDate" is not null and
                                                                      "FunctionaryXCompany"."beginDate"> "FunctionaryXAfp"."endDate")
                                                                      and not ("FunctionaryXCompany"."endDate" is not null and "FunctionaryXAfp"."beginDate">"FunctionaryXCompany"."endDate") )
                                                                      or ("FunctionaryXAfp"."endDate" is  null and "FunctionaryXCompany"."endDate" is null))
                                                                      and "InvestedCompany"."scope"=\'Nacional\' '
                                                    )
                                                    ->where("InvestedCompany.id",$companyId)
                                                    ->where("Afp.id",$afpId)
                                                    ->orderBy('Functionary.name')->get();
              //return $functionary_afp_Company_match[0]['afpBegin'];
              echo $functionary_afp_Company_match->count()."\n";
              if( $functionary_afp_Company_match->count()>0){
                try{
                  $n++;
                  /*fechai y fechaf son tipo Date con formato yyyy/mm/dd*/
                  //return $functionary_afp_Company_match['afpBegin'];


                  foreach ($functionary_afp_Company_match as $m){
                    $fechai=max($m['afpBegin'],
                                $m['emisorBegin']);
                    $fechaf=min($dateLogNeg->nullToDate($m['afpEnd']),
                                $dateLogNeg->nullToDate($m['emisorEnd']));

                    $periodoCoincidencia=$dateLogNeg->toRange($fechai,$fechaf);

                    $fechaiNum=$dateLogNeg->toYearMonth($fechai);
                    $fechafNum=$dateLogNeg->toYearMonth($fechaf);

                    //si la fecha de inicio de coincidencia es mayor a la fecha de inicio de inversion
                    //o la fecha de fin de coincidencia es menor igual que la fecha de fin de inversion
                    //entonces coincidio en algun momento mientras se invertia
                    echo $fechai."\n";
                    echo $fechaf."\n";

                    echo $fechaiNum."\n";
                    echo $fechafNum."\n";

                    echo $i->inicio."\n";
                    echo $i->fin."\n";
                      $intersection=$dateLogNeg->intersectionDateNum($i->inicio ,$i->fin, $fechaiNum, $fechafNum);
                      if(!is_null($intersection)){


                        $yearInvi= floor($i->inicio/100);
                        $monthInvi= $i->inicio%100;
                        $monthInvi=$dateLogNeg->intToMonth($monthInvi);

                        $yearInvf= floor($i->fin/100);
                        $monthInvf= $i->fin%100;
                        $monthInvf=$dateLogNeg->intToMonth($monthInvf);

                        echo $yearInvi." ".$monthInvi."\n";
                        echo $yearInvf." ".$monthInvf."\n";
                        $fechaiMatchInversionAfpEmisor = $intersection[0];

                        $fechafMatchInversionAfpEmisor  =$intersection[1];


                        echo "coincidenciaI: ".$fechaiMatchInversionAfpEmisor."\n";
                        echo "coincidenciaF: ".$fechafMatchInversionAfpEmisor."\n";
                        //periodo de coincidencia mientras se realizaba la inversion entre afp y emisor y
                        // a la vez el funcionario trabajaba en ambas
                        $yearI= floor($fechaiMatchInversionAfpEmisor/100);
                        $monthI=$fechaiMatchInversionAfpEmisor%100;

                        $yearF=floor($fechafMatchInversionAfpEmisor/100);
                        $monthF=$fechafMatchInversionAfpEmisor%100;

                        //armamos la cadena que representará el periodo de inversion
                        $periodoCoincidenciaInv="[".$yearI."/".$monthI." - ".$yearF."/".$monthF. "]";
                        echo "periodo coincidencia: ".$periodoCoincidenciaInv."\n";
                        $row = array (utf8_decode($m['afp']),
                                      utf8_decode($m['emisor']),
                                      utf8_decode($periodoCoincidencia), //esta es la coincidencia como una sola
                                      utf8_decode($periodoCoincidenciaInv) ,
                                      utf8_decode($m['functionary']),
                                      utf8_decode($m['afpPosition']),
                                      utf8_decode($m['companyPosition']),
                                      utf8_decode($yearInvi." ".$monthInvi),
                                      utf8_decode($yearInvf." ".$monthInvf)
                                    );

                        fputcsv($csv, $row);
                      }



                  }

                }
                catch(Exception $e){

                }
              }







    }

    return $n;

  }


  public function analisis(){




    $results= InvestmentRound::join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
              ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
              ->join ('Afp','Afp.id', '=', 'Found.afpId')
              ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
              ->where('Afp.id','=',1)
              ->where( function ($query) {
                        $query->where('Found.name','like','%1%')
                              ->orWhere('Found.name','like','%2%')
                              ->orWhere('Found.name','like','%3%');
                        })
              ->where('InvestmentRound.year','=',2017)
              ->where('InvestmentRound.month','=',1)
              ->where('FinancialInstrument.id','=',7)
              ->where('InvestedCompany.id','=',3)

              ->selectRaw('sum("InvestmentRound"."mount"*1000) as afptotal')->get();



    if ($results->count()==1){

      $afpTotal=$results[0]['afptotal']; /* Monto invertido en el activo por AFP */



    }
    else{
      echo "Se encontró mas de un resultado al calcular Afp Total".'\n';
    }


    $cantidades=array(84304,3350877,1248275) ;
    $fondos=array('HA01','HA02','HA03');


    for ($i=0;$i<3;$i++){
          $cantidad=$cantidades[$i];
          $results= InvestmentRound::join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
                    ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
                    ->join ('Afp','Afp.id', '=', 'Found.afpId')
                    ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
                    ->where('Found.name','=',$fondos[$i])
                    ->where('InvestedCompany.id','=',3)
                    ->where('FinancialInstrument.id','=',7)
                    ->where('InvestmentRound.year','=',2017)
                    ->where('InvestmentRound.month','=',1)
                    ->selectRaw('  sum("InvestmentRound"."mount"*1000) as monto')->get();


          if ($results->count()==1){
              //return $results;


              $monto=$results[0]['monto']; /*Monto invertido en el activo por fondo de cada AFP */

          }
          else{
            echo "Se encontró mas de un resultado al calcular monto".'\n';
          }


          $results= InvestmentRound::join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
                    ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
                    ->join ('Afp','Afp.id', '=', 'Found.afpId')
                    ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
                    ->where('Found.name','=',$fondos[$i])
                    ->where( function ($query) {
                              $query->where('FinancialInstrument.id','=',7)
                                    ->orWhere('FinancialInstrument.id','=',10);
                              })
                    ->where('InvestmentRound.year','=',2017)
                    ->where('InvestmentRound.month','=',1)
                    ->where('InvestedCompany.scope','=','Nacional')
                    ->selectRaw('sum("InvestmentRound"."mount"*1000) as portafolioAcciones')->get();



          if ($results->count()==1){

            //no es el mismo valor del que sale en el analisis de Percy, sale un valor algo menor, hay que ver por qué no calza
            $portafolioAccBonos=$results[0]['portafolioacciones']; /* Activos administratos en inversiones locales (total)*/


          }
          else{
            echo "Se encontró mas de un resultado al calcular Portafolio Total".'\n';
          }

          //la otra opción para $portafolioTotal es obtener el resultado directamente desde el excel que uso Percy
          $results= InvestmentRound::join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
                    ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
                    ->join ('Afp','Afp.id', '=', 'Found.afpId')
                    ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
                    ->where('Found.name','=',$fondos[$i])
                    ->where('InvestmentRound.year','=',2017)
                    ->where('InvestmentRound.month','=',1)
                    ->selectRaw('sum("InvestmentRound"."mount"*1000) as portafoliototal')->get();



          if ($results->count()==1){

            //A portafoliototal falta restarle "operaciones en transito"
            $portafolioTotal=$results[0]['portafoliototal']; /*Activos administratos (total)*/


          }
          else{
            echo "Se encontró mas de un resultado al calcular Portafolio Total".'\n';
          }



          $precio=$monto/$cantidad;

          $ratio1=$afpTotal/$portafolioTotal;
          $ratio2=$monto/$portafolioTotal;
          $ratio3=$afpTotal/$portafolioTotal;
          $ratio4=$afpTotal/$portafolioAccBonos;


          $ratio1excel=($monto/$portafolioTotal)*100;
          $ratio2excel=($monto/$portafolioAccBonos)*100;

          echo 'Monto: '.$monto."<br>";
          echo 'Cantidad: '.$cantidad."<br>";
          echo 'Precio: '.$precio."<br>";
          echo 'Portafolio Total: '.$portafolioTotal."<br>";
          echo 'Portafolio Acciones o Bonos: '.$portafolioAccBonos."<br>";
          echo 'Ratio1Excel: '.$ratio1excel."<br>";
          echo 'Ratio2Excel: '.$ratio2excel."<br>";
          echo '===================================='."<br>"."<br>";

    }
    return;

  }
}
