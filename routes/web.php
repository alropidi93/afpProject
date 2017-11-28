<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
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


Route::get('/', function () {
    return view('welcome');
});

Route::get('/insertAfp',function(){
  if (($entrada = fopen('files/afps.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {
        $afp=new Afp();
        $afp->name=$data['0'];
        $afp->save();
      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});

Route::get('/updateEmisor',function(){
  if (($entrada = fopen('files/emisoresUpdate.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {


        $emisor=InvestedCompany::find($data[0]);

        $emisor->scope=utf8_encode($data['1']);
        $emisor->save();
      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});

Route::get('/insertEmisor',function(){

  /*
  if (($entrada = fopen('files/emisores.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {


        $emisor=new InvestedCompany();
        $emisor->name=utf8_encode($data[0]);
        $emisor->scope='Nacional';
        $emisor->save();
      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }*/
  $emisor=new InvestedCompany();
  $emisor->name=utf8_encode('COMPASS GROUP');
  $emisor->scope='Nacional';

  $emisor->save();

});


Route::get('/insertEconomicGroup',function(){
  if (($entrada = fopen('files/gruposeconomicos.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {
        $eco=new EconomicGroup();
        $eco->name=utf8_encode($data['0']);
        $eco->save();
      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});


Route::get('/afpxeconomic',function(){
  if (($entrada = fopen('files/afpXeconomicgroup.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {
        $data['2'] = DateTime::createFromFormat('d/m/Y', $data['2']);


        if (empty($data['3'])) $data['3']=null;
        else{
          $data['3'] = DateTime::createFromFormat('d/m/Y', $data['3'])->format('Y-m-d');
        }
        Afp::find($data['0'])->economicGroups()->save(EconomicGroup::find($data['1']),
        ['beginDate' => $data['2']->format('Y-m-d'),
        'endDate'=>$data['3'],'description'=>null,'active'=>null]);

      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});


Route::get('/prueba',function(){

  //$dateManagment= new DateController();
  //$difference= $dateManagment->differenceDate('2008-03-14','2008-02-13');
  //return $difference;


  $emisores = InvestedCompany::all();//where('scope','Nacional')->get();

  $csv = fopen("Emisores.csv", "w");
  foreach ($emisores as  $em) {

    $row = array (utf8_decode($em['name']),$em['id']);
    fputcsv($csv, $row);
  }


/*
  $fondos=Afp::find(3)->founds;


  $counter=0;
  foreach ($fondos as $fondo ) {
    echo $fondo['name']."<br>";
    $counter++;
    # code...
  }

  return $counter;*/


});


Route::get('/afpxfunctionary',function(){
  if (($entrada = fopen('files/funcionariosxafp.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {

        $data[4] = DateTime::createFromFormat('d/m/Y', $data[4]);


        if (empty($data[5])) $data[5]=null;
        else{
          $data[5] = DateTime::createFromFormat('d/m/Y', $data[5])->format('Y-m-d');
        }
        Afp::find($data[0])->functionaries()->save(Functionary::find($data[1]),
        ['beginDate' => $data[4]->format('Y-m-d'),
        'endDate'=>$data[5],'typePosition'=>utf8_encode($data[2]),
        'position'=>utf8_encode($data[3])]);

      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});


Route::get('/emisorxfunctionary',function(){
  if (($entrada = fopen('files/funcionariosxemisor.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {

        $data[4] = DateTime::createFromFormat('d/m/Y', $data[4]);


        if (empty($data[5])) $data[5]=null;
        else{
          $data[5] = DateTime::createFromFormat('d/m/Y', $data[5])->format('Y-m-d');
        }

        InvestedCompany::find($data[0])->functionaries()->save(Functionary::find($data[1]),
        ['beginDate' => $data[4]->format('Y-m-d'),
        'endDate'=>$data[5],'typePosition'=>utf8_encode($data[2]),
        'position'=>utf8_encode($data[3])]);

      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});


Route::get('/emisorxeconomic',function(){
  if (($entrada = fopen('files/emisorxeconomicgroup.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {
        $data['2'] = DateTime::createFromFormat('d/m/Y', $data['2']);


        if (empty($data['3'])) $data['3']=null;
        else{
          $data['3'] = DateTime::createFromFormat('d/m/Y', $data['3'])->format('Y-m-d');
        }
        InvestedCompany::find($data['0'])->economicGroups()->save(EconomicGroup::find($data['1']),
        ['beginDate' => $data['2']->format('Y-m-d'),
        'endDate'=>$data['3'],'description'=>null,'active'=>null]);

      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});








Route::get('/escribe',function(){
  $groups = Shareholder::all();

  $csv = fopen("accionistas.csv", "w");
  foreach ($groups as  $g) {

    $row = array (utf8_decode($g['name']),utf8_decode($g['nationality']),$g['id']);
    fputcsv($csv, $row);
  }


});

Route::get('/pregunta6',function(){

  //pregunta 6
  $limit=0;

  $results = Afp::select('Functionary.name as functionaryName',
  'Afp.name  as afpName' ,
  'InvestedCompany.name as emisorName',
  'FunctionaryXAfp.beginDate as afpBegin',
  'FunctionaryXAfp.endDate as afpEnd',
  'FunctionaryXCompany.beginDate as emisorBegin',
  'FunctionaryXCompany.endDate as emisorEnd',
  'FunctionaryXAfp.position as afpPosition',
  'FunctionaryXCompany.position as emisorPosition')
  ->join('FunctionaryXAfp', 'Afp.id', '=', 'FunctionaryXAfp.afpId')
  ->join('Functionary', 'Functionary.id', '=', 'FunctionaryXAfp.functionaryId')
  ->join('FunctionaryXCompany','FunctionaryXAfp.functionaryId','=','FunctionaryXCompany.functionaryId')
  ->join('InvestedCompany', function($join) {
    $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id')
    ->whereNotNull('FunctionaryXAfp.endDate')
    ->on('FunctionaryXAfp.endDate' ,'<=','FunctionaryXCompany.beginDate');


  })->where('FunctionaryXCompany.id','>=',$limit)->get();

  $csv = fopen("New6.csv", "w");

  $dateManagment= new DateController();

  foreach ($results as $r){

    $period= $dateManagment->differenceDate($r['afpEnd'],$r['emisorBegin']);


    $row = array (utf8_decode($r['functionaryName']),utf8_decode($r['afpName']),utf8_decode($r['afpPosition']),
      utf8_decode($r['afpBegin']),utf8_decode($r['afpEnd']),
      utf8_decode($r['emisorName']) ,utf8_decode($r['emisorPosition']),
      utf8_decode($r['emisorBegin']), utf8_decode($dateManagment->currentDay($r['emisorEnd'])),utf8_decode($period));//$dateManagment->currentDay($r['emisorEnd'])

    fputcsv($csv, $row);

    //echo $r."<br>"."<br>";
  }
});

Route::get('/pregunta4',function(){

  //pregunta 4

      $limit=0;
      $results =InvestedCompany::select('Afp.name as afpName',
        'InvestedCompany.name as emisorName',
        'Shareholder.name as accionistaName',
        'ShareholderXCompany.participation as porcEmisor',
        'ShareholderXAfp.participation as porcAfp',
        'ShareholderXCompany.beginDate as emisorBegin',
        'ShareholderXCompany.endDate as emisorEnd',
        'ShareholderXAfp.beginDate as afpBegin',
        'ShareholderXAfp.endDate as afpEnd')
        ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
        ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
        ->join('ShareholderXAfp','ShareholderXAfp.shareholderId','=','Shareholder.id')
        ->join('Afp','Afp.id','=','ShareholderXAfp.afpId')
        ->where('ShareholderXCompany.id','>=',$limit)
        ->get();






  $csv = fopen("New4.csv", "w");

  $dateManagment= new DateController();
  $cont=0;
  foreach ($results as $r){

    $range= $dateManagment->intersectionDate(array('date1'=>$r['afpBegin'],
    'date2'=>$r['afpEnd'],'date3'=>$r['emisorBegin'],'date4'=> $r['emisorEnd']));



    if (!is_null($range)){

        $row = array (utf8_decode($r['afpName']),utf8_decode($r['emisorName']),utf8_decode($r['accionistaName']),
        $r['porcEmisor'],$r['porcAfp'],
        utf8_decode($range[0]),$dateManagment->currentDay(utf8_decode($range[1])));

      fputcsv($csv, $row);
      $cont++;
    }


    //echo $r."<br>"."<br>";
  }
  echo $cont;
});

Route::get('/pregunta7',function(){

  //pregunta 7
    $limit=0;
    $limit=0;

      $results =Afp::select('Functionary.name as functionaryName',
        'Afp.name  as afpName',
        'FunctionaryXAfp.position as afpPosition',
        'FunctionaryXAfp.beginDate as afpBegin',
        'FunctionaryXAfp.endDate as afpEnd',
        'InvestedCompanyXEconomicGroup.beginDate as beginAfp_econ',
        'InvestedCompanyXEconomicGroup.endDate as endAfp_econ',
        'InvestedCompany.name as emisorName',
        'FunctionaryXCompany.position as emisorPosition',
        'FunctionaryXCompany.beginDate as emisorBegin',
        'FunctionaryXCompany.endDate as emisorEnd',
        'AfpXEconomicGroup.beginDate as beginEmisor_econ',
        'AfpXEconomicGroup.endDate as endEmisor_econ',
        'EconomicGroup.name as economicGroupName')

        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
        ->join('Functionary',function($join) {
            $join->on('Functionary.id','=','FunctionaryXAfp.functionaryId')
                  ->whereNotNull('FunctionaryXAfp.endDate');
            })
        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
        ->join('InvestedCompany',function($join) {
            $join->on('InvestedCompany.id','=','FunctionaryXCompany.companyId')
                ->on('FunctionaryXAfp.endDate' ,'<=','FunctionaryXCompany.beginDate');
            })
        ->join('InvestedCompanyXEconomicGroup','InvestedCompanyXEconomicGroup.companyId','=','InvestedCompany.id')
        ->join('EconomicGroup','EconomicGroup.id','=','InvestedCompanyXEconomicGroup.economicGroupId')
        ->join('AfpXEconomicGroup',function($join) {
            $join->on('AfpXEconomicGroup.economicGroupId','=','EconomicGroup.id')
                ->on('AfpXEconomicGroup.afpId' ,'=','Afp.id');
            })->where('InvestedCompanyXEconomicGroup.id','>=',$limit)
            ->where('FunctionaryXCompany.id','>=',$limit)->get();



  $csv = fopen("New7.csv", "w");

  $dateManagment= new DateController();
  $cont=0;
  foreach ($results as $r){

    $rangeAfpXEconGroup= $dateManagment->intersectionDate(array('date1'=>$r['afpBegin'],
    'date2'=>$r['afpEnd'],'date3'=>$r['beginAfp_econ'],'date4'=> $r['endAfp_econ']));

    $rangeEmisorXEconGroup= $dateManagment->intersectionDate(array('date1'=>$r['emisorBegin'],
      'date2'=> $r['emisorEnd'],  'date3'=>$r['beginEmisor_econ'],  'date4'=>$r['endEmisor_econ']));

    if (!is_null( $rangeAfpXEconGroup) && !is_null(  $rangeEmisorXEconGroup)){
      $period= $dateManagment->differenceDate($rangeAfpXEconGroup[1],$rangeEmisorXEconGroup[0]);


      $row = array (utf8_decode($r['functionaryName']),utf8_decode($r['afpName']),utf8_decode($r['afpPosition']),
        utf8_decode($rangeAfpXEconGroup[0]),utf8_decode($rangeAfpXEconGroup[1]),
        utf8_decode($r['emisorName']) ,utf8_decode($r['emisorPosition']),
        utf8_decode($rangeEmisorXEconGroup[0]),$dateManagment->currentDay(utf8_decode($rangeEmisorXEconGroup[1])),
        utf8_decode($r['economicGroupName']),utf8_decode($period));//$dateManagment->currentDay($r['emisorEnd'])

      fputcsv($csv, $row);
      $cont++;
    }


    //echo $r."<br>"."<br>";
  }
  echo $cont;
});



Route::get('/pregunta1',function(){

  //pregunta 1

  $limit=0;
  $results = InvestedCompany::select('InvestedCompany.name as emisorName',
              'Afp.name as afpName','EconomicGroup.name as econGroupName',
              'InvestedCompanyXEconomicGroup.beginDate as emisorBegin',
              'InvestedCompanyXEconomicGroup.endDate as emisorEnd' ,
              'AfpXEconomicGroup.beginDate as afpBegin','AfpXEconomicGroup.endDate as afpEnd')
              ->join('InvestedCompanyXEconomicGroup','InvestedCompanyXEconomicGroup.companyId','=','InvestedCompany.id')
              ->join('EconomicGroup','InvestedCompanyXEconomicGroup.economicGroupId','=','EconomicGroup.id')
              ->join('AfpXEconomicGroup','AfpXEconomicGroup.economicGroupId','=','InvestedCompanyXEconomicGroup.economicGroupId')
              ->join('Afp', function($join) {
                $join->on('AfpXEconomicGroup.afpId','=','Afp.id');

              })->where('InvestedCompanyXEconomicGroup.id','>=',$limit)->get();

  //En results tengo todos los registros que al menos podrían tener las respuestas

  $csv = fopen("New1.csv", "w"); //abrimos el archivo donde escribiremos la pregunta 1
  $cont=0;
  $dateManagment= new DateController();
  foreach ($results as $r){


    $range=$dateManagment->intersectionDate(array('date1'=>$r['emisorBegin'],'date2'=>$r['emisorEnd'],
                                           'date3'=>$r['afpBegin'],'date4'=>$r['afpEnd']));

    if(!is_null($range)){
      $row = array (utf8_decode($r['emisorName']),utf8_decode($r['afpName']) ,
          utf8_decode($r['econGroupName']),utf8_decode($range[0]),
          utf8_decode($dateManagment->currentDay($range[1])) );

      fputcsv($csv, $row);
      $cont++;
    }


  }

  return $cont;

});





Route::get('/pregunta2',function(){

  //pregunta 2
  $limit=0;
  $results = Afp::select('Functionary.name as functionaryName',
              'Afp.name as afpName','FunctionaryXAfp.position as afpPosition',
              'InvestedCompany.name as emisorName', 'FunctionaryXCompany.position as emisorPosition',
              'FunctionaryXAfp.beginDate as afpBegin', 'FunctionaryXAfp.endDate as afpEnd',
              'FunctionaryXCompany.beginDate as emisorBegin', 'FunctionaryXCompany.endDate as emisorEnd')
              ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
              ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
              ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
              ->join('InvestedCompany', function($join) {
                $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id');

              })->where('FunctionaryXCompany.id','>=',$limit)->get();



//En results tengo todos los registros que al menos podrían tener las respuestas


  $csv = fopen("New2.csv", "w"); //abrimos el archivo donde escribiremos la pregunta 1
  $cont=0;
  $dateManagment= new DateController();
  foreach ($results as $r){


    $range=$dateManagment->intersectionDate(array('date1'=>$r['afpBegin'],'date2'=>$r['afpEnd'],
                            'date3'=>$r['emisorBegin'],'date4'=>$r['emisorEnd']));



    if(!is_null($range)){
      $row = array (utf8_decode($r['functionaryName']),
          utf8_decode($r['afpName']),
          utf8_decode($r['afpPosition']),
          utf8_decode($r['emisorName']) ,
          utf8_decode($r['emisorPosition']),
          utf8_decode($range[0]),
          utf8_decode($dateManagment->currentDay($range[1])),
          utf8_decode($dateManagment->differenceDate($r['afpBegin'],$r['emisorBegin'])) );

      fputcsv($csv, $row);
      $cont++;
    }


  }

  return $cont;

});




Route::get('/pregunta3',function(){

  //pregunta 3

  $limit=0;
  $limit2=0;
  $results=Afp::select('Functionary.name as functionaryName',
                        'Afp.name as afpName',
                        'FunctionaryXAfp.position as afpPosition',
                        'InvestedCompany.name as emisorName',
                        'FunctionaryXCompany.position as emisorPosition',
                        'EconomicGroup.name as economicGroupName',
                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',
                        'InvestedCompanyXEconomicGroup.beginDate as beginEmisor_econ',
                        'InvestedCompanyXEconomicGroup.endDate as endEmisor_econ',
                        'AfpXEconomicGroup.beginDate as beginAfp_econ',
                        'AfpXEconomicGroup.endDate as endAfp_econ'
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('InvestedCompanyXEconomicGroup','InvestedCompanyXEconomicGroup.companyId','=','InvestedCompany.id')
                        ->join('EconomicGroup','EconomicGroup.id','=','InvestedCompanyXEconomicGroup.economicGroupId')
                        ->join('AfpXEconomicGroup', function($join) {
                          $join->on('AfpXEconomicGroup.economicGroupId','=','InvestedCompanyXEconomicGroup.economicGroupId')
                               ->on('Afp.id','=','AfpXEconomicGroup.afpId');

                        })->where('InvestedCompanyXEconomicGroup.id','>=',$limit)
                        ->where('FunctionaryXCompany.id','>=',$limit2)->get();


//En results tengo todos los registros que al menos podrían tener las respuestas


  $csv = fopen("New3.csv", "w"); //abrimos el archivo donde escribiremos la pregunta 1
  $cont=0;
  $dateManagment= new DateController();
  foreach ($results as $r){

    $rangeFunctionary=$dateManagment->intersectionDate(array('date1'=>$r['afpBegin'],'date2'=>$r['afpEnd'],
                            'date3'=>$r['emisorBegin'],'date4'=>$r['emisorEnd']));

    $rangeEconomicGroup=$dateManagment->intersectionDate(array('date1'=>$r['beginEmisor_econ'],
                                      'date2'=>$r['endEmisor_econ'],
                                      'date3'=>$r['beginAfp_econ'],
                                      'date4'=>$r['endAfp_econ']));


    if(!is_null($rangeFunctionary) && !is_null($rangeEconomicGroup)){
      $range=$dateManagment->intersectionDate(array('date1'=>$rangeFunctionary[0],
                              'date2'=>$rangeFunctionary[1],
                              'date3'=>$rangeEconomicGroup[0],
                              'date4'=>$rangeEconomicGroup[1]));

      if (!is_null($range)){

        $row = array (utf8_decode($r['functionaryName']),
            utf8_decode($r['afpName']),
            utf8_decode($r['afpPosition']),
            utf8_decode($r['emisorName']) ,
            utf8_decode($r['emisorPosition']),
            utf8_decode($r['economicGroupName']),
            utf8_decode($range[0]),
            utf8_decode($dateManagment->currentDay($range[1])),
            utf8_decode($dateManagment->differenceDate($r['afpBegin'],$r['emisorBegin'])) );

        fputcsv($csv, $row);
        $cont++;
      }

    }


  }

  return $cont;

});



Route::get('/pregunta5',function(){

  //pregunta 5
  $limit=0;
  $limit2=0;
  $limit3=0;
  $results=Afp::select('Functionary.name as functionaryName',
                        'Afp.name as afpName',
                        'FunctionaryXAfp.position as afpPosition',
                        'InvestedCompany.name as emisorName',
                        'FunctionaryXCompany.position as emisorPosition',
                        'EconomicGroup.name as economicGroupName',
                        'Shareholder.name as accionista',
                        'ShareholderXCompany.participation as porcEmisor',
                        'ShareholderXAfp.participation as porcAfp',

                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',

                        'InvestedCompanyXEconomicGroup.beginDate as beginEmisor_econ',
                        'InvestedCompanyXEconomicGroup.endDate as endEmisor_econ',
                        'AfpXEconomicGroup.beginDate as beginAfp_econ',
                        'AfpXEconomicGroup.endDate as endAfp_econ',

                        'ShareholderXCompany.beginDate as beginEmisor_acc',
                        'ShareholderXCompany.endDate as endEmisor_acc',
                        'ShareholderXAfp.beginDate as beginAfp_acc',
                        'ShareholderXAfp.endDate as endAfp_acc '
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('InvestedCompanyXEconomicGroup','InvestedCompanyXEconomicGroup.companyId','=','InvestedCompany.id')
                        ->join('EconomicGroup','EconomicGroup.id','=','InvestedCompanyXEconomicGroup.economicGroupId')
                        ->join('AfpXEconomicGroup', function($join) {
                          $join->on('AfpXEconomicGroup.economicGroupId','=','InvestedCompanyXEconomicGroup.economicGroupId')
                               ->on('Afp.id','=','AfpXEconomicGroup.afpId');

                        })
                        ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
                        ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
                        ->join('ShareholderXAfp',function($join) {
                          $join->on('ShareholderXAfp.shareholderId','=','Shareholder.id')
                               ->on('Afp.id','=','ShareholderXAfp.afpId');

                        })
                        ->where('ShareholderXCompany.id','>=',$limit)
                        ->where('FunctionaryXCompany.id','>=',$limit2)
                        ->where('InvestedCompanyXEconomicGroup.id','>=',$limit3)
                        ->get();


//En results tengo todos los registros que al menos podrían tener las respuestas


  $csv = fopen("New5.csv", "w"); //abrimos el archivo donde escribiremos la pregunta 5
  $cont=0;
  $dateManagment= new DateController();
  foreach ($results as $r){

    $rangeFunctionary=$dateManagment->intersectionDate(array('date1'=>$r['afpBegin'],'date2'=>$r['afpEnd'],
                            'date3'=>$r['emisorBegin'],'date4'=>$r['emisorEnd']));

    $rangeEconomicGroup=$dateManagment->intersectionDate(array('date1'=>$r['beginEmisor_econ'],
                                      'date2'=>$r['endEmisor_econ'],
                                      'date3'=>$r['beginAfp_econ'],
                                      'date4'=>$r['endAfp_econ']));

    $rangeShareholder=$dateManagment->intersectionDate(array('date1'=>$r['beginEmisor_acc'],
                                      'date2'=>$r['endEmisor_acc'],
                                      'date3'=>$r['beginAfp_acc'],
                                      'date4'=>$r['endAfp_acc']));


    if(!is_null($rangeFunctionary) && !is_null($rangeEconomicGroup) && !is_null($rangeShareholder) ){

      $temportalRange=$dateManagment->intersectionDate(array('date1'=>$rangeFunctionary[0],
                              'date2'=>$rangeFunctionary[1],
                              'date3'=>$rangeEconomicGroup[0],
                              'date4'=>$rangeEconomicGroup[1]));

      if (!is_null($temportalRange)){

        $range=$dateManagment->intersectionDate(array('date1'=>$temportalRange[0],
                                'date2'=>$temportalRange[1],
                                'date3'=>$rangeShareholder[0],
                                'date4'=>$rangeShareholder[1]));


          if(!is_null($range)){
            $row = array (utf8_decode($r['functionaryName']),
                utf8_decode($r['afpName']),
                utf8_decode($r['afpPosition']),
                utf8_decode($r['emisorName']) ,
                utf8_decode($r['emisorPosition']),
                utf8_decode($r['economicGroupName']),
                utf8_decode($r['accionista']),
                $r['porcEmisor'],$r['porcAfp'],
                utf8_decode($range[0]),
                utf8_decode($dateManagment->currentDay($range[1])),
                utf8_decode($dateManagment->differenceDate($r['afpBegin'],$r['emisorBegin'])) );

            fputcsv($csv, $row);
            $cont++;

          }
      }

    }


  }

  return $cont;

});




Route::get('/pregunta8',function(){

  //pregunta 8
  $limit=0;
  $results = Afp::select('Functionary.name as functionaryName',
  'Afp.name  as afpName' ,
  'InvestedCompany.name as emisorName',
  'FunctionaryXAfp.beginDate as afpBegin',
  'FunctionaryXAfp.endDate as afpEnd',
  'FunctionaryXCompany.beginDate as emisorBegin',
  'FunctionaryXCompany.endDate as emisorEnd',
  'FunctionaryXAfp.position as afpPosition',
  'FunctionaryXCompany.position as emisorPosition')
  ->join('FunctionaryXAfp', 'Afp.id', '=', 'FunctionaryXAfp.afpId')
  ->join('Functionary', 'Functionary.id', '=', 'FunctionaryXAfp.functionaryId')
  ->join('FunctionaryXCompany','FunctionaryXAfp.functionaryId','=','FunctionaryXCompany.functionaryId')
  ->join('InvestedCompany', function($join) {
    $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id')
    ->whereNotNull('FunctionaryXCompany.endDate')
    ->on('FunctionaryXCompany.endDate' ,'<=','FunctionaryXAfp.beginDate');
  })->where('FunctionaryXCompany.id','>=',$limit)->get();



  $csv = fopen("New8.csv", "w");


  $dateManagment= new DateController();

  foreach ($results as $r){

      $period= $dateManagment->differenceDate($r['emisorEnd'],$r['afpBegin']);

    $row = array (utf8_decode($r['functionaryName']),utf8_decode($r['emisorName']) , utf8_decode($r['emisorPosition']),
        utf8_decode($r['emisorBegin']),utf8_decode($r['emisorEnd']) ,
        utf8_decode($r['afpName']), utf8_decode($r['afpPosition']),
        utf8_decode( $r['afpBegin']), utf8_decode($dateManagment->currentDay($r['afpEnd'])),
        utf8_decode($period));
    fputcsv($csv, $row);
    //echo $r."<br>"."<br>";
  }

});

Route::get('/insertFunctionary',function(){
  if (($entrada = fopen('files/funcionarios.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {


        $func=new Functionary();
        $func->name=utf8_encode($data[0]);

        $func->save();
      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});




Route::get('/luisa2',function(){

  try{
    $csv = fopen("luisa2.csv", "w");
  }catch(Exception $e){

  }
  if (($entrada = fopen('Consulta.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {
        echo "Linea<br>";
        $item=$data[0];  $emisor=utf8_encode($data[1]); //es una cadena
        $emisorId=$data[2]; $sanctionDate=$data[3];
        //return $sanctionDate;





        $matches=InvestmentRound::join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
        ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
        ->join ('Afp','Afp.id', '=', 'Found.afpId')
        ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
        ->where('InvestedCompany.name','=',$emisor)
        ->select('InvestedCompany.name as company',
                  'Afp.name as afp',
                  'InvestmentRound.year as year',
                  'InvestmentRound.month as month')
        ->selectRaw('sum("InvestmentRound"."mount") as total')
        ->groupBy('InvestedCompany.id')->groupBy('Afp.id')
        ->groupBy('InvestmentRound.year')->groupBy('InvestmentRound.month')
        ->orderBy('InvestedCompany.name')->orderBy('Afp.name')
        ->orderBy('InvestmentRound.year')->orderBy('InvestmentRound.month')

        ->get();


        $valuesSanctionDate =explode("-",$sanctionDate);
        $yearSanction=$valuesSanctionDate[0];
        $monthSanction=$valuesSanctionDate[1];

        $monthSanctionBefore=$monthSanction-1;
        $yearSanctionBefore=$yearSanction;
        if ($monthSanctionBefore==0){
          $monthSanctionBefore=12;
          $yearSanctionBefore=$yearSanctionBefore-1;
        }


        $sanctionDateBefore=$yearSanctionBefore *100+$monthSanctionBefore;

        $date=new DateController();

        $sanctionDate=$date->toYearMonth($sanctionDate);
        //return $sanctionDate;

        $matches=$matches->where('total','>','0')
        ->where('year_month','>=',$sanctionDateBefore);


        return $matches;
        foreach ($matches as $m) {
          $estado='Después';
          if ($m['year_month']==$sanctionDate)
            $estado='Durante';
          else if($m['year_month']==$sanctionDateBefore)
            $estado='Antes';

          $row = array (
                $item,
                $sanctionDate,
                utf8_decode($m['company']),
                utf8_decode($m['afp']),
                $m['year'],
                $m['month'],
                $m['total'],
                utf8_decode($estado));
          fputcsv($csv, $row);

        }










      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }




});




Route::get('/hard1',function(){

  $csv = fopen("hard1despues.csv", "w");
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
            )->orderBy('Functionary.name')->get();//->count();



            foreach ($matches as $m) {
              # code...
              $functionary=$m['functionary'];
              $afpId=$m['afpId']; $companyId=$m['emisorId'];
              $afp=$m['afp']; $company=$m['emisor'];

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
                $row = array (utf8_decode($functionary),utf8_decode($coincidence),utf8_decode($company) ,
                    utf8_decode($afp),utf8_decode($r['found']),
                    utf8_decode($r['instrument']),
                  $r['year'],$r['month'],utf8_decode($date->intToMonth($r['month'])),
                  $r['mount'],$r['mountPercent']);
                fputcsv($csv, $row);

              }
            }

  fclose($csv);
});

/*========================================================================*/

Route::get('/hard1b',function(){

  //pregunta dificil 1
  $csv = fopen("Hard1b.csv", "w");

  $date=new DateController();
  $matches = Afp::select('Functionary.name as functionaryName',
              'Afp.id as afpId',
              'InvestedCompany.id as emisorId',
              'FunctionaryXAfp.beginDate as afpBegin', 'FunctionaryXAfp.endDate as afpEnd',
              'FunctionaryXCompany.beginDate as emisorBegin', 'FunctionaryXCompany.endDate as emisorEnd')
              ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
              ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
              ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
              ->join('InvestedCompany', function($join) {
                $join->on('FunctionaryXCompany.companyId','=','InvestedCompany.id');

              })
              ->get();


  foreach ($matches as $match ) {
    $range=$date->intersectionDate(array('date1'=>$match['afpBegin'],'date2'=>$match['afpEnd'],
                                    'date3'=>$match['emisorBegin'],'date4'=>$match['emisorEnd']));
    if (!is_null($range)){
      $functionary=$match['functionaryName'];
      $companyId=$match['emisorId'];
      $afpId=$match['afpId'];

      $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


      $filter1 =explode("-",$range[0]);
      $filter1=$filter1[0]*100+$filter1[1];
      $filter2=explode("-",$range[1]);
      $filter2=$filter2[0]*100+$filter2[1];


      $results=InvestmentRound::select('InvestedCompany.name as emisor', 'Found.name as fondo','Afp.name as afp',
      'FinancialInstrument.name as instrumento', 'InvestmentRound.mount as monto',
      'InvestmentRound.mountPercent as montoPorcentaje', 'InvestmentRound.year as year',
      'InvestmentRound.month as month'
      )
      ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
      ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
      ->join ('Afp','Afp.id', '=', 'Found.afpId')
      ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
      ->where('Afp.id','<>',$afpId)
      ->where('InvestedCompany.id','=',$companyId)
      ->where('InvestedCompany.scope','=','Nacional')
      ->orderBy('InvestedCompany.name')
      ->orderBy('Afp.name')
      ->orderBy('Found.name')
      ->orderBy('FinancialInstrument.name')
      ->orderBy('InvestmentRound.year')
      ->orderBy('InvestmentRound.month')
      ->get();


      $results= $results->where('year_month','>=',$filter1)
                    ->where('year_month','<=',$filter2);

      foreach ($results as $key => $value) {

        $company=$value['emisor'];
        $found=$value['fondo'];
        $instrument=$value['instrumento'];
        $mount=$value['monto'];
        $mountPercent=$value['montoPorcentaje'];
        $year=$value['year'];
        $month=$value['month'];
        $afp=$value['afp'];
        $row = array (utf8_decode($functionary),
        utf8_decode($coincidence),
        utf8_decode($company) ,
            utf8_decode($afp),
            utf8_decode($found),
            utf8_decode($instrument),
            $mount,$mountPercent,$year,$month,
          utf8_decode($date->intToMonth($month)));
        fputcsv($csv, $row);

      }
    }
  }
  fclose($csv);
});

Route::get('/hard1Instr','ReportController@hard1Instr');

Route::get('/reporte','ReportController@report');

/*===========================================================================*/



Route::get('/hard2',function(){


  $csv = fopen("hard2antes.csv", "w");

  $date=new DateController();

  $matches=Afp::select('Functionary.name as functionaryName',
                        'Afp.id as afpId',
                        'InvestedCompany.id as companyId',
                        'EconomicGroup.name as economicGroupName',
                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',
                        'InvestedCompanyXEconomicGroup.beginDate as beginEmisor_econ',
                        'InvestedCompanyXEconomicGroup.endDate as endEmisor_econ',
                        'AfpXEconomicGroup.beginDate as beginAfp_econ',
                        'AfpXEconomicGroup.endDate as endAfp_econ'
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('InvestedCompanyXEconomicGroup','InvestedCompanyXEconomicGroup.companyId','=','InvestedCompany.id')
                        ->join('EconomicGroup','EconomicGroup.id','=','InvestedCompanyXEconomicGroup.economicGroupId')
                        ->join('AfpXEconomicGroup', function($join) {
                          $join->on('AfpXEconomicGroup.economicGroupId','=','InvestedCompanyXEconomicGroup.economicGroupId')
                               ->on('Afp.id','=','AfpXEconomicGroup.afpId');

                        })
                        ->get();

  foreach ($matches as $match){

      $rangeFunctionary=$date->intersectionDate(array('date1'=>$match['afpBegin'],
                                                      'date2'=>$match['afpEnd'],
                                                      'date3'=>$match['emisorBegin'],
                                                      'date4'=>$match['emisorEnd']));

      $rangeEconomicGroup=$date->intersectionDate(array('date1'=>$match['beginEmisor_econ'],
                                                      'date2'=>$match['endEmisor_econ'],
                                                      'date3'=>$match['beginAfp_econ'],
                                                      'date4'=>$match['endAfp_econ']));


    if(!is_null($rangeFunctionary) && !is_null($rangeEconomicGroup)){
      $range=$date->intersectionDate(array('date1'=>$rangeFunctionary[0],
                              'date2'=>$rangeFunctionary[1],
                              'date3'=>$rangeEconomicGroup[0],
                              'date4'=>$rangeEconomicGroup[1]));


      if (!is_null($range)){

        $functionary=$match['functionaryName'];
        $companyId=$match['companyId'];
        $afpId=$match['afpId'];
        $economicGroup=$match['economicGroupName'];

        $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


        $filter1 =explode("-",$range[0]);
        $filter1=$filter1[0]*100+$filter1[1];
        $filter2=explode("-",$range[1]);
        $filter2=$filter2[0]*100+$filter2[1];

        $results=InvestmentRound::select('InvestedCompany.name as emisor', 'Found.name as fondo','Afp.name as afp',
                  'FinancialInstrument.name as instrumento', 'InvestmentRound.mount as monto',
                  'InvestmentRound.mountPercent as montoPorcentaje', 'InvestmentRound.year as year',
                  'InvestmentRound.month as month'
                  )
                  ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
                  ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
                  ->join ('Afp','Afp.id', '=', 'Found.afpId')
                  ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
                  ->where('Afp.id','=',$afpId)
                  ->where('InvestedCompany.id','=',$companyId)
                  ->where('InvestedCompany.scope','=','Nacional')
                  ->orderBy('InvestedCompany.name')
                  ->orderBy('Afp.name')
                  ->orderBy('Found.name')
                  ->orderBy('FinancialInstrument.name')
                  ->orderBy('InvestmentRound.year')
                  ->orderBy('InvestmentRound.month')
                  ->get();

    $results= $results->where('year_month','>',$filter2);

    /*->where('year_month','>=',$filter1)
                  ->where('year_month','<=',$filter2);*/
      //->where('year_month','<',$filter1);

      //->where('year_month','>',$filter2);

    foreach ($results as $key => $value) {

      $company=$value['emisor'];
      $found=$value['fondo'];
      $instrument=$value['instrumento'];
      $mount=$value['monto'];
      $mountPercent=$value['montoPorcentaje'];
      $year=$value['year'];
      $month=$value['month'];
      $afp=$value['afp'];

      $row = array (utf8_decode($functionary),
          utf8_decode($economicGroup),
          utf8_decode($coincidence),
          utf8_decode($company) ,
          utf8_decode($afp),
          utf8_decode($found),
          utf8_decode($instrument),
              $mount,$mountPercent,$year,$month,
            utf8_decode($date->intToMonth($month))
          );
          fputcsv($csv, $row);

    }
  }

}

}

  fclose($csv);
});
/*===========================================================================*/
Route::get('/hard2b',function(){

  $csv = fopen("Hard2b.csv", "w");

  $date=new DateController();

  $matches=Afp::select('Functionary.name as functionaryName',
                        'Afp.id as afpId',
                        'InvestedCompany.id as companyId',
                        'EconomicGroup.name as economicGroupName',
                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',
                        'InvestedCompanyXEconomicGroup.beginDate as beginEmisor_econ',
                        'InvestedCompanyXEconomicGroup.endDate as endEmisor_econ',
                        'AfpXEconomicGroup.beginDate as beginAfp_econ',
                        'AfpXEconomicGroup.endDate as endAfp_econ'
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('InvestedCompanyXEconomicGroup','InvestedCompanyXEconomicGroup.companyId','=','InvestedCompany.id')
                        ->join('EconomicGroup','EconomicGroup.id','=','InvestedCompanyXEconomicGroup.economicGroupId')
                        ->join('AfpXEconomicGroup', function($join) {
                          $join->on('AfpXEconomicGroup.economicGroupId','=','InvestedCompanyXEconomicGroup.economicGroupId')
                               ->on('Afp.id','=','AfpXEconomicGroup.afpId');

                        })->get();

  foreach ($matches as $match){

      $rangeFunctionary=$date->intersectionDate(array('date1'=>$match['afpBegin'],
                                                      'date2'=>$match['afpEnd'],
                                                      'date3'=>$match['emisorBegin'],
                                                      'date4'=>$match['emisorEnd']));

      $rangeEconomicGroup=$date->intersectionDate(array('date1'=>$match['beginEmisor_econ'],
                                                      'date2'=>$match['endEmisor_econ'],
                                                      'date3'=>$match['beginAfp_econ'],
                                                      'date4'=>$match['endAfp_econ']));


    if(!is_null($rangeFunctionary) && !is_null($rangeEconomicGroup)){
      $range=$date->intersectionDate(array('date1'=>$rangeFunctionary[0],
                              'date2'=>$rangeFunctionary[1],
                              'date3'=>$rangeEconomicGroup[0],
                              'date4'=>$rangeEconomicGroup[1]));

      if (!is_null($range)){

        $functionary=$match['functionaryName'];
        $companyId=$match['companyId'];
        $afpId=$match['afpId'];
        $economicGroup=$match['economicGroupName'];

        $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


        $filter1 =explode("-",$range[0]);
        $filter1=$filter1[0]*100+$filter1[1];
        $filter2=explode("-",$range[1]);
        $filter2=$filter2[0]*100+$filter2[1];

        $results=InvestmentRound::select('InvestedCompany.name as emisor', 'Found.name as fondo','Afp.name as afp',
        'FinancialInstrument.name as instrumento', 'InvestmentRound.mount as monto',
        'InvestmentRound.mountPercent as montoPorcentaje', 'InvestmentRound.year as year',
        'InvestmentRound.month as month'
        )
        ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
        ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
        ->join ('Afp','Afp.id', '=', 'Found.afpId')
        ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
        ->where('Afp.id','<>',$afpId)
        ->where('InvestedCompany.id','=',$companyId)
        ->where('InvestedCompany.scope','=','Nacional')
        ->orderBy('InvestedCompany.name')
        ->orderBy('Afp.name')
        ->orderBy('Found.name')
        ->orderBy('FinancialInstrument.name')
        ->orderBy('InvestmentRound.year')
        ->orderBy('InvestmentRound.month')
        ->get();

    $results= $results->where('year_month','>=',$filter1)
                  ->where('year_month','<=',$filter2);

    foreach ($results as $key => $value) {

      $company=$value['emisor'];
      $found=$value['fondo'];
      $instrument=$value['instrumento'];
      $mount=$value['monto'];
      $mountPercent=$value['montoPorcentaje'];
      $year=$value['year'];
      $month=$value['month'];
      $afp=$value['afp'];

      $row = array (utf8_decode($functionary),
          utf8_decode($economicGroup),
          utf8_decode($coincidence),
          utf8_decode($company) ,
          utf8_decode($afp),
          utf8_decode($found),
          utf8_decode($instrument),
              $mount,$mountPercent,$year,$month,
            utf8_decode($date->intToMonth($month))
          );
          fputcsv($csv, $row);

        }
    }

  }

}

  fclose($csv);
});


Route::get('/hard2Instr',function(){

  //pregunta dificil 1
  $csv = fopen("hard2Instr.csv", "w");

  $date=new DateController();

  $matches=Afp::select('Functionary.name as functionaryName',
                        'Afp.id as afpId','Afp.name as afp',
                        'InvestedCompany.id as companyId',
                        'InvestedCompany.name as emisor',
                        'EconomicGroup.name as economicGroupName',
                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',
                        'InvestedCompanyXEconomicGroup.beginDate as beginEmisor_econ',
                        'InvestedCompanyXEconomicGroup.endDate as endEmisor_econ',
                        'AfpXEconomicGroup.beginDate as beginAfp_econ',
                        'AfpXEconomicGroup.endDate as endAfp_econ'
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('InvestedCompanyXEconomicGroup','InvestedCompanyXEconomicGroup.companyId','=','InvestedCompany.id')
                        ->join('EconomicGroup','EconomicGroup.id','=','InvestedCompanyXEconomicGroup.economicGroupId')
                        ->join('AfpXEconomicGroup', function($join) {
                          $join->on('AfpXEconomicGroup.economicGroupId','=','InvestedCompanyXEconomicGroup.economicGroupId')
                               ->on('Afp.id','=','AfpXEconomicGroup.afpId');

                        })
                        ->get();

  foreach ($matches as $match){

      $rangeFunctionary=$date->intersectionDate(array('date1'=>$match['afpBegin'],
                                                      'date2'=>$match['afpEnd'],
                                                      'date3'=>$match['emisorBegin'],
                                                      'date4'=>$match['emisorEnd']));

      $rangeEconomicGroup=$date->intersectionDate(array('date1'=>$match['beginEmisor_econ'],
                                                      'date2'=>$match['endEmisor_econ'],
                                                      'date3'=>$match['beginAfp_econ'],
                                                      'date4'=>$match['endAfp_econ']));


    if(!is_null($rangeFunctionary) && !is_null($rangeEconomicGroup)){
      $range=$date->intersectionDate(array('date1'=>$rangeFunctionary[0],
                              'date2'=>$rangeFunctionary[1],
                              'date3'=>$rangeEconomicGroup[0],
                              'date4'=>$rangeEconomicGroup[1]));

      if (!is_null($range)){

        $functionary=$match['functionaryName'];
        $companyId=$match['companyId'];
        $afpId=$match['afpId'];
        $economicGroup=$match['economicGroupName'];
        $company=$match['emisor'];
        $afp=$match['afp'];

        $coincidence= "[".$range[0]." / ".$range[1]."]";


        $filter1 =explode("-",$range[0]);
        $filter1=$filter1[0]*100+$filter1[1];
        $filter2=explode("-",$range[1]);
        $filter2=$filter2[0]*100+$filter2[1];

        $query=InvestmentRound::select('InvestmentRound.financialinstrumentId', 'InvestmentRound.year as year',
        'InvestmentRound.month as month'
        )
        ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
        ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
        ->join ('Afp','Afp.id', '=', 'Found.afpId')
        ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
        ->where('Afp.id','=',$afpId)
        ->where('InvestedCompany.id','=',$companyId)
        ->where('InvestedCompany.scope','=','Nacional')
        ->orderBy('InvestedCompany.name')
        ->orderBy('Afp.name')
        ->orderBy('Found.name')
        ->orderBy('FinancialInstrument.name')
        ->orderBy('InvestmentRound.year')
        ->orderBy('InvestmentRound.month')
        ->get();


        //$resultsBeforeInstrument=array_map(create_function('$o', 'return $o->instrument;'), $resultsBefore);
        //$resultsBeforeInstrument = array_column($resultsBefore, 'instrumento');

        $now=$query->where('year_month','>=',$filter1)
            ->where('year_month','<=',$filter2);

        $before=$query->where('year_month','<',$filter1);

        $after=$query->where('year_month','>',$filter2);



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
                    utf8_decode($economicGroup),
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
          utf8_decode($economicGroup),
                        utf8_decode($coincidence),

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
                        utf8_decode($economicGroup),
                        utf8_decode($coincidence),
                        utf8_decode($company) ,
                        utf8_decode($afp),
                        utf8_decode($i['name']),
                          utf8_decode('Después')
                      );
          fputcsv($csv, $row);
        }

  }

}

}

  fclose($csv);
});






Route::get('/hard3',function(){


  $csv = fopen("hard3despues.csv", "w");

  $date=new DateController();

  $matches = InvestedCompany::select('Afp.id as afpId',
              'InvestedCompany.id as companyId',
              'Shareholder.name as accionistaName',
              'ShareholderXCompany.beginDate as emisorBegin ',
              'ShareholderXCompany.endDate as emisorEnd ',
              'ShareholderXAfp.beginDate as afpBegin',
              'ShareholderXAfp.endDate as afpEnd')
              ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
              ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
              ->join('ShareholderXAfp','ShareholderXAfp.shareholderId','=','Shareholder.id')
              ->join('Afp','Afp.id','=','ShareholderXAfp.afpId')
              ->get();


  foreach ($matches as $match){

      $range=$date->intersectionDate(array('date1'=>$match['afpBegin'],'date2'=>$match['afpEnd'],
                                    'date3'=>$match['emisorBegin'],'date4'=>$match['emisorEnd']));


      if (!is_null($range)){

        $companyId=$match['emisorId'];
        $afpId=$match['afpId'];
        $accionista=$match['accionistaName'];

        $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


        $filter1 =explode("-",$range[0]);
        $filter1=$filter1[0]*100+$filter1[1];
        $filter2=explode("-",$range[1]);
        $filter2=$filter2[0]*100+$filter2[1];

        $results=InvestmentRound::select('InvestedCompany.name as emisor', 'Found.name as fondo','Afp.name as afp',
        'FinancialInstrument.name as instrumento', 'InvestmentRound.mount as monto',
        'InvestmentRound.mountPercent as montoPorcentaje', 'InvestmentRound.year as year',
        'InvestmentRound.month as month'
        )
        ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
        ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
        ->join ('Afp','Afp.id', '=', 'Found.afpId')
        ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
        ->where('Afp.id','=',$afpId)
        ->where('InvestedCompany.id','=',$companyId)
        ->where('InvestedCompany.scope','=','Nacional')
        ->orderBy('InvestedCompany.name')
        ->orderBy('Afp.name')
        ->orderBy('Found.name')
        ->orderBy('FinancialInstrument.name')
        ->orderBy('InvestmentRound.year')
        ->orderBy('InvestmentRound.month')
        ->get();


  //echo $filter1."-". $results['year_month']."-".$filter2."<br>";
    //echo $results->count()."a<br>";
    $results= $results->where('year_month','<',$filter1);
    //echo $results->count()."<br>";
    //->where('year_month','>',$filter2);
    /*->where('year_month','>=',$filter1)
                  ->where('year_month','<=',$filter2);*/
      //->where('year_month','<',$filter1);

    foreach ($results as $key => $value) {

      $company=$value['emisor'];
      $found=$value['fondo'];
      $instrument=$value['instrumento'];
      $mount=$value['monto'];
      $mountPercent=$value['montoPorcentaje'];
      $year=$value['year'];
      $month=$value['month'];
      $afp=$value['afp'];

      $row = array (

          utf8_decode($accionista),
          utf8_decode($coincidence),
          utf8_decode($company) ,
          utf8_decode($afp),
          utf8_decode($found),
          utf8_decode($instrument),
              $mount,$mountPercent,$year,$month,
                utf8_decode($date->intToMonth($month))
          );
          fputcsv($csv, $row);
          $cont++;
    }
  }

}



  fclose($csv);
});

Route::get('/hard3b',function(){


  $csv = fopen("Hard3b.csv", "w");

  $date=new DateController();

  $matches =InvestedCompany::select('Afp.id as afpId',
    'InvestedCompany.id as companyId',
    'Shareholder.name as accionistaName',
    'ShareholderXCompany.beginDate as  emisorBegin',
    'ShareholderXCompany.endDate as emisorEnd',
    'ShareholderXAfp.beginDate as afpBegin ',
    'ShareholderXAfp.endDate as afpEnd  ')
    ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
    ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
    ->join('ShareholderXAfp','ShareholderXAfp.shareholderId','=','Shareholder.id')
    ->join('Afp','Afp.id','=','ShareholderXAfp.afpId')
    ->get();

  foreach ($matches as $match){

      $range=$date->intersectionDate(array('date1'=>$match['afpBegin'],'date2'=>$match['afpEnd'],
                                    'date3'=>$match['emisorBegin'],'date4'=>$match['emisorEnd']));

      if (!is_null($range)){

        $companyId=$match['emisorId'];
        $afpId=$match['afpId'];
        $accionista=$match['accionistaName'];

        $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


        $filter1 =explode("-",$range[0]);
        $filter1=$filter1[0]*100+$filter1[1];
        $filter2=explode("-",$range[1]);
        $filter2=$filter2[0]*100+$filter2[1];

        $results=InvestmentRound::select('InvestedCompany.name as emisor', 'Found.name as fondo','Afp.name as afp',
        'FinancialInstrument.name as instrumento', 'InvestmentRound.mount as monto',
        'InvestmentRound.mountPercent as montoPorcentaje', 'InvestmentRound.year as year',
        'InvestmentRound.month as month'
        )
        ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
        ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
        ->join ('Afp','Afp.id', '=', 'Found.afpId')
        ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
        ->where('Afp.id','<>',$afpId)
        ->where('InvestedCompany.id','=',$companyId)
        ->where('InvestedCompany.scope','=','Nacional')
        ->orderBy('InvestedCompany.name')
        ->orderBy('Afp.name')
        ->orderBy('Found.name')
        ->orderBy('FinancialInstrument.name')
        ->orderBy('InvestmentRound.year')
        ->orderBy('InvestmentRound.month')
        ->get();

    $results= $results->where('year_month','>=',$filter1)
                  ->where('year_month','<=',$filter2);
    /*->where('year_month','>=',$filter1)
                  ->where('year_month','<=',$filter2);*/
      //->where('year_month','<',$filter1);

    foreach ($results as $key => $value) {

      $company=$value['emisor'];
      $found=$value['fondo'];
      $instrument=$value['instrumento'];
      $mount=$value['monto'];
      $mountPercent=$value['montoPorcentaje'];
      $year=$value['year'];
      $month=$value['month'];
      $afp=$value['afp'];

      $row = array (
          utf8_decode($afp),
          utf8_decode($company) ,
          utf8_decode($accionista),
          utf8_decode($coincidence),
          utf8_decode($found),
          utf8_decode($instrument),
              $mount,$mountPercent,$year,$month,
                utf8_decode($date->intToMonth($month))
          );
          fputcsv($csv, $row);
          $cont++;
    }
  }

}



  fclose($csv);
});





Route::get('/hard3Instr',function(){


  $csv = fopen("Hard3Instr.csv", "w");

  $date=new DateController();

  $matches =InvestedCompany::select('Afp.id as afpId','Afp.name as afp',
    'InvestedCompany.id as companyId','InvestedCompany.name as emisor',
    'Shareholder.name as accionistaName',
    'ShareholderXCompany.beginDate as  emisorBegin',
    'ShareholderXCompany.endDate as emisorEnd',
    'ShareholderXAfp.beginDate as afpBegin ',
    'ShareholderXAfp.endDate as afpEnd  ')
    ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
    ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
    ->join('ShareholderXAfp','ShareholderXAfp.shareholderId','=','Shareholder.id')
    ->join('Afp','Afp.id','=','ShareholderXAfp.afpId')
    ->get();



  foreach ($matches as $match){

      $range=$date->intersectionDate(array('date1'=>$match['afpBegin'],'date2'=>$match['afpEnd'],
                                    'date3'=>$match['emisorBegin'],'date4'=>$match['emisorEnd']));

      if (!is_null($range)){

        $companyId=$match['emisorId'];
        $afpId=$match['afpId'];
        $accionista=$match['accionistaName'];
        $afp=$match['afp'];
        $company=$match['emisor'];
        $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


        $filter1 =explode("-",$range[0]);
        $filter1=$filter1[0]*100+$filter1[1];
        $filter2=explode("-",$range[1]);
        $filter2=$filter2[0]*100+$filter2[1];

        $query=InvestmentRound::select('InvestmentRound.financialinstrumentId', 'InvestmentRound.year as year',
        'InvestmentRound.month as month'
        )
        ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
        ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
        ->join ('Afp','Afp.id', '=', 'Found.afpId')
        ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
        ->where('Afp.id','=',$afpId)
        ->where('InvestedCompany.id','=',$companyId)
        ->where('InvestedCompany.scope','=','Nacional')
        ->orderBy('InvestedCompany.name')
        ->orderBy('Afp.name')
        ->orderBy('Found.name')
        ->orderBy('FinancialInstrument.name')
        ->orderBy('InvestmentRound.year')
        ->orderBy('InvestmentRound.month')
        ->get();

        $now=$query->where('year_month','>=',$filter1)
            ->where('year_month','<=',$filter2);

        $before=$query->where('year_month','<',$filter1);

        $after=$query->where('year_month','>',$filter2);



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

          $row = array (utf8_decode($accionista),
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
          $row = array (   utf8_decode($accionista),
                        utf8_decode($coincidence),

                        utf8_decode($company) ,
                        utf8_decode($afp),
                        utf8_decode($i['name']),
                        'Nuevo'
                      );
          fputcsv($csv, $row);
        }

        foreach ($afters as $id) {


          $i=FinancialInstrument::find($id);
          $row = array (utf8_decode($accionista),
                        utf8_decode($coincidence),
                        utf8_decode($company) ,
                        utf8_decode($afp),
                        utf8_decode($i['name']),
                          utf8_decode('Después')
                      );
          fputcsv($csv, $row);
        }





  }

}



  fclose($csv);
});



/*=================================================================================================*/





Route::get('/hard4',function(){

  $csv = fopen("hard4despues.csv", "w");

  $date=new DateController();
  $matches=Afp::select('Functionary.name as functionaryName',
                        'Afp.id as afpId',
                        'FunctionaryXAfp.position as afpPosition',
                        'InvestedCompany.id as emisorId',
                        'FunctionaryXCompany.position as emisorPosition',
                        'Shareholder.name as accionista',
                        'ShareholderXCompany.participation as porcEmisor',
                        'ShareholderXAfp.participation as porcAfp',

                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',

                        'ShareholderXCompany.beginDate as beginEmisor_acc',
                        'ShareholderXCompany.endDate as endEmisor_acc',
                        'ShareholderXAfp.beginDate as beginAfp_acc',
                        'ShareholderXAfp.endDate as endAfp_acc '
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
                        ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
                        ->join('ShareholderXAfp',function($join) {
                          $join->on('ShareholderXAfp.shareholderId','=','Shareholder.id')
                               ->on('Afp.id','=','ShareholderXAfp.afpId');

                        })
                        ->get();



  foreach ($matches as $match){
      $rangeFunctionary=$date->intersectionDate(array('date1'=>$match['afpBegin'],'date2'=>$match['afpEnd'],
                              'date3'=>$match['emisorBegin'],'date4'=>$match['emisorEnd']));


      $rangeShareholder=$date->intersectionDate(array('date1'=>$match['beginEmisor_acc'],
                                        'date2'=>$match['endEmisor_acc'],
                                        'date3'=>$match['beginAfp_acc'],
                                        'date4'=>$match['endAfp_acc']));

      if(!is_null($rangeFunctionary) && !is_null($rangeShareholder)){
        $range=$date->intersectionDate(array('date1'=>$rangeFunctionary[0],
                                'date2'=>$rangeFunctionary[1],
                                'date3'=>$rangeShareholder[0],
                                'date4'=>$rangeShareholder[1]));

        if (!is_null($range)){

          $functionary=$match['functionaryName'];
          $companyId=$match['emisorId'];
          $afpId=$match['afpId'];
          $accionista=$match['accionista'];

          $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


          $filter1 =explode("-",$range[0]);
          $filter1=$filter1[0]*100+$filter1[1];
          $filter2=explode("-",$range[1]);
          $filter2=$filter2[0]*100+$filter2[1];

          $results=InvestmentRound::select('InvestedCompany.name as emisor', 'Found.name as fondo','Afp.name as afp',
          'FinancialInstrument.name as instrumento', 'InvestmentRound.mount as monto',
          'InvestmentRound.mountPercent as montoPorcentaje', 'InvestmentRound.year as year',
          'InvestmentRound.month as month'
          )
          ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
          ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
          ->join ('Afp','Afp.id', '=', 'Found.afpId')
          ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
          ->where('Afp.id','=',$afpId)
          ->where('InvestedCompany.id','=',$companyId)
          ->where('InvestedCompany.scope','=','Nacional')
          ->orderBy('InvestedCompany.name')
          ->orderBy('Afp.name')
          ->orderBy('Found.name')
          ->orderBy('FinancialInstrument.name')
          ->orderBy('InvestmentRound.year')
          ->orderBy('InvestmentRound.month')
          ->get();

      $results= $results->where('year_month','>',$filter2);
      //->where('year_month','>',$filter2);
      /*->where('year_month','>=',$filter1)
                    ->where('year_month','<=',$filter2);*/
        //->where('year_month','<',$filter1);

      foreach ($results as $key => $value) {


        $company=$value['emisor'];
        $found=$value['fondo'];
        $instrument=$value['instrumento'];
        $mount=$value['monto'];
        $mountPercent=$value['montoPorcentaje'];
        $year=$value['year'];
        $month=$value['month'];
        $afp=$value['afp'];

        $row = array (utf8_decode($functionary),
            utf8_decode($accionista),
            utf8_decode($coincidence),
            utf8_decode($company) ,
            utf8_decode($afp),
            utf8_decode($found),
            utf8_decode($instrument),
                $mount,$mountPercent,$year,$month,
            utf8_decode($date->intToMonth($month))
            );
            fputcsv($csv, $row);

      }
    }
  }
}
fclose($csv);

});




Route::get('/hard4b',function(){

  $csv = fopen("Hard4b.csv", "w");

  $date=new DateController();
  $matches=Afp::select('Functionary.name as functionaryName',
                        'Afp.id as afpId',
                        'FunctionaryXAfp.position as afpPosition',
                        'InvestedCompany.id as emisorId',
                        'FunctionaryXCompany.position as emisorPosition',
                        'Shareholder.name as accionista',
                        'ShareholderXCompany.participation as porcEmisor',
                        'ShareholderXAfp.participation as porcAfp',

                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',

                        'ShareholderXCompany.beginDate as beginEmisor_acc',
                        'ShareholderXCompany.endDate as endEmisor_acc',
                        'ShareholderXAfp.beginDate as beginAfp_acc',
                        'ShareholderXAfp.endDate as endAfp_acc '
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
                        ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
                        ->join('ShareholderXAfp',function($join) {
                          $join->on('ShareholderXAfp.shareholderId','=','Shareholder.id')
                               ->on('Afp.id','=','ShareholderXAfp.afpId');

                        })
                        ->get();

  foreach ($matches as $match){
      $rangeFunctionary=$date->intersectionDate(array('date1'=>$match['afpBegin'],'date2'=>$match['afpEnd'],
                              'date3'=>$match['emisorBegin'],'date4'=>$match['emisorEnd']));


      $rangeShareholder=$date->intersectionDate(array('date1'=>$match['beginEmisor_acc'],
                                        'date2'=>$match['endEmisor_acc'],
                                        'date3'=>$match['beginAfp_acc'],
                                        'date4'=>$match['endAfp_acc']));

      if(!is_null($rangeFunctionary) && !is_null($rangeShareholder)){
        $range=$date->intersectionDate(array('date1'=>$rangeFunctionary[0],
                                'date2'=>$rangeFunctionary[1],
                                'date3'=>$rangeShareholder[0],
                                'date4'=>$rangeShareholder[1]));

        if (!is_null($range)){

          $functionary=$match['functionaryName'];
          $companyId=$match['emisorId'];
          $afpId=$match['afpId'];
          $accionista=$match['accionista'];

          $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


          $filter1 =explode("-",$range[0]);
          $filter1=$filter1[0]*100+$filter1[1];
          $filter2=explode("-",$range[1]);
          $filter2=$filter2[0]*100+$filter2[1];

          $results=InvestmentRound::select('InvestedCompany.name as emisor', 'Found.name as fondo','Afp.name as afp',
          'FinancialInstrument.name as instrumento', 'InvestmentRound.mount as monto',
          'InvestmentRound.mountPercent as montoPorcentaje', 'InvestmentRound.year as year',
          'InvestmentRound.month as month'
          )
          ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
          ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
          ->join ('Afp','Afp.id', '=', 'Found.afpId')
          ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
          ->where('Afp.id','<>',$afpId)
          ->where('InvestedCompany.id','=',$companyId)
          ->where('InvestedCompany.scope','=','Nacional')
          ->orderBy('InvestedCompany.name')
          ->orderBy('Afp.name')
          ->orderBy('Found.name')
          ->orderBy('FinancialInstrument.name')
          ->orderBy('InvestmentRound.year')
          ->orderBy('InvestmentRound.month')
          ->get();

      $results= $results->where('year_month','>=',$filter1)
                    ->where('year_month','<=',$filter2);

      foreach ($results as $key => $value) {



        $company=$value['emisor'];
        $found=$value['fondo'];
        $instrument=$value['instrumento'];
        $mount=$value['monto'];
        $mountPercent=$value['montoPorcentaje'];
        $year=$value['year'];
        $month=$value['month'];
        $afp=$value['afp'];

        $row = array (utf8_decode($functionary),
            utf8_decode($accionista),
            utf8_decode($coincidence),
            utf8_decode($company) ,
            utf8_decode($afp),
            utf8_decode($found),
            utf8_decode($instrument),
                $mount,$mountPercent,$year,$month,
            utf8_decode($date->intToMonth($month))
            );
            fputcsv($csv, $row);

      }
    }
  }
}
fclose($csv);

});












Route::get('/hard4instr',function(){

  $csv = fopen("hard4instr.csv", "w");

  $date=new DateController();
  $matches=Afp::select('Functionary.name as functionaryName',
                        'Afp.id as afpId',
                        'Afp.name as afp',
                        'InvestedCompany.id as emisorId',
                        'InvestedCompany.name as emisor',


                        'Shareholder.name as accionista',


                        'FunctionaryXAfp.beginDate as afpBegin',
                        'FunctionaryXAfp.endDate as afpEnd',
                        'FunctionaryXCompany.beginDate as emisorBegin',
                        'FunctionaryXCompany.endDate as emisorEnd',

                                                'ShareholderXCompany.beginDate as beginEmisor_acc',
                        'ShareholderXCompany.endDate as endEmisor_acc',
                        'ShareholderXAfp.beginDate as beginAfp_acc',
                        'ShareholderXAfp.endDate as endAfp_acc '
                        )
                        ->join('FunctionaryXAfp','FunctionaryXAfp.afpId','=','Afp.id')
                        ->join('Functionary','Functionary.id','=','FunctionaryXAfp.functionaryId')
                        ->join('FunctionaryXCompany','FunctionaryXCompany.functionaryId','=','Functionary.id')
                        ->join('InvestedCompany','InvestedCompany.id','=','FunctionaryXCompany.companyId')
                        ->join('ShareholderXCompany','ShareholderXCompany.companyId','=','InvestedCompany.id')
                        ->join('Shareholder','Shareholder.id','=','ShareholderXCompany.shareholderId')
                        ->join('ShareholderXAfp',function($join) {
                          $join->on('ShareholderXAfp.shareholderId','=','Shareholder.id')
                               ->on('Afp.id','=','ShareholderXAfp.afpId');

                        })
                        ->get();
  foreach ($matches as $match){
      $rangeFunctionary=$date->intersectionDate(array('date1'=>$match['afpBegin'],'date2'=>$match['afpEnd'],
                              'date3'=>$match['emisorBegin'],'date4'=>$match['emisorEnd']));


      $rangeShareholder=$date->intersectionDate(array('date1'=>$match['beginEmisor_acc'],
                                        'date2'=>$match['endEmisor_acc'],
                                        'date3'=>$match['beginAfp_acc'],
                                        'date4'=>$match['endAfp_acc']));

      if(!is_null($rangeFunctionary) && !is_null($rangeShareholder)){
        $range=$date->intersectionDate(array('date1'=>$rangeFunctionary[0],
                                'date2'=>$rangeFunctionary[1],
                                'date3'=>$rangeShareholder[0],
                                'date4'=>$rangeShareholder[1]));

        if (!is_null($range)){

          $functionary=$match['functionaryName'];
          $companyId=$match['emisorId'];
          $company=$match['emisor'];
          $afpId=$match['afpId'];
          $afp=$match['afp'];
          $accionista=$match['accionista'];

          $coincidence= "[".$range[0]." / ".$date->currentDay($range[1])."]";


          $filter1 =explode("-",$range[0]);
          $filter1=$filter1[0]*100+$filter1[1];// solo toma en cuenta año y mes
          $filter2=explode("-",$range[1]);
          $filter2=$filter2[0]*100+$filter2[1];

          $query=InvestmentRound::select('InvestmentRound.financialinstrumentId',
              'InvestmentRound.year as year',
              'InvestmentRound.month as month'
          )
          ->join ('InvestedCompany','InvestedCompany.id', '=', 'InvestmentRound.companyId')
          ->join ('Found','Found.id', '=', 'InvestmentRound.foundId')
          ->join ('Afp','Afp.id', '=', 'Found.afpId')
          ->join ('FinancialInstrument','FinancialInstrument.id', '=', 'InvestmentRound.financialinstrumentId')
          ->where('Afp.id','=',$afpId)
          ->where('InvestedCompany.id','=',$companyId)
          ->where('InvestedCompany.scope','=','Nacional')
          ->orderBy('InvestedCompany.name')
          ->orderBy('Afp.name')
          ->orderBy('Found.name')
          ->orderBy('FinancialInstrument.name')
          ->orderBy('InvestmentRound.year')
          ->orderBy('InvestmentRound.month')
          ->get();

          $now=$query->where('year_month','>=',$filter1)
              ->where('year_month','<=',$filter2);

          $before=$query->where('year_month','<',$filter1);

          $after=$query->where('year_month','>',$filter2);



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
                        utf8_decode($accionista),
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
            utf8_decode($accionista),
                          utf8_decode($coincidence),

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
                          utf8_decode($accionista),
                          utf8_decode($coincidence),
                          utf8_decode($company) ,
                          utf8_decode($afp),
                          utf8_decode($i['name']),
                            utf8_decode('Después')
                        );
            fputcsv($csv, $row);
          }





    }
  }
}
fclose($csv);

});

Route::get('/hard5','ReportController@hard5');

Route::get('/hard5instr','ReportController@hard5Instr');
