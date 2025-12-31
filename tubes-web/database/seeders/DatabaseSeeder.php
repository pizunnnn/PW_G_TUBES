<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Regular Users
        User::create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Create Categories with account fields
        $categories = [
            [
                'name' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'is_active' => true,
                'account_fields' => [
                    'title' => 'MASUKKAN USER ID DAN SERVER',
                    'fields' => [
                        [
                            'name' => 'game_user_id',
                            'label' => 'User ID',
                            'type' => 'text',
                            'placeholder' => 'Contoh: 123456789',
                            'required' => true,
                            'hint' => 'Masukkan User ID Mobile Legends kamu'
                        ],
                        [
                            'name' => 'game_server',
                            'label' => 'Server ID',
                            'type' => 'text',
                            'placeholder' => 'Contoh: 1234',
                            'required' => true,
                            'hint' => 'Masukkan Server ID kamu'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'is_active' => true,
                'account_fields' => [
                    'title' => 'MASUKKAN USER ID',
                    'fields' => [
                        [
                            'name' => 'game_user_id',
                            'label' => 'User ID',
                            'type' => 'text',
                            'placeholder' => 'Contoh: 1234567890',
                            'required' => true,
                            'hint' => 'Masukkan User ID Free Fire kamu'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'PUBG Mobile',
                'slug' => 'pubg-mobile',
                'is_active' => true,
                'account_fields' => [
                    'title' => 'MASUKKAN USER ID',
                    'fields' => [
                        [
                            'name' => 'game_user_id',
                            'label' => 'User ID',
                            'type' => 'text',
                            'placeholder' => 'Contoh: 5123456789',
                            'required' => true,
                            'hint' => 'Masukkan User ID PUBG Mobile kamu'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Genshin Impact',
                'slug' => 'genshin-impact',
                'is_active' => true,
                'account_fields' => [
                    'title' => 'MASUKKAN UID DAN SERVER',
                    'fields' => [
                        [
                            'name' => 'game_user_id',
                            'label' => 'UID',
                            'type' => 'text',
                            'placeholder' => 'Contoh: 800123456',
                            'required' => true,
                            'hint' => 'Masukkan UID Genshin Impact kamu'
                        ],
                        [
                            'name' => 'game_server',
                            'label' => 'Server',
                            'type' => 'select',
                            'required' => true,
                            'hint' => 'Pilih server kamu',
                            'options' => [
                                ['value' => 'America', 'label' => 'America'],
                                ['value' => 'Asia', 'label' => 'Asia'],
                                ['value' => 'Europe', 'label' => 'Europe'],
                                ['value' => 'TW_HK_MO', 'label' => 'TW, HK, MO']
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Valorant',
                'slug' => 'valorant',
                'is_active' => true,
                'account_fields' => [
                    'title' => 'MASUKKAN RIOT ID',
                    'fields' => [
                        [
                            'name' => 'game_user_id',
                            'label' => 'Riot ID',
                            'type' => 'text',
                            'placeholder' => 'Contoh: PlayerName',
                            'required' => true,
                            'hint' => 'Masukkan Riot ID kamu (tanpa #tagline)'
                        ],
                        [
                            'name' => 'game_server',
                            'label' => 'Tagline',
                            'type' => 'text',
                            'placeholder' => 'Contoh: #1234',
                            'required' => true,
                            'hint' => 'Masukkan tagline kamu (dengan #)'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Honor of Kings',
                'slug' => 'honor-of-kings',
                'is_active' => true,
                'account_fields' => [
                    'title' => 'MASUKKAN USER ID',
                    'fields' => [
                        [
                            'name' => 'game_user_id',
                            'label' => 'User ID',
                            'type' => 'text',
                            'placeholder' => 'Contoh: 1234567890',
                            'required' => true,
                            'hint' => 'Masukkan User ID Honor of Kings kamu'
                        ]
                    ]
                ]
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Products
        $products = [
            // Mobile Legends
            ['name' => '50 Diamonds', 'slug' => 'ml-50-diamonds', 'category_id' => 1, 'price' => 15000, 'stock' => 100, 'description' => 'Mobile Legends 50 Diamonds - Top up cepat dan aman', 'image' => 'https://uniplay.id/cdn/dd3ccb84374a3f9225f0515c31ac6910-large.png', 'is_active' => true],
            ['name' => '100 Diamonds', 'slug' => 'ml-100-diamonds', 'category_id' => 1, 'price' => 28000, 'stock' => 100, 'description' => 'Mobile Legends 100 Diamonds - Top up cepat dan aman', 'image' => 'https://uniplay.id/cdn/dd3ccb84374a3f9225f0515c31ac6910-large.png', 'is_active' => true],
            ['name' => '250 Diamonds', 'slug' => 'ml-250-diamonds', 'category_id' => 1, 'price' => 68000, 'stock' => 100, 'description' => 'Mobile Legends 250 Diamonds - Top up cepat dan aman', 'image' => 'https://uniplay.id/cdn/dd3ccb84374a3f9225f0515c31ac6910-large.png', 'is_active' => true],
            ['name' => '500 Diamonds', 'slug' => 'ml-500-diamonds', 'category_id' => 1, 'price' => 135000, 'stock' => 100, 'description' => 'Mobile Legends 500 Diamonds - Top up cepat dan aman', 'image' => 'https://uniplay.id/cdn/dd3ccb84374a3f9225f0515c31ac6910-large.png', 'is_active' => true],

            // Free Fire
            ['name' => '50 Diamonds', 'slug' => 'ff-50-diamonds', 'category_id' => 2, 'price' => 8000, 'stock' => 100, 'description' => 'Free Fire 50 Diamonds - Proses instan', 'image' => 'https://uniplay.id/cdn/27f1a02395e94ada15b932beb3efbd62-large.jpeg', 'is_active' => true],
            ['name' => '100 Diamonds', 'slug' => 'ff-100-diamonds', 'category_id' => 2, 'price' => 15000, 'stock' => 100, 'description' => 'Free Fire 100 Diamonds - Proses instan', 'image' => 'https://uniplay.id/cdn/27f1a02395e94ada15b932beb3efbd62-large.jpeg', 'is_active' => true],
            ['name' => '500 Diamonds', 'slug' => 'ff-500-diamonds', 'category_id' => 2, 'price' => 70000, 'stock' => 100, 'description' => 'Free Fire 500 Diamonds - Proses instan', 'image' => 'https://uniplay.id/cdn/27f1a02395e94ada15b932beb3efbd62-large.jpeg', 'is_active' => true],

            // PUBG Mobile
            ['name' => '60 UC', 'slug' => 'pubg-60-uc', 'category_id' => 3, 'price' => 15000, 'stock' => 100, 'description' => 'PUBG Mobile 60 UC - Langsung masuk akun', 'image' => 'https://uniplay.id/cdn/75ccb3ee8968746243a08a942dcb8b64-large.jpeg', 'is_active' => true],
            ['name' => '325 UC', 'slug' => 'pubg-325-uc', 'category_id' => 3, 'price' => 75000, 'stock' => 100, 'description' => 'PUBG Mobile 325 UC - Langsung masuk akun', 'image' => 'https://uniplay.id/cdn/75ccb3ee8968746243a08a942dcb8b64-large.jpeg', 'is_active' => true],

            // Genshin Impact
            ['name' => '60 Genesis Crystals', 'slug' => 'genshin-60-crystals', 'category_id' => 4, 'price' => 16000, 'stock' => 100, 'description' => 'Genshin Impact 60 Genesis Crystals - Legal & Safe', 'image' => 'https://uniplay.id/cdn/8e36e04e087baaba89db4cda2b154936-large.jpeg', 'is_active' => true],
            ['name' => '300 Genesis Crystals', 'slug' => 'genshin-300-crystals', 'category_id' => 4, 'price' => 79000, 'stock' => 100, 'description' => 'Genshin Impact 300 Genesis Crystals - Legal & Safe', 'image' => 'https://uniplay.id/cdn/8e36e04e087baaba89db4cda2b154936-large.jpeg', 'is_active' => true],

            // Valorant
            ['name' => '125 VP', 'slug' => 'valorant-125-vp', 'category_id' => 5, 'price' => 15000, 'stock' => 100, 'description' => 'Valorant 125 VP - Official Riot Points', 'image' => 'https://digivospaces.b-cdn.net/UniPlay/Products/ac7dbc6c7f97282313b7d1c9f18f8dee.jpeg', 'is_active' => true],
            ['name' => '420 VP', 'slug' => 'valorant-420-vp', 'category_id' => 5, 'price' => 50000, 'stock' => 100, 'description' => 'Valorant 420 VP - Official Riot Points', 'image' => 'https://digivospaces.b-cdn.net/UniPlay/Products/ac7dbc6c7f97282313b7d1c9f18f8dee.jpeg', 'is_active' => true],

            // Honor of Kings
            ['name' => '50 Tokens', 'slug' => 'hok-50-tokens', 'category_id' => 6, 'price' => 12000, 'stock' => 100, 'description' => 'Honor of Kings 50 Tokens - Fast delivery', 'image' => 'https://digivospaces.b-cdn.net/UniPlay/Products/0e0502c3cc466b50f70897275b107376.jpeg', 'is_active' => true],
            ['name' => '80 Tokens', 'slug' => 'hok-80-tokens', 'category_id' => 6, 'price' => 18000, 'stock' => 100, 'description' => 'Honor of Kings 80 Tokens - Fast delivery', 'image' => 'https://digivospaces.b-cdn.net/UniPlay/Products/0e0502c3cc466b50f70897275b107376.jpeg', 'is_active' => true],
            ['name' => '200 Tokens', 'slug' => 'hok-200-tokens', 'category_id' => 6, 'price' => 45000, 'stock' => 100, 'description' => 'Honor of Kings 200 Tokens - Fast delivery', 'image' => 'https://digivospaces.b-cdn.net/UniPlay/Products/0e0502c3cc466b50f70897275b107376.jpeg', 'is_active' => true],
            ['name' => '500 Tokens', 'slug' => 'hok-500-tokens', 'category_id' => 6, 'price' => 110000, 'stock' => 100, 'description' => 'Honor of Kings 500 Tokens - Fast delivery', 'image' => 'https://digivospaces.b-cdn.net/UniPlay/Products/0e0502c3cc466b50f70897275b107376.jpeg', 'is_active' => true],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('User: user@example.com / password');
    }
}
