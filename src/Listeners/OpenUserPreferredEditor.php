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

        print_r($openEditorUri);

        if (! $openEditorUri) {
            return;
        }

        if (windows_os()) {
            return exec('explorer '.str_replace('%path', $event->filePath, $openEditorUri));
        }

        if (PHP_OS_FAMILY === 'Linux') {
            return exec('xdg-open '.str_replace('%path', $event->filePath, $openEditorUri));
        }

        if (PHP_OS_FAMILY === 'Darwin') {
            return exec('open '.str_replace('%path', $event->filePath, $openEditorUri));
        }
    }
}
