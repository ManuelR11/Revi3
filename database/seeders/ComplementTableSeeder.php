<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Complement;
use Illuminate\Database\Seeder;

class ComplementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        $complements = [
            [
                'name'        => 'Extra Queso',
                'description' => 'Porción adicional de queso derretido',
                'category'    => 'Alimentos',
                'price'       => '1.50',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
            [
                'name'        => 'Salsa BBQ',
                'description' => 'Salsa barbacoa casera',
                'category'    => 'Salsas',
                'price'       => '0.75',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
            [
                'name'        => 'Papas Extra',
                'description' => 'Porción extra de papas fritas',
                'category'    => 'Alimentos',
                'price'       => '2.25',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
            [
                'name'        => 'Guacamole',
                'description' => 'Guacamole fresco preparado con aguacate natural',
                'category'    => 'Salsas',
                'price'       => '1.75',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
            [
                'name'        => 'Bebida Grande',
                'description' => 'Upgrade a bebida de tamaño grande',
                'category'    => 'Bebidas',
                'price'       => '0.90',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
            [
                'name'        => 'Postre Brownie',
                'description' => 'Brownie de chocolate individual',
                'category'    => 'Postres',
                'price'       => '2.50',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
            [
                'name'        => 'Extra Carne',
                'description' => 'Porción adicional de carne a la elección del menú',
                'category'    => 'Alimentos',
                'price'       => '3.00',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
            [
                'name'        => 'Pan Sin Gluten',
                'description' => 'Sustitución de pan tradicional por opción sin gluten',
                'category'    => 'Opciones Saludables',
                'price'       => '1.20',
                'status'      => Status::ACTIVE,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ],
        ];

        Complement::query()->upsert(
            $complements,
            ['name'],
            ['description', 'category', 'price', 'status', 'updated_at']
        );
    }
}