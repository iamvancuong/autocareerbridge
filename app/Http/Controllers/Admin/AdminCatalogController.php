<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Major;
use Illuminate\Http\Request;

class AdminCatalogController extends Controller
{
    public function fields()
    {
        $fields = Field::withCount('majors')->get();
        $majors = Major::with('field')->get();
        return view('admin.catalog.index', compact('fields', 'majors'));
    }
    public function storeField(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Field::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Đã thêm Lĩnh vực!');
    }
    public function destroyField(Field $field) { $field->delete(); return redirect()->back()->with('success', 'Đã xóa Lĩnh vực.'); }
    public function storeMajor(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'field_id' => 'required|exists:fields,id']);
        Major::create(['name' => $request->name, 'field_id' => $request->field_id]);
        return redirect()->back()->with('success', 'Đã thêm Ngành học!');
    }
    public function destroyMajor(Major $major) { $major->delete(); return redirect()->back()->with('success', 'Đã xóa Ngành học.'); }
}