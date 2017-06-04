<?php namespace App\Http\Controllers;

use App\Prueba;
use App\Sensores;
use Illuminate\Http\Request;

class SensorController extends Controller {

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
	    echo "en inddex";
		return view('home');
	}

	public function nuevo(){
	    if (!empty($_POST)){
            $data = $_POST;
            $this->save_log(json_encode($data));
            $id_sensor=$data['ID'];
            unset($data['ID']);
            $colegio=$data['Colegio'];
            unset($data['Colegio']);


            foreach ($data as $sensor=>$value){
                $affectedRows = Sensores::where('id_sensor', '=', $id_sensor)
                    ->where('id_colegio', '=', $colegio)
                    ->where('sensor', '=', $sensor)
                    ->update(['status' => 0]);
                $value_pre=explode(';',$value);

                $value=explode(',',$value_pre[0]);
                $sensores=new Sensores();
                $sensores->id_sensor=$id_sensor;
                $sensores->id_colegio=$colegio;
                $sensores->sensor=$sensor;
                $sensores->valor=$value[0];
                $sensores->texto=(count($value)>1)?$value[1]:0;
                $sensores->color=(count($value_pre)>1)?$value_pre[1]:0;
                $sensores->save();
            }


        }else{
	        echo ( "no hay data");
        }


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

	public function lista(){
		$sensores = Sensores::orderBy("created_at","DESC")->get();
		return response()->json($sensores, 200);
    }
	public function listado(){
		return view('sensor/listado');
    }
	public function tabla(){
		return view('sensor/tabla');
    }
	public function colegio(Request $request){
		$id = $request->id;
		return view('sensor/colegio',compact('id'));
    }
	public function ultimo(Request $request){
		$id = $request->id;
		$sensor = Sensores::join('colegio', 'sensores.id_colegio', '=', 'colegio.id')
  			->select('sensores.*','colegio.name')->where("sensores.id_colegio",$id)->orderBy("sensores.created_at","DESC")->first();
  		if($sensor->color == 2)
  		{
  			$success = mail("gtkphpc@gmail.com","Alerta","{$sensor->name} con valor ");
  		}
		return response()->json($sensor, 200);
    }       
}
