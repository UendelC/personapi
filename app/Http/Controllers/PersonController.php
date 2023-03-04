<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\Response;

class PersonController extends Controller
{
    public function index()
    {
        return Person::paginate();
    }

    public function show(Person $person): Person
    {
        return $person;
    }

    public function store(StorePersonRequest $request): Person
    {
        return Person::create($request->validated());
    }

    public function update(UpdatePersonRequest $request, Person $person): Person
    {
        return tap($person)->update($request->validated());
    }

    public function destroy(Person $person): Response
    {
        $person->delete();

        return response()->noContent();
    }
}
