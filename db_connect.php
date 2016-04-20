<?php

/**
 * db_connect.php
 * @author ASK
 * On the trail of Peter Leow
 * 
 */
//require_once "db_config.php";
/**
 * Class to work with MySQL database
 */
class DB_Connect {

    private $con, //connection to data base
            $role, // users role in data base
            $access, //access to data base (read/write)
            $user_id, //users identificator
            $inspection, //inspections identificator
            $mro, //mro identificator
            $doc_id, //documents identificator
            $name_tbl, //table name
            $name_fld, //field name
            $value_fld, //field value
            $sql_query, //field value
            $record; //any tables record
    public $instControl;

    /**
     * constructor
     */
    function __construct() {
// connecting to database
        $this->con = $this->connect();
    }

    /**
     * Function to connect with database
     */
    private function connect() {
// import database connection variables
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        try {
            $myCon = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE, DB_USER, DB_PASSWORD, $options);
            $myCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $myCon->exec('SET NAMES "utf8"');
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
//All is OK!   
        return $myCon;
    }

    /**
     * Function to get the reference to a database
     */
    public function getDbConnection() {
        return $this->con;
    }

    /**
     * Function to set doc_id 
     */
    public function setDocId($doc_id) {
        $this->doc_id = $doc_id;
    }

    /**
     * Function to set name of table 
     */
    public function nameTbl($name_tbl) {
        $this->name_tbl = $name_tbl;
        $this->record = $this->instControl->getDoc($name_tbl)->getRecord();
        return $this;
    }

    /**
     * Function to set name of table 
     */
    public function setNameFld($name_fld) {
        $this->name_fld = $name_fld;
    }

    /**
     * Function to set name of table 
     */
    public function setValueFld($val) {
        $this->value_fld = $val;
    }

    /**
     * Function to set name of table 
     */
    public function setNameTbl($name_tbl) {
        $this->name_tbl = $name_tbl;
    }

