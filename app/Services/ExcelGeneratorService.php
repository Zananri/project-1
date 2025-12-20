<?php

namespace App\Services;

use App\Models\Transaction;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Storage;

class ExcelGeneratorService
{
    public function generateTransactionExcel(Transaction $transaction)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set sheet name
        $sheet->setTitle('Form Approval TR');
        
        // Header
        $sheet->setCellValue('A1', 'Form Approval Transaksi Resmi Perusahaan');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Info Pemohon
        $sheet->setCellValue('A3', 'Nama Pemohon');
        $sheet->setCellValue('C3', $transaction->nama_pemohon);
        $sheet->setCellValue('A4', 'Nama Perusahaan');
        $sheet->setCellValue('C4', $transaction->nama_perusahaan);
        $sheet->setCellValue('A5', 'Tanggal Pengajuan');
        $sheet->setCellValue('C5', $transaction->tanggal_pengajuan->format('d/m/Y'));
        
        // Table Headers
        $headers = ['No', 'Uraian Transaksi', 'Total', 'Dasar Transaksi', 'Lawan Transaksi', 
                    'Rekening Transaksi', 'Rencana Tanggal Transaksi', 'Pengakuan Transaksi', 'Keterangan'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '7', $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A7:I7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Data rows
        $row = 8;
        $totalSum = 0;
        
        // Group items by uraian_transaksi
        $groupedItems = [];
        foreach ($transaction->items as $item) {
            $uraian = $item->uraian_transaksi;
            if (!isset($groupedItems[$uraian])) {
                $groupedItems[$uraian] = [];
            }
            $groupedItems[$uraian][] = $item;
        }
        
        $groupNo = 1;
        foreach ($groupedItems as $uraian => $items) {
            $startRow = $row;
            
            // First row: Uraian Transaksi (bold, no data in other columns)
            $sheet->setCellValue('B' . $row, $uraian);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $row++;
            
            // Following rows: Kebutuhan with all data
            foreach ($items as $item) {
                $sheet->setCellValue('B' . $row, '    ' . $item->kebutuhan);
                $sheet->setCellValue('C' . $row, $item->total);
                $sheet->setCellValue('D' . $row, $item->dasar_transaksi);
                $sheet->setCellValue('E' . $row, $item->lawan_transaksi);
                $sheet->setCellValue('F' . $row, $item->rekening_transaksi);
                $sheet->setCellValue('G' . $row, $item->rencana_tanggal_transaksi ? 
                    $item->rencana_tanggal_transaksi->format('d/m/Y') : '');
                $sheet->setCellValue('H' . $row, $item->pengakuan_transaksi);
                $sheet->setCellValue('I' . $row, $item->keterangan);
                
                $totalSum += $item->total;
                $row++;
            }
            
            $endRow = $row - 1;
            
            // Merge the No column (A) vertically for entire group
            if ($endRow > $startRow) {
                $sheet->mergeCells('A' . $startRow . ':A' . $endRow);
            }
            $sheet->setCellValue('A' . $startRow, $groupNo);
            $sheet->getStyle('A' . $startRow)->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $startRow)->getFont()->setBold(true);
            
            $groupNo++;
        }
        
        // Total row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, $totalSum);
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Number format for total column
        $lastRow = $row;
        $sheet->getStyle('C8:C' . $lastRow)->getNumberFormat()
            ->setFormatCode('#,##0');
        
        // Text alignment for data rows
        $sheet->getStyle('D8:I' . ($lastRow - 1))->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);
        
        $sheet->getStyle('C8:C' . ($lastRow - 1))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        // Set all data columns (D-I) to left alignment
        $sheet->getStyle('D8:I' . ($lastRow - 1))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->getStyle('G8:G' . ($lastRow - 1))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Borders
        $sheet->getStyle('A7:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(35);
        
        // Catatan
        $row += 3;
        $sheet->setCellValue('A' . $row, 'Catatan:');
        $row++;
        $sheet->setCellValue('A' . $row, '1. Setiap kolom harus diisi sesuai ketentuan yang berlaku.');
        $row++;
        $sheet->setCellValue('A' . $row, '2. Bukti transaksi yang dilakukan dengan Pembelian Bensin, wajib dicantumkan dengan rapi dan dapat dipertanggungjawabkan kebenarannya.');
        
        // Generate filename
        $filename = 'Form_Approval_' . $transaction->nomor_transaksi . '_' . time() . '.xlsx';
        $filename = str_replace(['/', '.'], '_', $filename);
        
        // Save to storage
        $path = 'excel/' . $filename;
        $writer = new Xlsx($spreadsheet);
        
        // Create directory if not exists
        Storage::disk('public')->makeDirectory('excel');
        
        // Save file
        $fullPath = storage_path('app/public/' . $path);
        $writer->save($fullPath);
        
        return $path;
    }
}
