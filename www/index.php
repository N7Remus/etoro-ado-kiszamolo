<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
require_once __DIR__ . '/SimpleXLSX.php';
ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
set_time_limit(300);

$version = "0.3.2";

use Shuchkin\SimpleXLSX;

if (!function_exists('str_contains')) {
    /**
     * Check if substring is contained in string
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    function str_contains($haystack, $needle)
    {
        return (strpos($haystack, $needle) !== false);
    }
}
function getUSDInHUF($usd, $d)
{
    $d = str_replace("/", "-", $d);
    global $arfolyam;
    $i = 0;
    while (true) {
        $date = date('Y-m-d', strtotime("-$i day", strtotime($d)));
        if (!empty($arfolyam[$date])) {
            return $arfolyam[$date] * $usd;
        }
        if ($date == "1970-01-01") {
            die("Nem található a dátum az aktuális arfolyam-letoltes.xlsx-ben: $d");
        }
        $i++;
    }
}

function convertToDate($date)
{
    return date('Y-m-d', strtotime($date));
}

function compare_date_keys($dt1, $dt2) {
    return strtotime($dt1) - strtotime($dt2);
}
// Árfolyamadatok olvasása
$arfolyam = [];
$aktualis_szja = 0.15;
if ($xlsx = SimpleXLSX::parse('arfolyam-letoltes.xlsx')) {
    $rows = $xlsx->rows();
    /*
    [0] => Dátum/ISO
    [34] => USD
    */
    for ($i = 2; $i < count($rows); $i++) {
        $arfolyam[convertToDate($rows[$i][0])] = $rows[$i][34];
    }
    $a = array_keys($arfolyam);

    usort($a, "compare_date_keys");
    $f=reset($a);
    $e=end($a);
    echo "Elérhető árfolyamadatok $f -> $e <br>";
    echo "Az SZJA értéke $aktualis_szja (". $aktualis_szja*100 ."%)";
} else {
    echo SimpleXLSX::parseError();
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Etoro adó kiszamoló (<?= $version ?>)</title>
    <script src="/js/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/buttons.dataTables.min.css" />
     <script type="text/javascript" src="/js/datatables.min.js"></script>
    <script type="text/javascript" src="/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="/js/jszip.min.js"></script>
    <script type="text/javascript" src="/js/pdfmake.min.js"></script>
    <script type="text/javascript" src="/js/vfs_fonts.js"></script>
    <script type="text/javascript" src="/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="/js/buttons.print.min.js"></script>


    <!-- our project just needs Font Awesome Solid + Brands -->
    <link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="/chartjs/chart.umd.js"></script>


</head>


<body>
    <div class="">


        <header class="py-3 mb-3 border-bottom">
            <div class="container-fluid d-grid gap-3 align-items-center" style="grid-template-columns: 1fr;">

                <form id="main" class="" action="" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h1 class="">
                                Etoro-ado-kiszamolo (<?= $version ?>)
                            </h1>
                            <div class="btn-toolbar mb-2 mb-md-0">
                                <div class="btn-group me-2">
                                    <button type="submit" class="btn btn-sm  btn-outline-primary">Számolás</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tmp_name">Válassz Xlsx-et:</label>
                            <input type="file" name="files[]" id="file" accept=".xlsx, text/plain" class="form-control">
                        </div>
                    </div>
                </form>


            </div>
        </header>
        <?php

        if (!empty($_FILES)) {

            if (!empty($_FILES["files"]) && !empty($_FILES["files"]["tmp_name"]) && !empty($_FILES["files"]["tmp_name"][0])) {
                if ($xlsx = SimpleXLSX::parse($_FILES["files"]["tmp_name"][0])) {
        ?>
                    <div class="container-fluid ">
                        <div class="d-grid gap-3 " style="grid-template-columns: 1fr 1fr;">
                            <div class="bg-light border rounded-3 p-2">
                            <h2 class="display-6 py-3" style="text-align:center">Tranzakció(k)</h2>

                                <table id="example" class="table table-striped " style="width:100%">
                                    <thead>

                                        <tr>
                                            <td>Id</td>
                                            <td>Action</td>
                                            <td>Open</td>
                                            <td>Close</td>
                                            <td>Profit (USD)</td>
                                            <td>Profit (HUF)</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                        $negsum = 0;
                                        $sum = 0;
                                        $crypto = 0;
                                        if ($xlsx) {

                                            $closed = $xlsx->rows(1);
                                            $divident = $xlsx->rows(3);
                                            $dynamic_Profit_column = 8; // Profit / Profit(USD)
                                            $dynamic_Open_column = 4; // Close Date
                                            $dynamic_Close_column = 5; // Close Date
                                            $dynamic_Type_column = 15; // Close Date
                                            
                                            // dinamikus field keresés, megváltozott az exportált fájl mező sorrendje
                                            foreach ($closed[0] as $key => $value) {
                                                if (str_contains($value, 'Profit')) {
                                                    $dynamic_Profit_column = $key; // Profit / Profit(USD)
                                            
                                                }
                                                if (str_contains($value, 'Close Date')) {
                                                    $dynamic_Close_column = $key; // Close Date
                                                }
                                                if (str_contains($value, 'Type')) {
                                                    $dynamic_Type_column = $key; // Close Date
                                                }

                                                if (str_contains($value, 'Open Date')) {
                                                    $dynamic_Open_column = $key; // Close Date
                                                }
                                                
                                            }
                                            

                                            // [8] profit
                                            for ($i = 1; $i < count($closed); $i++) {
                                                // profit per transaction
                                                // var_dump($closed[$i][8]);
                                                // Amount       Units   Open Date       Close Date      Leverage       Spread   Profit

                                                if ($closed[$i][$dynamic_Type_column] == "Crypto") {
                                                    $crypto += getUSDInHUF($closed[$i][$dynamic_Profit_column], $closed[$i][$dynamic_Close_column]);
                                                } else {
                                                    $s = getUSDInHUF($closed[$i][$dynamic_Profit_column], $closed[$i][$dynamic_Close_column]);
                                                    if ($s < 0) {
                                                        $negsum += $s;
                                                    } else {
                                                        $sum += $s;
                                                    }
                                                }
                                        ?>
                                                <tr>
                                                    <td><?= $closed[$i][0] ?></td>
                                                    <td><?= $closed[$i][1] ?></td>
                                                    <td><?= $closed[$i][$dynamic_Open_column] ?></td>
                                                    <td><?= $closed[$i][$dynamic_Close_column] ?></td>
                                                    <td><?= $closed[$i][$dynamic_Profit_column] ?></td>
                                                    <td><?= getUSDInHUF($closed[$i][$dynamic_Profit_column], $closed[$i][$dynamic_Close_column]); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                    </tbody>

                                </table>
                                <hr>
                                <p>
                                    Stock bevétel (formázott) : <?= number_format($sum,2,"."," ") ?> Ft
                                    <br>
                                    Stock veszteség (formázott) : <?= number_format($negsum,2,"."," ") ?> Ft
                                    <br>
                                    Stock összesített bevétel : <?= $sum + $negsum ?> Ft
                                    <br>
                                    Stock összesített bevétel (formázott) : <?= number_format($sum + $negsum,2,"."," ") ?> Ft
                                    <br>
                                    Crypto bevétel (formázott) : <?= number_format($crypto,2,"."," ") ?> Ft
                                    <br>
                                    Crypto bevétel : <?= $crypto ?> Ft
                                </p>

                            </div>
                            <div class="bg-light border rounded-3 p-2">
                            <h2 class="display-6 py-3" style="text-align:center">Osztalék(ok)</h2>

                                <table id="example2" class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <td>Payment date</td>
                                            <td>Instrument</td>
                                            <td>Tax</td>
                                            <td>Profit (USD)</td>
                                            <td>Profit (HUF)</td>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                            $sum2 = 0;
                                            $szja = 0;
                                            for ($i = 1; $i < count($divident); $i++) {
                                                // profit - $divident[$i][2]
                                                // tax    - $divident[$i][3]
                                                $sum2 += getUSDInHUF($divident[$i][2], $divident[$i][0]);

                                                if (intval(trim(str_replace("%","",$divident[$i][3])))<15 )
                                                    $szja += getUSDInHUF($divident[$i][2], $divident[$i][0]) * $aktualis_szja;
                                        ?>
                                            <tr>
                                                <td><?= $divident[$i][0] ?></td>
                                                <td><?= $divident[$i][1] ?></td>
                                                <td><?= $divident[$i][3] ?></td>
                                                <td><?= $divident[$i][2] ?></td>
                                                <td><?= getUSDInHUF($divident[$i][2], $divident[$i][0]) ?></td>
                                            </tr>
                                    <?php
                                            }
                                        } else {
                                            echo SimpleXLSX::parseError();
                                        }
                                    ?>
                                    </tbody>
                                </table>
                                <hr>
                                <p>
                                    Kamatnyereség (formázott) : <?= number_format($sum2,2,"."," ") ?> Ft
                                    <br>
                                    Kamatnyereség : <?= $sum2 ?> Ft
                                    <br>
                                    Fizetendő SZJA (formázott) : <?= number_format($szja,2,"."," ") ?> Ft
                                    <br>
                                    Fizetendő SZJA : <?= $szja ?> Ft
                                </p>
                            </div>
                        </div>
                    </div>
        <?php
                }
            }
        }
        ?>



        <script>
            $(document).ready(function() {
                $('#example').DataTable({
                    dom: 'Bfrtip',
                    "language": {
                        "url": "/js/hu.json"
                    },
                    buttons: [
                        'csv', 'excel'
                    ]
                });
                $('#example2').DataTable({
                    dom: 'Bfrtip',
                    "language": {
                        "url": "/js/hu.json"
                    },
                    buttons: [
                        'csv', 'excel'
                    ]
                });
            });
        </script>
    </div>
    <div class="container">
  <footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
      <li class="nav-item"><a href="https://github.com/N7Remus/etoro-ado-kiszamolo" class="nav-link px-2 text-body-secondary">Az oldal forrása és dokumentációja elérhető a Github-on</a></li>
    </ul>
    <p class="text-center text-body-secondary">Ez az Etoro alaklmazás magyar adózáshoz készített feldolgozó hobbyprojek. <br>A generált adatok esetleges hibáiért/tévességéért semmilyen jellegű felelősséget nem vállalok.</p>
  </footer>
</div>
</body>

</html>
