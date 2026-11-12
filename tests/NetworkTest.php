<?php

declare(strict_types=1);

namespace Emarketeria\Tests;

use Emarketeria\Home\Campaign;
use Emarketeria\Home\Creator;
use Emarketeria\Home\Network;
use Emarketeria\Home\Sale;
use Emarketeria\Home\Sample;

return [
    ['a follower band runs low to high', static function (): void {
        $network = new Network();
        try {
            $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1000, 5000, 1000));
            Assert::true(false, 'an inverted band was accepted');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['a commission sits below the whole order', static function (): void {
        $network = new Network();
        try {
            $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 10000, 1000, 5000));
            Assert::true(false, 'a full commission was accepted');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['a creator outside the band joins nothing', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1000, 100000, 200000));
        $network->addCreator(new Creator('@small', 5000, 'home'));
        Assert::count(0, $network->matches('@small'));
    }],
    ['matches sort by commission then id', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-2', 'Second', 'Item', 10000, 1500, 0, 1000));
        $network->addCampaign(new Campaign('C-1', 'First', 'Item', 10000, 1500, 0, 1000));
        $network->addCampaign(new Campaign('C-3', 'Third', 'Item', 10000, 500, 0, 1000));
        $network->addCreator(new Creator('@any', 10, 'home'));
        $ids = array_map(static fn (Campaign $c): string => $c->id, $network->matches('@any'));
        Assert::same(['C-3', 'C-1', 'C-2'], $ids);
    }],
    ['a sale outside the band is refused', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1000, 100000, 200000));
        $network->addCreator(new Creator('@small', 5000, 'home'));
        try {
            $network->record(new Sale('C-1', '@small', 10000));
            Assert::true(false, 'an out of band sale was recorded');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
    ['commission is basis points of the order', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1200, 0, 1000000));
        $network->addCreator(new Creator('@any', 10, 'home'));
        $sale = new Sale('C-1', '@any', 349000);
        $network->record($sale);
        Assert::same(41880, $network->commission($sale));
    }],
    ['a reversed sale pays nothing', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1200, 0, 1000000));
        $network->addCreator(new Creator('@any', 10, 'home'));
        $sale = new Sale('C-1', '@any', 349000);
        $network->record($sale);
        $sale->reversed = true;
        Assert::same(0, $network->commission($sale));
        Assert::same(0, $network->payout('@any'));
    }],
    ['a payout sums every sale of one creator', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1000, 0, 1000000));
        $network->addCreator(new Creator('@any', 10, 'home'));
        $network->record(new Sale('C-1', '@any', 10000));
        $network->record(new Sale('C-1', '@any', 30000));
        Assert::same(4000, $network->payout('@any'));
    }],
    ['paid orders skip the reversed sale', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1000, 0, 1000000));
        $network->addCreator(new Creator('@any', 10, 'home'));
        $network->record(new Sale('C-1', '@any', 10000));
        $sale = new Sale('C-1', '@any', 50000);
        $network->record($sale);
        $sale->reversed = true;
        Assert::same(10000, $network->paidOrders());
    }],
    ['the sample network pays one creator across two campaigns', static function (): void {
        $network = Sample::network();
        Assert::same(5859, $network->payout('@marina.faz'));
        Assert::same(0, $network->payout('@pedala.rio'));
        Assert::count(5, $network->sales());
    }],
    ['a payout skips the campaign the creator sits outside', static function (): void {
        $network = new Network();
        $network->addCampaign(new Campaign('C-1', 'Brand', 'Item', 10000, 1000, 100000, 1000000));
        $network->addCreator(new Creator('@small', 5000, 'home'));
        try {
            $network->record(new Sale('C-1', '@small', 10000));
            Assert::true(false, 'a sale below the follower band was recorded');
        } catch (\InvalidArgumentException) {
            Assert::true(true);
        }
    }],
];
