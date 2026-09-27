<?php

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Response;
use App\Core\View;

abstract class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'main'): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        // Inject default layout metadata
        $defaults = [
            'locale' => $locale,
            'isBn' => $isBn,
            'metaTitle' => config('app.name') . ' — ' . ($isBn ? config('app.motto_bn') : config('app.motto')),
            'metaDescription' => $isBn 
                ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS) একটি প্রামাণিক আধ্যাত্মিক, শিক্ষামূলক ও মানবিক সেবামঞ্চ।' 
                : 'Sanatan Philosophy and Scripture (SPS) is an authentic scholarly, educational, and humanitarian institution.',
            'activeNav' => '',
        ];

        $viewData = array_merge($defaults, $data);
        return View::render($view, $viewData, $layout);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        $response = new Response('', $statusCode, ['Location' => $url]);
        return $response;
    }
}
