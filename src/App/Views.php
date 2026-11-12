<?php

declare(strict_types=1);

namespace Emarketeria\App;

use Emarketeria\Home\Network;
use Emarketeria\Home\Sample;

final class Views
{
    public function __construct(private readonly string $base)
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function money(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        $cents = abs($cents);

        return $sign . 'R$ ' . number_format(intdiv($cents, 100), 0, '.', ',') . ',' . sprintf('%02d', $cents % 100);
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$this->h($title)}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    public function notFound(string $path): string
    {
        return $this->header('Not found')
            . '<h1>Not found</h1>' . "\n"
            . '<p class="stage">' . $this->h($path) . ' is not a route here.</p>' . "\n"
            . $this->footer();
    }

    public function front(Sample $sample, string $today): string
    {
        $network = $sample->network();
        $out = $this->header('Emarketeria');
        $out .= '<h1>Emarketeria</h1>' . "\n";
        $out .= '<p class="lede">Brands publish a campaign and a follower band. Creators inside the band join, the network tracks the sale, and the commission follows the order.</p>' . "\n";
        $out .= '<p class="stage">Network as of ' . $this->h($today) . '.</p>' . "\n";

        $out .= '<h2>Campaigns</h2>' . "\n";
        $out .= '<table class="tiers">' . "\n";
        $out .= '<thead><tr><th>Brand</th><th>Product</th><th>Order</th><th>Commission</th><th>Follower band</th></tr></thead>' . "\n";
        $out .= '<tbody>' . "\n";
        foreach (['EC-11', 'EC-12', 'EC-13', 'EC-14'] as $id) {
            $campaign = $network->campaign($id);
            $out .= '<tr><td>' . $this->h($campaign->brand) . '</td>';
            $out .= '<td>' . $this->h($campaign->product) . '</td>';
            $out .= '<td>' . $this->money($campaign->orderCents) . '</td>';
            $out .= '<td>' . number_format($campaign->commissionBasisPoints / 100, 2, '.', '') . ' percent</td>';
            $out .= '<td>' . number_format($campaign->minFollowers / 1000, 0, '.', ',') . 'k to '
                . number_format($campaign->maxFollowers / 1000000, 0, '.', ',') . 'M</td></tr>' . "\n";
        }
        $out .= '</tbody>' . "\n" . '</table>' . "\n";

        $out .= '<h2>What each creator can join</h2>' . "\n";
        $out .= '<div class="cards">' . "\n";
        foreach (['@marina.faz', '@pedala.rio', '@casa.nova'] as $handle) {
            $creator = $network->creator($handle);
            $out .= '<article class="card">' . "\n";
            $out .= '<h3>' . $this->h($creator->handle) . '</h3>' . "\n";
            $out .= '<p>' . number_format($creator->followers / 1000, 0, '.', ',') . 'k followers, ' . $this->h($creator->niche) . '.</p>' . "\n";
            $out .= '<dl>';
            foreach ($network->matches($handle) as $campaign) {
                $out .= '<dt>' . $this->h($campaign->brand) . '</dt><dd>' . $this->money($campaign->orderCents) . ' order</dd>';
            }
            $out .= '<dt>Payout</dt><dd>' . $this->money($network->payout($handle)) . '</dd>';
            $out .= '</dl>' . "\n";
            $out .= '</article>' . "\n";
        }
        $out .= '</div>' . "\n";

        $out .= '<h2>The tracked sales</h2>' . "\n";
        $out .= '<table class="tiers">' . "\n";
        $out .= '<thead><tr><th>Campaign</th><th>Creator</th><th>Order</th><th>Commission</th><th>State</th></tr></thead>' . "\n";
        $out .= '<tbody>' . "\n";
        foreach ($network->sales() as $sale) {
            $out .= '<tr><td>' . $this->h($sale->campaignId) . '</td>';
            $out .= '<td>' . $this->h($sale->handle) . '</td>';
            $out .= '<td>' . $this->money($sale->orderCents) . '</td>';
            $out .= '<td>' . $this->money($network->commission($sale)) . '</td>';
            $out .= '<td><span class="badge ' . ($sale->reversed ? 'off' : 'on') . '">'
                . ($sale->reversed ? 'reversed' : 'tracked') . '</span></td></tr>' . "\n";
        }
        $out .= '</tbody>' . "\n</table>" . "\n";

        $out .= '<h2>Paid through the network</h2>' . "\n";
        $out .= '<p>' . number_format($network->paidOrders() / 100, 2, ',', '.') . ' in orders, after the reversal comes off.</p>' . "\n";
        $out .= $this->footer();

        return $out;
    }
}
