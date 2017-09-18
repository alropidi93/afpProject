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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/insertAfp',function(){
  if (($entrada = fopen('files/afp.csv','r')) !== FALSE)
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


Route::get('/insertEconomicGroup',function(){
  if (($entrada = fopen('files/economic.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {
        $eco=new EconomicGroup();
        $eco->name=$data['0'];
        $eco->save();
      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});

Route::get('/afpxeconomic',function(){
  if (($entrada = fopen('files/afpxeconomic.csv','r')) !== FALSE)
  {
    while (($data = fgetcsv($entrada ,1000, ',')) !==FALSE)
    {
      try {

        $flight = Afp::find($data['0']);
        $flight['economicGroupId']=$data['1'];
        $flight->save();
      } catch (Exception $e) {
        return $e->getMessage();

      }


    }
    fclose($entrada);
  }

});
