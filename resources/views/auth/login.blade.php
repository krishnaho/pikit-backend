<!doctype html>
<html lang="en">

<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="description" content="Zeato Food App | Dashboard">
    <meta name="author" content="Howin Cloud">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="/assets/favicon-mL4zeb56.ico" />
    @php
        $path = asset('/');
    @endphp
    <link rel="stylesheet" href="{{ $path }}assets/css/dashlitee5ca.css">
    <link id="skin-default" rel="stylesheet" href="{{ $path }}assets/css/themee5ca.css">
</head>


<body class="nk-body bg-white npc-default pg-auth user-select-none">
    <div class="nk-app-root">
        <div class="nk-main ">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">

                        <div class="brand-logo text-center fw-bold">
                            <div class="flex-grow-1 d-flex flex-column align-items-center mt-4"
                                style="color:#ffb800;font-weight:600">
                                <div class="h1 safa-logo-text py-2"
                                    style="line-height:45px;color:#ffb800;font-weight:600">
                                    <img class="mx-1" src="{{ asset('assets/images/logo.jpg') }}"
                                        style="width:18rem;object-fit:contain" alt="logo" />
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('login') }}" class="signin-form">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="default-01">Email </label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="email" class="form-control form-control-lg"
                                                placeholder="Email" id="email" class="block mt-1 w-full"
                                                type="email" name="email" :value="old('email')" required autofocus>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="password" :value="__('Password')">Password
                                            </label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control form-control-lg"
                                                placeholder="Password" id="password" class="block mt-1 w-full"
                                                name="password" required autocomplete="current-password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-lg btn-block text-white"
                                            style="background-color:#ffb800">{{ __('Log in') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container-fluid">
                            <div class="nk-footer-wrap">
                                <div class="nk-footer-copyright"> © 2026 pikit App. Powered By Howincloud <a
                                        href="https://howincloud.com" target="_blank"
                                        class="text-dark text-hover-dark me-2" style="  font-weight: 700">Howin<span
                                            style="color: red;  font-weight: 700">Cloud</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('login_asset/js/jquery.min.js') }}"></script>
    <script src="{{ asset('login_asset/js/popper.js') }}"></script>
    <script src="{{ asset('login_asset/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('login_asset/js/main.js') }}"></script>
</body>

</html>


