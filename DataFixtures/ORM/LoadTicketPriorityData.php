<?php
namespace AWHS\CrmBundle\DataFixtures\ORM;

use AWHS\CrmBundle\Entity\TicketPriority;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTicketPriorityData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $priority0 = new TicketPriority();
        $priority0->setName('Not Applicable');
        $priority0->setColor('');
        $priority0->setEnabled(true);
        $manager->persist($priority0);

        $priority1 = new TicketPriority();
        $priority1->setName('Low');
        $priority1->setColor('');
        $priority1->setEnabled(true);
        $manager->persist($priority1);

        $priority2 = new TicketPriority();
        $priority2->setName('Normal');
        $priority2->setColor('');
        $priority2->setEnabled(true);
        $manager->persist($priority2);

        $priority3 = new TicketPriority();
        $priority3->setName('High');
        $priority3->setColor('');
        $priority3->setEnabled(true);
        $manager->persist($priority3);

        $priority4 = new TicketPriority();
        $priority4->setName('Immediate');
        $priority4->setColor('');
        $priority4->setEnabled(true);
        $manager->persist($priority4);


        $manager->flush();
    }
}
