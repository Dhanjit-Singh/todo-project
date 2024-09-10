<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>PHP - Simple To Do List App</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="d-flex justify-content-center mt-4 mb-4">PHP - Simple To Do List App</h2>
        <div class="d-flex justify-content-center">
            <div class="col-md-6">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="taskName" placeholder="Add a new task">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary" id="addTask">Add Task</button>
                    </div>
                </div>
                <button id="showAllTasks" class="btn btn-info">Show All Tasks</button>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="taskTable">
                @foreach ($tasks as $task)
                <tr data-id="{{ $task->id }}" class="{{ $task->is_completed ? 'completed' : '' }}" style="{{ $task->is_completed ? 'display:none;' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->is_completed ? 'Done' : 'Pending' }}</td>
                    <td>
                        <input type="checkbox" class="mark-complete" {{ $task->is_completed ? 'checked' : '' }}>
                        <button class="btn btn-danger btn-sm delete-task">✖</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            function addTask() {
                const taskName = $('#taskName').val();
                if (taskName) {
                    $.ajax({
                        url: '/tasks',
                        type: 'POST',
                        data: {
                            name: taskName,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#taskTable').prepend(`
                                    <tr data-id="${response.task.id}">
                                        <td>1</td>
                                        <td>${response.task.name}</td>
                                        <td>Pending</td>
                                        <td>
                                            <input type="checkbox" class="mark-complete">
                                            <button class="btn btn-danger btn-sm delete-task">✖</button>
                                        </td>
                                    </tr>
                                `);

                                $('#taskTable tr').each(function(index) {
                                    $(this).find('td:first').text(index + 1);
                                });

                                $('#taskName').val('');
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON.message);
                        }
                    });
                }
            }

            $('#addTask').on('click', function(event) {
                event.preventDefault();
                addTask();
            });

            $('#taskName').on('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addTask();
                }
            });

            //===== Mark task as complete/incomplete =======
            $(document).on('change', '.mark-complete', function() {
                const $row = $(this).closest('tr');
                const taskId = $row.data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'PATCH',
                    data: {
                        completed: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $row.toggleClass('completed', isChecked);
                            $row.find('td:nth-child(3)').text(isChecked ? 'Done' : 'Pending');
                            
                            if (isChecked) {
                                $row.hide();
                            } else {
                                $row.show();
                            }
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message);
                    }
                });
            });

            //=========== Delete task ==============
            $(document).on('click', '.delete-task', function() {
                if (confirm('Are you sure you want to delete this task?')) {
                    const taskId = $(this).closest('tr').data('id');
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $(`tr[data-id="${taskId}"]`).remove();
                            }
                        }
                    });
                }
            });

            //============ Show all tasks ===========
            $('#showAllTasks').on('click', function() {
                $('#taskTable tr').show();
            });
        });
    </script>
</body>

</html>
