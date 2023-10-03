<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create(int $idContact, AddressCreateRequest $request) : JsonResponse 
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();

        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    function list(int $idContact) : JsonResponse 
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::query()->where('contact_id', $idContact)->get();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return (AddressResource::collection($address))->response()->setStatusCode(200);
    }

    function get(int $idContact, int $idAddress) : AddressResource 
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::query()->where('contact_id', $idContact)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new AddressResource($address);
    }

    function update(int $idContact, int $idAddress, AddressUpdateRequest $request) : AddressResource 
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::query()->where('contact_id', $idContact)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    function delete(int $idContact, int $idAddress) : JsonResponse 
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $address = Address::query()->where('contact_id', $idContact)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $address->delete();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
