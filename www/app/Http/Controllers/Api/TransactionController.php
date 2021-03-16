<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Transaction::class);

        $validatedData = $this->validate($request, [
            'payer' => 'required',
            'payee' => 'required',
            'value' => 'required|numeric'
        ]);

        /** @var User $payer */
        $payer = User::find($request['payer']);
        $payer->refresh();

        /** @var User $payee */
        $payee = User::find($request['payee']);
        $payee->refresh();

        $payerWallet = $payer->wallet;
        $payeeWallet = $payee->wallet;

        $payerWallet->funds -= $request['value'];
        $payeeWallet->funds += $request['value'];

        try {
            DB::beginTransaction();
            $payerWallet->save();
            $payeeWallet->save();
            $obj = Transaction::create($validatedData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(['data' => $obj], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
