<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Kurir;
use App\Models\Order;
use Illuminate\Http\Request;

class KurirController extends Controller
{
    // Dashboard utama kurir/admin: redirect sesuai role
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('kurir.index'); // daftar kurir
        }

        if ($user->hasRole('kurir')) {
            return redirect()->route('kurir.orderan-kurir'); // halaman order kurir
        }

        abort(403, 'Unauthorized');
    }

    // Daftar order yang bisa diambil kurir (hanya order belum diambil)
    public function orderanKurir()
    {
        $user = Auth::user();

        if (!$user->hasRole('kurir')) {
            abort(403, 'Unauthorized');
        }

        $orders = Order::with(['user', 'orderDetails.product', 'payment'])
            ->whereNull('kurir_id')
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('kurir.orderan-kurir', compact('orders'));
    }

    // Daftar kurir (admin)
    public function indexKurirs()
    {
        $kurirs = Kurir::paginate(10);
        return view('kurir.index', compact('kurirs'));
    }

    // Form tambah kurir (admin)
    public function create()
    {
        return view('kurir.create');
    }

    // Simpan data kurir (admin)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:kurirs,email',
            'phone' => 'required|string|max:20',
            'ktp_number' => 'required|string|unique:kurirs,ktp_number',
            'plate_number' => 'required|string|max:20',
            'address' => 'required|string',
            'vehicle_type' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'driver_license_number' => 'nullable|string|unique:kurirs,driver_license_number',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('kurir_photos', 'public');
        }

        Kurir::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ktp_number' => $validated['ktp_number'],
            'plate_number' => $validated['plate_number'],
            'address' => $validated['address'],
            'vehicle_type' => $validated['vehicle_type'],
            'photo_path' => $photoPath,
            'driver_license_number' => $validated['driver_license_number'] ?? null,
        ]);

        return redirect()->route('kurir.index')->with('success', 'Kurir berhasil ditambah.');
    }

    // Detail kurir
    public function show($id)
    {
        $kurir = Kurir::findOrFail($id);
        return view('kurir.show', compact('kurir'));
    }

    // Form edit kurir (admin)
    public function edit($id)
    {
        $kurir = Kurir::findOrFail($id);
        return view('kurir.edit', compact('kurir'));
    }

    // Update data kurir (admin)
    public function update(Request $request, $id)
    {
        $kurir = Kurir::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:kurirs,email,' . $kurir->id,
            'phone' => 'required|string|max:20',
            'ktp_number' => 'required|string|unique:kurirs,ktp_number,' . $kurir->id,
            'plate_number' => 'required|string|max:20',
            'address' => 'required|string',
            'vehicle_type' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'driver_license_number' => 'nullable|string|unique:kurirs,driver_license_number,' . $kurir->id,
        ]);

        $photoPath = $kurir->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('kurir_photos', 'public');
        }

        $kurir->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ktp_number' => $validated['ktp_number'],
            'plate_number' => $validated['plate_number'],
            'address' => $validated['address'],
            'vehicle_type' => $validated['vehicle_type'],
            'photo_path' => $photoPath,
            'driver_license_number' => $validated['driver_license_number'] ?? null,
        ]);

        return redirect()->route('kurir.index')->with('success', 'Data kurir berhasil diperbarui.');
    }

    // Hapus kurir (admin)
    public function destroy($id)
    {
        $kurir = Kurir::findOrFail($id);
        if ($kurir->photo_path && Storage::disk('public')->exists($kurir->photo_path)) {
            Storage::disk('public')->delete($kurir->photo_path);
        }
        $kurir->delete();

        return redirect()->route('kurir.index')->with('success', 'Kurir berhasil dihapus.');
    }

    // Fungsi ambil order oleh kurir (ajax atau form submit)
    public function takeOrder($orderId)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('kurir')) {
            return redirect()->back()->with('error', 'Anda bukan kurir yang valid.');
        }

        $order = Order::where('id', $orderId)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->whereNull('kurir_id')
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order sudah diambil atau tidak tersedia.');
        }

        // Ambil data kurir terkait user login
        $kurir = Kurir::where('email', $user->email)
                      ->orWhere('name', $user->name)
                      ->first();

        if (!$kurir) {
            return redirect()->back()->with('error', 'Data kurir tidak ditemukan.');
        }

        $order->kurir_id = $kurir->id;
        $order->status = 'taken'; // status diupdate jadi taken
        $order->taken_at = now();
        $order->save();

        return redirect()->back()->with('success', 'Order berhasil diambil.');
    }

    // ==== Fungsi Registrasi Kurir (buat User + Kurir sekaligus) ====

    public function registerKurirForm()
    {
        return view('kurir.register');
    }

    public function registerKurir(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20',
            'ktp_number' => 'required|string|unique:kurirs,ktp_number',
            'plate_number' => 'required|string|max:20',
            'address' => 'required|string',
            'vehicle_type' => 'required|string',
            'photo_base64' => 'required|string',
            'driver_license_number' => 'nullable|string|unique:kurirs,driver_license_number',
        ]);

        $photoPath = null;
        if ($request->photo_base64) {
            $image = $request->photo_base64;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $filename = 'kurir_' . time() . '.jpg';
            $path = 'kurir_photos/' . $filename;
            Storage::disk('public')->put($path, $imageData);

            $photoPath = $path;
        }

        // Buat user baru dengan role 'kurir'
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'kurir',
        ]);

        // Buat data kurir dengan relasi user_id
        Kurir::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ktp_number' => $validated['ktp_number'],
            'plate_number' => $validated['plate_number'],
            'address' => $validated['address'],
            'vehicle_type' => $validated['vehicle_type'],
            'photo_path' => $photoPath,
            'driver_license_number' => $validated['driver_license_number'] ?? null,
        ]);

        return redirect()->route('kurir.loginForm')->with('success', 'Registrasi berhasil, silakan login.');
    }

    // ==== Fungsi Login Kurir ====

    public function loginForm()
    {
        return view('kurir.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $request->session()->regenerate();

            if ($user->hasRole('admin')) {
                return redirect()->intended(route('kurir.index'));
            }

            if ($user->hasRole('kurir')) {
                return redirect()->intended(route('kurir.orderan-kurir'));
            }

            Auth::logout();
            return back()->withErrors(['email' => 'Anda tidak memiliki akses ke sistem kurir.']);
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('kurir.loginForm');
    }

    // --- Fungsi API ---

    public function apiIndex()
    {
        $kurirs = Kurir::all();
        return response()->json([
            'success' => true,
            'data' => $kurirs,
        ]);
    }

    public function apiShow($id)
    {
        $kurir = Kurir::find($id);
        if (!$kurir) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir tidak ditemukan',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $kurir,
        ]);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:kurirs,email',
            'phone' => 'required|string|max:20',
            'ktp_number' => 'required|string|unique:kurirs,ktp_number',
            'plate_number' => 'required|string|max:20',
            'address' => 'required|string',
            'vehicle_type' => 'required|string',
            'photo_base64' => 'nullable|string',
            'driver_license_number' => 'nullable|string|unique:kurirs,driver_license_number',
        ]);

        $photoPath = null;
        if ($request->has('photo_base64')) {
            try {
                $image = $request->input('photo_base64');
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);
                $filename = 'kurir_' . time() . '.jpg';
                $path = 'kurir_photos/' . $filename;
                Storage::disk('public')->put($path, $imageData);
                $photoPath = $path;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal upload foto',
                    'error' => $e->getMessage(),
                ], 400);
            }
        }

        $kurir = Kurir::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ktp_number' => $validated['ktp_number'],
            'plate_number' => $validated['plate_number'],
            'address' => $validated['address'],
            'vehicle_type' => $validated['vehicle_type'],
            'photo_path' => $photoPath,
            'driver_license_number' => $validated['driver_license_number'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kurir berhasil dibuat',
            'data' => $kurir,
        ], 201);
    }

    public function apiUpdate(Request $request, $id)
    {
        $kurir = Kurir::find($id);
        if (!$kurir) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:kurirs,email,' . $kurir->id,
            'phone' => 'required|string|max:20',
            'ktp_number' => 'required|string|unique:kurirs,ktp_number,' . $kurir->id,
            'plate_number' => 'required|string|max:20',
            'address' => 'required|string',
            'vehicle_type' => 'required|string',
            'photo_base64' => 'nullable|string',
            'driver_license_number' => 'nullable|string|unique:kurirs,driver_license_number,' . $kurir->id,
        ]);

        $photoPath = $kurir->photo_path;
        if ($request->has('photo_base64')) {
            try {
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                $image = $request->input('photo_base64');
                $image = str_replace('data:image/jpeg;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);
                $filename = 'kurir_' . time() . '.jpg';
                $path = 'kurir_photos/' . $filename;
                Storage::disk('public')->put($path, $imageData);
                $photoPath = $path;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal upload foto',
                    'error' => $e->getMessage(),
                ], 400);
            }
        }

        $kurir->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ktp_number' => $validated['ktp_number'],
            'plate_number' => $validated['plate_number'],
            'address' => $validated['address'],
            'vehicle_type' => $validated['vehicle_type'],
            'photo_path' => $photoPath,
            'driver_license_number' => $validated['driver_license_number'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data kurir berhasil diperbarui',
            'data' => $kurir,
        ]);
    }

    public function apiDelete($id)
    {
        $kurir = Kurir::find($id);
        if (!$kurir) {
            return response()->json([
                'success' => false,
                'message' => 'Kurir tidak ditemukan',
            ], 404);
        }

        if ($kurir->photo_path && Storage::disk('public')->exists($kurir->photo_path)) {
            Storage::disk('public')->delete($kurir->photo_path);
        }

        $kurir->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kurir berhasil dihapus',
        ]);
    }
}
