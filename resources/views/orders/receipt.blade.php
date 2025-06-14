@extends('base')

@section('css')
<style>
    .container {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
    }
    .receipt {
        width: 280px;
        border: 1px dashed #ccc;
        padding: 10px;
        margin-bottom: 20px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }
    .receipt-header {
        text-align: center;
        margin-bottom: 10px;
    }
    .receipt-items {
        margin: 10px 0;
    }
    .item-row {
        display: flex;
        justify-content: space-between;
    }
    .divider {
        border-top: 1px dashed #ccc;
        margin: 5px 0;
    }
    .total {
        display: flex;
        justify-content: space-between;
        font-weight: bold;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .status {
        margin-top: 20px;
        padding: 10px;
        border-radius: 4px;
    }
    .success {
        background-color: #d4edda;
        color: #155724;
    }
    .error {
        background-color: #f8d7da;
        color: #721c24;
    }
    #status-message {
        display: none;
    }
</style>
@stop

@section('content')
<div class="container">
    <h1>Order#{{$order->id}}</h1>


    {{-- <div class="form-group">
        <label for="printer-select">Select Printer:</label>
        <select id="printer-select" class="form-select">
            @if(count($printers) > 0)
                @foreach($printers as $printer)
                    <option value="{{ $printer }}">{{ $printer }}</option>
                @endforeach
            @else
                <option value="">No printers found</option>
            @endif
        </select>
    </div> --}}

    <div class="receipt-items" id="receipt-items" style="font-family: monospace; font-size: 12px; width: 300px;">
        <div style="text-align: center;">

            <strong>DOWNTOWN</strong><br>
            <strong>BAHAWAL NAGAR</strong><br>
            Tel: (063) 2280-988<br>
            Phone: 03202280987<br>
            03132890988<br>
            {{ $order->is_paid == 1 ? 'PAID' : 'UNPAID' }}<br>
            <br>
        </div>
        <div style="margin: 5px 0;">
            <div style="display: flex; justify-content: space-between;">
                <div>ORDER ID: {{ $order->id }}</div>
                <div><strong>Order Type: {{ ucfirst($order->type) }}</strong></div>
            </div>
            Date: {{ now()->format('d/m/Y H:i') }}<br>
            <div style="display: flex; justify-content: space-between;">
                <div>User: {{ $order->user->first_name }} {{ $order->user->last_name }}</div>
                <div><strong>Table: {{ $order->table_number }}</strong></div>
            </div>

            <br>
           <div style="text-align: center;">
            -------------------------------------<br>
            Order Detail<br>
            -------------------------------------<br>
           </div>
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 40%;">Item</div>
                <div style="width: 10%;">Qty</div>
                <div style="width: 20%;text-align: right">Rate</div>
                <div style="width: 20%;text-align: right">Total</div>
            </div>
            <div class="text-center">
                -------------------------------------<br>
            </div>
            @php
            $total = 0;
            @endphp
            @foreach ($order->items as $item)
                @php
                    $itemTotal = $item->price * $item->qty;
                    $total += $itemTotal;
                @endphp
                <div style="display: flex;flex-direction: column">
                    <div style="display: flex;justify-content: space-between;">
                        <div style="width: 100%;">{{ $item->name . ' ' . $item->variant }}</div>
                    </div>
                    <div style="display: flex;justify-content: space-between;">
                        <div style="width: 40%;"></div>
                        <div style="width: 10%;">{{ $item->qty }}</div>
                        <div style="width: 20%;text-align: right;">{{ number_format($item->price, 2) }}</div>
                        <div style="width: 20%;text-align: right;">{{ number_format($itemTotal, 2) }}</div>
                    </div>
                </div>
                <br>
            @endforeach
            <div class="text-center">
                -------------------------------------<br>
            </div>
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 65%;">Sub Total</div>
                <div style="width: 35%;text-align: right;">{{ number_format($total, 2) }}</div>
            </div>
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 65%;">VAT/GST (5%)</div>
                <div style="width: 35%;text-align: right;">{{ '0.00' }}</div>
            </div>
            @if ($order->type == 'dinning')
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 65%;">Service Charge (5%)</div>
                <div style="width: 35%;text-align: right;">{{ number_format($order->service_charges, 2) }}</div>
            </div>
            @endif
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 65%;"><strong>GRAND TOTAL</strong></div>
                <div style="width: 35%;text-align: right;"><strong>{{ number_format($total + $order->service_charges, 2) }}</strong></div>
            </div>
        </div>
        <div style="text-align: center;">
            <br>
            @if (@$order->customer)
            Customer Detail<br>
            -------------------------------------<br>
            <div style="text-align: left">
                {{ @$order->customer->phone_number }}<br>
                Delivery Address: {{ @$order->customer->address }}<br>
            </div>
            @endif
            -------------------------------------<br>
            Printed: {{ now()->format('d/m/Y H:i:s') }}<br>
            FOR ANY COMPLAINT & SUGGESTIONS<br>
            PLEASE CONTACT US @ (063) 2280-988<br>
            Software By Bitzsol<br>
        </div>
    </div>

    {{-- <div class="receipt" id="receipt">
        <div class="receipt-header">
            <div><strong>DOWNTOWN</strong></div>
            <div><strong>BAHAWAL NAGAR</strong></div>
            <div>Tel: (063) 2280-988</div>
            <div>{{ $order->is_paid == 1 ? 'PAID' : 'UNPAID' }}</div>
        </div>

        <div class="d-flex flex-column mb-3">
            <div class="d-flex flex-row justify-content-between">
                <div>ORDER ID: {{$order->id}}</div>
                <div><strong>Order Type: {{$order->type}}</strong></div>
            </div>
            <div>Date: {{ now()->format('d/m/Y H:i') }}</div>
            <div>User: {{ $order->user->first_name . ' ' . $order->user->last_name }}</div>
        </div>

        <div class="divider"></div>
        @php
        $total = 0;
        @endphp
        <div class="receipt-items" id="receipt-items">
            @foreach ($order->items as $item)
            <div class="item-row" style="flex-direction: column;margin-bottom: 0.75rem;">
                <div style="display: block;width: 100%"><span>{!! $item->name . ' ' . $item->variant !!}</span></div>
                <div class="item-row">
                    <span>Qty x {{$item->qty}}</span>
                    @if (Auth::user()->role != 'kitchen')
                    <span>PKR{{number_format($item->price * $item->qty, 2)}}</span>
                    @endif
                </div>
            </div>
            @php
                $total += ($item->price * $item->qty);
            @endphp
            @endforeach
        </div>

        <div class="divider"></div>

        <div class="total">
            <span>TOTAL:</span>
            @if (Auth::user()->role != 'kitchen')
            <span id="receipt-total">PKR{{number_format($total, 2)}}</span>
            @endif
        </div>

        <div class="divider"></div>

        <div class="receipt-header">
            <div>Thank you for your purchase!</div>
            <div>Please come again</div>
        </div>
    </div> --}}

    <div>
        <button id="test-btn" class="btn btn-primary">Print Receipt</button>
    <button id="pay-btn" class="btn btn-success">Pay & Print Receipt</button>
    </div>

    <div id="status-message" class="status">
        Status message will appear here
    </div>
