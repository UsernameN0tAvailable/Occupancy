<?php

namespace App\Controller;


use App\Repository\LocationRepository;
use App\Repository\MinuteEntryRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/export/{location}/{from}/{to}/{interval}", name="export")
     */
    public function export(int $interval, string $location, string $from, string $to, MinuteEntryRepository $minuteEntryRepository, LocationRepository $locationRepository)
    {
        $location = explode(',', $location);

        $counter = 0;
        foreach ($location as $entry) {
            $location[$counter] = $locationRepository->findOneBy(['name' => $entry]);
            $counter++;
        }

        $from = new \DateTime($from . ' 08:00:00');
        $to = new \DateTime($to . ' 18:00:00');


        $spreadsheet = new Spreadsheet();

        $i = 0;
        $names = '';
        foreach ($location as $loc) {

            $names = $names . $loc . '_';
            $data = $minuteEntryRepository->everythingByLocationInRange($loc, $from, $to);
            if (!$i) {
                $workSheet = $spreadsheet->getActiveSheet();
            } else {
                $workSheet = $spreadsheet->createSheet($i);
            }
            $this->fillSheet($interval, $workSheet, $data);
            $workSheet->setTitle($loc->getName());

            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $file_name = $names . 'from_' . $from->format('Y-m-d') . '_to_' . $to->format('Y-m-d') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $file_name);
        $writer->save($temp_file);

        return $this->file($temp_file, $file_name, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @param int $interval
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param array $data
     * @throws \Exception
     */
    public function fillSheet(int $interval, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $data): void
    {
        $row_index = 2;
        $sheet->setCellValue('A' . strval($row_index - 1), 'DateTime');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->setCellValue('B' . strval($row_index - 1), 'Occupancy');
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->setCellValue('C' . strval($row_index - 1), 'Total In');
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->setCellValue('D' . strval($row_index - 1), 'Total Out');
        $sheet->getColumnDimension('D')->setWidth(12);


        foreach ($data as $entry) {
            $time = $entry['time'];
            if (!(intval((new \DateTime($time))->format('i')) % $interval)) {
                $sheet->setCellValue('A' . strval($row_index), $time);
                $sheet->setCellValue('B' . strval($row_index), $entry['occupancy']);
                $sheet->setCellValue('C' . strval($row_index), $entry['total_in']);
                $sheet->setCellValue('D' . strval($row_index), $entry['total_out']);
                $row_index++;
            }
        }
    }
}
