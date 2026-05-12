@extends('admin.layout.master')
@section('title')
    Order Reviews
@endsection
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Reviews</h4>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table card-bordered" id="reviews" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="col">Id</th>
                                <th class="col">Order ID</th>
                                <th class="col">Customer Name</th>
                                <th class="col">Restaurant Name</th>
                                <th class="col">Review</th>
                                <th class="col">Rating</th>
                                <th class="col">Rated At</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            var datatable = $('#reviews').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                responsive: true,
                filter: true,
                dom: "<'row'" +
                    "<'col-6 d-flex align-items-center justify-content-start'f>" +
                    "<'col-6 d-flex align-items-center justify-content-end'l>" +
                    ">" +
                    "<'table-responsive my-2'tr>" +
                    "<'row'" +
                    "<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'i>" +
                    "<'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>" +
                    ">",
                order: [
                    [0, "desc"]
                ],
                ajax: '{{ route('admin.getAllReviews') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'order_id',
                        sortable: false
                    },
                    {
                        data: 'user_name',
                        sortable: false
                    },
                    {
                        data: 'store_name',
                        sortable: false
                    },
                    {
                        data: 'review',
                        sortable: false
                    },
                    {
                        data: 'rating',
                        sortable: false
                    },
                    {
                        data: 'rated_at',
                        sortable: false
                    },
                ],

            });
            var search = document.querySelector('.search');
            html = search.innerHTML
            search.innerHTML = '<span class="fw-3 px-2">Show</span>' + html +
                '<span class="fw-3 px-2">Entries</span>'
        });
    </script>
@endsection
