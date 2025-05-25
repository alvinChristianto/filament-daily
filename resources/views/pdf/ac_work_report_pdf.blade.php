<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AC service Bill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            /* margin: 2px; */
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .flex-container {
            display: flex;
            justify-content: space-around;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;

        }

        .details th,
        .summary th {
            border: 1px solid #ddd;
            padding: 2px;
        }

        .details td,
        .summary td {
            border: 1px solid #ddd;
            padding: 2px;
        }

        .total {
            font-weight: bold;
        }

        .roominfo {
            width: 100%;
            border: 1px solid #ddd;
            margin-top: 10px;
        }

        .roominfo th {
            width: 100%;
            border: 0px solid #ddd;
        }

        .roomrate {
            width: 100%;
            border: 1px solid #ddd;
            margin-top: 10px;
            background-color: rgb(133, 243, 166);
        }

        .roomrate th {
            width: 100%;
            background-color: rgb(133, 243, 166);
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
            background-color: #caf0f8;
        }

        .signature {
            width: 100%;
            border-collapse: collapse;
            padding-top: 40px;
            margin-bottom: 2px;
            text-align: center;

        }
    </style>
</head>

<body>

    <table class="header">
        <h2>INVOICE AC</h2>
        <tr>
            <th style="text-align: left;">
                <h2>Kembar Sejati Teknik</h2>
                <p>Jl. Merpati No.17, Nglarang Lor, Sidoarum, Kec. Godean, Kabupaten Sleman, DIY </p>

                <p>No. Telp : 0813-9299-5755</p>
                <p>Email : youleeanto05@gmail.com</p>
            </th>
            <th style="text-align: right;">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/logo.jpg'))) }}" style="width: 150px; height: 150px;">
            </th>
        </tr>
    </table>

    <table class="details">
        <tr>
            <th style="text-align: left;">
                <p>Nama Customer</p>
                <p style="font-weight: 400; font-size: 20px; color: blue">{{ $record1->customer_name }}</p>
            </th>
            <th style="text-align: left;">
                <p>Invoice No : </p>
                <p style="font-weight: 400; font-size: 20px; color: blue">{{ $record1->id_report }} ({{ $record1->created_at }})</p>

            </th>

        </tr>
    </table>

    <table class="roomrate">
        <thead>
            <tr>
                <th style="text-align: left;">
                    <p>Sparepart</p>
                </th>
                <th style="text-align: left;">
                    <p>Qty</p>
                </th>
                <th style="text-align: left;">
                    <p>Rate</p>
                </th>
                <th style="text-align: left;">
                    <p>Subtotal</p>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction_detail as $detail)
            <tr>
                <th style="text-align: left;">
                    <p style="font-weight: 400;">{{ $detail->name_sparepart }} ( ID {{ $detail->id_sparepart }})</p>
                </th>
                <th style="text-align: left;">
                    <p style="font-weight: 400;">{{ $detail->amount }}</p>
                </th>
                <th style="text-align: left;">
                    <p style="font-weight: 400;">{{ $detail->price_sell_sparepart }}</p>
                </th>
                <th style="text-align: left;">
                    <p>Rp {{ $detail->price_per }}</p>
                </th>
            </tr>
            @endforeach
        </tbody>
        <th style="text-align: left;">
            <p>Total Pembelian</p>
            <p>Diskon : Rp {{ $record1->discount }}</p>
            <p style="font-weight: 400; font-size: 20px; color: blue">Total Harga : Rp {{ $record1->total_price }}</p>
        </th>

    </table>

    <table class="roominfo">
        <th style="text-align: left;">
            <p> Informasi tambahan</p>
            <p style="font-weight: 400;">Metode Pembayaran {{ $record1->payment_name }}</p>
            <p style="font-weight: 400;">Service Selanjutnya pada {{ $record1->next_service_date }}</p>
        </th>
        </tr>
    </table>

    <table class="signature">
        <tr>

            <td class="signatures">
                <p>____________________</p>
                <p>Admin Signature</p>
            </td>
        </tr>
    </table>

</body>

</html>