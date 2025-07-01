<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Toko;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // --- Fungsi Web biasa (view) ---
    public function index()
    {
        $users = User::with('toko')->get();
        $tokoCount = Toko::count();
        $userCount = User::count();

        return view('admin.dashboard', compact('users', 'tokoCount', 'userCount'));
    }

    public function users()
    {
        $users = User::orderBy('id', 'asc')->paginate(10);
        $currentTime = now();

        return view('admin.users', compact('users', 'currentTime'));
    }

    public function createUser()
    {
        return view('admin.user-create');
    }

   public function storeUser(Request $request)
   {
       $request->validate([
           'name'      => 'required|string|max:255',
           'email'     => 'required|email|unique:users,email',
           'password'  => 'required|string|min:6|confirmed',
           'role'      => 'required|string|max:50',
           'is_active' => 'required|boolean',
           'alamat'    => 'required|string|max:255',
           'lat'       => 'nullable|numeric',
           'long'      => 'nullable|numeric',
       ]);

       User::create([
           'name'      => $request->name,
           'email'     => $request->email,
           'password'  => Hash::make($request->password),
           'is_active' => $request->boolean('is_active'),
           'role'      => $request->role,
           'alamat'    => $request->alamat,
           'lat'       => $request->lat,
           'long'      => $request->long,
       ]);

       return redirect()->route('admin.users')->with('success', 'User berhasil ditambah');
   }

    public function showUser(User $user)
    {
        return view('admin.show-user', compact('user'));
    }

    public function editUser(User $user)
    {
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:6|confirmed',
            'role'      => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'alamat'    => 'required|string|max:255',
            'lat'       => 'nullable|numeric',
            'long'      => 'nullable|numeric',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->is_active = $request->boolean('is_active');
        $user->role = $request->role;
        $user->alamat = $request->alamat;
        $user->lat = $request->lat;
        $user->long = $request->long;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus');
    }

    public function stores()
    {
        $stores = Toko::all();
        return view('admin.stores', compact('stores'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    // --- Fungsi API ---
    // 1. Dashboard API
    public function apiDashboard()
    {
        $users = User::with('toko')->get();
        $tokoCount = Toko::count();
        $userCount = User::count();

        return response()->json([
            'users' => $users,
            'toko_count' => $tokoCount,
            'user_count' => $userCount,
        ]);
    }

    // 2. Daftar User API
    public function apiUsers()
    {
        $users = User::orderBy('id', 'asc')->paginate(10);
        return response()->json($users);
    }

    // 3. Tambah User API
    public function apiCreateUser(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'alamat'    => 'required|string|max:255',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'is_active' => $validated['is_active'],
            'role'      => $validated['role'],
            'alamat'    => $validated['alamat'],
        ]);

        return response()->json(['message' => 'User created', 'user' => $user], 201);
    }

    // 4. Detail User API
    public function apiShowUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // 5. Update User API
    public function apiUpdateUser($id, Request $request)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:6',
            'role'      => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'alamat'    => 'required|string|max:255',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        $user->role = $validated['role'];
        $user->is_active = $validated['is_active'];
        $user->alamat = $validated['alamat'];
        $user->save();

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    // 6. Hapus User API
    public function apiDeleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    // 7. List toko API
    public function apiStores()
    {
        $stores = Toko::all();
        return response()->json($stores);
    }

    // 8. Pengaturan API (contoh dummy)
    public function apiSettings()
    {
        return response()->json([
            'site_name' => 'My Store',
            'admin_email' => 'admin@example.com',
        ]);
    }
}
