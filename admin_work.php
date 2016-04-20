<?php
session_start();
if (!isset($_SESSION['id'])) {
    exit;
} else {
    require_once ('model.php');
    $_SESSION[FRM_PREVIOUS] = $_SESSION[FRM_CURRENT];
    $_SESSION[FRM_CURRENT] = FRM_ADMIN_WORK;
    if ($_SESSION['id_menu'] == '1' || $_SESSION['id_menu'] == '2') {
        header('Location: doc_edit.php');
        exit();
    }
}

//JSON template  to exchange data  between back-end and front-end sides of the project
$json[DATA] = '{
            "val":"",
            "name_fld":"doc_id",
            "name_tbl":"' . DB_BOOK .
        '"}';
//convert to stdclass object
//$data = json_decode($json[DATA]);
//$db = new DB_Connect();
//$db->deleteAllRecords('menu');
$ctrl = new Control();
$data = json_decode($json[DATA]);
$month_name = ($_SESSION['mon1'] == $_SESSION['mon2'] && $_SESSION['mon1'] != 0) ? $ctrl->getMonthName($_SESSION['mon1']) : '';
$ctrl->setUser();


$list_array = array(
    0 => '--Выбрать--',
    1 => '1-ый квартал',
    2 => '2-ой квартал',
    3 => '3-ий квартал',
    4 => '4-ий квартал',
);

$list_kv = $ctrl->selectedListArray($list_array, $_SESSION['quarter']);
$list_kv = '<select name="quarter" id="quarter">' . $list_kv . ' </select>';

if ($_SESSION['mro'] == 0) {
    $_SESSION['mro'] = 4;
}
$list_mro = $ctrl->getDB()->selectedListR('mro', 'name', $_SESSION['mro']);

$list_mro = '<select name="mro" id="mro">' . $list_mro . ' </select>';
$str_h2 = "<div>Подготовка<span id='sel_date'>(Выборка по дате сообщения или акта)</span></div>";
$str_div = '<div id="toolbar" class="ui-widget-header ui-corner-all">
            <button id="year">За год </button>
            <label for="month_picker">За месяц</label>
            <input     id="month_picker" type="text" value=' . $month_name . '></div>';

switch ($_SESSION['id_menu']) {
    case '3':
    case '777':
        $str_h2 = "<div>"
                . "Список незарегистрированных дел "
                . "<span id='sel_date'>(Выборка по дате сообщения или акта)</span>"
                . "</div>";
        break;
    case '4':
        $str_h2 = "<div>Все дела<span id='sel_date'>(Выборка по дате сообщения или акта)</span></div>";
        break;
    case '5':
        $str_h2 = "<div>Оперативный журнал <span id='sel_date'>(Выборка по дате регистрации)</span></div>";
        $str_div = '
            <div id="toolbar" class="ui-widget-header ui-corner-all">
            <button id="year">За год </button>
            <button id="reg">Незарегистрированные</button>
            <label for="month_picker">За месяц</label>
            <input     id="month_picker" type="text" value=' . $month_name . '>' . $list_mro .
                '</div>';
        break;
    case '6':
        $str_h2 = "<div>Сообщения<span id='sel_date'>(Выборка по дате сообщения или акта)</span></div>";
        break;
    case '8':
        $str_h2 = "Отчет";
        $str_div = '<div id="toolbar" class="ui-widget-header ui-corner-all">
                    <label for="month_picker">За месяц</label>
                    <input     id="month_picker" type="text" value=' . $month_name . '>' . $list_mro .
                '<button id="save">Сохранить</button>'
                . ' <button id="load_excel">Выгрузить в excel</button></div>';
        break;
    case '9':
        $str_h2 = "Отчет ФОРМА 2 (Новая редакция)";
        $str_div = '<div id="toolbar" class="ui-widget-header ui-corner-all">
                    <label for="month_picker">За месяц</label>
                    <input     id="month_picker" type="text" value=' . $month_name . '>' . $list_mro .
                '<button id="save_frm">Сохранить</button>'
                . ' <button id="load_excel">Выгрузить в excel</button></div>';
        break;
    case '10':
        $str_h2 = "Отчет ФОРМА 1 (Новая редакция)";
        $str_div = '<div id="toolbar" class="ui-widget-header ui-corner-all">
                    <label for="month_picker">За месяц</label>
                    <input     id="month_picker" type="text" value=' . $month_name . '>' . $list_mro .
                '<button id="save_frm">Сохранить</button>'
                . ' <button id="load_excel">Выгрузить в excel</button></div>';
        break;
    case '11':
        $str_h2 = "Отчет ФОРМА 2 (Новая редакция)";
        $str_div = '<div id="toolbar" class="ui-widget-header ui-corner-all">
                    <label>За квартал</label>' . $list_kv . '<label>МРО</label>' . $list_mro
                . ' <button id="load_excel">Выгрузить в excel</button></div>';
        break;
    case '12':
        $str_h2 = "Отчет ФОРМА 1 (Новая редакция)";
        $str_div = '<div id="toolbar" class="ui-widget-header ui-corner-all">
                    <label>За квартал</label>' . $list_kv . '<label>МРО</label>' . $list_mro
                . ' <button id="load_excel">Выгрузить в excel</button></div>';
        break;
}





