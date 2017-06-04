<?php namespace App\Http\Controllers;

use App\Colegio;
use App\Prueba;
use App\Sensores;

class ColegioController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        $colegios = Colegio::where('status', '=', 1)->get();
        $data_colegios=array();
        /*foreach ($colegios as $key=>$colegio){
            var_dump($colegio); exit;
        }*/
        foreach ($colegios as $key=>$colegio){

            $sensores= Sensores::where('id_colegio', '=', $colegio->id)
                ->where('status', '=', 1)
                ->get()
            ;
            $colegios[$key]['sensores']=$sensores;
        }
		return view('colegio/index', ['colegios' => json_encode($colegios)]);  
	}

	public function listar(){
        $colegios = Colegio::where('status', '=', 1)->get();
        $data_colegios=array();
        /*foreach ($colegios as $key=>$colegio){
            var_dump($colegio); exit;
        }*/
        foreach ($colegios as $key=>$colegio){

            $sensores= Sensores::where('id_colegio', '=', $colegio->id)
                ->where('status', '=', 1)
                ->get()
            ;
            $colegios[$key]['sensores']=$sensores;
        }
        echo json_encode($colegios);

    }

    private function save_log($data){
        error_log($data);
        try{
            $prueba = new Prueba();

            $prueba->data = $data;

            $prueba->save();

            error_log('ok');

        }catch (\Exception $e){
            error_log($e->getMessage());
        }
    }

}
