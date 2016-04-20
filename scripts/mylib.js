/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var gUser; //it keeps an user id 
var gDocId; //document id
var gProtocolId; //prpotocol id
var gRequest; //it keeps the address of  a RequestHttp object
var gNewLabel;

/*******************************************************************************
 /*Class to request  data from a http server
 ***************************************************************************** */
//constructor
function RequestHttp(head, json_data) {
    this.setJsonObj(head, json_data);
    gRequest = this; //keep the address of this RequestHttp object
}



RequestHttp.prototype = {
    json_obj: {//JSON object
        head: "",
        data: ""
    },
    xmlhttp: {}, //xmlhttp object
    setJsonObj: function (head, json_data) {
        this.json_obj.head = head;
        this.json_obj.data = JSON.stringify(json_data);
    },
    //method to send  a  request to the http server
    requestHttpServer: function () {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            try {
                this.xmlhttp = new XMLHttpRequest();
            } catch (e) {
            }
        } else
        {// code for IE6, IE5
            try {
                this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
            }
        }
        this.xmlhttp.responseType = "text";
        this.xmlhttp.onreadystatechange = this.stateChanged;
        var json_str = JSON.stringify(this.json_obj);
        var param1 = encodeURIComponent(json_str);
        var post = "json_str=" + param1;
//        alert(json_str);
        this.xmlhttp.open("POST", "handler.php", true);
        //xmlhttp.setRequestHeader('Content-type', 'application/json; charset=utf-8');
        this.xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        this.xmlhttp.send(post);
    },
    //method 
    stateChanged: function () {
        if (gRequest.xmlhttp.readyState === 4) {
            if (gRequest.xmlhttp.status === 200) {
                gRequest.handlerResponseHttpServer();
            }
        }
    },
    //method  to handle an  aswer from the http server
    handlerResponseHttpServer: function () {
        var json_obj = JSON.parse(this.xmlhttp.responseText);
        if (json_obj.success === "yes") {
            switch (json_obj.head) {
                case 'disabled_enabled':
                    if (json_obj.data === "enabled") {
                        document.getElementById("delete").disabled = false;
                        //document.getElementById("save").disabled = false;
                    } else {
                        document.getElementById("delete").disabled = true;
                        //document.getElementById("save").disabled = true;
                    }
                    break;
                case 'html':
                    //alert(json_obj.data);
                    var json = JSON.parse(json_obj.data);
                    document.getElementById('txtHint').innerHTML = json.html;
                    document.getElementById('reg_num').value = json.reg_num;
                    break;
                case 'delete':
                    json_obj = JSON.parse(json_obj.data);
                    if (!empty(json_obj.error))
                        alert(json_obj.error);
                    else {
                        if (json_obj.name_tbl === 'user') {
                            document.location.replace("spr_out.php");
                        }
                    }
                    break;
                case 'update_user':
                    if (!empty(json_obj.data))
                        alert(json_obj.data);
                    document.location.replace("spr_out.php");
                    break;
                case 'save_report':
                    alert(json_obj.data);
                    document.location.replace("load.php");
                    break;
                case 'save_new_report':
                    alert(json_obj.data);
                    document.location.replace("load.php");
                    break;
                case 'set_order':
                    document.location.replace("load.php");
                    break;
                case 'checked':
                    //alert(json_obj.data);
                    json_obj = JSON.parse(json_obj.data);
                    var radiobtn1 = document.getElementById("radio" + json_obj.status);
                    radiobtn1.checked = true;
                    var radiobtn2 = document.getElementById("radio1" + json_obj.role);
                    radiobtn2.checked = true;
                    if (json_obj.access === 1)
                    {
                        var checkbtn = document.getElementById("check");
                        checkbtn.checked = true;
                    }
                    break;
                case 'menu_changed':
                    //alert(json_obj.data);
                    break;
                case 'month_data':
                    //alert(json_obj.data);
                    document.location.replace("load.php");
                    break;
                case 'mro_changed':
                    //alert(json_obj.data);
                    document.location.replace("load.php");
                    break;
                case 'quarter_changed':
                    //alert(json_obj.data);
                    document.location.replace("load.php");
                    break;
                case 'add_protocol':
                    if (!empty(json_obj.data))
                        alert(json_obj.data);
                    break;
                case 'edit_protocol':
                    if (!empty(json_obj.data))
                        alert(json_obj.data);
                    break;
                case 'delete_protocol':
                    if (!empty(json_obj.data))
                        alert(json_obj.data);
                    break;
                case 'save_reg_num':
                    if (!empty(json_obj.data))
                        alert(json_obj.data);
                    document.location.replace("load.php");
                    break;
                case 'goto':
                    if (!empty(json_obj.data))
                        alert(json_obj.data);
                    document.location.replace("msg_input.php");
                    break;
                case 'set_list':
                    var json = JSON.parse(json_obj.data);
                    // var json_str = JSON.stringify(json);
                    // alert(json_str);
                    document.myform.doc_id.options.length = 0;
                    document.getElementById('txtHint').innerHTML = "";
                    if (json.data.length > 0) {
                        document.myform.doc_id.options[0] = new Option('--Выбрать--', '0', true, false)
                        for (var i = 0; i < json.data.length; i++) {
                            document.myform.doc_id.options[i + 1] = new Option(json.data[i].to_who, json.data[i].doc_id, true, false)
                        }
                    } else {
                        document.myform.doc_id.options[0] = new Option('Нет данных', '0', true, false)
                        // alert(json_obj.msg);
                        document.myform.reg_num.value = '';
                    }
                    document.myform.reg_num.disabled = (json.name_fld == 'edit' ||
                            json.data.length == 0);
                    break;
            }
        } else
            alert(json_obj.msg);
    }
};
//var json_str = JSON.stringify(json);
//alert(json_str);

