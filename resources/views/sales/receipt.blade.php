<!DOCTYPE html><html><head><meta charset="utf-8"><title>Recibo #{{ $sale->id }}</title>
<style>body{font-family:Arial,sans-serif;max-width:800px;margin:0 auto;padding:20px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:8px}.text-right{text-align:right}@media print{button{display:none}}</style></head>
<body>
<div style="text-align:center"><h2>{{ config('app.name') }}</h2><h4>Recibo de Venta #{{ $sale->id }}</h4></div>
<p>Fecha: {{ $sale->created_at->format('d/m/Y H:i') }} | Cliente: {{ $sale->customer?->name ?? 'Consumidor final' }} | Vendedor: {{ $sale->user->name }}</p>
<table><thead><tr><th>Producto</th><th>Variante</th><th>Cant</th><th class="text-right">Precio</th><th class="text-right">Desc</th><th class="text-right">Subtotal</th></tr></thead><tbody>
@foreach($sale->items as $item)
<tr><td>{{ $item->product?->name }}</td><td>{{ $item->variant?->name }}</td><td>{{ $item->quantity }}</td><td class="text-right">$ {{ number_format($item->unit_price, 0, ',', '.') }}</td><td class="text-right">$ {{ number_format($item->discount, 0, ',', '.') }}</td><td class="text-right">$ {{ number_format($item->subtotal, 0, ',', '.') }}</td></tr>
@endforeach
</tbody><tfoot>
<tr><td colspan="5" class="text-right"><strong>Total:</strong></td><td class="text-right"><strong>$ {{ number_format($sale->total, 0, ',', '.') }}</strong></td></tr>
<tr><td colspan="5" class="text-right">Método de pago:</td><td class="text-right">{{ $sale->payment_method }}</td></tr>
</tfoot></table>
<br><button onclick="window.print()">Imprimir</button> <button onclick="window.close()">Cerrar</button>
</body></html>
