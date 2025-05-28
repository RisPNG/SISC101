$(document).ready(function(){
	var studentid,classid,sectionid;
	var attendancestatus;
	var checkedInOut="1";
	var currDate,currTime,currDateTime;
	var year,month,day,hour,minute,second;
	
	function getDateTime() {	
	    var now     = new Date(); 
        year    = now.getFullYear();
        month   = now.getMonth()+1; 
        day     = now.getDate();
        hour    = now.getHours();
        minute  = now.getMinutes();
        second  = now.getSeconds(); 
	    if(month.toString().length == 1) {
             month = '0'+month;
        }
        if(day.toString().length == 1) {
             day = '0'+day;
        }   
        if(hour.toString().length == 1) {
             hour = '0'+hour;
        }
        if(minute.toString().length == 1) {
             minute = '0'+minute;
        }
        if(second.toString().length == 1) {
             second = '0'+second;
        }   
        currDate = year+'/'+month+'/'+day;
		currTime = hour+':'+minute+':'+second;
		currDateTime=currDate+" "+currTime;		
        return currDateTime;
    }
    
    function dateAdd(date, interval, units) {
        var ret = new Date(date);
        var checkRollover = function() { if(ret.getDate() != date.getDate()) ret.setDate(0);};
        switch(interval.toLowerCase()) {
            case 'minute'   :  ret.setMinutes(ret.getMinutes() + units); break;
            case 'hour'     :  ret.setHours(ret.getHours() + units); break;
            case 'day'      :  ret.setDate(ret.getDate() + units); break;
            default         :  ret = new Date();  break;
        }
        return ret;
    }
			
	var kelasData = $('#kelasList').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"my_action.php",
			type:"POST",
			data:{action:'listKelas'},
			dataType:"json"
		},
		"columnDefs":[
			{ "targets":[0, 1, -1], "orderable":false, },
			{ targets: [1], visible: false },			
		],
		"pageLength": 10
	});

	$("#attnForm").submit(function(e) {		
		var formData = $(this).serialize();
		$.ajax({
			url:"my_action.php",
			method:"POST",
			data:formData,
			success:function(data){}
		});
		return false;
	});
	
	$('#addKelas').click(function(){
		getDateTime();
		var now = new Date();
		document.getElementById("attendancetime").value=currTime;
		var score=1.0;
		
		// Get start time from table
		var startTimeStr = document.getElementById("startTime").innerHTML;
		var sTime = new Date(currDate + " " + startTimeStr);
		
		// Calculate late status
		var sTimePlus5min = dateAdd(sTime, 'minute', 5);
		attendancestatus="1";
		if (now > sTimePlus5min) { 
			attendancestatus="2"; 
			score=2.0/3.0;
		}
		
		var userid = $(this).attr("id");		
		var action = "addKelas";
		studentid = document.getElementById("sID").innerHTML;
		classid = document.getElementById("cID").innerHTML;
		sectionid = document.getElementById("iID").innerHTML;
		
		if(confirm("Are you sure you want to save your attendance?\n\nDate: "+currDate+"\nTime: "+currTime)) {
			var attendancedate=currDate;
			var attendancetime=currTime;
			$.ajax({
				url:"my_action.php",
				method:"POST",
				data:{
					userid:userid, 
					action:action, 
					studentid:studentid, 
					classid:classid, 
					sectionid:sectionid, 
					attendancedate:attendancedate, 
					attendancetime:attendancetime, 
					attendancestatus:attendancestatus, 
					checkedInOut:checkedInOut, 
					score:score
				},
				success:function(data) {
					// Refresh table
					kelasData.ajax.reload(null, false);
					
					// Show success message with timestamps
					$('#message').html(
						'<div class="alert alert-success">'+
						'Attendance saved successfully!<br>' + 
						(checkedInOut == "1" ? 'Check-in: ' + currTime : '') +
						(checkedInOut == "2" ? 'Check-out: ' + currTime : '') +
						'</div>'
					).removeClass('hidden');
					
					// Hide message after 3 seconds
					setTimeout(function() {
						$('#message').addClass('hidden');
					}, 3000);
					
					// Update button states
					if(checkedInOut == "1") {
						in2out();
					} else if(checkedInOut == "2") {
						out2in();
					}
				}
			});
		}
	});	

	$(document).on('click', '.update', function(){
		checkedInOut="1";
		document.getElementById("addKelas").click();
	});	

	$(document).on('click', '.delete', function(){
		checkedInOut="2";
		document.getElementById("addKelas").click();
	});	

	function in2out() {
		$(this).closest('tr').find('.update').prop('disabled', true);
		$(this).closest('tr').find('.delete').prop('disabled', false);
	}

	function out2in() {
		$(this).closest('tr').find('.update').prop('disabled', false);
		$(this).closest('tr').find('.delete').prop('disabled', true);
	}
});