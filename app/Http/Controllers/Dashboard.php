<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Receipt;
use App\Models\payment;
use App\Models\TrnxHdr;
use app\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Dashboard extends Controller
{
    //
    use HttpResponses;
    public function dashboard(Request $request){

        $storeid = $request->input('storeid');
        $businessid = $request->input('businessid');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');

        $storesArray = array_map('trim', explode(',', $storeid));

        //cashsales
        $cashsalesq = TrnxHdr::select(
            'trnxhdr.transactiontype as item',
            'trnxhdr.storeid as branch',
            DB::raw('SUM(trnxdetails.amount) as amount')
        )
            ->join('trnxdetails', 'trnxhdr.invoiceno', '=', 'trnxdetails.invoiceno')
            ->where('trnxhdr.businessid', $businessid)
            ->where('trnxhdr.transactiontype', 'CSHS')
            ->whereIn('trnxhdr.storeid', $storesArray)
            ->whereRaw('DATE(trnxhdr.tdate) BETWEEN ? AND ?', [$startdate, $enddate])
            ->groupBy('trnxhdr.transactiontype', 'trnxhdr.storeid');
            //->get();

//        Log::info('SQL Query: ' . $cashsalesq->toSql());
//        Log::info('SQL Bindings: ', $cashsalesq->getBindings());

        $cashsales = $cashsalesq->get();



        //creditsales
        $creditsales = TrnxHdr::select(
            'trnxhdr.transactiontype as item',
            'trnxhdr.storeid as branch',
            DB::raw('SUM(trnxdetails.amount) as amount')
        )
            ->join('trnxdetails', 'trnxhdr.invoiceno', '=', 'trnxdetails.invoiceno')
            ->where('trnxhdr.businessid', $businessid)
            ->where('trnxhdr.transactiontype', 'CRDS')
            ->whereIn('trnxhdr.storeid', $storesArray)
            ->whereRaw('DATE(trnxhdr.tdate) BETWEEN ? AND ?', [$startdate, $enddate])
            ->groupBy('trnxhdr.transactiontype', 'trnxhdr.storeid')
            ->get();

        //transfers
        $transfers = TrnxHdr::select(
            'trnxhdr.invoiceno as item',
            'trnxhdr.trfsreceiver as branch',
            DB::raw('SUM(trnxdetails.qty) as amount')
        )
            ->join('trnxdetails', 'trnxhdr.invoiceno', '=', 'trnxdetails.invoiceno')
            ->where('trnxhdr.businessid', $businessid)
            ->where('trnxhdr.transactiontype', 'TRFS')
            ->where('trnxhdr.trfsreceiver','<>' ,'')
            //->whereRaw('DATE(trnxhdr.tdate) BETWEEN ? AND ?', [$startdate, $enddate])
            ->groupBy('trnxhdr.invoiceno', 'trnxhdr.trfsreceiver')
            ->get();


        $funds0 = Ledger::select(
            'storeid',
            'accountcode',
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('account', 'D')
            ->where('accountcode', 'like', $request->fundscode .'%')
            ->whereRaw('DATE(tdate) BETWEEN ? AND ?', [$startdate, $enddate])
            ->where('businessid', $businessid) // Recommended to keep data scoped
            ->groupBy('storeid', 'accountcode');

            $funds= $funds0->get();

                $receipts = Receipt::select(
                'customercode',
                DB::raw('SUM(amount) as amount')
            )
            ->where('businessid', $businessid)
           // ->where('storeid', $storeid)
            // Use whereBetween for better performance and readability
            ->whereBetween('receiptdate', [$startdate, $enddate])
            ->groupBy('customercode')
            ->get();

             $payments = payment::select(
                'customercode',
                DB::raw('SUM(amount) as amount')
            )
            ->where('businessid', $businessid)
           // ->where('storeid', $storeid)
            // Use whereBetween for better performance and readability
            ->whereBetween('receiptdate', [$startdate, $enddate])
            ->groupBy('customercode')
            ->get();

             $postdatedpayments = payment::select(
                'customercode','maturitydate','amount'
            )
            ->where('businessid', $businessid)
            ->where('isfuture', 1)
            ->where('receiptno','NOT LIKE', '%R')
            // Use whereBetween for better performance and readability
            //->whereBetween('receiptdate', [$startdate, $enddate])
            ->get();

        //Log::info('SQL Query: ' . $funds0->toSql());
       // Log::info('SQL Bindings: ', $funds0->getBindings());

        $datapayload = [
            "datapayload" => [
                "cashsales" => $cashsales,
                "creditsales" => $creditsales,
                "transfers" => $transfers,
                "funds" => $funds,
                "receipts" => $receipts,
                "payments" => $payments,
                "postdatedpayments" => $postdatedpayments
            ]
        ];

        return $this->success($datapayload);

    }

}
