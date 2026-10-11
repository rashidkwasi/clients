<?php

namespace App\Http\Controllers;
use App\Models\Coa;
use App\Models\CoaGroup;
use App\Models\BusinessSetup;
use App\Models\Store;
use App\Models\Company;
use App\Models\PaymentOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HttpResponses;

use function Symfony\Component\Clock\now;

class SetupController extends Controller
{

  use HttpResponses;

    public function loadsetup(Request $request)
    {  // $request->validate([
        //     'businessid' => 'required|string',
        //     'businessname' => 'required|string',
        //     'telephone' => 'required|string',
        //     'address' => 'required|string',
        //     'city' => 'required|string',
        //     'shortcode' => 'required|string',
        //     'version' => 'required|string',
        //     'contactperson' => 'required|string',
        //     'store_id'            => 'required|string',
        //     'store_name'          => 'required|string',
        //     'location'            => 'required|string',
        //     'ledgercode'          => 'required|string',
        //     'base'                => 'required|boolean',
        //     'retail'              => 'required|boolean',
        //     'headoffice'          => 'required|boolean',
        //     'storecode'           => 'required|string',
        //     'allowoutofstocksale' => 'required|boolean',
        // ]);
    
        $businessid = $request->businessid;
        $createdby  = $request->createdby;
        $businessname = $request->businessname;
        $telephone = $request->telephone;
        $address = $request->address;
        $city = $request->city;
        $shortcode = $request->shortcode;
       
        $version = $request->version;
        $contactperson = $request->contactperson;
        $storeid = 'Main';
        $storecode = 'MN';
        $branchname = $request->store_name;
        $branchcode = $request->storecode;
        $location = $request->location;
        $contactphone = $request->storephone;
        $ledgercode = $request->ledgercode;
        $base = $request->base;
        $retail = $request->retail;
        $headoffice = $request->headoffice;
        $allowoutofstocksale = $request->allowoutofstocksale;
                
        DB::beginTransaction();

        try {
        // Create the company
         // Create the store
        //   $exists = Store::where('businessid', $request->businessid)
        //                ->exists();
 
        // if ($exists) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Business already exists for this business.',
        //     ], 409);
        // }
 
        $store = Company::create([
            'name'                  => $businessname,
            'telephone'             => $telephone,
            'address'               => $address ?? '',
            'city'                  => $city?? '',
            'shortcode'            => $shortcode,
            'version'              => $version,
            'contactperson'          => $contactperson,
            'createdby'          => 'system',
            'modifiedby'          => 'system',
            'businessid'            => $businessid,
        ]);

        // // Create the store
        //   $exists = Store::where('store_id',   $request->store_id)
        //                ->where('businessid', $request->businessid)
        //                ->exists();
 
        // if ($exists) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Store already exists for this business.',
        //     ], 409);
        // }0244709188
 
        $store = Store::create([
            'store_id'            => $storeid,
            'store_name'          => $branchname,
            'location'            => $location ?? '',
            'phone'               => $telephone ?? '',
            'businessid'          => $businessid,
            'ledgercode'          => $ledgercode,
            'base'                => $base,
            'retail'              => $retail,
            'headoffice'          => $headoffice,
            'storecode'           => $storecode,
            'allowoutofstocksale' => $allowoutofstocksale,
            
        ]);




        $data_accounts = [
            ['accountcode' => '100001', 'accountname' => 'Sales',            'accounttype' => 'Sales',    'accountsubtype' => '100', 'status' => 1],
            ['accountcode' => '220001', 'accountname' => 'Cost of Sales',    'accounttype' => 'Expenses', 'accountsubtype' => '220', 'status' => 1],
            ['accountcode' => '229999', 'accountname' => 'Suspense Account', 'accounttype' => 'Expenses', 'accountsubtype' => '220', 'status' => 1],
            ['accountcode' => '400001', 'accountname' => 'Stock',            'accounttype' => 'Assets',   'accountsubtype' => '400', 'status' => 1],
            ['accountcode' => '401CASH', 'accountname' => 'Cash',             'accounttype' => 'Assets',   'accountsubtype' => '401', 'status' => 1],
            ['accountcode' => '401MOMO', 'accountname' => 'Momo',              'accounttype' => 'Assets',   'accountsubtype' => '401', 'status' => 1],
            ['accountcode' => '99999',  'accountname' => 'Capital',          'accounttype' => 'Equity',   'accountsubtype' => '999', 'status' => 1],
        ];
 
        $inserted = 0;
        $skipped  = 0;
 
        foreach ($data_accounts as $row) {
            $exists = Coa::where('accountcode', $row['accountcode'])
                         ->where('businessid', $businessid)
                         ->exists();
 
            if ($exists) {
                $skipped++;
                continue;
            }
 
            Coa::create([
                'accountcode'    => $row['accountcode'],
                'accountname'    => $row['accountname'],
                'accounttype'    => $row['accounttype'],
                'accountsubtype' => $row['accountsubtype'],
                'storeid'        => $storeid,
                'businessid'     => $businessid,
                'status'         => $row['status'],
                'createdby'      => 'system',
                'modifiedby'     => 'system',
            ]);
 
            $inserted++;
        }

        $data_group = [
            ['element' => 'sales',      'groupname' => 'Returns',                   'elementcode' => 1, 'groupsummarycode' => 11, 'groupcode' => 110],
            ['element' => 'sales',      'groupname' => 'Sales',                     'elementcode' => 1, 'groupsummarycode' => 10, 'groupcode' => 100],
            ['element' => 'expenses',   'groupname' => 'Emoluments',                'elementcode' => 2, 'groupsummarycode' => 20, 'groupcode' => 200],
            ['element' => 'expenses',   'groupname' => 'Administration',            'elementcode' => 2, 'groupsummarycode' => 21, 'groupcode' => 210],
            ['element' => 'expenses',   'groupname' => 'Operation',                 'elementcode' => 2, 'groupsummarycode' => 22, 'groupcode' => 220],
            ['element' => 'assets',     'groupname' => 'Current Assets',            'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 400],
            ['element' => 'assets',     'groupname' => 'Cash and Cash Equivalents', 'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 401],
            ['element' => 'assets',     'groupname' => 'Accounts Receivable',       'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 402],
            ['element' => 'assets',     'groupname' => 'Inventory',                 'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 403],
            ['element' => 'assets',     'groupname' => 'Prepaid Expenses',          'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 404],
            ['element' => 'assets',     'groupname' => 'Non-current Assets',        'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 410],
            ['element' => 'assets',     'groupname' => 'Plant and Machinery',       'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 411],
            ['element' => 'assets',     'groupname' => 'Vehicle',                   'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 412],
            ['element' => 'assets',     'groupname' => 'Intangible',                'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 413],
            ['element' => 'liabilities','groupname' => 'Current Liabilities',       'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 700],
            ['element' => 'liabilities','groupname' => 'Accounts Payable',          'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 701],
            ['element' => 'liabilities','groupname' => 'Accrued Expenses',          'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 702],
            ['element' => 'liabilities','groupname' => 'Short Term Loan',           'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 703],
            ['element' => 'liabilities','groupname' => 'Long Term Liabilities',     'elementcode' => 7, 'groupsummarycode' => 71, 'groupcode' => 710],
            ['element' => 'liabilities','groupname' => 'Loans',                     'elementcode' => 7, 'groupsummarycode' => 71, 'groupcode' => 711],
            ['element' => 'equity',     'groupname' => 'Equity',                    'elementcode' => 9, 'groupsummarycode' => 90, 'groupcode' => 900],
            ['element' => 'equity',     'groupname' => 'Capital',                   'elementcode' => 9, 'groupsummarycode' => 90, 'groupcode' => 901],
            ['element' => 'equity',     'groupname' => 'Drawings',                  'elementcode' => 9, 'groupsummarycode' => 90, 'groupcode' => 902],
        ];
 
        $inserted = 0;
        $skipped  = 0;
 
        foreach ($data_group as $row) {
            $exists = CoaGroup::where('groupcode',   $row['groupcode'])
                               ->where('businessid', $businessid)
                               ->exists();
 
            if ($exists) {
                $skipped++;
                continue;
            }
 
            CoaGroup::create([
                'element'          => $row['element'],
                'groupname'        => $row['groupname'],
                'elementcode'      => $row['elementcode'],
                'groupsummarycode' => $row['groupsummarycode'],
                'groupcode'        => $row['groupcode'],
                'businessid'       => $businessid,
                'createdby'        => 'system',
                'modifiedby'       => 'system',
            ]);
 
            $inserted++;
        }

                $setup = BusinessSetup::create([
            'startdate'   => now(),
            'enddate'     => now(),
            'suspense'    => '229999',
            'pettycash'   => '401CASH',
            'funds'       => '401',
            'receivables' => '402',
            'payables'    => '701',
            'stock'       => '400001',
            'sales'       => '100001',
            'costofsales' => '220001',
            'storeid'     => $storeid,
            'businessid'  => $businessid,
            'createdby'   => 'system',
            'modifiedby'  => 'system',
        ]);


         DB::commit();

          return $this->success('Store and business created successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the store and business.',
                    'error'   => $e->getMessage(),
                ], 500);
            }
 
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Store created successfully.',
        //     'data'    => $store,
        // ], 201);
        
 
//         $data_group = [
//             ['element' => 'sales',      'groupname' => 'Returns',                   'elementcode' => 1, 'groupsummarycode' => 11, 'groupcode' => 110],
//             ['element' => 'sales',      'groupname' => 'Sales',                     'elementcode' => 1, 'groupsummarycode' => 10, 'groupcode' => 100],
//             ['element' => 'expenses',   'groupname' => 'Emoluments',                'elementcode' => 2, 'groupsummarycode' => 20, 'groupcode' => 200],
//             ['element' => 'expenses',   'groupname' => 'Administration',            'elementcode' => 2, 'groupsummarycode' => 21, 'groupcode' => 210],
//             ['element' => 'expenses',   'groupname' => 'Operation',                 'elementcode' => 2, 'groupsummarycode' => 22, 'groupcode' => 220],
//             ['element' => 'assets',     'groupname' => 'Current Assets',            'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 400],
//             ['element' => 'assets',     'groupname' => 'Cash and Cash Equivalents', 'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 401],
//             ['element' => 'assets',     'groupname' => 'Accounts Receivable',       'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 402],
//             ['element' => 'assets',     'groupname' => 'Inventory',                 'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 403],
//             ['element' => 'assets',     'groupname' => 'Prepaid Expenses',          'elementcode' => 4, 'groupsummarycode' => 40, 'groupcode' => 404],
//             ['element' => 'assets',     'groupname' => 'Non-current Assets',        'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 410],
//             ['element' => 'assets',     'groupname' => 'Plant and Machinery',       'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 411],
//             ['element' => 'assets',     'groupname' => 'Vehicle',                   'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 412],
//             ['element' => 'assets',     'groupname' => 'Intangible',                'elementcode' => 4, 'groupsummarycode' => 41, 'groupcode' => 413],
//             ['element' => 'liabilities','groupname' => 'Current Liabilities',       'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 700],
//             ['element' => 'liabilities','groupname' => 'Accounts Payable',          'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 701],
//             ['element' => 'liabilities','groupname' => 'Accrued Expenses',          'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 702],
//             ['element' => 'liabilities','groupname' => 'Short Term Loan',           'elementcode' => 7, 'groupsummarycode' => 70, 'groupcode' => 703],
//             ['element' => 'liabilities','groupname' => 'Long Term Liabilities',     'elementcode' => 7, 'groupsummarycode' => 71, 'groupcode' => 710],
//             ['element' => 'liabilities','groupname' => 'Loans',                     'elementcode' => 7, 'groupsummarycode' => 71, 'groupcode' => 711],
//             ['element' => 'equity',     'groupname' => 'Equity',                    'elementcode' => 9, 'groupsummarycode' => 90, 'groupcode' => 900],
//             ['element' => 'equity',     'groupname' => 'Capital',                   'elementcode' => 9, 'groupsummarycode' => 90, 'groupcode' => 901],
//             ['element' => 'equity',     'groupname' => 'Drawings',                  'elementcode' => 9, 'groupsummarycode' => 90, 'groupcode' => 902],
//         ];
 
//         $inserted = 0;
//         $skipped  = 0;
 
//         foreach ($data_group as $row) {
//             $exists = CoaGroup::where('groupcode',   $row['groupcode'])
//                                ->where('businessid', $businessid)
//                                ->exists();
 
//             if ($exists) {
//                 $skipped++;
//                 continue;
//             }
 
//             CoaGroup::create([
//                 'element'          => $row['element'],
//                 'groupname'        => $row['groupname'],
//                 'elementcode'      => $row['elementcode'],
//                 'groupsummarycode' => $row['groupsummarycode'],
//                 'groupcode'        => $row['groupcode'],
//                 'businessid'       => $businessid,
//                 'createdby'        => $createdby,
//                 'modifiedby'       => $createdby,
//             ]);
 
//             $inserted++;
//         }
 
//         return response()->json([
//             'success'  => true,
//             'message'  => "COA groups loaded successfully.",
//             'inserted' => $inserted,
//             'skipped'  => $skipped,
//         ], 201);
//     }

// public function loadDefaultAccounts(Request $request)
//     {
//         $request->validate([
//             'businessid' => 'required|string',
//             'createdby'  => 'required|string',
//             'storeid'    => 'required|string',
//         ]);
 
//         $businessid = $request->businessid;
//         $createdby  = $request->createdby;
//         $storeid    = $request->storeid;
 
//         $data_accounts = [
//             ['accountcode' => '100001', 'accountname' => 'Sales',            'accounttype' => 'Sales',    'accountsubtype' => '100', 'status' => 1],
//             ['accountcode' => '220001', 'accountname' => 'Cost of Sales',    'accounttype' => 'Expenses', 'accountsubtype' => '220', 'status' => 1],
//             ['accountcode' => '220002', 'accountname' => 'Suspense Account', 'accounttype' => 'Expenses', 'accountsubtype' => '220', 'status' => 1],
//             ['accountcode' => '400001', 'accountname' => 'Stock',            'accounttype' => 'Assets',   'accountsubtype' => '400', 'status' => 1],
//             ['accountcode' => '401CASH', 'accountname' => 'Cash',             'accounttype' => 'Assets',   'accountsubtype' => '401', 'status' => 1],
//             ['accountcode' => '401MOMO', 'accountname' => 'Momo',              'accounttype' => 'Assets',   'accountsubtype' => '401', 'status' => 1],
//             ['accountcode' => '99999',  'accountname' => 'Capital',          'accounttype' => 'Equity',   'accountsubtype' => '999', 'status' => 2],
//         ];
 
//         $inserted = 0;
//         $skipped  = 0;
 
//         foreach ($data_accounts as $row) {
//             $exists = Coa::where('accountcode', $row['accountcode'])
//                          ->where('businessid', $businessid)
//                          ->exists();
 
//             if ($exists) {
//                 $skipped++;
//                 continue;
//             }
 
//             Coa::create([
//                 'accountcode'    => $row['accountcode'],
//                 'accountname'    => $row['accountname'],
//                 'accounttype'    => $row['accounttype'],
//                 'accountsubtype' => $row['accountsubtype'],
//                 'storeid'        => $storeid,
//                 'businessid'     => $businessid,
//                 'status'         => $row['status'],
//                 'createdby'      => $createdby,
//                 'modifiedby'     => $createdby,
//             ]);
 
//             $inserted++;
//         }
 
//         return response()->json([
//             'success'  => true,
//             'message'  => 'COA accounts loaded successfully.',
//             'inserted' => $inserted,
//             'skipped'  => $skipped,
//         ], 201);
//     }



//      public function loadDefaultLedgerSetup(Request $request)
//     {
//         $request->validate([
//             'startdate'   => 'required|date',
//             'enddate'     => 'required|date|after:startdate',
//             'suspense'    => 'required|string',
//             'pettycash'   => 'required|string',
//             'funds'       => 'required|string',
//             'receivables' => 'required|string',
//             'payables'    => 'required|string',
//             'stock'       => 'required|string',
//             'sales'       => 'required|string',
//             'costofsales' => 'required|string',
//             'storeid'     => 'required|string',
//             'businessid'  => 'required|string',
//             'createdby'   => 'required|string',
//         ]);
 
//         $exists = BusinessSetup::where('businessid', $request->businessid)
//                                ->where('storeid',    $request->storeid)
//                                ->exists();
 
//         if ($exists) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Ledger setup already exists for this business and store.',
//             ], 409);
//         }
 
//         $setup = BusinessSetup::create([
//             'startdate'   => $request->startdate,
//             'enddate'     => $request->enddate,
//             'suspense'    => $request->suspense,
//             'pettycash'   => $request->pettycash,
//             'funds'       => $request->funds,
//             'receivables' => $request->receivables,
//             'payables'    => $request->payables,
//             'stock'       => $request->stock,
//             'sales'       => $request->sales,
//             'costofsales' => $request->costofsales,
//             'storeid'     => $request->storeid,
//             'businessid'  => $request->businessid,
//             'createdby'   => $request->createdby,
//             'modifiedby'  => $request->createdby,
//         ]);
 
//         return response()->json([
//             'success' => true,
//             'message' => 'Ledger setup created successfully.',
//             'data'    => $setup,
//         ], 201);
//     }

//       public function mainstore(Request $request)
//     {
//         // $request->validate([
//         //     'store_id'            => 'required|string',
//         //     'store_name'          => 'required|string',
//         //     'location'            => 'required|string',
//         //     'phone'               => 'required|string',
//         //     'businessid'          => 'required|string',
//         //     'ledgercode'          => 'required|string',
//         //     'base'                => 'required|boolean',
//         //     'retail'              => 'required|boolean',
//         //     'headoffice'          => 'required|boolean',
//         //     'storecode'           => 'required|string',
//         //     'allowoutofstocksale' => 'required|boolean',
//         // ]);
 
//         // $exists = Store::where('store_id',   $request->store_id)
//         //                ->where('businessid', $request->businessid)
//         //                ->exists();
 
//         // if ($exists) {
//         //     return response()->json([
//         //         'success' => false,
//         //         'message' => 'Store already exists for this business.',
//         //     ], 409);
//         // }
 
//         // $store = Store::create([
//         //     'store_id'            => $request->store_id,
//         //     'store_name'          => $request->store_name,
//         //     'location'            => $request->location,
//         //     'phone'               => $request->phone,
//         //     'businessid'          => $request->businessid,
//         //     'ledgercode'          => $request->ledgercode,
//         //     'base'                => $request->base,
//         //     'retail'              => $request->retail,
//         //     'headoffice'          => $request->headoffice,
//         //     'storecode'           => $request->storecode,
//         //     'allowoutofstocksale' => $request->allowoutofstocksale,
//         // ]);
 
//         // return response()->json([
//         //     'success' => true,
//         //     'message' => 'Store created successfully.',
//         //     'data'    => $store,
//         // ], 201);
//     }


// public function loadDefaultsPayOptions(Request $request)
//     {
//         $request->validate([
//             'accountcode' => 'required|string',
//             'storeid'     => 'required|string',
//             'businessid'  => 'required|string',
//             'createdby'   => 'required|string',
//         ]);
 
//         $businessid  = $request->businessid;
//         $storeid     = $request->storeid;
//         $accountcode = $request->accountcode;
//         $createdby   = $request->createdby;
 
//         $data_payoptions = [
//             ['paymentoption' => 'Cash', 'accountcode' => '401CASH', 'status' => 1],
//             ['paymentoption' => 'Momo', 'accountcode' => '401MOMO', 'status' => 1],

//         ];
 
//         $inserted = 0;
//         $skipped  = 0;
 
//         foreach ($data_payoptions as $row) {
//             $exists = PaymentOption::where('paymentoption', $row['paymentoption'])
//                                    ->where('businessid',    $businessid)
//                                    ->where('storeid',       $storeid)
//                                    ->exists();
 
//             if ($exists) {
//                 $skipped++;
//                 continue;
//             }
 
//             PaymentOption::create([
//                 'paymentoption' => $row['paymentoption'],
//                 'accountcode'   => $row['accountcode'],
//                 'storeid'       => $storeid,
//                 'businessid'    => $businessid,
//                 'status'        => $row['status'],
//                 'createdby'     => $createdby,
//                 'modifiedby'    => $createdby,
//             ]);
 
//             $inserted++;
//         }
 
//         return response()->json([
//             'success'  => true,
//             'message'  => 'Default payment options loaded successfully.',
//             'inserted' => $inserted,
//             'skipped'  => $skipped,
//         ], 201);
//     }
// }


    }
    }