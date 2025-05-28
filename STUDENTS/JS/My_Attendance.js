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
			url:"my_action.php",
			type:"POST",
			data: function(d) {
                d.action = 'listKelas';
            },
			dataType:"json",
            error: function (xhr, error, thrown) { // More detailed error reporting
                console.error("DataTables AJAX error:", error, thrown);
                console.error("ResponseText:", xhr.responseText);
                alert("Error fetching data. Check console for details. Response: " + xhr.responseText.substring(0, 500) + "..."); // Show part of the error
            }
		},
		"columnDefs":[
			{ "targets":[0, 1, 8, 9, 10, 11], "orderable":false },
			{ "targets": [1], "visible": false }
		],
		"pageLength": 10
	});

    // Delegate event for Check In button
	$('#kelasList').on('click', '.checkInBtn', function(){
        var $button = $(this);
        $('#studentid').val($button.data('sid'));
        $('#classid').val($button.data('cid'));
        $('#sectionid').val($button.data('iid'));

        var classStartTimeStr = $button.data('starttime');

        getDateTime();

        $('#attendancedate').val(currDate);
        $('#attendancetime').val(currTime);
        $('#checkedInOut').val('1');

        var attendanceStatusVal = "1";
        var scoreVal = 1.0;

        if (classStartTimeStr && classStartTimeStr !== 'N/A') {
            var now = new Date();
            var classStartTimeParts = classStartTimeStr.split(':');
            var classStartDateTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(classStartTimeParts[0]), parseInt(classStartTimeParts[1]), 0);
            var classStartGracePeriod = new Date(classStartDateTime.getTime() + 5 * 60000);

            if (now > classStartGracePeriod) {
                attendanceStatusVal = "2";
                scoreVal = parseFloat((2.0/3.0).toFixed(2));
            }
        }
        $('#attendancestatus').val(attendanceStatusVal);
        $('#score').val(scoreVal);

        if(confirm("Are you sure you want to Check In for this class?\n\nDate: "+currDate+"\nTime: "+currTime)) {
            $("#attnForm").submit();
        }
    });

    // Delegate event for Check Out button
    $('#kelasList').on('click', '.checkOutBtn', function(){
        var $button = $(this);
        $('#studentid').val($button.data('sid'));
        $('#classid').val($button.data('cid'));
        $('#sectionid').val($button.data('iid'));

        getDateTime();

        $('#attendancedate').val(currDate);
        $('#attendancetime').val(currTime);
        $('#checkedInOut').val('2');
        $('#attendancestatus').val("1"); // Default, actual status is from check-in
        $('#score').val("1.0"); // Default, actual score is from check-in

        if(confirm("Are you sure you want to Check Out for this class?\n\nDate: "+currDate+"\nTime: "+currTime)) {
            $("#attnForm").submit();
        }
    });

	$("#attnForm").submit(function(event) {
		event.preventDefault();
		$('#addKelas').prop('disabled', true);

		var formData = $(this).serialize();

		$.ajax({
			url: "my_action.php",
			method: "POST",
			data: formData,
            dataType: "json", // Expect JSON response from addKelas action too
			success:function(data){
                if(data.status === 'success') {
                    // alert(data.message || 'Attendance saved successfully!'); // Optional success alert
                } else {
                    alert('Error: ' + (data.message || 'Could not save attendance.'));
                }
				kelasData.ajax.reload();
			},
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Attendance save AJAX error: ", textStatus, errorThrown);
                console.error("ResponseText:", jqXHR.responseText);
                alert("Error saving attendance. Check console for details. Response: " + jqXHR.responseText.substring(0, 500) + "...");
                kelasData.ajax.reload(); // Still reload, server might have partial state or to clear pending UI
            },
            complete: function() {
                $('#addKelas').prop('disabled', false);
            }
		});
	});

	$(document).on('submit','#userForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"my_action.php",
			method:"POST",
			data:formData,
            dataType: "json", // Expect JSON
			success:function(data){
                if(data.status === 'success') {
				    $('#userForm')[0].reset();
				    $('#userModal').modal('hide');
                } else {
                    alert("Error updating user: " + (data.message || "Unknown error."));
                }
			},
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("User update AJAX error: ", textStatus, errorThrown);
                alert("Error updating user. Check console.");
            },
            complete: function() {
                $('#save').attr('disabled', false);
            }
		});
	});
});