<!DOCTYPE html>
<html>

<head>
    <title>Payment</title>
</head>

<body>
    <h1>Complete Your Payment</h1>
    @if (isset($amount))
        <div
            style="display: flex;justify-content: center;width: 61%;color:orangered;align-items: center;font-size: 20px;font-weight: 700;background: lightgrey;margin-left: 363px;margin-bottom: 5px;">        </div>
    @endif

    @if (isset($payUrl))
        <iframe src="{{ $payUrl }}" width="100%" height="600px" frameborder="0"></iframe>
    @else
        <p>Payment URL not available.</p>
    @endif
</body>

</html>
