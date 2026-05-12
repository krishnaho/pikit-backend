@extends('admin.layout.master')
@section('title')
    Users With Orders
@endsection

@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title page-title">Users With Orders</h4>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table table-bordered" id="usersWithOrdersTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Total Orders</th>
                                <th>Last Order</th>
                            </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function () {
        $('#usersWithOrdersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('restaurantOwner.usersWithOrdersAjax') }}',
                error: function(xhr, status, error) {
                }
            },
            columns: [
                { data: 'user_id', name: 'user_id' },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                { data: 'order_count', name: 'order_count' },
                { data: 'last_order', name: 'last_order' },
            ]
        });
    });
</script>
@endsection

@push('scripts')
  

@endpush
