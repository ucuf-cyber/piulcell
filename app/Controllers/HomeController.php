<?php

class HomeController extends Controller {
    public function index() {
        $banners = Database::all("SELECT * FROM banners WHERE is_active=1 ORDER BY sort_order");
        $brands = Database::all("SELECT * FROM brands ORDER BY sort_order LIMIT 8");
        $featured = Database::all("SELECT p.* FROM products p WHERE p.is_active=1 AND p.is_featured=1 ORDER BY p.id DESC LIMIT 10");
        $newest = Database::all("SELECT p.* FROM products p WHERE p.is_active=1 AND p.is_new=1 ORDER BY p.id DESC LIMIT 10");
        $bestSeller = Database::all("SELECT p.* FROM products p WHERE p.is_active=1 ORDER BY p.sold_count DESC LIMIT 10");
        $categories = Database::all("SELECT * FROM categories WHERE is_active=1 ORDER BY sort_order");

        $this->view('home.index', [
            'pageTitle' => ($this->shared['settings']['store_name'] ?? 'Piul Cell') . ' — ' . ($this->shared['settings']['store_tagline'] ?? ''),
            'banners' => $banners,
            'brands' => $brands,
            'featured' => $featured,
            'newest' => $newest,
            'bestSeller' => $bestSeller,
            'categories' => $categories,
        ]);
    }
}
