<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller {

    public function store(Request $request) {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:150',
            'kode_supplier' => 'required|string|max:20|unique:suppliers',
            'kontak_person' => 'nullable|string|max:100',
            'telepon'       => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:100',
            'alamat'        => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'supplier' => [
                    'id'            => $supplier->id,
                    'nama_supplier' => $supplier->nama_supplier,
                    'kode_supplier' => $supplier->kode_supplier,
                ],
            ]);
        }

        return back()->with('success', 'Supplier berhasil ditambahkan.');
    }
}