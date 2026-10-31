<?php

return [
    'email' => env('RYO_EMAIL'),
    'location' => env('RYO_LOCATION'),
    'social' => array_filter([
        'Instagram' => env('RYO_INSTAGRAM'),
        'LinkedIn' => env('RYO_LINKEDIN'),
        'Behance' => env('RYO_BEHANCE'),
    ]),
    'services' => [
        'website-development' => ['title' => 'Pengembangan Website', 'description' => 'Website yang dirancang untuk menjelaskan bisnismu, membantu pelanggan, dan mudah dikembangkan.', 'icon' => 'website'],
        'ui-ux-design' => ['title' => 'Desain UI/UX', 'description' => 'Alur dan tampilan digital yang jelas, mudah dipahami, dan nyaman digunakan.', 'icon' => 'design'],
        'landing-pages' => ['title' => 'Landing Page', 'description' => 'Halaman terarah untuk memperkenalkan layanan, kampanye, atau penawaran bisnismu.', 'icon' => 'landing'],
        'e-commerce' => ['title' => 'Pengembangan E-Commerce', 'description' => 'Toko online yang memudahkan pelanggan menjelajahi produk dan menyelesaikan pembelian.', 'icon' => 'commerce'],
        'web-applications' => ['title' => 'Aplikasi Web Khusus', 'description' => 'Aplikasi yang disesuaikan dengan proses kerja, kebutuhan tim, dan layanan bisnismu.', 'icon' => 'application'],
        'seo-maintenance' => ['title' => 'SEO, Performa & Perawatan', 'description' => 'Fondasi teknis, struktur konten, dan pemeliharaan untuk menjaga website tetap sehat.', 'icon' => 'performance'],
        'company-profile' => ['title' => 'Website Company Profile', 'description' => 'Ruang digital untuk memperkenalkan cerita, layanan, dan keahlian perusahaanmu.', 'icon' => 'website'],
        'education-training' => ['title' => 'Website Pendidikan & Pelatihan', 'description' => 'Website untuk menjelaskan program belajar dan membantu peserta menemukan informasi.', 'icon' => 'education'],
        'ai-automation' => ['title' => 'Otomasi AI', 'description' => 'Alur otomatis untuk membantu mengurangi pekerjaan berulang sesuai kebutuhan tim.', 'icon' => 'automation'],
    ],
    'pricing' => [
        'landing-page' => ['name' => 'Landing Page', 'icon' => 'landing', 'fit' => 'Untuk satu layanan, kampanye, atau penawaran dengan tujuan yang spesifik.', 'examples' => ['Halaman promosi', 'Kampanye', 'Peluncuran program']],
        'business-website' => ['name' => 'Website Bisnis', 'icon' => 'website', 'fit' => 'Untuk bisnis, perusahaan, lembaga, atau personal brand yang membutuhkan rumah digital.', 'examples' => ['Company profile', 'Website organisasi', 'Website pendidikan']],
        'custom-solution' => ['name' => 'Solusi Digital Khusus', 'icon' => 'application', 'fit' => 'Untuk kebutuhan yang melibatkan fitur, alur kerja, atau integrasi yang lebih spesifik.', 'examples' => ['E-commerce', 'Aplikasi web', 'Otomasi AI']],
    ],
    'projects' => [
        'trainingkota' => ['title' => 'TrainingKota', 'category' => 'Portal K3 & Layanan Profesional', 'description' => 'Portal untuk mengenal layanan profesional dan program keselamatan kerja.', 'url' => 'https://traningkota.my.id', 'embed_allowed' => true, 'art' => 1],
        'najkaltare' => ['title' => 'Najkaltare', 'category' => 'Katalog Layanan & Pelatihan', 'description' => 'Katalog layanan dan pelatihan industri yang membantu pengunjung menjelajahi kebutuhan kompetensi.', 'url' => 'https://najkaltare.my.id', 'embed_allowed' => false, 'art' => 2],
        'chifheto' => ['title' => 'Chifheto', 'category' => 'Pelatihan Teknis & Industri', 'description' => 'Katalog pelatihan teknis dan industri dengan program yang dikelompokkan berdasarkan topik.', 'url' => 'https://chifheto.my.id', 'embed_allowed' => false, 'art' => 3],
    ],
];