/*******************************************************************************
 /*doRequestHttpServer
 ***************************************************************************** */
function doRequestHttpServer(head, json_data, msg) {
    var result = !empty(msg) ? confirm(msg) : true;
    if (result) {
        var get = new RequestHttp(head, json_data);
        get.requestHttpServer();
    }
}






/*******************************************************************************
 /*Class to check date
 ***************************************************************************** */

function  DocsDate(array) {//constructor
    this.array = array; //general data structure.It is obtained from PHP
    this.interval = array.interval;
}

DocsDate.prototype = {
    msg:
            {//message to users
                great: 'Недопустимая дата',
                dayOff: 'Выходной день',
                equally: ''
            },
    color:
            {//color to edit date
                red: '#D82741',
                purple: '#6F50E7',
                green: '#428739'
            },
    control_date: "", //date to control all documents date
    end_date: "", //last date of control
    control_date2: "", //date to control all documents date
    end_date2: "", //last date of control
    doc_date: "", //current column date

    set_endDate: function () {
        this.end_date = Date.parse(this.array.control_date); //us_date format
        if (this.end_date != null) {
            (this.interval.type === 'day') ? this.end_date.addDays(this.interval.val) :
                    this.end_date.addMonths(this.interval.val);
            while (this.isDateDayOff(this.end_date)) {
                this.end_date.add({days: 1});
            }
        }
//        alert(this.end_date);
    },
    set_endDate2: function () {
        this.end_date2 = Date.parse(this.array.next_control_date); //us_date format
        if (this.end_date2 != null) {
            (this.array.next_interval.type === 'day') ? this.end_date2.addDays(this.array.next_interval.val) :
                    this.end_date2.addMonths(this.array.next_interval.val);
            while (this.isDateDayOff(this.end_date2)) {
                this.end_date2.add({days: 1});
            }
        }
//        alert(this.end_date);
    },
    setNextDate: function () {
        if (this.array.next === 0) {
            this.control_date2 = this.control_date.clone();
            this.end_date2 = this.end_date.clone();
        } else {
            this.set_endDate2();
            this.control_date2 = Date.parse(this.array.next_control_date); //us_date format
        }
    },
    //method to set style of date
    set_datestyle: function () {
        this.control_date = Date.parse(this.array.control_date); //us_date format
        if (this.control_date != null) {
            this.set_endDate();
            this.setNextDate();
            for (var i = 0; i < this.array.date_col_name.length; i++)
                this.checkDate(i);
        }
    },
    //method to check date
    checkDate: function (i) {
        var el_name = this.array.date_col_name[i];
        var inputs_date = document.getElementsByName(el_name);
        var substringArray = el_name.split("_");
        var error = document.getElementById(substringArray[0]);
        //inputs_date[0].value.toString()
        if (inputs_date.length > 0) {
            this.doc_date = Date.parse(this.convert(inputs_date[0].value).toString());
            //alert( this.control_date);
            if (this.doc_date != null && this.doc_date.between(this.control_date, this.end_date) &&
                    this.doc_date.between(this.control_date2, this.end_date2)) {
                if (this.isDateDayOff(this.doc_date)) {
                    error.innerHTML = this.msg.dayOff;
                    inputs_date[0].style.color = this.color.purple;
                    error.style.color = this.color.purple;
                } else {
                    error.innerHTML = this.msg.equally;
                    inputs_date[0].style.color = this.color.green;
                    error.style.color = this.color.green;
                }
            } else
            {
                error.innerHTML = this.msg.great;
                inputs_date[0].style.color = this.color.red;
                error.style.color = this.color.red;
            }
        }
    },
    //method to determine  the day is off or not
    isDateDayOff: function (date) {
        if (this.isDateInArray(date, this.array.days_off.weekend_days))
            return true;
        if (this.isDateInArray(date, this.array.days_off.holidays))
            return true;
        if (date.is().saturday() || date.is().sunday()) {
            if (this.isDateInArray(date, this.array.days_off.working_days))
                return false;
            return true;
        }
        return false;
    },
    //method to found date in an array of dates
    isDateInArray: function (date, array) {
        for (var i = 0; i < array.length; i++) {
            var date_w = Date.parse(array[i]);
            if (date_w.equals(date))
                return true;
        }
        return false;
    },
    //method to convert date
    convert: function (date) {
        var substringArray = date.split('.');
        var strData = '';
        strData = strData.concat(substringArray[1]); //month
        strData = strData.concat('/');
        strData = strData.concat(substringArray[0]); //day
        strData = strData.concat('/');
        strData = strData.concat(substringArray[2]); //year
        return strData;
    }

};
/**
 * 
 * @param {type} find
 * @param {type} replace_to
 * @returns {String.prototype@call;replace}
 */
