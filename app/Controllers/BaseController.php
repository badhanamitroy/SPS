<?php

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
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

    protected function json(array $data, int $statusCode = 200): Response
    {
        $response = new Response();
        return $response->json($data, $statusCode);
    }

    /**
     * Validate CSRF token from POST parameters or HTTP headers.
     */
    protected function validateCsrf(Request $request): bool
    {
        $token = (string)($request->getPost('_csrf') 
            ?: $request->getPost('_token') 
            ?: ($request->getHeader('X-CSRF-TOKEN') ?? $request->getHeader('X-XSRF-TOKEN') ?? ''));

        return \App\Core\Session::validateCsrfToken($token);
    }

    /**
     * Standardized 403 Forbidden response.
     */
    protected function forbidden(Request $request, string $reason = ''): Response
    {
        $locale = I18n::getLocale();
        if ($request->isAjax() || str_contains((string)$request->getHeader('Accept', ''), 'application/json')) {
            return $this->json([
                'success' => false,
                'error' => '403 Forbidden: ' . ($reason ?: 'Unauthorized access.')
            ], 403);
        }

        $res = $this->render('errors/403', [
            'metaTitle' => '403 Forbidden | ' . config('app.short_name', 'SPS'),
            'permission' => $reason,
        ], 'admin');
        $res->setStatusCode(403);
        return $res;
    }
}

