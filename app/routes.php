<?php
// ============================================
// ROUTES
// ============================================

$router = new Router();

// --- Public ---
$router->get('/', 'HomeController@index');
$router->get('/invoice/{id}', 'InvoiceController@show');
$router->get('/katalog', 'ProductController@catalog');
$router->get('/produk/{slug}', 'ProductController@detail');
$router->get('/cari', 'ProductController@search');
$router->any('/page/{slug}', 'PageController@show');

// --- Auth ---
$router->any('/login', 'AuthController@login');
$router->any('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->any('/forgot-password', 'AuthController@forgot');
$router->any('/reset-password', 'AuthController@reset');

// --- Cart (AJAX) ---
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->get('/cart', 'CartController@index');
$router->get('/cart/mini', 'CartController@mini');
$router->get('/cart/count', 'CartController@count');

// --- Checkout / Payment ---
$router->any('/checkout', 'CheckoutController@index');
$router->post('/checkout/process', 'CheckoutController@process');
$router->post('/voucher/validate', 'CheckoutController@validateVoucher');
$router->post('/shipping/calculate', 'ShippingController@calculate');
$router->get('/payment/instructions/{id}', 'PaymentController@instructions');
$router->post('/payment/upload-proof/{id}', 'PaymentController@uploadProof');
$router->get('/payment/status/{id}', 'PaymentController@status');

// --- Account ---
$router->get('/akun', 'AccountController@profile');
$router->post('/akun/update', 'AccountController@update');
$router->any('/akun/ganti-password', 'AccountController@changePassword');
$router->get('/akun/alamat', 'AccountController@addresses');
$router->any('/akun/alamat/tambah', 'AccountController@addAddress');
$router->post('/akun/alamat/hapus/{id}', 'AccountController@deleteAddress');
$router->post('/akun/alamat/primary/{id}', 'AccountController@setPrimaryAddress');
$router->get('/akun/pesanan', 'AccountController@orders');
$router->get('/akun/pesanan/{id}', 'AccountController@orderDetail');
$router->post('/akun/pesanan/batal/{id}', 'AccountController@cancelOrder');
$router->post('/akun/pesanan/terima/{id}', 'AccountController@confirmReceived');
$router->get('/akun/wishlist', 'AccountController@wishlist');
$router->post('/wishlist/toggle', 'AccountController@toggleWishlist');
$router->any('/akun/ulasan/{order_item_id}', 'AccountController@writeReview');
$router->get('/akun/notifikasi', 'AccountController@notifications');
$router->post('/notifikasi/baca/{id}', 'AccountController@readNotif');

// --- Admin ---
$router->get('/admin', 'AdminDashboardController@index');
$router->get('/admin/dashboard', 'AdminDashboardController@index');
$router->get('/admin/products', 'AdminProductsController@index');
$router->any('/admin/products/create', 'AdminProductsController@create');
$router->any('/admin/products/edit/{id}', 'AdminProductsController@edit');
$router->post('/admin/products/delete/{id}', 'AdminProductsController@delete');
$router->any('/admin/products/variants/{id}', 'AdminProductsController@variants');
$router->post('/admin/products/variant/save', 'AdminProductsController@saveVariant');
$router->post('/admin/products/variant/delete/{id}', 'AdminProductsController@deleteVariant');
$router->get('/admin/brands', 'AdminBrandsController@index');
$router->any('/admin/brands/create', 'AdminBrandsController@create');
$router->any('/admin/brands/edit/{id}', 'AdminBrandsController@edit');
$router->post('/admin/brands/delete/{id}', 'AdminBrandsController@delete');
$router->get('/admin/categories', 'AdminCategoriesController@index');
$router->any('/admin/categories/create', 'AdminCategoriesController@create');
$router->any('/admin/categories/edit/{id}', 'AdminCategoriesController@edit');
$router->post('/admin/categories/delete/{id}', 'AdminCategoriesController@delete');
$router->get('/admin/orders', 'AdminOrdersController@index');
$router->get('/admin/orders/{id}', 'AdminOrdersController@detail');
$router->post('/admin/orders/update-status/{id}', 'AdminOrdersController@updateStatus');
$router->post('/admin/orders/tracking/{id}', 'AdminOrdersController@tracking');
$router->get('/admin/payments/pending', 'AdminPaymentsController@pending');
$router->get('/admin/payments', 'AdminPaymentsController@all');
$router->post('/admin/payments/verify/{id}', 'AdminPaymentsController@verify');
$router->post('/admin/payments/reject/{id}', 'AdminPaymentsController@reject');
$router->post('/admin/payments/cod-done/{id}', 'AdminPaymentsController@codDone');
$router->any('/admin/payment-accounts', 'AdminPaymentsController@accounts');
$router->post('/admin/payment-accounts/save', 'AdminPaymentsController@saveAccount');
$router->post('/admin/payment-accounts/delete/{id}', 'AdminPaymentsController@deleteAccount');
$router->get('/admin/customers', 'AdminCustomersController@index');
$router->get('/admin/customers/{id}', 'AdminCustomersController@detail');
$router->post('/admin/customers/toggle/{id}', 'AdminCustomersController@toggle');
$router->get('/admin/vouchers', 'AdminVouchersController@index');
$router->any('/admin/vouchers/create', 'AdminVouchersController@create');
$router->any('/admin/vouchers/edit/{id}', 'AdminVouchersController@edit');
$router->post('/admin/vouchers/delete/{id}', 'AdminVouchersController@delete');
$router->get('/admin/banners', 'AdminBannersController@index');
$router->any('/admin/banners/create', 'AdminBannersController@create');
$router->any('/admin/banners/edit/{id}', 'AdminBannersController@edit');
$router->post('/admin/banners/delete/{id}', 'AdminBannersController@delete');
$router->get('/admin/reviews', 'AdminReviewsController@index');
$router->post('/admin/reviews/approve/{id}', 'AdminReviewsController@approve');
$router->post('/admin/reviews/reject/{id}', 'AdminReviewsController@reject');
$router->post('/admin/reviews/reply/{id}', 'AdminReviewsController@reply');
$router->get('/admin/shipping-zones', 'AdminShippingController@index');
$router->any('/admin/shipping-zones/create', 'AdminShippingController@create');
$router->any('/admin/shipping-zones/edit/{id}', 'AdminShippingController@edit');
$router->post('/admin/shipping-zones/delete/{id}', 'AdminShippingController@delete');
$router->any('/admin/settings', 'AdminSettingsController@index');
$router->get('/admin/users', 'AdminUsersController@index');
$router->any('/admin/users/create', 'AdminUsersController@create');
$router->any('/admin/users/edit/{id}', 'AdminUsersController@edit');
$router->post('/admin/users/delete/{id}', 'AdminUsersController@delete');

// --- Owner ---
$router->get('/owner', 'OwnerDashboardController@index');
$router->get('/owner/dashboard', 'OwnerDashboardController@index');
$router->get('/owner/analytics', 'OwnerAnalyticsController@index');
$router->get('/owner/financial', 'OwnerFinancialController@index');
$router->get('/owner/financial/export-csv', 'OwnerFinancialController@exportCsv');
$router->get('/owner/reports', 'OwnerReportsController@index');
$router->get('/owner/reports/export-csv', 'OwnerReportsController@exportCsv');
$router->get('/owner/reports/export-pdf', 'OwnerReportsController@exportPdf');
