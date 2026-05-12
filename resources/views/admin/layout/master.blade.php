<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->

<head>
    <meta charset="utf-8">
    <meta name="description" content="Zeato Food and Grocery App | Dashboard">
    <meta name="author" content="Howin Cloud">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    @php
        $path = asset('/');
    @endphp
    <link rel="icon" href="/assets/favicon-mL4zeb56.ico" />
    <link rel="stylesheet" href="{{ $path }}assets/css/dashlitee5ca.css">
    <link id="skin-default" rel="stylesheet" href="{{ $path }}assets/css/themee5ca.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/css/toastr.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/js/toastr.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="nk-body bg-lighter npc-general has-sidebar ">
    @include('admin.includes.toastr')
    <div class="nk-app-root user-select-none">
        <div class="nk-main">
            @include('admin.includes.sidebar')

            <div class="nk-wrap ">
                @include('admin.includes.header')
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            @yield('content')
                        </div>
                    </div>
                </div>
                @include('admin.includes.footer')
            </div>
        </div>
    </div>
    <script src="{{ $path }}assets/toastr/toastr.js"></script>
    <script src="{{ $path }}assets/js/bundlee5ca.js"></script>
    <script src="{{ $path }}assets/js/scriptse5ca.js"></script>
    <script src="{{ $path }}assets/js/demo-settingse5ca.js"></script>
    <script src="{{ $path }}assets/js/charts/chart-ecommercee5ca.js"></script>
    <script>
        var path = "{{ $path }}";
    </script>
    <script src="{{ $path }}assets/plugins/custom/tinymce/tinymce.bundle.js"></script>
    <script src="https://cdn.tiny.cloud/1/xglc7avqzi80ti3arayewa85tmhzedfpl4iioptnx4dzgxvr/tinymce/5/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script>
        tinymce.init({
            selector: '.myeditorinstance',
            plugins: ' autolink link image paste imagetools lists  searchreplace   code  autolink lists  media   preview table   help',
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons',
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/js/toastr.js"></script>
</body>

</html>