String.prototype.replaceAll = function (find, replace_to) {
    return this.replace(new RegExp(find, "gi"), replace_to);
};
/**
 * editProtocol
 * @returns {undefined}
 */
function edit_protocol(j, index, id) {
    gProtocolId = id;
    var el_view = document.getElementById('overlay_view');
    var el_quest = document.getElementById('overlay_quest');
    var str = document.getElementById("mytbl").rows[j].cells[0].innerHTML;
    var el_delete_v = document.getElementById('delete_prtcl_v');
    var el_delete_q = document.getElementById('delete_prtcl_q');
    var substringArray = str.split('-');
    substringArray[1] = substringArray[1].replaceAll('<dfn>', '');
    substringArray[1] = substringArray[1].replaceAll('</dfn>', '');
    if (index == 0) {//protocol of view 
        document.getElementById('overlay_view_head').innerHTML = 'Редактирование протокола осмотра';
        document.getElementById('overlay_view_date').value = substringArray[0];
        document.getElementById('overlay_view_object').value = substringArray[1];
        el_view.style.visibility = (el_view.style.visibility == "visible") ? "hidden" : "visible";
        el_delete_v.style.visibility = (el_view.style.visibility == "visible") ? "visible" : "hidden";
        el_quest.style.visibility = "hidden";
        el_delete_q.style.visibility = 'hidden';
    } else {//protocol of quest
        document.getElementById('overlay_quest_head').innerHTML = 'Редактирование протокола опроса';
        document.getElementById('overlay_quest_date').value = substringArray[0];
        document.getElementById('overlay_quest_fio').value = substringArray[1];
        var res = substringArray[2].match(/лицо/gi) ? 1 :
                substringArray[2].match(/свидетель/gi) ? 2 :
                substringArray[2].match(/иное/gi) ? 3 : 0;
        document.getElementById('overlay_quest_nsi_3').value = res;
        el_quest.style.visibility = (el_quest.style.visibility == "visible") ? "hidden" : "visible";
        el_delete_q.style.visibility = (el_quest.style.visibility == "visible") ? "visible" : "hidden";
        el_view.style.visibility = "hidden";
        el_delete_v.style.visibility = 'hidden';
    }
    var pos_attr = (el_view.style.visibility == "visible" || el_quest.style.visibility == "visible") ?
            "fixed" : "absolute";
    setCalendarPos(pos_attr);
}
/**
 * addProtocol
 * @returns {undefined}
 */
function add_protocol(index) {
    var el_view = document.getElementById('overlay_view');
    var el_quest = document.getElementById('overlay_quest');
    var el_delete_v = document.getElementById('delete_prtcl_v');
    var el_delete_q = document.getElementById('delete_prtcl_q');
    el_delete_v.style.visibility = 'hidden';
    el_delete_q.style.visibility = 'hidden';
    if (index == 0) {
        document.getElementById('overlay_view_head').innerHTML = 'Ввод нового протокола осмотра';
        document.getElementById('overlay_view_date').value = '';
        document.getElementById('overlay_view_object').value = '';
        el_view.style.visibility = (el_view.style.visibility == "visible") ? "hidden" : "visible";
        el_quest.style.visibility = "hidden";
    } else {
        document.getElementById('overlay_quest_head').innerHTML = 'Ввод нового протокола опроса';
        document.getElementById('overlay_quest_date').value = '';
        document.getElementById('overlay_quest_fio').value = '';
        document.getElementById('overlay_quest_nsi_3').value = '0';
        el_quest.style.visibility = (el_quest.style.visibility == "visible") ? "hidden" : "visible";
        el_view.style.visibility = "hidden";
    }
    var pos_attr = (el_view.style.visibility == "visible" || el_quest.style.visibility == "visible") ?
            "fixed" : "absolute";
    setCalendarPos(pos_attr);
}

