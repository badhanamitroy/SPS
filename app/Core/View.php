<?php

namespace App\Core;

class View
{
    private static string $viewsPath;

    public static function init(string $path): void
    {
        self::$viewsPath = rtrim($path, '/\\');
    }

    /**
     * Render a page view wrapped within a layout
     */
    public static function render(string $view, array $data = [], string $layout = 'main'): Response
    {
        $content = self::renderViewOnly($view, $data);
        
        if ($layout === '') {
            return new Response($content);
        }

        $layoutData = array_merge($data, ['content' => $content]);
        $fullHtml = self::renderLayout($layout, $layoutData);

        return new Response($fullHtml);
    }

    /**
     * Render only the view file without layout
     */
    public static function renderViewOnly(string $view, array $data = []): string
    {
        $viewFile = self::$viewsPath . '/pages/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        return self::renderTemplate($viewFile, $data);
    }

    /**
     * Render a layout
     */
    public static function renderLayout(string $layout, array $data = []): string
    {
        $layoutFile = self::$viewsPath . '/layouts/' . $layout . '.php';

        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout file not found: {$layoutFile}");
        }

        return self::renderTemplate($layoutFile, $data);
    }

    /**
     * Render a reusable component
     */
    public static function component(string $name, array $props = []): string
    {
        $componentFile = self::$viewsPath . '/components/' . str_replace('.', '/', $name) . '.php';

        if (!file_exists($componentFile)) {
            return "<!-- Component not found: {$name} -->";
        }

        return self::renderTemplate($componentFile, $props);
    }

    private static function renderTemplate(string $filePath, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include $filePath;
        return ob_get_clean() ?: '';
    }
}
