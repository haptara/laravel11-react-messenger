<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Fikri Nurmaila',
            'email' => 'fikrinurmaila@gmail.com',
            'password' => bcrypt('password'),
            'is_admin'  => true
        ]);
        User::factory()->create([
            'name' => 'Lea Dewi',
            'email' => 'leadewi@gmail.com',
            'password' => bcrypt('password'),
        ]);

        User::factory(10)->create();

        for($i = 0; $i < 5; $i++){
            $group  = Group::factory()->create([
                'owner_id'  => 1,
            ]);

            $users  = User::inRandomOrder()->limit(rand(2, 5))->pluck('id');
            $group->users()->attach(array_unique([1,...$users]));
        }

        Message::factory(1000)->create();
        $messages   = Message::whereNull('group_id')->orderBy('created_at')->get();

        $conversation   = $messages->groupBy(function ($message){
            return collect([$message->sender_id, $message->receiver_id])
            ->sort()->implode('_');
        })->map(function ($groupMessages){
            return [
                'user_id1'  => $groupMessages->first()->sender_id,
                'user_id2'  => $groupMessages->first()->receiver_id,
                'last_message_id'   => $groupMessages->last()->id,
                'created_at'    => new Carbon(),
                'updated_at'    => new Carbon()
            ];
        })->values();

        Conversation::insertOrIgnore($conversation->toArray());
    }
}
