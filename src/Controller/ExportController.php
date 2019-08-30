<?php

namespace App\Controller;


use App\Repository\LocationRepository;
use App\Repository\MinuteEntryRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/export/{location}/{from}/{to}/{interval}", name="export")
     */
    public function export(int $interval, string $location, string $from, string $to, MinuteEntryRepository $minuteEntryRepository, LocationRepository $locationRepository)
    {

        $location = $locationRepository->findOneBy(['name' => $location]);

        $from = new \DateTime($from . ' 08:00:00');
        $to = new \DateTime($to . ' 18:00:00');
        $data = $minuteEntryRepository->everythingByLocationInRange($location, $from, $to);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row_index = 4;
        $sheet->setCellValue('C' . strval($row_index - 1), 'DateTime');
        $sheet->setCellValue('D' . strval($row_index - 1), 'Occupancy');
        $sheet->setCellValue('E' . strval($row_index - 1), 'Total In');
        $sheet->setCellValue('F' . strval($row_index - 1), 'Total Out');

        foreach ($data as $entry){
            $time = $entry['time'];
            if(!(intval((new \DateTime($time))->format('i')) % $interval)) {
                $sheet->setCellValue('C' . strval($row_index), $time);
                $sheet->setCellValue('D' . strval($row_index), $entry['occupancy']);
                $sheet->setCellValue('E' . strval($row_index), $entry['total_in']);
                $sheet->setCellValue('F' . strval($row_index), $entry['total_out']);
                $row_index++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $file_name = $location->getName() . '_from_' . $from->format('Y-m-d') . '_to_' . $to->format('Y-m-d') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $file_name);
        $writer->save($temp_file);



        return $this->file($temp_file, $file_name, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