/**
 * save_protocol
 * 
 * @returns {undefined}
 */
function save_protocol(index, json_data) {
    var json_obj = json_data;
    var head;
    if (index == 0) {//protocol of view
        head = document.getElementById('overlay_view_head').innerHTML;
        json_obj.protocol_date = document.getElementById('overlay_view_date').value;
        json_obj.obj_fio = document.getElementById('overlay_view_object').value;
        json_obj.nsi_3 = '111';
        json_obj.type = '1';
    } else {//protocol of quest
        head = document.getElementById('overlay_quest_head').innerHTML;
        json_obj.protocol_date = document.getElementById('overlay_quest_date').value;
        json_obj.obj_fio = document.getElementById('overlay_quest_fio').value;
        json_obj.nsi_3 = document.getElementById('overlay_quest_nsi_3').value;
        json_obj.type = '2';
    }
    //var json_str = JSON.stringify(json_obj);
    //alert(json_str);
    //var json_str = JSON.stringify(json_obj);
    //alert(json_str);
    var substringArray = head.split(' ');
    if (substringArray[0].match(/ввод/gi)) {
        doRequestHttpServer('add_protocol', json_obj, '');
    } else {
        json_obj.id = gProtocolId;
        doRequestHttpServer('edit_protocol', json_obj, '');
    }
    setCalendarPos('absolute');
}
/*
 * delete_protocol
 * @param {type} index
 * @param {type} json_data
 * @returns {undefined}
 */

function delete_protocol(json_data) {
    var json_obj = json_data;
    json_obj.id = gProtocolId;
    doRequestHttpServer('delete_protocol', json_obj, 'Удалить данные?');
}


/**
 * del_record
 * @param {type} json_data
 * @returns {undefined}
 */
function del_record(json_data) {
    var json_obj = json_data;
    json_obj.val = (json_obj.name_tbl === 'user') ? document.getElementById('name').value : json_obj.val.id;
    var doc = document.documentElement;
    var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
    var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
    json_obj['pageXOffset'] = left;
    json_obj['pageYOffset'] = top;
    //   var json_str = JSON.stringify(json_obj);
    //   alert(json_str);


    doRequestHttpServer('delete', json_obj, 'Удалить данные?');
}

/**
 * viewDoc
 * @param {type} value
 * @returns {undefined}
 */
function view_doc(val, json_data) {
    var json_obj = json_data;
    json_obj.val = val; //set val as value
    doRequestHttpServer('html', json_obj, '');
    //var json_str = JSON.stringify(json_obj);
    //alert(json_str);
}
/**
 * set_list
 * @param {type} val
 * @returns {undefined}
 */
function set_list(val, json_data) {
    var json_obj = json_data;
    json_obj.val = val; //set val as value 
    //var json_str = JSON.stringify(json_obj);
    //alert(json_str);
//    if (json_obj.name_fld == 'registration')
    doRequestHttpServer('set_list', json_obj, '');
}
/**
 * menu_changed
 * @param {type} json_data
 * @returns {undefined}
 */
function menu_changed(val, json_data) {
    var json_obj = json_data;
    json_obj.val = val;
    //var json_str = JSON.stringify(json_obj);
    //alert(json_str);
    document.getElementById("form_a").innerHTML = '<img src="images/LoaderIcon.gif" />';
    doRequestHttpServer('menu_changed', json_obj, '');
}





/**
 * set_datestyle
 * @param {type} arr
 * @returns {undefined}
 */
function set_datestyle(arr) {
    var date = new DocsDate(arr);
    date.set_datestyle();
}

/**
 * overlay
 * @returns {undefined}
 */
function overlay_user(index, json_data) {
    var json_obj = json_data;
    var el = document.getElementById("overlay_user");
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
    document.getElementById('fio').value = document.getElementById("mytbl").rows[index].cells[0].innerHTML;
    document.getElementById('id_inspection').value = document.getElementById("mytbl").rows[index].cells[1].innerHTML;
    json_obj.val = document.getElementById('name').value = document.getElementById("mytbl").rows[index].cells[2].innerHTML;
    gUser = json_obj.val;
    var rows = document.getElementById('mytbl').getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var str1 = gUser.toString();
        var str2 = rows[i].cells[2].innerHTML.toString();
        if (str1 === str2) {
            var el = document.getElementById("row_" + i.toString());
            el.style.backgroundColor = "#D93600";
        }
    }
    doRequestHttpServer('checked', json_obj, '');
}



