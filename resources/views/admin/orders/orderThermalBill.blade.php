<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Grosav | Invoice</title>

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
                    <h4>Grosav</h4>
                </div>

                <div class="float-right">
                    <br>
                    <strong>DATE: {{ $order->created_at->format('d-m-Y') }}</strong> <br>
                    <strong>Bill NO: {{ $order->unique_order_id }}</strong>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="mb-3">PAYMENT MODE : {{ $order->payment_mode }}
                        </h6>
                        <div>Ordered At : {{ $order->created_at->format('d-m-Y h:i:s A') }}</div>
                        <div>Delivered At : {{ $order->updated_at->format('d-m-Y h:i:s A') }}</div>
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
                        @php
                            $i = 1;
                        @endphp
                        <tbody>
                            @foreach ($order->orderitems as $orderitem)
                                <tr>
                                    <td class="center">{{ $i }}</td>
                                    <td class="left">{{ $orderitem->name }} <br />
                                        @if ($orderitem->orderitemaddons)
                                            <ul class="ms-3">
                                                @foreach ($orderitem->orderitemaddons as $addon)
                                                    @if ($addon->is_combo)
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-tags-fill"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2zm3.5 4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
                                                            <path
                                                                d="M1.293 7.793A1 1 0 0 1 1 7.086V2a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l.043-.043-7.457-7.457z" />
                                                        </svg>
                                                        <span>{{ $addon->name }} - ₹{{ $addon->price }}</span><br>
                                                    @elseif($addon->type = 'MULTIPLE')
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-plus"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                                        </svg>
                                                        <span>{{ $addon->name }} - ₹{{ $addon->price }}</span><br>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>

                                    <td class="left">{{ $orderitem->quantity }}</td>
                                    <td class="right">
                                        ₹{{ $orderitem->price * $orderitem->quantity }}</td>
                                    <td class="right">
                                        ₹{{ $orderitem->price * $orderitem->quantity }}
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-sm-5 ml-auto ">
                        <table class="table table-clear ">
                            <th><strong> Payment Details </strong></th>
                            <tbody>
                                <tr style="page-break-after: always;">
                                    <td class="left">

                                        Sub Total: ₹{{ number_format((float) $order->sub_total, 2, '.', '') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left">
                                        Delivery Charge:
                                        ₹{{ number_format((float) $order->delivery_charge, 2, '.', '') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left">
                                        Tax: ₹{{ $order->tax }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left">
                                        Total: ₹{{ number_format((float) $order->total, 2, '.', '') }}
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
