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

    public function laundryTransaction($id)
    {
        $record1 = DB::table('laundry_transactions')
            ->join('laundry_customers', 'laundry_transactions.id_customer', '=', 'laundry_customers.id')
            ->join('payments', 'laundry_transactions.id_payment', '=', 'payments.id')
            ->where('laundry_transactions.id_transaction', $id)
            ->select('laundry_transactions.*', 'ac_customers.name as customer_name', 'payments.name as payment_name')
            ->first();
        // $transaction_detail = json_decode($record1->transaction_detail);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.laundry_transaction_pdf', compact('record1', 'transaction_detail')); // Pass the variable $record to the blade file
        return $pdf->stream(); // renders the PDF in the browser
    }

    public function acWorkReport($id)
    {
        $record1 = DB::table('ac_working_reports')
            ->join('ac_customers', 'ac_working_reports.id_customer', '=', 'ac_customers.id')
            ->join('payments', 'ac_working_reports.id_payment', '=', 'payments.id')
            ->where('ac_working_reports.id_report', $id)
            ->select('ac_working_reports.*', 'ac_customers.name as customer_name', 'payments.name as payment_name')
            ->first();
        $transaction_detail = json_decode($record1->transaction_detail);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.ac_work_report', compact('record1', 'transaction_detail')); // Pass the variable $record to the blade file
        return $pdf->stream(); // renders the PDF in the browser
    }
}
