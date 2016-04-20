<?php

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/init.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class to contol objects View and Doc 
 * 
 *   
 */
class Control {

    private $db,
            $doc,
            $view,
            $json,
            $list,
            $user;
    private static $allowedClasses = array('Doc', 'View', 'DB_Connect');

    /**
     * Function
     */
    public function __get($property) {
        $caller = debug_backtrace(false);
        if (!isset($caller[1]))
            throw new Exception('Error');
        if (!in_array($caller[1]['class'], self::$allowedClasses))
            throw new Exception('Error');
        return $this->$property;
    }

    /**
     * Function
     */
    public function __construct() {
        $this->db = new DB_Connect();
        $this->db->instControl = $this;
        $this->doc = [];
        $this->user = [];
    }

    /**
     * Function
     */
    public function setUser() {
        $this->user['id'] = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';
        $this->user['name'] = isset($_SESSION['name']) ? $_SESSION['name'] : '';
        $this->user['fio'] = isset($_SESSION['fio']) ? $_SESSION['fio'] : '';
        $this->user['role'] = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $this->user['access'] = isset($_SESSION['access']) ? $_SESSION['access'] : '';
        $this->user['id_inspection'] = isset($_SESSION['id_inspection']) ? $_SESSION['id_inspection'] : '';
        $this->user['name_inspection'] = isset($_SESSION['inspection_name']) ? $_SESSION['inspection_name'] : '';
        $this->user['mro'] = isset($_SESSION['mro']) ? $_SESSION['mro'] : '';
    }

    /**
     * Function
     */
    public function setUserInspection($inspection) {
        $this->user['id_inspection'] = $inspection;
    }

    /**
     * Function
     */
    public function setUserRole($role) {
        $this->user['role'] = $role;
    }

    /**
     * Function
     */
    public function setUserMRO($mro) {
        $this->user['mro'] = $mro;
    }

    /**
     * Function
     */
    public function getUserRole() {
        return $this->user['role'];
    }

    /**
     * Function
     */
    public function createNewDoc($name) {
        if (!isset($this->doc[$name])) {
            $this->doc[$name] = new Doc();
            $this->doc[$name]->instControl = $this;
            $this->doc[$name]->create($name);
        }
        return $this->doc[$name];
    }

    /**
     * Function
     */
    public function createNewView($name) {
        $this->view = new View();
        $this->view->instControl = $this;
        $this->view->create($name);
        return $this->view;
    }

    /**
     * Function
     */
    public function createNewMVC($name) {
        $json = $this->json[$name];
        $data = json_decode($json);
        $this->createNewDoc($data->doc_name);
        $this->createNewView($data->view_name);
        $this->view->setDocName($data->doc_name); //to link doc and view
    }

    /**
     * Function
     */
    public function createNewList($name) {
        if (!isset($this->list[$name]))
            $this->list[$name] = [];
        return $this;
    }

    /**
     * Function
     */
    public function deleteList($name) {
        if (isset($this->list[$name]))
            unset($this->list[$name]);
    }

    /**
     * Function
     */
    public function deleteVolume() {
        $name_tbl = array(
            0 => DB_BOOK,
            1 => DB_MSG,
            2 => DB_ACT,
            3 => DB_PREPARE,
            4 => DB_REVIEW,
            5 => DB_PENALTY,
            6 => DB_TERMINATION,
            7 => DB_INFORM,
            8 => DB_PROTOCOL
        );
        for ($i = 1; $i < 9; $i++) {
            $tbl = $this->createNewDoc($name_tbl[$i]);
            $tbl->deleteUnmatchedItem();
        }
        $this->getDB()->deleteUnmatchedHead();
    }

    /**
     * Function
     */
    public function correctStateVolume() {
        $name_tbl = array(
            0 => DB_BOOK,
            1 => DB_MSG,
            2 => DB_ACT,
            3 => DB_PREPARE,
            4 => DB_REVIEW,
            5 => DB_PENALTY,
            6 => DB_TERMINATION,
            7 => DB_INFORM,
            8 => DB_PROTOCOL
        );
        $book = $this->createNewDoc($name_tbl[0]);
        $row = $book->selRecordS();
        foreach ($row as $item) {
            $status = STATE_NULL;
            for ($i = 7; $i > 1; $i--) {
                $tbl = $this->createNewDoc($name_tbl[$i]);
                $tbl->setQueryFld(DOC_ID, $item[ID]);
                if ($tbl->isFound()) {
                    switch ($i) {
                        case 1:
                            $status = STATE_MSG;
                            break;
                        case 2:
                            $status = STATE_ACT;
                            break;
                        case 3:
                            $status = STATE_PREPARE;
                            break;
                        case 4:
                            $status = STATE_REVIEW;
                            break;
                        case 5:
                            $status = STATE_PENALTY;
                            break;
                        case 6:
                            $status = STATE_TERMINATION;
                            break;
                        case 7:
                            $status = STATE_INFORM;
                            break;
                    }
                    break;
                }
            }
            if ($item[STATE] != $status)
                $book->updateRecord(STATE, $status);
        }
    }

