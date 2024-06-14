<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Movement</title>
    <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Additional styles for modal */
        .modal-content {
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <h5 class="card-header">
            <i class="fa fa-exchange"></i> Enter Movement
        </h5>
        <div class="card-body">
            <form id="movementForm" action="process_movement.php" method="post">
                <div class="form-group">
                    <label for="test_jig_name">Test Jig Name</label>
                    <input type="text" class="form-control" id="test_jig_name" name="test_jig_name" required>
                </div>
                <div class="form-group">
                    <label for="user_id">User</label>
                    <select class="form-control" id="user_id" name="user_id" required>
                        <option value="" disabled selected>Select User</option>
                        <?php
                        // Fetch users from the database
                        include 'db.php';
                        $sqlUsers = "SELECT id, username FROM users";
                        $resultUsers = $conn->query($sqlUsers);
                        while ($row = $resultUsers->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['username']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="new_location">New Location</label>
                    <input type="text" class="form-control" id="new_location" name="new_location" required>
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Submit</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal for skill check notification -->
<div class="modal fade" id="skillCheckModal" tabindex="-1" role="dialog" aria-labelledby="skillCheckModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="skillCheckModalLabel">Skill Check</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Seek advice from supervisor.</p>
                <h6>Users with necessary skills:</h6>
                <ul id="qualifiedUsersList"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="vendor/components/jquery/jquery.min.js"></script>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('#user_id').change(function () {
            var userId = $(this).val();
            $('#test_jig_name').prop('disabled', !userId);
            $('#new_location').prop('disabled', !userId);
            $('#submitBtn').prop('disabled', !userId);

            if (userId) {
                $.ajax({
                    url: 'check_user_skills.php',
                    method: 'POST',
                    data: { user_id: userId, test_jig_name: $('#test_jig_name').val() },
                    success: function (response) {
                        var result = JSON.parse(response);

                        if (!result.hasSkills) {
                            $('#qualifiedUsersList').empty();
                            result.qualifiedUsers.forEach(function (user) {
                                $('#qualifiedUsersList').append('<li>' + user.username + '</li>');
                            });
                            $('#skillCheckModal').modal('show');
                        }
                    }
                });
            }
        });

        $('#movementForm').submit(function (e) {
            var userId = $('#user_id').val();
            var testJigName = $('#test_jig_name').val();

            if (userId && testJigName) {
                e.preventDefault();
                $.ajax({
                    url: 'check_user_skills.php',
                    method: 'POST',
                    data: { user_id: userId, test_jig_name: testJigName },
                    success: function (response) {
                        var result = JSON.parse(response);

                        if (result.hasSkills) {
                            $('#movementForm')[0].submit();
                        } else {
                            $('#qualifiedUsersList').empty();
                            result.qualifiedUsers.forEach(function (user) {
                                $('#qualifiedUsersList').append('<li>' + user.username + '</li>');
                            });
                            $('#skillCheckModal').modal('show');
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>
