<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Imports\ProductsImport; // Esta clase la creás para definir cómo importar

class ProductImportController extends Controller
{
    public function import(Request $request)
    {
        $import = new ProductsImport;
        $import->import($request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            $errores = [];

            foreach ($import->failures() as $failure) {
                $errores[] = 'Fila ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'errors' => $errores,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Importación completada con éxito.',
        ]);
    }

public function importUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        $path = $request->file('file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Asumimos que la primera fila son los encabezados
        $headers = array_map('trim', $data[0]);
        unset($data[0]); // Quitamos los headers

        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            $row = array_combine($headers, $row);
            $sku = trim($row['sku'] ?? '');

            if (empty($sku)) {
                $skipped++;
                $errors[] = "Error en fila " . ($index + 2) . ": No se encontro SKU";
                continue;
            }

            $product = Product::where('sku', $sku)->first();

            if ($product) {
                try {
                    $product->update([
                        'description' => $row['descripcion'] ?? $product->description,
                        'price' => $row['precio'] ?? $product->price,
                    ]);
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Error en fila " . ($index + 2) . ": " . $e->getMessage();
                }
            } else {
                $skipped++;
            }
        }

        return response()->json([
            'message' => "No se encontraron $skipped productos",
            'errors' => $errors,
        ]);
    }
}
