<?php
/**
 * SmartyService — Bonus Smarty Integration
 *
 * This service bridges Laravel with Smarty template engine.
 * Usage: SmartyService is injected in the controllers wherever its needed for Smarty-rendered output.
 *
 */

namespace App\Services;

use Smarty\Smarty;

class SmartyService
{
    protected Smarty $smarty;

    public function __construct()
    {
        $this->smarty = new Smarty();

        $this->smarty->setTemplateDir(resource_path('smarty/templates'));
        $this->smarty->setCompileDir(storage_path('framework/smarty/compile'));
        $this->smarty->setCacheDir(storage_path('framework/smarty/cache'));
        $this->smarty->setConfigDir(resource_path('smarty/configs'));

        // Expose Laravel config helpers to Smarty
        $this->smarty->assign('appName',  config('app.name'));
        $this->smarty->assign('appUrl',   config('app.url'));
        $this->smarty->assign('authUser', auth()->user());
    }

    /**
     * Assign a variable to all Smarty templates.
     */
    public function assign(string $key, mixed $value): static
    {
        $this->smarty->assign($key, $value);
        return $this;
    }

    /**
     * Render a Smarty template and return the HTML string.
     */
    public function render(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        return $this->smarty->fetch($template . '.tpl');
    }

    /**
     * Render and return as a Laravel response.
     */
    public function response(string $template, array $data = []): \Illuminate\Http\Response
    {
        return response($this->render($template, $data));
    }
}

