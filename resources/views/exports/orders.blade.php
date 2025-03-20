<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 32px;font-weight: bold;background-color: #FFC414;color: white;text-align: center"><strong>DOWNTOWN</strong></th>
        </tr>
        <tr>
            <th colspan="9" style="font-size: 18px;font-weight: bold;text-align:center"><strong>Orders</strong></th>
        </tr>
        <tr>
            <th style="width: 50px">ID</th>
        <th style="width: 100px">Type</th>
        <th style="width: 250px">Customer</th>
        <th style="width: 100px">Table No.</th>
        <th style="width: 100px">Payment Status</th>
        <th style="width: 100px">Status</th>
        <th style="width: 250px">Created By</th>
        <th style="width: 100px">Created At</th>
        <th style="width: 250px">Price</th>
        </tr>
        @if(@$applied_filters)
        <tr>
            <td colspan="9" style="color: #FFC414">Applied Filters: {{$applied_filters}}</td>
        </tr>
        @endif
    </thead>
    <tbody>
        @foreach ($data as $d)
        <tr>
            <td>{{$d->id}}</td>
            <td>{{$d->type}}</td>
            <td>{{@$d->customer->first_name . ' ' . @$d->customer->last_name}}</td>
            <td>{{$d->table_number}}</td>
            <td>{{$d->payment_status}}</td>
            <td>{{$d->status}}</td>
            <td>{{@$d->user->first_name . ' ' . @$d->user->last_name}}</td>
            <td>{{date("Y-m-d H:iA", strtotime($d->created_at))}}</td>
            <td>{{$d->total_price}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
