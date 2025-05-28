$(document).ready(function(){

    var studentid_val, classid_val, sectionid_val, startTime_val;
    var attendancestatus_val;
    var checkedInOut_val; // Will be '1' for In, '2' for Out

    var currDate, currTime, now_global; // 'now_global' to avoid conflict with Date.now

    function getDateTime() {
        now_global     = new Date();
        var year    = now_global.getFullYear();
        var month   = now_global.getMonth()+1;
        var day     = now_global.getDate();
        var hour    = now_global.getHours();
        var minute  = now_global.getMinutes();
        var second  = now_global.getSeconds();

        if(month.toString().length == 1) { month = '0'+month; }
        if(day.toString().length == 1) { day = '0'+day; }
        if(hour.toString().length == 1) { hour = '0'+hour; }
        if(minute.toString().length == 1) { minute = '0'+minute; }
        if(second.toString().length == 1) { second = '0'+second; }

        currDate = year + '-' + month + '-' + day; // Standard YYYY-MM-DD for MySQL
        currTime = hour + ':' + minute + ':' + second;
    }

    function dateAdd(date, interval, units) {
      var ret = new Date(date); //don't change original date
      switch(interval.toLowerCase()) {
        case 'minute' :  ret.setTime(ret.getTime() + units*60000);  break;
        // Add other cases if needed
        default       :  ret = undefined;  break;
      }
      return ret;
    }

    var kelasData = $('#kelasList').DataTable({
        "lengthChange": false,
        "processing":true,
        "serverSide":true,
        "order":[],
        "ajax":{
            url:"my_action.php", // Ensure this points to your PHP action handler
            type:"POST",
            data: function(d) { // Dynamic data for DataTables
                d.action = 'listKelas';
            },
            dataType:"json",
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("DataTables AJAX error: ", textStatus, errorThrown, jqXHR.responseText);
                alert("Error loading class list. Please check console for details.");
            }
        },
        "columnDefs":[
            // Col 0: No., Col 1: ID (hidden), Col 2-7: Class Details, Col 8: Sched. Start + Hidden Spans
            // Col 9: Checked In, Col 10: Checked Out, Col 11: Action Buttons
            { "targets":[0, 1, 11], "orderable":false },
            { "targets": [1], "visible": false },
        ],
        "pageLength": 10
    });

    // This click handler is triggered programmatically by checkIn/checkOut buttons
    // It uses the hidden input #addKelas as a trigger point, but sends data via AJAX directly.
    $('#addKelas').on('click', function(){
        if (checkedInOut_val === "1" || checkedInOut_val === "2") {
            getDateTime(); // Sets currDate, currTime, now_global

            // Populate hidden form fields (optional if data is sent directly in AJAX, but can be good for debugging or if form is ever submitted traditionally)
            $('#studentid').val(studentid_val);
            $('#classid').val(classid_val);
            $('#sectionid').val(sectionid_val);
            $('#attendancedate').val(currDate);
            $('#attendancetime').val(currTime);
            $('#checkedInOut').val(checkedInOut_val);

            var score = 1.0;
            // Ensure startTime_val is in HH:MM format for Date parsing
            // getDateTime() sets currDate to YYYY-MM-DD format
            var classScheduleDateTimeStr = currDate + " " + startTime_val + ":00"; // Add seconds
            var sTime = new Date(classScheduleDateTimeStr);

            if (isNaN(sTime.getTime())) {
                alert("Error: Invalid class start time format (" + startTime_val + "). Cannot proceed.");
                console.error("Invalid date constructed from:", classScheduleDateTimeStr);
                return false;
            }

            var sTimePlus5min = dateAdd(sTime, 'minute', +5);
            attendancestatus_val = "1"; // Present
            if (now_global > sTimePlus5min) {
                attendancestatus_val = "2"; // Late
                score = 2.0/3.0;
            }
            $('#attendancestatus').val(attendancestatus_val);

            var confirmationMessage = "Are you sure you want to " + (checkedInOut_val === "1" ? "Check In" : "Check Out") + "?\n\n";
            confirmationMessage += "Class ID: " + classid_val + "\n";
            confirmationMessage += "Date: " + currDate + "\n";
            confirmationMessage += "Time: " + currTime;

            if(confirm(confirmationMessage)) {
                $.ajax({
                    url: "my_action.php", // Target action file
                    method: "POST",
                    data: {
                        action: "addKelas", // Action for backend PHP
                        studentid: studentid_val,
                        classid: classid_val,
                        sectionid: sectionid_val,
                        attendancedate: currDate,
                        attendancetime: currTime,
                        attendancestatus: attendancestatus_val,
                        checkedInOut: checkedInOut_val, // Tells backend if it's In (1) or Out (2)
                        score: score
                    },
                    dataType: "json", // Expect JSON response from server
                    success:function(response){
                        if(response && response.status === "success") {
                            // alert(response.message); // Optional success message
                        } else if (response && response.message) {
                            alert("Server: " + response.message);
                        } else {
                            alert("An unknown error occurred or invalid response from server.");
                        }
                        kelasData.ajax.reload(null, false); // Reload DataTable, false = don't reset pagination
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX submission error: ", textStatus, errorThrown, jqXHR.responseText);
                        alert("Error submitting attendance: " + textStatus + ". Please check console.");
                    }
                });
            } else {
                return false;
            }
        }
    });

    $(document).on('click', '.checkInBtn', function(){
        if ($(this).is(':disabled')) return;

        var $row = $(this).closest('tr');
        var rowIdSuffix = $(this).data('row-id');

        // The cell containing hidden spans and start time is the 9th cell (index 8)
        var dataCell = $row.find('td').eq(8);

        studentid_val = dataCell.find('#sID_'+rowIdSuffix).text();
        classid_val = dataCell.find('#cID_'+rowIdSuffix).text();
        sectionid_val = dataCell.find('#iID_'+rowIdSuffix).text();
        startTime_val = dataCell.find('#startTime_'+rowIdSuffix).text(); // HH:MM format

        if (!studentid_val || !classid_val || !sectionid_val || !startTime_val) {
            alert("Error: Could not retrieve class details for check-in.");
            console.error("CheckIn - Missing IDs/startTime. Found:", "sID:", studentid_val, "cID:", classid_val, "iID:", sectionid_val, "startTime:", startTime_val, "suffix:", rowIdSuffix);
            return;
        }

        checkedInOut_val="1"; // Set for Check In
        $('#addKelas').click(); // Trigger the common submission logic (which is an AJAX call)
    });

    $(document).on('click', '.checkOutBtn', function(){
        if ($(this).is(':disabled')) return;

        var $row = $(this).closest('tr');
        var rowIdSuffix = $(this).data('row-id');

        var dataCell = $row.find('td').eq(8); // Still need this to get student/class/section IDs

        studentid_val = dataCell.find('#sID_'+rowIdSuffix).text();
        classid_val = dataCell.find('#cID_'+rowIdSuffix).text();
        sectionid_val = dataCell.find('#iID_'+rowIdSuffix).text();
        // startTime_val is not strictly needed for checkout decision but good to have for consistency if #addKelas logic expects it
        startTime_val = dataCell.find('#startTime_'+rowIdSuffix).text();

        if (!studentid_val || !classid_val || !sectionid_val) {
            alert("Error: Could not retrieve class details for check-out.");
            console.error("CheckOut - Missing IDs. Found:", "sID:", studentid_val, "cID:", classid_val, "iID:", sectionid_val, "suffix:", rowIdSuffix);
            return;
        }

        checkedInOut_val="2"; // Set for Check Out
        $('#addKelas').click(); // Trigger the common submission logic
    });


    // User Modal Form submission (for editing user details - separate functionality)
    $(document).on('submit','#userForm', function(event){
        event.preventDefault();
        $('#save_user_form').attr('disabled','disabled'); // Use specific ID
        var formData = $(this).serialize();
        // Ensure action for userForm is correctly set, e.g., action: "updateUser"
        // The hidden field #action_user_form should handle this if part of serialize()
        $.ajax({
            url:"my_action.php", // Or a different endpoint for user updates
            method:"POST",
            data:formData,
            dataType: "json", // Assuming user update also returns JSON
            success:function(data){
                $('#userForm')[0].reset();
                $('#userModal').modal('hide');
                $('#save_user_form').attr('disabled', false);
                // Potentially reload a different DataTable if there's one for users,
                // or update UI in some other way.
                // kelasData.ajax.reload(); // This reloads class attendance, maybe not intended here.
                if(data.status === "success"){
                    // alert("User updated successfully");
                } else {
                    // alert("Failed to update user: " + (data.message || "Unknown error"));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#save_user_form').attr('disabled', false);
                alert("Error updating user: " + textStatus);
            }
        });
    });

});