    /**
     * Function to set record  
     */
    public function getRecord() {
        return $this->record;
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
    public function setQuery($sql_query) {
        $this->sql_query = $sql_query;
    }

    /**
     * Function to get hash password  from  MySQL database
     */
    public function getPassword($username, $password) {

        try {
            $sql_query = "SELECT password "
                    . " FROM user "
                    . " WHERE name ='$username' "
                    . ' LIMIT 1'
            ;
            $st = $this->con->prepare($sql_query);
            $st->execute();
            $user = $st->fetch(PDO::FETCH_OBJ);
// The entered password ic correct,if hash of the password with its hash returns the same hash 
            if (crypt($password, $user->password) == $user->password) {
                return true; //'  Ok!';
            }
            return false; //'  Wrong!';
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }

    /**
     * Function to execute a sql query
     */
    public function execQuery() {
        $result = [];
        if (!empty($this->sql_query)) {
            try {
                $st = $this->con->prepare($this->sql_query);
                $st->execute();
                if ($st) {
                    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                        $result[] = $row;
                    }
                }
            } catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
        }
        return $result;
    }

    /**
     * Function to test the result of execution a sql query
     */
    public function isExecQuery() {
        $res = false;
        try {
            $st = $this->con->prepare($this->sql_query);
            $st->execute();
            if ($st) {
                $res = true;
            }
        } catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
        return $res;
    }

    /**
     * Function to get inpection  from  MySQL database
     */
    public function selectInspection() {
        $user = (object) $this->instControl->getUser();
        switch ($user->role) {
            case R_ADMIN://administrator
                $role_query = "";
                break;
            case R_ADMIN_MRO:
                $role_query = " WHERE id_mro='$user->mro' ";
                break;
            case R_ADMIN_INSPECTION:
            case R_USER:
                $role_query = " WHERE id='$user->id_inspection' ";
                break;
        }
        $this->sql_query = " SELECT * FROM inspection ";
        $this->sql_query .=$role_query;
        return $this->execQuery();
    }

    /**
     * Function to get user password  from  MySQL database
     */
    public function getInspection($id) {
        $this->sql_query = "SELECT * FROM inspection where id='$id'";
        return $this->execQuery();
    }

    /**
     * Function to get all records from the user table
     */
    public function getAllUser() {
        $this->sql_query = "SELECT * FROM user";
        return $this->execQuery();
    }

//SELECT * FROM `user` WHERE id_inspection IN(select id from inspection where id_mro=1)
//order by id_inspection)

    /**
     * Function to get information from the user table 
     */
    public function getAllUserInspection() {
        $this->sql_query = "SELECT user.fio, user.id_inspection,"
                . " inspection.name_i, user.name ,user.role "
                . " FROM `user` INNER JOIN `inspection`"
                . " ON user.id_inspection=inspection.id WHERE 1"
                . " ORDER BY inspection.id,fio ";
        return $this->execQuery();
    }

    /**
     * Function to get the  user record   from  MySQL database
     */
    public function selectUser($username) {
        $this->sql_query = "SELECT * FROM user where name='$username'";
        return $this->execQuery();
    }

    /**
     * Function to get user password  from  MySQL database
     */
    public function getUsersOfInspection($id) {
        $this->sql_query = "SELECT * FROM user where id_inspection='$id'";
        return $this->execQuery();
    }

    /**
     * Function to insert new user account into MySQL database
     */
    public function insertUser() {
        $record = $this->record;
        $this->sql_query = "INSERT INTO user (fio,id_inspection,name, password,status,role,access)"
                . " VALUES( '$record->fio','$record->id_inspection',"
                . "'$record->name','$record->hash','$record->status','$record->role','$record->access' )";
        return $this->isExecQuery();
    }

    /**
     * Function to update the user table  in  MySQL database
     */
    public function updateUser() {
        $record = $this->record;
        $row = $this->selectUser($record->pass2);
        if (!empty($record->password)) {
            $security = new Security();
            $record->hash = $security->hashPassword($record->password); //hash
        } else {
            $record->hash = $row[0]['password'];
        }
        $this->sql_query = "UPDATE user"
                . " Set  fio='$record->fio',"
                . " id_inspection='$record->id_inspection',"
                . " name='$record->name',"
                . " password='$record->hash',"
                . " status='$record->status', "
                . " role='$record->role',"
                . " access='$record->access'"
                . " where name='$record->pass2'";
        return $this->isExecQuery();
    }

    /**
     * Function to get information from the weekend table  
     */
    public function selectHolidays() {
        $this->sql_query = "SELECT * FROM holidays";
        return $this->execQuery();
    }

    /**
     * Function to get information from the weekend table  
     */
    public function selectWeekends() {
        $this->sql_query = "SELECT * FROM weekends";
        return $this->execQuery();
    }

    /**
     * Function to get information from the msg table  
     */
    public function selectMsgJoin() {
        $this->sql_query = "SELECT msg.num,msg.msg_date,"
                . "msg.nsi_1,nsi_1.name_1,msg.to_who,doc_id"
                . " FROM msg INNER JOIN nsi_1"
                . " ON msg.nsi_1=nsi_1.id"
                . " WHERE msg.doc_id='$this->doc_id'";
        return $this->execQuery();
    }

    /**
     * Function to get irmformation  from the act table 
     */
    public function selectActJoin() {
        $this->sql_query = "SELECT act.num,act.act_date,act.nsi_2,"
                . " nsi_2.name_2,"
                . " act.summa,act.ticket_date,act.ticket_num,to_who, doc_id"
                . " FROM act INNER JOIN nsi_2"
                . " ON act.nsi_2=nsi_2.id "
                . " WHERE act.doc_id='$this->doc_id'";
        return $this->execQuery();
    }

    /**
     * Function to get irmformation  from the prepare table 
     */
    public function selectPrepareJoin($nsi_3, $nsi_10) {
        $nsi3 = $nsi10 = $name3 = $name10 = "";
        if (!empty($nsi_3)) {
            $name3 = " nsi_3.name_3, ";
            $nsi3 = " INNER JOIN nsi_3 ON p.nsi_3=nsi_3.id ";
        }
        if (!empty($nsi_10)) {
            $name10 = " nsi_10.name_10,";
            $nsi10 = " INNER JOIN nsi_10 ON p.nsi_10=nsi_10.id ";
        }

        $this->sql_query = "SELECT user.fio, p.view_date, p.view_object,"
                . " p.quest_date, p.quest_fio,"
                . $name3
                . " p.statute_num, p.statute_date, p.protocol_num, p.protocol_date,"
                . $name10
                . " p.doc_num, p.doc_date, p.addressat"
                . " FROM prepare p "
                . " INNER JOIN user"
                . " ON p.inspector_id=user.id "
                . $nsi3
                . $nsi10
                . " where p.doc_id='$this->doc_id'";
        return $this->execQuery();
    }

    /**
     * Function to select  information   from  penalty table
     */
    public function selectPenaltyJoin() {
        $this->sql_query = "CALL getPenalty('$this->doc_id')";
        return $this->execQuery();
    }

    /**
     * Function to select  information   from  termination table
     */
    public function selectTerminationJoin() {
        $this->sql_query = "CALL getTermination('$this->doc_id')";
        return $this->execQuery();
    }

    /**
     * Function to select  information   from  inform table
     */
    public function selectInformJoin() {
        $this->sql_query = "SELECT exec_num, exec_date, nsi_9.name_9, "
                . " force_date, force_num, force_summa"
                . " FROM inform p"
                . " INNER JOIN nsi_9"
                . " ON p.nsi_9=nsi_9.id"
                . " WHERE doc_id='$this->doc_id'";
        return $this->execQuery();
    }

    /**
     * Function to select  information   from  book  and msg tables
     */
    public function selectUnion($reg_num) {
        $user = (object) $this->instControl->getUser();
        switch ($user->role) {
            case R_ADMIN://administrator
                $role_query = "";
                break;
            case R_ADMIN_MRO://administrator mro
                $role_query = " WHERE "
                        . " (b.inspector_id "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection "
                        . " IN (SELECT id  FROM inspection WHERE id_mro='$user->mro'))"
                        . " OR "
                        . " b.prepar_insp "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection "
                        . " IN (SELECT id  FROM inspection WHERE id_mro='$user->mro'))) ";
                break;
            case R_ADMIN_INSPECTION://administrator inspection
                $role_query = " WHERE "
                        . " (b.inspector_id "
                        . " IN (SELECT id FROM user  WHERE id_inspection='$user->id_inspection')"
                        . " OR"
                        . " b.prepar_insp "
                        . " IN (SELECT id FROM user  WHERE id_inspection='$user->id_inspection')) ";
                break;
            case R_USER://user
                $role_query = " WHERE (b.inspector_id='$user->id'"
                        . " OR "
                        . " b.prepar_insp ='$user->id') ";
                break;
        }
        $role_query .=($reg_num == '0') ? " and  b.reg_num ='' " : "";
        $this->sql_query = "(SELECT  to_who,doc_id FROM msg m"
                . " INNER JOIN book b "
                . " ON m.doc_id=b.id "
                . $role_query
                . " )"
                . " UNION "
                . "(SELECT to_who, doc_id FROM  act a "
                . " INNER JOIN book b "
                . " ON a.doc_id=b.id "
                . $role_query
                . ")"
                . " ORDER BY to_who";
        return $this->execQuery();
    }

    /**
     * Function to select  information   from  book  and msg tables
     */
    public function selectUnionBook($reg_num, $mon, $year) {
        $user = (object) $this->instControl->getUser();
        switch ($user->role) {
            case R_ADMIN://administrator
                $role_query = "";
                break;
            case R_ADMIN_MRO://administrator mro
                $role_query = " WHERE  "
                        . " (b.inspector_id "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection "
                        . " IN (SELECT id  FROM inspection WHERE id_mro='$user->mro'))"
                        . " OR "
                        . " b.prepar_insp "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection "
                        . " IN (SELECT id  FROM inspection WHERE id_mro='$user->mro'))) ";
                break;
            case R_ADMIN_INSPECTION://administrator inspection
                $role_query = " WHERE "
                        . " (b.inspector_id "
                        . " IN (SELECT id FROM user  WHERE id_inspection='$user->id_inspection')"
                        . " OR"
                        . " b.prepar_insp "
                        . " IN (SELECT id FROM user  WHERE id_inspection='$user->id_inspection')) ";
                break;
            case R_USER://user
                $role_query = " WHERE (b.inspector_id='$user->id'"
                        . " OR "
                        . " b.prepar_insp ='$user->id') ";
                break;
        }
        $order_str = $_SESSION['order'];
        $role_query .=($reg_num == '0') ? " and  b.reg_num ='' " : "";
        $msg1_query = EMPT_Y;
        $msg2_query = " WHERE  YEAR(date)='$year'";
        if ($mon != 13) {
            $msg2_query .="  AND MONTH(date)='$mon'";
        } else {
            $msg2_query .="  AND MONTH(date)<'$mon'";
        }
        $msg1_query.=($reg_num == '1' || $reg_num == '2') ? " INNER JOIN user u_a ON b_a.inspector_id=u_a.id " :
                " INNER JOIN user u_a ON b_a.prepar_insp=u_a.id ";
        $msg2_query.=($reg_num == '2') ? " AND  b_a.prepar_insp='0' " :
                (($reg_num == '3') ? " AND  b_a.state='3' " : "");
        $this->sql_query = "SELECT reg_num,to_who,name_i,fio,name_12,doc_id, date,"
                . "CONVERT(SUBSTRING_INDEX(reg_num,'-',1),UNSIGNED INTEGER) AS reg_num1 FROM "
                . " ((SELECT  to_who,doc_id,msg_date AS date  FROM msg m"
                . " INNER JOIN book b "
                . " ON m.doc_id=b.id "
                . $role_query
                . " )"
                . " UNION "
                . "(SELECT to_who, doc_id,act_date AS date FROM  act a "
                . " INNER JOIN book b "
                . " ON a.doc_id=b.id "
                . $role_query
                . " AND a.doc_id NOT IN  "
                . " (SELECT  doc_id  FROM msg m"
                . " INNER JOIN book b "
                . " ON m.doc_id=b.id "
                . $role_query
                . " )"
                . ")"
                . " ORDER BY to_who)   un"
                . " INNER JOIN book b_a "
                . " ON un.doc_id=b_a.id "
                . $msg1_query
                . " INNER JOIN inspection i_a "
                . " ON i_a.id=u_a.id_inspection "
                . " INNER JOIN nsi_12 n_si "
                . " ON n_si.id=b_a.state"
//                . " INNER JOIN prepare pr_a "
//                . " ON pr_a.doc_id=b_a.id "
                . $msg2_query
                . "ORDER BY " . $order_str;
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function getDailyReport($mro, $mon, $year, $order) {
        switch ($mro) {
            case 4://administrator
                $this->sql_query = "CALL administrator_new($mon,$year,$order)";
                break;
            case 1://administrator mro
            case 2://administrator mro
            case 3://administrator mro
                $this->sql_query = "CALL adminMROnew($mro,$mon,$year,$order)";
                break;
        }
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function msgCountTotalMonth($mro, $mon, $year) {
        $this->sql_query = "CALL reportTotalMonth($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function msgCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL reportPerArticle($mro,$mon,$article,$nsi5,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function getCountPerArticle($mro, $mon, $article, $nsi5, $year, $forma, $operation, $cumulative) {
        $this->sql_query = "CALL getCount($mro,$mon,$article,$nsi5,$year,$forma,$operation,$cumulative)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function getContextData($mro, $mon, $article, $nsi5, $year, $forma, $operation, $cumulative) {
        $this->sql_query = "CALL getContextData($mro,$mon,$article,$nsi5,$year,$forma,$operation,$cumulative)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function getInputData($mro, $mon, $year, $row_name, $rep_name, $cumulative) {
        $record = [];
        $record['mro'] = $mro;
        $record['mon'] = $mon;
        $record['year'] = $year;
        $record['row_name'] = $row_name;
        $record['rep_name'] = $rep_name;
        $record['cumulative'] = $cumulative;
        $this->sql_query = "CALL getInputData(:mro,:mon,:year,:row_name,:rep_name,:cumulative)";
//        $this->sql_query = "CALL getSaved(:mro,:mon,:year,:row_name,:rep_name)";
        return $this->execQueryBindParamInput($record);
    }

    /**
     * Function to execute a sql query
     */
    public function execQueryBindParamInput($record) {
        $result = [];
        $data = $record;
        if (!empty($this->sql_query)) {
            try {
                $st = $this->con->prepare($this->sql_query);
                $st->bindParam(':mro', $data['mro']);
                $st->bindParam(':mon', $data['mon']);
                $st->bindParam(':year', $data['year']);
                $st->bindParam(':row_name', $data['row_name']);
                $st->bindParam(':rep_name', $data['rep_name']);
                $st->bindParam(':cumulative', $data['cumulative']);
                $st->execute();
                if ($st) {
                    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                        $result[] = $row;
                    }
                }
            } catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
        }
        return $result;
    }

    /**
     * Function
     */
    public function getSummaPerArticle($mro, $mon, $article, $nsi5, $year, $forma, $operation, $cumulative) {
        $this->sql_query = "CALL getSumma($mro,$mon,$article,$nsi5,$year,$forma,$operation,$cumulative)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function msgCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL reportCumulative($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function msgCountCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL reportCumulativeN($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function protocolCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL protocolCountTotal($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function protocolCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL protocolCountCumulative($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function protocolCountCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL protocolCountCumulativeN($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function protocolCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL protocolCountPerArticle($mro,$mon,$article,$nsi5,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function protocolCountPerArticleF_1($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL protocolCountPerArticleF_1($mro,$mon,$article,$nsi5,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function statuteCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL statuteCountTotal($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function statuteCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL statuteCountCumulative($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function statuteCountCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL statuteCountCumulativeN($mro,$mon,$year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function statuteCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        switch ($mro) {
            case 4://administrator
                $this->sql_query = "CALL statuteCountAdminPerArticle($mon, $article, $nsi5, $year)";
                break;
            case 1://administrator mro
            case 2://administrator mro
            case 3://administrator mro
                $this->sql_query = "CALL statuteCountMROperArticle($mro, $mon, $article, $nsi5, $year)";
                break;
        }
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function warningCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL warningCountPerArticle($mro, $mon, $article, $nsi5, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function warningCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL warningCountTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function warningCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL warningCountCumulative($mro, $mon,  $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function warningCountCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL warningCountCumulativeN($mro, $mon,  $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL penaltyCountTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL penaltyCountCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyCountCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL penaltyCountCumulativeN($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        switch ($mro) {
            case 4://administrator
                $this->sql_query = "CALL penaltyCountAdminPerArticle($mon, $article, $nsi5, $year)";
                break;
            case 1://administrator mro
            case 2://administrator mro
            case 3://administrator mro
                $this->sql_query = "CALL penaltyCountMROperArticle($mro, $mon, $article, $nsi5, $year)";
                break;
        }
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltySummaTotal($mro, $mon, $year) {
        $this->sql_query = "CALL penaltySummaTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltySummaCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL penaltySummaCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltySummaCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL penaltySummaCumulativeN($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltySummaPerArticle($mro, $mon, $article, $nsi5, $year) {
        switch ($mro) {
            case 4://administrator
                $this->sql_query = "CALL penaltySummaAdminPerArticle($mon, $article, $nsi5, $year)";
                break;
            case 1://administrator mro
            case 2://administrator mro
            case 3://administrator mro
                $this->sql_query = "CALL penaltySummaMROperArticle($mro, $mon, $article, $nsi5, $year)";
                break;
        }
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyTicketTotal($mro, $mon, $year) {
        $this->sql_query = "CALL penaltyTicketTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyTicketCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL penaltyTicketCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyTicketCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL penaltyTicketCumulativeN($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function penaltyTicketPerArticle($mro, $mon, $article, $nsi5, $year) {
        switch ($mro) {
            case 4://administrator
                $this->sql_query = "CALL penaltyTicketAdminPerArticle($mon, $article, $nsi5, $year)";
                break;
            case 1://administrator mro
            case 2://administrator mro
            case 3://administrator mro
                $this->sql_query = "CALL penaltyTicketMROperArticle($mro, $mon, $article, $nsi5, $year)";
                break;
        }
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function terminationCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL terminationCountTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function terminationCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL terminationCountCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function terminationCountCumulativeN($mro, $mon, $year) {
        $this->sql_query = "CALL terminationCountCumulativeN($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function terminationCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL terminationCountPerArticle($mro, $mon, $article, $nsi5, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL commissionCountTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL commissionCountCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL commissionCountPerArticle($mro, $mon, $article, $nsi5, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionPenaltyCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL commissionPenaltyCountTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionPenaltyCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL commissionPenaltyCountCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionPenaltyCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL commissionPenaltyCountPerArticle($mro, $mon, $article, $nsi5, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionTerminationCountTotal($mro, $mon, $year) {
        $this->sql_query = "CALL commissionTerminationCountTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionTerminationCountCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL commissionTerminationCountCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionTerminationCountPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL commissionTerminationCountPerArticle($mro, $mon, $article, $nsi5, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionSummaTotal($mro, $mon, $year) {
        $this->sql_query = "CALL commissionSummaTotal($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionSummaCumulative($mro, $mon, $year) {
        $this->sql_query = "CALL commissionSummaCumulative($mro, $mon, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function commissionSummaPerArticle($mro, $mon, $article, $nsi5, $year) {
        $this->sql_query = "CALL commissionSummaPerArticle($mro, $mon, $article, $nsi5, $year)";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function getSaved($mro, $mon, $year, $row_name, $rep_name) {
        $record = [];
        $record['mro'] = $mro;
        $record['mon'] = $mon;
        $record['year'] = $year;
        $record['row_name'] = $row_name;
        $record['rep_name'] = $rep_name;
        $this->sql_query = "CALL getSaved(:mro,:mon,:year,:row_name,:rep_name)";
//        $this->sql_query = "CALL getSaved(:mro,:mon,:year,:row_name,:rep_name)";
        return $this->execQueryBindParam($record);
    }

    /**
     * Function
     */
    public function getSavedCumulative($mro, $mon, $year, $row_name, $rep_name) {
        $record = [];
        $record['mro'] = $mro;
        $record['mon'] = $mon;
        $record['year'] = $year;
        $record['row_name'] = $row_name;
        $record['rep_name'] = $rep_name;
        $this->sql_query = "CALL getSavedCumulative(:mro,:mon,:year,:row_name,:rep_name)";
//        $this->sql_query = "CALL getSaved(:mro,:mon,:year,:row_name,:rep_name)";
        return $this->execQueryBindParam($record);
    }

    /**
     * Function to execute a sql query
     */
    public function execQueryBindParam($record) {
        $result = [];
        $data = $record;
        if (!empty($this->sql_query)) {
            try {
                $st = $this->con->prepare($this->sql_query);
                $st->bindParam(':mro', $data['mro']);
                $st->bindParam(':mon', $data['mon']);
                $st->bindParam(':year', $data['year']);
                $st->bindParam(':row_name', $data['row_name']);
                $st->bindParam(':rep_name', $data['rep_name']);
                $st->execute();
                if ($st) {
                    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                        $result[] = $row;
                    }
                }
            } catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
        }
        return $result;
    }

    /**
     * Function
     */
    public function deleteUnmatchedHead() {
        $this->sql_query = "DELETE FROM " . DB_BOOK
                . " WHERE "
                . ID . " NOT IN "
                . " (SELECT " . DOC_ID . " FROM " . DB_MSG . ")"
                . " AND " . ID
                . " NOT IN "
                . " (SELECT " . DOC_ID . " FROM " . DB_ACT . ")"
                . " AND " . ID
                . " NOT IN "
                . " (SELECT " . DOC_ID . " FROM " . DB_PREPARE . ")"
                . " AND " . ID
                . " NOT IN "
                . " (SELECT " . DOC_ID . " FROM " . DB_REVIEW . ")"
                . " AND " . ID
                . " NOT IN "
                . " (SELECT " . DOC_ID . " FROM " . DB_PENALTY . ")"
                . " AND " . ID
                . " NOT IN "
                . " (SELECT " . DOC_ID . " FROM " . DB_TERMINATION . ")"
                . " AND " . ID
                . " NOT IN "
                . " (SELECT " . DOC_ID . " FROM " . DB_INFORM . ")";
        return $this->isExecQuery();
    }

    /**
     * Function to select  information   from  book  and msg tables
     */
    public function selectDocsJoinMsg($role) {
        $this->sql_query = '';
        $user = (object) $this->instControl->getUser();
        switch ($role) {
            case '1'://administrator
                $this->sql_query = "SELECT p.id, msg.to_who, msg.msg_date "
                        . " FROM book p"
                        . " INNER JOIN msg"
                        . " ON p.id=msg.doc_id";
                break;
            case '2'://administrator mro
                $this->sql_query = "SELECT p.id, msg.to_who, msg.msg_date "
                        . " FROM book p"
                        . " INNER JOIN msg"
                        . " ON p.id=msg.doc_id"
                        . " WHERE p.inspector_id "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection IN (SELECT id  FROM inspection WHERE id_mro='$this->mro')) "
                        . " or p.prepar_insp "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection IN (SELECT id  FROM inspection  WHERE id_mro='$this->mro'))";
                break;
            case '3'://administrator of  inspection can see all documets of inspection.
                $this->sql_query = "SELECT p.id, msg.to_who, msg.msg_date "
                        . " FROM book p"
                        . " INNER JOIN msg"
                        . " ON p.id=msg.doc_id"
                        . " WHERE p.inspector_id "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection='$this->inspecction')  or p.prepar_insp"
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection='$this->inspecction')";
                break;
            case '4'://user
                $this->sql_query = "SELECT p.id, msg.to_who, msg.msg_date "
                        . " FROM book p"
                        . " INNER JOIN msg"
                        . " ON p.id=msg.doc_id"
                        . " WHERE p.inspector_id='$user->id'"
                        . " or p.prepar_insp='$user->id'";
                break;
        }
        return $this->execQuery();
    }

    /**
     * Function to select  information   from  book and act tables
     */
    public function selectDocsJoinAct($role) {
        $this->sql_query = '';
        $user = (object) $this->instControl->getUser();
        switch ($role) {
            case '1'://administrator
                $this->sql_query = "SELECT p.id, act.to_who, act.act_date "
                        . " FROM book p"
                        . " INNER JOIN act"
                        . " ON p.id=act.doc_id";
                break;
            case '2'://administrator mro
                $this->sql_query = "SELECT p.id, act.to_who, act.act_date "
                        . " FROM book p"
                        . " INNER JOIN act"
                        . " ON p.id=act.doc_id"
                        . " WHERE p.inspector_id "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection IN (SELECT id  FROM inspection WHERE id_mro='$this->mro')) "
                        . " or p.prepar_insp "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection IN (SELECT id  FROM inspection  WHERE id_mro='$this->mro'))";
                break;
            case '3'://administrator of  inspection can see all documets of inspection.
                $this->sql_query = "SELECT p.id, act.to_who, act.act_date "
                        . " FROM book p"
                        . " INNER JOIN act"
                        . " ON p.id=act.doc_id"
                        . " WHERE p.inspector_id "
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection='$this->inspecction')  or p.prepar_insp"
                        . " IN (SELECT id FROM user "
                        . " WHERE id_inspection='$this->inspecction')";
                break;
            case '4'://user
                $this->sql_query = "SELECT p.id, act.to_who, act.act_date "
                        . " FROM book p"
                        . " INNER JOIN act"
                        . " ON p.id=act.doc_id"
                        . " WHERE p.inspector_id='$user->id' or p.prepar_insp='$user->id'";
                break;
        }
        return $this->execQuery();
    }

    /**
     * Function to get information from book table
     */
    public function selectDocsOfUser($user_id) {
        $this->sql_query = "SELECT * FROM book"
                . "  WHERE  inspector_id='$user_id'";
        return $this->execQuery();
    }

    /**
     * Function
     */
    public function selectDocsId() {
        $this->sql_query = "SELECT * FROM book where id='$this->doc_id'";
        return $this->execQuery();
    }

    /**
     * Function to get hiden
     */
    public function selectHiden($name_tbl, $name_fld) {
        $this->sql_query = "SELECT * FROM hiden_fld "
                . " WHERE  name_tbl='$name_tbl' "
                . " and name_fld2='$name_fld'";
        $res = $this->execQuery();
        return (count($res) > 0) ? $res[0] : $res;
    }

    /**
     * Function to get the  nsi selected list  from  MySQL database
     */
    public function selectedList($num_nsi, $selected) {
        $nsi = 'nsi_' . $num_nsi;
        $name_s = 'name_' . $num_nsi;
        $row = $this->selectAllRecords($nsi);
        $value = "0";
        $name = "--Выбрать--";
        if ($value == $selected) {
            echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
        } else {
            echo "<option value='" . $value . "'>" . $name . "</option>";
        }
        foreach ($row as $item) {
            $value = $item['id'];
            $name = $item[$name_s];
            if ($value == $selected) {
                echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
            } else {
                echo "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
    }

    /**
     * Function to get the  nsi selected list  from  MySQL database and return it to php
     */
    public function selectedListR($tbl_name, $fld_name, $selected) {
        $row = $this->selectAllRecords($tbl_name);
        $stR = '';
        $value = "0";
        $name = "--Выбрать--";
        if ($value == $selected) {
            $stR .= "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
        } else {
            $stR .= "<option value='" . $value . "'>" . $name . "</option>";
        }
        foreach ($row as $item) {
            $value = $item['id'];
            $name = $item[$fld_name];
            if ($value == $selected) {
                $stR .= "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
            } else {
                $stR .= "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
        return $stR;
    }

    /**
     * Function to get all records 
     */
    public function selectAllRecords($tbl_name) {
        $this->sql_query = "SELECT * FROM  " . $tbl_name;
        $this->sql_query .= ($tbl_name == DB_INSPECTOR) ? " ORDER BY id_inspection " : "";
        return $this->execQuery();
    }

    /**
     * Function to delete all records 
     */
    public function deleteAllRecords($tbl_name) {
        $this->sql_query = "TRUNCATE " . $tbl_name;
        return $this->isExecQuery();
    }

    /**
     * Function to build selected list of  all users of mro   
     */
    public function selectedListAllUserMRO($selected, $mro, $role) {
        if ($role == '1') {
            $row = $this->selectAllRecords(DB_INSPECTOR);
        } else {
            $row = $this->getAllUserMRO($mro);
        }
        $value = "0";
        $name = "--Выбрать--";
        if ($value == $selected) {
            echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
        } else {
            echo "<option value='" . $value . "'>" . $name . "</option>";
        }
        $old = '0';
        foreach ($row as $item) {
            $value = $item['id'];
            $name = $item['fio'];
            $id_inspection = $item['id_inspection'];
            if ($id_inspection != $old) {
                $old = $id_inspection;
                $res = $this->getInspection($id_inspection);
                foreach ($res as $itm) {
                    $name_i = str_replace(' ', '_', $itm['name_i']);
                    echo '<optgroup label=' . $name_i . '>';
                }
            }
            if ($value == $selected) {
                echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
            } else {
                echo "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
    }

    /**
     * Function to get all users  of mro  
     */
    public function getAllUserMRO($mro) {
        $this->sql_query = "CALL getUsersOfMRO('$mro')";
        return $this->execQuery();
    }

    /**
     * Function to get nsi  from  MySQL database
     */
// Build HTML <option> tags
//<!--                              -->
    public function selectedListSeniorInspectors($status, $selected) {
        $row = $this->getSeniorInspectors($status);
        $value = "0";
        $name = "--Выбрать--";
        if ($value == $selected) {
            echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
        } else {
            echo "<option value='" . $value . "'>" . $name . "</option>";
        }
        $old = '0';
        foreach ($row as $item) {
            $value = $item['id'];
            $name = $item['fio'];
            $id_inspection = $item['id_inspection'];
            if ($id_inspection != $old) {
                $old = $id_inspection;
                $res = $this->getInspection($id_inspection);
                foreach ($res as $itm) {
                    $name_i = str_replace(' ', '_', $itm['name_i']);
                    echo '<optgroup label=' . $name_i . '>';
                }
            }
            if ($value == $selected) {
                echo "<option selected='selected' value='" . $value . "'>" . $name . "</option>";
            } else {
                echo "<option value='" . $value . "'>" . $name . "</option>";
            }
        }
    }

    /**
     * Function to get user password  from  MySQL database
     */
    public function getSeniorInspectors($status) {
        $this->sql_query = "SELECT * FROM user"
                . " where status='$status'"
                . " ORDER BY id_inspection";
        return $this->execQuery();
    }

    public function deleteDoc($json_data) {
//convert to stdclass object
        $this->sql_query = "DELETE FROM "
                . $json_data->name_tbl . " WHERE "
                . $json_data->name_fld . "='$json_data->val'";
        return $this->isExecQuery();
    }

    /**
     * Function to get data from MSG or ACT table
     * getData
     * @param type $doc_id
     * @return string
     */
    public function getData($mode) {
        $row = $this->selectMsgJoin();
        $count = 0;
        $json_str = "";
        if (count($row) != 0) { //Message
            $count++;
            $json_str = "<fieldset><legend class='txtTableLegend'>" .
                    "Сообщение об административном правонарушении" . "</legend><br />";
            $json_str = $json_str . "<table border='0' cellpadding='2' class='mydatatable'>
<tr>
<th>Входящий номер</th>
<th>Дата</th>
<th>От кого поступило</th>
<th>На кого поступило</th>
</tr>";
            foreach ($row as $item) {
                $json_str = $json_str . "<tr>";
                $json_str = $json_str . "<td >" . $item['num'] . "</td>";
                $json_str = $json_str . "<td>" . $item['msg_date'] . "</td>";
                $json_str = $json_str . "<td>" . $item['name_1'] . "</td>";
                $json_str = $json_str . "<td>" . $item['to_who'] . "</td>";
                $json_str = $json_str . "</tr>";
            }
            $json_str = $json_str . "</table>";
            $json_str = $json_str . "</fieldset>";
        } else {
            $row = $this->selectActJoin();
            if (count($row) != 0) {//Act
                $count++;
                $json_str = $json_str . "<fieldset><legend class='txtTableLegend'>" .
                        "Акт, фиксирующий нарушение" . "</legend><br />";
                $json_str = $json_str . "<table border='0' cellpadding='2' class='mydatatable'>
<tr>
<th>№ акта</th>
<th>Дата акта</th>
<th>Кем составлен</th>
<th>Сумма ущерба</th>
<th>Дата квитанции об оплате</th>
<th>№ квитанции</th>
<th>На кого составлен</th>
</tr>";
                foreach ($row as $item) {
                    $json_str = $json_str . "<tr>";
                    $json_str = $json_str . "<td>" . $item['num'] . "</td>";
                    $json_str = $json_str . "<td>" . $item['act_date'] . "</td>";
                    $json_str = $json_str . "<td>" . $item['name_2'] . "</td>";
                    $json_str = $json_str . "<td>" . $item['summa'] . "</td>";
                    $json_str = $json_str . "<td>" . $item['ticket_date'] . "</td>";
                    $json_str = $json_str . "<td>" . $item['ticket_num'] . "</td>";
                    $json_str = $json_str . "<td>" . $item['to_who'] . "</td>";
                    $json_str = $json_str . "</tr>";
                }
                $json_str = $json_str . "</table>";
                $json_str = $json_str . "</fieldset>";
            }
        }
        if ($count > 0) {
            $json_str = $json_str . "<input    class='button button-blue' name='edit' type='submit'";
            $json_str .= ($mode == 'edit') ? " value='Редактировать'>" : " value='Присвоить номер'>";
        } else
            $json_str = $json_str . "Информация отсутствует";
        return $json_str;
    }

    /**
     * Function to test the date column name is checked or not
     */
    public function isDateChecked($name_fld) {
        $this->sql_query = "SELECT * FROM checked_date "
                . " WHERE  name_tbl='$this->name_tbl' and name_fld='$name_fld'";
        return (count($result = $this->execQuery()) > 0) ? true : false;
    }

    /**
     * Function to output debug messages 
     */
    public function outDebugMessage($array) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

}

/**
 * Class to secutity work
 */
class Security {

    /**
     * Function to hash password 
     */
    public function hashPassword($password) {
        $cost = 7;
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        $salt = sprintf("$2y$%02d$", $cost) . $salt;
        $hash = crypt($password, $salt);
        return $hash;
    }

}
