@extends('admin.layout.master')
@section('title')
    Orders
@endsection
@section('content')
    <div class="card ">
        <div class="card-header">
            <h3 class="title mt-7">
                Orders
            </h3>
        </div>
        <div class="card-body">
            <form action="">
                @csrf
                <div class="text-end">

                    <button type="submit" formaction="{{ route('admin.exportAllOrder') }}"
                        class="btn btn-light-success btn-lg"><i class="bi bi-cloud-arrow-down-fill"></i>Export</button>
                </div>
            </form>
            <div>
                <table id="orders" class="table align-middle table-row-dashed fs-6 gy-5 ">
                    <thead>
                        <tr class="text-start fw-bolder fs-7 text-uppercase gs-0">
                            <th>Unique Order ID</th>
                            <th>Order Status</th>
                            <th>Store</th>
                            <th>Total</th>
                            <th>City</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            var datatable = $('#orders').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                responsive: true,
                lengthMenu: [10, 25, 50, 100, 200, 500, 10000],
                order: [
                    [5, "desc"]
                ],
                ajax: '{{ route('datatable.Order') }}',
                columns: [{
                        data: 'unique_order_id',
                        name: 'unique_order_id'
                    },
                    {
                        data: 'orderstatus',
                        name: 'orderstatus.name',
                        sortable: false,
                    },
                    {
                        data: 'store',
                        name: 'store.name',
                        sortable: false
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'city',
                        name: 'city.name',
                        sortable: false
                    },
                    {
                        data: 'created_at',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false
                    },
                ],
            });
        });
    </script>
@endsection
