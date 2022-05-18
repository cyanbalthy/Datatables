$(document).ready(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'http://localhost:8080',
            type: 'POST'
        },
        colums: [
            {data: 'id'},
            {data: 'first_name'},
            {data: 'last_name'},
            {data: 'gender'},
            {data: 'birth_date'},
            {data: 'hire_date'}
        ]
    });
});