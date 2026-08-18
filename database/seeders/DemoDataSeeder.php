<?php

namespace Database\Seeders;

use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Sale::query()->exists()) {
            return;
        }

        $user = User::query()->where('email', 'testuser@test.com')->first()
            ?? User::factory()->create(['email' => 'testuser@test.com']);

        $categories = collect([
            ['name' => 'Bebidas', 'description' => 'Refrescos, aguas y jugos'],
            ['name' => 'Snacks', 'description' => 'Botanas y dulces'],
            ['name' => 'Lácteos', 'description' => 'Leche, queso y yogur'],
            ['name' => 'Panadería', 'description' => 'Pan y repostería'],
            ['name' => 'Limpieza', 'description' => 'Productos de limpieza'],
            ['name' => 'Cuidado Personal', 'description' => 'Higiene y cuidado'],
        ])->map(fn (array $data) => Category::create($data));

        $productNames = [
            'Agua Pura 1L', 'Refresco Cola 500ml', 'Jugo de Naranja 1L', 'Café Molido 250g',
            'Papas Fritas 150g', 'Galletas de Chocolate', 'Chocolate con Leche', 'Cacahuates Salados',
            'Leche Entera 1L', 'Yogur Natural 150g', 'Queso Fresco 400g', 'Mantequilla 200g',
            'Pan Francés', 'Pan Dulce', 'Repostería Variada',
            'Detergente 1kg', 'Jabón para Trastes', 'Cloro 1L',
            'Shampoo 500ml', 'Jabón de Baño', 'Pasta Dental', 'Toallas Sanitarias',
        ];

        $products = collect($productNames)->map(function (string $name, int $index) use ($categories) {
            $purchasePrice = fake()->randomFloat(2, 0.5, 4);
            $category = $categories[$index % $categories->count()];

            return Product::create([
                'category_id' => $category->id,
                'name' => $name,
                'purchase_price' => $purchasePrice,
                'sale_price' => round($purchasePrice * fake()->randomFloat(2, 1.3, 1.8), 2),
                'current_stock' => fake()->randomElement([0, 2, 3, 4, 5, 10, 15, 20, 30, 40, 50]),
                'min_stock' => 5,
                'has_tax' => true,
                'tax_percentage' => 13,
                'is_active' => true,
            ]);
        });

        $customers = collect([
            ['first_name' => 'María', 'last_name' => 'García', 'tax_id' => '0101-010101-101-1'],
            ['first_name' => 'José', 'last_name' => 'Martínez', 'tax_id' => '0202-020202-202-2'],
            ['first_name' => 'Ana', 'last_name' => 'López', 'tax_id' => null],
            ['first_name' => 'Carlos', 'last_name' => 'Hernández', 'tax_id' => null],
            ['first_name' => 'Luisa', 'last_name' => 'Pérez', 'tax_id' => null],
        ])->map(fn (array $data) => Customer::create($data));

        for ($daysAgo = 30; $daysAgo >= 0; $daysAgo--) {
            $day = now()->subDays($daysAgo)->startOfDay();
            $isToday = $daysAgo === 0;

            $register = CashRegister::create([
                'user_id' => $user->id,
                'shift' => 'MORNING',
                'opening_amount' => 50,
                'status' => $isToday ? 'OPEN' : 'CLOSED',
                'opening_date' => $day->copy()->setHour(7)->setMinute(0),
                'closing_date' => $isToday ? null : $day->copy()->setHour(19)->setMinute(0),
            ]);

            $salesCount = fake()->numberBetween(0, 8);

            for ($i = 0; $i < $salesCount; $i++) {
                $details = collect()->times(fake()->numberBetween(1, 3), function () use ($products) {
                    $product = $products->random();
                    $quantity = fake()->numberBetween(1, 5);

                    return [
                        'product' => $product,
                        'quantity' => $quantity,
                        'unit_price' => $product->sale_price,
                        'unit_cost' => $product->purchase_price,
                        'discount' => 0,
                    ];
                });

                $total = round($details->sum(
                    fn (array $detail) => $detail['quantity'] * $detail['unit_price'] - $detail['discount']
                ), 2);

                $sale = Sale::create([
                    'cash_register_id' => $register->id,
                    'user_id' => $user->id,
                    'customer_id' => fake()->boolean(25) ? $customers->random()->id : null,
                    'ticket_number' => 'TKT-'.$day->format('ymd').'-'.Str::upper(Str::random(5)),
                    'total' => $total,
                    'amount_received' => $total,
                    'change_due' => 0,
                    'payment_method' => fake()->randomElement(['CASH', 'CARD', 'TRANSFER']),
                    'status' => fake()->boolean(8) ? 'CANCELLED' : 'COMPLETED',
                    'created_at' => $day->copy()->addMinutes(fake()->numberBetween(480, 720)),
                ]);

                foreach ($details as $detail) {
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $detail['product']->id,
                        'quantity' => $detail['quantity'],
                        'unit_price' => $detail['unit_price'],
                        'unit_cost' => $detail['unit_cost'],
                        'discount' => $detail['discount'],
                        'subtotal' => round(
                            $detail['quantity'] * $detail['unit_price'] - $detail['discount'],
                            2
                        ),
                    ]);
                }
            }
        }
    }
}
