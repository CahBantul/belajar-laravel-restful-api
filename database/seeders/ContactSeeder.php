<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where('username', 'test')->first();

        $contact = new Contact();
        $contact->first_name = 'Fardan';
        $contact->last_name = 'Nozami';
        $contact->email = 'nozami@gmail.com';
        $contact->phone = '+6281229822979';
        $contact->user_id = $user->id;
        $contact->save();
    }
}
