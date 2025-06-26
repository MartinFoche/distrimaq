<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class ProductsImport implements ToModel , WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Product([
            'sku' => $row['sku'],
            'description'  => $row['descripcion'],
            'price' => $row['precio'],
        ]);
    }
    public function rules(): array
    {
        return [
            'sku' => 'required|unique:products,sku',
            'descripcion' => 'required',
            'precio' => 'required|numeric',
        ];
    }
}
