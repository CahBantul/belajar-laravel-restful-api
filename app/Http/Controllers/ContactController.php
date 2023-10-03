<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    private function getContact(User $user, $idContact) : Contact 
    {
        $contact = Contact::query()->where("id", $idContact)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $contact = new Contact($data);
        $user = Auth::user();
        $contact->user_id = $user->id;
        $contact->save();


        return (new ContactResource($contact))->response()->setStatusCode(201);
    }

    public function get(int $idContact): ContactResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

        return new ContactResource($contact);
    }

    public function update(int $idContact, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $idContact): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $contact->delete();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $size = $request->input('size', 10);
        $page = $request->input('page', 1);

        $contact = Contact::query()->where('user_id', $user->id);

        if ($request->filled("name")) {
            $contact->where('first_name', 'LIKE', "%$request->name%")
                    ->orWhere('last_name', 'LIKE', "%$request->name%");
        }

        if ($request->filled("email")) {
            $contact->where('email', 'LIKE', "%$request->email%");
        }

        if ($request->filled("phone")) {
            $contact->where('phone', 'LIKE', "%$request->phone%");
        }

        $contacts = $contact->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
