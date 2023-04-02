$(document).ready(function() {
    $('#faculty').change(function() {
        var faculty = $(this).val();
        $.ajax({
            url: 'phpForScripts/get_schools.php',
            type: 'POST',
            data: { faculty: faculty },
            dataType: 'json',
            success: function(response) {
                $('#school').empty();
                $('#department').empty();
                $('#school').append('<option value = "0">Select School</option>');
                $('#department').append('<option value = "0">Select Department</option>');
                $.each(response, function(index,school) {
                    $('#school').append('<option value="' + school[0] + '">' + school[1] + '</option>');
                });
            }
        });
    });
});