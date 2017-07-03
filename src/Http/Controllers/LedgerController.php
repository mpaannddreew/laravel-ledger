<?php

namespace FannyPack\Ledger\Http\Controllers;

use Illuminate\Http\Request;
use FannyPack\Ledger\EntryRepository as Ledger;
use Illuminate\Validation\Factory as Validator;

class LedgerController extends Controller
{
    /**
     * The ledger repository
     *
     * @var Ledger
     */
    protected $ledger;

    /**
     * Validator instance
     *
     * @var Validator
     */
    protected $validator;

    /**
     * LedgerController constructor.
     * @param Ledger $ledger
     * @param Validator $validator
     */
    public function __construct(Ledger $ledger, Validator $validator)
    {
        $this->middleware('auth:api')->except(['index']);
        $this->middleware(['web', 'auth'])->only(['index']);
        $this->ledger = $ledger;
        $this->validator = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validator->make($request->all(), [
            'offset' => 'numeric',
            'limit' => 'numeric',
            'days_ago' => 'numeric',
            'entry_type' => 'alpha',
            'from_date' => 'date|date_format:Y-m-d',
            'days_from_date' => 'numeric',
        ])->validate();

        $offset = (int)$request->input('offset', 0);
        $limit = (int)$request->input('limit', 10);
        $days = (int)$request->input('days_ago', 0);
        $type = $request->input('entry_type', '');
        $from_date = $request->input('from_date', '');
        $days_from_date = $request->input('days_from_date', 1);
        
        if ($from_date)
        {
            $entries = $this->ledger->findFromDate($from_date, $days_from_date, $type, $offset, $limit);
        }else{
            if ($type)
            {
                $entries = $this->ledger->getTypeEntries($type, $days, $offset, $limit);
            }else {
                $entries = $this->ledger->getEntries($days, $offset, $limit);
            }
        }

        $response = [
            'success' => true,
            'count' => count($entries),
            'offset' => $offset,
            'limit' => $limit,
            'data' => $entries
        ];

        if ($days) $response['days_ago'] = $days;

        if ($type) $response['entry_type'] = $type;

        if ($from_date) {
            $response['from_date'] = $from_date;

            if ($days_from_date) $response['days_from_date'] = $days_from_date;
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param $entry_id
     * @return \Illuminate\Http\Response
     */
    public function show($entry_id)
    {
        $this->validator->make(['entry_id' => $entry_id], [
            'entry_id' => 'required|numeric',
        ])->validate();
        
        $entry = $this->ledger->find((int)$entry_id);
        $response = [
            'success' => true,
            'item' => $entry
        ];

        return response()->json($response);
    }
}
