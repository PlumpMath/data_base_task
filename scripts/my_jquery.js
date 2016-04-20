/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* global gNewLabel */

var loader_image = "<img src='images/LoaderIcon.gif' />";
var left, top;
//_________________________________________________________________________
//
//
//                         Main function 
//
//_________________________________________________________________________

$(function () {
    var json_obj = {//JSON object
        val: '5',
        mon1: '1',
        mon2: '12',
        year: ''
    };
//_________________________________________________________________________
//
//
//                         Toolbar 
//
//_________________________________________________________________________

    if ($('div').is("#toolbar")) {
        var month_picker = $('#month_picker');
        month_picker.css('color', 'blue');
        month_picker.css('font-size', ' medium');
        month_picker.Zebra_DatePicker({
            format: 'm Y',
            months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            lang_clear_date: 'Очистить данные',
            firstDay: 1,
            onSelect: function (date) {
                var array = date.split(' ');
                json_obj.val = '3';
                json_obj.mon1 = array[0];
                json_obj.mon2 = array[0];
                json_obj.year = array[1];
                $('#form_a').html(loader_image);
                doRequestHttpServer('month_data', json_obj, '');
            }
        });
        $('#reg').click(function () {
            json_obj.val = '2';
            json_obj.mon1 = 0;
            json_obj.mon2 = 0;
            $('#form_a').html(loader_image);
            doRequestHttpServer('month_data', json_obj, '');
        });
        $('#year').click(function () {
            json_obj.val = '1';
            json_obj.mon1 = 1;
            json_obj.mon2 = 12;
            $('#form_a').html(loader_image);
            doRequestHttpServer('month_data', json_obj, '');
        });
        $("#year").button();
        $("#reg").button();
        $("#save").button();
        $("#load_excel").button();
        if ($('select').is("#mro")) {
            $("#mro").change(function () {
                json_obj.val = $("#mro").val();
                $('#form_a').html(loader_image);
                doRequestHttpServer('mro_changed', json_obj, '');
                // alert(mro.toString());
            });
        }
        if ($('select').is("#quarter")) {
            $("#quarter").change(function () {
                json_obj.val = $("#quarter").val();
                $('#form_a').html(loader_image);
                doRequestHttpServer('quarter_changed', json_obj, '');
                // alert(mro.toString());
            });
        }
        //jqgh_
        //                alert($("#jqgh_total").text('NAME'));

//_________________________________________________________________________
//
//
//                         Context menu 
//
//_________________________________________________________________________
        if ($('table').is("#mytbl")) {
            $.contextMenu({
                selector: '.context-menu-one',
                callback: function (key, options) {
                    var array = key.split('_');
                    var mon = array[1];
                    if (mon != '13') {
                        //var date = new Date();
                        json_obj.mon1 = mon;
                        json_obj.mon2 = mon;
                        //json_obj.year = date.getFullYear().toString();
                    }
                    $('#form_a').html(loader_image);
                    doRequestHttpServer('month_data', json_obj, '');
                },
                items: {
                    "mon_1": {name: "Январь", icon: "edit"},
                    "mon_2": {name: "Февраль", icon: "edit"},
                    "mon_3": {name: "Март", icon: "edit"},
                    "mon_4": {name: "Апрель", icon: "edit"},
                    "mon_5": {name: "Май", icon: "edit"},
                    "mon_6": {name: "Июнь", icon: "edit"},
                    "mon_7": {name: "Июль", icon: "edit"},
                    "mon_8": {name: "Август", icon: "edit"},
                    "mon_9": {name: "Сентябрь", icon: "edit"},
                    "mon_10": {name: "Октябрь", icon: "edit"},
                    "mon_11": {name: "Ноябрь", icon: "edit"},
                    "mon_12": {name: "Декабрь", icon: "edit"},
                    "sep1": "---------",
                    "year_13": {name: "Год", icon: "edit"},
                    "reg_0": {name: "Незарегистрированные", icon: "edit"}
                }
            });

        }
        if ($('table').is("#list,#list_new1,#list_new2")) {
            $.contextMenu({
                selector: '.context-menu-one',
                callback: function (key, options) {
                    var array = key.split('_');
                    var mon = array[1];
                    //var date = new Date();
                    json_obj.mon1 = mon;
                    json_obj.mon2 = mon;
                    //json_obj.year = date.getFullYear().toString();
                    $('#form_a').html(loader_image);
                    doRequestHttpServer('month_data', json_obj, '');
                },
                items: {
                    "mon_1": {name: "Январь", icon: "edit"},
                    "mon_2": {name: "Февраль", icon: "edit"},
                    "mon_3": {name: "Март", icon: "edit"},
                    "mon_4": {name: "Апрель", icon: "edit"},
                    "mon_5": {name: "Май", icon: "edit"},
                    "mon_6": {name: "Июнь", icon: "edit"},
                    "mon_7": {name: "Июль", icon: "edit"},
                    "mon_8": {name: "Август", icon: "edit"},
                    "mon_9": {name: "Сентябрь", icon: "edit"},
                    "mon_10": {name: "Октябрь", icon: "edit"},
                    "mon_11": {name: "Ноябрь", icon: "edit"},
                    "mon_12": {name: "Декабрь", icon: "edit"}
                }
            });
            $.contextMenu({
                selector: '.context-menu',
                callback: function (key, options) {
                    var array = key.split('_');
                    json_obj.val = array[1];
                    $('#form_a').html(loader_image);
                    doRequestHttpServer('quarter_changed', json_obj, '');
                },
                items: {
                    "kv_1": {name: "1-квартал", icon: "edit"},
                    "kv_2": {name: "2-квартал", icon: "edit"},
                    "kv_3": {name: "3-квартал", icon: "edit"},
                    "kv_4": {name: "4-квартал", icon: "edit"}
                }
            });
        }

    }




//_________________________________________________________________________
//
//
//                         Tooltip 
//
//_________________________________________________________________________

    if ($('form').is("#frm_login")) {
        $("#name, #password").tooltip({// place tooltip on the right edge
// use the built-in fadeIn/fadeOut effect
            effect: "fade"
        });
    }


//_________________________________________________________________________
//
//
//                         Report 
//
//_________________________________________________________________________

    if ($('table').is("#list")) {

        var grid = $("#list");
//_________________________________________________________________________
//
//
//                         jqGrid for the report 
//
//_________________________________________________________________________
        grid.jqGrid({
//            url: 'http://localhost:7070/projects/php1/load_report.php',
            url: 'load_report.php',
            datatype: "json",
            mtype: "POST",
            colNames: ["п/п", "Наименование", "Наименование",
                'физ.лицо', 'юр.лицо', 'долж.лицо',
                'физ.лицо', 'юр.лицо', 'долж.лицо',
                'физ.лицо', 'юр.лицо', 'долж.лицо',
                'физ.лицо', 'юр.лицо', 'долж.лицо',
                'физ.лицо', 'юр.лицо', 'долж.лицо',
                "Итого за месяц", "С нарастающим итогом"],
            colModel: [
                {name: "npp", width: 40, align: "center"},
                {name: "group_name", width: 270},
                {name: "name", width: 280, classes: "cvname"},
                {name: "st203f", width: 70, align: "right", classes: "col_color", decimalPlaces: 2, formatter: currencyFormatter, editable: true},
                {name: "st203u", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st203d", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2010f", width: 70, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2010u", width: 70, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2010d", width: 70, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2011f", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2011u", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2011d", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2012f", width: 70, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2012u", width: 70, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2012d", width: 70, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2335f", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2335u", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2335d", width: 70, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "total", width: 100, align: "right", classes: "cvtotal", formatter: currencyFormatter},
                {name: "total_cumulative", width: 100, align: "right", formatter: currencyFormatter}
            ],
            cmTemplate: {sortable: false},
            rowNum: 45,
            //width: null,
            shrinkToFit: true,
            autowidth: true,
            rowList: [15, 30, 45],
            pager: '#pager',
//            sortname: 'npp',
            viewrecords: true,
//            sortorder: "desc",
            caption: "ФОРМА 2",
            height: '100%',
            loadonce: true,
            grouping: true,
            groupingView: {
                groupField: ['group_name'],
                groupColumnShow: [false]
            },
            rowattr: function (rd) {
                switch (rd.name) {
                    case "ЗАЯВЛЕНИЯ и СООБЩЕНИЯ":
                        return {"class": "rowcss"};
                    default:
                        return {};
                }
            },
            //           afterInsertRow: function (rowid, aData) {
//                if (
//                        aData.group_name == "ЗАЯВЛЕНИЯ и СООБЩЕНИЯ"
//                        ) {
//                    grid.jqGrid('setRowData', rowid, false, "rowcss");
//                    grid.setCell(rowid, "st203f", "DONE ", null, null, true);
//                } else {
            //   grid.setCell(rowid, "total", grid.getCell(rowid, "total"), "cvteste", null, true);

//                }

//            },
//loadComplete: function() {
//    var grid = $("list");
//    var ids = grid.getDataIDs();
//    for (var i = 0, idCount = ids.length; i < idCount; i++) {
//        grid.setCell(id, 'myname', 'My text for link');
//    }
//},
            loadComplete: function () {
                var i, group, cssClass, headerIdPrefix = this.id + "ghead_",
                        groups = $(this).jqGrid("getGridParam", "groupingView").groups,
                        l = groups.length;
                for (i = 0; i < l; i++) {
                    group = groups[i];
                    switch (group.value) {
                        case "ЗАЯВЛЕНИЯ и СООБЩЕНИЯ":
                        case "ПРОТОКОЛЫ":
                        case "ОПРЕДЕЛЕНИЯ":
                        case "ПОСТАНОВЛЕНИЯ":
                        case "ШТРАФЫ":
                        case "ЖАЛОБЫ":
                        case "":
                            cssClass = "rowcss";
                            break;
                        default:
                            cssClass = "";
                            break;
                    }
                    // listghead_0_1
                    if (cssClass !== "") {
                        $("#" + headerIdPrefix + group.idx + "_" + i).addClass(cssClass);
                    }
                }
//                var colPos = 5;
//                $(this).jqGrid('hideCol', $(this).getGridParam("colModel")[colPos].name);
                $(this).find('TR.jqgrow:eq(0)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(1)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(4)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(5)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(6)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(7)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(12)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(13)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(14)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(16)').addClass('row_color');
//                $(this).find('TR.jqgrow:eq(23)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(24)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(25)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(26)').addClass('row_color');
                $(this).find('TR.jqgrow:eq(27)').addClass('row_color');
//                $(this).find('TR.jqgrow[editable=11]').addClass('row_color');
//                $(this).find('TR.jqgrow[editable=1]')

                var rowids = $(this).jqGrid('getDataIDs');
                for (var i = 0; i < rowids.length; i++)
                {
//                    var rowid = rowids[i];
//                    var data = $(this).getRowData(rowid);
                    if (i == 1 || i == 3 || i == 5 || i == 9 || i == 13 || i == 18 || i == 25) {
                        $(this).jqGrid('setRowData', i, false, 'mybold');
                    }
                }
                var cm = $(this).jqGrid('getGridParam', 'colModel'), l = cm.length, j;
                for (var j = 0; j < l; j++) {
                    changeEditableByRow($(this), cm[j].name, 15);
                }

            }
            ,
            cellEdit: true,
            cellsubmit: 'clientArray',
            jsonReader: {
                repeatitems: false,
                page: function () {
                    return 1;
                },
                root: function (obj) {
                    return obj;
                },
                records: function (obj) {
                    return obj.length;
                }
            }
        });

//_________________________________________________________________________
//
//
//                         Set group header 
//
//_________________________________________________________________________

        grid.jqGrid("setGroupHeaders", {
            useColSpanStyle: true,
            groupHeaders: [
                {startColumnName: 'st203f', numberOfColumns: 3, titleText: '<em>Статья 20.3</em>'},
                {startColumnName: 'st2010f', numberOfColumns: 3, titleText: '<em>Статья 20.10</em>'},
                {startColumnName: 'st2011f', numberOfColumns: 3, titleText: '<em>Статья 20.11</em>'},
                {startColumnName: 'st2012f', numberOfColumns: 3, titleText: '<em>Статья 20.12</em>'},
                {startColumnName: 'st2335f', numberOfColumns: 3, titleText: '<em>Статья 23.35</em>'}
            ]
        });
        gridcsvexport("list");
        grid.setGridWidth($(window).width() - 67);
        //  grid.jqGrid('setRowData', 7, false, 'mybold');




//_________________________________________________________________________
//
//
//                         $("#save").click(function (){}) 
//
//_________________________________________________________________________

        $("#save").click(function () {
            json_obj.val = {};
            json_obj.val['mro'] = '';
            json_obj.val['rep_date'] = $('#month_picker').val();
            var rowids = grid.jqGrid('getDataIDs');
            for (var i = 0; i < rowids.length; i++) {
                var rowid = rowids[i];
                var data = grid.getRowData(rowid);
                var cm = grid.jqGrid('getGridParam', 'colModel');
                if (i === 15) {
                    for (var j = 0; j < cm.length; j++) {
                        if (j > 2 && j < 18) {
                            var name = cm[j].name;
                            var text = data[cm[j].name];
                            json_obj.val[name] = text.replace(/\s+/g, '');
//                            json_obj.val[name] = text.replace(/\D/gi, '').replace(/^0+/, '');
                            //alert(Number(json_obj.val[name]));
                        }
                    }
                    json_obj.val['row_id'] = data[cm[0].name];
                }
            }
            doRequestHttpServer('save_report', json_obj, '');
        });
//_________________________________________________________________________
//
//
//                         $("#load_excel").click(function (){}) 
//
//_________________________________________________________________________
        $("#load_excel").click(function () {
//            jQuery("#list").jqGrid('excelExport', { url: 'ExportExcel.xls' });
            exportExcel($("#list"));
//            fnExcelReport();
        });
    }


//
//_________________________________________________________________________
//
//
//                         Public function 
//
//_________________________________________________________________________

//_________________________________________________________________________
//
//
//                         Formatter 
//
//_________________________________________________________________________

// Custom formatter that ceils all number values 
    function currencyFormatter(cellvalue, options, rowObject) {
        // Ceil the number value if any decimal numbers are present
        var value = Number(cellvalue).toString();
        if (value === '0')
            return '';
        var color;
        if (value > 1000) {
            color = 'green';
        }

        // Loop through all thousands and add a space
        value = value.replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g, "\$1 ");
//            return  '<span class="cellWithoutBackground" style="color:' + color + ';">' + value + '</span>';

        return  value;
//        return '<span style="color:green">' + value + '</span>';
    }


//_________________________________________________________________________
//
//
//                         Get column name by index 
//
//_________________________________________________________________________

    var getColumnIndexByName = function (gr, columnName) {
        var cm = gr.jqGrid('getGridParam', 'colModel');
        for (var i = 0, l = cm.length; i < l; i++) {
            if (cm[i].name === columnName) {
                return i; // return the index
            }
        }
        return -1;
    };

//_________________________________________________________________________
//
//
//                         Change editable by row 
//
//_________________________________________________________________________

    var changeEditableByRow = function (gr, colName, colNum) {
        var pos = getColumnIndexByName(gr, colName);
        // nth-child need 1-based index so we use (i+1) below
        var cells = $("tbody > tr.jqgrow > td:nth-child(" + (pos + 1) + ")", gr[0]);
        for (var i = 0; i < cells.length; i++) {
            var cell = $(cells[i]);
            if (i != colNum) {
                cell.addClass('not-editable-cell');
            } else {
                var rowids = grid.jqGrid('getDataIDs');
//                    gr.jqGrid('editRow', rowids[i], true);
                var selRow = gr.getGridParam('selrow');
                var recNum = gr.getGridParam('reccount');
//                    if (selRow == null && recNum > 0) {
//                        $(this).editCell(15, pos, true);
//                    }
                gr.jqGrid('setRowData', i + 1, false, 'mybold');
                cell.addClass('row_force');
            }
        }
    }





//_________________________________________________________________________
//
//
//                          Set order
//
//_________________________________________________________________________

    if ($('table').is("#mytbl")) {
        if ($('th').is("#reg_num_insp")) {
            $("#reg_num_insp").click(function () {
                $("#reg_num_insp").css('color', 'red');
                $("#fio_insp").css('color', 'white');
                json_obj.val = {};
                json_obj.val['order'] = 'reg_num1';
                doRequestHttpServer('set_order', json_obj, '');
            });
        }
        if ($('th').is("#fio_insp")) {
            $("#fio_insp").click(function () {
                $("#reg_num_insp").css('color', 'white');
                $("#fio_insp").css('color', 'red');
                json_obj.val = {};
                json_obj.val['order'] = 'name_i,to_who';
                doRequestHttpServer('set_order', json_obj, '');
            });
        }

        if ($('span').is("#sel_date")) {
            $("#sel_date").css('color', 'blue');
        }
        var session_pageX = document.getElementById('sess_pageX').value;
        var session_pageY = document.getElementById('sess_pageY').value;
        window.scroll(session_pageX, session_pageY);
        $(document).on("mousemove", function (event) {
//     $("#log").text("pageX: " + event.pageX + ", pageY: " + event.pageY);
        });
    }
//var doc = document.documentElement;
//var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
//var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
//$i = document.documentElement.scrollHeight;
// window.scroll(0, $i);
//_________________________________________________________________________
//
//
//                          Set position
//
//_________________________________________________________________________

    if ($('table').is("#usertbl")) {
        var session_pageX = document.getElementById('sess_pageX').value;
        var session_pageY = document.getElementById('sess_pageY').value;
        window.scroll(session_pageX, session_pageY);
        $(document).on("mousemove", function (event) {
//     $("#log").text("pageX: " + event.pageX + ", pageY: " + event.pageY);
        });
    }


//_________________________________________________________________________
//
//
//                          Export to excel
//
//_________________________________________________________________________


    /**
     * export to excel
     * @param {type} $id
     * @returns {undefined}
     */
    function exportExcel($id) {
        var keys = [], ii = 0, rows = {}, json_obj = {};
        var ids = $id.getDataIDs(); // Get All IDs
        var row = $id.getRowData(ids[0]); // Get First row to get the labels
        for (var k in row) {
            keys[ii++] = k; // capture col names
            rows[k] = [];
        }
        for (i = 0; i < ids.length; i++) {
            row = $id.getRowData(ids[i]); // get each row
            for (j = 0; j < keys.length; j++)
                rows[keys[j]][i] = row[keys[j]]; // output each Row as tab delimited
        }
        json_obj['rows'] = rows;
        var colNames = $id.jqGrid('getGridParam', 'colNames');
        json_obj['col_names'] = colNames;
        var i, group = [], groups = $id.jqGrid("getGridParam", "groupingView").groups,
                l = groups.length;
        for (i = 0; i < l; i++) {
            group[i] = groups[i].value;
        }
        json_obj['group'] = group;
        var json_str = JSON.stringify(json_obj);
        var form = "<form name='csvexportform' action='excelExport.php' method='post'>";
        form = form + "<input type='hidden' name='csvBuffer' value='" + json_str + "'>";
        form = form + "</form><script>document.csvexportform.submit();</sc" + "ript>";
        OpenWindow = window.open('', '');
        OpenWindow.document.write(form);
        OpenWindow.document.close();
    }
//_________________________________________________________________________
//
//
//                         gridcsvexport
//
//_________________________________________________________________________

    /**
     * gridcsvexport
     * @param {type} id
     * @returns {undefined}
     */
    function gridcsvexport(id) {
        $('#' + id).jqGrid('navGrid', '#pager', {view: true, del: false, add: false, edit: false, excel: true})
                .navButtonAdd('#pager', {
                    caption: "Export to Excel",
                    buttonicon: "ui-icon-save",
                    onClickButton: function () {
                        if ($('table').is("#list")) {
                            exportExcel($("#list"));
                        }
                    },
                    position: "last"
                });
    }




//_________________________________________________________________________
//
//
//                          NSI
//
//_________________________________________________________________________


//menu NSI
    /**
     * 
     */

//MRO
    $("#li_mro").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(101);
    });