/**
 * Function to update user information
 * update_user()
 * @returns {undefined}
 */
function update_user(json_data, button) {
    var json_obj = {};
    var json_objU = json_data;

    json_objU.fio = document.getElementById('fio').value;
    json_objU.id_inspection = document.getElementById('id_inspection').value;
    json_objU.name = document.getElementById('name').value;
    json_objU.password = document.getElementById('password').value;
    json_objU.pass2 = document.getElementById('pass2').value;
    var compare = (json_objU.password == json_objU.pass2);
    if (compare) {
        json_objU.pass2 = gUser;
        var inputs = document.getElementsByName("status");
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].checked)
            {
                json_objU.status = inputs[i].value;
                break;
            }
        }
        inputs = document.getElementsByName("role");
        for (i = 0; i < inputs.length; i++) {
            if (inputs[i].checked)
            {
                json_objU.role = inputs[i].value;
                break;
            }
        }
        var checkbtn = document.getElementById("check");
        if (checkbtn.checked) {
            json_objU.access = 1;
        } else {
            json_objU.access = 0;
        }
        json_obj['val'] = json_objU;
        json_obj['button'] = (button === 1) ? 'save' : 'cancel';
        var doc = document.documentElement;
        var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
        var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
        json_obj['pageXOffset'] = left;
        json_obj['pageYOffset'] = top;

        var msg = (button === 1) ? 'Сохранить данные?' : '';

//        var json_str = JSON.stringify(json_obj);
//        alert(json_str);

        doRequestHttpServer('update_user', json_obj, msg);
    } else {
        alert("Пароли не совпадают!");
    }

    //doRequestHttpServer('update', json_obj, '');
}


/**
 * empty
 * @param {type} msg
 * @returns {Boolean}
 */
function empty(msg) {
    // Determine whether a variable is empty
    //return (msg === "" || msg === 0 || msg === "0" || msg === null || msg === false || (is_array(msg) && msg.length === 0));
    return (msg === '');
}


/**
 * shineLinks
 * @param {type} id
 * @returns {undefined}
 */
function shineLinks(id) {

    try {

        var el = document.getElementById(id).getElementsByTagName('a');
        var url = document.location.href;
        for (var i = 0; i < el.length; i++) {

            if (url == el[i].href) {

                el[i].className = 'active';
            }
            ;
        }
        ;
    } catch (e) {
    }

}

/**
 * Keyboard input only number
 */
function Ftest(obj)
{
    if (this.ST)
        return;
    var ov = obj.value;
    var ovrl = ov.replace(/\d*\.?\d*/, '').length;
    this.ST = true;
    if (ovrl > 0) {
        obj.value = obj.lang;
        Fshowerror(obj);
        return
    }
    obj.lang = obj.value;
    this.ST = null;
}
/**
 * Fshowerror
 * @param {type} obj
 * @returns {Fshowerror}
 */
function Fshowerror(obj)
{
    if (!this.OBJ)
    {
        this.OBJ = obj;
        obj.style.backgroundColor = 'pink';
        this.TIM = setTimeout(Fshowerror, 50)
    } else
    {
        this.OBJ.style.backgroundColor = '';
        clearTimeout(this.TIM);
        this.ST = null;
        Ftest(this.OBJ);
        this.OBJ = null
    }
}

/**
 * Hide selected. It is used in the penalty and termination  form
 * @param {type} a
 * @param {type} id
 * @returns {undefined}
 */
