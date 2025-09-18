<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertDefaultCategories extends Migration
{
    public function up()
    {
        DB::table('categories')->insert([
            ['name' => 'Caramelos'],
            ['name' => 'Chupetines'],
            ['name' => 'Chocolates'],
            ['name' => 'Gomitas'],
            ['name' => 'Galletitas'],
            ['name' => 'Malvaviscos'],
            ['name' => 'Alfajores'],
            ['name' => 'Barritas'],
            ['name' => 'Pastillas'],
            ['name' => 'Turrones'],
            ['name' => 'Medicamentos'],
        ]);
    }

    public function down()
    {
        DB::table('categories')
            ->whereIn('name', [
                'Caramelos', 'Chupetines', 'Chocolates', 'Gomitas',
                'Galletitas', 'Malvaviscos', 'Alfajores', 'Barritas',
                'Pastillas', 'Turrones', 'Medicamentos'
            ])
            ->delete();
    }
}