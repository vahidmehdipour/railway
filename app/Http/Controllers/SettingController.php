<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Setting;
use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * The settings managed through the settings page.
     *
     * @var array<string, string>
     */
    private const DEFAULT_SETTINGS = [
        'max_theory_score' => '14',
        'max_practical_score' => '4',
        'max_ethics_score' => '2',
        'accident_coefficient' => '2',
        'near_miss_coefficient' => '1',
        'warning_coefficient' => '0.5',
    ];

    /**
     * Display the settings and stations management page.
     */
    public function index(): View
    {
        $settings = collect(self::DEFAULT_SETTINGS)
            ->map(fn (string $default, string $key): mixed => get_setting($key, $default))
            ->all();

        return view('settings.index', [
            'settings' => $settings,
            'stations' => Station::query()
                ->withCount('evaluations')
                ->orderBy('name')
                ->get(),
            'roles' => Role::query()
                ->withCount('personnels')
                ->orderBy('name_fa')
                ->get(),
        ]);
    }

    /**
     * Update the application settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (array_keys(self::DEFAULT_SETTINGS) as $key) {
            $rules[$key] = ['required', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value],
            );
        }

        return back()->with('success', 'تنظیمات سیستم با موفقیت ذخیره شد.');
    }

    /**
     * Store a new station.
     */
    public function storeStation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:stations,name'],
        ], [
            'name.required' => 'نام ایستگاه الزامی است.',
            'name.unique' => 'این ایستگاه قبلاً ثبت شده است.',
        ]);

        Station::create($validated);

        return back()->with('success', 'ایستگاه جدید با موفقیت اضافه شد.');
    }

    /**
     * Show the form for editing a station.
     */
    public function editStation(Station $station): View
    {
        return view('settings.stations.edit', [
            'station' => $station,
        ]);
    }

    /**
     * Update an existing station.
     */
    public function updateStation(Request $request, Station $station): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('stations', 'name')->ignore($station->getKey())],
        ], [
            'name.required' => 'نام ایستگاه الزامی است.',
            'name.unique' => 'این ایستگاه قبلاً ثبت شده است.',
        ]);

        $station->update($validated);

        return redirect()->route('settings.index')->with('success', 'ایستگاه با موفقیت بهروزرسانی شد.');
    }

    /**
     * Remove a station.
     */
    public function destroyStation(Station $station): RedirectResponse
    {
        $station->delete();

        return back()->with('success', 'ایستگاه مورد نظر حذف شد.');
    }

    /**
     * Store a new personnel role definition.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        Role::create($this->validatedRole($request));

        return back()->with('success', 'نقش جدید با موفقیت اضافه شد.');
    }

    /**
     * Show the form for editing a personnel role definition.
     */
    public function editRole(Role $role): View
    {
        return view('settings.roles.edit', [
            'role' => $role,
        ]);
    }

    /**
     * Update an existing personnel role definition.
     */
    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $role->update($this->validatedRole($request, $role));

        return redirect()->route('settings.index')->with('success', 'نقش با موفقیت بهروزرسانی شد.');
    }

    /**
     * Remove an unused personnel role definition.
     */
    public function destroyRole(Role $role): RedirectResponse
    {
        if ($role->personnels()->exists()) {
            return back()->with('error', 'این نقش دارای پرسنل ثبتشده است و قابل حذف نیست.');
        }

        $role->delete();

        return back()->with('success', 'نقش مورد نظر حذف شد.');
    }

    /**
     * Validate a personnel role request.
     *
     * @return array<string, string>
     */
    private function validatedRole(Request $request, ?Role $role = null): array
    {
        $nameEn = Rule::unique('roles', 'name_en');
        $nameFa = Rule::unique('roles', 'name_fa');

        if ($role) {
            $nameEn->ignore($role->getKey());
            $nameFa->ignore($role->getKey());
        }

        return $request->validate([
            'name_fa' => ['required', 'string', 'max:255', $nameFa],
            'name_en' => ['required', 'string', 'max:255', $nameEn],
        ], [
            'name_fa.required' => 'نام فارسی نقش الزامی است.',
            'name_fa.unique' => 'این نقش قبلاً ثبت شده است.',
            'name_en.required' => 'نام انگلیسی نقش الزامی است.',
            'name_en.unique' => 'این نقش قبلاً ثبت شده است.',
        ]);
    }
}
