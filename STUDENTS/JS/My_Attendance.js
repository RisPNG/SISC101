$(document).ready(function(){
    
    var studentid, classid, sectionid;
    var attendancestatus;
    var checkedInOut = "1"; // 0:none  1:In   2:Out
    var currDate, currTime, currDateTime;
    var year, month, day, hour, minute, second;
    
    function getDateTime() {    
        var now = new Date(); 
        year = now.getFullYear();
        month = now.getMonth() + 1; 
        day = now.getDate();
        hour = now.getHours();
        minute = now.getMinutes();
        second = now.getSeconds(); 
        if(month.toString().length == 1) {
            month = '0' + month;
        }
        if(day.toString().length == 1) {
            day = '0' + day;
        }   
        if(hour.toString().length == 1) {
            hour = '0' + hour;
        }
        if(minute.toString().length == 1) {
            minute = '0' + minute;
        }
        if(second.toString().length == 1) {
            second = '0' + second;
        }   
        currDate = year + '/' + month + '/' + day;
        currTime = hour + ':' + minute + ':' + second;
        currDateTime = currDate + " " + currTime;
        return currDateTime;
    }
                
    var kelasData = $('#kelasList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "my_action.php",
            type: "POST",
            data: {action: 'listKelas'},
            dataType: "json"
        },
        "columnDefs": [
            { "targets": [0, 1, -1], "orderable": false },
            { targets: [1], visible: false }
        ],
        "pageLength": 10
    });

    $("#attnForm").submit(function(e) {        
        var formData = $(this).serialize();
        $.ajax({
            url: "my_action.php",
            method: "POST",
            data: formData,
            success: function(data){                
            }
        });
        return false;
    });
    
    $('#addKelas').click(function(){
        var userid = $(this).attr("id");        
        var action = "addKelas";
        studentid = document.getElementById("sID").innerHTML;
        classid = document.getElementById("cID").innerHTML;
        sectionid = document.getElementById("iID").innerHTML;
        if(checkedInOut == "1" || checkedInOut == "2") {
            getDateTime();
            document.getElementById("attendancetime").value = currTime;
            var score = 1.0;
            var sTime = new Date(currDate + " " + document.getElementById("startTime").innerHTML);
            var sTimePlus5min = new Date(dateAdd(sTime, 'minute', +5));
            attendancestatus = "1";
            if(now > sTimePlus5min) { 
                attendancestatus = "2"; 
                score = 2.0 / 3.0;
            }
            if(confirm("Are you sure you want to save/update your attendance with the following?\n\nDate: " + currDate + "\nTime: " + currTime)) {
                var attendancedate = currDate;
                var attendancetime = currTime;
                $.ajax({
                    url: "my_action.php",
                    method: "POST",
                    data: {userid: userid, action: action, studentid: studentid, classid: classid, sectionid: sectionid, attendancedate: attendancedate, attendancetime: attendancetime, attendancestatus: attendancestatus, checkedInOut: checkedInOut, score: score},
                    success: function(data) {                    
                        kelasData.ajax.reload();
                    }
                });
            } else {
                return false;
            }
        }
    });    

    $(document).on('click', '#checkIn', function(){
        checkedInOut = "1";
        document.getElementById("addKelas").click();
    });    

    $(document).on('click', '#checkOut', function(){
        checkedInOut = "2";
        document.getElementById("addKelas").click();
    });    

    $(document).on('submit', '#userForm', function(event){
        event.preventDefault();
        $('#save').attr('disabled', 'disabled');
        var formData = $(this).serialize();
        $.ajax({
            url: "my_action.php",
            method: "POST",
            data: formData,
            success: function(data){                
                $('#userForm')[0].reset();
                $('#userModal').modal('hide');                
                $('#save').attr('disabled', false);
                kelasData.ajax.reload();
            }
        });
    });    
});

function dateAdd(date, interval, units) {
    var ret = new Date(date);
    switch(interval.toLowerCase()) {
        case 'minute': ret.setMinutes(ret.getMinutes() + units); break;
    }
    return ret;
}

var now = new Date();