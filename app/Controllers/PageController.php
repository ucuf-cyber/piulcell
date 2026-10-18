<?php

class PageController extends Controller {
    public function show(string $slug) {
        $page = Database::row("SELECT * FROM static_pages WHERE slug=? AND is_active=1", [$slug]);
        if (!$page) { http_response_code(404); $this->view('static.404', ['pageTitle' => '404']); return; }
        $this->view('static.page', ['pageTitle' => $page['title'], 'page' => $page]);
    }
}
