<?php

class OwnerBaseController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth('owner');
    }
}

class OwnerDashboardController extends OwnerBaseController {
    public function index() {
        $kpi = [
            'total_revenue' => (float) Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='delivered'"),
            'total_orders' => (int) Database::val("SELECT COUNT(*) FROM orders WHERE status='delivered'"),
            'avg_order_value' => (float) Database::val("SELECT COALESCE(AVG(total),0) FROM orders WHERE status='delivered'"),
            'customers' => (int) Database::val("SELECT COUNT(*) FROM users WHERE role='buyer'"),
            'this_month' => (float) Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='delivered' AND delivered_at >= DATE_FORMAT(NOW(),'%Y-%m-01')"),
            'last_month' => (float) Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='delivered' AND delivered_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH,'%Y-%m-01') AND delivered_at < DATE_FORMAT(NOW(),'%Y-%m-01')"),
        ];
        $topProducts = Database::all("SELECT p.id, p.name, p.thumbnail, SUM(oi.quantity) sold, SUM(oi.subtotal) revenue FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN products p ON p.id=oi.product_id WHERE o.status='delivered' GROUP BY p.id ORDER BY revenue DESC LIMIT 5");
        $topCategories = Database::all("SELECT c.name, SUM(oi.subtotal) revenue, SUM(oi.quantity) qty FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN products p ON p.id=oi.product_id JOIN categories c ON c.id=p.category_id WHERE o.status='delivered' GROUP BY c.id ORDER BY revenue DESC LIMIT 5");
        $monthlyChart = Database::all("SELECT DATE_FORMAT(delivered_at,'%Y-%m') ym, COALESCE(SUM(total),0) total FROM orders WHERE status='delivered' AND delivered_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY ym ORDER BY ym");
        $this->view('owner.dashboard', ['pageTitle' => 'Owner Dashboard', 'kpi' => $kpi, 'topProducts' => $topProducts, 'topCategories' => $topCategories, 'monthlyChart' => $monthlyChart], 'admin');
    }
}

class OwnerAnalyticsController extends OwnerBaseController {
    public function index() {
        $funnel = [
            'view' => (int) Database::val("SELECT COALESCE(SUM(view_count),0) FROM products"),
            'add_cart' => (int) Database::val("SELECT COUNT(*) FROM carts"),
            'orders' => (int) Database::val("SELECT COUNT(*) FROM orders"),
            'paid' => (int) Database::val("SELECT COUNT(*) FROM orders WHERE status NOT IN ('pending','cancelled','refunded')"),
        ];
        $productAnalytics = Database::all("SELECT p.id, p.name, p.view_count, p.sold_count, (SELECT COUNT(*) FROM reviews WHERE product_id=p.id AND is_approved=1) review_count FROM products p ORDER BY p.view_count DESC LIMIT 20");
        $this->view('owner.analytics', ['pageTitle' => 'Analitik Bisnis', 'funnel' => $funnel, 'productAnalytics' => $productAnalytics], 'admin');
    }
}

class OwnerReportsController extends OwnerBaseController {
    public function index() {
        $period = $_GET['period'] ?? '30';
        $days = (int)$period;
        $orders = Database::all("SELECT o.*, u.name AS buyer_name FROM orders o JOIN users u ON u.id=o.user_id WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY) ORDER BY o.created_at DESC");
        $newCustomers = (int) Database::val("SELECT COUNT(*) FROM users WHERE role='buyer' AND created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)");
        $this->view('owner.reports', ['pageTitle' => 'Laporan Penjualan', 'orders' => $orders, 'period' => $days, 'newCustomers' => $newCustomers], 'admin');
    }

