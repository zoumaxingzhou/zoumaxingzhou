define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'school_grade/index' + location.search,
                    add_url: 'school_grade/add',
                    edit_url: 'school_grade/edit',
                    del_url: 'school_grade/del',
                    multi_url: 'school_grade/multi',
                    import_url: 'school_grade/import',
                    table: 'school_grade',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'school_id', title: __('School_id')},
                        // {field: 'grade_id', title: __('Grade_id')},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.id', title: __('Grade.id')},
                        {field: 'school.name', title: __('School.name'), operate: 'LIKE'},
                        {field: 'grade.name', title: __('Grade.name'), operate: 'LIKE'},
                        // {field: 'grade.createtime', title: __('Grade.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.deletetime', title: __('Grade.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.weigh', title: __('Grade.weigh')},
                        // {field: 'school.id', title: __('School.id')},
                        // {field: 'school.province', title: __('School.province')},
                        // {field: 'school.city', title: __('School.city')},
                        // {field: 'school.area', title: __('School.area')},
                        // {field: 'school.createtime', title: __('School.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'school.updatetime', title: __('School.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'school.deletetime', title: __('School.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table,
                            buttons:[{name: 'grade_list',
                                text: __('年级课程列表'),
                                title: __('年级课程列表'),
                                classname: 'btn btn-xs btn-primary btn-dialog',
                                icon: 'fa fa-list',
                                url: 'school_grade_course/index?school_id={school.id}&grade_id={grade.id}', },

                            ],events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],queryParams: function (params) {
                var filter = JSON.parse(params.filter);
                var op     = JSON.parse(params.op);
                //这里可以动态赋值,比如从URL获取admin_id的值,filater.admin_id = Fast.api.query('admin_id')
                var school_id = Fast.api.query('school_id');
                if(school_id){
                    filter.school_id = school_id;
                    // op.province     = "=";
                }
                params.filter = JSON.stringify(filter);
                params.op = JSON.stringify(op);
                return params;
            }
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'school_grade/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'school_grade/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'school_grade/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});