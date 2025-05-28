<?php
include('class/User.php');
$user = new User();
$user->loginStatus();
include('include/header.php');
?>
<title>Student Management System - GPA Calculator</title>
<?php include('include/container.php');?>
<div class="container contact">
    <h2>GPA Calculator</h2>
    <?php include('menu.php');?>
    <div class="table-responsive">
        <p>Calculate your GPA by entering your credit hours and grade value for each course below.</p>
        <form id="gpa-form">
            <table class="table table-bordered" id="gpa-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Credit Hours</th>
                        <th>Grade (0.00 - 4.00)</th>
                    </tr>
                </thead>
                <tbody>
<?php for ($i = 0; $i < 5; $i++): ?>
                    <tr>
                        <td><input type="text" class="form-control" name="course[]" placeholder="Course Name"></td>
                        <td><input type="number" class="form-control" name="credits[]" step="1" min="0" value="3"></td>
                        <td><input type="number" class="form-control" name="grade[]" step="0.01" min="0" max="4" value="4.00"></td>
                    </tr>
<?php endfor; ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" id="calculate-btn">Calculate GPA</button>
        </form>
        <h4 id="gpa-result" style="margin-top:20px;"></h4>
    </div>
</div>
<script>
    document.getElementById('calculate-btn').addEventListener('click', function () {
        var totalPoints = 0, totalCredits = 0;
        var rows = document.querySelectorAll('#gpa-table tbody tr');
        rows.forEach(function (row) {
            var credits = parseFloat(row.querySelector('input[name="credits[]"]').value) || 0;
            var grade = parseFloat(row.querySelector('input[name="grade[]"]').value) || 0;
            totalCredits += credits;
            totalPoints += grade * credits;
        });
        var gpa = totalCredits ? (totalPoints / totalCredits).toFixed(2) : '0.00';
        document.getElementById('gpa-result').textContent = 'Your GPA is: ' + gpa;
    });
</script>
<?php include('include/footer.php');?>