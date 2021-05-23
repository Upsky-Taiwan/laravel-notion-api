<?php

namespace FiveamCode\LaravelNotionApi\Tests;

use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\MultiSelect;
use FiveamCode\LaravelNotionApi\Entities\Properties\Select;
use FiveamCode\LaravelNotionApi\Entities\Properties\Text;
use FiveamCode\LaravelNotionApi\Entities\PropertyItems\RichText;
use FiveamCode\LaravelNotionApi\Entities\PropertyItems\SelectItem;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;

/**
 * Class EndpointPagePropertyTest
 *
 * The fake API responses are based on our test environment with real api-calls (since the current Notion examples do not match with the actual calls).
 * @see https://developers.notion.com/reference/get-page
 *
 * @package FiveamCode\LaravelNotionApi\Tests
 */
class EnpointPagePropertyTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return ['FiveamCode\LaravelNotionApi\LaravelNotionApiServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Notion' => \FiveamCode\LaravelNotionApi\NotionFacade::class
        ];
    }

    /** @test */
    public function it_checks_if_specific_page_property_is_a_valid_multi_select_property()
    {
        // successful /v1/pages/PAGE_DOES_EXIST
        Http::fake([
            'https://api.notion.com/v1/pages/afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/pages/response_specific_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $pageResult = \Notion::pages()->find("afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8");
        $multiSelect = $pageResult->getProperty('Tags');

        $this->assertInstanceOf(Page::class, $pageResult);

        $this->assertInstanceOf(MultiSelect::class, $multiSelect);
        $this->assertSame('multi_select', $multiSelect->getType());
        $this->assertSame("[Ipd", $multiSelect->getId());
        $this->assertSame(['great', 'awesome'], $multiSelect->getNames());
        $this->assertInstanceOf(SelectItem::class, $multiSelect->getItems()->first());
        $this->assertInstanceOf(SelectItem::class, $multiSelect->getContent()->first());
        $this->assertSame("great", $multiSelect->getItems()->first()->getName());
        $this->assertSame("brown", $multiSelect->getItems()->first()->getColor());
        $this->assertSame("4e1cfee9-0acf-4cf0-ab01-121790c3eeab", $multiSelect->getItems()->first()->getId());
    }


    /** @test */
    public function it_checks_if_specific_page_property_is_a_valid_select_property()
    {
        // successful /v1/pages/PAGE_DOES_EXIST
        Http::fake([
            'https://api.notion.com/v1/pages/afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/pages/response_specific_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $pageResult = \Notion::pages()->find("afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8");
        $select = $pageResult->getProperty('SelectColumn');

        $this->assertInstanceOf(Page::class, $pageResult);

        $this->assertInstanceOf(Select::class, $select);
        $this->assertSame('select', $select->getType());
        $this->assertSame("nKff", $select->getId());
        $this->assertSame("testing", $select->getName());
        $this->assertInstanceOf(SelectItem::class, $select->getItem());
        $this->assertInstanceOf(SelectItem::class, $select->getContent());
        $this->assertSame("testing", $select->getItem()->getName());
        $this->assertSame("blue", $select->getItem()->getColor());
        $this->assertSame("e7ec0090-22c2-47d6-8035-f42af53032fb", $select->getItem()->getId());
    }

    /** @test */
    public function it_checks_if_specific_page_property_is_a_valid_text_property()
    {
        // successful /v1/pages/PAGE_DOES_EXIST
        Http::fake([
            'https://api.notion.com/v1/pages/afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8'
            => Http::response(
                json_decode(file_get_contents('tests/stubs/endpoints/pages/response_specific_200.json'), true),
                200,
                ['Headers']
            )
        ]);

        $pageResult = \Notion::pages()->find("afd5f6fb-1cbd-41d1-a108-a22ae0d9bac8");
        $text = $pageResult->getProperty('New Property 🔥');

        $this->assertInstanceOf(Page::class, $pageResult);

        $this->assertInstanceOf(Text::class, $text);
        $this->assertSame('text', $text->getType());
        $this->assertSame("|Zt@", $text->getId());
        $this->assertSame("this is a nice Text :-)", $text->getPlainText());
        $this->assertInstanceOf(RichText::class, $text->getRichText());
        $this->assertInstanceOf(RichText::class, $text->getContent());
        $this->assertSame("this is a nice Text :-)", $text->getRichText()->getPlainText());
        $this->assertCount(2, $text->getRichText()->getRaw());
    }
}
