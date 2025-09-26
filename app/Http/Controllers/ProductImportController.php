<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Product;
use App\Models\Category;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ProductImage;
use App\Imports\ProductsImport;
use Illuminate\Support\Facades\Log;

class ProductImportController extends Controller
{
    public function importUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt'
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $headerRow = null;
        foreach ($rows as $i => $row) {
            foreach ($row as $cell) {
                if ($cell && strtolower(trim($cell)) === 'id') {
                    $headerRow = $i;
                    break 2;
                }
            }
        }

        if (!$headerRow) {
            throw new \Exception("No se encontraron encabezados en el archivo");
        }

        $headers = array_map(function ($h) {
        $h = trim(mb_strtolower($h));             
        $h = preg_replace('/\s+/', '_', $h);      
        $h = iconv('UTF-8', 'ASCII//TRANSLIT', $h); 

        // Mapa de alias para casos raros
        $map = [
                "descripci_on" => "descripcion",
                "descripci'on" => "descripcion", 
                "descripcion"  => "descripcion",
                "precio"       => "precio",
                "sku"          => "sku",
                "id"           => "id",
            ];

            return $map[$h] ?? $h;
        }, $rows[$headerRow]);


        $rows = array_slice($rows, $headerRow);
        $currentCategory = null;
        $created = 0;
        $updated = 0;
        $omitted = 0;
        
        foreach ($rows as $row) {
            $rowData = [];
            foreach ($headers as $key => $header) {
                if (!$header) continue;
                $rowData[$header] = $row[$key] ?? null;
            }
            $id = $rowData['id'] ?? '';
            $partSku = $rowData['sku'] ?? '';
            $sku = trim($id . $partSku);
            $price = $this->parsePrice($rowData['precio'] ?? '');
            
            if (!$sku && !$price && !empty($rowData['descripcion'])) {
                $currentCategory = trim($rowData['descripcion']);
                continue; 
            }

            if (!$sku) continue;

            $product = Product::where('sku', $sku)->first();

            if ($product) {
                if ($product->price != $price) { 
                    $product->update([
                        'price' => $price,
                    ]);
                    $updated++;
                }
                else{
                    $omitted++;
                }
            } else {
                $product = Product::create([
                    'sku'         => $sku,
                    'description' => $rowData['descripcion'] ?? '',
                    'price'       => $price,
                ]);
                $category = Category::firstOrCreate(['name' => $currentCategory]);
                $product->categories()->syncWithoutDetaching([$category->id]);
                $created++;
            }
        }
        return response()->json([
            'message' => "Importación completada. Productos creados: $created, actualizados: $updated, omitidos: $omitted."
        ]);
    }

    private function parsePrice($value)
    {
        $value = trim((string)$value);

        if ($value === '' || stripos($value, 'sin stock') !== false) {
            return 0;
        }

        $value = str_replace(['$', ' '], '', $value);
        $value = str_replace(',', '', $value); 

        return (float) $value;
    }
}