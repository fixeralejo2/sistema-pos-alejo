<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:monospace;font-size:11px;width:80mm}h2{text-align:center;font-size:14px}table{width:100%}hr{border-top:1px dashed #000}.text-right{text-align:right}</style></head>
<body>
<h2>{{ config('app.name') }}</h2>
<hr>
<p>Venta #{{ $sale->id }} - {{ $sale->created_at->format('d/m/Y H:i') }}<br>
Cliente: {{ $sale->customer?->name ?? 'Consumidor final' }}<br>
Vendedor: {{ $sale->user->name }}</p>
<hr>
<table><thead><tr><th>Producto</th><th>Cant</th><th class="text-right">Precio</th></tr></thead><tbody>
@foreach($sale->items as $item)
<tr><td>{{ $item->product?->name }} {{ $item->variant?->name }}</td><td>{{ $item->quantity }}</td><td class="text-right">$ {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>
@endforeach
</tbody></table>
<hr>
<table><tr><td>Subtotal:</td><td class="text-right">$ {{ number_format($sale->subtotal, 0, ',', '.') }}</td></tr>
<tr><td>Descuento:</td><td class="text-right">$ {{ number_format($sale->discount, 0, ',', '.') }}</td></tr>
<tr><td><strong>TOTAL:</strong></td><td class="text-right"><strong>$ {{ number_format($sale->total, 0, ',', '.') }}</strong></td></tr>
<tr><td>Recibido:</td><td class="text-right">$ {{ number_format($sale->payment_received, 0, ',', '.') }}</td></tr>
<tr><td>Cambio:</td><td class="text-right">$ {{ number_format($sale->change_given, 0, ',', '.') }}</td></tr></table>
<hr><p style="text-align:center">Método: {{ $sale->payment_method }}<br>¡Gracias por su compra!</p>
</body></html>
