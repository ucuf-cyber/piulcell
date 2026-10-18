<?php

class ShippingController extends Controller {
    public function calculate() {
        $lat = (float) $this->input('lat', 0);
        $lng = (float) $this->input('lng', 0);
        $subtotal = (float) $this->input('subtotal', 0);
        if (!$lat || !$lng) $this->json(['success' => false, 'message' => 'Koordinat tujuan diperlukan']);

        $sLat = (float) get_setting('store_latitude', -6.289888);
        $sLng = (float) get_setting('store_longitude', 106.763866);
        $distance = haversine_km($sLat, $sLng, $lat, $lng);
        error_log("[Shipping] calc request lat=" . $lat . " lng=" . $lng . " subtotal=" . $subtotal . " distance=" . $distance);

        // Pick zone
        $zones = Database::all("SELECT * FROM shipping_zones WHERE is_active=1
                                ORDER BY CASE WHEN max_distance=0 THEN 999999 ELSE max_distance END ASC");
        $matched = null;
        foreach ($zones as $z) {
            if ((int)$z['max_distance'] === 0 || $distance <= (float)$z['max_distance']) { $matched = $z; break; }
        }
        if (!$matched) {
            // Fallback: try to pick a zone explicitly marked as unlimited (max_distance=0)
            $fallback = Database::row("SELECT * FROM shipping_zones WHERE is_active=1 AND (max_distance=0 OR max_distance IS NULL) ORDER BY id LIMIT 1");
            if ($fallback) $matched = $fallback;
            else {
                // Or pick the zone with largest max_distance
                $largest = Database::row("SELECT * FROM shipping_zones WHERE is_active=1 ORDER BY COALESCE(max_distance,0) DESC LIMIT 1");
                if ($largest) $matched = $largest;
            }
        }
        if (!$matched) {
            error_log('[Shipping] no matched zone, fallback failed');
            $this->json(['success' => false, 'message' => 'Area pengiriman tidak tersedia']);
        }

        $base = isset($matched['base_price']) ? (float)$matched['base_price'] : 0;
        $perKm = isset($matched['price_per_km']) ? (float)$matched['price_per_km'] : 0;
        $raw = $base + ($distance * $perKm);
        error_log(sprintf('[Shipping] matched zone id=%s name=%s base=%s perKm=%s raw=%s', $matched['id'] ?? 'null', $matched['name'] ?? '', $base, $perKm, $raw));
        $cost = ceil($raw / 1000) * 1000;

        $freeMin = max(7500000, (int) get_setting('free_shipping_min', 7500000));
        $isFree = $freeMin > 0 && $subtotal >= $freeMin;

        $codMax = (int) get_setting('cod_max_distance', 15);
        $codAvailable = $distance <= $codMax;

        error_log('[Shipping] cost final=' . ($isFree ? 0 : (int)$cost));
        $this->json([
            'success' => true,
            'distance_km' => round($distance, 1),
            'zone_name' => $matched['name'],
            'courier' => $matched['courier'],
            'etd' => $matched['etd_days'] . ' hari kerja',
            'cost' => $isFree ? 0 : (int)$cost,
            'cost_display' => $isFree ? 'GRATIS' : rupiah($cost),
            'is_free' => $isFree,
            'cod_available' => $codAvailable,
        ]);
    }
}
