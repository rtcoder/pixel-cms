<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mainClient = Client::where('name', '--Developer--')->first();
        if (!$mainClient) {
            $mainClient = new Client();
            $mainClient->unsetEventDispatcher();
            $mainClient->fill([
                'name' => '--Developer--',
                'slug' => 'developer',
                'email' => 'dawidjez@gmail.com',
                'available_locales' => ['pl', 'en'],
                'locale' => 'pl'
            ]);
            $mainClient->save();
        }

        $role = Role::firstOrCreate([
            'name' => 'Administrator',
            'is_admin' => true,
            'client_id' => $mainClient->id
        ]);

        $adminUser = User::where('email', 'dawidjez@gmail.com')->first();
        if (!$adminUser) {
            $adminUser = new User();
            $adminUser->fill([
                'email' => 'dawidjez@gmail.com',
                'name' => 'Administrator',
                'password' => bcrypt('i6$!Y@Zal3BpejCYga7A'),
                'client_id' => $mainClient->id,
                'role_id' => $role->id,
                'is_active' => true,
            ]);
            $adminUser->save();
        }
    }
}
