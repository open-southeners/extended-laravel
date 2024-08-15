<?php

namespace OpenSoutheners\ExtendedLaravel;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Laravel\Passport\Passport;
use Laravel\Socialite\Contracts\Factory;
use ReflectionClass;
use Stripe\Stripe;

class AboutCommandIntegration
{
    public static function register()
    {
        if (! class_exists(AboutCommand::class)) {
            return;
        }

        (new static)
            ->printExtendedDriversInfo()
            ->printIntegrationsInfo()
            ->printCashierInfo();
    }

    private function getUserConfigured(string $variable, string $value): string
    {
        $userConfiguredValue = env($variable);

        if (! empty($userConfiguredValue)) {
            return sprintf('<fg=yellow;options=bold>%s</>', $value);
        }

        return $value;
    }

    public function printExtendedDriversInfo(): self
    {
        AboutCommand::add('Drivers', fn (): array => [
            'Storage' => $this->getUserConfigured('FILESYSTEM_DISK', Storage::getDefaultDriver()),
        ]);

        return $this;
    }

    public function printIntegrationsInfo(): self
    {
        AboutCommand::add('Integrations', function (): array {
            $integrations = [];

            if (class_exists(Factory::class)) {
                $socialiteManager = app(Factory::class);

                $providers = array_keys((new ReflectionClass($socialiteManager))
                    ->getProperty('customCreators')
                    ->getValue($socialiteManager));

                $integrations['Socialite'] = sprintf(
                    '%s %s',
                    ! empty($providers) ? '<fg=green;options=bold>YES</>' : '<fg=yellow;options=bold>NO</>',
                    ! empty($providers) ? Str::wrap(implode(', ', $providers), '(', ')') : ''
                );
            }

            if (class_exists(Passport::class)) {
                $integrations['Passport'] = (
                    ! empty(env('PASSPORT_PRIVATE_KEY')) && ! empty(env('PASSPORT_PUBLIC_KEY'))
                    || (Passport::keyPath('oauth-public.key') && Passport::keyPath('oauth-private.key')
                    )) ? '<fg=green;options=bold>YES</>' : '<fg=yellow;options=bold>NO</>';
            }

            return $integrations;
        });

        return $this;
    }

    public function printCashierInfo(): self
    {
        $cashierDriver = match (true) {
            class_exists('Laravel\Cashier\Cashier') => 'Stripe',
            class_exists('Laravel\Paddle\Cashier') => 'Paddle',
            default => false,
        };

        if (! $cashierDriver) {
            return $this;
        }

        AboutCommand::add('Cashier', function () use ($cashierDriver): array {
            $infoArray = [
                'Driver' => $cashierDriver,
                'Cashier version' => Cashier::VERSION,
                'Customer model' => Cashier::$customerModel,
                'Enables routes' => Cashier::$registersRoutes ? '<fg=green;options=bold>YES</>' : '<fg=yellow;options=bold>NO</>',
                'Enables taxes' => Cashier::$calculatesTaxes ? '<fg=green;options=bold>YES</>' : '<fg=yellow;options=bold>NO</>',
                'Enables incomplete subscriptions' => Cashier::$deactivateIncomplete ? '<fg=green;options=bold>YES</>' : '<fg=yellow;options=bold>NO</>',
                'Enables past due subscriptions' => Cashier::$deactivatePastDue ? '<fg=green;options=bold>YES</>' : '<fg=yellow;options=bold>NO</>',
            ];

            if ($cashierDriver === 'Stripe') {
                $infoArray['Enabled'] = Cashier::stripe()->getApiKey() ? '<fg=green;options=bold>YES</>' : '<fg=yellow;options=bold>NO</>';
                $infoArray['Stripe SDK version'] = Stripe::VERSION;
                $infoArray['Stripe API version'] = Cashier::STRIPE_VERSION;
            }

            return $infoArray;
        });

        return $this;
    }
}
