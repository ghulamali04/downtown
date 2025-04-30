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


    <div class="form-group">
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
    </div>

    <div class="receipt" id="receipt">
        <div class="receipt-header">
            <div><strong>DOWNTOWN</strong></div>
            <div>Jail Road, Mall of Bahawalnagar</div>
            <div>Tel: (063) 2280-988</div>
            <div id="receipt-date">Date: {{ now()->format('F j, Y g:i A') }}</div>
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
                    <span>PKR{{number_format($item->price * $item->qty, 2)}}</span>
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
            <span id="receipt-total">PKR{{number_format($total, 2)}}</span>
        </div>

        <div class="divider"></div>

        <div class="receipt-header">
            <div>Thank you for your purchase!</div>
            <div>Please come again</div>
        </div>
    </div>

    <button id="test-btn" class="btn btn-primary">Print Receipt</button>

    <div id="status-message" class="status">
        Status message will appear here
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Setup event listeners
        document.getElementById('test-btn').addEventListener('click', printTestPage);


        async function printTestPage() {
            const printerName = document.getElementById('printer-select').value;

            if (!printerName) {
                showStatus('Please select a printer', true);
                return;
            }

            try {
                const response = await fetch('{{url('')}}/order/receipt/print/{{$order->id}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        printer: printerName
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showStatus('Test page sent to printer successfully');
                } else {
                    showStatus(`Printing failed: ${result.message}`, true);
                }
            } catch (error) {
                console.error('Error sending test page:', error);
                showStatus('Error sending test page to server', true);
            }
        }
    });
</script>
@stop

@section('scripts')

@stop
