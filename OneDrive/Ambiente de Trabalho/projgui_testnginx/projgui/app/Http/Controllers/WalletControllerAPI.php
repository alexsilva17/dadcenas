<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Wallet as WalletResource;
use App\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WalletControllerApi extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return WalletResource::collection(Wallet::paginate(5));
        } else {
            return WalletResource::collection(Wallet::all());
        }

       
    }
    
    public function getWallets(Request $request)
    {
        $wallet = WalletResource::collection(Wallet::orderBy('id')->take(100)->get());
        return $wallet;
    }

    public function getBalance(Request $request)
    {
        $wallet =  Wallet::select('email','balance')->where('email', $request->user()->email)->first(); 
        return $wallet;
        //return json_encode(array("balance" =>  Wallet::select('balance')->where('email', $request->user()->email)->first()));
    }

    public function getMoney(Request $request)
    {
        $wallet =  Wallet::all(); 
        $money = 1;
        foreach ($wallet as $value) {
            $money += $value->balance;
        }
        return $money;
    }

}
