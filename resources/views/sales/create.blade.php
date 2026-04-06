@extends('layouts.app')
@section('header', 'Nueva Venta')
@section('main_content')
@if(!$openRegister)
<div class="alert alert-danger">Debes abrir caja antes de realizar ventas. <a href="{{ route('cash-registers.create') }}">Abrir caja</a></div>
@else
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <input type="text" id="product-search" class="form-control" placeholder="Buscar producto por nombre o código...">
                <div id="search-results" class="list-group mt-1" style="position:absolute;z-index:1000;width:90%"></div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm" id="cart-table">
                    <thead><tr><th>Producto</th><th>Variante</th><th>Cant</th><th>Precio</th><th>Desc</th><th>Subtotal</th><th></th></tr></thead>
                    <tbody id="cart-body"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h5>Resumen</h5></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Cliente</label>
                    <select name="customer_id" id="customer_id" class="form-control">
                        <option value="">Consumidor final</option>
                        @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <table class="table table-sm">
                    <tr><td>Subtotal:</td><td class="text-right" id="disp-subtotal">$ 0</td></tr>
                    <tr><td>Descuentos:</td><td class="text-right" id="disp-discount">$ 0</td></tr>
                    <tr><td><strong>Total:</strong></td><td class="text-right"><strong id="disp-total">$ 0</strong></td></tr>
                </table>
                <div class="form-group">
                    <label>Método de Pago</label>
                    <select id="payment_method" class="form-control">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta_debito">Tarjeta Débito</option>
                        <option value="tarjeta_credito">Tarjeta Crédito</option>
                        <option value="nequi">Nequi</option>
                        <option value="daviplata">Daviplata</option>
                        <option value="mixto">Mixto</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Recibido</label>
                    <input type="number" id="payment_received" class="form-control" value="0" oninput="calcChange()">
                </div>
                <div class="form-group">
                    <label>Cambio</label>
                    <input type="number" id="change_given" class="form-control" readonly value="0">
                </div>
                <div class="form-group">
                    <label>Notas</label>
                    <textarea id="notes" class="form-control" rows="2"></textarea>
                </div>
                <button class="btn btn-success btn-block" onclick="submitSale()"><i class="fas fa-check"></i> Registrar Venta</button>
            </div>
        </div>
    </div>
</div>
<form id="sale-form" method="POST" action="{{ route('sales.store') }}">
    @csrf
    <div id="form-inputs"></div>
</form>
@endif
@endsection
@section('extra_js')
<script>
let cart = [];

document.getElementById('product-search').addEventListener('input', function() {
    const q = this.value;
    if (q.length < 2) { document.getElementById('search-results').innerHTML = ''; return; }
    fetch(`/products-search?q=${encodeURIComponent(q)}`)
        .then(r => r.json()).then(products => {
            const div = document.getElementById('search-results');
            div.innerHTML = '';
            products.forEach(p => {
                p.variants.forEach(v => {
                    const a = document.createElement('a');
                    a.href = '#'; a.className = 'list-group-item list-group-item-action';
                    a.textContent = `${p.name} - ${v.name} (Stock: ${v.stock}) $ ${v.final_price}`;
                    a.onclick = (e) => { e.preventDefault(); addToCart(p, v); div.innerHTML = ''; document.getElementById('product-search').value = ''; };
                    div.appendChild(a);
                });
            });
        });
});

function addToCart(product, variant) {
    const existing = cart.find(i => i.variant_id == variant.id);
    if (existing) { existing.quantity++; }
    else { cart.push({product_id: product.id, variant_id: variant.id, name: product.name, variant_name: variant.name, quantity: 1, price: variant.final_price, discount: 0}); }
    renderCart();
}

function renderCart() {
    const tbody = document.getElementById('cart-body');
    tbody.innerHTML = '';
    let subtotal = 0, totalDiscount = 0;
    cart.forEach((item, i) => {
        const itemSubtotal = (item.price - item.discount) * item.quantity;
        subtotal += item.price * item.quantity;
        totalDiscount += item.discount * item.quantity;
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${item.name}</td><td>${item.variant_name}</td>
            <td><input type="number" class="form-control form-control-sm" style="width:60px" value="${item.quantity}" min="1" onchange="updateQty(${i}, this.value)"></td>
            <td>$ ${item.price.toLocaleString()}</td>
            <td><input type="number" class="form-control form-control-sm" style="width:70px" value="${item.discount}" min="0" onchange="updateDiscount(${i}, this.value)"></td>
            <td>$ ${itemSubtotal.toLocaleString()}</td>
            <td><button type="button" class="btn btn-xs btn-danger" onclick="removeItem(${i})">X</button></td>`;
        tbody.appendChild(tr);
    });
    const total = subtotal - totalDiscount;
    document.getElementById('disp-subtotal').textContent = '$ ' + subtotal.toLocaleString();
    document.getElementById('disp-discount').textContent = '$ ' + totalDiscount.toLocaleString();
    document.getElementById('disp-total').textContent = '$ ' + total.toLocaleString();
    calcChange();
}

function updateQty(i, val) { cart[i].quantity = parseInt(val) || 1; renderCart(); }
function updateDiscount(i, val) { cart[i].discount = parseFloat(val) || 0; renderCart(); }
function removeItem(i) { cart.splice(i, 1); renderCart(); }

function calcChange() {
    const total = parseFloat(document.getElementById('disp-total').textContent.replace(/[^0-9.]/g,'')) || 0;
    const received = parseFloat(document.getElementById('payment_received').value) || 0;
    document.getElementById('change_given').value = Math.max(0, received - total).toFixed(0);
}

function submitSale() {
    if (cart.length === 0) { alert('Agrega productos al carrito'); return; }
    const form = document.getElementById('sale-form');
    const inputs = document.getElementById('form-inputs');
    inputs.innerHTML = '';
    cart.forEach((item, i) => {
        inputs.innerHTML += `<input type="hidden" name="items[${i}][product_id]" value="${item.product_id}">
            <input type="hidden" name="items[${i}][variant_id]" value="${item.variant_id}">
            <input type="hidden" name="items[${i}][quantity]" value="${item.quantity}">
            <input type="hidden" name="items[${i}][price]" value="${item.price}">
            <input type="hidden" name="items[${i}][discount]" value="${item.discount}">`;
    });
    inputs.innerHTML += `<input type="hidden" name="customer_id" value="${document.getElementById('customer_id').value}">
        <input type="hidden" name="payment_method" value="${document.getElementById('payment_method').value}">
        <input type="hidden" name="payment_received" value="${document.getElementById('payment_received').value}">
        <input type="hidden" name="notes" value="${document.getElementById('notes').value}">`;
    form.submit();
}
</script>
@endsection
