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


/*
        $before=$before->get();
        $now->whereRaw('("InvestmentRound"."year"*100+"InvestmentRound"."month" >= ?)
            and("InvestmentRound"."year"*100+"InvestmentRound"."month"<= ?)
          ', array($fechaInicio,$fechaFinal))
          ->whereNotIn('InvestmentRound.financialinstrumentId',$before);


        $after->whereRaw(' ("InvestmentRound"."year"*100+"InvestmentRound"."month" > ?)',array($fechaInicio))
        ->whereNotIn('InvestmentRound.financialinstrumentId',$before)
        ->whereNotIn('InvestmentRound.financialinstrumentId',$now);

       //$before=$before->get();
        //$now=$now->get();
        //$after=$after->get();

        if (!empty($before)) {
          $before=$before->get();
          foreach ($before as $id) {

            $i=FinancialInstrument::find($id->financialinstrumentId);
            $row = array (utf8_decode($functionary),
                          utf8_decode($coincidence),
                          utf8_decode($company) ,
                          utf8_decode($afp),
                          utf8_decode($i['name']),
                          'Anterior'
                        );
            fputcsv($csv, $row);


          }
        }

        if (!empty($now)) {
        $now=$now->get();
          foreach ($now as $id) {
            $i=FinancialInstrument::find($id->financialinstrumentId);
            $row = array (utf8_decode($functionary),
                          utf8_decode($coincidence),
                          utf8_decode($company) ,
                          utf8_decode($afp),
                          utf8_decode($i['name']),
                          'Nuevo'
                        );
            fputcsv($csv, $row);


          }
        }

        if (!empty($after)) {
          $after=$after->get();
          foreach ($after as $id) {
            $i=FinancialInstrument::find($id->financialinstrumentId);
            $row = array (utf8_decode($functionary),
                          utf8_decode($coincidence),
                          utf8_decode($company) ,
                          utf8_decode($afp),
                          utf8_decode($i['name']),
                          'Despues'
                        );
            fputcsv($csv, $row);
          }
        }
*/

        $c++;
        echo $c."\n";


    }

    fclose($csv);

  }

  public function hard5(){
    $csv = fopen("hard5despues.csv", "w");
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
                                and "InvestedCompany"."scope"=\'Nacional\' and
                                "FunctionaryXAfp"."position"=\'PRESIDENTE DEL DIRECTORIO\''
              )->orderBy('Functionary.name')->get(); //->count()
              $c=0;
              foreach ($matches as $m) {
                # code...
                $functionary=$m['functionary'];
                $afpId=$m['afpId']; $companyId=$m['emisorId'];
                $afp=$m['afp']; $company=$m['emisor'];
                $afpPosition=$m['AfpPosition'];

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


                $results= $results->where('year_month','>',$fechaFinal);
                /*->where('year_month','>=',$fechaInicio)
                  ->where('year_month','<=',$fechaFinal);*/
                // ->where('year_month','<',$fechaInicio);
                //->where('year_month','>',$fechaFinal);

                foreach ($results as $r) {
                  # code...
                  $row = array (utf8_decode($functionary),
                      utf8_decode($afpPosition),
                      utf8_decode($coincidence),utf8_decode($company) ,
                      utf8_decode($afp),utf8_decode($r['found']),
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
                                  utf8_decode($afpPosition),
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
                                  utf8_decode($company) ,
                                  utf8_decode($afp),
                                  utf8_decode($i['name']),
                                    utf8_decode('Después')
                                );
                    fputcsv($csv, $row);
                  }


          /*
                  $before=$before->get();
                  $now->whereRaw('("InvestmentRound"."year"*100+"InvestmentRound"."month" >= ?)
                      and("InvestmentRound"."year"*100+"InvestmentRound"."month"<= ?)
                    ', array($fechaInicio,$fechaFinal))
                    ->whereNotIn('InvestmentRound.financialinstrumentId',$before);


                  $after->whereRaw(' ("InvestmentRound"."year"*100+"InvestmentRound"."month" > ?)',array($fechaInicio))
                  ->whereNotIn('InvestmentRound.financialinstrumentId',$before)
                  ->whereNotIn('InvestmentRound.financialinstrumentId',$now);

                 //$before=$before->get();
                  //$now=$now->get();
                  //$after=$after->get();

                  if (!empty($before)) {
                    $before=$before->get();
                    foreach ($before as $id) {

                      $i=FinancialInstrument::find($id->financialinstrumentId);
                      $row = array (utf8_decode($functionary),
                                    utf8_decode($coincidence),
                                    utf8_decode($company) ,
                                    utf8_decode($afp),
                                    utf8_decode($i['name']),
                                    'Anterior'
                                  );
                      fputcsv($csv, $row);


                    }
                  }

                  if (!empty($now)) {
                  $now=$now->get();
                    foreach ($now as $id) {
                      $i=FinancialInstrument::find($id->financialinstrumentId);
                      $row = array (utf8_decode($functionary),
                                    utf8_decode($coincidence),
                                    utf8_decode($company) ,
                                    utf8_decode($afp),
                                    utf8_decode($i['name']),
                                    'Nuevo'
                                  );
                      fputcsv($csv, $row);


                    }
                  }

                  if (!empty($after)) {
                    $after=$after->get();
                    foreach ($after as $id) {
                      $i=FinancialInstrument::find($id->financialinstrumentId);
                      $row = array (utf8_decode($functionary),
                                    utf8_decode($coincidence),
                                    utf8_decode($company) ,
                                    utf8_decode($afp),
                                    utf8_decode($i['name']),
                                    'Despues'
                                  );
                      fputcsv($csv, $row);
                    }
                  }
          */

        $c++;
        echo $c."\n";


    }

    fclose($csv);




  }


}
