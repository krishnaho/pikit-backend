<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <title>@yield('title')</title>
    <meta charset="utf-8" />
    <meta name="description" content="SAFA Group of companies | Dashboard">
    <meta name="author" content="Howin Cloud">
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Metronic - Bootstrap 5 HTML, VueJS, React, Angular &amp; Laravel Admin Dashboard Theme" />
    <meta property="og:url" content="" />
    <meta property="og:site_name" content="" />
    <link rel="canonical" href="" />
    @php
        $path = asset('/');
    @endphp
    <link rel="icon" href="{{ asset('assets/images/logo-sqare.png') }}" type="image/png" sizes="16x16" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Page Vendor Stylesheets(used by this page)-->
    <link href="{{ $path }}assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ $path }}assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <link href="{{ $path }}assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" />
    <!--end::Page Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ $path }}assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ $path }}assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <script src="{{ $path }}assets/plugins/global/plugins.bundle.js"></script>

    <!--end::Global Stylesheets Bundle-->
    <style>
        [list]::-webkit-calendar-picker-indicator {
            display: none;
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed">
    <div class="d-flex flex-column flex-root">
        <div class="row page d-flex flex-row flex-column-fluid">
            <div class=" d-flex flex-column " id="kt_wrapper">

                <!--begin::Header-->
                @include('admin.includes.header')
                <!--end::Header-->
                <div class="mt-18 ">
                    @yield('content')

                </div>

                @include('admin.includes.footer')
                @include('admin.includes.toastr')
            </div>
        </div>
        <!--begin::Global Javascript Bundle(used by all pages)-->
        <script src="{{ $path }}assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->
        <!--begin::Page Vendors Javascript(used by this page)-->

        <script src="{{ $path }}assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
        <script src="{{ $path }}assets/plugins/custom/datatables/datatables.bundle.js"></script>

        <!--end::Page Vendors Javascript-->
        <!--begin::Page Custom Javascript(used by this page)-->
        <script src="{{ $path }}assets/js/widgets.bundle.js"></script>
        <script src="{{ $path }}assets/js/custom/widgets.js"></script>
        <script src="{{ $path }}assets/js/custom/apps/chat/chat.js"></script>
        <script src="{{ $path }}assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
        <script src="{{ $path }}assets/js/custom/utilities/modals/create-app.js"></script>
        <script src="{{ $path }}assets/js/custom/utilities/modals/create-campaign.js"></script>
        <script src="{{ $path }}assets/js/custom/utilities/modals/users-search.js"></script>
        <script src="{{ $path }}assets/js/custom/documentation/general/datatables/api.js"></script>
        <script src="{{ $path }}assets/js/custom/documentation/forms/daterangepicker.js"></script>
        <script src="{{ $path }}assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
        <script src="{{ $path }}assets/js/custom/documentation/forms/formrepeater/basic.js"></script>
        <script src="{{ $path }}assets/js/custom/documentation/base/forms/advanced.js"></script>
        <script src="{{ $path }}assets/plugins/custom/tinymce/tinymce.bundle.js"></script>

        {{-- tinymce --}}
        <!--end::Page Custom Javascript-->
        <!--end::Javascript-->
        <script src="https://cdn.tiny.cloud/1/xglc7avqzi80ti3arayewa85tmhzedfpl4iioptnx4dzgxvr/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
        <script>
            tinymce.init({
                selector: '.myeditorinstance',
                plugins: ' autolink link image paste imagetools lists  searchreplace   code  autolink lists  media   preview table   help',
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify  | searchreplace link image | print preview media fullpage | outdent indent  addcomment showcomments   code   table help ',
                toolbar_mode: 'floating',
                menu: {
                    favs: {
                        title: 'My Favorites',
                        items: 'code visualaid | searchreplace | emoticons'
                    }
                },
                menubar: 'favs file edit view insert view format tools table help',
                // image_uploadtab: true,
                images_file_types: 'jpg,svg,webp,png',
                tinycomments_mode: 'embedded',
                tinycomments_author: 'Author name',
                /* enable title field in the Image dialog*/
                image_title: true,
                /* enable automatic uploads of images represented by blob or data URIs*/
                automatic_uploads: true,
                file_picker_types: 'image',
                /* and here's our custom image picker*/
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');


                    input.onchange = function() {
                        var file = this.files[0];

                        var reader = new FileReader();
                        reader.onload = function() {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            /* call the callback and populate the Title field with the file name */
                            cb(blobInfo.blobUri(), {
                                title: file.name
                            });
                        };
                        reader.readAsDataURL(file);
                    };

                    input.click();
                },
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            });
        </script>
</body>
<!--end::Body-->

</html>
