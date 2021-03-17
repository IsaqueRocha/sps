<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;

class TransactionController extends Controller
{
    private $repo;

    public function __construct(TransactionRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     *  @OA\Post(
     *     tags={"Transaction"},
     *     summary="Store a newly created resource in storage.",
     *     description="store a new transaction on database",
     *     path="/transaction",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="payer", type="string", description="the payer id"),
     *             @OA\Property(property="payee", type="string", description="the payee id"),
     *             @OA\Property(property="value", type="string", description="a numeric float value"),
     *       )
     *     ),
     *     @OA\Response(
     *         response="201", description="New transaction created"
     *     ),
     *     @OA\Response(
     *         response="400", description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response="401", description="Unauthorized"
     *     ),
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Transaction::class);

        $data = $this->validate($request, [
            'payer' => 'required|uuid',
            'payee' => 'required|uuid',
            'value' => 'required|numeric'
        ]);

        if ($data['value'] <= 0.0) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        try {
            if ($this->getPermission()) {
                $payerWallet = $this->repo->getUserWallet($data['payer']);
                $payeeWallet = $this->repo->getUserWallet($data['payee']);

                $this->repo->addFunds($payeeWallet, $data['value']);
                $this->repo->removeFunds($payerWallet, $data['value']);

                DB::beginTransaction();
                $payerWallet->save();
                $payeeWallet->save();
                $obj = Transaction::create($data);
                DB::commit();
            } else {
                throw new Exception("Error Processing Permission Request", 1); //NOSONAR
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(['data' => $obj], Response::HTTP_CREATED);
    }

    protected function getPermission()
    {
        return $this->repo->getPermission();
    }
}
