<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\MinuteEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @method MinuteEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method MinuteEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method MinuteEntry[]    findAll()
 * @method MinuteEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MinuteEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MinuteEntry::class);
    }


    public function findByDay(Location $location, \DateTime $date)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT TIME(date_time) AS time, occupancy    
              FROM minute_entry
              WHERE location_id = :location_id AND DATE(date_time) = :date';


        $stmt = $conn->prepare($sql);
        $stmt->execute(array('location_id' => $location->getId(),
            'date' => $date->format('Y-m-d')));
        return $stmt->fetchAll();
    }


    public function findByLocationInRange(Location $location, \DateTime $from, \DateTime $to)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT TIME(date_time) AS time, MAX(occupancy) AS max, MIN(occupancy) AS min, AVG(occupancy) avg   
              FROM minute_entry
              WHERE location_id = :location_id AND date_time BETWEEN :from AND :to
              GROUP BY time';


        $stmt = $conn->prepare($sql);
        $stmt->execute(array('location_id' => $location->getId(),
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s')));
        return $stmt->fetchAll();
    }


    public function findDailyStatsInRange(Location $location, \DateTime $from, \DateTime $to)
    {

        $conn = $this->getEntityManager()->getConnection();


        $sql = 'SELECT MAX(max) AS max, AVG(max) AS avg, MIN(max) AS min, WEEKDAY(date) AS weekday
FROM (
         SELECT MAX(occupancy) AS max, DATE(date_time) AS date
         FROM minute_entry
         WHERE location_id = :location_id
           AND date_time BETWEEN :from AND :to
         GROUP BY date
     ) AS smth
GROUP BY weekday';


        $stmt = $conn->prepare($sql);
        $stmt->execute(array('location_id' => $location->getId(),
            'from' => $from->format('Y-m-d H:i:s'),
            'to' => $to->format('Y-m-d H:i:s')));
        return $stmt->fetchAll();

    }
}
