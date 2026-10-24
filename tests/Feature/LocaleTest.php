<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function test_default_locale_is_en(): void
    {
        $this->get('/login');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_locale_can_be_switched_via_route(): void
    {
        $response = $this->get('/locale/km');

        $response->assertRedirect();
        $response->assertSessionHas('locale', 'km');
        $response->assertCookie('twa_locale', 'km');
    }

    public function test_locale_is_restored_from_session(): void
    {
        $this->withSession(['locale' => 'km'])->get('/login');

        $this->assertSame('km', app()->getLocale());
    }

    public function test_locale_is_restored_from_cookie(): void
    {
        $this->withCookie('twa_locale', 'km')->get('/login');

        $this->assertSame('km', app()->getLocale());
    }

    public function test_invalid_locale_is_ignored(): void
    {
        $this->get('/locale/invalid');

        $this->assertNotSame('invalid', session('locale'));
    }

    public function test_translation_files_exist_and_provide_keys(): void
    {
        $this->assertSame('Dashboard', __('app.dashboard', [], 'en'));
        $this->assertSame('ផ្ទាំងគ្រប់គ្រង', __('app.dashboard', [], 'km'));
    }
}
