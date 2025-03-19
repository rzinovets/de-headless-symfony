<?php

namespace App\DataFixtures;

use App\Entity\TelegramGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TelegramGroupFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $telegramGroup = new TelegramGroup();
        $telegramGroup->setCode(TelegramGroup::CODE_GROUP_NOTIFY_CONTACT_FORM);
        $telegramGroup->setChatId('-4657886914');
        $telegramGroup->setDescription('Contact form group');

        $manager->persist($telegramGroup);
        $manager->flush();
    }
}