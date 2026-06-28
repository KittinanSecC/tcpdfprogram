<?php

require_once('tcpdf/tcpdf.php');

// 🔹 A4 (mm)
$A4_WIDTH = 210;
$A4_HEIGHT = 297;

// 🔹 A4 (pixel @300 DPI)
$IMG_W = 2480;
$IMG_H = 3508;

$margin = 50;
$gap = 20;

// 🔹 เช็คไฟล์
if (!isset($_FILES['images'])) {
    die("ไม่มีไฟล์");
}

$files = $_FILES['images'];
$total = count($files['name']);

if ($total == 0) {
    die("ไม่มีรูป");
}

// =========================
// 1. สร้างภาพ A4
// =========================
$canvas = imagecreatetruecolor($IMG_W, $IMG_H);
$white = imagecolorallocate($canvas, 255, 255, 255);
imagefill($canvas, 0, 0, $white);

// 🔹 คำนวณ grid
$cols = ceil(sqrt($total));
$rows = ceil($total / $cols);

$usableWidth = $IMG_W - ($margin * 2) - ($gap * ($cols - 1));
$usableHeight = $IMG_H - ($margin * 2) - ($gap * ($rows - 1));

$cellWidth = floor($usableWidth / $cols);
$cellHeight = floor($usableHeight / $rows);

$index = 0;

for ($i = 0; $i < $total; $i++) {

    if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

    $tmp = $files['tmp_name'][$i];

    // 🔹 โหลดภาพ (กัน error)
    $imgData = @file_get_contents($tmp);
    if (!$imgData) continue;

    $src = @imagecreatefromstring($imgData);
    if (!$src) continue;

    $sw = imagesx($src);
    $sh = imagesy($src);

    // 🔹 scale
    $scale = min($cellWidth / $sw, $cellHeight / $sh);
    $nw = (int)($sw * $scale);
    $nh = (int)($sh * $scale);

    $row = floor($index / $cols);
    $col = $index % $cols;

    $x = (int)($margin + ($col * ($cellWidth + $gap)) + ($cellWidth - $nw) / 2);
    $y = (int)($margin + ($row * ($cellHeight + $gap)) + ($cellHeight - $nh) / 2);

    imagecopyresampled($canvas, $src, $x, $y, 0, 0, $nw, $nh, $sw, $sh);

    imagedestroy($src);
    $index++;
}

// 🔹 สร้างโฟลเดอร์ output ถ้ายังไม่มี
if (!is_dir('output')) {
    mkdir('output', 0777, true);
}

// 🔹 บันทึกภาพ temp
$tempImage = "output/temp_" . time() . ".jpg";
imagejpeg($canvas, $tempImage, 100);
imagedestroy($canvas);


// =========================
// 2. สร้าง PDF A4
// =========================
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// 🔹 ปิด header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// 🔹 margin = 0
$pdf->SetMargins(0, 0, 0);
$pdf->SetAutoPageBreak(false, 0);

$pdf->AddPage();

// 🔹 ใส่ภาพเต็มหน้า
$pdf->Image($tempImage, 0, 0, $A4_WIDTH, $A4_HEIGHT, 'JPG', '', '', false, 300);

// 🔹 บันทึก PDF (ใช้ path จริง)
$pdfFile = __DIR__ . "/output/result_" . time() . ".pdf";
$pdf->Output($pdfFile, 'F');

// 🔹 ใช้ URL สำหรับแสดงผล
$pdfUrl = "output/" . basename($pdfFile);

// ลบ temp
unlink($tempImage);

// =========================
// 3. แสดงผล
// =========================
echo "<h2>สร้าง PDF สำเร็จ</h2>";
echo "<a href='$pdfUrl' target='_blank'>ดาวน์โหลด PDF</a><br><br>";
echo "<iframe src='$pdfUrl' width='100%' height='600px'></iframe>";