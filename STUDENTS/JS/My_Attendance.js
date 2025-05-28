$(document).ready(function(){
	var currDate, currTime; // For storing current date and time

	function getDateTime() {
	    var now     = new Date();
        var year    = now.getFullYear();
        var month   = now.getMonth()+1;
        var day     = now.getDate();
        var hour    = now.getHours();
        var minute  = now.getMinutes();
        var second  = now.getSeconds();
	    if(month.toString().length == 1) { month = '0'+month; }
        if(day.toString().length == 1) { day = '0'+day; }
        if(hour.toString().length == 1) { hour = '0'+hour; }
        if(minute.toString().length == 1) { minute = '0'+minute; }
        if(second.toString().length == 1) { second = '0'+second; }
        currDate = year+'/'+month+'/'+day; // Format YYYY/MM/DD
		currTime = hour+':'+minute+':'+second; // Format HH:MM:SS
    }

	var kelasData = $('#kelasList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"my_action.php", // This should point to the PHP file with getKelasList and addKelas
			type:"POST",
			data: function(d) { // Send action for listKelas
                d.action = 'listKelas';
            },
			dataType:"json"
		},
		"columnDefs":[
			{ "targets":[0, 1, 8, 9, 10, 11], "orderable":false }, // No, ID, Start Time, Checked In, Checked Out, Actions
			{ "targets": [1], "visible": false } // Hide Class ID column if not needed for display
		],
		"pageLength": 10
	});

    // Delegate event for Check In button
	$('#kelasList').on('click', '.checkInBtn', function(){
        var $button = $(this);
        $('#studentid').val($button.data('sid'));
        $('#classid').val($button.data('cid'));
        $('#sectionid').val($button.data('iid'));

        var classStartTimeStr = $button.data('starttime'); // HH:MM format

        getDateTime(); // Sets global currDate, currTime

        $('#attendancedate').val(currDate);
        $('#attendancetime').val(currTime);
        $('#checkedInOut').val('1'); // 1 for Check In

        var attendanceStatusVal = "1"; // 1 = Present
        var scoreVal = 1.0;

        if (classStartTimeStr && classStartTimeStr !== 'N/A') {
            var now = new Date(); // Current date and time for comparison
            var classStartTimeParts = classStartTimeStr.split(':');

            // Create a date object for class start time ON THE CURRENT DAY
            var classStartDateTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(classStartTimeParts[0]), parseInt(classStartTimeParts[1]), 0);

            // Add 5 minutes grace period
            var classStartGracePeriod = new Date(classStartDateTime.getTime() + 5 * 60000);

            if (now > classStartGracePeriod) {
                attendanceStatusVal = "2"; // 2 = Late
                scoreVal = parseFloat((2.0/3.0).toFixed(2)); // Keep score consistent
            }
        }
        $('#attendancestatus').val(attendanceStatusVal);
        $('#score').val(scoreVal);

        if(confirm("Are you sure you want to Check In for this class?\n\nDate: "+currDate+"\nTime: "+currTime)) {
            $("#attnForm").submit(); // Trigger form submission
        }
    });

    // Delegate event for Check Out button
    $('#kelasList').on('click', '.checkOutBtn', function(){
        var $button = $(this);
        $('#studentid').val($button.data('sid'));
        $('#classid').val($button.data('cid'));
        $('#sectionid').val($button.data('iid'));

        getDateTime(); // Sets global currDate, currTime

        $('#attendancedate').val(currDate);
        $('#attendancetime').val(currTime); // This is the checkout time
        $('#checkedInOut').val('2'); // 2 for Check Out
        
        // For checkout, attendance_status and score might not need to be re-evaluated or can be set to a default
        // The PHP side for check-out primarily updates check_out time.
        // If you need to send these, ensure they are appropriate.
        $('#attendancestatus').val("1"); // Or fetch existing status if needed
        $('#score').val("1.0"); // Or fetch existing score

        if(confirm("Are you sure you want to Check Out for this class?\n\nDate: "+currDate+"\nTime: "+currTime)) {
            $("#attnForm").submit(); // Trigger form submission
        }
    });

	// Handles the actual form submission via AJAX
	$("#attnForm").submit(function(event) {
		event.preventDefault(); // Prevent default browser submission
		$('#addKelas').prop('disabled', true); // Disable trigger if it were visible

		var formData = $(this).serialize();
        // The form already includes <input type="hidden" name="action" id="formAction" value="addKelas">
        // So, formData string will contain action=addKelas

		$.ajax({
			url: "my_action.php", // Ensure this is the correct endpoint
			method: "POST",
			data: formData,
			success:function(data){
                // Optionally display a success message
				// $('#message').text('Attendance saved successfully!').removeClass('hidden').addClass('alert-success');
				kelasData.ajax.reload(); // Reload DataTable to reflect changes
			},
            error: function(jqXHR, textStatus, errorThrown) {
                // Optionally display an error message
                // $('#message').text('Error saving attendance: ' + errorThrown).removeClass('hidden').addClass('alert-danger');
                console.error("Attendance save error: ", textStatus, errorThrown);
                kelasData.ajax.reload(); // Still reload, server might have partial state
            },
            complete: function() {
                $('#addKelas').prop('disabled', false); // Re-enable if it were visible
            }
		});
	});

	// User modal form submission (for editing user details - existing logic)
	// Ensure my_action.php handles 'updateUser' action if this modal is used.
	$(document).on('submit','#userForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize(); // This form has action_user_modal with value updateUser
		$.ajax({
			url:"my_action.php", // Check if my_action.php handles updateUser
			method:"POST",
			data:formData,
			success:function(data){
				$('#userForm')[0].reset();
				$('#userModal').modal('hide');
				$('#save').attr('disabled', false);
				// Consider if kelasData.ajax.reload(); is needed here.
                // If user details affect the attendance list display (e.g. status), then yes.
			}
		});
	});
});