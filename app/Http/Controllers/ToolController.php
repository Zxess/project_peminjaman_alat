<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Tool; 
use App\Models\Category; 
use App\Models\ActivityLog;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Storage; 
 
class ToolController extends Controller 
{ 

    public function index() 
    { 
        $tools = Tool::with('category')->latest()->paginate(10); 
         
        return view('admin.tools.index', compact('tools')); 
    } 
 
    public function create() 
    { 
        $categories = Category::all();
        return view('admin.tools.create', compact('categories')); 
    } 
 
    public function store(Request $request) 
    { 
        $request->validate([ 
            'nama_alat' => 'required|string|max:255', 
            'category_id' => 'required|exists:categories,id', 
            'stok' => 'required|integer|min:0', 
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:204800',
            'deskripsi' => 'nullable|string' 
        ]); 
 
        $gambarPath = null; 
        if ($request->hasFile('gambar')) {  
            $gambarPath = $request->file('gambar')->store('tools', 'public'); 
        } 

        Tool::create([ 
            'nama_alat' => $request->nama_alat, 
            'category_id' => $request->category_id, 
            'stok' => $request->stok, 
            'deskripsi' => $request->deskripsi, 
            'gambar' => $gambarPath 
        ]); 
 
        ActivityLog::record('Tambah Alat', 'Menambahkan alat baru: ' . $request->nama_alat); 
 
        return redirect()->route('tools.index')->with('success', 'Alat berhasil ditambahkan.'); 
    } 
 
    public function edit(Tool $tool) 
    { 
        $categories = Category::all(); 
        return view('admin.tools.edit', compact('tool', 'categories')); 
    } 
  
    public function update(Request $request, Tool $tool) 
    { 
        $request->validate([ 
            'nama_alat' => 'required|string|max:255', 
            'category_id' => 'required|exists:categories,id', 
            'stok' => 'required|integer|min:0', 
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]); 
 
        $data = $request->except(['gambar']); 
 
        if ($request->hasFile('gambar')) { 
            if ($tool->gambar && Storage::disk('public')->exists($tool->gambar)) { 
                Storage::disk('public')->delete($tool->gambar); 
            }  
            $data['gambar'] = $request->file('gambar')->store('tools', 'public'); 
        } 
 
        $tool->update($data); 
 
        ActivityLog::record('Update Alat', 'Memperbarui data alat: ' . $tool->nama_alat); 
 
        return redirect()->route('tools.index')->with('success', 'Data alat diperbarui.'); 
    } 
 
    public function destroy(Tool $tool) 
    { 
        if ($tool->gambar && Storage::disk('public')->exists($tool->gambar)) { 
            Storage::disk('public')->delete($tool->gambar); 
        } 
 
        $namaAlat = $tool->nama_alat; 
        $tool->delete(); 
 
        ActivityLog::record('Hapus Alat', 'Menghapus alat: ' . $namaAlat); 
 
        return redirect()->route('tools.index')->with('success', 'Alat berhasil dihapus.'); 
    } 
} 