//Inspections
    $("#li_inspection").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(102);
    });
//holidays
    $("#li_holidays").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(103);
    });
//weekends
    $("#li_weekends").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(104);
    });
    //NSI_1
    $("#li_nsi_1").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(1);
    });
    //NSI_2
    $("#li_nsi_2").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(2);
    });
    //NSI_2
    $("#li_nsi_3").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(3);
    });
    //NSI_2
    $("#li_nsi_4").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(4);
    });
    //NSI_2
    $("#li_nsi_5").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(5);
    });
    //NSI_2
    $("#li_nsi_6").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(6);
    });
    //NSI_2
    $("#li_nsi_7").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(7);
    });
    //NSI_2
    $("#li_nsi_8").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(8);
    });
    //NSI_2
    $("#li_nsi_9").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(9);
    });
    //NSI_2
    $("#li_nsi_10").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(10);
    });
    //NSI_2
    $("#li_nsi_11").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(11);
    });
    //NSI_2
    $("#li_nsi_12").click(function (e) {
        $('#form_a').html(loader_image);
        callAJAX(12);
    });
//_________________________________________________________________________
//
//
//                          AJAX  NSI
//
//_________________________________________________________________________

//call_ajax
    function callAJAX(id_num) {
        var name_fld = 'name_' + id_num.toString(), mydata = [], grid;
//_________________________________________________________________________
//
//
//                          onclickSubmitLocal
//
//_________________________________________________________________________
        var onclickSubmitLocal = function (options, postdata) {
            var $this = grid, p = grid.jqGrid("getGridParam"), // p = this.p,
                    grid_p = grid[0].p,
                    idname = p.prmNames.id,
                    id = this.id,
                    idInPostdata = id + "_id",
                    rowid = postdata[idInPostdata],
                    addMode = rowid === "_empty",
                    oldValueOfSortColumn,
                    newId,
                    idOfTreeParentNode;
            // postdata has row id property with another name. we fix it:
            if (addMode) {
                // generate new id
//                newId = $.jgrid.randId();
                if (grid_p.records == 0) {
                    newId = 1;
                } else {
                    var r_id, r_id0 = 0;
                    var rowids = grid.jqGrid('getDataIDs');
                    for (var i = 0; i < rowids.length; i++) {
                        var rowid = rowids[i];
                        var data = grid.getRowData(rowid);
                        r_id = parseInt(data['id']);
                        if (r_id > r_id0) {
                            r_id0 = r_id;
                        } else {
                            r_id = r_id0;
                        }
                    }
                    newId = r_id + 1;
                }
                while ($("#" + newId).length !== 0) {
//                    newId = $.jgrid.randId();
                    newId++;
                }
                //               postdata[idname] = String(newId);
            } else if (postdata[idname] === undefined) {
                // set id property only if the property not exist
                postdata[idname] = rowid;
            }
            delete postdata[idInPostdata];
            // prepare postdata for tree grid
            if (p.treeGrid === true) {
                if (addMode) {
                    idOfTreeParentNode = p.treeGridModel === "adjacency" ? p.treeReader.parent_id_field : "parent_id";
                    postdata[idOfTreeParentNode] = p.selrow;
                }

                $.each(p.treeReader, function () {
                    if (postdata.hasOwnProperty(this)) {
                        delete postdata[this];
                    }
                });
            }

            // decode data if there encoded with autoencode
            if (p.autoencode) {
                $.each(postdata, function (n, v) {
                    postdata[n] = $.jgrid.htmlDecode(v); // TODO: some columns could be skipped
                });
            }

            // save old value from the sorted column
            oldValueOfSortColumn = p.sortname === "" ? undefined : $this.jqGrid("getCell", rowid, p.sortname);
            // save the data in the grid
            if (p.treeGrid === true) {
                if (addMode) {
                    $this.jqGrid("addChildNode", newId, p.selrow, postdata);
                } else {
                    $this.jqGrid("setTreeRow", rowid, postdata);
                }
            } else {
                if (addMode) {
                    $this.jqGrid("addRowData", newId, postdata, options.addedrow);
                } else {
                    $this.jqGrid("setRowData", rowid, postdata);
                }
            }

            if ((addMode && options.closeAfterAdd) || (!addMode && options.closeAfterEdit)) {
                // close the edit/add dialog
                $.jgrid.hideModal("#editmod" + $.jgrid.jqID(id), {
                    gb: "#gbox_" + $.jgrid.jqID(id),
                    jqm: options.jqModal,
                    onClose: options.onClose
                });
            }

            if (postdata[p.sortname] !== oldValueOfSortColumn) {
                // if the data are changed in the column by which are currently sorted
                // we need resort the grid
                setTimeout(function () {
                    $this.trigger("reloadGrid", [{current: true}]);
                    for (var i = 0; i < mydata.length; i++)
                    {
                        grid.jqGrid('setRowData', i + 1, mydata[i]);
                    }
                    var rowids = grid.jqGrid('getDataIDs');
                    for (var i = 0; i < rowids.length; i++) {
                        var rowid = rowids[i];
                        mydata[i] = grid.getRowData(rowid);
                    }


                }, 100);
            }

            // !!! the most important step: skip ajax request to the server
            options.processing = true;
            return {};
        },
//_________________________________________________________________________
//
//
//                          editSettings
//
//_________________________________________________________________________

                editSettings = {
                    //recreateForm: true,
                    jqModal: false,
                    reloadAfterSubmit: false,
                    closeOnEscape: true,
                    savekey: [true, 13],
                    closeAfterEdit: true,
                    onclickSubmit: onclickSubmitLocal
                },
//_________________________________________________________________________
//
//
//                         addSettings
//
//_________________________________________________________________________
        addSettings = {
            //recreateForm: true,
//            serializeEditData: function (data) {
//                return $.param($.extend({}, data, {id: 0}));
//            },
            jqModal: false,
            reloadAfterSubmit: false,
            savekey: [true, 13],
            closeOnEscape: true,
            closeAfterAdd: true,
            onclickSubmit: onclickSubmitLocal
        },
//_________________________________________________________________________
//
//
//                          delSettings
//
//_________________________________________________________________________
        delSettings = {
            // because I use "local" data I don't want to send the changes to the server
            // so I use "processing:true" setting and delete the row manually in onclickSubmit
//_________________________________________________________________________
//
//
//                          onclickSubmit
//
//_________________________________________________________________________
            onclickSubmit: function (options, rowid) {
                var $this = $(this), id = $.jgrid.jqID(this.id), p = this.p,
                        newPage = p.page;
                // reset the value of processing option to true to
                // skip the ajax request to "clientArray".
                options.processing = true;
                // delete the row
                $this.jqGrid("delRowData", rowid);
                if (p.treeGrid) {
                    $this.jqGrid("delTreeNode", rowid);
                } else {
                    $this.jqGrid("delRowData", rowid);
                }
                $.jgrid.hideModal("#delmod" + id, {
                    gb: "#gbox_" + id,
                    jqm: options.jqModal,
                    onClose: options.onClose
                });
                if (p.lastpage > 1) {// on the multipage grid reload the grid
                    if (p.reccount === 0 && newPage === p.lastpage) {
                        // if after deliting there are no rows on the current page
                        // which is the last page of the grid
                        newPage--; // go to the previous page
                    }
                    // reload grid to make the row from the next page visable.
                    $this.trigger("reloadGrid", [{page: newPage}]);
                }

                return true;
            },
            processing: true
        };

//_________________________________________________________________________
//
//
//                          jQuery.ajax
//
//_________________________________________________________________________

        jQuery.ajax({
            url: "nsi_read.php",
            type: "POST",
            data: ({id: id_num}),
            dataType: "json",
            async: false,
            success: function (data) {
                var capt, col_names, col_model;
                if (id_num < 100) {
                    capt = 'Справочник №' + id_num.toString();
                    col_names = ['Номер', 'Название'];
                    col_model = [
                        {name: 'id', index: 'id', width: 70, align: 'center', sorttype: 'int', searchoptions: {sopt: ['eq', 'ne']}},
                        {name: name_fld, index: name_fld, width: 700, editable: true}
                    ];
                } else if (id_num === 101) {
                    capt = 'МРО';
                    col_names = ['Номер', 'Название', 'Код инспекции'];
                    col_model = [
                        {name: 'id', index: 'id', width: 60, sorttype: "int"},
                        {name: 'name', index: 'name', width: 700, editable: true},
                        {name: 'id_inspection', index: 'id_inspection', width: 60, editable: true}
                    ];
                } else if (id_num === 102) {
                    capt = 'Районные инспекции';
                    col_names = ['Номер', 'Название', 'Код МРО'];
                    col_model = [
                        {name: 'id', index: 'id', width: 60, sorttype: "int"},
                        {name: 'name_i', index: 'name_i', width: 700, editable: true},
                        {name: 'id_mro', index: 'id_mro', width: 60, editable: true}
                    ];
                } else if (id_num === 103) {
                    capt = 'Праздничные дни';
                    col_names = ['Номер', 'Название', 'Число', 'Месяц'];
                    col_model = [
                        {name: 'id', index: 'id', width: 60, sorttype: "int"},
                        {name: 'name', index: 'name', width: 200, editable: true},
                        {name: 'day', index: 'day', width: 60, editable: true},
                        {name: 'month', index: 'month', width: 60, editable: true}
                    ];
                } else if (id_num === 104) {
                    capt = 'Перенос рабочих дней';
                    col_names = ['Номер', 'Выходной день', 'Рабочий день'];
                    col_model = [
                        {name: 'id', index: 'id', width: 60, sorttype: "int"},
                        {name: 'weekend_day', index: 'weekend_day', width: 200, editable: true},
                        {name: 'working_day', index: 'working_day', width: 200, editable: true}
                    ];
                }
                $("#bodycontent").html('<h3>Справочники</h3>');
                $("#toolbar").html('<button id="save_spr">Занести в базу данных</button>');
                $('#form_a').html('<table id="list"><tr><td></td></tr></table><div id="pager"></div>');
                $("#save_spr").button();
                grid = $("#list");
//_________________________________________________________________________
//
//
//                         jqGrid   NSI
//
//_________________________________________________________________________

                grid.jqGrid({
//                    editurl: "dummy.php",
                    datatype: 'local',
                    data: mydata,
                    colNames: col_names,
                    colModel: col_model,
//                    cmTemplate: {editable: true, searchoptions: {clearSearch: false}},
                    rowNum: 20,
                    rowList: [5, 10, 20],
//                    shrinkToFit: true,
                    autowidth: true,
                    pager: "#pager",
                    gridview: true,
//                    rownumbers: true,
                    autoencode: true,
                    ignoreCase: true,
                    sortname: "id",
                    viewrecords: true,
//                    sortorder: "desc",
//                    caption: "Demonstrates implementating of local form editing",
                    caption: capt,
                    height: "100%",
                    editurl: "clientArray",
                    ondblClickRow: function (rowid) {
                        var $this = $(this), p = this.p;
                        if (p.selrow !== rowid) {
                            // prevent the row from be unselected on double-click
                            // the implementation is for "multiselect:false" which we use,
                            // but one can easy modify the code for "multiselect:true"
                            $this.jqGrid("setSelection", rowid);
                        }
                        $this.jqGrid("editGridRow", rowid, editSettings);
                    }

                });//end jqGrid
//_________________________________________________________________________
//
//
//                        fill jqGrid   NSI with data
//
//_________________________________________________________________________

//               alert(name_fld);
                for (var i = 0; i <= data.length; i++)
                {
                    grid.jqGrid('addRowData', i + 1, data[i]);
                    mydata[i] = data[i];
                }
                grid.setGridWidth($(window).width() - 50);
                grid.jqGrid("navGrid", "#pager", {}, editSettings, addSettings, delSettings, {
//                    multipleSearch: true,
//                    overlay: false,
                });
            },
            error: function () {}
        });// end jQuery.ajax

//_________________________________________________________________________
//
//
//                         $("#save_spr").click
//
//_________________________________________________________________________

        $("#save_spr").click(function () {
            var data = [];
            var rowids = grid.jqGrid('getDataIDs');
            for (var i = 0; i < rowids.length; i++) {
                var rowid = rowids[i];
                data[i] = grid.getRowData(rowid);
            }
//            alert(data_array);
            var data_array = JSON.stringify(data);
            save_nsi(data_array);
        });

//_________________________________________________________________________
//
//
//                         put NSI data  into the base
//
//_________________________________________________________________________

        /**
         * save_nsi
         * @param {type} data_array
         * @returns {undefined}
         */
        function save_nsi(data_array) {
            jQuery.ajax({
                url: "nsi_save.php",
                type: "POST",
                data: {myJson: data_array},
                dataType: "json",
                async: false,
                success: function (data) {
//                   var array = JSON.stringify(data);
                    alert('Данные успешено занесны в базу!');
                },
                error: function () {}
            });
        }
    }