    public function exportCsv() {
        $days = (int) ($_GET['period'] ?? 30);
        $rows = Database::all("SELECT o.order_number, o.created_at, u.name buyer, u.email, u.phone, o.ship_city, o.subtotal, o.shipping_cost, o.discount, o.total, o.status, p.payment_method
                               FROM orders o JOIN users u ON u.id=o.user_id LEFT JOIN payments p ON p.order_id=o.id
                               WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY) ORDER BY o.created_at DESC");
        $filename = 'laporan-penjualan-' . date('Ymd-His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        // BOM for Excel UTF-8 detection
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['No.Order','Tanggal','Buyer','Email','HP','Kota','Subtotal','Ongkir','Diskon','Total','Status','Metode Bayar']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['order_number'], $r['created_at'], $r['buyer'], $r['email'], $r['phone'], $r['ship_city'],
                $r['subtotal'], $r['shipping_cost'], $r['discount'], $r['total'],
                order_status_label($r['status']), payment_method_label($r['payment_method'] ?? '-'),
            ]);
        }
        fclose($out);
        exit;
    }

    public function exportPdf() {
        $days = (int) ($_GET['period'] ?? 30);
        $orders = Database::all("SELECT o.*, u.name buyer_name, u.phone buyer_phone FROM orders o JOIN users u ON u.id=o.user_id WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY) ORDER BY o.created_at DESC");
        $settings = $this->shared['settings'];
        $deliveredOrders = array_filter($orders, function($o){ return $o['status'] === 'delivered'; });
        $totals = [
            'orders' => count($orders),
            'revenue' => array_sum(array_column($deliveredOrders, 'total')),
            'shipping' => array_sum(array_column($orders, 'shipping_cost')),
            'discount' => array_sum(array_column($orders, 'discount')),
            'paid' => count(array_filter($orders, function($o){ return in_array($o['status'], ['processing','shipped','delivered']); })),
            'cancelled' => count(array_filter($orders, function($o){ return in_array($o['status'], ['cancelled','refunded']); })),
        ];
        // Render print-friendly HTML
        include APP_PATH . '/Views/owner/report_pdf.php';
        exit;
    }
}

class OwnerFinancialController extends OwnerBaseController {
    public function index() {
        $year = (int)($_GET['year'] ?? date('Y'));
        $monthly = Database::all("SELECT MONTH(delivered_at) m, COALESCE(SUM(total),0) revenue, COALESCE(SUM(shipping_cost),0) shipping, COALESCE(SUM(discount),0) discount, COUNT(*) orders FROM orders WHERE YEAR(delivered_at)=? AND status='delivered' GROUP BY MONTH(delivered_at) ORDER BY m", [$year]);
        $this->view('owner.financial', ['pageTitle' => 'Laporan Keuangan', 'year' => $year, 'monthly' => $monthly], 'admin');
    }

    public function exportCsv() {
        $year = (int)($_GET['year'] ?? date('Y'));
        $rows = Database::all("SELECT MONTH(delivered_at) m, COALESCE(SUM(total),0) revenue, COALESCE(SUM(shipping_cost),0) shipping, COALESCE(SUM(discount),0) discount, COUNT(*) orders FROM orders WHERE YEAR(delivered_at)=? AND status='delivered' GROUP BY MONTH(delivered_at) ORDER BY m", [$year]);
        $filename = "keuangan-{$year}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Bulan','Jumlah Pesanan','Revenue','Ongkir','Diskon','Net Revenue']);
        $totals = [0,0,0,0,0];
        foreach ($rows as $r) {
            $net = $r['revenue'] - $r['shipping'] - $r['discount'];
            fputcsv($out, [date('F', mktime(0,0,0,(int)$r['m'])), $r['orders'], $r['revenue'], $r['shipping'], $r['discount'], $net]);
            $totals[0] += $r['orders']; $totals[1] += $r['revenue']; $totals[2] += $r['shipping']; $totals[3] += $r['discount']; $totals[4] += $net;
        }
        fputcsv($out, ['TOTAL', $totals[0], $totals[1], $totals[2], $totals[3], $totals[4]]);
        fclose($out);
        exit;
    }
}