function Selected(a, id) {

    var label = a;
    var tid = id;
    var br = navigator.userAgent;
    if (br.search(/MSIE/) > -1) {
        // code for IE6, IE5
        var block = 'block';
    } else {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        var block = 'table-cell';
    }
    if (tid == 'inp1')
    {
        if (label == 0) {
            document.getElementById("fio").style.display = 'none';
            document.getElementById("sel_fio").style.display = 'none';
            document.getElementById("name").style.display = 'none';
            document.getElementById("in_name").style.display = 'none';
            document.getElementById("err").style.display = 'none';
            document.getElementById('td0').rowSpan = 1;
            document.getElementById('td0').colSpan = 1;
        } else if (label == 1) {
            document.getElementById("fio").style.display = block;
            document.getElementById("sel_fio").style.display = block;
            document.getElementById("name").style.display = 'none';
            document.getElementById("in_name").style.display = 'none';
            document.getElementById("err").style.display = block;
            document.getElementById('td0').rowSpan = 2;
            document.getElementById('td0').colSpan = 1;
        } else if (label == 2) {
            document.getElementById("fio").style.display = 'none';
            document.getElementById("sel_fio").style.display = 'none';
            document.getElementById("name").style.display = block;
            document.getElementById("in_name").style.display = block;
            document.getElementById("err").style.display = block;
            document.getElementById('td0').rowSpan = 2;
            document.getElementById('td0').colSpan = 1;
        }

    }
    if (tid == 'inp6')
    {
        if (label == 0) {
            document.getElementById("td6").style.display = 'none';
            document.getElementById("td7").style.display = 'none';
            document.getElementById("td8").style.display = 'none';
            document.getElementById('td5').rowSpan = 2;
        } else if (label == 1) {
            document.getElementById("td6").style.display = block;
            document.getElementById("td7").style.display = block;
            document.getElementById("td8").style.display = block;
            document.getElementById('td5').rowSpan = 3;
        } else if (label == 2 || label == 3) {
            document.getElementById("td6").style.display = 'none';
            document.getElementById("td7").style.display = 'none';
            document.getElementById("td8").style.display = 'none';
            document.getElementById('td5').rowSpan = 2;
        }

    }
    if (tid == 'inp9')
    {
        if (label == 0) {
            document.getElementById("summa").style.display = 'none';
            document.getElementById("in_summa").style.display = 'none';
            document.getElementById("er_summa").style.display = 'none';
        } else if (label == 1) {
            document.getElementById("summa").style.display = block;
            document.getElementById("in_summa").style.display = block;
            document.getElementById("er_summa").style.display = block;
        } else if (label == 2) {
            document.getElementById("summa").style.display = 'none';
            document.getElementById("in_summa").style.display = 'none';
            document.getElementById("er_summa").style.display = 'none';
        }

    }
    if (tid == 'inp11') {
        if (label == 0 || label == 1 || label == 2) {
            document.getElementById("thn1_mro").style.display = 'none';
            document.getElementById("thn2_mro").style.display = 'none';
            document.getElementById("thn3_mro").style.display = 'none';
            document.getElementById("tdn1_mro").style.display = 'none';
            document.getElementById("tdn2_mro").style.display = 'none';
            document.getElementById("tdn3_mro").style.display = 'none';
            document.getElementById("tdn4_mro").style.display = 'none';
        } else if (label == 3) {
            document.getElementById("thn1_mro").style.display = block;
            document.getElementById("thn2_mro").style.display = block;
            document.getElementById("thn3_mro").style.display = block;
            document.getElementById("tdn1_mro").style.display = block;
            document.getElementById("tdn2_mro").style.display = block;
            document.getElementById("tdn3_mro").style.display = block;
            document.getElementById("tdn4_mro").style.display = block;
        }

    }
    if (tid == 'inform') {
        if (label == 1) {
            document.getElementById("t1_p").style.display = 'none';
            document.getElementById("t1_v").style.display = 'none';
            document.getElementById("t2_v").style.display = 'none';
            document.getElementById("t3_v").style.display = 'none';
            document.getElementById("t1_d").style.display = 'none';
            document.getElementById("t2_d").style.display = 'none';
            document.getElementById("t3_d").style.display = 'none';
            document.getElementById("t1_n").style.display = 'none';
            document.getElementById("t2_n").style.display = 'none';
            document.getElementById("t3_n").style.display = 'none';
            document.getElementById("t1_s").style.display = 'none';
            document.getElementById("t2_s").style.display = 'none';
            document.getElementById("t3_s").style.display = 'none';
        } else if (label == 0) {
            document.getElementById("t1_p").style.display = block;
            document.getElementById("t1_v").style.display = block;
            document.getElementById("t2_v").style.display = block;
            document.getElementById("t3_v").style.display = block;
            document.getElementById("t1_d").style.display = block;
            document.getElementById("t2_d").style.display = block;
            document.getElementById("t3_d").style.display = block;
            document.getElementById("t1_n").style.display = block;
            document.getElementById("t2_n").style.display = block;
            document.getElementById("t3_n").style.display = block;
            document.getElementById("t1_s").style.display = block;
            document.getElementById("t2_s").style.display = block;
            document.getElementById("t3_s").style.display = block;
        }

    }

}
/**
 * 
 * @returns {undefined}
 */
