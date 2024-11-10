<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role as Model; // Menggunakan alias Model untuk Role
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    private $viewIndex = 'master_data.role.index';  // Halaman index untuk menampilkan daftar roles
    private $viewCreate = 'master_data.role.form';  // Halaman form untuk membuat role baru
    private $viewEdit = 'master_data.role.form';    // Halaman form untuk mengedit role
    private $routePrefix = 'admin.master_data.role';             // Prefix untuk route admin.role

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Model::latest()->get();  // Ambil daftar roles terbaru

            return datatables()->of($roles)
                ->addIndexColumn()  // Menambahkan index ke tabel DataTables
                ->addColumn('action', function ($role) {
                    // Tombol aksi untuk edit dan delete role
                    return '
                        <a href="' . route($this->routePrefix . '.edit', $role->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <form action="' . route($this->routePrefix . '.destroy', $role->id) . '" method="POST" style="display:inline;" class="delete-form">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm delete-button">Hapus</button>
                        </form>
                    ';
                })
                ->make(true);
        }

        return view('admin.' . $this->viewIndex, [
            'routePrefix' => $this->routePrefix,
            'title' => 'Data Roles'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('admin.' . $this->viewCreate, [
            'model' => new Model(),
            'method' => 'POST',
            'route' => route($this->routePrefix . '.store'),
            'button' => 'SIMPAN',
            'title' => 'FORM DATA ROLE',
            'permissions' => $permissions
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();  // Mulai transaksi database
        try {
            // Simpan role baru ke database
            $role = Model::create([
                'name' => $request['name'],
                'guard_name' => 'web',  // Default guard untuk role adalah 'web'
            ]);

            // Ambil nama role dari request
            $roleName = $request['name'];

            // // Daftar permission default yang ingin di-attach ke role
            // $permissions = [
            //     $roleName . '.view',
            //     $roleName . '.create',
            //     $roleName . '.edit',
            //     $roleName . '.delete',
            //     $roleName . '.update',
            //     // Anda bisa menambah permission default lainnya di sini
            // ];

            // // Cek apakah permission sudah ada, jika tidak ada maka buat
            // foreach ($permissions as $permissionName) {
            //     // Cek apakah permission dengan nama tersebut sudah ada
            //     $permission = Permission::firstOrCreate(['name' => $permissionName]);

            //     // Attach permission ke role hanya jika belum ter-attach
            //     if (!$role->hasPermissionTo($permission)) {
            //         $role->givePermissionTo($permission);  // Attach permission ke role
            //     }
            // }

            // Ambil permissions yang dipilih oleh pengguna
            $selectedPermissions = $request->input('permissions', []);

            // Tambahkan permission yang dipilih ke role
            foreach ($selectedPermissions as $permissionId) {
                // Cek apakah permission dengan id tersebut ada
                $permission = Permission::findOrFail($permissionId);

                // Attach permission ke role hanya jika belum ter-attach
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }

            // Hapus permission yang tidak dipilih lagi (opsional)
            $role->revokePermissionTo($role->permissions->whereNotIn('id', $selectedPermissions)->pluck('id'));

            DB::commit();  // Commit transaksi
            session()->flash('success', 'Role Berhasil Ditambahkan');
            return redirect()->route('admin.master_data.role.index'); 
        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaksi jika terjadi kesalahan
            session()->flash('error', 'Role Gagal Ditambahkan');
            return back();  // Kembali ke halaman sebelumnya
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Model $role)
    {
        $permissions = Permission::all();
        $data = [
            'model' => $role,  // Ambil data role berdasarkan ID
            'method' => 'PUT',
            'route' => route($this->routePrefix . '.update', $role->id),
            'button' => 'UPDATE',
            'title' => 'FORM DATA EDIT ROLE',
            'permissions' => $permissions
        ];
        return view('admin.' . $this->viewEdit, $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $role)
    {
        DB::beginTransaction();  // Mulai transaksi database
        try {
            // Update data role
            $role->update([
                'name' => $request['name'],
            ]);

            // Ambil permissions yang dipilih oleh pengguna
            $selectedPermissions = $request->input('permissions', []);

            // Tambahkan permission yang dipilih ke role
            foreach ($selectedPermissions as $permissionId) {
                // Cek apakah permission dengan id tersebut ada
                $permission = Permission::findOrFail($permissionId);

                // Attach permission ke role hanya jika belum ter-attach
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }

            // Hapus permission yang tidak dipilih lagi (opsional)
            $role->revokePermissionTo($role->permissions->whereNotIn('id', $selectedPermissions)->pluck('id'));

            DB::commit();  // Commit transaksi
            session()->flash('success', 'Role Berhasil Diupdate');
            return redirect()->route('admin.master_data.role.index'); 
        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaksi jika terjadi kesalahan
            session()->flash('error', 'Role Gagal Diupdate');
            return back();  // Kembali ke halaman sebelumnya
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Model $role)
    {
        DB::beginTransaction();  // Mulai transaksi database
        try {
            $role->delete();  // Hapus role
            DB::commit();  // Commit transaksi
            session()->flash('success', 'Role Berhasil Dihapus');
            return redirect()->route('admin.master_data.role.index');  // Redirect ke halaman daftar roles
        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaksi jika terjadi kesalahan
            session()->flash('error', 'Role Gagal Dihapus');
            return redirect()->back();// Kembali ke halaman daftar roles
        }
    }
}
