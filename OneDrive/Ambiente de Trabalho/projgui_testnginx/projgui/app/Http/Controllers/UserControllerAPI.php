<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Wallet;
use App\Movement;
use Hash;

class UserControllerAPI extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('onlyAdmin', $request->user());

    
        $offset = ($request->input('page') - 1) * 30;
            $users = User::query();
           
            if ($request->input('type')) {
                $users = $users->where('type', $request->input('type'));
            }
            if ($request->input('name')) {
                $users = $users->where('name', $request->input('name'));
            }
            if ($request->input('email')) {
               //strpos( 'date', $request->input('date'));
                $users = $users->where('email', $request->input('email'));
                
            }
            if ($request->input('nif')) {
                //strpos( 'date', $request->input('date'));
                 $users = $users->where('nif', $request->input('nif'));
                 
             }
             $users = UserResource::collection($users->orderBy('created_at', 'DESC')->skip($offset)->take(30)->get());
       
        return $users;
    }

    public function totalUsers(Request $request)
    {
        $this->authorize('onlyAdmin', $request->user());
        return json_encode(array("total_users" => User::count()));
    }


    public function getCurrentUser(Request $request, $id)
    {

        $user = User::findOrFail($id);

        return new UserResource($user);
    }


    public function create()
    {


        $user = new User;
        return new UserResource($user);
    }

    public function store(Request $request)
    {
        //$current = $request->input('currentUser');
        //$this->authorize('create',$request->user);

        /*$request->validate([
            "data" => "required|date|date_format:Y-m-d",
            "hora_descolagem" => "date_format:H:i|required",
            "hora_aterragem" => "date_format:H:i|required|after:hora_descolagem",
            "aeronave" => "required|exists:aeronaves,matricula|min:1|max:8",
            "num_diario" => "required|integer",
            "num_servico" => "required|integer",
            "piloto_id" => "required|integer|exists:users,id,tipo_socio,P",
            "natureza" => "required|in:T,I,E",
            "aerodromo_partida" => "required|exists:aerodromos,code",
            "aerodromo_chegada" => "required|exists:aerodromos,code",
            "num_aterragens" => "required|integer|gte:0",
            "num_descolagens" => "required|integer|gte:0",
            "num_pessoas" => "required|integer|gt:0",
            "conta_horas_inicio" => "required|integer|gte:0",
            "conta_horas_fim" => "required|integer|gte:conta_horas_inicio",
            "tempo_voo" => "required|integer|gte:0",
            "preco_voo" => "required|gte:0",
            "modo_pagamento" => "required|in:T,M,N,P",
            "num_recibo" => "required|max:20",
            "tipo_instrucao" => $request["natureza"] == "I" ? "required|in:D,S" : "",
            "instrutor_id" => $request["natureza"] == "I" ? "required|exists:users,id,tipo_socio,P|exists:users,id,instrutor,1" : "exists:users,id,tipo_socio,P|nullable|exists:users,id,instrutor,1",
            ]);*/
        $request->validate([

            'name' => 'required|min:3|regex:/^[a-zA-ZÀ-ú\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:3',
            'nif' => $request['type'] == 'u' ? 'required|regex:/^[0-9]{9}+$/' : "",

        ]);

        $user = User::create([
            'type' => $request->input('type'),
            'active' => '1',
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'nif' => $request->input('nif'),
        ]);

        if ($request->filled('photo')) {
            $imagem = $request->get('photo');

            \Image::make($request->get('photo'))->save(storage_path('app/public/fotos/'.$user->id.'.jpg'));
            $user->photo = "" . $user->id . '.jpg';
        }
        $user->password = Hash::make($user->password);
        $user->save();


        if ($user->type == 'u') {
            $wallet = Wallet::create([
                'id' => $user->id,
                'email' => $user->email,
                'balance' => '0'
            ]);
            $wallet->save();
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'password' => 'min:3',
            'passwordNova' => 'min:3',
            'passwordConfirmacao' => 'min:3',
        ]);
        $user = User::findOrFail($id);
        if ($request->filled('password')) {

            if (Hash::check($request->input('password'), $user->password)) {

                if ($request->input('passwordNova') == $request->input('passwordConfirmacao')) {

                    $user->password = $request->input('passwordNova');
                    $user->password = Hash::make($user->password);
                }
            }
        }

        if ($request->filled('photo') ) {
            if ($request->input('photo') != $user->id ) {
                # code...
            
            \Image::make($request->get('photo'))->save(storage_path('app\public\fotos/'.$user->id.'.jpg'));
            $user->photo = "" . $user->id . '.jpg';
        }
        $user->name = $request->input('name');
        $user->save();
        return new UserResource($user);
    }
    }

    public function destroy(Request $request, $id)
    {
        
        $this->authorize('onlyAdmin', $request->user());

        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function emailAvailable(Request $request)
    {
        $totalEmail = 1;
        if ($request->has('email') && $request->has('id')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->where('id', '<>', $request->id)->count();
        } else if ($request->has('email')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->count();
        }
        return response()->json($totalEmail == 0);
    }

    public function activateUser(Request $request, $id)
    {
        $this->authorize('onlyAdmin', $request->user());

        $user = User::findOrFail($id);
        $user->active = 1;
        $user->save();
    }

    public function deactivateUser(Request $request, $id)
    {
        $this->authorize('onlyAdmin', $request->user());

        $user = User::findOrFail($id);
        $user->active = 0;
        $user->save();
    }
}
