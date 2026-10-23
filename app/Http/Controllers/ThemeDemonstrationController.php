<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThemeDemonstrationRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ThemeDemonstrationController extends Controller
{
    public function listing(): View
    {
        return view('theme.listing', [
            'navigation' => config('navigation'),
        ]);
    }

    public function form(): View
    {
        return view('theme.form', [
            'navigation' => config('navigation'),
        ]);
    }

    public function store(StoreThemeDemonstrationRequest $request): RedirectResponse
    {
        $request->validated();

        return redirect()
            ->route('theme.form')
            ->with('success', 'Demonstration validation passed. No data was saved.');
    }
}
