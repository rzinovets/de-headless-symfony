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
        $contactFormGroup = new TelegramGroup();
        $contactFormGroup->setCode(TelegramGroup::CODE_GROUP_NOTIFY_CONTACT_FORM);
        $contactFormGroup->setChatId('-4657886914');
        $contactFormGroup->setDescription('Contact form group');

        $manager->persist($contactFormGroup);

        $supportGroup = new TelegramGroup();
        $supportGroup->setCode(TelegramGroup::CODE_GROUP_NOTIFY_SUPPORT);
        $supportGroup->setChatId('-4778691122');
        $supportGroup->setDescription('Support group');

        $manager->persist($supportGroup);

        $manager->flush();
    }
}
