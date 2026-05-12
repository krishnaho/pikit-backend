<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zeato | Invoice</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700&display=swap"
        rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Gilroy';
            src: url('{{ asset('assets/fonts/Gilroy-Black.ttf') }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url('{{ asset('assets/fonts/Gilroy-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        /* @font-face {
            font-family: 'Gilroy';
            src: url('{{ asset('assets/fonts/Gilroy-ExtraBold.ttf') }}') format('truetype');
            font-weight: 800;
            font-style: normal;
        } */

        @font-face {
            font-family: 'Gilroy';
            src: url('{{ asset('assets/fonts/Gilroy-Medium.ttf') }}') format('truetype');
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url('{{ asset('assets/fonts/Gilroy-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url('{{ asset('assets/fonts/Gilroy-SemiBold.ttf') }}') format('truetype');
            font-weight: 600;
            font-style: normal;
        }

        * {
            font-family: 'Gilroy', sans-serif !important;
        }

        .roboto-text {
            font-family: 'Roboto', sans-serif !important;
            font-weight: 400 !important;
            font-size: 8px !important;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #bill,
            #bill * {
                visibility: visible;
            }

            #bill {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .container {
                page-break-inside: avoid;
            }

            .table-responsive-sm {
                page-break-inside: auto;
            }

            .table thead {
                display: table-header-group;
            }

            .table tfoot {
                display: table-footer-group;
            }

            .table tbody tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .table td,
            .table th {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .card {
                page-break-inside: avoid;
            }

            .dashed-border {
                border: 2px dashed #000;
            }

            .dashed-bottom {
                border-bottom: 2px dashed #000;
            }

            hr.dashed-line {
                border: none;
                border-bottom: 2px dashed #000;
            }

            .content-container {
                margin: 0 auto;
            }
        }

        .dashed-border {
            border: 1px dashed #000;
        }

        .dashed-bottom {
            border-bottom: 1px dashed #000;
        }

        hr.dashed-line {
            border: none;
            border-bottom: 1.5px dashed #000;
        }

        .content-container {
            margin: 0 auto;
        }
    </style>
</head>

<body>
    @php
        info("ORder BILL");
        info($order);
    @endphp
    <div id="bill" class="text-center pt-5 content-container" style="width: 95%">
        <h3 style="font-size: 20px; font-weight: 900;">
            {{ $order->restaurant->name }}
        </h3>
        <h6 style="font-size: 10px; font-weight: 500;">{{ $order->created_at->format('D, M d, Y • h:i:s A') }}</h6>
        <div class="pt-3 pb-3">
            <div style="position: relative;">
                <div
                    style="position: absolute; top: -0.5rem; left: 50%; transform: translateX(-50%); background-color: white; padding: 0 0.9rem; font-size: 10px; font-weight: 700;">
                    Delivery
                </div>
                <div style="border-radius: 0.5rem;" class="rounded-lg p-3 dashed-border ">
                    <div class="font-weight-bold" style="font-size: 14px">Order# {{ $order->unique_order_id }}</div>
                </div>
            </div>
        </div>

        @if ($order->customer_phone != null)
            <div class="pt-2"
                style="display: flex; justify-content: space-between; font-size: 10px; font-weight: 500;">
                <div>Customer phone:</div>
                <div>{{ $order->customer_phone }}</div>
            </div>
            <hr class="dashed-line">
        @endif

        <div class="font-weight-bold " style="font-weight:700;">Payment Mode: {{ $order->payment_mode }}</div>
        <hr class="dashed-line">
        @foreach ($order->orderitems as $orderitem)
            <div class="py-2 "
                style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500; font-weight: 500;">
                <div style="width: 70%; text-align: left;">
                    {{ $orderitem->name }} x {{ $orderitem->quantity }}
                    @if (isset($orderitem->orderitemaddons) && $orderitem->orderitemaddons->isNotEmpty())
                        ({{ implode(', ', array_column($orderitem->orderitemaddons->toArray(), 'name')) }})
                    @endif
                </div>
                <!-- Price -->
                <div style="width: 29%; text-align: right;">
                    ₹
                    {{ $orderitem->price * $orderitem->quantity + (isset($orderitem->orderitemaddons) && !empty($orderitem->orderitemaddons) ? array_sum(array_column($orderitem->orderitemaddons->toArray(), 'price')) : 0) }}
                </div>
            </div>
        @endforeach

        <hr class="dashed-line">
 <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Customer Details</div>
            {{-- <div>AED {{ number_format((float) $order->total, 2, '.', '') }}</div> --}}
        </div>
        <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Name</div>
            <div> {{ $order->user->name }}</div>
        </div>
        <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Phone</div>
            <div>{{ $order->user->phone }}</div>
        </div>
        <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Address</div>
            <div>{{ $order->address}}</div>
        </div>
         <hr class="dashed-line">
        <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Sub Total</div>
            <div>₹ {{ number_format((float) $order->sub_total, 2, '.', '') }}</div>
        </div>

        <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Delivery Fee</div>
            <div>
                {{ $order->delivery_charge != 0 ? '₹ ' . number_format((float) $order->delivery_charge, 2, '.', '') : 'FREE' }}
            </div>
        </div>

        <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Service Fee</div>
            <div>₹ {{ $order->platform_fee }}</div>
        </div>

        <div
            style="width: 100%; height: auto; font-size: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
            <div>Total</div>
            <div>₹ {{ number_format((float) $order->total, 2, '.', '') }}</div>
        </div>

       

        <hr class="dashed-line">

        <div class="text-left roboto-text">
            Vat (Incl), Thank you for ordering with us!<br />
            We look forward to serving you again. Have a great day!
        </div>

        <div style="text-align: center;" class="mt-4">
            <img src="{{ asset('assets/images/zeato-bill.png') }}">
        </div>

</body>

{{-- <script>
    window.onload = function() {
        window.print();
    };
</script> --}}

</html>