</div>
<script>
    const receiptBtn = document.getElementById('test-btn')
    const payBtn = document.getElementById('pay-btn')
        // Setup event listeners
        receiptBtn.addEventListener('click', printTestPage);
        payBtn.addEventListener('click', printTestPage2);


        async function printTestPage2 () {
            const c = confirm("Are you sure you want to print paid receipt?")
            if (c) {
                payBtn.innerHTML = '<span class="mr-1">Processing...</span><i class="ri-loader-4-line ri-spin"></i>'
            printPage('paid')
            }
        }

        async function printTestPage() {
            const c = confirm("Are you sure you want to print unpaid receipt?")
            if (c) {
                receiptBtn.innerHTML = '<span class="mr-1">Processing...</span><i class="ri-loader-4-line ri-spin"></i>'
            printPage('')
            }
        }

async function printPage(pay = '')
{
    //             fetch('/print/receipt', {
//     method: 'POST',
//     headers: {
//         'Content-Type': 'application/json',
//         'X-CSRF-TOKEN': '{{ csrf_token() }}'
//     },
//     body: JSON.stringify({
//         order_id: '12345',
//         table: '5',
//         customer: 'John Doe',
//         items: [
//             {name: 'Burger', quantity: 2, price: 12.99, notes: 'No onions'},
//             {name: 'Fries', quantity: 1, price: 4.99, notes: ''}
//         ],
//         subtotal: 30.97,
//         tax: 2.48,
//         payment_method: 'Cash'
//     })
// })
// .then(response => response.json())
// .then(data => console.log(data));

const printerName = '';//document.getElementById('printer-select').value;

/*if (!printerName) {
    showStatus('Please select a printer', true);
    return;
}*/

try {
    const response = await fetch('{{url('')}}/print/receipt?order_id={{$order->id}}&type='+pay, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            printer: printerName,
            type: pay
        })
    });

    const result = await response.json();

    if (result.success == 'true' || result.success == true || result.success) {
        showSuccessMessage('Receipt sent to printer successfully');
    } else {
        showErrorMessage('Unable to send receipt to printer');
    }
} catch (error) {
    console.error('Error sending test page:', error);
    showErrorMessage('Unable to send receipt to printer');
} finally {
    receiptBtn.innerHTML = 'Print Receipt'
    payBtn.innerHTML = 'Pay & Print Receipt'
}
}
</script>
@stop

