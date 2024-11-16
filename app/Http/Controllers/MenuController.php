<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Models\Menu as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{

    private $viewIndex = 'master_data.menu.index';
    private $viewCreate = 'master_data.menu.form';
    private $viewEdit = 'master_data.menu.form';    
    private $routePrefix = 'admin.master_data.menu';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $menus = Model::latest()->get();
            return DataTables::of($menus)
            ->addIndexColumn()        
            ->addColumn('foto_menu', function ($row) {
                return '<img src="' . asset("storage/$row->image") . '" width="100" class="mt-2">';
            })
            ->addColumn('action', function ($row) {
                // Directly return the action buttons
                return '
                    <a href="' . route($this->routePrefix . '.edit', $row->id) . '" class="btn btn-primary btn-sm">Edit</a>
                    <form action="' . route($this->routePrefix . '.destroy', $row->id) . '" method="POST" style="display:inline;" class="delete-form">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm delete-button">Hapus</button>
                    </form>
                    <form action="' . route($this->routePrefix . '.status', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        <button type="submit" class="btn btn-sm ' . ($row->status ? 'btn-success' : 'btn-danger') . '">
                            ' . ($row->status ? 'Aktif' : 'Non-Aktif') . '
                        </button>
                    </form>
                ';
            })
            ->rawColumns(['foto_menu', 'action']) // Render HTML correctly for the image and buttons
            ->make(true);
        }

        return view('admin.' . $this->viewIndex, [            
            'routePrefix' => $this->routePrefix,
            'title' => 'Data Menu'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'model' => new Model(),
            'method' => 'POST',
            'route' => route($this->routePrefix . '.store'),
            'button' => 'SIMPAN',
            'title' => 'FORM DATA MENU',
        ];
        return view('admin.' . $this->viewCreate, $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuRequest $request)
    {
        DB::beginTransaction();
        try {
            Model::create([
                'nama' => $request['nama'],
                'deskripsi' => $request['deskripsi'],
                'price' => $request['price'],
                'stok' => $request['stok'],
                'status' => $request['stok'] > 0, // default aktif jika tidak disetel
                'image' => $request->file('image')->store('menus'),
            ]);

            DB::commit();
            session()->flash('success', 'Menu Berhasil Ditambahkan');
            return redirect()->route("admin.master_data.menu.index");
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Menu Gagal Ditambahkan');
            return back();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {

       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Model $menu)
    {
        $menu = Model::findOrFail($menu->id);

        $data = [
            'model' => $menu,
            'method' => 'PUT',
            'route' => route($this->routePrefix . '.update', $menu->id),
            'button' => 'UPDATE',
            'title' => 'FORM DATA EDIT MENU',
        ];
        return view('admin.' . $this->viewEdit, $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuRequest $request, Model $menu)
    {
        DB::beginTransaction();
        try {
            $menuUpdate = [
                'nama' => $request['nama'],
                'deskripsi' => $request['deskripsi'],
                'price' => $request['price'],
                'stok' => $request['stok'],
                'status' => $request['stok'] > 0,                
            ];

            $model = Model::findOrFail($menu->id);

            if ($request->hasFile('image')) {
                Storage::delete($model->image);
                $menuUpdate['image'] = $request->file('image')->store('menus');
            }

            $model->update($menuUpdate);
            DB::commit();
            session()->flash('success', 'Menu Berhasil Diupdate');
            return redirect()->route("admin.master_data.menu.index");            
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Menu Gagal Diupdate');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Model $menu)
    {
        DB::beginTransaction();
        try {
            Model::findOrFail($menu->id)->delete();
            Storage::delete($menu->image);
            DB::commit();
            session()->flash('success', 'Berhasil Menghapus Menu');
            return redirect()->route("admin.master_data.menu.index");
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error2', 'Gagal Menghapus Menu');
            return redirect()->back();
        }
    }


    public function status(Model $menu)
    {
        DB::beginTransaction();
        try {            
            // Toggle the status (active to inactive, or vice versa)
            $menu->status = !$menu->status;
            $menu->save();

            DB::commit();
            session()->flash('success', 'Status Berhasil Diperbarui');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Gagal Memperbarui Status');
            return back();
        }
    }

}
