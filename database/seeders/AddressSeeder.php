<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->get()->first();

        Address::query()->create([
            'street' => 'jalan',
            'city' => 'kota',
            'province' => 'provinsi',
            'country' => 'negara',
            'postal_code' => '1111',
            'contact_id' => $contact->id,
        ]);
    }
}