//_________________________________________________________________________
//
//
//                          New report 2
//
//_________________________________________________________________________

    if ($('table').is("#list_new2")) {
        var grid = $("#list_new2");
        var col_width = 70;
//_________________________________________________________________________
//
//
//                         jqGrid for the report 2 
//
//_________________________________________________________________________
        grid.jqGrid({
            url: 'load_report.php',
            datatype: "json",
            mtype: "POST",
            colNames: ["п/п", "Наименование", "Наименование",
                'ФЛ', 'ЮЛ', 'ИП',
                'ФЛ',
                'ФЛ', 'ЮЛ', 'ИП',
                'ФЛ', 'ЮЛ', 'ИП',
                'ФЛ',
                'ФЛ',
                'ФЛ',
                'ФЛ',
                'ФЛ',
                'ФЛ',
                'ФЛ',
                'ИП',
                'ФЛ',
                'ФЛ',
                'ФЛ',
                "Итого за месяц", "С нарастающим итогом"],
            colModel: [
                {name: "npp", width: 40, align: "center"},
                {name: "group_name", width: 270},
                {name: "name", width: 380, classes: "cvname"},
                {name: "st203f", width: col_width, align: "right", classes: "col_color", decimalPlaces: 2, formatter: currencyFormatter, editable: true},
                {name: "st203u", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st203p", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2010f", width: col_width, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2011f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2011u", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2011p", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2012f", width: col_width, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2012u", width: col_width, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st2012p", width: col_width, align: "right", formatter: currencyFormatter, editable: true},
                {name: "st231f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st232f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st233f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st234f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st235f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2335f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2361f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st2361p", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st244f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st245f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "st246f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true},
                {name: "total", width: 100, align: "right", classes: "cvtotal", formatter: currencyFormatter},
                {name: "total_cumulative", width: 100, align: "right", formatter: currencyFormatter}
            ],
            cmTemplate: {sortable: false},
            rowNum: 45,
            //width: null,
            shrinkToFit: true,
            autowidth: true,
            rowList: [15, 30, 45],
            pager: '#pager',
//            sortname: 'npp',
            viewrecords: true,
//            sortorder: "desc",
            caption: "ФОРМА 2",
            height: '100%',
            loadonce: true,
            grouping: true,
            ondblClickRow: function (rowId, iRow, iCol, e) {
                //               alert(rowId + ' ' + iRow + ' ' + iCol);
                var cm = $(this).jqGrid('getGridParam', 'colModel');
                var column_name = cm[iCol].name;
                var cell_val = parseFloat($(this).jqGrid('getCell', rowId, column_name));
                if (iCol > 2 && iCol < 24) {
                    if (cell_val > 0) {
                        getContextReportData(rowId, iCol);
                    }
                }
            },
            groupingView: {
                groupField: ['group_name'],
                groupColumnShow: [false]
            },
            rowattr: function (rd) {
                switch (rd.name) {
                    case "ЗАЯВЛЕНИЯ и СООБЩЕНИЯ":
                        return {"class": "rowcss"};
                    default:
                        return {};
                }
            },
            loadComplete: function () {
                var i, group, cssClass, headerIdPrefix = this.id + "ghead_",
                        groups = $(this).jqGrid("getGridParam", "groupingView").groups,
                        l = groups.length;
                for (i = 0; i < l; i++) {
                    group = groups[i];
                    switch (group.value) {
                        case "АДМИНИСТРАТИВНОЕ ПРОИЗВОДСТВО":
                        case "ШТРАФЫ":
                        case "ЖАЛОБЫ":
                            cssClass = "rowcss";
                            break;
                        default:
                            cssClass = "";
                            break;
                    }
                    // listghead_0_1
                    if (cssClass !== "") {
                        $("#" + headerIdPrefix + group.idx + "_" + i).addClass(cssClass);
                    }
                }
//                var colPos = 5;
//                $(this).jqGrid('hideCol', $(this).getGridParam("colModel")[colPos].name);
//                $(this).find('TR.jqgrow:eq(0)').addClass('row_color');
//                $(this).find('TR.jqgrow:eq(23)').addClass('row_color');
//                $(this).find('TR.jqgrow[editable=11]').addClass('row_color');
//                $(this).find('TR.jqgrow[editable=1]')

                var rowids = $(this).jqGrid('getDataIDs');
                for (var i = 0; i < rowids.length; i++)
                {
//                    var rowid = rowids[i];
//                    var data = $(this).getRowData(rowid);
                    if (i == 1 || i == 9 || i == 14) {
                        $(this).jqGrid('setRowData', i, false, 'mybold');
                    }
                }
                var cm = $(this).jqGrid('getGridParam', 'colModel'), l = cm.length, j;
                for (var j = 0; j < l; j++) {
                    changeEditableByRow($(this), cm[j].name, 11);
                }

            }
            ,
            cellEdit: true,
            cellsubmit: 'clientArray',
            jsonReader: {
                repeatitems: false,
                page: function () {
                    return 1;
                },
                root: function (obj) {
                    return obj;
                },
                records: function (obj) {
                    return obj.length;
                }
            }
        });
        set_label(1, grid);
        grid.jqGrid("setGroupHeaders", {
            useColSpanStyle: true,
            groupHeaders: [
                {startColumnName: 'st203f', numberOfColumns: 3, titleText: '<em>Статья 20.3</em>'},
                {startColumnName: 'st2010f', numberOfColumns: 1, titleText: '<em>Статья 20.10</em>'},
                {startColumnName: 'st2011f', numberOfColumns: 3, titleText: '<em>Статья 20.11</em>'},
                {startColumnName: 'st2012f', numberOfColumns: 3, titleText: '<em>Статья 20.12</em>'},
                {startColumnName: 'st231f', numberOfColumns: 1, titleText: '<em>Статья 23.1</em>'},
                {startColumnName: 'st232f', numberOfColumns: 1, titleText: '<em>Статья 23.2</em>'},
                {startColumnName: 'st233f', numberOfColumns: 1, titleText: '<em>Статья 23.3</em>'},
                {startColumnName: 'st234f', numberOfColumns: 1, titleText: '<em>Статья 23.4</em>'},
                {startColumnName: 'st235f', numberOfColumns: 1, titleText: '<em>Статья 23.5</em>'},
                {startColumnName: 'st2335f', numberOfColumns: 1, titleText: '<em>Статья 23.35</em>'},
                {startColumnName: 'st2361f', numberOfColumns: 2, titleText: '<em>Статья 23.61</em>'},
                {startColumnName: 'st244f', numberOfColumns: 1, titleText: '<em>Статья 24.4</em>'},
                {startColumnName: 'st245f', numberOfColumns: 1, titleText: '<em>Статья 24.5</em>'},
                {startColumnName: 'st246f', numberOfColumns: 1, titleText: '<em>Статья 24.6</em>'}
            ]
        });
        gridcsvexport("list_new2");
        grid.setGridWidth($(window).width() - 67);
        //  grid.jqGrid('setRowData', 7, false, 'mybold');
        $("#save_frm").button();

    }

//_________________________________________________________________________
//
//
//                          New report 1
//
//_________________________________________________________________________

    if ($('table').is("#list_new1")) {
        var grid = $("#list_new1");
        var col_width = 70;

//
//
//_________________________________________________________________________
//
//
//                         jqGrid for the report 1 
//
//_________________________________________________________________________
        grid.jqGrid({
            url: 'load_report.php',
            datatype: "json",
            mtype: "POST",
            colModel: [
                {name: "npp", width: 40, align: "center", label: "п/п"},
                {name: "group_name", width: 270},
                {name: "name", width: 380, classes: "cvname", label: "Наименование"},
                {name: "st203f", width: col_width, align: "right", classes: "col_color", decimalPlaces: 2, formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st203u", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ЮЛ'},
                {name: "st203p", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ИП'},
                {name: "st2010f", width: col_width, align: "right", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st2011f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st2011u", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ЮЛ'},
                {name: "st2011p", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ИП'},
                {name: "st2012f", width: col_width, align: "right", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st2012u", width: col_width, align: "right", formatter: currencyFormatter, editable: true,
                    label: 'ЮЛ'},
                {name: "st2012p", width: col_width, align: "right", formatter: currencyFormatter, editable: true,
                    label: 'ИП'},
                {name: "st231f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st232f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st233f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st234f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st235f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st2335f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st2361f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st2361p", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ИП'},
                {name: "st244f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st245f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "st246f", width: col_width, align: "right", classes: "col_color", formatter: currencyFormatter, editable: true,
                    label: 'ФЛ'},
                {name: "total", width: 100, align: "right", classes: "cvtotal", formatter: currencyFormatter,
                    label: "Итого за месяц"},
                {name: "total_cumulative", width: 100, align: "right", formatter: currencyFormatter,
                    label: 'С нарастающим итогом'}
            ],
            cmTemplate: {sortable: false},
            rowNum: 45,
            //width: null,
            shrinkToFit: true,
            autowidth: true,
            rowList: [15, 30, 45],
            pager: '#pager',
//            sortname: 'npp',
            viewrecords: true,
//            sortorder: "desc",
            caption: "ФОРМА 1",
            height: '100%',
            loadonce: true,
            grouping: true,
            ondblClickRow: function (rowId, iRow, iCol, e) {
                //alert(rowId + ' ' + iRow + ' ' + iCol);
                var cm = $(this).jqGrid('getGridParam', 'colModel');
                var column_name = cm[iCol].name;
                var cell_val = parseFloat($(this).jqGrid('getCell', rowId, column_name));
                if (iCol > 2 && iCol < 24) {
                    if (cell_val > 0.0) {
                        getContextReportData(rowId, iCol);
                    }
                }
            }
            ,
            groupingView: {
                groupField: ['group_name'],
                groupColumnShow: [false]
            }
            ,
            rowattr: function (rd) {
                switch (rd.name) {
                    case "ЗАЯВЛЕНИЯ и СООБЩЕНИЯ":
                        return {"class": "rowcss"};
                    default:
                        return {};
                }
            },
            loadComplete: function () {
                var i, group, cssClass, headerIdPrefix = this.id + "ghead_",
                        groups = $(this).jqGrid("getGridParam", "groupingView").groups,
                        l = groups.length;
                for (i = 0; i < l; i++) {
                    group = groups[i];
                    switch (group.value) {
                        case "АДМИНИСТРАТИВНОЕ ПРОИЗВОДСТВО":
                        case "ШТРАФЫ":
                        case "ЖАЛОБЫ":
                            cssClass = "rowcss";
                            break;
                        default:
                            cssClass = "";
                            break;
                    }
                    // listghead_0_1
                    if (cssClass !== "") {
                        $("#" + headerIdPrefix + group.idx + "_" + i).addClass(cssClass);
                    }
                }
//                var colPos = 5;
//                $(this).jqGrid('hideCol', $(this).getGridParam("colModel")[colPos].name);
//                $(this).find('TR.jqgrow:eq(0)').addClass('row_color');
//                $(this).find('TR.jqgrow:eq(23)').addClass('row_color');
//                $(this).find('TR.jqgrow[editable=11]').addClass('row_color');
//                $(this).find('TR.jqgrow[editable=1]')

                var rowids = $(this).jqGrid('getDataIDs');
                for (var i = 0; i < rowids.length; i++)
                {
//                    var rowid = rowids[i];
//                    var data = $(this).getRowData(rowid);
                    if (i == 1 || i == 8 || i == 13) {
                        $(this).jqGrid('setRowData', i, false, 'mybold');
                    }
                }
                var cm = $(this).jqGrid('getGridParam', 'colModel'), l = cm.length, j;
                for (var j = 0; j < l; j++) {
                    changeEditableByRow($(this), cm[j].name, 10);
                }

            }
            ,
            cellEdit: true,
            cellsubmit: 'clientArray',
            jsonReader: {
                repeatitems: false,
                page: function () {
                    return 1;
                },
                root: function (obj) {
                    return obj;
                },
                records: function (obj) {
                    return obj.length;
                }
            }
        });


        set_label(1, grid);
        grid.jqGrid("setGroupHeaders", {
            useColSpanStyle: true,
            groupHeaders: [
                {startColumnName: 'st203f', numberOfColumns: 3, titleText: '<em>Статья 20.3</em>'},
                {startColumnName: 'st2010f', numberOfColumns: 1, titleText: '<em>Статья 20.10</em>'},
                {startColumnName: 'st2011f', numberOfColumns: 3, titleText: '<em>Статья 20.11</em>'},
                {startColumnName: 'st2012f', numberOfColumns: 3, titleText: '<em>Статья 20.12</em>'},
                {startColumnName: 'st231f', numberOfColumns: 1, titleText: '<em>Статья 23.1</em>'},
                {startColumnName: 'st232f', numberOfColumns: 1, titleText: '<em>Статья 23.2</em>'},
                {startColumnName: 'st233f', numberOfColumns: 1, titleText: '<em>Статья 23.3</em>'},
                {startColumnName: 'st234f', numberOfColumns: 1, titleText: '<em>Статья 23.4</em>'},
                {startColumnName: 'st235f', numberOfColumns: 1, titleText: '<em>Статья 23.5</em>'},
                {startColumnName: 'st2335f', numberOfColumns: 1, titleText: '<em>Статья 23.35</em>'},
                {startColumnName: 'st2361f', numberOfColumns: 2, titleText: '<em>Статья 23.61</em>'},
                {startColumnName: 'st244f', numberOfColumns: 1, titleText: '<em>Статья 24.4</em>'},
                {startColumnName: 'st245f', numberOfColumns: 1, titleText: '<em>Статья 24.5</em>'},
                {startColumnName: 'st246f', numberOfColumns: 1, titleText: '<em>Статья 24.6</em>'}
            ]
        });
        gridcsvexport("list_new1");
        grid.setGridWidth($(window).width() - 67);
        //  grid.jqGrid('setRowData', 7, false, 'mybold');



        $("#save_frm").button();

    }


//_________________________________________________________________________
//
//
//                         $("#save").click(function (){}) 
//
//_________________________________________________________________________

    $("#save_frm").click(function () {
        var param = get_param(2);
        json_obj.val = {};
        json_obj.val['name'] = param['name'];
        json_obj.val['mro'] = '';
        json_obj.val['rep_date'] = $('#month_picker').val();
        var rowids = grid.jqGrid('getDataIDs');
        for (var i = 0; i < rowids.length; i++) {
            var rowid = rowids[i];
            var data = grid.getRowData(rowid);
            var cm = grid.jqGrid('getGridParam', 'colModel');
            if (i === param['row_input']) {
                for (var j = 0; j < cm.length; j++) {
                    if (j > 2 && j < 24) {
                        var name = cm[j].name;
                        var text = data[cm[j].name];
                        var val = text.replace(/\s+/g, '');
                        if (Number(val) != 0) {
                            json_obj.val[name] = val;
                        }
//                            json_obj.val[name] = text.replace(/\D/gi, '').replace(/^0+/, '');
                        //alert(Number(json_obj.val[name]));
                    }
                }
                json_obj.val['row_id'] = data[cm[0].name];
                var array = JSON.stringify(json_obj.val);
//                    alert(array);

            }
        }
        doRequestHttpServer('save_new_report', json_obj, '');
    });

    /**
     * set_label
     * @param {type} id
     * @param {type} grid
     * @returns {undefined}
     */
    function set_label(id, grid) {
        jQuery.ajax({
            url: "get_data.php",
            type: "POST",
            data: ({id: id}),
            dataType: "json",
            async: false,
            success: function (data) {
                //var array = JSON.stringify(data);
                grid.setLabel('total', data['new_label']);
            },
            error: function () {}
        });
    }

    /**
     * get_param
     * @param {type} id
     * @returns {Array|my_jquery_L17.get_param.param}
     */
    function get_param(id) {
        var param = [];
        jQuery.ajax({
            url: "get_data.php",
            type: "POST",
            data: ({id: id}),
            dataType: "json",
            async: false,
            success: function (data) {
                //var array = JSON.stringify(data);
                param['row_input'] = data['row_input'];
                param['name'] = data['name'];
            },
            error: function () {}
        });
        return param;
    }

    /**
     * getContextReportData
     * @returns {undefined}
     */
    function getContextReportData(rowId, iCol) {
        $('#overlay').fadeIn('fast', function () {
            $('#nonebox').animate({'top': '160px'}, 500);
        });
        var data_array = {};
        data_array['name'] = 'context_data';
        data_array['rowId'] = rowId;
        data_array['iCol'] = iCol;
        var data_array = JSON.stringify(data_array);
        jQuery.ajax({
            url: "get_data.php",
            type: "POST",
            data: ({myJson: data_array}),
            dataType: "json",
            async: false,
            success: function (data) {
                var col_names, col_model;
//                var array = JSON.stringify(data);
//                alert(array);
                if (data.operation < 8) {
                    col_names = ['Регистрационный номер ', 'ФИО нарушителя'];
                    col_model = [
                        {name: 'reg_num', index: 'reg_num', width: 90},
                        {name: 'fio_penalized', index: 'fio_penalized', width: 400}
                    ];
                } else {
                    col_names = ['Регистрационный номер ', 'ФИО нарушителя', 'Сумма штрафа'];
                    col_model = [
                        {name: 'reg_num', index: 'reg_num', width: 90},
                        {name: 'fio_penalized', index: 'fio_penalized', width: 400},
                        {name: 'summa', index: 'summa', width: 90, formatter: contextFormatter}
                    ];

                }
                jQuery('#list_context').jqGrid('GridUnload');
                jQuery("#list_context").jqGrid({
                    datatype: "local",
                    data: data.sql,
                    colNames: col_names,
                    colModel: col_model,
                    rowNum: 45,
                    rowList: [15, 30, 45],
                    shrinkToFit: true,
                    autowidth: true,
                    pager: '#pager_con',
                    sortname: 'id',
                    viewrecords: true,
                    sortorder: "desc",
                    caption: "Список дел",
                    cellEdit: true,
                    cellsubmit: 'clientArray',
                    ondblClickRow: function (rowId, iRow, iCol, e) {
                        //alert(rowId + ' ' + iRow + ' ' + iCol);
                        var cm = $(this).jqGrid('getGridParam', 'colModel');
                        var column_name = cm[0].name;
                        var reg_num = $(this).jqGrid('getCell', rowId, column_name);
                        //                       alert(reg_num);
                        var json_obj = {//JSON object
                            val: ""
                        }
                        json_obj.val = {};
                        json_obj.val['doc_id'] = get_doc_id(reg_num);
                        var doc = document.documentElement;
                        var left = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);
                        var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                        json_obj.val['pageXOffset'] = left;
                        json_obj.val['pageYOffset'] = top;
                        doRequestHttpServer('goto', json_obj, '');


                    }

                });
                jQuery("#list_context").jqGrid('navGrid', '#pager_con', {edit: false, add: false, del: false, search: false});//
//
//                        fill jqGrid   NSI with data
//
//_________________________________________________________________________

            },
            error: function () {
                alert('Error');
            }
        });// end jQuery.ajax

    }

    $('#box-close').click(function () {
        $('#nonebox').animate({'top': '-1300px'}, 500, function () {
            $('#overlay').fadeOut('fast');
        });
    });

    /**
     * get_doc_id
     * @param {type} id
     * @returns {Array|my_jquery_L17.get_param.param}
     */
    function get_doc_id(reg_num) {
        var doc_id;
        var data_array = {};
        data_array['name'] = 'get_doc_id';
        data_array['reg_num'] = reg_num;
        var data_array = JSON.stringify(data_array);
        jQuery.ajax({
            url: "get_data.php",
            type: "POST",
            data: ({myJson: data_array}),
            dataType: "json",
            async: false,
            success: function (data) {
//                var array = JSON.stringify(data);
//                alert(array);
                doc_id = data['doc_id'];
            },
            error: function () {
                alert('Error');
            }
        });
        return doc_id;
    }

//_________________________________________________________________________
//
//
//                         Formatter 
//
//_________________________________________________________________________

// Custom formatter that ceils all number values 
    function contextFormatter(cellvalue, options, rowObject) {
        // Ceil the number value if any decimal numbers are present
        var value = Number(cellvalue);
        var color='green';
        if (value === 0) {
            color = 'red';
        }
        value = value.toString();

        // Loop through all thousands and add a space
        value = value.replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g, "\$1 ");
            return  '<span  style="color:' + color + ';">' + value + '</span>';

//        return  value;
    }




});// end main function





//                    colNames: ['Номер', 'Название'],
//                    colModel: [
//                        {name: 'id', index: 'id', width: 70, align: 'center', sorttype: 'int', searchoptions: {sopt: ['eq', 'ne']}},
//                        {name: name_fld, index: name_fld, width: 700, editable: true}
//                    ],
