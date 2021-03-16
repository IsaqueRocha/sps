<?php

namespace App\Http\Controllers\Api;

use DB;
use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;

class TransactionController extends Controller
{
    private $repo;

    public function __construct(TransactionRepository $repo)
    {
        $this->repo = $repo;
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

        $data = $this->validate($request, [
            'payer' => 'required',
            'payee' => 'required',
            'value' => 'required|numeric'
        ]);

        $payerWallet = $this->repo->getUserWallet($data['payer']);
        $payeeWallet = $this->repo->getUserWallet($data['payee']);

        $this->repo->addFunds($payeeWallet, $data['value']);
        $this->repo->removeFunds($payerWallet, $data['value']);

        try {
            if ($this->repo->getPermission()) {
                DB::beginTransaction();
                $payerWallet->save();
                $payeeWallet->save();
                $obj = Transaction::create($data);
                DB::commit();
            } else {
                throw new Exception("Error Processing Permission Request", 1);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(['data' => $obj], Response::HTTP_CREATED);
    }
}