function doSelectPenalty() {
    var e = document.getElementById("inp1");
    var selIndex = e.options[e.selectedIndex].value;
    Selected(selIndex, 'inp1');
    e = document.getElementById("inp6");
    selIndex = e.options[e.selectedIndex].value;
    Selected(selIndex, 'inp6');
    e = document.getElementById("inp9");
    selIndex = e.options[e.selectedIndex].value;
    Selected(selIndex, 'inp9');
    return 1;
}
/**
 * Hide selected. It is used in the termination form
 */

function doSelectTerm() {
    var e = document.getElementById("inp1");
    var selIndex = e.options[e.selectedIndex].value;
    Selected(selIndex, 'inp1');
    e = document.getElementById("inp6");
    selIndex = e.options[e.selectedIndex].value;
    Selected(selIndex, 'inp6');
    return 1;
}
/**
 * Hide selected. It is used in the termination form
 */

function doSelectPrepare() {
    var el_view = document.getElementById('overlay_view');
    var el_quest = document.getElementById('overlay_quest');
    el_quest.style.visibility = "hidden";
    el_view.style.visibility = "hidden";
    return 1;
}
/**
 * Hide selected. It is used in the termination form
 */

function doSelectInspector() {
    var el = document.getElementById("overlay_user");
    el.style.visibility = "hidden";
    return 0;
}
/**
 * Hide selected. It is used in the review form
 */

function doSelectReview() {
    var e = document.getElementById("inp11");
    var selIndex = e.options[e.selectedIndex].value;
    Selected(selIndex, 'inp11');
    return 1;
}

/**
 * Hide selected. It is used in the review form
 */

function doSelectReview() {
    var e = document.getElementById("inp11");
    var selIndex = e.options[e.selectedIndex].value;
    Selected(selIndex, 'inp11');
    return 1;
}
/**
 * Hide selected. It is used in the inform form
 */

function doSelectInform() {
    var e = document.getElementById("nsi_9");
    var inp_num = document.getElementById("inp_num").value;
    var inp_date = document.getElementById("inp_date").value;
    var inp_date2 = document.getElementById("inp_date2").value;
    inp_num = (inp_num == '0') ? '' : inp_num;
    var selIndex = e.options[e.selectedIndex].value;
    var inp1 = document.getElementById("inp1").value;
    var inp2 = document.getElementById("inp2").value;
    var inp3 = document.getElementById("inp3").value;
    inp2 = (inp2 == '0') ? '' : inp2;
    inp3 = (inp3 == '0.00') ? '' : inp3;
    selIndex = (selIndex.toString() == '0') ? '' : selIndex.toString();
    var top_str = inp_num + inp_date + inp_date2;
    var bottom_str = selIndex + inp1 + inp2 + inp3;
//    alert(top_str);
//    alert(bottom_str);
    var flag = (empty(top_str) && empty(bottom_str)) ? 0 :
            (empty(top_str) && !empty(bottom_str)) ? 0 :
            (!empty(top_str) && empty(bottom_str)) ? 1 :
            (!empty(top_str) && !empty(bottom_str)) ? 0 : 1;
    Selected(flag, 'inform');
    return 1;
}

/**
 * onLoadForm
 * @param {type} json_data
 * @returns {undefined}
 */
function onLoadForm(json_data) {
    var json_obj = json_data;
    var arr = json_obj.val;
    if (json_obj.name_tbl != 'inform' && json_obj.name_tbl != 'book' && json_obj.name_tbl != 'user') {
        json_obj.val = arr.id; //set val to doc_id
        doRequestHttpServer('disabled_enabled', json_obj, '');
    }
    var nop = (json_obj.name_tbl == 'msg') ? 0 :
            (json_obj.name_tbl == 'act') ? 0 :
            (json_obj.name_tbl == 'prepare') ? doSelectPrepare() :
            (json_obj.name_tbl == 'review') ? doSelectReview() :
            (json_obj.name_tbl == 'penalty') ? doSelectPenalty() :
            (json_obj.name_tbl == 'termination') ? doSelectTerm() :
            (json_obj.name_tbl == 'inform') ? doSelectInform() :
            (json_obj.name_tbl == 'user') ? doSelectInspector() :
            (json_obj.name_tbl == 'book') ? doAdmin() : 0;
    if (nop == 1) {
        set_datestyle(arr); //set style of controls date    setCalendarPos('absolute');
    }
    setCalendarPos('absolute');
}

/**
 * testDate
 * @param {type} obj
 * @param {type} doc_id
 * @param {type} reg_date
 * @param {type} reg_num
 * @param {type} inspector
 * @returns {undefined}
 */
