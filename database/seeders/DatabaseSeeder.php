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
       //we generate two users

       User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
        'is_admin' => true
       ]);

       User::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => bcrypt('password'),
        'is_admin' => true
       ]);
       //generate 10 users
       User::factory(10)->create();

       for ($i = 0; $i<5 ; $i++) {
         $group = Group::factory()->create([
            'owner_id' => 1,
         ]);

        $users = User::inRandomOrder()->limit(rand(2, 5))->pluck('id');
        //use array_unique and we don't have duplicated value

        $group->users()->attach(array_unique([1, ...$users]));

       }

       //generate 1000 mesage

       Message::factory(1000)->create();
        //whereNull, we are not generate the group because we take groupe_ID NULL
        //50 percent will be possible group_id null
       $message = Message::whereNull('group_id')->orderBy('created_at')->get();

       $conversations = $message->groupBy(function($message){

        //[2, 1]->sort()->[1, 2]->implode('_)->1_2 and 1_2 is the key of the grouped element in the new table return
        //ca et ca identique alors le message est collectionner dans un tableau et a la fin la clé de cette
        //valeur pour le nouveau tableau est 1_2 pour les message avec les couple sender-receiver identique
            return collect([$message->sender_id, $message->receiver_id])->sort()->implode('_');
       })->map(function($groupedMessages){
           return [
               //on recupère chaque groupe de message et à la fin on l'utilise
               //pour renvoyé le tableau suivant
              //this is a elt group message [[1, 2], [2, 1], [1,2]]
              //the first() is [1,2] it is the message and the sender_id of this message is [1, 2]->sender_id
              'user_id1' => $groupedMessages->first()->sender_id,
              'user_id2' => $groupedMessages->first()->receiver_id,
              'last_message_id' => $groupedMessages->last()->id,
               'created_at' => new Carbon(), //new date inside the conversation table
                'updated_at' => new Carbon(),
            ];
       })->values();

       Conversation::insertOrIgnore($conversations->toArray());

    }
}
