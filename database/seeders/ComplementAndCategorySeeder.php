<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Complement;
use App\Models\CategoryComplement;
use Illuminate\Database\Seeder;

class ComplementAndCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        // 🔹 Definir categorías base
        $categories = [
            'Alimentos'           => 'Complementos de comida',
            'Salsas'              => 'Variedad de salsas y aderezos',
            'Bebidas'             => 'Opciones de bebidas',
            'Postres'             => 'Dulces y postres',
            'Opciones Saludables' => 'Alternativas saludables',
        ];

        // Insertar o actualizar categorías
        $categoryModels = [];
        foreach ($categories as $name => $description) {
            $categoryModels[$name] = CategoryComplement::updateOrCreate(
                ['name' => $name],
                ['description' => $description, 'updated_at' => $timestamp]
            );
        }

        // 🔹 Definir complements y categorías asociadas
        $complements = [
            [
                'name'        => 'Extra Queso',
                'description' => 'Porción adicional de queso derretido',
                'price'       => '1.50',
                'status'      => Status::ACTIVE,
                'category'    => 'Alimentos',
            ],
            [
                'name'        => 'Salsa BBQ',
                'description' => 'Salsa barbacoa casera',
                'price'       => '0.75',
                'status'      => Status::ACTIVE,
                'category'    => 'Salsas',
            ],
            [
                'name'        => 'Papas Extra',
                'description' => 'Porción extra de papas fritas',
                'price'       => '2.25',
                'status'      => Status::ACTIVE,
                'category'    => 'Alimentos',
            ],
            [
                'name'        => 'Guacamole',
                'description' => 'Guacamole fresco preparado con aguacate natural',
                'price'       => '1.75',
                'status'      => Status::ACTIVE,
                'category'    => 'Salsas',
            ],
            [
                'name'        => 'Bebida Grande',
                'description' => 'Upgrade a bebida de tamaño grande',
                'price'       => '0.90',
                'status'      => Status::ACTIVE,
                'category'    => 'Bebidas',
            ],
            [
                'name'        => 'Postre Brownie',
                'description' => 'Brownie de chocolate individual',
                'price'       => '2.50',
                'status'      => Status::ACTIVE,
                'category'    => 'Postres',
            ],
            [
                'name'        => 'Extra Carne',
                'description' => 'Porción adicional de carne a la elección del menú',
                'price'       => '3.00',
                'status'      => Status::ACTIVE,
                'category'    => 'Alimentos',
            ],
            [
                'name'        => 'Pan Sin Gluten',
                'description' => 'Sustitución de pan tradicional por opción sin gluten',
                'price'       => '1.20',
                'status'      => Status::ACTIVE,
                'category'    => 'Opciones Saludables',
            ],
        ];

        // 🔹 Insertar complements y relacionarlos con categorías
        foreach ($complements as $comp) {
            $complement = Complement::updateOrCreate(
                ['name' => $comp['name']],
                [
                    'description' => $comp['description'],
                    'price'       => $comp['price'],
                    'status'      => $comp['status'],
                    'updated_at'  => $timestamp,
                ]
            );

            // Relacionar con categoría
            if (isset($categoryModels[$comp['category']])) {
                $complement->categories()->syncWithoutDetaching([$categoryModels[$comp['category']]->id]);
            }
        }
    }
}
