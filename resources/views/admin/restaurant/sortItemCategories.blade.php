@extends('admin.layout.master')
@section('title')
      Item Category
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-9 col-xl-10 order-2 order-sm-1 mt-3">
                <h2 class="h6"><strong>Sort Item category</strong></h2>
                <div id="sortablelist" class="list-group mb-4 mt-3" data-id="1" onchange="onSort();">
                    @foreach ($itemcategories as $itemCategory)
                        <div class="list-group-item each-slide d-flex align-items-center justify-content-between"
                            data-id="{{ $itemCategory->id }}">
                            <div>
                                <p class="mb-0 d-inline-flex align-items-center">
                                    {{ $itemCategory->name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="token" value="{{ csrf_token() }}">

    <style>
        .list-group .list-group-item {
            border-radius: 0;
            cursor: move;
        }

        .list-group .list-group-item:hover {
            background-color: #f7f7f7;
        }

    </style>




    <script>
        $(document).ready(function() {
            console.log('hii');
            new Sortable(sortablelist, {
                animation: 150,
                ghostClass: 'sortable-ghost'
            });

        });

        function onSort() {
            let newSortOrder = {};
            $('.each-slide').each(function() {
                newSortOrder[$(this).index()] = $(this).data('id');
            });
            console.log(newSortOrder);
            $.ajax({
                  url: '{{ route('admin.sortItemCategory') }}',
                  type: 'POST',
                  dataType: 'JSON',
                  data: {newOrder: newSortOrder, _token: $('#token').val()},
              })
              .done(function(res) {
                   $.jGrowl("Slides sorted successfully", {
                       position: 'bottom-center',
                       header: 'Done ✅',
                       theme: 'bg-success',
                       life: '2000',
                   }); 
              })
        }
    </script>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Sortable.js -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <!-- Include KTUtil (if using a theme that requires it) -->
    <script src="path/to/KTUtil.js"></script>

    <!-- Include TinyMCE if needed -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
@endsection
