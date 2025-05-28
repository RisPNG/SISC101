<?php
include('class/User.php');
$user = new User();
$user->loginStatus();
include('include/header.php');
?>
<title>Student Management System - Pomodoro Timer</title>
<?php include('include/container.php');?>
<div class="container contact">
    <h2>Pomodoro Timer</h2>
    <?php include('menu.php');?>
    <div class="table-responsive">
        <p>Use the Pomodoro Timer to manage your study sessions. Set session and break lengths below, then click <strong>Start</strong> to begin.</p>
        <form id="pomodoro-form" class="form-inline">
            <div class="form-group">
                <label for="session-length">Session Length (minutes): </label>
                <input type="number" class="form-control" id="session-length" value="25" min="1">
            </div>
            <div class="form-group" style="margin-left:15px;">
                <label for="break-length">Break Length (minutes): </label>
                <input type="number" class="form-control" id="break-length" value="5" min="1">
            </div>
            <button type="button" class="btn btn-primary" id="start-btn" style="margin-left:15px;">Start</button>
            <button type="button" class="btn btn-default" id="reset-btn" style="margin-left:5px;" disabled>Reset</button>
        </form>
        <div style="margin-top:20px;">
            <h4 id="timer-label">Ready</h4>
            <h1 id="timer-display">00:00</h1>
        </div>
    </div>
</div>
<script>
    (function() {
        var isRunning = false, timerId, totalSeconds = 0, isSession = true;
        var sessionInput = document.getElementById('session-length');
        var breakInput = document.getElementById('break-length');
        var startBtn = document.getElementById('start-btn');
        var resetBtn = document.getElementById('reset-btn');
        var label = document.getElementById('timer-label');
        var display = document.getElementById('timer-display');

        function updateDisplay(seconds) {
            var m = Math.floor(seconds / 60);
            var s = seconds % 60;
            display.textContent = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        }

        function switchMode() {
            isSession = !isSession;
            var length = isSession ? parseInt(sessionInput.value, 10) : parseInt(breakInput.value, 10);
            totalSeconds = length * 60;
            label.textContent = isSession ? 'Session Time' : 'Break Time';
            updateDisplay(totalSeconds);
        }

        function tick() {
            if (totalSeconds <= 0) {
                switchMode();
            } else {
                totalSeconds--;
                updateDisplay(totalSeconds);
            }
        }

        startBtn.addEventListener('click', function () {
            if (isRunning) return;
            isRunning = true;
            startBtn.disabled = true;
            resetBtn.disabled = false;
            isSession = true;
            label.textContent = 'Session Time';
            totalSeconds = parseInt(sessionInput.value, 10) * 60;
            updateDisplay(totalSeconds);
            timerId = setInterval(tick, 1000);
        });

        resetBtn.addEventListener('click', function () {
            clearInterval(timerId);
            isRunning = false;
            startBtn.disabled = false;
            resetBtn.disabled = true;
            isSession = true;
            label.textContent = 'Ready';
            display.textContent = '00:00';
        });
    })();
</script>
<?php include('include/footer.php');?>