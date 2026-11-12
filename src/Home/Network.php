<?php

declare(strict_types=1);

namespace Emarketeria\Home;

use InvalidArgumentException;

/**
 * The matching network. It holds creators, campaigns and the sales that link
 * them, and it answers what a creator can join and what the network owes.
 */
final class Network
{
    private const RATE = 10000;

    /** @var array<string, Campaign> */
    private array $campaigns = [];

    /** @var array<string, Creator> */
    private array $creators = [];

    /** @var list<Sale> */
    private array $sales = [];

    public function addCampaign(Campaign $campaign): void
    {
        if ($campaign->commissionBasisPoints <= 0 || $campaign->commissionBasisPoints >= self::RATE) {
            throw new InvalidArgumentException('A commission sits above zero and below the whole order.');
        }
        if ($campaign->minFollowers > $campaign->maxFollowers) {
            throw new InvalidArgumentException('The follower band runs low to high.');
        }
        $this->campaigns[$campaign->id] = $campaign;
    }

    public function addCreator(Creator $creator): void
    {
        $this->creators[$creator->handle] = $creator;
    }

    public function campaign(string $id): Campaign
    {
        if (!isset($this->campaigns[$id])) {
            throw new InvalidArgumentException('No campaign with id ' . $id . '.');
        }

        return $this->campaigns[$id];
    }

    public function creator(string $handle): Creator
    {
        if (!isset($this->creators[$handle])) {
            throw new InvalidArgumentException('No creator with handle ' . $handle . '.');
        }

        return $this->creators[$handle];
    }

    /**
     * The campaigns whose follower band holds this creator, cheapest commission
     * to the brand first. A creator outside every band gets nothing.
     *
     * @return list<Campaign>
     */
    public function matches(string $handle): array
    {
        $creator = $this->creator($handle);
        $out = [];
        foreach ($this->campaigns as $campaign) {
            if ($campaign->accepts($creator->followers)) {
                $out[] = $campaign;
            }
        }
        usort($out, static function (Campaign $a, Campaign $b): int {
            return [$a->commissionBasisPoints, $a->id] <=> [$b->commissionBasisPoints, $b->id];
        });

        return $out;
    }

    public function record(Sale $sale): void
    {
        if (!$this->campaign($sale->campaignId)->accepts($this->creator($sale->handle)->followers)) {
            throw new InvalidArgumentException('That creator sits outside the band of that campaign.');
        }
        if ($sale->orderCents <= 0) {
            throw new InvalidArgumentException('A sale needs an order above zero.');
        }
        $this->sales[] = $sale;
    }

    /**
     * The commission on one sale, in whole cents. A basis point is a hundredth of
     * a percent, so the rounding happens once, at the end.
     */
    public function commission(Sale $sale): int
    {
        if ($sale->reversed) {
            return 0;
        }
        $rate = $this->campaign($sale->campaignId)->commissionBasisPoints;

        return intdiv($sale->orderCents * $rate, self::RATE);
    }

    /** @return list<Sale> */
    public function sales(): array
    {
        return $this->sales;
    }

    /**
     * What one creator has earned across the whole network, reversed sales
     * included at zero.
     */
    public function payout(string $handle): int
    {
        $sum = 0;
        foreach ($this->sales as $sale) {
            if ($sale->handle === $handle) {
                $sum += $this->commission($sale);
            }
        }

        return $sum;
    }

    public function paidOrders(): int
    {
        $sum = 0;
        foreach ($this->sales as $sale) {
            if (!$sale->reversed) {
                $sum += $sale->orderCents;
            }
        }

        return $sum;
    }

    /**
     * The network's own share, being the commission less what goes to the
     * creator. Here the creator takes the whole commission, so the share is zero
     * until a split is set.
     */
    public function networkShare(Sale $sale): int
    {
        return 0;
    }
}
