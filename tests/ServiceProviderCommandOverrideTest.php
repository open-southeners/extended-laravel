<?php

namespace OpenSoutheners\ExtendedLaravel\Tests;

use Illuminate\Console\Application as Artisan;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Console\PolicyMakeCommand as BasePolicyMakeCommand;
use OpenSoutheners\ExtendedLaravel\Console\Commands\PolicyMakeCommand as OverridePolicyMakeCommand;
use PHPUnit\Framework\Attributes\Test;

class ServiceProviderCommandOverrideTest extends TestCase
{
    #[Test]
    public function make_policy_command_registered_on_artisan_is_our_override()
    {
        $artisan = $this->app[Kernel::class];

        $this->assertInstanceOf(
            OverridePolicyMakeCommand::class,
            $artisan->all()['make:policy']
        );
    }

    #[Test]
    public function a_third_party_package_extending_the_same_base_class_is_not_affected()
    {
        // Regression test for a Laravel Nova compatibility issue: Nova (and other
        // packages) independently extend the same base Laravel command classes we
        // override and register their own separately-named commands. This simulates
        // that, using an Artisan::starting hook registered earlier than ours (during
        // normal register()/boot(), unlike ours which defers to `booted()`), and
        // asserts neither package's command registration clobbers the other's.
        Artisan::starting(function (Artisan $artisan) {
            $artisan->add(new class($this->app['files']) extends BasePolicyMakeCommand
            {
                protected $name = 'nova:policy';
            });
        });

        $artisan = $this->app[Kernel::class];
        $all = $artisan->all();

        // Nova's own command survives untouched...
        $this->assertArrayHasKey('nova:policy', $all);
        $this->assertSame('nova:policy', $all['nova:policy']->getName());

        // ...and our make:policy override is unaffected by Nova's registration.
        $this->assertInstanceOf(OverridePolicyMakeCommand::class, $all['make:policy']);
    }
}
