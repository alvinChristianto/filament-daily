<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DownloadPDFController extends Controller
{

    public function sparepartShipment($id)
    {
        // dd($id);
        $record1 = DB::table('sparepart_transaction_shipments')
            ->join('warehouses', 'sparepart_transaction_shipments.id_warehouse', '=', 'warehouses.id')
            ->join('payments', 'sparepart_transaction_shipments.id_payment', '=', 'payments.id')
            ->where('sparepart_transaction_shipments.id_transaction', $id)
            ->select('sparepart_transaction_shipments.*', 'warehouses.name as customer_name', 'payments.name as payment_name')
            ->first();
        $transaction_detail = json_decode($record1->transaction_detail);
        // dd($record1);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.sparepart_shipment_pdf', compact('record1', 'transaction_detail')); // Pass the variable $record to the blade file
        return $pdf->stream(); // renders the PDF in the browser
    }
}
