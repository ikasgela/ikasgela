<?php

namespace Tests\Unit;

use App\Http\Controllers\FlashCardController;
use App\Models\FlashCard;
use App\Models\FlashDeck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class FlashCardControllerTest extends TestCase
{
    use DatabaseTransactions;

    private FlashCardController $controller;

    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new FlashCardController();
    }

    public function testIndex()
    {
        $result = $this->controller->index();
        $this->assertNull($result);
    }

    public function testCreate()
    {
        $result = $this->controller->create();
        $this->assertNull($result);
    }

    public function testShow()
    {
        $deck = FlashDeck::factory()->create();
        $card = FlashCard::factory()->create(['flash_deck_id' => $deck->id]);
        $result = $this->controller->show($card);
        $this->assertNull($result);
    }

    public function testEdit()
    {
        $deck = FlashDeck::factory()->create();
        $card = FlashCard::factory()->create(['flash_deck_id' => $deck->id]);
        $result = $this->controller->edit($card);
        $this->assertNull($result);
    }

    public function testStore()
    {
        $request = new \App\Http\Requests\StoreFlashCardRequest();
        $result = $this->controller->store($request);
        $this->assertNull($result);
    }

    public function testUpdate()
    {
        $deck = FlashDeck::factory()->create();
        $card = FlashCard::factory()->create(['flash_deck_id' => $deck->id]);
        $request = new \App\Http\Requests\UpdateFlashCardRequest();
        $result = $this->controller->update($request, $card);
        $this->assertNull($result);
    }

    public function testStoreFlashCardRequestAuthorize()
    {
        $request = new \App\Http\Requests\StoreFlashCardRequest();
        $this->assertTrue($request->authorize());
    }

    public function testStoreFlashCardRequestRules()
    {
        $request = new \App\Http\Requests\StoreFlashCardRequest();
        $this->assertIsArray($request->rules());
    }

    public function testUpdateFlashCardRequestAuthorize()
    {
        $request = new \App\Http\Requests\UpdateFlashCardRequest();
        $this->assertTrue($request->authorize());
    }

    public function testUpdateFlashCardRequestRules()
    {
        $request = new \App\Http\Requests\UpdateFlashCardRequest();
        $this->assertIsArray($request->rules());
    }

    public function testDestroy()
    {
        $deck = FlashDeck::factory()->create();
        $card = FlashCard::factory()->create(['flash_deck_id' => $deck->id]);
        $result = $this->controller->destroy($card);
        $this->assertNull($result);
    }
}