function testDate(obj, doc_id, reg_date, reg_num, fio_penalized, inspector) {
    var json_obj = {//JSON object
        val: "",
        reg_num: "",
        reg_date: "",
        fio_penalized: ""
    }
//    document.getElementById("reg_date").style.position = "fixed"
//    var x = document.getElementById("tcal").style.position = 'fixed';

//    el.position = "fixed";
    json_obj.val = {};
    json_obj.val['doc_id'] = doc_id;
    var doc = document.documentElement;
    var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
    var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
    json_obj.val['pageXOffset'] = left;
    json_obj.val['pageYOffset'] = top;
    switch (inspector) {
        case 1://Administrator
            if (obj.cellIndex == 0) {
                json_obj.reg_num = obj.innerHTML;
                json_obj.reg_date = reg_date;
                json_obj.fio_penalized = fio_penalized;
                var rows = document.getElementById('mytbl').getElementsByTagName('tr');
                for (var i = 0; i < rows.length; i++) {
                    var str1 = reg_num.toString();
                    var str2 = rows[i].cells[0].innerHTML.toString();
                    if (str1 === str2) {
                        var str3 = fio_penalized.toString();
                        var str4 = rows[i].cells[1].innerHTML.toString();
                        if (str3 === str4) {
                            var el = document.getElementById("row_" + i.toString());
                            el.style.backgroundColor = "#D93600";
                        }
                    }
                }
                addRegNum(json_obj);
            } else {
                doRequestHttpServer('goto', json_obj, '');
            }
            break;
        case 2://Admin MRO
            doRequestHttpServer('goto', json_obj, '');
            break;
    }
}

/**
 * addRegNum
 * @returns {undefined}
 */
function setCalendarPos(pos_attr) {
    var css_obj = null;
    if (typeof document.styleSheets[2].rules == 'undefined' && typeof document.styleSheets[2].cssRules != 'undefined')
        css_obj = document.styleSheets[2].cssRules;
    else if (typeof document.styleSheets[2].rules != 'undefined')
        css_obj = document.styleSheets[2].rules;
    if (css_obj)
        for (var i in css_obj) {
            if (css_obj[i].selectorText == '#tcal') {
                css_obj[i].style.cssText = 'position:' + pos_attr + ';' +
                        'visibility: hidden;' +
                        'z-index: 10001;' +
                        'width: 170px;' +
                        'background-color: white;' +
                        'margin-top: 2px;' +
                        'padding: 0 2px 2px 2px;' +
                        'border: 1px solid silver;';
            }
        }
    else
        alert('File is not found');
}
/**
 * addRegNum
 * @returns {undefined}
 */
function addRegNum(json_obj) {
//    var el = document.querySelector("#overlay_reg");
//    el.style.backgroundColor = "#D93600";
    var el = document.getElementById('overlay_reg');
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
    gDocId = json_obj.val['doc_id'];
    document.getElementById('reg_num').value = json_obj.reg_num;
    document.getElementById('reg_date').value = json_obj.reg_date;
    document.getElementById('fio_penalized').innerHTML = json_obj.fio_penalized;
    var pos_attr = (el.style.visibility == "visible") ? "fixed" : "absolute";

    setCalendarPos(pos_attr);
}
/**
 * saveRegNum
 * @returns {undefined}
 */
function saveRegNum(button) {
    var theDropDown = document.getElementById("dropDown");
    var json_obj = {//JSON object
        val: {},
        doc_id: "",
        reg_num: "",
        reg_date: ""
    };

    json_obj.val = {};
    json_obj.doc_id = gDocId;
    json_obj.reg_num = document.getElementById('reg_num').value;
    json_obj.reg_date = document.getElementById('reg_date').value;
    //var json_str = JSON.stringify(json_obj);
    var doc = document.documentElement;
    var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
    var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
    json_obj.val['pageXOffset'] = left;
    json_obj.val['pageYOffset'] = top;
    json_obj.val['button'] = (button === 1) ? 'save' : 'cancel';

    doRequestHttpServer('save_reg_num', json_obj, '');
}

/**
 * 
 * @returns {undefined}
 */
function doAdmin() {
    var el = document.getElementById('overlay_reg');
    el.style.visibility = "hidden";
    return 0;
}



function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

// "getElementsByClassName" не определен IE, 
// так что этот метод можно реализовать в JavaScript 
if (document.getElementsByClassName == undefined) {
    document.getElementsByClassName = function (cl) {
        var retnode = [];
        var myclass = new RegExp('\\b' + cl + '\\b');
        var elem = this.getElementsByTagName('*');
        for (var i = 0; i < elem.length; i++) {
            var classes = elem[i].className;
            if (myclass.test(classes)) {
                retnode.push(elem[i]);
            }
        }
        return retnode;
    }
}


