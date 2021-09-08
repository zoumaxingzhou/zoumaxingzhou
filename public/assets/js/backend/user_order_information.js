define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user_order_information/index' + location.search,
                    add_url: 'user_order_information/add',
                    edit_url: 'user_order_information/edit',
                    del_url: 'user_order_information/del',
                    multi_url: 'user_order_information/multi',
                    import_url: 'user_order_information/import',
                    table: 'user_order_information',
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
                        // {field: 'order_id', title: __('Order_id')},
                        // {field: 'school_id', title: __('School_id')},
                        // {field: 'grade_id', title: __('Grade_id')},
                        // {field: 'course_id', title: __('Course_id')},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        // {field: 'school.id', title: __('School.id')},
                        // {field: 'school.province', title: __('School.province')},
                        // {field: 'school.city', title: __('School.city')},
                        // {field: 'school.area', title: __('School.area')},
                        {field: 'school.name', title: __('School.name'), operate: 'LIKE'},
                        // {field: 'school.createtime', title: __('School.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'school.updatetime', title: __('School.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'school.deletetime', title: __('School.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'course.id', title: __('Course.id')},
                        {field: 'grade.name', title: __('Grade.name'), operate: 'LIKE'},

                        {field: 'course.name', title: __('Course.name'), operate: 'LIKE'},
                        // {field: 'course.createtime', title: __('Course.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'course.deletetime', title: __('Course.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'course.weigh', title: __('Course.weigh')},
                        // {field: 'grade.id', title: __('Grade.id')},
                        // {field: 'grade.createtime', title: __('Grade.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.deletetime', title: __('Grade.deletetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'grade.weigh', title: __('Grade.weigh')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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