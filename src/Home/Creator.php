<?php

declare(strict_types=1);

namespace Emarketeria\Home;

/**
 * One creator. The follower count is what the band test reads and the handle is
 * what the payout reads.
 */
final class Creator
{
    public function __construct(
        public readonly string $handle,
        public readonly int $followers,
        public readonly string $niche,
    ) {
    }
}
