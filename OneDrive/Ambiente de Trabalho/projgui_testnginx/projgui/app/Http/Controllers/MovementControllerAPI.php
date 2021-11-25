<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\Movement as MovementResource;
use App\Http\Resources\Wallet as WalletResource;
use App\Http\Resources\Categorie as CategorieResource;

use App\Movement;
use App\Wallet;
use App\Category;

class MovementControllerAPI extends Controller
{


    public function index(Request $request)
    {
        /*$data = DB::table('movements')
        ->select('movements.id','movements.type','profiles.photo')
        ->join('wallets','wallets.id','=','movements.wallet_id')
        ->get();*/

        $this->authorize('onlyUser', $request->user());

        if ($request->has('page')) {

            $offset = ($request->input('page') - 1) * 30;
            $movements = Movement::query()->where('wallet_id',$request->user()->id);

            if ($request->input('id')) {
                $movements = $movements->where('id', $request->input('id'));
            }
            if ($request->input('type')) {
                $movements = $movements->where('type', $request->input('type'));
            }
            if ($request->input('typePayment')) {
                $movements = $movements->where('type_payment', $request->input('typePayment'));
            }
            if ($request->input('category')) {
                $movements = $movements->where('category_id', $request->input('category'));
            }
            if ($request->input('dateInf')) {
                $movements = $movements->where('date','>', $request->input('dateInf'));
            }
            if ($request->input('dateSup')) {
                 $movements = $movements->where('date','<', $request->input('dateSup'));
            }

            if ($request->input('destination')) {
                $wallet = Wallet::select('id')->where('email', $request->input('destination'))->get();
                foreach ($wallet as $value) {
                    $movements = $movements->where('transfer_wallet_id', $value->id);
                }
            }
            if ($request->input('source')) {
                $wallet = Wallet::select('id')->where('email', $request->input('source'))->get();
                $movements = $movements->where('transfer_wallet_id', $wallet);
            }

            $movements = MovementResource::collection($movements->orderBy('date', 'DESC')->skip($offset)->take(30)->get());
        }
        else{
            $movements = MovementResource::collection(Movement::where('wallet_id', $request->user()->id)->orderBy('date', 'DESC')->get());
        }

        return $movements;
    }

    public function totalMovements(Request $request)
    {
        $this->authorize('onlyUser', $request->user());
        return json_encode(array("total_movements" => Movement::where('wallet_id', $request->user()->id)->count()));
    }

    public function movementIncome(Request $request)
    {

        $this->authorize('onlyOperator', $request->user());

        $validator = $request->validate([
            "email" => "required|email",
            "value" => "required|numeric|between:0.01,5000.00",
            "transfer" => "required|in:0,1",
            "type" => "required|in:c,bt",
            "source_description" => "required",
            "iban" => $request["transfer"] == 1 ? "required|regex:/^[A-Z]{2}[0-9]{23}$/" : "nullable"
        ]);

        $wallet =  Wallet::select('id', 'balance')->where('email', $request->email)->first();

        $movement = Movement::create([
            'wallet_id' => $wallet->id,
            'type' => 'i',
            'transfer' => $request->input('transfer'),
            'type_payment' => $request->input('type'),
            'iban' => $request->input('transfer') == 1 ? $request->input('iban') : null,
            'source_description' => $request->input('source_description'),
            'date' => date('Y-m-d H:i:s'),
            'start_balance' => $wallet->balance,
            'end_balance' => $wallet->balance + $request->input('value'),
            'value' => $request->input('value')
        ]);

        $movement->save();


        $wallet->balance += $request->input('value');
        $wallet->save();
    }

    public function movementExpense(Request $request)
    {
        $this->authorize('onlyUser', $request->user());

        $wallet =  Wallet::select('id', 'balance')->where('email', $request->user()->email)->first();

        $validator = $request->validate([
            "transfer" => "required|in:0,1",
            "value" => "required|numeric|between:0.01,5000.00",
            "category" => "required",
            "description" => "required",

            "type" => $request["transfer"] == 0 ? "required|in:bt,mb" : "nullable",
            "iban" => $request["transfer"] == 0 ? "required_if:type,'bt'|regex:/^[A-Z]{2}[0-9]{23}$/|nullable" : "nullable",
            "mb_entity_code" => $request["transfer"] == 0 ? "required_if:type,'mb'|numeric|digits:5|nullable" : "nullable",
            "mb_payment_reference" => $request["transfer"] == 0 ? "required_if:type,'mb'|numeric|digits:9|nullable" : "nullable",

            "destination_email" => $request["transfer"] == 1 ? "required|email" : "nullable",
            "source_description" => $request["transfer"] == 1 ? "required" : "nullable",
        ]);

        $movement = new Movement();
        $movement->wallet_id = $wallet->id;
        $movement->type = 'e';
        $movement->transfer = $request->input('transfer');
        $movement->category_id = $request->input('category');
        $movement->description = $request->input('description');
        $movement->value = $request->input('value');
        $movement->date = date('Y-m-d H:i:s');
        $movement->start_balance = $wallet->balance;
        $movement->end_balance = $wallet->balance - $request->input('value');

        if ($request["transfer"] == 0) {
            $movement->type_payment = $request->input('type');
            if ($request["type"] == 'bt') {
                $movement->iban = $request->input('iban');
            }
            if ($request["type"] == 'mb') {
                $movement->mb_entity_code = $request->input('mb_entity_code');
                $movement->mb_payment_reference = $request->input('mb_payment_reference');
            }
        }

        if ($request["transfer"] == 1) {
            //fazer query, ir buscar o id da carteira com este mail
            $destination_wallet =  Wallet::select('id', 'balance')->where('email', $request->input('destination_email'))->first();

            $movement->transfer_wallet_id = $destination_wallet->id;
            $movement->source_description = $request->input('source_description');

            // ############# CREATE MIRRORED MOVEMENT #############

            $mirror_movement = new Movement();
            $mirror_movement->wallet_id = $destination_wallet->id;
            $mirror_movement->type = 'i';
            $mirror_movement->transfer = 1;
            $mirror_movement->category_id = null;
            $mirror_movement->description = null;
            $mirror_movement->value = $request->input('value');
            $mirror_movement->date = date('Y-m-d H:i:s');
            $mirror_movement->start_balance = $destination_wallet->balance;
            $mirror_movement->end_balance = $destination_wallet->balance + $request->input('value');
            $mirror_movement->transfer_wallet_id = $wallet->id;
            $mirror_movement->source_description = null; //$request->input('source_description');
        }


        //verificar se não fica balanço negativo

        if ($movement->end_balance < 0) {
            return 0;
        } else {
            $movement->save();
            $wallet->balance -= $request->input('value');
            $wallet->save();

            //se não tiver definida é porque não é de transferencia, logo este código não se faz
            if(isset($mirror_movement)){
                $mirror_movement->save();
                $destination_wallet->balance += $request->input('value'); 
                $destination_wallet->save();
            }
            
            return 1;
        }
    }

    public function getCategories(Request $request)
    {
        $this->authorize('onlyUser', $request->user());

        $categories = CategorieResource::collection(Category::where('type', 'e')->get());
        return $categories;
    }

    public function updateMovement(Request $request, $id)
    {
        $this->authorize('onlyUser', $request->user());

        $validator = $request->validate([
            "category_id" => $request["type"] == 'e' ? "required" : "nullable",
            "description" => $request["type"] == 'e' ? "required" : "nullable"
        ]);

        $movement = Movement::findOrFail($id);
        $movement->category_id = $request->input('category_id');
        $movement->description = $request->input('description');

        $movement->save();
    }
}
