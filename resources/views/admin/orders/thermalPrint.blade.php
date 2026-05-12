<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zeato | Theminal Print</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />

</head>


<body>

    <div id="bill" class="container">

        <div onclick="printDiv('bill')" class="btn btn-primary" style="position: fixed; right: 5vh; top: 5vh;">
            Print
        </div>

        <div class="card">
            <div class="card-header">
                <div class="text-center">
                    <h4>Zeato</h4>
                </div>

                <div class="float-right">
                    <br>
                    <strong>DATE: {{ $order->created_at->format('Y-m-d') }}</strong> <br>
                    <strong>Bill NO: {{ $order->unique_order_id }}</strong>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h4>{{ $order->restaurant->name }}</h4>
                        <h6 class="mb-3">PAYMENT MODE : ONLINE</h6>
                        <div>Ordered At : {{ $order->payment_mode }}</div>
                        <div>Delivered At : {{ $order->order_delivered_at ? $order->order_delivered_at : 'N/A' }}</div>
                    </div>
                </div>
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="center">No</th>
                                <th>Item</th>
                                <th class="left">Quantity</th>
                                <th class="center">Unit Price</th>
                                <th class="right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($order->order_items as $key => $item)
                                <tr>
                                    <td class="center">{{ $key + 1 }}</td>
                                    <td class="left">{{ $item->name }}</td>
                                    <td class="left">{{ $item->quantity }}</td>
                                    <td class="right">₹{{ $item->price }}</td>
                                    <td class="right">₹{{ $item->quantity * $item->price }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-sm-5 ml-auto ">
                        <table class="table table-clear ">
                            <th><strong> Payment Details </strong></th>
                            <tbody>
                                <tr>
                                    <td class="left">
                                        Sub Total: ₹{{ $order->sub_total }}
                                    </td>
                                </tr>

                                @if ($order->delivery_charge)
                                    <tr>
                                        <td class="left">
                                            Delivery Charge: ₹{{ $order->delivery_charge }}
                                        </td>
                                    </tr>
                                @endif

                                @if ($order->tip_amount)
                                    <tr>
                                        <td class="left">
                                            Tip Amount: ₹{{ $order->tip_amount }}
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td class="left">
                                        Total: <strong>₹{{ $order->total }}</strong>
                                    </td>
                                </tr>
                            <tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>

</body>

</html>
