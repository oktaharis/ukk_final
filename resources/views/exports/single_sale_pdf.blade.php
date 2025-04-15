<!-- resources/views/exports/single_sale_pdf.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .total {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>No: {{ $sale->id }}</p>
        <p>Tanggal: {{ $sale->created_at->format('d M Y') }}</p>
    </div>

    <div class="details">
        <p>Kasir: {{ $sale->user->name }}</p>
        <p>Pelanggan: {{ $sale->customer_name }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            {{-- {{ dd($sale->products) }} --}}
            @foreach($sale->products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ $product->pivot->quantity }}</td>
                <td>Rp {{ number_format($product->price * $product->pivot->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: Rp {{ number_format($sale->total_price, 0, ',', '.') }}
    </div>

</body>

</html>
