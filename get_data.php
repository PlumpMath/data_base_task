<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
if (!isset($_SESSION['id'])) {
    exit;
} else {
    require_once ('model.php');
}

$array = array(
    //Ч 1. Ст 20.3
    array(1, 2),
    array(1, 3),
    array(1, 4),
    // Ст 20.10
    array(3, 2),
    // Ст 20.11
    array(12, 2),
    array(12, 3),
    array(12, 4),
    // Ст 20.12
    array(4, 2),
    array(4, 3),
    array(4, 4),
    // Ст 23.1
    array(6, 2),
    // Ст 23.2
    array(7, 2),
    // Ст 23.3
    array(8, 2),
    // Ст 23.4
    array(9, 2),
    // Ст 23.5
    array(10, 2),
    // Ст 23.35
    array(5, 2),
    // Ст 23.61
    array(11, 2),
    array(11, 4),
    // Ст 24.4
    array(13, 2),
    // Ст 24.5
    array(14, 2),
    // Ст 24.6
    array(15, 2)
);

if (isset($_POST['id'])) {
    $id = $_POST['id'];
//$id = $_GET['id'];

    $data = [];
    switch ($id) {
        case 1:
            $data['new_label'] = ($_SESSION['quarter'] == 0) ? 'Итого за месяц' : 'Итого за квартал';
            break;
        case 2:

            $data['row_input'] = ($_SESSION['frm'] == 1) ? 10 : 11;
            $data['name'] = ($_SESSION['frm'] == 1) ? 'report1' : 'report2';
            break;
    }
} else if (isset($_POST['myJson'])) {
    $d_con = json_decode($_POST['myJson'], true);
    switch ($d_con['name']) {
        case 'context_data':
            $ctrl = new Control();
            $mon = $_SESSION['mon1'];
            $mro = $_SESSION['mro'];
            $year = $_SESSION['year'];
            $operation = $d_con['rowId'];
            $index = $d_con['iCol'] - 3;
            $frm = $_SESSION['frm'];
            if ($frm == 1) {
                $operation++;
                if ($operation == 13) {
                    $operation = 9;
                }
            } else {
                if ($operation == 13) {
                    $operation = 9;
                } else if ($operation == 9) {
                    $operation = 8;
                }
            }
            $data['operation'] = $operation;
            $st = $array[$index][0];
            $person = $array[$index][1];
            $cum = $_SESSION['quarter'];
            if ($_SESSION['quarter'] > 0) {
                $mon = $_SESSION['quarter'] * 3;
            }

            $data['sql'] = $ctrl->getDB()->getContextData($mro, $mon, $st, $person, $year, $frm, $operation, $cum);

//        $data = $ctrl->getDB()->getContextData(1, 1, 3, 2, 2016, 2, 1, 0);
            if (count($data) == 0) {
                $data['sql'][0]['reg_num'] = '0';
                $data['sql'][0]['fio_penalized'] = 'Heт данных';
                if ($operation > 8) {
                    $data['sql'][0]['summa'] = 0.0;
                }
            }
            break;
        case 'get_doc_id':
            $ctrl = new Control();
            $book = $ctrl->createNewDoc(DB_BOOK);
            $book->setQueryFld(REG_NUM, $d_con['reg_num']);
            $data['doc_id'] = $book->getFieldValue(ID);
            break;
    }
}


echo json_encode($data);

