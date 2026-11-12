<?php

declare(strict_types=1);

namespace Emarketeria\Home;

/**
 * A tracked sale. The commission is a share of the order in basis points, so one
 * hundred basis points is one percent. A sale reverses once and a reversed sale
 * pays nothing.
 */
final class Sale
{
    public bool $reversed = false;

    public function __construct(
        public readonly string $campaignId,
        public readonly string $handle,
        public readonly int $orderCents,
    ) {
    }
}
