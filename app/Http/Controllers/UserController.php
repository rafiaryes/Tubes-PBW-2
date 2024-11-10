<?php

namespace App\Http\Controllers;

use App\Models\User as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private $viewIndex = 'master_data.user.index';
    private $viewCreate = 'master_data.user.form';
    private $viewEdit = 'master_data.user.form';
    private $routePrefix = 'admin.master_data.user';

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = Model::latest()->with('roles')->get();

            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('roles', function ($user) {
                    return $user->roles->pluck('name')->join(', ');
                })
                ->addColumn('action', function ($user) {
                    return '
                        <a href="' . route($this->routePrefix . '.edit', $user->id) . '" class="btn btn-primary btn-sm">Edit</a>
                        <form action="' . route($this->routePrefix . '.destroy', $user->id) . '" method="POST" style="display:inline;" class="delete-form">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm delete-button">Hapus</button>
                        </form>
                        <form action="' . route($this->routePrefix . '.status', $user->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-sm ' . ($user->status ? 'btn-success' : 'btn-danger') . '">
                                ' . ($user->status ? 'Aktif' : 'Non-Aktif') . '
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.' . $this->viewIndex, [
            'routePrefix' => $this->routePrefix,
            'title' => 'Data User'
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $data = [
            'model' => new Model(),
            'roles' => Role::all(),
            'method' => 'POST',
            'route' => route($this->routePrefix . '.store'),
            'button' => 'SIMPAN',
            'title' => 'FORM DATA USER',
        ];
        return view('admin.' . $this->viewCreate, $data);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $user = Model::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => true,
            ]);

            $user->assignRole($request->role);

            DB::commit();
            session()->flash('success', 'User Berhasil Ditambahkan');
            return redirect()->route('admin.master_data.user.index');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'User Gagal Ditambahkan');
            return back();
        }
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(Model $user)
    {
        $data = [
            'model' => $user,
            'roles' => Role::all(),
            'method' => 'PUT',
            'route' => route($this->routePrefix . '.update', $user->id),
            'button' => 'UPDATE',
            'title' => 'FORM DATA EDIT USER',
        ];
        return view('admin.' . $this->viewEdit, $data);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, Model $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|confirmed|min:8',
            'role' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $userUpdate = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }

            $user->update($userUpdate);
            $user->syncRoles([$request->role]);

            DB::commit();
            session()->flash('success', 'User Berhasil Diupdate');
            return redirect()->route('admin.master_data.user.index');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'User Gagal Diupdate');
            return back();
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Model $user)
    {
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            session()->flash('success', 'User Berhasil Dihapus');
            return redirect()->route('admin.master_data.user.index');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'User Gagal Dihapus');
            return back();
        }
    }

    /**
     * Toggle the status of a user.
     */
    public function status(Model $user)
    {
        DB::beginTransaction();
        try {
            $user->status = !$user->status;
            $user->save();

            DB::commit();
            session()->flash('success', 'Status User Berhasil Diperbarui');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Gagal Memperbarui Status User');
            return back();
        }
    }
}
