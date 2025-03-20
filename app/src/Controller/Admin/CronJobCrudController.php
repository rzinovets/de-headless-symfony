<?php

namespace App\Controller\Admin;

use Cron\CronBundle\Entity\CronJob;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CronJobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CronJob::class;
    }
}
