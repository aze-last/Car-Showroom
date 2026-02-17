<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array<int, string> $emails */
        $emails = config('showroom.admin_seed_emails', []);
        $password = config('showroom.admin_password', 'password');
        if (empty($emails)) {
            $this->command?->warn('AdminUserSeeder skipped: no ADMIN_SEED_EMAILS configured.');

            return;
        }

        $users = User::query()
            ->whereIn('email', $emails)
            ->get()
            ->keyBy(fn (User $user): string => strtolower($user->email));

        $promoted = 0;

        foreach ($emails as $email) {
            $user = $users->get($email);

            if (! $user instanceof User) {
                $created = new User;
                $created->forceFill([
                    'name' => 'Admin User',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'is_admin' => true,
                ]);
                $created->save();

                $this->command?->info("AdminUserSeeder: created new admin user {$email}.");
                $promoted++;

                continue;
            }

            $updated = false;

            if (! $user->is_admin) {
                $user->is_admin = true;
                $updated = true;
            }

            if (! Hash::check((string) $password, (string) $user->password)) {
                $user->password = Hash::make((string) $password);
                $updated = true;
            }

            if ($user->email_verified_at === null) {
                $user->email_verified_at = now();
                $updated = true;
            }

            if (! $updated) {
                continue;
            }

            $user->save();

            $this->command?->info("AdminUserSeeder: synced existing admin settings for {$email}.");
            $promoted++;
        }

        $this->command?->info("AdminUserSeeder: promoted {$promoted} user(s) to admin.");
    }
}
