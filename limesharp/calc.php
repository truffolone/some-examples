<?php

//something basic
$bp = __DIR__ . "/";

//loading stuff
require($bp . "lib/Errors/CustomException.php");
require($bp . "lib/CreateDatePeriod.php");
require($bp . "lib/CreateCsv.php");
require($bp . "lib/CreateData.php");

//starting the app
try {

    //check CLI data for filename
    if(empty($_SERVER['argv'][1])) {
        throw new CustomException("No CSV filename", "Please specify a CSV file name as output");
    }

    $startMonth = date("d-m-Y");
    $months = empty($_SERVER['argv'][3]) ? 12 : (int) $_SERVER['argv'][3];

    //checking date and input mispelling
    if(!empty($_SERVER['argv'][2])) {
        $inputDate = $_SERVER['argv'][2];
        $id = explode("-", $inputDate);
        if(count($id) === 1) {
            //was it a badly formed argv[3]?
            if(strlen($id[0]) == 4) {
                //year
                $startMonth = date("d-m-Y", strtotime('01-01-' . $id[0]));
            } elseif(strlen($id[0]) <= 2) {
                $months = $id[0];
            } else {
                throw new CustomException("Badly Formed Start Date", "Start Date is Badly Formed (" . $inputDate . ")");
            }
        } elseif(count($id) === 2) {
            if(strlen($id[0]) === 4) {
                $startMonth = date("d-m-Y", strtotime("01-" . $id[1] . "-" . $id[0]));
            } elseif(strlen($id[1]) === 4) {
                $startMonth = date("d-m-Y", strtotime("01-" . $id[0] . "-" . $id[1]));
            } else {
                throw new CustomException("Badly Formed Start Date", "Start Date is Badly Formed (" . $inputDate . ")");
            }
        } else {
            $startMonth = date("d-m-Y", strtotime($inputDate));
        }
    }

    $timePeriodObj = new CreateDatePeriod($startMonth, $months);
    $period = $timePeriodObj->load();

    $myCsv = new CreateCsv();
    $myCsv->setCsvBP($bp);
    $myCsv->setCsvFolder('csv');
    $myCsv->setCsvName($_SERVER['argv'][1]);

    $myData = new CreateData();
    $myData->setPeriod($period);
    $dataArray = $myData->createData('limesharp')->retrieveData();

    $myCsv->setData($dataArray);
    $myCsv->setHeaders(array('Month', 'Payment Date', 'Bonus Payment Date'));
    $myCsv->createCsv();
} catch(CustomException $e) {
    echo "ERROR: " . $e->getTitle() . " => " . $e->getMessage() . "\n";
}