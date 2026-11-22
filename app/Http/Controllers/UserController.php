<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /** @var array<string, string> */
    private const ROLE_LABELS = [
        'viewer' => 'مشاهدهگر',
        'editor' => 'ویرایشگر',
        'super-admin' => 'مدیر سیستم',
    ];

    /**
     * Display all users with their roles and direct permissions.
     */
    public function index(): View
    {
        $users = User::query()
            ->with(['roles', 'permissions'])
            ->orderBy('name')
            ->get();

        return view('users.index', [
            'users' => $users,
            'roleLabels' => self::ROLE_LABELS,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('users.create', $this->formData());
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($validated['role'] ?? null) {
            $user->assignRole($validated['role']);
        }

        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('users.index')
            ->with('success', 'کاربر با موفقیت ایجاد شد.');
    }

    /**
     * Redirect the unused resource show route to the edit form.
     */
    public function show(User $user): RedirectResponse
    {
        return redirect()->route('users.edit', $user);
    }

    /**
     * Show the form for editing the given user.
     */
    public function edit(User $user): View
    {
        return view('users.edit', array_merge($this->formData(), ['user' => $user]));
    }

    /**
     * Update the given user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateRequest($request, $user);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $user->is(auth()->user()) ? true : $request->boolean('is_active'),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $role = $validated['role'] ?? null;
        $user->syncRoles($role ? [$role] : []);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('users.index')
            ->with('success', 'اطلاعات کاربر با موفقیت بهروزرسانی شد.');
    }

    /**
     * Remove the given user, protecting the current session.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 403, 'نمی‌توانید حساب خود را حذف کنید.');

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'کاربر مورد نظر حذف شد.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        $permissions = Permission::all();

        return [
            'roles' => self::ROLE_LABELS,
            'permissions' => $permissions,
            'permissionGroups' => $permissions
                ->groupBy(fn (Permission $permission): string => Permissions::groupFor($permission->name))
                ->map(fn ($items, string $group): array => [
                    'label' => $group,
                    'permissions' => $items->all(),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateRequest(Request $request, ?User $user = null): array
    {
        $emailRule = Rule::unique('users', 'email');

        if ($user) {
            $emailRule->ignore($user->id);
        }

        $passwordRules = $user
            ? ['nullable', 'string', 'min:8', 'confirmed']
            : ['required', 'string', 'min:8', 'confirmed'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'password' => $passwordRules,
            'is_active' => ['boolean'],
            'role' => ['nullable', 'string', Rule::in(array_keys(self::ROLE_LABELS))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('auth_permissions', 'name')->where('guard_name', 'web'),
            ],
        ], [
            'name.required' => 'نام کاربر الزامی است.',
            'email.required' => 'ایمیل الزامی است.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.required' => 'رمز عبور الزامی است.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed' => 'تأیید رمز عبور مطابقت ندارد.',
            'role.in' => 'نقش انتخابشده معتبر نیست.',
            'permissions.array' => 'مجوزها باید به صورت لیست ارسال شوند.',
            'permissions.*.exists' => 'یکی از مجوزهای انتخابشده معتبر نیست.',
        ]);
    }
}
