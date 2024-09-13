<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Courier New', monospace;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        .receipt-footer {
            text-align: center;
            border-top: 2px dashed #000;
            padding-top: 10px;
        }

        table {
            width: 100%;
        }

        th,
        td {
            text-align: left;
            padding: 5px;
        }

        .total-row {
            font-weight: bold;
        }

        .centered {
            text-align: center;
        }

        .barcode {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="receipt-header">
            <p style="font-weight: bold;">{{ settings()->company_name }}</p>
            <p style="border-bottom: 2px dashed #000; margin-bottom: 10px; padding-bottom: 10px;">
                Mail: {{ settings()->company_email }}<br>
                Tel: {{ settings()->company_phone }}</p>
            <p style="border-bottom: 2px dashed #000; padding-bottom: 10px;">
                Date:{{ \Carbon\Carbon::parse($sale->date)->format('d M,Y') }}
                Invoice No:{{ $sale->reference }}
            </p>
        </div>
        <div class="receipt-body">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Shop Price</th>
                        <th>Qty</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalDiscount = 0; ?> <!-- Initialize total discount amount -->
                    @foreach($sale->saleDetails as $saleDetail)
                    <tr>
                        <td>{{ $saleDetail->product->product_name }}</td>
                        <td>{{ ($saleDetail->unit_price) }}.00</td>
                        <td>{{format_currency($saleDetail->sub_total / $saleDetail->quantity) }}</td>
                        <td>{{ floor($saleDetail->quantity) == $saleDetail->quantity ? number_format($saleDetail->quantity, 0) : $saleDetail->quantity }}</td>
                        <td>{{ ($saleDetail->sub_total) }}.00</td>
                        <?php $totalDiscount += ($saleDetail->product_discount_amount) * ($saleDetail->quantity); ?> <!-- Add item discount to total discount -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="receipt-footer">
            <table>
                <tr>
                    <td>Discount (-)</td>
                    <td>{{ format_currency($totalDiscount) }}</td> <!-- Display total discount -->
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td>{{ format_currency($sale->total_amount) }}</td>
                </tr>

                <tbody>
                    <tr style="background-color:#ddd;">
                        <td class="centered" style="padding: 5px;">
                            Paid By: {{ $sale->payment_method }}
                        </td>
                        <td class="centered" style="padding: 5px;">
                            Cash: {{ format_currency($sale->paid_amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <table>
                <tr style="text-align: left; padding: 5px;">
                    <td>Balance (-)</td>
                    <td style="text-align: left; padding: 5px;">{{ format_currency($sale->paid_amount - $sale->total_amount) }}</td>

                </tr>
            </table>

            <br>
            THANK YOU!
            <br>
            <tr style="border-bottom: 0;">
                <td class="centered" colspan="3">
                    <div class="barcode">
                        {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeSVG($sale->reference, 'C128', 1, 25, 'black', false) !!}
                    </div>
                </td>
            </tr>
            <br>
            Developed By: K/H CODEHUB
            <br>
        </div>
        <br>
    </div>
</body>

</html>