$style_selcol1 = ($_SESSION['order'] == 'reg_num1') ? " style='background-color:lightgrey;' " : EMPT_Y;
$style_selcol2 = ($_SESSION['order'] == 'name_i,to_who') ? " style='background-color:lightgrey;' " : EMPT_Y;
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
№ 3 must be  <link href="calendar/css/tcal.css" rel="stylesheet" type="text/css"  />

-->
<html>           
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="media/img/favicon.ico">
        <link href="css/jquery.contextMenu.css" rel="stylesheet" type="text/css">
        <link href="css/menu.css" rel="stylesheet" type="text/css">
        <link href="calendar/css/tcal.css" rel="stylesheet" type="text/css">
        <link href="css/mystyle.css" rel="stylesheet" type="text/css">
        <link href="zebra_datepicker/css/default.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="jquery/jquery.toolsF.min.js"></script> 
        <script type="text/javascript" src="jquery/jquery-ui.min.js"></script> 
        <link rel="stylesheet" href="jquery/jquery-ui.min.css" type="text/css" charset="utf-8">  
        <script type="text/javascript" src="calendar/scripts/tcal.js"></script> 
        <script type="text/javascript" src="scripts/lib/jquery.contextMenu.js"></script>  
        <script type="text/javascript" src="scripts/lib/json2.js"></script>  
        <script type="text/javascript" src="scripts/lib/date.js"></script>
        <script type="text/javascript" src="scripts/mylib.js"></script>  
        <script type="text/javascript" src="scripts/my_jquery.js"></script>  
        <script  type="text/javascript"src="zebra_datepicker/scripts/zebra_datepicker.js"></script>
        <script src="jqGrid/js/i18n/grid.locale-ru.js" type="text/javascript" charset="utf-8"></script>
        <script src="jqGrid/js/jquery.jqGrid.min.js" type="text/javascript" charset="utf-8"></script>
        <link rel="stylesheet" href="jqGrid/css/ui.jqgrid.css" type="text/css" charset="utf-8">  
        <title>Администратор</title>
        <STYLE>
            .cvtotal {
                background-color: bisque ;color:orangered;font-size: 12px;font-weight: bold !important;
            }
            .cvname {
                font-size: 12px;font-weight: bold;
            }
            .mybold td {font-weight : bold !important}
            .rowcss {
                background:#336699 ;color:white;font-size: 12px;font-weight: bold;
            }
            .row_color {
                background:#B2D2FF ;color:black;font-size: 12px;font-weight: bold;
            }
            .row_force {
                background:#777 ;color:white ;font-size: 12px;font-weight: bold !important;
            }
            .col_color {
                background:#B2D2FF ;color:black;font-size: 12px;font-weight: bold;
            }
            span.cellWithoutBackground
            {
                display:block;
                background-image:none;
                margin-right:-2px;
                margin-left:-2px;
                height:14px;
                padding:4px;
            }
            select {
                margin-left:20px;
                width: 400px;
            }

        </STYLE>
    </head>
    <body  onload='onLoadForm(<?php echo json_encode($data) ?>)'>
        <div id="sitebranding">
            <h1>Административные правонарушения</h1>
        </div>
        <?php
        $year = ($_SESSION['year'] != 0) ? $_SESSION['year'] : date("Y");
        echo '<div id="head_main"><div id="head_left"><dfn>Инспектор :   </dfn>   '
        . $_SESSION['fio'] . '<br /><dfn> Инспекция :</dfn>'
        . $_SESSION['inspection_name'] .
        '</div><div id="head_right"><dfn>Год :</dfn>' . $year . '</div></div>   ';
        ?>

        <div id="mainmenu">
            <ul>
                <li><a href="#">Дела в процессе</a>
                    <ul>
                        <li><a href="load.php"
                               onclick='menu_changed("3",<?php echo json_encode($data) ?>)'>
                                Незарегистрированные дела</a></li>
                        <li><a href="load.php"
                               onclick='menu_changed("6",<?php echo json_encode($data) ?>)'>
                                Сообщения</a></li>
                        <li><a href="load.php"
                               onclick='menu_changed("7",<?php echo json_encode($data) ?>)'>
                                Подготовка</a></li>
                        <li><a href="load.php"
                               onclick='menu_changed("4",<?php echo json_encode($data) ?>)'>
                                Все дела</a></li>
                        <li><a href="load.php"
                               onclick='menu_changed("5",<?php echo json_encode($data) ?>)'>
                                Оперативный журнал</a></li>
                    </ul>
                </li>
                <li><a href="#">Отчеты</a>
                    <ul>
                        <li><a href="load.php"
                               onclick='menu_changed("8",<?php echo json_encode($data) ?>)'>
                                Отчет</a></li>
                        <li><a href="#">Отчет "ФОРМА 1" (Новая редакция)</a>
                            <ul>
                                <li><a href="load.php"
                                       onclick='menu_changed("10",<?php echo json_encode($data) ?>)'>Месяц </a></li>
                                <li><a href="load.php"
                                       onclick='menu_changed("12",<?php echo json_encode($data) ?>)'>Квартал</a></li>
                            </ul>

                        </li>
                        <li><a href="#">Отчет "ФОРМА 2" (Новая редакция)</a>
                            <ul>
                                <li><a href="load.php"
                                       onclick='menu_changed("9",<?php echo json_encode($data) ?>)'>Месяц </a></li>
                                <li><a href="load.php"
                                       onclick='menu_changed("11",<?php echo json_encode($data) ?>)'>Квартал</a></li>
                            </ul>

                        </li>
                    </ul>
                </li>
                <li><a href="#">Справочник пользователей</a>
                    <ul>
                        <li><a href="new_user.php">Ввод </a></li>
                        <li><a href="spr_out.php">Просмотр и Редактирование </a></li>
                    </ul>
                </li>
                <li><a href="#">НСИ</a>
                    <ul>
                        <li id="li_mro"><a href="#">МРО</a></li>
                        <li id="li_inspection" ><a href="#">Районные инспекции</a></li>
                        <li id="li_holidays" ><a href="#">Праздничные дни</a></li>
                        <li id="li_weekends" ><a href="#">Перенос рабочих дней</a></li>
                        <li id="li_nsi_1" ><a href="#">Справочник №1</a></li>
                        <li id="li_nsi_2" ><a href="#">Справочник №2</a></li>
                        <li id="li_nsi_3" ><a href="#">Справочник №3</a></li>
                        <li id="li_nsi_4" ><a href="#">Справочник №4</a></li>
                        <li id="li_nsi_5" ><a href="#">Справочник №5</a></li>
                        <li id="li_nsi_6" ><a href="#">Справочник №6</a></li>
                        <li id="li_nsi_7" ><a href="#">Справочник №7</a></li>
                        <li id="li_nsi_8" ><a href="#">Справочник №8</a></li>
                        <li id="li_nsi_9" ><a href="#">Справочник №9</a></li>
                        <li id="li_nsi_10" ><a href="#">Справочник №10</a></li>
                        <li id="li_nsi_11" ><a href="#">Справочник №11</a></li>
                        <li id="li_nsi_12" ><a href="#">Справочник №12</a></li>
                    </ul>
                </li>
                <li><a href="logout.php">Выход</a></li>
            </ul><!-- Конец списка -->
        </div><!-- Конец блока #mainmenu -->

        <div id="bodycontent">
            <h3><?php echo $str_h2; ?></h3>
        </div> <!-- end of bodycontent div -->
        <?php echo $str_div; ?>

        <form  id='form_a' method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php
