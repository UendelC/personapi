<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class PersonControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItListsThePeoplePaginated(): void
    {
        $people = Person::factory()->count(20)->create();

        $this->getJson(route('people.index'))
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonFragment($people->first()->toArray());
    }

    public function testItShowsAPerson(): void
    {
        $person = Person::factory()->create();

        $this->getJson(route('people.show', $person))
            ->assertOk()
            ->assertJsonFragment($person->toArray());
    }

    public function testItCreatesAPerson(): void
    {
        $payload = Person::factory()->make()->toArray();

        $this->postJson(route('people.store'), $payload)
            ->assertCreated()
            ->assertJsonFragment($payload);

        $this->assertDatabaseHas(Person::class, $payload);
        $this->assertDatabaseCount(Person::class, 1);
    }

    public function testItDoesNotCreateAPersonWithAnInvalidCpf(): void
    {
        $payload = Person::factory()->make(['cpf' => '12345678901'])->toArray();

        $this->postJson(
            route('people.store'),
            $payload
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('cpf');

        $this->assertDatabaseCount(Person::class, 0);
    }

    public function testItDoesNotCreateAPersonWithAnExistingEmail(): void
    {
        $person = Person::factory()->create();

        $this->postJson(
            route('people.store'),
            Person::factory()->make(['email' => $person->email])->toArray()
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount(Person::class, 1);
    }

    public function testItDoesNotCreateAPersonWithAnExistingCpf(): void
    {
        $person = Person::factory()->create();

        $this->postJson(
            route('people.store'),
            Person::factory()->make(['cpf' => $person->cpf])->toArray()
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('cpf');

        $this->assertDatabaseCount(Person::class, 1);
    }

    public function testItDoesNotCreateAPersonWithAFutureDob(): void
    {
        $payload = Person::factory()
            ->make(['dob' => now()->tomorrow()->format('Y-m-d')])
            ->toArray();

        $this->postJson(
            route('people.store'),
            $payload
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('dob');

        $this->assertDatabaseCount(Person::class, 0);
    }

    public function testItUpdatesAPerson(): void
    {
        $person = Person::factory()->create();
        $data = Person::factory()->make()->toArray();

        $this->putJson(route('people.update', $person), $data)
            ->assertOk()
            ->assertJsonFragment($data);

        $this->assertDatabaseHas(Person::class, ['id' => $person->id] + $data);
        $this->assertDatabaseCount(Person::class, 1);
    }

    public function testItDoesNotUpdateToAnExistingEmail(): void
    {
        $person = Person::factory()->create();
        $otherPerson = Person::factory()->create();

        $this->putJson(
            route('people.update', $person),
            Person::factory()->make(['email' => $otherPerson->email])->toArray()
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseHas(Person::class, $person->toArray());
        $this->assertDatabaseCount(Person::class, 2);
    }

    public function testItDoesNotUpdateToAnExistingCpf(): void
    {
        $person = Person::factory()->create();
        $otherPerson = Person::factory()->create();

        $this->putJson(
            route('people.update', $person),
            Person::factory()->make(['cpf' => $otherPerson->cpf])->toArray()
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('cpf');

        $this->assertDatabaseHas(Person::class, $person->toArray());
        $this->assertDatabaseCount(Person::class, 2);
    }

    public function testItDoesNotUpdateToAFutureDob(): void
    {
        $person = Person::factory()->create();

        $this->putJson(
            route('people.update', $person),
            Person::factory()
                ->make(['dob' => now()->tomorrow()->format('Y-m-d')])
                ->toArray()
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('dob');

        $this->assertDatabaseHas(Person::class, $person->toArray());
        $this->assertDatabaseCount(Person::class, 1);
    }

    public function testItDoesNotUpdateToAnInvalidCpf(): void
    {
        $person = Person::factory()->create();

        $this->putJson(
            route('people.update', $person),
            Person::factory()->make(['cpf' => '12345678901'])->toArray()
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors('cpf');
    }

    public function testItDeletesAPerson(): void
    {
        $person = Person::factory()->create();

        $this->deleteJson(route('people.destroy', $person))
            ->assertNoContent();

        $this->assertDatabaseMissing(Person::class, $person->toArray());
        $this->assertDatabaseCount(Person::class, 0);
    }
}
