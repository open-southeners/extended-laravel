<?php

namespace OpenSoutheners\ExtendedLaravel\Listeners;

use OpenSoutheners\ExtendedLaravel\Events\CommandFileGenerated;

class OpenUserPreferredEditor
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(CommandFileGenerated $event)
    {
        /** @phpstan-ignore larastan.noEnvCallsOutsideOfConfig */
        $openEditorUri = match (env('APP_IDE')) {
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

        if ($openEditorUri !== '') {
            match (true) {
                windows_os() => exec('explorer '.str_replace('%path', $event->filePath, $openEditorUri)),
                PHP_OS_FAMILY === 'Linux' => exec('xdg-open '.str_replace('%path', $event->filePath, $openEditorUri)),
                PHP_OS_FAMILY === 'Darwin' => exec('open '.str_replace('%path', $event->filePath, $openEditorUri)),
                default => '',
            };
        }
    }
}
