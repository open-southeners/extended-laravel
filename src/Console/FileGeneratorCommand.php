<?php

namespace OpenSoutheners\ExtendedLaravel\Console;

use Illuminate\Console\GeneratorCommand;

abstract class FileGeneratorCommand extends GeneratorCommand
{
    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        parent::handle();

        $this->openWithIde(
            $this->getPath(
                $this->qualifyClass($this->getNameInput()),
            ),
        );
    }

    /**
     * Get the editor file opener URL by its name.
     *
     * @param  string  $ide
     * @return string
     */
    protected function getEditorUrl($ide)
    {
        return match ($ide) {
            'sublime' => 'subl://open?url=file://%path',
            'textmate' => 'txmt://open?url=file://%path',
            'emacs' => 'emacs://open?url=file://%path',
            'macvim' => 'mvim://open/?url=file://%path',
            'phpstorm' => 'phpstorm://open?file=%path',
            'idea' => 'idea://open?file=%path',
            'vscode' => 'vscode://file/%path',
            'vscode-insiders' => 'vscode-insiders://file/%path',
            'vscode-remote' => 'vscode://vscode-remote/%path',
            'vscode-insiders-remote' => 'vscode-insiders://vscode-remote/%path',
            'atom' => 'atom://core/open/file?filename=%path',
            'nova' => 'nova://core/open/file?filename=%path',
            'netbeans' => 'netbeans://open/?f=%path',
            'zed' => 'zed://file//%path',
            default => '',
        };
    }

    /**
     * Open resulted file path with the configured IDE.
     *
     * @param  string  $path
     * @return string|false|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function openWithIde($path)
    {
        $openEditorUrl = $this->getEditorUrl(env('APP_IDE'));

        if (!$openEditorUrl) {
            return;
        }

        if (windows_os()) {
            return exec('explorer ' . str_replace('%path', $path, $openEditorUrl));
        }

        if (PHP_OS_FAMILY === 'Linux') {
            return exec('xdg-open ' . str_replace('%path', $path, $openEditorUrl));
        }

        if (PHP_OS_FAMILY === 'Darwin') {
            return exec('open ' . str_replace('%path', $path, $openEditorUrl));
        }
    }
}
