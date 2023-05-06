$(document).ready(function() {
    $('#login').show();
    $('#offices').on('change',function(){
        var office = $(this).val();
        if(office == 0){
            $('#div_select_faculty').hide();
            $('#div_select_school').hide();
            $('#login').hide();
        }
        else if (office == 1) {
            $('#div_select_faculty').show();
            $('#div_select_school').hide();
            $('#login').show();

        }
        else if (office == 2) {
            $('#div_select_faculty').show();
            $('#div_select_school').show();
            $('#login').show();
        }
        else if (office == 3){
            $('#div_select_faculty').hide();
            $('#div_select_school').hide();
            $('#login').show();
        }
    });
    var office = $('#offices').val();
    if(office == 0){
        $('#div_select_faculty').hide();
        $('#div_select_school').hide();
        $('#login').hide();
    }
    else if (office == 1) {
            $('#div_select_faculty').show();
            $('#div_select_school').hide();
        }
        else if (office == 2) {
            $('#div_select_faculty').show();
            $('#div_select_school').show();
        }
        else if (office == 3){
            $('#div_select_faculty').hide();
            $('#div_select_school').hide();
            $('#login').show();
        }
    $('#select_faculty').change(function() {
            var faculty = $(this).val();
            $.ajax({
                url: 'phpForScripts/get_schools.php',
                type: 'POST',
                data: { faculty: faculty },
                dataType: 'json',
                success: function(response) {
                    $('#select_school').empty();
                    $('#select_school').append('<option value = "0">Select School</option>');
                    var x = 0;
                    $.each(response, function(index,school) {
                        $('#select_school').append('<option value="' + school[0] + '" <?php if(isset($_POST["select_school"]) && $_POST["select_school"]== "'+x+'") echo "selected"; ?>' + school[1] + '</option>');
                        x++;
                    });
                }
            });
        });
});