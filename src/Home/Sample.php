<?php

declare(strict_types=1);

namespace Emarketeria\Home;

final class Sample
{
    public static function network(): Network
    {
        $network = new Network();
        $network->addCampaign(new Campaign('EC-11', 'Lumen Skincare', 'Night serum', 12900, 1200, 40000, 100000000));
        $network->addCampaign(new Campaign('EC-12', 'Rota Bikes', 'City frame', 349000, 600, 500000, 100000000));
        $network->addCampaign(new Campaign('EC-13', 'Cafe do Morro', 'Monthly box', 8900, 1500, 100, 250000));
        $network->addCampaign(new Campaign('EC-14', 'Verde Casa', 'Water filter', 47900, 900, 20000, 2000000));

        $network->addCreator(new Creator('@marina.faz', 180000, 'home'));
        $network->addCreator(new Creator('@pedala.rio', 740000, 'cycling'));
        $network->addCreator(new Creator('@mesa.curta', 4200, 'food'));
        $network->addCreator(new Creator('@casa.nova', 61000, 'home'));

        $network->record(new Sale('EC-11', '@marina.faz', 12900));
        $network->record(new Sale('EC-11', '@casa.nova', 25800));
        $network->record(new Sale('EC-12', '@pedala.rio', 349000));
        $network->record(new Sale('EC-13', '@mesa.curta', 8900));
        $network->record(new Sale('EC-14', '@marina.faz', 47900));

        $network->sales()[2]->reversed = true;

        return $network;
    }
}
