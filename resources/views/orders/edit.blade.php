@extends('layouts.admin')

@section('title', 'Edit Order #' . $order->id)

@section('content')
    <h1 class="text-3xl font-bold mb-6">Edit Order #{{ $order->id }}</h1>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div id="order-items">
            @foreach ($order->orderDetails as $i => $detail)
                <div class="mb-4 border p-4 rounded" data-index="{{ $i }}">
                    <label class="block font-semibold mb-1">Produk</label>
                    <select name="items[{{ $i }}][product_id]" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $detail->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->nama }} (Rp {{ number_format($product->harga,0,',','.') }})
                            </option>
                        @endforeach
                    </select>

                    <label class="block font-semibold mt-3 mb-1">Jumlah</label>
                    <input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ $detail->quantity }}" class="w-full border border-gray-300 rounded px-3 py-2" required>

                    <button type="button" onclick="removeItem(this)" class="mt-2 text-red-600 hover:text-red-900">Hapus Item</button>
                </div>
            @endforeach
        </div>

        <button type="button" onclick="addItem()" class="mb-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">+ Tambah Item</button>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">Update Order</button>
    </form>

    <script>
        let index = {{ $order->orderDetails->count() }};
        function addItem() {
            const container = document.getElementById('order-items');
            const div = document.createElement('div');
            div.classList.add('mb-4', 'border', 'p-4', 'rounded');
            div.setAttribute('data-index', index);
            div.innerHTML = `
                <label class="block font-semibold mb-1">Produk</label>
                <select name="items[\${index}][product_id]" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->nama }} (Rp {{ number_format($product->harga,0,',','.') }})</option>
                    @endforeach
                </select>

                <label class="block font-semibold mt-3 mb-1">Jumlah</label>
                <input type="number" name="items[\${index}][quantity]" min="1" value="1" class="w-full border border-gray-300 rounded px-3 py-2" required>

                <button type="button" onclick="removeItem(this)" class="mt-2 text-red-600 hover:text-red-900">Hapus Item</button>
            `;
            container.appendChild(div);
            index++;
        }

        function removeItem(button) {
            const div = button.parentElement;
            div.remove();
        }
    </script>
@endsection