// put your code here
// initialize inspection options array
            $mon = ($_SESSION['mon1'] == $_SESSION['mon2']) ? $_SESSION['mon1'] : 13;
            $year = $_SESSION['year'];
            switch ($_SESSION['id_menu']) {
                case '777':
                case '3':
                    $row = $ctrl->getDB()->selectUnionBook('0', $mon, $year);
                    break;
                case '4':
                    $row = $ctrl->getDB()->selectUnionBook('1', $mon, $year);
                    break;
                case '5':
                    break;
                case '6':
                    $row = $ctrl->getDB()->selectUnionBook('2', $mon, $year);
                    break;
                case '7':
                    $row = $ctrl->getDB()->selectUnionBook('3', $mon, $year);
                    break;
            }
            if ($_SESSION['id_menu'] == '777' ||
                    $_SESSION['id_menu'] == '3' ||
                    $_SESSION['id_menu'] == '4' ||
                    $_SESSION['id_menu'] == '6' ||
                    $_SESSION['id_menu'] == '7') {
                echo "<table border='0' cellpadding='2' class='datatable' id='mytbl'>
<tr>
<th id='reg_num_insp'>Рег.номер</th>
<th id='fio_insp'>На кого поступило</th>";
                if ($_SESSION['id_menu'] == '4' || $_SESSION['id_menu'] == '6') {
                    echo "<th>Инспектор, составивший документ</th>";
                } else {
                    echo "<th>Инспектор, осуществляющий  подготовку дела к рассмотрению</th>";
                }
                echo "<th>Состояние</th></tr>";
                $old_item = "";
                foreach ($row as $item) {
                    if ($old_item != $item['name_i'] && $_SESSION['order'] == 'name_i,to_who') {
                        echo "<tr>";
                        echo "<th></th>";
                        echo "<th>" . $item['name_i'] . "</th>";
                        echo "<th></th>";
                        echo "<th></th>";
                        echo "</tr>";
                        $old_item = $item['name_i'];
                    }
//                echo "<tr   onclick='overlay(this.rowIndex,";
//                echo json_encode($data);
//                echo ")'>";
                    $style_selrow = ($_SESSION['id_doc'] == $item['doc_id']) ? " style='color:green;font-weight: bold;' " : EMPT_Y;
                    echo "<tr onclick='testDate(this," . $item['doc_id'] . ","
                    . '""' . ',"' . $item['reg_num'] . '"' . ',""' . ",1)'" . $style_selrow . " >";
                    echo "<td " . $style_selcol1 . ">" . $item['reg_num'] . "</td>";
                    echo "<td " . $style_selcol2 . ">" . $item['to_who'] . "</td>";
                    echo "<td>" . $item['fio'] . "</td>";
                    echo "<td>" . $item['name_12'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else if ($_SESSION['id_menu'] == '5') {
                $msg = $ctrl->createNewDoc(DB_MSG);
                $act = $ctrl->createNewDoc(DB_ACT);
                $prepare = $ctrl->createNewDoc(DB_PREPARE);
                $review = $ctrl->createNewDoc(DB_REVIEW);
                $penalty = $ctrl->createNewDoc(DB_PENALTY);
                $termination = $ctrl->createNewDoc(DB_TERMINATION);
                $inform = $ctrl->createNewDoc(DB_INFORM);
                $nsi_7 = $ctrl->createNewDoc(DB_NSI_7);
                $nsi_11 = $ctrl->createNewDoc(DB_NSI_11);
                $array = [];
                $i = 0;
                $year = $_SESSION['year'];
                $mro = $_SESSION['mro'];
                for ($mon = $_SESSION['mon1']; $mon <= $_SESSION['mon2']; $mon++) {
                    $order = ($_SESSION['order'] == 'name_i,to_who') ? 1 : 2;
                    $row = $ctrl->getDB()->getDailyReport($mro, $mon, $year, $order);
                    foreach ($row as $item) {
                        if ($_SESSION['mon1'] != 0) {
                            $array[$i]['mon'] = $ctrl->getMonthName($ctrl->getMonth($item['reg_date'], DATE_SQL));
                        } else {
                            $array[$i]['mon'] = 'Незарегистрированные';
                        }
                        $array[$i]['doc_id'] = $item['doc_id'];
                        $array[$i]['reg_date'] = (empty($item['reg_date'])) ? '""' :
                                '"' . $ctrl->formatDate($item['reg_date'], DATE_FORM) . '"';
                        $array[$i]['reg_num'] = $item['reg_num'];
                        $array[$i]['fio_penalized'] = $item['fio_penalized'];
                        $array[$i]['name_i'] = $item['name_i'];
                        $array[$i]['id_mro'] = $item['id_mro'];
                        $msg->setQueryFld(DOC_ID, $item['doc_id']);
                        $msg_col = $msg->getFieldValue('num');
                        if (!empty($msg_col)) {
                            $msg_col .=PHP_EOL;
                            $msg_col .='  от  ';
                            $msg_col .= $ctrl->formatDate($msg->getFieldValue('msg_date'), DATE_FORM);
                        }
                        $array[$i]['msg'] = $msg_col;
                        $act->setQueryFld(DOC_ID, $item['doc_id']);
                        $act_col = $act->getFieldValue('num');
                        $act_col .=PHP_EOL;
                        $act_col .='  от  ';
                        $act_col .= $ctrl->formatDate($act->getFieldValue('act_date'), DATE_FORM);
                        $array[$i]['act'] = $act_col;
                        $array[$i]['inspector'] = $ctrl->convertToFIO($item['fio']);
                        $array[$i]['inspector'] .= ' инспектор  ';
                        $array[$i]['inspector'] .= $item['name_i'];
                        $prepare->setQueryFld(DOC_ID, $item['doc_id']);
                        $prepare_col = '';
                        if (!$prepare->isEmptyField('protocol_num')) {
                            $prepare_col .= 'Протокол № ';
                            $prepare_col .= $prepare->getFieldValue('protocol_num');
                            $prepare_col .=PHP_EOL;
                            $prepare_col .='  от  ';
                            $prepare_col .= $ctrl->formatDate($prepare->getFieldValue('protocol_date'), DATE_FORM);
                        }
                        $array[$i]['prepare'] = $prepare_col;
                        $review->setQueryFld(DOC_ID, $item['doc_id']);
                        $review_col = '';
                        if ($review->isFound()) {
                            $review_col .= $ctrl->formatDate($review->getFieldValue('view_date'), DATE_FORM);
                            $id = $review->getFieldValue('nsi_11');
                            $nsi_11->setQueryFld(ID, $id);
                            $review_col .=($id == '3') ? ' начальнику ' : '  ';
                            $review_col .=$nsi_11->getFieldValue('name_11');
                        }
                        $array[$i]['review'] = $review_col;
                        $penalty->setQueryFld(DOC_ID, $item['doc_id']);
                        $termination->setQueryFld(DOC_ID, $item['doc_id']);
                        $solution_col = 'Постановление № ';
                        if (!$prepare->isEmptyField('statute_num')) {
                            $solution_col .= $prepare->getFieldValue('statute_num');
                            $solution_col .=PHP_EOL;
                            $solution_col .='  от  ';
                            $solution_col .= $ctrl->formatDate($prepare->getFieldValue('statute_date'), DATE_FORM);
                        } else if ($penalty->isFound()) {
                            $solution_col .= $penalty->getFieldValue('statute_num');
                            $solution_col .=PHP_EOL;
                            $solution_col .='  от  ';
                            $solution_col .= $ctrl->formatDate($penalty->getFieldValue('statute_date'), DATE_FORM);
                        } else if ($termination->isFound()) {
                            $solution_col .= $termination->getFieldValue('statute_num');
                            $solution_col .=PHP_EOL;
                            $solution_col .='  от  ';
                            $solution_col .= $ctrl->formatDate($termination->getFieldValue('statute_date'), DATE_FORM);
                        } else {
                            $solution_col = EMPT_Y;
                        }
                        $array[$i]['solution'] = $solution_col;
                        $inf_col = EMPT_Y;
                        if ($penalty->isFound()) {
                            $id = $penalty->getFieldValue('nsi_7');
                            $nsi_7->setQueryFld(ID, $id);
                            $inf_col .=$nsi_7->getFieldValue('name_7');
                        }
                        $array[$i]['inf'] = $inf_col;
                        $i++;
                    }
                }
//                var_dump($array);
                echo " <div class='context-menu-one box menu-1'>";
                echo "<table border='0' cellpadding='2' class='datatable' id='mytbl'>
                    
<tr>
<th id='reg_num_insp'>рег.№</th>
<th id='fio_insp'>Ф.И.О.</th>
<th>вх № и дата сообщения об адм. п/н</th>
<th>№ и дата акта</th>
<th>кто осуществляет проверку по сообщению</th>
<th>осуществленные процессуальные действия(№ и дата документа)</th>
<th>Напрвление дела на рассмотрение(№ и дата документа)</th>
<th>Принятое решение(№ и дата документа)</th>                
<th>Примечание</th>
</tr>";
                $old_item = "";
                $old_mro = "";
                $old_mon = "";
                $row = 1;
                foreach ($array as $item) {
                    if ($old_mon != $item['mon']) {
                        echo "<tr>";
                        echo "<th colspan='9' style='color:white;background-color:#f09c15;'>" . $item['mon'] . "</th>";
                        echo "</tr>";
                        $old_mon = $item['mon'];
                        $old_item = "";
                        $old_mro = "";
                        $row++;
                    }
                    if ($old_mro != $item['id_mro'] && $_SESSION['order'] == 'name_i,to_who') {
                        echo "<tr>";
                        echo "<th colspan='9' style='color:white;background-color:#0000CD;'>" . $item['name_i'] . "</th>";
                        echo "</tr>";
                        $old_mro = $item['id_mro'];
                        $old_item = $item['name_i'];
                        $row++;
                    } else if ($old_item != $item['name_i'] && $_SESSION['order'] == 'name_i,to_who') {
                        echo "<tr>";
                        echo "<th colspan='9' style='color:white;background-color:#5A7994;'>" . $item['name_i'] . "</th>";
                        echo "</tr>";
                        $old_item = $item['name_i'];
                        $row++;
                    }
                    $style_selrow = ($_SESSION['id_doc'] == $item['doc_id']) ? " style='color:green;font-weight: bold;' " : EMPT_Y;
                    $id_str = " id='row_" . $row . "' ";
                    echo "<tr" . $id_str . $style_selrow . " >";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'" . $style_selcol1 . "  >" . $item['reg_num'] . "</td>";

                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'" . $style_selcol2 . ">" . $item['fio_penalized'] . "</td>";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'>" . $item['msg'] . "</td>";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'>" . $item['act'] . "</td>";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'>" . $item['inspector'] . "</td>";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'>" . $item['prepare'] . "</td>";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'>" . $item['review'] . "</td>";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'>" . $item['solution'] . "</td>";
                    echo "<td onclick='testDate(this," . $item['doc_id'] . ","
                    . $item['reg_date'] . ',"' . $item['reg_num'] . '"' . ',"' . $item['fio_penalized'] . '"' . ",1)'>" . $item['inf'] . "</td>";
                    echo "</tr>";
                    $row++;
                }
                echo "</table>";
                echo "</div>";
            } else if ($_SESSION['id_menu'] == '8') {
                echo " <div class='context-menu-one box menu-1'>";
                echo '<table id="list"><tr><td></td></tr></table><div id="pager"></div>';
            } else if ($_SESSION['id_menu'] == '9') {
                echo " <div class='context-menu-one box menu-1'>";
                echo '<table id="list_new2"><tr><td></td></tr></table><div id="pager"></div>';
            } else if ($_SESSION['id_menu'] == '10') {
                echo " <div class='context-menu-one box menu-1'>";
                echo '<table id="list_new1"><tr><td></td></tr></table><div id="pager"></div>';
            } else if ($_SESSION['id_menu'] == '11') {
                echo " <div class='context-menu box menu-1'>";
                echo '<table id="list_new2"><tr><td></td></tr></table><div id="pager"></div>';
            } else if ($_SESSION['id_menu'] == '12') {
                echo " <div class='context-menu box menu-1'>";
                echo '<table id="list_new1"><tr><td></td></tr></table><div id="pager"></div>';
            }
            ?>
            <div id="overlay_reg">
                <div align="center">
                    <table border='0' cellpadding='2' class='mydatatable' align="center" name="usertbl">
                        <tr class="altrow">
                            <th id="overlay_head" align="left" colspan="2" >Ввод регистрационного номера</th>
                        </tr>
                        <tr class="altrow">
                            <th align="right" >ФИО</th>
                            <td id='fio_penalized' style='color:blue;font-weight: bold;' >
                            </td>
                        </tr>
                        <tr>
                            <th align="right" >Дата регистрации</th>
                            <td align="left">
                                <input id="reg_date"
                                       type="text" 
                                       name="reg_date"
                                       class="tcal"
                                       value=""
                                       title=""
                                       >
                            </td>
                        </tr>
                        <tr class="altrow">
                            <th align="right" >Регистрационный номер</th>
                            <td align="left">
                                <input class="txt_inp"   id="reg_num" name="reg_num" value="" type="text">
                            </td>
                        </tr>
                    </table>
                    <input     class="button button-blue1"   name="save_reg"   type="button"
                               value="Сохранить"
                               onclick='saveRegNum(1)'>
                    <input      class="button button-blue1"  id="cancel" type="button" 
                                name="cancel_reg" value="Отменить"
                                onclick='saveRegNum(2)'>
                </div>
            </div>
            <div class="overlay" id="overlay" style="display:none;"></div>  <!--это фоновый блок, тот самый оверлэй--> 
            <div class="nonebox" id="nonebox"> 
                <a class="box-close" id="box-close"></a> 
                <table id="list_context"><tr><td></td></tr></table><div id="pager_con"></div>            </div>
        </form>
        <input type="hidden" id="sess_pageX" value="<?php echo $_SESSION['pageXOffset']; ?>"/>
        <input type="hidden" id="sess_pageY" value="<?php echo $_SESSION['pageYOffset']; ?>"/>
        <iframe id="txtArea1" style="display:none"></iframe>
    </body>
</html>
