@extends('layouts.user')

@section('title', 'Order Saya')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold mb-8 text-[#0A2E6E] tracking-wide">Order Saya</h1>

    @if($orders->count())
        @php
            // Kelompokkan order berdasarkan tanggal (Y-m-d)
            $ordersGrouped = $orders->groupBy(function($order) {
                return $order->created_at->format('Y-m-d');
            });

            // Prioritas status untuk menentukan gabungan
            $statusOrder = [
                'pending' => 1,
                'dikemas' => 2,
                'shipped' => 3,
                'completed' => 4,
                'dibatalkan' => 0,
            ];

            // Definisi timeline status dengan label, deskripsi, ikon, dan warna
            $timeline = [
                'pending' => ['label' => 'Pending', 'desc' => 'Menunggu pembayaran atau verifikasi', 'icon' => '⏳', 'color' => 'text-yellow-400'],
                'dikemas' => ['label' => 'Dikemas', 'desc' => 'Disiapkan untuk dikirim sesuai jadwal', 'icon' => '📦', 'color' => 'text-indigo-500'],
                'shipped' => ['label' => 'Dikirim', 'desc' => 'Dalam perjalanan ke alamat tujuan', 'icon' => '🚚', 'color' => 'text-purple-600'],
                'completed' => ['label' => 'Selesai', 'desc' => 'Pesanan telah sampai dan selesai', 'icon' => '🎉', 'color' => 'text-green-500'],
                'dibatalkan' => ['label' => 'Dibatalkan', 'desc' => 'Pesanan dibatalkan', 'icon' => '❌', 'color' => 'text-red-500'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($ordersGrouped as $date => $ordersOnDate)
                @php
                    $totalQuantity = $ordersOnDate->sum(fn($o) => $o->orderDetails->sum('quantity'));
                    $totalPrice = $ordersOnDate->sum('total_price');

                    // Tentukan status gabungan berdasarkan prioritas tertinggi
                    $maxStatusLevel = -1;
                    $statusGabungan = 'pending';
                    foreach ($ordersOnDate as $order) {
                        $level = $statusOrder[strtolower($order->status)] ?? 1;
                        if ($level > $maxStatusLevel) {
                            $maxStatusLevel = $level;
                            $statusGabungan = strtolower($order->status);
                        }
                    }
                    $statusInfo = $timeline[$statusGabungan];
                @endphp

                <section class="bg-white rounded-lg shadow p-5 flex flex-col min-w-0">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b border-gray-300 pb-1">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}
                    </h2>

                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3">
                        <div class="text-sm font-semibold text-gray-700 tracking-wide mb-2 sm:mb-0">
                            <span>Order Gabungan</span>
                            <span class="text-gray-500 text-xs">({{ $ordersOnDate->count() }} pesanan)</span>
                        </div>
                        <a href="{{ route('orders.user.show', $ordersOnDate->first()->id) }}"
                            class="bg-[#0A2E6E] hover:bg-[#072147] text-white text-sm px-5 py-2 rounded-md font-semibold w-full sm:w-auto text-center transition-shadow shadow-sm hover:shadow-md">
                            Lihat Detail
                        </a>
                    </div>

                    {{-- Timeline --}}
                    <ul class="flex space-x-3 overflow-x-auto no-scrollbar border-b border-gray-300 pb-3 mb-3">
                        @foreach ($timeline as $key => $step)
                            @php
                                if ($key === $statusGabungan) {
                                    $statusType = 'active';
                                } elseif ($statusOrder[$key] < $statusOrder[$statusGabungan]) {
                                    $statusType = 'done';
                                } else {
                                    $statusType = 'pending';
                                }
                            @endphp
                            <li class="flex flex-col items-center min-w-[55px] text-center flex-shrink-0">
                                <div
                                    class="w-8 h-8 flex items-center justify-center rounded-full ring-4 ring-white
                                    {{ $statusType == 'done' ? 'bg-green-500 text-white' : ($statusType == 'active' ? $step['color'] . ' bg-white ring-2' : 'bg-gray-300 text-gray-400') }}">
                                    <span class="text-lg select-none">{{ $step['icon'] }}</span>
                                </div>
                                <h3 class="mt-1 font-semibold text-[11px] leading-tight
                                    {{ $statusType == 'done' ? 'text-green-700' : ($statusType == 'active' ? 'text-indigo-700' : 'text-gray-400') }}">
                                    {{ $step['label'] }}
                                </h3>
                                <p class="text-[9px] leading-snug
                                    {{ $statusType == 'done' ? 'text-green-600' : ($statusType == 'active' ? 'text-indigo-600' : 'text-gray-400') }}">
                                    {{ $step['desc'] }}
                                </p>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center text-gray-700 text-sm space-y-2 sm:space-y-0">
                        <div><strong>Total item:</strong> {{ $totalQuantity }}</div>
                        <div class="font-semibold text-blue-700 whitespace-nowrap">
                            Rp {{ number_format($totalPrice, 0, ',', '.') }}
                        </div>
                    </div>
                </section>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @else
        <p class="text-gray-600 text-center mt-20 text-base">Kamu belum memiliki order.</p>
    @endif
</div>

<style>
    /* Hilangkan scrollbar timeline */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Pastikan flex item di grid tidak overflow */
    section {
        min-width: 0;
        word-break: break-word;
        overflow-wrap: break-word;
    }
</style>
@endsection
