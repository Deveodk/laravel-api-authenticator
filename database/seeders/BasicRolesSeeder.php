<?php

use DeveoDK\LaravelApiAuthenticator\Services\OptionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class BasicRolesSeeder extends Seeder
{
    /** @var array */
    private $roles;

    /** @var OptionService */
    private $optionsService;

    /**
     * BasicRolesSeeder constructor.
     * @param OptionService $optionsService
     */
    public function __construct(OptionService $optionsService)
    {
        $this->optionsService = $optionsService;
        $this->roles = $optionsService->get('seederRoles');
    }

    /**
     * Seed the given roles
     */
    public function run()
    {
        foreach ($this->roles as $role) {
            Role::create(['name' => $role, 'guard_name' => 'core']);
        }
    }
}
