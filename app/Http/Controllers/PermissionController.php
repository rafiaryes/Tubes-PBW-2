<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as Model; // Menggunakan alias Model untuk Permission
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    private $viewIndex = 'master_data.permission.index';  // Halaman index untuk menampilkan daftar permissions
    private $viewCreate = 'master_data.permission.form';  // Halaman form untuk membuat permission baru
    private $viewEdit = 'master_data.permission.form';     // Halaman form untuk mengedit permission
    private $routePrefix = 'admin.master_data.permission';             // Prefix untuk route admin.permission

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $permissions = Model::latest()->get();  // Ambil daftar permissions terbaru

            return datatables()->of($permissions)
                ->addIndexColumn()  // Menambahkan index ke tabel DataTables
                ->addColumn('prefix', function ($permission) {                    
                    return explode('.', $permission->name)[0];  
                })
                ->addColumn('action', function ($permission) {
                    // Tombol aksi untuk edit dan delete permission
                    return '
                        <a href="' . route($this->routePrefix . '.edit', $permission->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <form action="' . route($this->routePrefix . '.destroy', $permission->id) . '" method="POST" style="display:inline;" class="delete-form">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm delete-button">Hapus</button>
                        </form>
                    ';
                })                
                ->rawColumns(columns: ['prefix', 'action']) 
                ->make(true);
        }

        return view('admin.' . $this->viewIndex, [
            'routePrefix' => $this->routePrefix,
            'title' => 'Data Permissions'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
    
        if ($roles->isEmpty()) {
            return redirect()->back()->with('error2', 'Tidak ada role yang tersedia, tidak dapat menambahkan permission.');
        }        

        return view('admin.' . $this->viewCreate, [
            'model' => new Model(),
            'method' => 'POST',
            'route' => route($this->routePrefix . '.store'),
            'button' => 'SIMPAN',
            'title' => 'FORM DATA PERMISSION',
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();  // Mulai transaksi database
        try {
            // Simpan permission baru ke database
            $permission = Model::create([
                'name' => $request['name'],
                'guard_name' => 'web',  // Default guard untuk permission adalah 'web'
            ]);

            // Attach roles ke permission
            if ($request->has('roles')) {
                $permission->roles()->sync($request->roles);
            }

            DB::commit();  // Commit transaksi
            session()->flash('success', 'Permission Berhasil Ditambahkan');
            return redirect()->route('admin.master_data.role.index'); 
        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaksi jika terjadi kesalahan
            session()->flash('error', 'Permission Gagal Ditambahkan');
            return back();  // Kembali ke halaman sebelumnya
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Model $permission)
    {
        $roles = Role::all();  // Ambil semua roles yang ada

        return view('admin.' . $this->viewEdit, [
            'model' => $permission,  // Ambil data permission berdasarkan ID
            'method' => 'PUT',
            'route' => route($this->routePrefix . '.update', $permission->id),
            'button' => 'UPDATE',
            'title' => 'FORM DATA EDIT PERMISSION',
            'roles' => $roles,  // Pass roles ke form
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $permission)
    {
        DB::beginTransaction();  // Mulai transaksi database
        try {
            // Update data permission
            $permission->update([
                'name' => $request['name'],
            ]);

            // Sync roles yang akan diberikan permission ini
            if ($request->has('roles')) {
                $permission->roles()->sync($request->roles);
            }

            DB::commit();  // Commit transaksi
            session()->flash('success', 'Permission Berhasil Diupdate');
            return redirect()->route('admin.master_data.role.index'); 
        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaksi jika terjadi kesalahan
            session()->flash('error', 'Permission Gagal Diupdate');
            return back();  // Kembali ke halaman sebelumnya
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Model $permission)
    {
        DB::beginTransaction();  // Mulai transaksi database
        try {
            $permission->delete();  // Hapus permission
            DB::commit();  // Commit transaksi
            session()->flash('success', 'Permission Berhasil Dihapus');
            return redirect()->route('admin.master_data.role.index'); 
        } catch (\Exception $e) {
            DB::rollback();  // Rollback transaksi jika terjadi kesalahan
            session()->flash('error', 'Permission Gagal Dihapus');
            return back();  // Kembali ke halaman sebelumnya
        }
    }
}
