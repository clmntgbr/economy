<?php

namespace App\Util\Logger;

use App\Entity\Logger as LoggerEntity;
use Doctrine\ORM\EntityManagerInterface;

class Command
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var \DateTime */
    private $start;

    /** @var \DateTime */
    private $end;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function start()
    {
        $this->start = new \DateTime('now');
    }

    public function end(string $processus, $type, array $result)
    {
        $this->end = new \DateTime('now');

        $command = new LoggerEntity\Command(
            $processus,
            $type,
            $this->start,
            $this->end,
            $this->start->diff($this->end)->format('%H:%I:%S'),
            $result
        );

        $this->em->persist($command);
        $this->em->flush();
    }
}