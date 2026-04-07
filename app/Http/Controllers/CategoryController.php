<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Category; 
use App\Models\ActivityLog;
use Illuminate\Http\Request; 
 
class CategoryController extends Controller 
{ 
    public function index() 
    { 
        $categories = Category::withCount('tools')->latest()->paginate(10); 
        return view('admin.categories.index', compact('categories')); 
    } 
 
    public function create() 
    { 
        return view('admin.categories.create'); 
    } 

    public function store(Request $request) 
    { 
        $request->validate([ 
 
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori' 
        ]); 
 
        Category::create([ 
            'nama_kategori' => $request->nama_kategori 
        ]); 
 
        ActivityLog::record('Tambah Kategori', 'Menambah kategori: ' . $request->nama_kategori); 
 
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.'); 
    } 
 
    public function edit(Category $category) 
    { 
        return view('admin.categories.edit', compact('category')); 
    } 
  
    public function update(Request $request, Category $category) 
    { 
        $request->validate([ 
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $category->id 
        ]); 
 
        $oldName = $category->nama_kategori; 
        $category->update([ 
            'nama_kategori' => $request->nama_kategori 
        ]); 
 
        ActivityLog::record('Update Kategori', "Mengubah kategori $oldName menjadi " . $request->nama_kategori); 
 
        return redirect()->route('categories.index')->with('success', 'Kategori diperbarui.'); 
    } 
 
    public function destroy(Category $category) 
    { 

        if ($category->tools()->count() > 0) { 
            return back()->withErrors(['error' => 'Kategori tidak bisa dihapus karena masih memiliki data Alat. Hapus atau pindahkan alatnya terlebih dahulu.']); 
        } 
 
        $nama = $category->nama_kategori; 
        $category->delete(); 
 
        ActivityLog::record('Hapus Kategori', 'Menghapus kategori: ' . $nama); 
 
        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.'); 
    } 
}