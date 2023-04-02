$(document).ready(function(){
    $('#school').change(function(){
        var school = $(this).val();
        if(school != 0){
            $.ajax({
                url: 'phpForScripts/get_Departments.php',
                type: 'POST',
                data: { school: school},
                dataType: 'json',
                success: function(response) {
                    $('#department').empty();
                    $.each(response, function(index,dept) {
                        $('#department').append('<option value="' + dept[0] + '">' + dept[1] + '</option>');
                    });
                }
            });
        }
        else{
            $('#department').empty();
            $('#department').append('<option value = "0">Choose Department</option>');
        }
    });
});