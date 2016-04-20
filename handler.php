<?php

session_start();
if (!isset($_SESSION['id'])) {
    exit;
} else {
    require_once ('model.php');
    $_SESSION[FRM_PREVIOUS] = $_SESSION[FRM_CURRENT];
    $_SESSION[FRM_CURRENT] = FRM_ACT;
}



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$response_str = '{
          "head": "",
          "data":"",
          "success": "",
           "msg":""
     }';
//convert to stdclass object
$response = json_decode($response_str);

$ctrl = new Control();
$ctrl->setUser();

$book = $ctrl->createNewDoc(DB_BOOK);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
//    $db = new DB_Connect(); //connect to database\F
    if ($ctrl->getDB()) {
        $response->success = 'yes';
    } else {
        $responce->success = 'no';
        $response->msg = 'Не могу подключиться к базе данных';
        return;
    }
    $json_str = $_POST["json_str"];
//convert to stdclass object
    $json_obj = json_decode($json_str);
    $data = json_decode($json_obj->data);
    $response->head = $json_obj->head;
    switch ($json_obj->head) {
        case H_DISABLED_ENABLED:
            $prepare = $ctrl->createNewDoc(DB_PREPARE);
            $prepare->setQueryFld(DOC_ID, $_SESSION['id_doc']);
            $review = $ctrl->createNewDoc(DB_REVIEW);
            $review->setQueryFld(DOC_ID, $_SESSION['id_doc']);
            $penalty = $ctrl->createNewDoc(DB_PENALTY);
            $penalty->setQueryFld(DOC_ID, $_SESSION['id_doc']);
            $termination = $ctrl->createNewDoc(DB_TERMINATION);
            $termination->setQueryFld(DOC_ID, $_SESSION['id_doc']);
            $inform = $ctrl->createNewDoc(DB_INFORM);
            $inform->setQueryFld(DOC_ID, $_SESSION['id_doc']);
            switch ($data->name_tbl) {
                case DB_MSG:
                case DB_ACT:
                    $res = $prepare->isFound();
                    break;
                case DB_PREPARE:
                    $res = $review->isFound();
                    if (!$res) {
                        $res = $inform->isFound();
                    }
                    break;
                case DB_REVIEW:
                    $res = $penalty->isFound();
                    if (!$res) {
                        $res = $termination->isFound();
                    }
                    break;
                case DB_TERMINATION:
                case DB_PENALTY:
                    $res = $inform->isFound();
                    break;
            }
            if ($res) {
                $response->data = 'disabled';
            } else {
                $response->data = 'enabled';
            }
            break;
        case H_HTML:
            $book = $ctrl->createNewDoc(DB_BOOK);
            $book->setQueryFld(ID, $data->val);
            if ($book->isFound()) {
                $row = $book->selectRecord();
            }
            $array = [];
            $array['reg_num'] = $row['reg_num'];
            $ctrl->getDB()->setDocId($data->val);
            $array['html'] = $ctrl->getDB()->getData($data->name_fld);
            $response->data = json_encode($array);
            break;
        case H_DELETE:
            $array = array();
            $book = $ctrl->createNewDoc(DB_BOOK);
            $book->setQueryFld(ID, $_SESSION['id_doc']);
            $status = $book->getFieldValue(STATE);
            $del = false;
            $process_tbl = true;
            $array['name_tbl'] = $data->name_tbl;
            switch ($data->name_tbl) {
                case DB_MSG:
                    if ($status == STATE_ACT || $status == STATE_MSG) {
                        $del = true;
                        $status = ($status == STATE_ACT) ? $status : STATE_NULL;
                    }
                    break;
                case DB_ACT:
                    if ($status == STATE_ACT) {
                        $msg = $ctrl->createNewDoc(DB_MSG);
                        $msg->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                        $del = true;
                        $status = $msg->isFound() ? STATE_MSG : STATE_NULL;
                    }
                    break;
                case DB_PREPARE:
                    if ($status == STATE_PREPARE) {
                        $act = $ctrl->createNewDoc(DB_ACT);
                        $act->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                        $prtcl = $ctrl->createNewDoc(DB_PROTOCOL);
                        $prtcl->setQueryFld($data->name_fld, $data->val);
                        $del = $prtcl->deleteRecord(); //delete doc .
                        $status = $act->isFound() ? STATE_ACT : STATE_MSG;
                    }
                    break;
                case DB_REVIEW:
                    if ($status == STATE_REVIEW) {
                        $del = true;
                        $status = STATE_PREPARE;
                    }
                    break;
                case DB_PENALTY:
                    if ($status == STATE_PENALTY) {
                        $del = true;
                        $status = STATE_REVIEW;
                    }
                    break;
                case DB_TERMINATION:
                    if ($status == STATE_TERMINATION) {
                        $del = true;
                        $status = STATE_REVIEW;
                    }
                    break;
                case DB_INFORM:
                    if ($status == STATE_INFORM) {
                        $penalty = $ctrl->createNewDoc(DB_PENALTY);
                        $penalty->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                        $termination = $ctrl->createNewDoc(DB_TERMINATION);
                        $termination->setQueryFld(DOC_ID, $_SESSION['id_doc']);
                        $del = true;
                        $status = $penalty->isFound() ? STATE_PENALTY :
                                ($termination->isFound() ? STATE_TERMINATION : STATE_PREPARE);
                    }
                    break;
                case DB_INSPECTOR:
                    $del = true;
                    $process_tbl = false;
                    $_SESSION['pageXOffset'] = $data->pageXOffset;
                    $_SESSION['pageYOffset'] = $data->pageYOffset;
                    break;
            }
            if ($del) {
                $tbl = $ctrl->createNewDoc($data->name_tbl);
                $tbl->setQueryFld($data->name_fld, $data->val);
                $result = $tbl->deleteRecord(); //delete doc .
                if ($result && $process_tbl) {
                    $book->updateRecord(STATE, $status);
                    $array['error'] = EMPT_Y;
                } else {
                    $array['error'] = $result ? EMPT_Y : FAILURE_DELETE;
                }
            }
            $response->data = json_encode($array);
            break;
        case H_CHECKED:
            $row = $ctrl->getDB()->selectUser($data->val);
            $array = array();
            foreach ($row as $item) {
                $array['status'] = $item['status'];
                $array['role'] = $item['role'];
                if (empty($item['access']))
                    $array['access'] = 0;
                else
                    $array['access'] = 1;
            }
            $response->data = json_encode($array);
            break;

        case H_SAVE_REPORT:
            $user = (object) $ctrl->getUser();
            $record = $data->val;
            $record->mro = $user->mro;
            $mon = $ctrl->getMonthNum($record->rep_date);
            if ($mon != 0 && $record->mro != 4) {
                $mon = $mon < 10 ? '0' . $mon : $mon;
                $year = $_SESSION['year'];
                $record->rep_date = $ctrl->formatDate($year . '-' . $mon . '-01', DATE_SQL);

                $report = $ctrl->createNewDoc(DB_REPORT);


                $list_report = $ctrl->createNewDoc(DB_LIST_REPORT);
                $list_report->setQueryFld(BASE_NAME, $report->getNameTbl());
                $id = $list_report->getFieldValue(ID);

                $list_row = $ctrl->createNewDoc(DB_LIST_ROW);
                $list_row->setQueryFld(REP_ID, $id); //id of report
                $list_row->setQueryFld2(NAME, $record->row_id); //name of row
                $record->row_id = $list_row->getFieldValue(ID);

                $report->setQueryFld(MRO, $record->mro);
                $report->setQueryFld2(ROW_ID, $record->row_id);
                $report->setQueryFld3(REP_DATE, $record->rep_date);
                $report->deleteRecord();
                $report->setRecord($record);
                $report->insertRecord();
                $response->data = SUCCESS;
            } else {
                if ($record->mro == 4) {
                    $response->data = 'Не выбран  МРО';
                } else {
                    $response->data = 'Не выбран месяц отчета!';
                }
            }
            break;
        case H_SAVE_NEW_REPORT:
            $user = (object) $ctrl->getUser();
            $record = $data->val;
            $record->mro = $user->mro;
            $mon = $ctrl->getMonthNum($record->rep_date);
            if ($mon != 0 && $record->mro != 4) {
                $mon = $mon < 10 ? '0' . $mon : $mon;
                $year = $_SESSION['year'];
                $record->rep_date = $ctrl->formatDate($year . '-' . $mon . '-01', DATE_SQL);

                $report = $ctrl->createNewDoc(DB_REP);


                $list_report = $ctrl->createNewDoc(DB_LIST_REPORT);
                $list_report->setQueryFld(BASE_NAME, $record->name);
                $id = $list_report->getFieldValue(ID);

                $list_row = $ctrl->createNewDoc(DB_LIST_ROW);
                $list_row->setQueryFld(REP_ID, $id); //id of report
                $list_row->setQueryFld2(NAME, $record->row_id); //name of row
                $record->row_id = $list_row->getFieldValue(ID);

                $report->setQueryFld(MRO, $record->mro);
                $report->setQueryFld2(ROW_ID, $record->row_id);
                $report->setQueryFld3(REP_DATE, $record->rep_date);
                $report->deleteRecord();
                $new_record = $report->getRecord();
                $new_record->mro = $record->mro;
                $new_record->row_id = $record->row_id;
                $new_record->rep_date = $record->rep_date;
                foreach ($record as $key => $value) {
                    if ($key != 'mro' && $key != 'rep_date' && $key != 'row_id'&& $key != 'name') {
                        $new_record->name_fld = $key;
                        $new_record->summa = $value;
                        $report->setRecord($new_record);
                        $report->insertRecord();
                    }
                }
                $response->data = SUCCESS;
            } else {
                if ($record->mro == 4) {
                    $response->data = 'Не выбран  МРО';
                } else {
                    $response->data = 'Не выбран месяц отчета!';
                }
            }

            break;

        case H_SET_ORDER:
            $record = $data->val;
            $_SESSION['order'] = $record->order;
            $response->data = 'SET_ORDER';
            break;




        case H_UPDATE_USER:
            $record = $data->val;
            $button = $data->button;
            $_SESSION['pageXOffset'] = $data->pageXOffset;
            $_SESSION['pageYOffset'] = $data->pageYOffset;
            if ($button == BTN_SAVE) {
                $ctrl->getDB()->setRecord($record);
                $result = $ctrl->getDB()->updateUser(); //update .
            }
            $response->data = EMPT_Y;

            break;
        case H_MENU_CHANGED:
            $_SESSION['id_menu'] = $data->val;
            if ($_SESSION['id_menu'] == 10 || $_SESSION['id_menu'] == 12) {
                $_SESSION['frm'] = 1;
            } else if ($_SESSION['id_menu'] == 9 || $_SESSION['id_menu'] == 11) {
                $_SESSION['frm'] = 2;
            }
            $_SESSION['mon1'] = 1;
            $_SESSION['mon2'] = 12;
            $_SESSION['pageXOffset'] = 0;
            $_SESSION['pageYOffset'] = 0;
            if ($_SESSION['id_menu'] == 11 || $_SESSION['id_menu'] == 12) {
                $_SESSION['quarter'] = 1;
            } else {
                $_SESSION['quarter'] = 0;
            }


            $response->data = SUCCESS;
            break;
        case H_MONTH_DATA:
            //$_SESSION['id_menu'] = $data->val;
            $_SESSION['quarter'] = 0;
            $_SESSION['mon1'] = (int) $data->mon1;
            $_SESSION['mon2'] = (int) $data->mon2;
            switch ($data->val) {
                case '1':
                    if ($_SESSION['year'] == 0) {
                        $_SESSION['year'] = (int) date('Y');
                    }
                    break;
                case '2':
                    $_SESSION['year'] = 0;
                    break;
                case '3':
                    $_SESSION['year'] = (int) $data->year;
                    break;
            }

            if ($_SESSION['mon1'] == 0 && $_SESSION['mon2'] == 0) {
                $_SESSION['year'] = 0;
            } else if (!empty($data->year)) {
                $_SESSION['year'] = $data->year;
            }
            $response->data = json_encode($_SESSION);
            break;
        case H_MRO_CHANGED:
            $_SESSION['mro'] = $data->val;
            $response->data = json_encode($_SESSION);
            break;
        case H_QUARTER_CHANGED:
            $_SESSION['quarter'] = $data->val;
            $response->data = SUCCESS;
            break;
        case H_SET_LIST:
            if ($data->val != '17') {
                $mro = $ctrl->createNewDoc(DB_MRO);
                $role = $ctrl->getUserRole();
                $mro->setQueryFld(ID_INSPECTION, $data->val);
                if ($mro->isFound()) {
                    if ($role == R_ADMIN) {
                        $row = $mro->selectRecord();
                        $ctrl->setUserRole(R_ADMIN_MRO);
                        $ctrl->setUserMRO($row['id']);
                    }
                } else {
                    if ($role == R_ADMIN || $role == R_ADMIN_MRO) {
                        $ctrl->setUserRole(R_ADMIN_INSPECTION);
                        $ctrl->setUserInspection($data->val);
                    }
                }
            }
            $row = [];
            $row['data'] = ($data->name_fld == 'edit') ? $ctrl->getDB()->selectUnion('1') :
                    $ctrl->getDB()->selectUnion('0');
            $row['name_fld'] = $data->name_fld;
            $response->data = json_encode($row);
            //$response->msg = 'Незарегистрированных дел нет.';
            break;
        case H_SAVE_REG_NUM:
            $record = $data->val;
            $_SESSION['id_doc'] = $data->doc_id;
            $_SESSION['pageXOffset'] = $record->pageXOffset;
            $_SESSION['pageYOffset'] = $record->pageYOffset;
            if ($record->button == BTN_SAVE) {
                $book = $ctrl->createNewDoc(DB_BOOK);
                $book->setQueryFld(ID, $data->doc_id);
                $data->reg_date = $ctrl->formatDate($data->reg_date, DATE_SQL);
                $book->updateRecord(REG_DATE, $data->reg_date);
                $book->updateRecord(REG_NUM, $data->reg_num);
            }
            $response->data = EMPT_Y;
            break;
        case H_ADD_PROTOCOL:
//            var_dump($data);
            if (!empty($data->protocol_date) && !empty($data->obj_fio)) {
                $prtcl = $ctrl->createNewDoc(DB_PROTOCOL);
                $data->protocol_date = $ctrl->formatDate($data->protocol_date, DATE_SQL);
                $prtcl->setRecord((object) $data);
                $prtcl->insertRecord();
            } else {
                $str_p = ($data->nsi_3 == '111') ? ' осмотра. ' : ' опроса. ';
                $str_obj_fio = ($data->nsi_3 == '111') ? ' название объекта осмотра' : ' ФИО опрашиваемого ';
                $response->data = empty($data->protocol_date) ? 'Введите дату протокола ' . $str_p :
                        (empty($data->obj_fio) ? 'Введите ' . $str_obj_fio : '');
            }
            break;
        case H_EDIT_PROTOCOL:
//            $response->data = 'Редактирование протокола';
            if (!empty($data->protocol_date) && !empty($data->obj_fio)) {
                $prtcl = $ctrl->createNewDoc(DB_PROTOCOL);
                $prtcl->setQueryFld(ID, $data->id);
                $data->protocol_date = $ctrl->formatDate($data->protocol_date, DATE_SQL);
                $prtcl->updateRecord(PROTOCOL_DATE, $data->protocol_date);
                $prtcl->updateRecord(OBJ_FIO, $data->obj_fio);
                $prtcl->updateRecord(NSI_3, $data->nsi_3);
            } else {
                $str_p = ($data->nsi_3 == '111') ? ' осмотра. ' : ' опроса. ';
                $str_obj_fio = ($data->nsi_3 == '111') ? ' название объекта осмотра' : ' ФИО опрашиваемого ';
                $response->data = empty($data->protocol_date) ? 'Введите дату протокола ' . $str_p :
                        (empty($data->obj_fio) ? 'Введите ' . $str_obj_fio : '');
            }
            break;
        case H_DELETE_PROTOCOL:
            $prtcl = $ctrl->createNewDoc(DB_PROTOCOL);
            $prtcl->setQueryFld(ID, $data->id);
            $result = $prtcl->deleteRecord();
//            $response->data = $result ? SUCCESS_DELETE : FAILURE_DELETE;
            $response->data = EMPT_Y;
            break;
        case H_GOTO:
            $record = $data->val;
            $_SESSION['id_doc'] = $record->doc_id;
            $_SESSION['pageXOffset'] = $record->pageXOffset;
            $_SESSION['pageYOffset'] = $record->pageYOffset;
            $response->data = EMPT_Y;
            break;
    }
//$response->msg = "Response";
    $str = json_encode($response);
    echo $str;
}


    