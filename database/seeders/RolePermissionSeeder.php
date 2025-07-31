<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\task;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Forget cached roles and permissions
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // Define roles
            $roleNames = ['Admin', 'HR', 'Manager', 'Employee'];
            $roles = [];

            foreach ($roleNames as $roleName) {
                $roles[$roleName] = Role::firstOrCreate(['name' => $roleName]);
            }

            // Define permissions and assign to roles
            $permissionGroups = [
                'employee_permissions' => [
                    'manage employees' => ['HR'],
                    'approve leaves' => ['Manager'],
                    'view payroll' => ['Employee'],
                ],
                'payroll_permissions' => [
                    'view all payrolls' => ['Admin'],
                    'view hr and manager payrolls' => ['HR'],
                    'view employee payrolls' => ['Manager'],
                    'view own payroll' => ['Employee'],
                ],
                'dashboard_permissions' => [
                    'view all dashboards' => ['Admin'],
                    'view hr and below dashboards' => ['HR'],
                    'view manager and below dashboards' => ['Manager'],
                    'view own dashboard' => ['Employee'],
                ],
            ];

            foreach ($permissionGroups as $group) {
                foreach ($group as $permission => $rolesToAssign) {
                    $perm = Permission::firstOrCreate(['name' => $permission]);
                    foreach ($rolesToAssign as $roleName) {
                        $roles[$roleName]->givePermissionTo($perm);
                    }
                }
            }

            // Create 2 dummy users for each role
            $userGroups = [];

            foreach ($roles as $roleName => $role) {
                $userGroups[$roleName] = [];

                for ($i = 1; $i <= 2; $i++) {

                    $user = User::create([
                        'name'       => "$roleName User $i",
                        'email'      => strtolower($roleName) . $i . '@hrms.com',
                        'password'   => Hash::make('123456'),

                    ]);

                    $user->assignRole($roleName);
                    $userGroups[$roleName][] = $user;
                }
            }

            // Sample tasks
            $statuses = ['pending', 'in_progress', 'completed'];
            $titles = ['Prepare Report', 'Client Meeting', 'Data Entry', 'Performance Review', 'Weekly Sync'];
            $taskId = 1;

            // Admin assigns tasks to HR, Manager, Employee
            foreach ($userGroups['Admin'] as $admin) {
                foreach (['HR', 'Manager', 'Employee'] as $targetRole) {
                    foreach ($userGroups[$targetRole] as $targetUser) {
                        Task::create([
                            'assigned_by'  => $admin->id,
                            'assigned_to'  => $targetUser->id,
                            'title'        => $titles[array_rand($titles)] . " #$taskId",
                            'description'  => "Task #$taskId by Admin to " . $targetUser->name,
                            'status'       => $statuses[array_rand($statuses)],
                            'due_date'     => Carbon::now()->addDays(rand(2, 10)),
                        ]);
                        $taskId++;
                    }
                }
            }

            // HR assigns tasks to Manager and Employee
            foreach ($userGroups['HR'] as $hr) {
                foreach (['Manager', 'Employee'] as $targetRole) {
                    foreach ($userGroups[$targetRole] as $targetUser) {
                        task::create([
                            'assigned_by'  => $hr->id,
                            'assigned_to'  => $targetUser->id,
                            'title'        => $titles[array_rand($titles)] . " #$taskId",
                            'description'  => "Task #$taskId by HR to " . $targetUser->name,
                            'status'       => $statuses[array_rand($statuses)],
                            'due_date'     => Carbon::now()->addDays(rand(2, 10)),
                        ]);
                        $taskId++;
                    }
                }
            }

            // Manager assigns tasks to Employees
            foreach ($userGroups['Manager'] as $manager) {
                foreach ($userGroups['Employee'] as $employee) {
                    Task::create([
                        'assigned_by'  => $manager->id,
                        'assigned_to'  => $employee->id,
                        'title'        => $titles[array_rand($titles)] . " #$taskId",
                        'description'  => "Task #$taskId by Manager to " . $employee->name,
                        'status'       => $statuses[array_rand($statuses)],
                        'due_date'     => Carbon::now()->addDays(rand(2, 10)),
                    ]);
                    $taskId++;
                }
            }

            DB::commit();
            $this->command->info('✅ Roles, permissions, users, and tasks seeded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
        }
    }
}
