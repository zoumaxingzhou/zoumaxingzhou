define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'school_grade_course/index' + location.search,
                    add_url: 'school_grade_course/add',
                    edit_url: 'school_grade_course/edit',
                    del_url: 'school_grade_course/del',
                    multi_url: 'school_grade_course/multi',
                    import_url: 'school_grade_course/import',
                    table: 'school_grade_course',
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
                        // {field: 'course_id', title: __('Course_id')},
                        // {field: 'grade_id', title: __('Grade_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        // {field: 'school.id', title: __('School.id')},
                        // {field: 'school.province', title: __('School.province')},
                        // {field: 'school.city', title: __('School.city')},
                        // {field: 'school.area', title: __('School.area')},
                        {field: 'school.name', title: __('School.name'), operate: 'LIKE'},
                        // {field: 'school.createtime', title: __('School.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'school.updatetime', title: __('School.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'school.deletetime', title: __('School.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.id', title: __('Grade.id')},
                        {field: 'grade.name', title: __('Grade.name'), operate: 'LIKE'},
                        // {field: 'grade.createtime', title: __('Grade.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.deletetime', title: __('Grade.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.weigh', title: __('Grade.weigh')},
                        // {field: 'course.id', title: __('Course.id')},
                        {field: 'course.name', title: __('Course.name'), operate: 'LIKE'},
                        // {field: 'course.createtime', title: __('Course.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'course.deletetime', title: __('Course.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'course.weigh', title: __('Course.weigh')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],queryParams: function (params) {
                    var filter = JSON.parse(params.filter);
                    var op     = JSON.parse(params.op);
                    //这里可以动态赋值,比如从URL获取admin_id的值,filater.admin_id = Fast.api.query('admin_id')
                    var school_id = Fast.api.query('school_id');
                    var grade_id = Fast.api.query('grade_id');
                    if(school_id){
                        filter.school_id = school_id;
                        // op.province     = "=";
                    }
                    if(grade_id){
                        filter.grade_id = grade_id;
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
                url: 'school_grade_course/recyclebin' + location.search,
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
                                    url: 'school_grade_course/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'school_grade_course/destroy',
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