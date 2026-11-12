<?php

declare(strict_types=1);

namespace Emarketeria\Home;

/**
 * A brand's campaign: what it pays per sale, the commission the network keeps
 * and the follower band it wants. All money in whole cents.
 */
final class Campaign
{
    public function __construct(
        public readonly string $id,
        public readonly string $brand,
        public readonly string $product,
        public readonly int $orderCents,
        public readonly int $commissionBasisPoints,
        public readonly int $minFollowers,
        public readonly int $maxFollowers,
    ) {
    }

    public function accepts(int $followers): bool
    {
        return $followers >= $this->minFollowers && $followers <= $this->maxFollowers;
    }
}