    /**
     * Function
     */
    public function setJson($json) {
        $this->json = $json;
    }

    /**
     * Function POST
     */
    public function POST() {
        $res = false;
        $frm_name = $this->view->getFrmName();
        switch ($frm_name) {
            case FRM_MSG:
            case FRM_ACT:
            case FRM_PREPARE:
            case FRM_REVIEW:
            case FRM_PENALTY:
            case FRM_TERMINATION:
            case FRM_INFORM:
                if ($this->view->isFoundDoc()) {
                    $this->view->selectRecord();
                }
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['delete'])) {
                        header('Location:' . $_SERVER["PHP_SELF"]); //Where the button <delete> is clicked reload page
                        exit();
                    }
                    if (isset($_POST['save_prtcl_v'])) {
                        header('Location:' . $_SERVER["PHP_SELF"]);
                        exit();
                    }
                    if (isset($_POST['cancel_prtcl_v'])) {
                        header('Location:' . $_SERVER["PHP_SELF"]);
                        exit();
                    }
                    if (isset($_POST['delete_prtcl_v'])) {
                        header('Location:' . $_SERVER["PHP_SELF"]);
                        exit();
                    }
                    if (isset($_POST['save_prtcl_q'])) {
                        header('Location:' . $_SERVER["PHP_SELF"]);
                        exit();
                    }
                    if (isset($_POST['cancel_prtcl_q'])) {
                        header('Location:' . $_SERVER["PHP_SELF"]);
                        exit();
                    }
                    if (isset($_POST['delete_prtcl_q'])) {
                        header('Location:' . $_SERVER["PHP_SELF"]);
                        exit();
                    }
                    $this->view->POST();
                    if ($this->view->isSuccess()) {
                        $this->view->pushRecord();
                        if ($this->view->isFoundDoc()) {

                            $this->view->deleteDoc();
                        }
                        $res = $this->view->insertDoc();
                    }
                }
                break;
            case FRM_NEW_DOC:
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $doc_date = date("y.m.d");
                    $this->view->setFieldValueRecord('doc_date', $doc_date);
                    $this->view->pushRecord();
                    $res = $this->view->insertDoc();
                }
                break;
            case FRM_LOGIN:
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $this->view->POST();
                    $res = $this->view->isSuccess();
                }

                break;
            case FRM_NEW_USER:
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $this->view->POST();
                    if ($this->view->isSuccess()) {
                        $this->view->pushRecord();
                        $res = $this->view->insertDoc();
                    }
                }

                break;
            case FRM_REG_NUM:
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $this->view->POST();
                    if ($this->view->isSuccess()) {
                        $this->view->pushRecord();
                    }
                    $res = true;
                }
                break;
        }
        return $res;
    }

    public function getDoc($name) {
        return $this->doc[$name];
    }

    public function getDB() {
        return $this->db;
    }

    public function getView() {
        return $this->view;
    }

    public function getJson() {
        return $this->json;
    }

    public function getUser() {
        return $this->user;
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
     * Function to get the control date from the prepare table
     */
    public function getPrepareDate() {
        $array = [];
        $date_ctrl = '';
        if ($this->getDoc(DB_PREPARE)->isFound()) {
            $record = $this->getDoc(DB_PREPARE)->selectRecord();
            $row = (array) $record;
            foreach ($row as $key => $value) {
                $pos = strripos($key, '_'); //search '_' in the string
                if ($pos) {
                    $arr = preg_split("/[\s,_]+/", $key);
                    if ($arr[1] == 'date') {
                        $array[] = $value;
                    }
                }
            }
            sort($array);
        }
        $date_ctrl = $this->formatDate($array[count($array) - 1], DATE_US);
        return $date_ctrl;
    }

    /**
     * Function to get control date
     */
    public function getControlDate() {
        $date_ctrl = $this->getDate(DB_MSG, 'msg_date');
        $date_ctrl = empty($date_ctrl) ? $this->getDate(DB_ACT, 'act_date') : $date_ctrl;
        return $date_ctrl;
    }

    /**
     * Function to get msg date
     */
    public function getDate($db_name, $date_name) {
        $record = $this->getDoc($db_name)->isFound() ? $this->getDoc($db_name)->selectRecord() : [];
        $date_ctrl = empty($record) ? EMPT_Y : $record[$date_name];
        $date_ctrl = $this->formatDate($date_ctrl, DATE_US);
        return $date_ctrl;
    }

    /**
     * Function to get weekeend days 
     */
    public function getWeekendDays() {
        $row = $this->getDB()->selectWeekends();
        $array = [];
        if (count($row) != 0) {
            foreach ($row as $item) {
                $array[] = $this->formatDate($item['weekend_day'], DATE_US);
            }
        }
        return $array;
    }

    /**
     * Function to get working days 
     */
    public function getWorkingDays() {
        $row = $this->getDB()->selectWeekends();
        $array = [];
        if (count($row) != 0) {
            foreach ($row as $item) {
                $array[] = $this->formatDate($item['working_day'], DATE_US);
            }
        }
        return $array;
    }

    /**
     * Function to get holidays 
     */
    public function getHolidays() {
        $today = date(DATE_US);
        $year = $this->getYear($today, DATE_US);
        $row = $this->getDB()->selectHolidays();
        $array = [];
        if (count($row) != 0) {
            foreach ($row as $item) {
                $str_month = (intval($item['month']) < 10) ? '0' . $item['month'] : $item['month'];
                $str_day = (intval($item['day']) < 10) ? '0' . $item['day'] : $item['day'];
                $array[] = $this->formatDate($year . '-' . $str_month . '-' . $str_day, DATE_US);
            }
        }
        return $array;
    }

    /**
     * Function to get names of dates column
     */
    public function getDateColName() {
        $record = $this->getView()->getRecord();
        $name_tbl = $this->getView()->getDocName();
        $array = [];
        foreach ($record as $key => $value) {
            $pos = strripos($key, '_'); //search '_' in the string
            if ($pos) {
                $arr = preg_split("/[\s,_]+/", $key);
                if ($arr[1] == 'date') {
                    if ($this->getDB()->nameTbl($name_tbl)->isDateChecked($key))
                        $array[] = $key;
                }
            }
        }
        return $array;
    }

    /**
     * Function to get leap year 
     */
    function isLeapYear($year) {
        return ((bool) ( cal_days_in_month(CAL_GREGORIAN, 2, $year) - 28 ));
    }

    //$string = "PHP, HTML, CSS";
    //$arr = explode(", ", $string);
    /**
     * Function to get year from date
     */
    public function getYear($pdate, $format) {
        $date = DateTime::createFromFormat($format, $pdate);
        return $date->format("Y");
    }

    /**
     * Function to get month from date
     */
    public function getMonth($pdate, $format) {
        $date = DateTime::createFromFormat($format, $pdate);
        return (int) $date->format("m");
    }

    /**
     * Function to get month from date
     */
    public function getMonthName($mon) {
        $mons = array(
            1 => "Январь",
            2 => "Февраль",
            3 => "Март",
            4 => "Апрель",
            5 => "Май",
            6 => "Июнь",
            7 => "Июль",
            8 => "Август",
            9 => "Сентябрь",
            10 => "Октябрь",
            11 => "Ноябрь",
            12 => "Декабрь");
        return $mons[$mon];
    }

    /**
     * Function to get num  month 
     */
    public function getMonthNum($name_mon) {
        $mons = array(
            1 => "Январь",
            2 => "Февраль",
            3 => "Март",
            4 => "Апрель",
            5 => "Май",
            6 => "Июнь",
            7 => "Июль",
            8 => "Август",
            9 => "Сентябрь",
            10 => "Октябрь",
            11 => "Ноябрь",
            12 => "Декабрь");
        for ($i = 1; $i < 13; $i++) {
            if ($mons[$i] == $name_mon)
                return $i;
        }
        return 0;
    }

    /**
     * Function to get day from date
     */
    public function getDay($pdate) {
        $date = DateTime::createFromFormat(DATE_US, $pdate);
        return $date->format("d");
    }

    /**
     * Function to format date
     */
    public function formatDate($date, $formatS) {
        //       var_dump($date);
        $dateS = EMPT_Y;
        if ($this->preg_match_date($date)) {
            $dateN = new DateTime($date);
            $dateS = $dateN->format($formatS);
            if (empty($date))
                $dateS = EMPT_Y;
            if ($date == '0000-00-00')
                $dateS = EMPT_Y;
        }
        return $dateS;
    }

// Extract the number from a date string 
//preg_match("/(?P<year>\d{4})-(?P<month>\d{2})-(?P<day>\d{2})/", "2012-10-20", $results)
    /**
     * Function to format date
     */
    public function preg_match_date($date) {
        $preg_ex = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'; //DATE_SQL
        if (preg_match($preg_ex, $date))
            return true;
        $preg_ex = '/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}$/'; //DATE_FORM
        if (preg_match($preg_ex, $date))
            return true;
        $preg_ex = '/^(0[1-9]|1[0-2])\/(0[1-9]|[1-2][0-9]|3[0-1])\/[0-9]{4}$/'; //DATE_US
        if (preg_match($preg_ex, $date))
            return true;
//        $preg_ex = '/(\d{2})\/(\d{2})\/(\d{4})$/';
//        if (preg_match($preg_ex, $date))
//            return true;
        return false;
    }

    /**
     * Function to ouput menu
     */
    public function outputMenu() {
        $msg = $this->createNewDoc(DB_MSG);
        $msg->setQueryFld(DOC_ID, $_SESSION['id_doc']);
        $act = $this->createNewDoc(DB_ACT);
        $act->setQueryFld(DOC_ID, $_SESSION['id_doc']);
        $prepare = $this->createNewDoc(DB_PREPARE);
        $prepare->setQueryFld(DOC_ID, $_SESSION['id_doc']);
        $review = $this->createNewDoc(DB_REVIEW);
        $review->setQueryFld(DOC_ID, $_SESSION['id_doc']);
        $penalty = $this->createNewDoc(DB_PENALTY);
        $penalty->setQueryFld(DOC_ID, $_SESSION['id_doc']);
        $termination = $this->createNewDoc(DB_TERMINATION);
        $termination->setQueryFld(DOC_ID, $_SESSION['id_doc']);
        $inform = $this->createNewDoc(DB_INFORM);
        $inform->setQueryFld(DOC_ID, $_SESSION['id_doc']);
        echo '<div id="mainmenu">
<ul>
<li><a href = "msg_input.php">Сообщение</a></li>
<li><a href = "act_input.php">Акт</a></li>';
        if ($msg->isFound() || $act->isFound()) {
            echo '<li><a href = "prepare_input.php">Подготовка </a></li>';
            if ($prepare->isFound()) {
                if ($prepare->isEmptyField(STATUTE_NUM)) {
                    echo '<li><a href = "review_input.php">Направление на рассм.</a></li>';
                    if ($review->isFound()) {
                        if ($penalty->isFound()) {
                            echo '<li><a href = "penalty_input.php">Взыскание</a></li>';
                            echo '<li><a href = "inform_input.php">Исполнение</a></li>';
                        } else {
                            if ($termination->isFound()) {
                                echo '<li><a href = "termination_input.php">Прекращение</a></li>';
                            } else {
                                echo '<li><a href = "#">Рассмотрение</a>';
                                echo '<ul><li><a href = "penalty_input.php">Взыскание</a></li>';
                                echo '<li><a href = "termination_input.php">Прекращение</a></li></ul></li>';
                            }
                        }
                    }
                } else
                    echo '<li><a href = "inform_input.php">Исполнение</a></li>';
            }
        }
        if ($_SESSION['role'] == '1') {
            echo '<li><a href = "admin_work.php">Назад</a></li>';
        } else {
            echo '<li><a href = "inspector_work.php">Назад</a></li>';
        }
        echo '<li><a href="logout.php">Выход</a></li>
            </ul>
        </div>'; //Конец блока #mainmenu  
    }

    /**
     * Function to ouput menu
     */
    public function setControlDate() {
        $frm_name = $this->view->getFrmName();
        $array = [];
        $array['id'] = $_SESSION['id_doc'];
//get keys of columns with name "date"
        $array['date_col_name'] = $this->getDateColName();
//get holidays and weekends
        $array['days_off']['weekend_days'] = $this->getWeekendDays();
        $array['days_off']['working_days'] = $this->getWorkingDays();
        $array['days_off']['holidays'] = $this->getHolidays();
        switch ($frm_name) {
            case FRM_PREPARE:
                $msg = $this->createNewDoc(DB_MSG);
                $msg->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $act = $this->createNewDoc(DB_ACT);
                $act->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $array['control_date'] = $this->getControlDate();
                $array['interval']['type'] = 'day';
                $array['interval']['val'] = 10;
                $array['next'] = 0;
                break;
            case FRM_REVIEW:
                $prepare = $this->createNewDoc(DB_PREPARE);
                $prepare->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $array['control_date'] = $this->getPrepareDate();
                $array['interval']['type'] = 'day';
                $array['interval']['val'] = 5;
                $array['next'] = 0;
                break;
            case FRM_PENALTY:
                $act = $this->createNewDoc(DB_ACT);
                $act->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $review = $this->createNewDoc(DB_REVIEW);
                $review->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $array['control_date'] = $this->getDate(DB_REVIEW, 'in_date');
                $array['interval']['type'] = 'day';
                $array['interval']['val'] = 15;
                $act_date = $this->getDate(DB_ACT, 'act_date');
                if (empty($act_date))
                    $array['next'] = 0;
                else {
                    $array['next'] = 1;
                    $array['next_control_date'] = $act_date;
                    $array['next_interval']['type'] = 'month';
                    $array['next_interval']['val'] = 2;
                }
                break;
            case FRM_TERMINATION:
                $review = $this->createNewDoc(DB_REVIEW);
                $review->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $array['control_date'] = $this->getDate(DB_REVIEW, 'in_date');
                $array['interval']['type'] = 'day';
                $array['interval']['val'] = 15;
                $array['next'] = 0;
                break;
            case FRM_INFORM:
                $review = $this->createNewDoc(DB_REVIEW);
                $review->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $penalty = $this->createNewDoc(DB_PENALTY);
                $penalty->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                $record = $penalty->selectRecord();
                $array['control_date'] = $this->getDate(DB_PENALTY, 'force_date');
                $array['interval']['type'] = 'day';
                $array['interval']['val'] = ($record['nsi_5'] == '2') ?
                        cal_days_in_month(
                                CAL_GREGORIAN, $this->getMonth($array['control_date'], DATE_US), $this->getYear($array['control_date'], DATE_US)
                        ) :
                        15;
                $array['next'] = 0;
                break;
        }
        return $array;
    }

    /**
     * selectedListArray
     * @param type $selected
     */
    public function selectedListArray($arr, $selected) {
        $stR = '';
        foreach ($arr as $key => $value) {
            if ($key == $selected) {
                $stR .= "<option selected='selected' value='" . $key . "'>" . $value . "</option>";
            } else {
                $stR .= "<option value='" . $key . "'>" . $value . "</option>";
            }
        }
        return $stR;
    }

    /**
     * Function 
     */
    public function selectedListInspection($selected) {
        // initialize inspection options array
        $row = $this->getDB()->selectInspection();
        if (count($row) > 1) {
            $value = "0";
            $name = "--Выбрать--";
            if ($value == $selected) {
                echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
            } else {
                echo "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
        foreach ($row as $item) {
            $value = $item['id'];
            $name = $item['name_i'];
            if ($value == $selected) {
                echo '<option value="' . $value .
                '" selected="selected">' . $name . '</option>';
            } else {
                if (!strncmp($name, 'БРЕСТ', 5)) {
                    echo '<optgroup label="Брест">';
                } else
                if (!strncmp($name, 'БАРАНОВ', 7)) {
                    echo '<optgroup label="Барановичи">';
                } else
                if (!strncmp($name, 'ПИНСК', 5)) {
                    echo '<optgroup label="Пинск">';
                }
                echo '<option value="' . $value . '">' . $name . '</option>';
            }
        }
    }

    /**
     * Function 
     */
    public function selectedListDocs($options, $selected) {
        $row = $this->getDB()->selectUnion($options);
        $value = "0";
        $name = "--Выбрать--";
        if ($value == $selected) {
            echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
        } else {
            echo "<option value='" . $value . "'>" . $name . "</option>";
        }
        foreach ($row as $item) {
            $value = $item['doc_id'];
            $name = $item['to_who'];
            if ($value == $selected) {
                echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
            } else {
                echo "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
    }

    /**
     * Function 
     */
    public function convertToFIO($fio) {
        list($f, $i, $o) = preg_split("/[' '.]+/", $fio);
        $result = $f;
        $result .= ' ' . mb_substr($i, 0, 1) . '.';
        $result .= mb_substr($o, 0, 1) . '.';
        return $result;
    }

}

class Doc {

    private $db,
            $record,
            $error_msg,
            $name_tbl,
            $name_fld,
            $value_fld,
            $name_fld2,
            $value_fld2,
            $name_fld3,
            $value_fld3,
            $sql_query;
    public $instControl;

    function __construct() {
        $this->name_tbl = EMPT_Y;
        $this->name_fld = EMPT_Y;
        $this->value_fld = EMPT_Y;
        $this->name_fld2 = EMPT_Y;
        $this->value_fld2 = EMPT_Y;
        $this->name_fld3 = EMPT_Y;
        $this->value_fld3 = EMPT_Y;
        $this->sql_query = EMPT_Y;
    }

    function create($name) {
        $json = $this->getJson();
        $this->name_tbl = $name;
        $this->record = json_decode($json[$name]);
        $this->clearValueRecord();
        $this->error_msg = json_decode($json[$name]);
    }

    /**
     * Function to clear value of record
     */
    public function clearValueRecord() {
        $this->record = $this->clearValue($this->record);
    }

    public function setDB($db) {
        $this->db = $db;
    }

    public function setQuery($sql_query) {
        $this->sql_query = $sql_query;
    }

    public function execQuery() {
        $this->getDB()->setQuery($this->sql_query);
        return $this->getDB()->execQuery();
    }

    public function isExecQuery() {
        $this->getDB()->setQuery($this->sql_query);
        return $this->getDB()->isExecQuery();
    }

    public function getDB() {
        return $this->instControl->getDB();
    }

    public function getView() {
        return $this->instControl->getView();
    }

    public function getJson() {
        return $this->instControl->getJson();
    }

    public function getNameTbl() {
        return $this->name_tbl;
    }

    public function setQueryFld($name_fld, $value_fld) {
        $this->name_fld = $name_fld;
        $this->value_fld = $value_fld;
    }

    public function setQueryFld2($name_fld, $value_fld) {
        $this->name_fld2 = $name_fld;
        $this->value_fld2 = $value_fld;
    }

    public function setQueryFld3($name_fld, $value_fld) {
        $this->name_fld3 = $name_fld;
        $this->value_fld3 = $value_fld;
    }

    /**
     * Function to get error messages  
     */
    public function getErrorMsgRecord() {
        return $this->error_msg;
    }

    /**
     * Function to set error messages  
     */
    public function setErrorMsgRecord($err_msg) {
        $this->error_msg = $err_msg;
    }

    /**
     * Function 
     */
    public function setFieldValueRecord($field, $val) {
        $record = (array) $this->record;
        foreach ($record as $key => $vaue) {
            if ($key == $field)
                $record[$key] = $val;
        }
        $this->record = (object) $record;
    }

    /**
     * Function to select a record
     */
    public function selectRecord() {
        $this->sql_query = "SELECT * FROM " . $this->name_tbl;
        if ($this->name_fld != EMPT_Y) {
            $this->sql_query .= " WHERE " . $this->name_fld . "='$this->value_fld'";
            if ($this->name_fld2 != EMPT_Y) {
                $this->sql_query .= " AND " . $this->name_fld2 . "='$this->value_fld2'";
                if ($this->name_fld3 != EMPT_Y) {
                    $this->sql_query .= " AND " . $this->name_fld3 . "='$this->value_fld3'";
                }
            }
        }
        $row = $this->execQuery();
        $this->setRecord((object) $row[0]);
        return $row[0];
    }

    /**
     * Function to select  records
     */
    public function selRecordS() {
        $this->sql_query = "SELECT * FROM " . $this->name_tbl;
        if ($this->name_fld != EMPT_Y) {
            $this->sql_query .= " WHERE " . $this->name_fld . "='$this->value_fld'";
            if ($this->name_fld2 != EMPT_Y) {
                $this->sql_query .= " AND " . $this->name_fld2 . "='$this->value_fld2'";
                if ($this->name_fld3 != EMPT_Y) {
                    $this->sql_query .= " AND " . $this->name_fld3 . "='$this->value_fld3'";
                }
            }
        }
        $row = $this->execQuery();
        return $row;
    }

    /**
     * Function to insert a record 
     */
    public function insertRecord() {
        $record = (array) $this->record;
//         var_dump($this->record);
        $this->sql_query = "INSERT INTO " . $this->name_tbl;
        $this->sql_query .= " (`" . implode("`, `", array_keys($record)) . "`)";
        $this->sql_query .= " VALUES ('" . implode("', '", $record) . "') ";
//        var_dump($this->sql_query);

        return $this->isExecQuery();
    }

    /**
     * Function to update a record
     */
    public function updateRecord($name_fld, $value_fld) {
        $this->sql_query = "UPDATE " . $this->name_tbl
                . " SET " . $name_fld . "='$value_fld'"
                . " WHERE " . $this->name_fld . "='$this->value_fld'";
        return $this->isExecQuery();
    }

    /**
     * Function to delete a record   
     */
    public function deleteRecord() {
//convert to stdclass object
        $this->sql_query = "DELETE FROM " . $this->name_tbl;
        if ($this->name_fld != EMPT_Y) {
            $this->sql_query .= " WHERE " . $this->name_fld . "='$this->value_fld'";
            if ($this->name_fld2 != EMPT_Y) {
                $this->sql_query .= " AND " . $this->name_fld2 . "='$this->value_fld2'";
                if ($this->name_fld3 != EMPT_Y) {
                    $this->sql_query .= " AND " . $this->name_fld3 . "='$this->value_fld3'";
                }
            }
        }
        //     var_dump($this->sql_query);
        return $this->isExecQuery();
    }

    /**
     * Function to delete  unmatched records   
     */
    public function deleteUnmatchedItem() {
//convert to stdclass object
        $this->sql_query = "DELETE FROM " . $this->name_tbl
                . " WHERE " . DOC_ID
                . " NOT IN "
                . " (SELECT " . ID . " FROM " . DB_BOOK
                . " )";
        return $this->isExecQuery();
    }

    /**
     * Function to find  record   
     */
    public function isFound() {
        $this->sql_query = "SELECT * FROM " . $this->name_tbl .
                " WHERE " . $this->name_fld . "='$this->value_fld' LIMIT 1";
        $row = $this->execQuery();
        return (count($row) != 0) ? true : false;
    }

    /**
     * Function to clear value of object
     */
    public function clearValue($var) {
        $arr = (is_array($var)) ? $var : (array) $var;
        foreach ($arr as $key => $vaue) {
            $arr[$key] = '';
        }
        return (is_array($var)) ? $arr : (object) $arr;
    }

    /**
     * Function to set record  
     */
    public function setRecord($record) {
        $this->record = $record;
    }

    /**
     * Function to set record  
     */
    public function setRecordAsView() {
        $record = $this->getView()->getRecord();
        foreach ($record as $key => $vaue) {
            $this->setFieldValueRecord($key, $record[$key]);
        }
    }

    /**
     * Function to set record  
     */
    public function getRecord() {
        return $this->record;
    }

    /**
     * Function 
     */
    public function isEmptyField($field_name) {
        $arr = ($this->isFound()) ? $this->selectRecord() : [];
        if (count($arr) != 0) {
            $res = ($arr[$field_name] == NULL) ? EMPT_Y : $arr[$field_name];
            $res = ($arr[$field_name] == '0') ? EMPT_Y : $arr[$field_name];
        } else {
            $res = EMPT_Y;
        }
        return ($res == EMPT_Y);
    }

    /**
     * Function 
     */
    public function getFieldValue($field_name) {
        $row = ($this->isFound()) ? $this->selectRecord() : [];
        $res = (count($row) != 0) ? $row[$field_name] : EMPT_Y;
        return $res;
    }

    /**
     * Function to  get error message
     */
    public function getErrorMsgField($field_name) {
        $arr = (array) $this->error_msg;
        return $arr[$field_name];
    }

}

/**
 * Class to work with form  
 */
class View {

    private $doc_name,
            $frm_name,
            $record, //output field 
            $error_msg; //err messges
    public $instControl;

    /**
     * constructor
     */
    function __construct() {
        
    }

    /**
     * Function 
     */
    function create($name) {
        $json = $this->getJson();
        $this->frm_name = $name;
        $this->record = (array) json_decode($json[$name]);
        $this->clearValueRecord();
        $this->error_msg = (array) $this->record;
    }

    /**
     * Function 
     */
    public function setDocName($doc_name) {
        $this->doc_name = $doc_name;
    }

    /**
     * Function 
     */
    public function getDoc() {
        return $this->instControl->getDoc($this->doc_name);
    }

    /**
     * Function 
     */
    public function getDocName() {
        return $this->doc_name;
    }

    /**
     * Function 
     */
    public function getFrmName() {
        return $this->frm_name;
    }

    /**
     * Function 
     */
    public function getJson() {
        return $this->instControl->getJson();
    }

    /**
     * Function 
     */
    public function selectRecord() {
        $record = (array) $this->getDoc()->selectRecord();
        foreach ($this->record as $key => $value) {
            $this->setFieldValueRecord($key, $record[$key]);
        }
        $this->formatAllDatesRecord(DATE_FORM); //format date  to the sql format date
        $this->formatSumma(1);
    }

    /**
     * Function 
     */
    public function setFieldValueRecord($field_name, $val) {
        $this->record[$field_name] = $val;
    }

    /**
     * Function 
     */
    public function deleteDoc() {
        $this->getDoc()->deleteRecord();
    }

    /**
     *  Function to format all dates columns in an record
     */
    public function formatAllDatesRecord($formatS) {
        $arr = [];
        foreach ($this->record as $key => $value) {
            $pos = strripos($key, '_'); //search '_' in the string
            if ($pos) {
                $arr = preg_split("/[\s,_]+/", $key);
                if ($arr[1] == 'date') {
                    $this->record[$key] = $this->instControl->formatDate($this->record[$key], $formatS);
                }
            }
        }
    }

    /**
     *  Function to check  date format
     */
    public function isCorrectDateView($date) {
        return $this->instControl->preg_match_date($date);
    }

    /**
     *  Function to check  date 
     */
    public function isDate($key) {
        $arr = [];
        $pos = strripos($key, '_'); //search '_' in the string
        if ($pos) {
            $arr = preg_split("/[\s,_]+/", $key);
            if ($arr[1] == 'date') {
                return true;
            }
        }
        return false;
    }

    /**
     * Function to format summa
     */
    public function formatSumma($n) {
        if (isset($this->record['summa'])) {
            if ($n == 1) {
                $number = floatval($this->record['summa']);
                $this->record['summa'] = number_format($number, 2, '.', ' ');
            } else {
                $this->record['summa'] = str_replace(" ", "", $this->record['summa']);
            }
        }
    }

    /**
     * Function to clear value of record
     */
    public function clearValueRecord() {
        $this->record = $this->clearValue($this->record);
    }

    /**
     * Function to clear value of object
     */
    public function clearValue($var) {
        $arr = (is_array($var)) ? $var : (array) $var;
        foreach ($arr as $key => $vaue) {
            $arr[$key] = '';
        }
        return (is_array($var)) ? $arr : (object) $arr;
    }

    /**
     * Function to get formatting  record  
     */
    public function getFormatedRecord() {
        $this->formatAllDatesRecord(DATE_SQL); //format date  to the sql format date
        $this->formatSumma(0);
        return $this->record;
    }

    /**
     * Function to set record  
     */
    public function pushRecord() {
        $this->formatAllDatesRecord(DATE_SQL); //format date  to the sql format date
        $this->formatSumma(0);
        $this->getDoc()->setRecordAsView();
        $this->formatAllDatesRecord(DATE_FORM); //format date  to the form format date
        $this->formatSumma(1);
    }

    /**
     * Function to get record  
     */
    public function getRecord() {
        return $this->record;
    }

    /**
     * Function to get connection to DB  
     */
    public function getDB() {
        return $this->instControl->getDB();
    }

    /**
     * Function to get error messages  
     */
    public function getErrorMsg() {
        return $this->error_msg;
    }

    /**
     * Function to set error messages  
     */
    public function setErrorMsg($err_msg) {
        $this->error_msg = (array) $err_msg;
    }

    /**
     * Function 
     */
    public function setErrorMsgField($field) {
        $array = (array) $this->getDoc()->getErrorMsgRecord();
        $arr = $this->error_msg;
        foreach ($arr as $key => $vaue) {
            if ($key == $field) {
                $arr[$key] = isset($array[$key]) ? $array[$key] : '';
            }
        }
        $this->error_msg = $arr;
    }

    /**
     * Function 
     */
    public function setFieldValueError($field_name, $val) {
        $this->error_msg[$field_name] = $val;
    }

    /**
     * Function 
     */
    public function isFoundDoc() {
        return $this->getDoc()->isFound();
    }

    /**
     * Function 
     */
    public function isError() {
        $arr = $this->error_msg;
        return !$this->isEmpty($arr);
    }

    /**
     * Function 
     */
    public function isEmpty($arr) {
        foreach ($arr as $key => $vaue) {
            if (!empty($arr[$key]))
                return false;
        }
        return true;
    }

    /**
     * Function 
     */
    public function isSuccess() {
        if ($this->isError()) {
            return false;
        }
        $record = (object) $this->getRecord();
        $error_message = [];
        switch ($this->frm_name) {
            case FRM_MSG:
                $error_message[TO_WHO] = (!preg_match(CYRILLIC_SEQUENCE, $record->to_who)) ?
                        CYRILLIC : EMPT_Y;
                break;
            case FRM_ACT:
            case FRM_PREPARE:
            case FRM_REVIEW:
            case FRM_PENALTY:
            case FRM_TERMINATION:
            case FRM_INFORM:
                break;
            case FRM_LOGIN:
                $error_message[NAME] = (!preg_match(LATIN_NUMBER_SEQUENCE, $record->name)) ?
                        LATIN_NUMBER : EMPT_Y;
                if (empty($error_message[NAME])) {
                    $this->getDoc()->setQueryFld(NAME, $record->name);
                    $error_message[NAME] = !$this->isFoundDoc() ?
                            USER_NOT_FOUND : EMPT_Y;
                    if (empty($error_message[NAME])) {
                        $error_message[PASSWORD] = (!preg_match(LATIN_NUMBER_SEQUENCE, $record->password)) ?
                                LATIN_NUMBER : (!$this->getDB()->getPassword($record->name, $record->password) ?
                                        WRONG_PASSWORD : EMPT_Y);
                    }
                }
                break;
            case FRM_NEW_USER:
                $error_message[FIO] = (!preg_match(CYRILLIC_SEQUENCE, $record->fio)) ?
                        CYRILLIC : EMPT_Y;
                $error_message[PASSWORD] = (strcmp($record->password, $record->pass2) != 0) ?
                        PASSWORDS_NOT_MATCH : EMPT_Y;
                $error_message[NAME] = (!preg_match(LATIN_NUMBER_SEQUENCE, $record->name)) ?
                        LATIN_NUMBER : EMPT_Y;
                if (empty($error_message[NAME])) {
                    $this->getDoc()->setQueryFld(NAME, $record->name);
                    $error_message[NAME] = $this->isFoundDoc() ?
                            NAME_IS_IN_DATABASE : EMPT_Y;
                }
                break;
        }
        foreach ($error_message as $key => $value) {
            $this->setFieldValueError($key, $value);
        }
        return $this->isEmpty($error_message);
    }

    /**
     * Function 
     */
    public function insertDoc() {
        $res = $this->getDoc()->insertRecord();
        return $res;
    }

    /**
     * Function 
     */
    public function POST() {
        $record = $this->getRecord();
        foreach ($record as $key => $vaue) {
            if (isset($_POST[$key])) {
                $val = $this->getHidenValue($key);
                $this->setFieldValueRecord($key, $val);
                if (empty($val) && $val != '0') {
                    $this->setErrorMsgField($key);
                } else if ($this->isDate($key)) {
                    if (!$this->isCorrectDateView($val)) {
                        $this->setFieldValueError($key, 'Недопустимый формат даты');
                    }
                }
            } else {
                $this->setErrorMsgField($key); //checkbox or radiobutoon
            }
        }
    }

    /**
     * Function to get hiden feld
     */
    public function isHiden($name_fld) {
        $result = false;
        switch ($this->doc_name) {
            case DB_PENALTY:
            case DB_TERMINATION:
                $row = $this->getDB()->selectHiden($this->doc_name, $name_fld);
                $result = (count($row) > 0);
                break;
        }
        return $result;
    }

    /**
     * Function to get hiden value
     */
    public function getHidenValue($name_fld) {
        if ($this->isHiden($name_fld)) {
            $row = (object) $this->getDB()->selectHiden($this->doc_name, $name_fld);
            $result = ($this->testValue($_POST[$row->name_fld1]) == $row->val1) ?
                    (($row->hiden != '0') ? $row->val2 : $this->testValue($_POST[$row->val2])) :
                    $this->testValue($_POST[$name_fld]);
        } else {
            $result = $this->testValue($_POST[$name_fld]);
        }
        return $result;
    }

    /**
     * Function to test value  
     */
    public function testValue($val) {
        return $this->instControl->test_input($val);
    }

}

class LoadThread extends Thread {

    protected $cond, $role;

    public function run() {
        /* do this in a loop to handle spurious wakeups */
        session_start();
        if (!isset($_SESSION['id'])) {
            exit;
        }
        $ctrl = new Control();
        $ctrl->setUser();
        $this->role = $ctrl->getUserRole();
        while ($this->synchronized(function() {
            /* if predicate not satisfied then wait */
            if (!$this->cond) {
                return $this->wait();
            }
            return false;
        }));

//        echo "Thread!\n";
    }

    public function getUserRole() {
        return $this->role;
    }

}

?>